import hashlib
import hmac
import base64
import imaplib
import json
import os
import re
import smtplib
import time
from email import policy
from email.parser import BytesParser
from email.utils import parseaddr
from email.message import EmailMessage
from html import escape
from typing import Any

import httpx
from fastapi import FastAPI, Header, HTTPException, Request
from openai import OpenAI, OpenAIError

app = FastAPI(title="TTR Customer Automation", version="1.0.0")

ORDER_REFERENCE_PATTERN = re.compile(
    r"(?:\bREQ-[A-Z]+-\d+-\d+\b|\bORD-[^\s<>]+-\d{14}\b)", re.IGNORECASE
)
ALLOWED_ATTACHMENT_TYPES = {
    "image/jpeg", "image/png", "image/webp", "application/pdf"
}
MAX_ATTACHMENT_BYTES = 4 * 1024 * 1024


def env(name: str, default: str = "") -> str:
    return os.getenv(name, default).strip()


def verify_signature(body: bytes, timestamp: str, signature: str) -> None:
    secret = env("TTR_WEBHOOK_SECRET")
    if not secret or not timestamp.isdigit() or abs(time.time() - int(timestamp)) > 300:
        raise HTTPException(status_code=401, detail="Invalid webhook credentials")
    expected = "sha256=" + hmac.new(
        secret.encode(), timestamp.encode() + b"." + body, hashlib.sha256
    ).hexdigest()
    if not hmac.compare_digest(expected, signature):
        raise HTTPException(status_code=401, detail="Invalid webhook signature")


def fallback_customer_message(order: dict[str, Any]) -> str:
    customer = order["customer"]
    products = " & ".join(item["name"] for item in order.get("items", []))
    return (
        f"Hello {customer['name']},\n\n"
        f"We received your order {order['reference']} for {products}. "
        f"The total is {order['total']:.2f} {order.get('currency', 'USD')}.\n\n"
        "Your order is pending while payment is reviewed. Please reply with your "
        "payment reference and proof. A team member will verify it manually before "
        "access is activated.\n\nTHE TRADING ROUTINE"
    )


def generate_customer_message(order: dict[str, Any]) -> str:
    if not env("OPENAI_API_KEY"):
        return fallback_customer_message(order)
    try:
        client = OpenAI(api_key=env("OPENAI_API_KEY"))
        response = client.responses.create(
            model=env("OPENAI_MODEL", "gpt-5-mini"),
            instructions=(
                "Write a concise, professional payment follow-up message for THE TRADING "
                "ROUTINE. Use the customer's apparent language when confidently inferable; "
                "otherwise use English. State that approval is manual. Never claim payment "
                "was received or verified. Ask for the transaction reference and proof."
            ),
            input=json.dumps(order, ensure_ascii=False),
        )
        return response.output_text.strip() or fallback_customer_message(order)
    except OpenAIError as error:
        print(f"OpenAI unavailable; using fallback message ({type(error).__name__}).")
        return fallback_customer_message(order)


def send_email(to_email: str, subject: str, plain_message: str) -> str:
    required = [env("SMTP_HOST"), env("SMTP_USERNAME"), env("SMTP_PASSWORD"), env("SMTP_FROM_EMAIL")]
    if not all(required):
        raise RuntimeError("SMTP is not configured")
    message = EmailMessage()
    message["From"] = f"{env('SMTP_FROM_NAME', 'THE TRADING ROUTINE')} <{env('SMTP_FROM_EMAIL')}>"
    message["To"] = to_email
    message["Subject"] = subject
    message.set_content(plain_message)
    message.add_alternative(
        "<div style='font-family:Arial,sans-serif;line-height:1.6'>"
        + "<br>".join(escape(plain_message).splitlines())
        + "</div>",
        subtype="html",
    )
    with smtplib.SMTP(env("SMTP_HOST"), int(env("SMTP_PORT", "587")), timeout=20) as smtp:
        smtp.starttls()
        smtp.login(env("SMTP_USERNAME"), env("SMTP_PASSWORD"))
        smtp.send_message(message)
    return message["Message-ID"] or f"email-{int(time.time() * 1000)}"


def post_callback(payload: dict[str, Any]) -> None:
    body = json.dumps(payload, ensure_ascii=False, separators=(",", ":")).encode()
    timestamp = str(int(time.time()))
    signature = "sha256=" + hmac.new(
        env("TTR_CALLBACK_SECRET").encode(), timestamp.encode() + b"." + body, hashlib.sha256
    ).hexdigest()
    with httpx.Client(timeout=20) as client:
        response = client.post(
            env("TTR_CALLBACK_URL"),
            content=body,
            headers={
                "Content-Type": "application/json",
                "X-TTR-Timestamp": timestamp,
                "X-TTR-Signature": signature,
            },
        )
        response.raise_for_status()


def plain_email_body(message: Any) -> str:
    if message.is_multipart():
        for part in message.walk():
            if part.get_content_type() == "text/plain" and part.get_content_disposition() != "attachment":
                try:
                    return part.get_content().strip()
                except (LookupError, UnicodeError):
                    payload = part.get_payload(decode=True) or b""
                    return payload.decode("utf-8", errors="replace").strip()
        return ""
    try:
        return message.get_content().strip()
    except (LookupError, UnicodeError):
        payload = message.get_payload(decode=True) or b""
        return payload.decode("utf-8", errors="replace").strip()


def email_attachments(message: Any) -> list[dict[str, str]]:
    attachments: list[dict[str, str]] = []
    total_bytes = 0
    for part in message.iter_attachments():
        content_type = part.get_content_type().lower()
        data = part.get_payload(decode=True) or b""
        if content_type not in ALLOWED_ATTACHMENT_TYPES or not data:
            continue
        total_bytes += len(data)
        if total_bytes > MAX_ATTACHMENT_BYTES:
            break
        filename = os.path.basename(part.get_filename() or "proof")
        attachments.append({
            "filename": filename[:180],
            "content_type": content_type,
            "data_base64": base64.b64encode(data).decode("ascii"),
        })
    return attachments


def fallback_reply_analysis(body: str, has_attachments: bool) -> dict[str, Any]:
    return {
        "translated_message": body,
        "summary": "Customer replied" + (" with payment proof." if has_attachments else "."),
        "recommendation": "pending",
        "confidence": 0,
        "reason": "OpenAI analysis unavailable; administrator review is required.",
    }


def analyze_customer_reply(body: str, has_attachments: bool) -> dict[str, Any]:
    fallback = fallback_reply_analysis(body, has_attachments)
    if not env("OPENAI_API_KEY"):
        return fallback
    try:
        response = OpenAI(api_key=env("OPENAI_API_KEY")).responses.create(
            model=env("OPENAI_MODEL", "gpt-5-mini"),
            instructions=(
                "Analyze a customer reply about a pending trading-product order. "
                "Return JSON only with keys translated_message, summary, recommendation, "
                "confidence, reason. Translate the message to English. recommendation must "
                "be approve, pending, or reject. Treat attachments only as unverified proof; "
                "never claim payment is verified. Prefer pending when evidence is ambiguous. "
                "confidence must be 0-100. The administrator makes the final decision."
            ),
            input=json.dumps({"message": body, "has_attachments": has_attachments}, ensure_ascii=False),
        )
        raw = response.output_text.strip()
        if raw.startswith("```"):
            raw = re.sub(r"^```(?:json)?\s*|\s*```$", "", raw, flags=re.IGNORECASE)
        result = json.loads(raw)
        recommendation = str(result.get("recommendation", "pending")).lower()
        if recommendation not in {"approve", "pending", "reject"}:
            recommendation = "pending"
        return {
            "translated_message": str(result.get("translated_message") or body),
            "summary": str(result.get("summary") or fallback["summary"]),
            "recommendation": recommendation,
            "confidence": max(0, min(100, float(result.get("confidence", 0)))),
            "reason": str(result.get("reason") or fallback["reason"]),
        }
    except (OpenAIError, ValueError, TypeError, json.JSONDecodeError) as error:
        print(f"Reply analysis unavailable; using fallback ({type(error).__name__}).")
        return fallback


@app.get("/health")
def health() -> dict[str, str]:
    return {"status": "ok"}


@app.post("/webhooks/orders")
async def pending_order(
    request: Request,
    x_ttr_timestamp: str = Header(default=""),
    x_ttr_signature: str = Header(default=""),
) -> dict[str, Any]:
    body = await request.body()
    verify_signature(body, x_ttr_timestamp, x_ttr_signature)
    event = json.loads(body)
    if event.get("event_type") == "order.status_changed":
        order = event["order"]
        status = str(order.get("status", "pending"))
        status_labels = {"unlocked": "approved", "cancelled": "rejected", "pending": "pending"}
        label = status_labels.get(status, status)
        message = (
            f"Hello {order['customer']['name']},\n\n"
            f"Your order {order['reference']} is now {label}.\n"
            f"{order.get('status_message', '')}\n\nTHE TRADING ROUTINE"
        )
        external_id = send_email(
            order["customer"]["email"],
            f"Order {label}: {order['reference']}",
            message,
        )
        try:
            post_callback({
                "event": "message.sent",
                "order_id": order["id"],
                "message": {
                    "channel": "email", "sender": env("SMTP_FROM_EMAIL"),
                    "recipient": order["customer"]["email"], "original": message,
                    "external_id": external_id,
                },
                "preserve_review": True,
            })
        except (httpx.HTTPError, OSError) as error:
            print(f"Status callback unavailable; email was still sent ({type(error).__name__}).")
        return {"accepted": True, "event_id": event.get("event_id")}

    if event.get("event_type") != "order.pending":
        raise HTTPException(status_code=422, detail="Unsupported event")
    order = event["order"]
    customer_message = generate_customer_message(order)
    external_id = send_email(
        order["customer"]["email"],
        f"Payment instructions for {order['reference']}",
        customer_message,
    )

    callback_saved = True
    try:
        post_callback(
            {
            "event": "message.sent",
            "order_id": order["id"],
            "message": {
                "channel": "email",
                "sender": env("SMTP_FROM_EMAIL"),
                "recipient": order["customer"]["email"],
                "original": customer_message,
                "external_id": external_id,
            },
            "review": {
                "contact_status": "waiting_for_proof",
                "recommended_status": "pending",
                "reason": "Payment instructions sent; waiting for customer proof.",
            },
            }
        )
    except (httpx.HTTPError, OSError) as error:
        callback_saved = False
        print(f"Callback unavailable; email was still sent ({type(error).__name__}).")

    owner_email = env("OWNER_EMAIL")
    if owner_email:
        owner_message = (
            f"New pending order: {order['reference']}\n"
            f"Customer: {order['customer']['name']}\n"
            f"Email: {order['customer']['email']}\n"
            f"Phone: {order['customer']['phone']}\n"
            f"Total: {order['total']} {order.get('currency', 'USD')}\n\n"
            "The customer was contacted automatically by email. WhatsApp automation "
            "will remain disabled until an official Business API number is connected."
        )
        try:
            send_email(owner_email, f"New pending order {order['reference']}", owner_message)
        except (smtplib.SMTPException, OSError) as error:
            print(f"Owner notification failed ({type(error).__name__}).")

    return {
        "accepted": True,
        "event_id": event.get("event_id"),
        "callback_saved": callback_saved,
    }


@app.post("/jobs/poll-email")
def poll_email_replies(x_ttr_job_secret: str = Header(default="")) -> dict[str, Any]:
    expected_secret = env("TTR_JOB_SECRET")
    if not expected_secret or not hmac.compare_digest(expected_secret, x_ttr_job_secret):
        raise HTTPException(status_code=401, detail="Invalid job credentials")

    imap_host = env("IMAP_HOST", "imap.hostinger.com")
    imap_port = int(env("IMAP_PORT", "993"))
    username = env("IMAP_USERNAME", env("SMTP_USERNAME"))
    password = env("IMAP_PASSWORD", env("SMTP_PASSWORD"))
    if not username or not password:
        raise HTTPException(status_code=503, detail="IMAP is not configured")

    processed = 0
    skipped = 0
    with imaplib.IMAP4_SSL(imap_host, imap_port) as mailbox:
        mailbox.login(username, password)
        mailbox.select("INBOX")
        status, data = mailbox.search(None, "UNSEEN")
        if status != "OK":
            raise HTTPException(status_code=502, detail="Unable to search mailbox")

        for message_number in (data[0].split() if data and data[0] else [])[-25:]:
            status, message_data = mailbox.fetch(message_number, "(RFC822)")
            if status != "OK" or not message_data or not isinstance(message_data[0], tuple):
                skipped += 1
                continue
            message = BytesParser(policy=policy.default).parsebytes(message_data[0][1])
            sender = parseaddr(str(message.get("From", "")))[1].lower()
            if not sender or sender == username.lower():
                mailbox.store(message_number, "+FLAGS", "\\Seen")
                skipped += 1
                continue

            subject = str(message.get("Subject", ""))
            body = plain_email_body(message)
            reference_match = ORDER_REFERENCE_PATTERN.search(subject + "\n" + body)
            if not reference_match or not body:
                skipped += 1
                continue

            reference = reference_match.group(0).upper()
            attachments = email_attachments(message)
            analysis = analyze_customer_reply(body, bool(attachments))
            recommendation_map = {
                "approve": "likely_valid", "pending": "pending", "reject": "likely_invalid"
            }
            post_callback({
                "event": "message.received",
                "order_reference": reference,
                "message": {
                    "channel": "email",
                    "sender": sender,
                    "recipient": username,
                    "original": body,
                    "translated": analysis["translated_message"],
                    "summary": analysis["summary"],
                    "external_id": str(message.get("Message-ID", ""))[:255],
                    "attachments": attachments,
                },
                "review": {
                    "contact_status": "proof_received" if attachments else "needs_admin_review",
                    "recommended_status": recommendation_map[analysis["recommendation"]],
                    "confidence": analysis["confidence"],
                    "reason": analysis["reason"],
                },
            })
            mailbox.store(message_number, "+FLAGS", "\\Seen")
            processed += 1

    return {"success": True, "processed": processed, "skipped": skipped}
