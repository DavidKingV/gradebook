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
        // Verificar si ya existe un registro con el studentID
        $sqlCheck = "SELECT COUNT(*) FROM tyc WHERE studentID = ?";
        $stmtCheck = $this->connection->prepare($sqlCheck);
        $stmtCheck->bind_param('i', $userID);
        $stmtCheck->execute();
        $stmtCheck->bind_result($count);
        $stmtCheck->fetch();
        $stmtCheck->close();

        if ($count > 0) {
            // Si existe un registro, se hace un UPDATE
            $sqlUpdate = "UPDATE tyc SET accepted = 1 WHERE studentID = ?";
            $stmtUpdate = $this->connection->prepare($sqlUpdate);
            $stmtUpdate->bind_param('i', $userID);
            $stmtUpdate->execute();
            
            if ($stmtUpdate->affected_rows === 0) {
                $stmtUpdate->close();
                return null;
            } else {
                return true;
            }
        } else {
            // Si no existe un registro, se hace un INSERT
            $sqlInsert = "INSERT INTO tyc (studentID, accepted) VALUES (?, 1)";
            $stmtInsert = $this->connection->prepare($sqlInsert);
            $stmtInsert->bind_param('i', $userID);
            $stmtInsert->execute();
            
            if ($stmtInsert->affected_rows === 0) {
                $stmtInsert->close();
                return null;
            } else {
                return true;
            }
        }
    }
}