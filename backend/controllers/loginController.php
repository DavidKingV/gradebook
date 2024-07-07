<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Esmefis\Gradebook\getEnv;
use Esmefis\Gradebook\DBConnection;
use Esmefis\Gradebook\verifyAuth;

class LoginControl{

    private $connection;

    public function __construct(DBConnection $dbConnection) {
        $this->connection = $dbConnection->getConnection();
    }

    public function login($userData){
        parse_str($userData, $userData);

        $user = $userData['user'];
        $password = $userData['password'];

        $query = "SELECT id, password, hashed_password FROM login_students WHERE user = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param('s', $user);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $stmt->close();

            $stored_password = $row['password'];
            $stored_hashed_password = $row['hashed_password'];
    
            if ($stored_hashed_password === null && $stored_password === $password) {
                // La contraseña no está hashada en la base de datos, pero coincide con la contraseña original
                // Actualizar la contraseña con su versión hashada
                $new_hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $sql_update = "UPDATE login_students SET hashed_password = ? WHERE id = ?";
                $stmt_update = $this->connection->prepare($sql_update);
                $stmt_update->bind_param("si", $new_hashed_password, $row['id']);
                $stmt_update->execute();
                $stmt_update->close();
                
                return array("success" => true, "message" => "Inicio de sesión exitoso (y contraseña actualizada)", "uID" => $row['id']);
            } elseif ($stored_hashed_password !== null && password_verify($password, $stored_hashed_password)) {
                // La contraseña está hashada en la base de datos y coincide con la contraseña proporcionada
                return array("success" => true, "message" => "Inicio de sesión exitoso", "uID" => $row['id']);
            } else {
                // La contraseña no coincide
                return array("success" => false, "message" => "Contraseña incorrecta");
            }
        } else {
            // El usuario no se encontró en la base de datos
            return array("success" => false, "message" => "Usuario no encontrado");
        }
    }

}