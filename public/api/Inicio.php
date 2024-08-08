<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../backend/controllers/StartController.php';

use Esmefis\Gradebook\DBConnection;

session_start();

$action = $_POST['action'];

$connection = new DBConnection();
$controller = new StartController($connection);

switch($action){

    case 'consultarTyC':
        $studentID = $_SESSION['studentID'] ??  $_SESSION['uID'] ?? null;

        $termsAndConditions = $controller->consultarTyC($studentID);
        header('Content-Type: application/json');
        echo json_encode($termsAndConditions);
        break;

    case 'aceptarTyC':
        $studentID = $_SESSION['studentID'] ??  $_SESSION['uID'] ?? null;

        $termsAndConditions = $controller->aceptarTyC($studentID);
        header('Content-Type: application/json');
        echo json_encode($termsAndConditions);
        break;
    
}

?>