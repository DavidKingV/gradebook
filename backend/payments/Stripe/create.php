<?php
require_once __DIR__ . '/../../../vendor/autoload.php';
require __DIR__ . '/../../controllers/PaymentsController.php';

date_default_timezone_set('Etc/GMT-6');
setlocale(LC_TIME, 'es_MX.UTF-8');

use Esmefis\Gradebook\getEnv;
use Esmefis\Gradebook\DBConnection;

getEnv::cargar();

$connection = new DBConnection();
$controller = new PaymentsController($connection);


$paymentAmount = $controller->getPaymentsAmount();

if(!$paymentAmount['success']){
    return $paymentAmount;
    exit();
}
$amount = $paymentAmount['monthly_amount'];

$stripe = new \Stripe\StripeClient($_ENV['STRIPE_SECRET_KEY']);

header('Content-Type: application/json');

$YOUR_DOMAIN = 'http://localhost:8080/gradebookEsmefis/backend/views/stripe';

$checkout_session = $stripe->checkout->sessions->create([
  'ui_mode' => 'embedded',
  'line_items' => [[
    [
        'price_data' => [
          'currency' => 'mxn',
          'product_data' => ['name' => 'Mensualidad de ' . date('F')],
          'unit_amount' => $amount.'00',
          'tax_behavior' => 'exclusive',
        ],
        'quantity' => 1,
      ],
  ]],
  'mode' => 'payment',
  'return_url' => $YOUR_DOMAIN . '/complete.php?session_id={CHECKOUT_SESSION_ID}',
]);

  echo json_encode(array('clientSecret' => $checkout_session->client_secret));