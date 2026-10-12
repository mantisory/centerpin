<?php
require_once '../config/stripe.php';
require_once __DIR__ . '/lib/PHPMailer.php';
require_once __DIR__ . '/lib/SMTP.php';
require_once __DIR__ . '/lib/Exception.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$payload   = file_get_contents('php://input');
$sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

// Diagnostic log — remove after confirming webhook works
file_put_contents(__DIR__ . '/../webhook.log',
    '[' . date('Y-m-d H:i:s') . '] HIT sig=' . substr($sigHeader, 0, 40) . "\n",
    FILE_APPEND | LOCK_EX);
$secret    = STRIPE_WEBHOOK_SECRET;

// Verify Stripe signature to confirm this POST is genuinely from Stripe.
// Without this check, anyone could POST fake "payment succeeded" events.
$parts     = [];
foreach (explode(',', $sigHeader) as $part) {
    [$k, $v] = explode('=', $part, 2) + ['', ''];
    $parts[$k][] = $v;
}
$timestamp  = $parts['t'][0] ?? '';
$signatures = $parts['v1'] ?? [];

if (!$timestamp || !$signatures) {
    http_response_code(400);
    exit('Invalid signature header');
}

$expectedSig = hash_hmac('sha256', $timestamp . '.' . $payload, $secret);
$valid = false;
foreach ($signatures as $sig) {
    if (hash_equals($expectedSig, $sig)) {
        $valid = true;
        break;
    }
}
if (!$valid) {
    http_response_code(400);
    exit('Signature mismatch');
}

// Reject events older than 5 minutes (replay attack protection)
if (abs(time() - (int)$timestamp) > 300) {
    http_response_code(400);
    exit('Timestamp too old');
}

$event = json_decode($payload, true);

if (($event['type'] ?? '') === 'payment_intent.succeeded') {
    $intent      = $event['data']['object'];
    $amountPaid  = number_format($intent['amount'] / 100, 2);
    $currency    = strtoupper($intent['currency']);
    $intentId    = $intent['id'];
    $created     = date('Y-m-d H:i:s', $intent['created']);
    $receiptEmail = $intent['metadata']['customer_email'] ?? ($intent['receipt_email'] ?? '');

    // Order breakdown from metadata
    $meta      = $intent['metadata'] ?? [];
    $subtotal  = $meta['subtotal'] ?? '0.00';
    $shipping_  = $meta['shipping'] ?? '0.00';
    $tax       = $meta['tax']      ?? '0.00';
    $tariff    = $meta['tariff']   ?? '0.00';
    $pickup    = $meta['pickup']   ?? '';

    // Customer / shipping info
    $shipping    = $intent['shipping'] ?? [];
    $custName    = $shipping['name']  ?? ($meta['customer_name']  ?? '');
    $custPhone   = $shipping['phone'] ?? ($meta['customer_phone'] ?? '');
    $addr        = $shipping['address'] ?? [];
    $addrLine1   = $addr['line1']       ?? '';
    $addrLine2   = $addr['line2']       ?? '';
    $addrCity    = $addr['city']        ?? '';
    $addrState   = $addr['state']       ?? '';
    $addrZip     = $addr['postal_code'] ?? '';
    $addrCountry = $addr['country']     ?? '';

    $addrFormatted = trim($addrLine1
        . ($addrLine2 ? ', ' . $addrLine2 : '')
        . ', ' . $addrCity
        . ($addrState ? ', ' . $addrState : '')
        . ' ' . $addrZip
        . ', ' . $addrCountry, ', ');

    // Pickup orders replace the shipping address with a pickup notice
    $shipToLabel = 'Ship To';
    $shipToValue = $addrFormatted;
    if ($pickup === 'local') {
        $shipToLabel = 'Delivery';
        $shipToValue = 'Local Pickup Selected';
    } elseif ($pickup === 'priority') {
        $shipToLabel = 'Delivery';
        $shipToValue = 'Priority In-Store Pickup Selected (within 24 hours)';
    }

    // Parse item lines from metadata (item_1, item_2, …)
    // Format: "Name | Qty: N | $price | SKU: sku"
    $itemRows = [];
    $itemKeys = array_filter(array_keys($meta), fn($k) => preg_match('/^item_\d+$/', $k));
    sort($itemKeys);
    foreach ($itemKeys as $key) {
        $line  = $meta[$key];
        $parts = array_map('trim', explode('|', $line));
        $name  = $parts[0] ?? '';
        $qty   = '';
        $price = '';
        $sku   = '';
        foreach ($parts as $part) {
            if (preg_match('/^Qty:\s*(\d+)$/', $part, $m))       $qty   = $m[1];
            elseif (preg_match('/^\$([0-9.]+)$/', $part, $m))    $price = $m[1];
            elseif (preg_match('/^SKU:\s*(.+)$/',  $part, $m))   $sku   = $m[1];
        }
        $lineTotal = ($qty && $price) ? number_format((float)$qty * (float)$price, 2) : '';
        $itemRows[] = [
            'name'      => htmlspecialchars($name),
            'sku'       => htmlspecialchars($sku),
            'qty'       => htmlspecialchars($qty),
            'price'     => $price ? '$' . number_format((float)$price, 2) : '',
            'line_total'=> $lineTotal ? '$' . $lineTotal : '',
        ];
    }

    // Log to file
    $logLine = "[{$created}] PAID {$currency} \${$amountPaid} | ID: {$intentId} | {$custName}\n";
    foreach ($itemRows as $r) {
        $logLine .= "  " . $r['name'] . " x" . $r['qty'] . " @ " . $r['price'] . "\n";
    }
    $logLine .= "  Subtotal: \${$subtotal} | S&H: \${$shipping_} | Tax: \${$tax} | Tariff: \${$tariff} | Total: {$currency} \${$amountPaid}\n";
    file_put_contents(__DIR__ . '/../orders.log', $logLine, FILE_APPEND | LOCK_EX);

    // Build item rows HTML
    $itemRowsHtml = '';
    $rowBg = ['#f8f9fa', '#ffffff'];
    foreach ($itemRows as $i => $r) {
        $bg = $rowBg[$i % 2];
        $skuHtml = $r['sku'] ? '<br><span style="font-size:11px;color:#888;">SKU: ' . $r['sku'] . '</span>' : '';
        $itemRowsHtml .= '
            <tr style="background:' . $bg . ';">
              <td style="padding:10px 12px;border-bottom:1px solid #e9ecef;">' . $r['name'] . $skuHtml . '</td>
              <td style="padding:10px 12px;border-bottom:1px solid #e9ecef;text-align:center;">' . $r['qty'] . '</td>
              <td style="padding:10px 12px;border-bottom:1px solid #e9ecef;text-align:right;">' . $r['price'] . '</td>
              <td style="padding:10px 12px;border-bottom:1px solid #e9ecef;text-align:right;font-weight:bold;">' . $r['line_total'] . '</td>
            </tr>';
    }
    if (!$itemRowsHtml) {
        $itemRowsHtml = '<tr><td colspan="4" style="padding:12px;text-align:center;color:#888;">(no item detail)</td></tr>';
    }

    // Tariff row (only if non-zero)
    $tariffRowHtml = '';
    if ((float)$tariff > 0) {
        $tariffRowHtml = '<tr>
          <td style="padding:6px 0;color:#555;">Tariff Fees:</td>
          <td style="padding:6px 0;text-align:right;color:#555;">$' . $tariff . '</td>
        </tr>';
    }

    // Tax row (only if non-zero)
    $taxRowHtml = '';
    if ((float)$tax > 0) {
        $taxRowHtml = '<tr>
          <td style="padding:6px 0;color:#555;">Tax:</td>
          <td style="padding:6px 0;text-align:right;color:#555;">$' . $tax . '</td>
        </tr>';
    }

    $pickupBannerHtml = '';
    if ($pickup === 'local') {
        $pickupBannerHtml = '<br><strong style="color:#172F41;">LOCAL PICKUP SELECTED</strong>';
    } elseif ($pickup === 'priority') {
        $pickupBannerHtml = '<br><strong style="color:#172F41;">PRIORITY IN-STORE PICKUP SELECTED</strong> (within 24 hours)';
    }

    $subject = ($pickup ? '[PICKUP] ' : '') . "New Order — {$currency} \${$amountPaid} — " . ($custName ?: 'Customer');

    $htmlBody = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body style="margin:0;padding:20px;background:#f0f2f5;font-family:Arial,Helvetica,sans-serif;color:#333;">
<div style="max-width:640px;margin:0 auto;background:#fff;border-radius:10px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.12);">

  <!-- Header -->
  <div style="background:#172F41;color:#fff;padding:28px 30px 24px;text-align:center;">
    <img src="https://www.centerpinangling.com/images/fish_logo.png" alt="Centerpin Angling" style="height:auto;width:auto;max-height:120px;margin-bottom:16px;display:block;margin-left:auto;margin-right:auto;">
    <h1 style="margin:0;font-size:26px;font-weight:700;">New Order Received</h1>
    <div style="margin-top:10px;font-size:14px;opacity:.8;">{$created} &nbsp;|&nbsp; {$currency} \${$amountPaid}</div>
  </div>

  <div style="padding:30px;">

    <!-- Order meta -->
    <div style="background:#f8f9fa;border-left:4px solid #172F41;border-radius:4px;padding:14px 18px;margin-bottom:28px;font-size:13px;color:#555;">
      <strong style="color:#172F41;">Stripe ID:</strong> {$intentId}{$pickupBannerHtml}
    </div>

    <!-- Customer info -->
    <h2 style="font-size:16px;color:#172F41;border-bottom:2px solid #172F41;padding-bottom:8px;margin:0 0 16px;">Customer Information</h2>
    <table style="width:100%;border-collapse:collapse;margin-bottom:28px;font-size:14px;">
      <tr>
        <td style="padding:6px 0;width:120px;color:#888;font-weight:bold;vertical-align:top;">Name</td>
        <td style="padding:6px 0;">{$custName}</td>
      </tr>
      <tr>
        <td style="padding:6px 0;color:#888;font-weight:bold;vertical-align:top;">Email</td>
        <td style="padding:6px 0;">{$receiptEmail}</td>
      </tr>
      <tr>
        <td style="padding:6px 0;color:#888;font-weight:bold;vertical-align:top;">Phone</td>
        <td style="padding:6px 0;">{$custPhone}</td>
      </tr>
      <tr>
        <td style="padding:6px 0;color:#888;font-weight:bold;vertical-align:top;">{$shipToLabel}</td>
        <td style="padding:6px 0;line-height:1.5;">{$shipToValue}</td>
      </tr>
    </table>

    <!-- Items -->
    <h2 style="font-size:16px;color:#172F41;border-bottom:2px solid #172F41;padding-bottom:8px;margin:0 0 16px;">Items Ordered</h2>
    <table style="width:100%;border-collapse:collapse;font-size:14px;margin-bottom:28px;">
      <thead>
        <tr style="background:#172F41;color:#fff;">
          <th style="padding:10px 12px;text-align:left;font-weight:600;">Item</th>
          <th style="padding:10px 12px;text-align:center;width:50px;font-weight:600;">Qty</th>
          <th style="padding:10px 12px;text-align:right;width:90px;font-weight:600;">Unit Price</th>
          <th style="padding:10px 12px;text-align:right;width:90px;font-weight:600;">Total</th>
        </tr>
      </thead>
      <tbody>
        {$itemRowsHtml}
      </tbody>
    </table>

    <!-- Totals -->
    <div style="display:flex;justify-content:flex-end;">
      <table style="border-collapse:collapse;font-size:14px;min-width:280px;">
        <tr>
          <td style="padding:6px 0;color:#555;">Subtotal:</td>
          <td style="padding:6px 0;text-align:right;color:#555;">\${$subtotal}</td>
        </tr>
        <tr>
          <td style="padding:6px 0;color:#555;">Shipping &amp; Handling:</td>
          <td style="padding:6px 0;text-align:right;color:#555;">\${$shipping_}</td>
        </tr>
        {$tariffRowHtml}
        {$taxRowHtml}
        <tr style="border-top:2px solid #172F41;">
          <td style="padding:12px 0 6px;font-size:17px;font-weight:700;color:#172F41;">Total ({$currency}):</td>
          <td style="padding:12px 0 6px;text-align:right;font-size:17px;font-weight:700;color:#172F41;">\${$amountPaid}</td>
        </tr>
      </table>
    </div>

  </div>

  <!-- Footer -->
  <div style="background:#172F41;color:#a8bcc8;padding:28px 30px;text-align:center;font-size:13px;line-height:1.8;">
    <div style="font-size:15px;font-weight:700;color:#fff;letter-spacing:1px;margin-bottom:12px;">CENTERPIN ANGLING</div>
    <div>1315 Lawrence Ave E, Unit 201 &mdash; Toronto, ON</div>
    <div style="margin-top:4px;">
      <a href="tel:18447596153" style="color:#a8bcc8;text-decoration:none;">1-844-759-6153</a>
      &nbsp;&mdash;&nbsp;
      <a href="mailto:sales@centerpinangling.com" style="color:#a8bcc8;text-decoration:none;">sales@centerpinangling.com</a>
    </div>
    <div style="margin-top:12px;font-size:12px;color:#6e8fa3;border-top:1px solid #2a4a60;padding-top:12px;">
      <strong style="color:#a8bcc8;">Walk-In Hours:</strong><br>
      Tue &ndash; Fri: 12:00 pm &ndash; 7:00 pm<br>
      Sat: 12:00 pm &ndash; 4:00 pm (Seasonal: Sep &ndash; May)<br>
      Sun &amp; Mon: Closed
    </div>
    <div style="margin-top:14px;font-size:11px;color:#4a6a7a;">
      &copy; <?php echo date('Y'); ?> Centerpin Angling. All rights reserved.
    </div>
  </div>

</div>
</body>
</html>
HTML;

    $sendMail = function(string $to, string $subject) use ($htmlBody): void {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        // Port 465 expects implicit TLS (SMTPS); STARTTLS on 465 hangs the connection.
        $mail->SMTPSecure = (SMTP_PORT === 465)
            ? PHPMailer::ENCRYPTION_SMTPS
            : PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;
        $mail->Timeout    = 15;
        $mail->setFrom(SMTP_USER, 'Centerpin Angling');
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $htmlBody;
        $mail->send();
    };

    $logMail = function(string $to, bool $ok, string $err = '') use ($intentId): void {
        $ts  = date('Y-m-d H:i:s');
        $line = $ok
            ? "[{$ts}] MAIL OK  to:{$to} id:{$intentId}\n"
            : "[{$ts}] MAIL ERR to:{$to} id:{$intentId} err:{$err}\n";
        file_put_contents(__DIR__ . '/../mail.log', $line, FILE_APPEND | LOCK_EX);
    };

    // Notify store owner
    try {
        $sendMail(NOTIFICATION_EMAIL, $subject);
        $logMail(NOTIFICATION_EMAIL, true);
    } catch (\Exception $e) {
        $logMail(NOTIFICATION_EMAIL, false, $e->getMessage());
    }

    // Send confirmation to customer
    if ($receiptEmail && filter_var($receiptEmail, FILTER_VALIDATE_EMAIL)) {
        try {
            $sendMail($receiptEmail, "Your Order Confirmation — {$currency} \${$amountPaid}");
            $logMail($receiptEmail, true);
        } catch (\Exception $e) {
            $logMail($receiptEmail, false, $e->getMessage());
        }
    }

}

http_response_code(200);
echo 'ok';
