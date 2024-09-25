<?php
require_once __DIR__ . '/../../../vendor/autoload.php';
require __DIR__ . '/../../controllers/PaymentsController.php';

date_default_timezone_set('Etc/GMT-6');
setlocale(LC_TIME, 'es_MX.UTF-8');

use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;   
use Esmefis\Gradebook\DBConnection;
use Esmefis\Gradebook\getEnv;

$connection = new DBConnection();
$controller = new PaymentsController($connection);


$paymentAmount = $controller->getPaymentsAmount();

if(!$paymentAmount['success']){
    return $paymentAmount;
    exit();
}

getEnv::cargar();
MercadoPagoConfig::setAccessToken($_ENV['MERCADOPAGO_ACCESS_TOKEN']);

$amount = $paymentAmount['monthly_amount'];

$client = new PreferenceClient();
$preference = $client->create([
  "items"=> [
    [
        "title" => "Mensualidad de " . date('F'),
        "quantity" => 1,
        "unit_price" => $amount
    ]
  ],
  "payment_methods" => [
    "excluded_payment_types" => [
        [
            "id" => "ticket"
        ],
        [
            "id" => "bank_transfer"
        ]
    ],
    "installments" => 1
  ],
  "statement_descriptor" => "ESMEFIS"
]);
?>