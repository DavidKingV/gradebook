<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Esmefis\Gradebook\DBConnection;

class StartModel{
    private $connection;

    public function __construct(DBConnection $dbConnection) {
        $this->connection = $dbConnection->getConnection();
    }

    public function consultarTyC($userID) {
        
        $sql = "SELECT accepted FROM tyc WHERE studentID = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param('i', $userID);

        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows === 0){
            return null;
        }else{
            $data = $result->fetch_assoc();
            return $data['accepted'];
        }
        $stmt->close();;
    }

    public function aceptarTyC($userID) {
        $sql = "UPDATE tyc SET accepted = 1 WHERE studentID = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param('i', $userID);

        $stmt->execute();
        $stmt->close();

        return true;
    }
}