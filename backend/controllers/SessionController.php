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

    public function login(string $userData): array{
        // Convertir el string de entrada en un arreglo asociativo
        parse_str($userData, $parsedData);

        // Validar que existan las claves necesarias
        if (!isset($parsedData['user'], $parsedData['password'])) {
            return ['success' => false, 'message' => 'Datos incompletos'];
        }

        // Sanitizar y extraer los datos del usuario
        $username = filter_var($parsedData['user'], FILTER_SANITIZE_STRING);
        $password = $parsedData['password'];

        // Obtener la información del usuario desde el modelo
        $userRecord = $this->loginModel->getUserData($username);
        if (!$userRecord) {
            return ['success' => false, 'message' => 'Usuario no encontrado'];
        }

        // Preparar la conexión para obtener datos locales del usuario
        $dbConnection = new DBConnection();
        $connection = $dbConnection->getConnection();
        $userDataGetter = new GetUserData($connection);

        // Variables de la base de datos
        $storedPassword = $userRecord['password'];
        $storedHashedPassword = $userRecord['hashed_password'];

        // Caso 1: La contraseña no está hasheada y coincide con la ingresada
        if ($storedHashedPassword === null && $storedPassword === $password) {
            // Actualizar la contraseña con su versión hashada
            $newHashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $updateSuccess = $this->loginModel->updateHashedPassword($userRecord['id'], $newHashedPassword);
            if (!$updateSuccess) {
                return ['success' => false, 'message' => 'Error al actualizar la contraseña, por favor intente de nuevo más tarde'];
            }

            SessionModel::startSession($userRecord['student_id'], $username);
            $userDataGetter->getLocalUserData($userRecord['student_id']);
            return [
                'success' => true,
                'message' => 'Inicio de sesión exitoso (y contraseña actualizada)',
                'uID' => $userRecord['student_id']
            ];
        }
        // Caso 2: La contraseña está hasheada y coincide con la ingresada
        elseif ($storedHashedPassword !== null && password_verify($password, $storedHashedPassword)) {
            SessionModel::startSession($userRecord['student_id'], $username);
            $userDataGetter->getLocalUserData($userRecord['student_id']);
            return [
                'success' => true,
                'message' => 'Inicio de sesión exitoso',
                'uID' => $userRecord['student_id']
            ];
        }
        // Caso 3: La contraseña no coincide
        else {
            return ['success' => false, 'message' => 'Contraseña incorrecta'];
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