<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../backend/models/PracticalModel.php';

use Esmefis\Gradebook\DBConnection;
use Esmefis\Gradebook\getEnv;

class PracticalController{
    private $practicalModel;

    public function __construct(DBConnection $dbConnection) {
        $this->practicalModel = new PracticalModel($dbConnection);
    }

    public function practicalHours($data){
        $practicalModel = $this->practicalModel->practicalHours($data);
        if($practicalModel['success']){
            $addEvent = $this->practicalModel->addGoogleCalendarEvent($data);
            if($addEvent !== null){
                return [
                    'success' => true,
                    'message' => 'Horas de prácticas agregadas correctamente'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error al agregar horas de prácticas'
                ];
            }
        } else {
            return [
                'success' => false,
                'message' => $practicalModel['message']
            ];
        }
    }
}