# TTR Phase 1 automation service

This FastAPI service receives signed pending-order events from the Hostinger PHP
website, generates a customer-safe message with OpenAI, sends it by email, and
writes the activity back to the website through a signed callback.

## Current channel support

- Email: ready after SMTP environment variables are configured.
- WhatsApp: intentionally disabled. A normal WhatsApp number cannot send or
  receive automated webhook messages. Connect an official WhatsApp Business
  Cloud API number before enabling this channel.

## Deployment

1. Deploy this directory to Render, Railway, or a VPS.
2. Copy `.env.example` values into the hosting provider's secret settings.
3. Set `TTR_AUTOMATION_WEBHOOK_URL` on Hostinger to
   `https://YOUR-PYTHON-SERVICE/webhooks/orders`.
4. Use the same random `TTR_AUTOMATION_WEBHOOK_SECRET` on both services.
5. Use the same random `TTR_AUTOMATION_CALLBACK_SECRET` on both services.
6. Run `dispatch_automation_events.php` every minute using a Hostinger PHP cron
   job.

Never commit `.env` files, SMTP passwords, OpenAI keys, or webhook secrets.
