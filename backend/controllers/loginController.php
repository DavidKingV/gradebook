<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Esmefis\Gradebook\getEnv;
use Esmefis\Gradebook\DBConnection;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

getEnv::cargar();

class SessionManager {
    public static function startSession($uID, $uName) {
        session_set_cookie_params($_ENV['LIFE_TIME']);
        session_start();

        $_SESSION['uID'] = $uID;

        $payLoad = [
            'uID' => $uID,
            'uName' => $uName
        ];

        $jwt = JWT::encode($payLoad, $_ENV['SECRET_KEY'], 'HS256');
        setcookie('LoSessionToken', $jwt, time() + $_ENV['LIFE_TIME'], "/", "", true, true);

        return true;
    }

    public static function endSession() {
        session_start();

        if (!isset($_SESSION["adnanhussainturki/microsoft"])) {
            session_unset();
            session_destroy();  

            // Eliminar todas las cookies
            foreach ($_COOKIE as $key => $value) {
                setcookie($key, '', time() - 3600, '/');
            }

            return array("success" => true, "message" => "Sesión cerrada");
        }else{
            session_unset();
            session_destroy();  

            return array("success" => true, "microsoftLogout" => true, "message" => "Cerrando sesión de Microsoft");
        }

        
    }
}

class LoginControl{

    private $connection;

    public function __construct(DBConnection $dbConnection) {
        $this->connection = $dbConnection->getConnection();
    }

    public function login($userData){
        parse_str($userData, $userData);

        $user = filter_var($userData['user'], FILTER_SANITIZE_STRING);
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
                
                if ($stmt_update->affected_rows <= 0) {
                    return array("success" => false, "message" => "Error al actualizar la contraseña, por favor intente de nuevo más tarde");
                }else{
                    $stmt_update->close();
                    SessionManager::startSession($row['id'], $user);
                    return array("success" => true, "message" => "Inicio de sesión exitoso (y contraseña actualizada)", "uID" => $row['id']);
                }
                
            } elseif ($stored_hashed_password !== null && password_verify($password, $stored_hashed_password)) {
                // La contraseña está hashada en la base de datos y coincide con la contraseña proporcionada
                SessionManager::startSession($row['id'], $user);
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

class MicrosoftLoginControl{
    
    private $connection;
    
    public function __construct(DBConnection $dbConnection) {
        $this->connection = $dbConnection->getConnection();
    }

    public function createLoginData($accessToken, $userName, $userEmail){
        $query = "INSERT INTO microsoft_users (microsoft_access_token, microsoft_user_name, microsoft_user_email) VALUES (?, ?, ?)";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param('sss', $accessToken, $userName, $userEmail);
        $stmt->execute();
    
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $stmt->close();
                return true;
            }
        }

        $stmt->close();
        return false;
    }
    
    public function checkLoginData($accessToken, $userName = null, $userEmail = null){
        $query = "SELECT id FROM microsoft_users WHERE microsoft_access_token = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param('s', $accessToken);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $stmt->close();
            return array("success" => true);
        } else {
            $stmt->close();
            if ($this->createLoginData($accessToken, $userName, $userEmail)) {
                return array("success" => true, "message" => "Datos creados y guardados correctamente");
            } else {
                return array("success" => false, "message" => "No se pudieron guardar los datos");
            }
        }
    }
}