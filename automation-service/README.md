# TTR customer automation service

This FastAPI service receives signed pending-order events from the Hostinger PHP
website, generates a customer-safe message with OpenAI, sends it by email, and
writes the activity back to the website through a signed callback.

It also polls the support mailbox for replies, stores safe proof attachments on
the PHP host, and uses OpenAI to translate, summarize, and recommend a status.
Recommendations are advisory; only the administrator changes an order status.

## Current channel support

- Email: ready after SMTP environment variables are configured.
- Email replies: ready after IMAP and `TTR_JOB_SECRET` are configured.
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
7. Call `POST https://YOUR-PYTHON-SERVICE/jobs/poll-email` every minute with
   header `X-TTR-Job-Secret: YOUR_TTR_JOB_SECRET`. Only unread messages that
   contain a known order reference are processed.

## Required PHP environment variables

- `ADMIN_EMAIL`
- `ADMIN_PASSWORD_HASH` (recommended) or `ADMIN_PASSWORD`
- `TTR_AUTOMATION_WEBHOOK_URL`
- `TTR_AUTOMATION_WEBHOOK_SECRET`
- `TTR_AUTOMATION_CALLBACK_SECRET`
- SMTP variables matching the Python service

Generate an admin password hash with `php -r "echo password_hash('YOUR_PASSWORD', PASSWORD_DEFAULT);"`.

Never commit `.env` files, SMTP passwords, OpenAI keys, or webhook secrets.
