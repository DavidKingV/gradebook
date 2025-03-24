<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../backend/controllers/ScheduleController.php';

session_start();

use Esmefis\Gradebook\DBConnection;

$connection = new DBConnection();
$controller = new ScheduleController($connection);

function responseJson($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

// Inicializar datos según método HTTP
if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if ($data === null) {
        $data = $_POST;
    }
} else {
    $data = $_GET;
}

// Comprobar si existe 'action'
if (!isset($data['action'])) {
    responseJson(['error' => 'Action not specified']);
}

$action = $data['action'];

    switch ($action) {
        case 'getSchedules':
            responseJson($controller->getSchedules());
            break;
        case 'getEvents':
            responseJson($controller->getEvents($_SESSION['groupId']));
            break;

        case 'getEventDetails':
            $eventId = $data['eventId'];
            responseJson($controller->getEventDetails($eventId));
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Acción no válida']);
            break;
    }
