<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../models/PaymentsModel.php';

use Esmefis\Gradebook\getEnv;
use Esmefis\Gradebook\DBConnection;

class PaymentsController{
    private $paymentsModel;

    public function __construct(DBConnection $dbConnection) {
        $this->paymentsModel = new PaymentsModel($dbConnection);
    }

    public function getPyamentsList(){
        $payments = $this->paymentsModel->getPaymentsList();
        if ($payments !== null) {
            $data = array();
            while($row = $payments->fetch_assoc()){
                $data[] = array(
                    'success' => true,
                    'payment_id' => $row['id'],
                    'student_id' => $row['id_student'],
                    'concept' => $row['concept'],
                    'extra' => $row['extra'],
                    'total' => $row['total']
                );
            }
        }else{
            $data[] = array(
                'success' => false,
                'message' => 'No se encontraron pagos registrados'
            );
        }
        return $data;
    }
}