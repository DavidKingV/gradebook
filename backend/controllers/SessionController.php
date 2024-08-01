<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../models/SessionModel.php';
require_once __DIR__ . '/../models/LoginModel.php';

use Esmefis\Gradebook\getEnv;
use Esmefis\Gradebook\DBConnection;
use Esmefis\Gradebook\GetUserData;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

getEnv::cargar();

class SessionController {
    public function startSession($uID, $uName) {
        $resultado = SessionModel::startSession($uID, $uName);
        return $resultado;
    }

    public function endSession() {
        $resultado = SessionModel::endSession();
        return $resultado;
    }
}

class LoginController{
    private $loginModel;

    public function __construct(DBConnection $dbConnection) {
        $this->loginModel = new LoginModel($dbConnection);
    }

    public function login($userData){
        parse_str($userData, $userData);

        $user = filter_var($userData['user'], FILTER_SANITIZE_STRING);
        $password = $userData['password'];

        $userData = $this->loginModel->getUserData($user);

        if ($userData) {
            $dbConnection = new DBConnection();
            $connection = $dbConnection->getConnection();
            $guserData = new GetUserData($connection);
            
            $stored_password = $userData['password'];
            $stored_hashed_password = $userData['hashed_password'];
    
            if ($stored_hashed_password === null && $stored_password === $password) {
                // La contraseña no está hashada en la base de datos, pero coincide con la contraseña original
                // Actualizar la contraseña con su versión hashada                
                $new_hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $updateSuccess = $this->loginModel->updateHashedPassword($userData['id'], $new_hashed_password);
                
                if (!$updateSuccess) {
                    return array("success" => false, "message" => "Error al actualizar la contraseña, por favor intente de nuevo más tarde");
                } else {
                    SessionModel::startSession($userData['student_id'], $user);
                    $guserData->getLocalUserData($userData['student_id']);
                    return array("success" => true, "message" => "Inicio de sesión exitoso (y contraseña actualizada)", "uID" => $userData['student_id']);
                }
                
            } elseif ($stored_hashed_password !== null && password_verify($password, $stored_hashed_password)) {
                // La contraseña está hashada en la base de datos y coincide con la contraseña proporcionada
                SessionModel::startSession($userData['student_id'], $user);
                $guserData->getLocalUserData($userData['student_id']);
                return array("success" => true, "message" => "Inicio de sesión exitoso", "uID" => $userData['student_id']);
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
        $query = "SELECT displayName, mail FROM microsoft_students WHERE displayName = ? AND mail = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param('ss', $userName, $userEmail);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $stmt->close();
            return array("success" => true);
        } else {
            $stmt->close();
            return array("success" => false, "message" => "User data not found");
        }
    }
}