<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../backend/controllers/SessionController.php';

use Esmefis\Gradebook\DBConnection;

$action = $_POST['action'];

$connection = new DBConnection();
$controller = new LoginController($connection);


switch ($action) {
    case 'login':
        $userData = $_POST['loginData'];

        $response = $controller->login($userData);
        header('Content-Type: application/json');
        echo json_encode($response);

        break;
    case 'logout':
        $sessionController = new SessionController ();
        $response = $sessionController->endSession();

        header('Content-Type: application/json');
        echo json_encode($response);
        break;
    default:
        echo json_encode(['error' => 'Invalid action']);
        break;
}