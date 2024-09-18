<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../backend/controllers/PaymentsController.php';

use Esmefis\Gradebook\DBConnection;

session_start();

$action = $_POST['action'];

$connection = new DBConnection();
$controller = new PaymentsController($connection);

switch ($action) {
    case 'getPaymentsList':
        $payments = $controller->getPyamentsList();

        header('Content-Type: application/json');
        echo json_encode($payments);
        break;

    default:
        break;
}

?>
