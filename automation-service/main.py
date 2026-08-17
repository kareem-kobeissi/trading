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
from fastapi import BackgroundTasks, FastAPI, Header, HTTPException, Request
from fastapi.responses import PlainTextResponse
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
    products = order_product_names(order)
    amount = order_payment_amount(order)
    return (
        f"Hello {customer['name']},\n\n"
        f"We received your order *{order['reference']}* for the *{products}*.\n\n"
        "Your order is currently pending while we wait for your action. A message "
        "has been sent to your *WhatsApp* with the available options.\n\n"
        "Please choose one of the following to continue:\n\n"
        f"1\ufe0f\u20e3 *Pay by Whish Money \u2014 {amount}*\n"
        "2\ufe0f\u20e3 *Join through our Broker Partner \u2014 free access after confirmed registration*\n\n"
        "Once you choose an option and send the required confirmation, our team will "
        "review and approve your order, then grant you access to your purchase.\n\n"
        "*THE TRADING ROUTINE*"
    )


def generate_customer_message(order: dict[str, Any]) -> str:
    # Customer payment choices must remain deterministic. AI is used later for
    # reply analysis, but never to invent prices, payment details, or offers.
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


def post_callback(payload: dict[str, Any]) -> dict[str, Any]:
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
        return response.json()


def request_hostinger_email(
    order_id: int, recipient: str, subject: str, plain_message: str
) -> str:
    response = post_callback({
        "event": "email.send_requested",
        "order_id": order_id,
        "message": {
            "channel": "email",
            "recipient": recipient,
            "subject": subject,
            "original": plain_message,
        },
    })
    if not response.get("success"):
        raise RuntimeError("Hostinger rejected the email request")
    return str(response.get("external_id") or f"hostinger-{int(time.time() * 1000)}")


def whatsapp_recipient(phone: str) -> str:
    return re.sub(r"\D", "", phone)


def verify_meta_signature(body: bytes, signature: str) -> None:
    app_secret = env("META_APP_SECRET")
    expected = "sha256=" + hmac.new(app_secret.encode(), body, hashlib.sha256).hexdigest()
    if not app_secret or not signature or not hmac.compare_digest(expected, signature):
        raise HTTPException(status_code=401, detail="Invalid Meta webhook signature")


def whatsapp_graph_post(payload: dict[str, Any]) -> dict[str, Any]:
    phone_number_id = env("WHATSAPP_PHONE_NUMBER_ID")
    token = env("WHATSAPP_ACCESS_TOKEN")
    if not phone_number_id or not token:
        raise RuntimeError("WhatsApp Cloud API is not configured")
    with httpx.Client(timeout=25) as client:
        response = client.post(
            f"https://graph.facebook.com/v25.0/{phone_number_id}/messages",
            json={"messaging_product": "whatsapp", **payload},
            headers={"Authorization": f"Bearer {token}"},
        )
        response.raise_for_status()
        return response.json()


def whatsapp_message_id(response: dict[str, Any]) -> str:
    messages = response.get("messages") or []
    return str(messages[0].get("id", "")) if messages else ""


def send_whatsapp_text(recipient: str, message: str) -> str:
    response = whatsapp_graph_post({
        "to": whatsapp_recipient(recipient),
        "type": "text",
        "text": {"preview_url": True, "body": message},
    })
    return whatsapp_message_id(response)


def send_whatsapp_image(recipient: str, image_url: str, caption: str = "") -> str:
    image: dict[str, str] = {"link": image_url}
    if caption:
        image["caption"] = caption
    response = whatsapp_graph_post({
        "to": whatsapp_recipient(recipient),
        "type": "image",
        "image": image,
    })
    return whatsapp_message_id(response)


def order_product_names(order: dict[str, Any]) -> str:
    names = [
        str(item.get("name", "")).strip()
        for item in order.get("items", [])
        if isinstance(item, dict) and str(item.get("name", "")).strip()
    ]
    return " & ".join(names) or "your selected product"


def order_payment_amount(order: dict[str, Any]) -> str:
    try:
        amount = float(order.get("total") or 0)
    except (TypeError, ValueError):
        amount = 0
    if amount <= 0:
        item_amounts = []
        for item in order.get("items", []):
            if not isinstance(item, dict):
                continue
            try:
                item_amounts.append(float(item.get("price") or 0))
            except (TypeError, ValueError):
                continue
        amount = sum(item_amounts)
    if amount <= 0 and any(
        isinstance(item, dict) and str(item.get("type", "")).strip() == "course"
        for item in order.get("items", [])
    ):
        amount = 200
    currency = str(order.get("currency") or "USD").strip().upper()
    formatted = f"{amount:,.2f}".rstrip("0").rstrip(".")
    return f"${formatted} {currency}"


def send_whatsapp_order_template(order: dict[str, Any]) -> str:
    template_name = env("WHATSAPP_ORDER_TEMPLATE")
    if not template_name:
        raise RuntimeError("WhatsApp order template is not configured")
    response = whatsapp_graph_post({
        "to": whatsapp_recipient(order["customer"]["phone"]),
        "type": "template",
        "template": {
            "name": template_name,
            "language": {"code": env("WHATSAPP_TEMPLATE_LANGUAGE", "en_US")},
            "components": [{
                "type": "body",
                "parameters": [
                    {"type": "text", "text": str(order["customer"]["name"])},
                    {
                        "type": "text",
                        "text": (
                            f"{order['reference']} for {order_product_names(order)}. "
                            f"Pay {order_payment_amount(order)} by Whish Money, or register "
                            "through our broker partner to receive access for free"
                        ),
                    },
                ],
            }],
        },
    })
    return whatsapp_message_id(response)


def lookup_order_by_phone(phone: str) -> dict[str, Any]:
    result = post_callback({"event": "customer.lookup_by_phone", "phone": phone})
    order = result.get("order")
    if not isinstance(order, dict):
        raise RuntimeError("No order found for WhatsApp customer")
    return order


def record_whatsapp_message(
    order_id: int, event: str, sender: str, recipient: str,
    message: str, external_id: str
) -> bool:
    result = post_callback({
        "event": event,
        "order_id": order_id,
        "message": {
            "channel": "whatsapp",
            "sender": sender,
            "recipient": recipient,
            "original": message,
            "external_id": external_id,
        },
        "preserve_review": True,
    })
    return result.get("message_saved") is not False


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


@app.get("/webhooks/whatsapp", response_class=PlainTextResponse)
def verify_whatsapp_webhook(request: Request) -> str:
    params = request.query_params
    mode = params.get("hub.mode", "")
    token = params.get("hub.verify_token", "")
    challenge = params.get("hub.challenge", "")
    verify_token = env("WHATSAPP_VERIFY_TOKEN")
    if not verify_token or mode != "subscribe" or not hmac.compare_digest(token, verify_token):
        raise HTTPException(status_code=403, detail="Webhook verification failed")
    if not challenge.isdigit():
        raise HTTPException(status_code=400, detail="Invalid webhook challenge")
    return challenge


@app.post("/webhooks/whatsapp")
async def receive_whatsapp_webhook(
    request: Request,
    background_tasks: BackgroundTasks,
    x_hub_signature_256: str = Header(default=""),
) -> dict[str, str]:
    body = await request.body()
    verify_meta_signature(body, x_hub_signature_256)
    payload = json.loads(body)
    background_tasks.add_task(process_whatsapp_webhook, payload)
    return {"status": "accepted"}


def process_whatsapp_webhook(payload: dict[str, Any]) -> None:
    for entry in payload.get("entry", []):
        for change in entry.get("changes", []):
            value = change.get("value") or {}
            business_number = str((value.get("metadata") or {}).get("display_phone_number", ""))
            for message in value.get("messages", []):
                customer_phone = str(message.get("from", ""))
                message_id = str(message.get("id", ""))
                message_type = str(message.get("type", ""))
                selection = ""
                displayed_text = ""

                if message_type == "button":
                    button = message.get("button") or {}
                    selection = str(button.get("payload") or button.get("text") or "")
                    displayed_text = str(button.get("text") or selection)
                elif message_type == "interactive":
                    interactive = message.get("interactive") or {}
                    reply = interactive.get("button_reply") or interactive.get("list_reply") or {}
                    selection = str(reply.get("id") or reply.get("title") or "")
                    displayed_text = str(reply.get("title") or selection)
                elif message_type == "text":
                    displayed_text = str((message.get("text") or {}).get("body", "")).strip()
                    selection = displayed_text
                else:
                    continue

                try:
                    order = lookup_order_by_phone(customer_phone)
                    order_id = int(order["id"])
                    incoming_is_new = record_whatsapp_message(
                        order_id, "message.received", customer_phone, business_number,
                        displayed_text, message_id
                    )
                    if not incoming_is_new:
                        continue

                    normalized = selection.strip().lower().replace(" ", "_")
                    order_reference = str(order.get("order_ref") or order.get("reference") or "").strip()
                    products = order_product_names(order)
                    amount = order_payment_amount(order)
                    order_context = f"Order *{order_reference}* for *{products}*.\n\n" if order_reference else ""
                    if "whish" in normalized:
                        response_text = (
                            order_context +
                            "*Whish Money Payment*\n\n"
                            f"Please send the exact order amount of *{amount}* to "
                            f"*{env('WHISH_PAYMENT_NUMBER', '+961 71 493 997')}*.\n\n"
                            "After payment, reply here with:\n\n"
                            "- The *transaction reference*\n"
                            "- A *clear payment screenshot*\n\n"
                            "Your payment will be reviewed manually, and access will be activated once confirmed."
                        )
                    elif "broker" in normalized:
                        response_text = (
                            order_context +
                            "Hello 👋\n\n"
                            "Please register with our broker partner using the link below:\n\n"
                            f"👉 {env('BROKER_SIGNUP_URL', 'https://portal.bbcorp.trade/auth/jwt/sign-up/partner/X2sUYi/prod/KAS663')}\n\n"
                            "Create your account through this link and complete the registration and verification process.\n\n"
                            "After the broker registration is confirmed, your access will be granted *for free*.\n\n"
                            "Once completed, send us your *trading account login number* for confirmation.\n\n"
                            "*The Trading Routine*"
                        )
                    else:
                        response_text = (
                            "Please choose one of the options in the order message: "
                            "Pay with Whish Money or Sign up with our broker."
                        )

                    outgoing_id = send_whatsapp_text(customer_phone, response_text)
                    record_whatsapp_message(
                        order_id, "message.sent", business_number, customer_phone,
                        response_text, outgoing_id
                    )
                    if "whish" in normalized:
                        qr_url = env(
                            "WHISH_QR_IMAGE_URL",
                            "https://thetradingroutine.com/barcode.jpeg",
                        )
                        qr_id = send_whatsapp_image(
                            customer_phone,
                            qr_url,
                            "Scan this QR code to pay with Whish Money.",
                        )
                        record_whatsapp_message(
                            order_id, "message.sent", business_number, customer_phone,
                            "Whish Money payment QR code.", qr_id
                        )
                except (httpx.HTTPError, OSError, RuntimeError, ValueError, KeyError) as error:
                    print(f"WhatsApp message processing failed ({type(error).__name__}): {error}")


@app.post("/webhooks/orders")
async def pending_order(
    request: Request,
    background_tasks: BackgroundTasks,
    x_ttr_timestamp: str = Header(default=""),
    x_ttr_signature: str = Header(default=""),
) -> dict[str, Any]:
    body = await request.body()
    verify_signature(body, x_ttr_timestamp, x_ttr_signature)
    event = json.loads(body)
    if event.get("event_type") not in {"order.pending", "order.status_changed"}:
        raise HTTPException(status_code=422, detail="Unsupported event")

    background_tasks.add_task(process_order_event, event)
    return {"accepted": True, "event_id": event.get("event_id")}


def process_order_event(event: dict[str, Any]) -> None:
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
        external_id = request_hostinger_email(
            order["id"],
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
        return

    order = event["order"]
    customer_message = generate_customer_message(order)
    external_id = request_hostinger_email(
        order["id"],
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

    customer_phone = str(order["customer"].get("phone", ""))
    if env("WHATSAPP_ORDER_TEMPLATE") and whatsapp_recipient(customer_phone):
        template_record = (
            f"Hello {order['customer']['name']}, we received order {order['reference']} "
            f"for {order_product_names(order)}. Pay {order_payment_amount(order)} by Whish "
            "Money, or register through our broker partner to receive access for free."
        )
        try:
            whatsapp_id = send_whatsapp_order_template(order)
            record_whatsapp_message(
                int(order["id"]), "message.sent",
                env("WHATSAPP_BUSINESS_NUMBER", "+96178756329"), customer_phone,
                template_record, whatsapp_id
            )
        except (httpx.HTTPError, OSError, RuntimeError, KeyError) as error:
            print(f"WhatsApp order message failed ({type(error).__name__}): {error}")

    owner_email = env("OWNER_EMAIL")
    if owner_email:
        owner_message = (
            f"New pending order: {order['reference']}\n"
            f"Customer: {order['customer']['name']}\n"
            f"Email: {order['customer']['email']}\n"
            f"Phone: {order['customer']['phone']}\n"
            f"Total: {order['total']} {order.get('currency', 'USD')}\n\n"
            "The customer was contacted through the configured automated channels."
        )
        try:
            request_hostinger_email(
                order["id"], owner_email,
                f"New pending order {order['reference']}", owner_message
            )
        except (httpx.HTTPError, OSError, RuntimeError) as error:
            print(f"Owner notification failed ({type(error).__name__}).")

    if not callback_saved:
        print(f"Order {order['reference']} email sent, but its callback was not saved.")


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
