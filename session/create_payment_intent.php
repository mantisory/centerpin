<?php
header('Content-Type: application/json');

require_once '../config/stripe.php';

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['amount']) || !is_numeric($input['amount'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid amount']);
    exit;
}

$amount = intval($input['amount']);
if ($amount < 50) {
    http_response_code(400);
    echo json_encode(['error' => 'Amount too small']);
    exit;
}

$items = isset($input['items']) && is_array($input['items']) ? $input['items'] : [];

$descParts = [];
$metadata = [];
foreach ($items as $i => $item) {
    $name  = substr(strval($item['name'] ?? ''), 0, 200);
    $qty   = intval($item['qty'] ?? 1);
    $price = floatval($item['price'] ?? 0);
    $sku   = substr(strval($item['sku'] ?? ''), 0, 50);
    $descParts[] = $name . ' x' . $qty;
    $line = $name . ' | Qty: ' . $qty . ' | $' . number_format($price, 2) . ($sku ? ' | SKU: ' . $sku : '');
    $metadata['item_' . ($i + 1)] = substr($line, 0, 500);
}
$description = 'Order: ' . implode(', ', $descParts);

$pickup = strval($input['pickup'] ?? '');
if ($pickup === 'local' || $pickup === 'priority') {
    $metadata['pickup'] = $pickup;
    $description = ($pickup === 'priority' ? '[PRIORITY PICKUP] ' : '[LOCAL PICKUP] ') . $description;
}

$metadata['subtotal'] = number_format(floatval($input['subtotal'] ?? 0), 2);
$metadata['shipping'] = number_format(floatval($input['shipping'] ?? 0), 2);
$metadata['tax']      = number_format(floatval($input['tax']      ?? 0), 2);
$metadata['tariff']   = number_format(floatval($input['tariff']   ?? 0), 2);

$methods = array_map('trim', explode(',', STRIPE_PAYMENT_METHODS));
$postData = ['amount' => $amount, 'currency' => 'cad', 'description' => substr($description, 0, 1000)];
foreach ($metadata as $key => $value) {
    $postData["metadata[$key]"] = $value;
}
foreach ($methods as $i => $method) {
    $postData["payment_method_types[$i]"] = $method;
}
$postData = http_build_query($postData);

$ch = curl_init('https://api.stripe.com/v1/payment_intents');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_USERPWD, STRIPE_API_KEY . ':');
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($curlError || $httpCode !== 200) {
    http_response_code(500);
    echo json_encode(['error' => 'Payment initialization failed']);
    exit;
}

$intent = json_decode($response, true);
echo json_encode(['clientSecret' => $intent['client_secret']]);
