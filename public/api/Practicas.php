<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../backend/controllers/PracticalController.php';

use Esmefis\Gradebook\DBConnection;

session_start();

$action = $_POST['action'];

$connection = new DBConnection();
$controller = new PracticalController($connection);

switch($action){

    case 'ReqPract':
        $data = $_POST['datos'];

        $practicas = $controller->practicalHours($data);
        header('Content-Type: application/json');
        echo json_encode($practicas);
        
        break;
    
}