<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Esmefis\Gradebook\DBConnection;

class PaymentsModel {
    private $connection;

    public function __construct(DBConnection $dbConnection) {
        $this->connection = $dbConnection->getConnection();
    }

    public function getPaymentsList() {

        $query = "SELECT * FROM students_payments WHERE id_student = ? ORDER BY payment_date DESC";
        $payments = $this->connection->prepare($query);
        $userId = $_SESSION['userId'] ?? $_SESSION['studentID'] ?? $_SESSION['uID'];
        $payments->bind_param('i', $userId);

        $payments->execute();
        $result = $payments->get_result();
        if ($result->num_rows === 0) {
            return null;
        }
        
        $payments->close();
        return $result;
    }

    public function getPaymentsAmount() {
        $query = "SELECT monthly_amount FROM students_payments_amounts";
        $payments = $this->connection->prepare($query);
        $payments->execute();
        $result = $payments->get_result();
        if ($result->num_rows === 0) {
            return null;
        }
        
        $payments->close();
        return $result;
    }
}