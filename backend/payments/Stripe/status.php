<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

use Esmefis\Gradebook\getEnv;
use Esmefis\Gradebook\DBConnection;

getEnv::cargar();

$stripe = new \Stripe\StripeClient($_ENV['STRIPE_SECRET_KEY']);
header('Content-Type: application/json');

try {
  // retrieve JSON from POST body
  $jsonStr = file_get_contents('php://input');
  $jsonObj = json_decode($jsonStr);

  $session = $stripe->checkout->sessions->retrieve($jsonObj->session_id);

  echo json_encode(['status' => $session->status, 'customer_email' => $session->customer_details->email, 'id' => $session->id]);
  http_response_code(200);
} catch (Error $e) {
  http_response_code(500);
  echo json_encode(['error' => $e->getMessage()]);
}