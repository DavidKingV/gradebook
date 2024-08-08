<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../models/StartModel.php';

use Esmefis\Gradebook\getEnv;
use Esmefis\Gradebook\DBConnection;

class StartController{
    private $startModel;

    public function __construct(DBConnection $dbConnection) {
        $this->startModel = new StartModel($dbConnection);
    }

    public function consultarTyC($studentID){
        $startModel = $this->startModel->consultarTyC($studentID);
        if($startModel !== null){
           if($startModel > 0){
                return [
                    'success' => true,
                    'message' => 'Términos y condiciones aceptados'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Términos y condiciones no aceptados'
                ];
            }
        } else {
            return [
                'success' => false,
                'message' => 'No se encontraron términos y condiciones para el usuario'
            ];
        }
    }

    public function aceptarTyC($studentID){
        $startModel = $this->startModel->aceptarTyC($studentID);
        if($startModel !== null){
            return [
                'success' => true,
                'message' => 'Términos y condiciones aceptados'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'No se encontraron términos y condiciones para el usuario'
            ];
        }
    }
}