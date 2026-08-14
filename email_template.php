<?php

function brandedEmailTemplate($title, $bodyHtml, $preheader = '')
{
    $safeTitle = htmlspecialchars((string) $title, ENT_QUOTES, 'UTF-8');
    $safePreheader = htmlspecialchars((string) $preheader, ENT_QUOTES, 'UTF-8');
    $year = date('Y');

    return <<<HTML
<!doctype html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>{$safeTitle}</title></head>
<body style="margin:0;padding:0;background:#070d20;color:#eaf6ff;font-family:Arial,Helvetica,sans-serif">
  <div style="display:none;max-height:0;overflow:hidden;opacity:0">{$safePreheader}</div>
  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#070d20">
    <tr><td align="center" style="padding:28px 12px">
      <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width:620px;background:#0d1730;border:1px solid #1aaed1;border-radius:20px;overflow:hidden;box-shadow:0 18px 55px rgba(0,0,0,.38)">
        <tr><td align="center" style="padding:30px 24px 22px;background:#0a132b;border-bottom:1px solid #16345a">
          <img src="https://thetradingroutine.com/hsenn.jpeg" width="76" height="76" alt="THE TRADING ROUTINE" style="display:block;width:76px;height:76px;border-radius:50%;object-fit:cover;border:2px solid #27d7f5">
          <div style="margin-top:15px;color:#64e7ff;font-size:21px;font-weight:800;letter-spacing:1.5px">THE TRΛDING ROUTINE</div>
          <div style="margin-top:6px;color:#8296b8;font-size:12px;letter-spacing:1px">STRUCTURE · DISCIPLINE · EXECUTION</div>
        </td></tr>
        <tr><td style="padding:32px 30px">
          <h1 style="margin:0 0 20px;color:#ffffff;font-size:26px;line-height:1.25;text-align:center">{$safeTitle}</h1>
          <div style="color:#c8d8eb;font-size:15px;line-height:1.75">{$bodyHtml}</div>
        </td></tr>
        <tr><td align="center" style="padding:20px 24px;background:#091229;border-top:1px solid #16345a;color:#7184a3;font-size:12px;line-height:1.6">
          <div>THE TRΛDING ROUTINE</div>
          <div><a href="https://thetradingroutine.com" style="color:#55dcf5;text-decoration:none">thetradingroutine.com</a></div>
          <div style="margin-top:6px">&copy; {$year} THE TRΛDING ROUTINE. All rights reserved.</div>
        </td></tr>
      </table>
    </td></tr>
  </table>
</body>
</html>
HTML;
}

function brandedPlainTextEmail($title, $plainMessage, $preheader = '')
{
    $body = nl2br(htmlspecialchars((string) $plainMessage, ENT_QUOTES, 'UTF-8'));
    return brandedEmailTemplate($title, $body, $preheader);
}
