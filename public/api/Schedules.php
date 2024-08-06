<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../backend/controllers/ScheduleController.php';

use Esmefis\Gradebook\DBConnection;

$action = $_GET['action'];

$controller = new ScheduleController();

switch ($action) {
    case 'getSchedules':

        $response = $controller->getSchedules();
        header('Content-Type: application/json');
        echo json_encode($response);

        break;
    default:
        echo json_encode(['error' => 'Invalid action']);
        break;
}
