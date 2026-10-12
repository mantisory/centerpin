<?php
header('Content-Type: application/json');

require_once '../config/stripe.php';

$input = json_decode(file_get_contents('php://input'), true);

$clientSecret = strval($input['client_secret'] ?? '');
$email        = trim(strval($input['email'] ?? ''));
$name         = trim(strval($input['name'] ?? ''));
$phone        = trim(strval($input['phone'] ?? ''));
$pickup       = strval($input['pickup'] ?? '');

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid email']);
    exit;
}

if (!preg_match('/^(pi_[A-Za-z0-9]+)_secret_[A-Za-z0-9]+$/', $clientSecret, $m)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid client secret']);
    exit;
}
$intentId = $m[1];

// Verify the caller actually holds this intent's client secret before touching it
$ch = curl_init('https://api.stripe.com/v1/payment_intents/' . $intentId);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERPWD, STRIPE_API_KEY . ':');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$intent = json_decode($response, true);
if ($httpCode !== 200 || ($intent['client_secret'] ?? '') !== $clientSecret) {
    http_response_code(403);
    echo json_encode(['error' => 'Not authorized']);
    exit;
}

$metadataFields = ['metadata[customer_email]' => $email];
if ($name !== '')   $metadataFields['metadata[customer_name]']  = substr($name, 0, 200);
if ($phone !== '')  $metadataFields['metadata[customer_phone]'] = substr($phone, 0, 50);
if ($pickup === 'local' || $pickup === 'priority') $metadataFields['metadata[pickup]'] = $pickup;

$ch = curl_init('https://api.stripe.com/v1/payment_intents/' . $intentId);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($metadataFields));
curl_setopt($ch, CURLOPT_USERPWD, STRIPE_API_KEY . ':');
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    http_response_code(500);
    echo json_encode(['error' => 'Update failed']);
    exit;
}

echo json_encode(['ok' => true]);
