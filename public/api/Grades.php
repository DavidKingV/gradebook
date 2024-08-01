<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../backend/controllers/GradesController.php';

use Esmefis\Gradebook\DBConnection;

session_start();

$action = $_POST['action'];

$connection = new DBConnection();
$controller = new GradesController($connection);

switch($action){

    case 'getGrades':
        $studentID = $_SESSION['studentID'] ?? null;
        $uID = $_SESSION['uID'] ?? null;

        $grades = $controller->getGrades($studentID ?? $uID);
        header('Content-Type: application/json');
        echo json_encode($grades);
        break;
    
}

?>