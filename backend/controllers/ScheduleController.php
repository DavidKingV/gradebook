<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../models/ScheduleModel.php';

use Esmefis\Gradebook\getEnv;
use Esmefis\Gradebook\DBConnection;

getEnv::cargar();

class ScheduleController {
    private $scheduleModel;

    public function __construct(DBConnection $dbConnection) {
        $this->scheduleModel = new ScheduleModel($dbConnection);
    }

    public function getSchedules() {

        if(!isset($_SESSION["studentId"])) {
            return array(['success' => false, 'message' => 'Cuenta local']);
        }

        $resultado = ScheduleModel::getSchedules();
        return $resultado;
    }

    public function getEvents($groupId) {
        $resultado = $this->scheduleModel->getEvents($groupId);
        return $resultado;
    }

    public function getEventDetails($eventId) {
        $resultado = $this->scheduleModel->getEventDetails($eventId);
        return $resultado;
    }
}

?>