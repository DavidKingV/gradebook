<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Esmefis\Gradebook\DBConnection;

class LoginModel {
    private $connection;

    public function __construct(DBConnection $dbConnection) {
        $this->connection = $dbConnection->getConnection();
    }

    public function getUserData($user) {
        $query = "SELECT id, student_id, password, hashed_password FROM login_students WHERE user = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param('s', $user);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return null;
        }
    }

    public function updateHashedPassword($userId, $hashedPassword) {
        $sql_update = "UPDATE login_students SET hashed_password = ? WHERE id = ?";
        $stmt_update = $this->connection->prepare($sql_update);
        $stmt_update->bind_param("si", $hashedPassword, $userId);
        $stmt_update->execute();
        $affectedRows = $stmt_update->affected_rows;
        $stmt_update->close();
        return $affectedRows > 0;
    }
}

?>