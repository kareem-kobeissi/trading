import hashlib
import hmac
import json
import os
import smtplib
import time
from email.message import EmailMessage
from html import escape
from typing import Any

import httpx
from fastapi import FastAPI, Header, HTTPException, Request
from openai import OpenAI

app = FastAPI(title="TTR Customer Automation", version="1.0.0")


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
    if event.get("event_type") != "order.pending":
        raise HTTPException(status_code=422, detail="Unsupported event")
    order = event["order"]
    customer_message = generate_customer_message(order)
    external_id = send_email(
        order["customer"]["email"],
        f"Payment instructions for {order['reference']}",
        customer_message,
    )

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
        send_email(owner_email, f"New pending order {order['reference']}", owner_message)

    return {"accepted": True, "event_id": event.get("event_id")}
