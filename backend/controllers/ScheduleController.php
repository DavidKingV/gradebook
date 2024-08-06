<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../models/ScheduleModel.php';

use Esmefis\Gradebook\getEnv;
use Esmefis\Gradebook\DBConnection;

getEnv::cargar();

class ScheduleController {
    public function getSchedules() {
        $resultado = ScheduleModel::getSchedules();
        return $resultado;
    }
}

?>