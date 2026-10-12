<?php
// Detect environment:
//   1. Honour APP_ENV if set on the server (e.g. via .htaccess: SetEnv APP_ENV dev)
//   2. Fall back to file-existence check — if .env is present in the project root, assume dev
$_appEnv = getenv('APP_ENV') ?: (file_exists(__DIR__ . '/../.env') ? 'dev' : 'prod');

$_envConfig = __DIR__ . '/stripe.' . $_appEnv . '.php';
if (!file_exists($_envConfig)) {
    die('Missing Stripe environment config: stripe.' . $_appEnv . '.php');
}
require_once $_envConfig;   // sets $_envFile

if (!file_exists($_envFile)) {
    die('Missing .env file (' . $_appEnv . '): ' . $_envFile);
}
$_env = parse_ini_file($_envFile, false, INI_SCANNER_RAW);

define('STRIPE_API_KEY',         $_env['STRIPE_SECRET_KEY']      ?? '');
define('STRIPE_PUBLISHABLE_KEY', $_env['STRIPE_PUBLISHABLE_KEY'] ?? '');
define('STRIPE_WEBHOOK_SECRET',  $_env['STRIPE_WEBHOOK_SECRET']  ?? '');
define('STRIPE_SUCCESS_URL',     $_env['STRIPE_SUCCESS_URL']     ?? '');
define('STRIPE_CANCEL_URL',      $_env['STRIPE_CANCEL_URL']      ?? '');
define('NOTIFICATION_EMAIL',     $_env['NOTIFICATION_EMAIL']     ?? '');
define('SMTP_HOST',              $_env['SMTP_HOST']              ?? '');
define('SMTP_PORT',              (int)($_env['SMTP_PORT']        ?? 587));
define('SMTP_USER',              $_env['SMTP_USER']              ?? '');
define('SMTP_PASS',              $_env['SMTP_PASS']              ?? '');

// Payment methods shown at checkout. 'card' includes Apple Pay & Google Pay on supported devices.
// To re-enable buy-now-pay-later options add: ,klarna,affirm
define('STRIPE_PAYMENT_METHODS', 'card');
