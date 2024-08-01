<?php
namespace Esmefis\Gradebook;

use GuzzleHttp\Client;

class UserRepository {
    private $connection;

    public function __construct($connection){
        $this->connection = $connection;
    }

    public function findLocalUserById($uID){
        $stmt = $this->connection->prepare("SELECT * FROM students WHERE id = ?");
        if ($stmt === false) {
            throw new \Exception($this->connection->error);
        }

        $stmt->bind_param("i", $uID);
        $stmt->execute();
        $result = $stmt->get_result();

        $user = null;
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
        }

        $stmt->close();
        return $user;
    }
    
}

class GetUserData{
    private $connection;
    private $userRepository;
        
    public function __construct($connection){
        $this->connection = $connection;
        $this->userRepository = new UserRepository($connection);
    }

    public function getLocalUserData($uID){
        $user = $this->userRepository->findLocalUserById($uID);

        if ($user) {

            $_SESSION['userName'] = $user['nombre'];
            $_SESSION['userEmail'] = $user['email'];
            $_SESSION['userPhone'] = $user['telefono'];
            $_SESSION['userPhoto'] = $_ENV['DEFAULT_PROFILE_PHOTO'];

            return;
        } else {
            return array("success" => false, "message" => "Usuario no encontrado");
        }

    }

    public function getMicrosoftUserData($accessToken){
        $client = new Client();

        try {
            // Obtener datos del usuario
            $userResponse = $client->get('https://graph.microsoft.com/v1.0/me', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                ]
            ]);
            $userData = json_decode($userResponse->getBody()->getContents(), true);

            // Obtener foto del usuario
            $photoResponse = $client->get('https://graph.microsoft.com/v1.0/me/photo/$value', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Accept' => 'image/jpeg'
                ]
            ]);
            $photo = base64_encode($photoResponse->getBody()->getContents());

            // Guardar datos en variables de sesión
            $_SESSION['userName'] = $userData['displayName'];
            $_SESSION['userEmail'] = $userData['mail'];
            $_SESSION['userMicrosoft'] = $userData['id'];
            $_SESSION['userPhoto'] = 'data:image/jpeg;base64,' . $photo ?? $_ENV['DEFAULT_PROFILE_PHOTO'];

            $userId = "SELECT student_id FROM microsoft_students WHERE id = ?";
            $stmt = $this->connection->prepare($userId);
            $stmt->bind_param('s', $userData['id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();

            if ($result->num_rows == 1):$_SESSION['studentID'] = $result->fetch_assoc()['student_id'];
            endif;

            return;
            
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            // Manejo de errores específico para códigos de estado HTTP
            $statusCode = $e->getResponse()->getStatusCode();
            if ($statusCode == 401) {
                // Error de autenticación
                return ['success' => false, 'message' => 'Unauthorized. Please check your access token.'];
            } else {
                // Otros errores de cliente
                return ['success' => false, 'message' => 'Client error: ' . $e->getMessage()];
            }
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            // Manejo de errores del servidor
            return ['success' => false, 'message' => 'Server error: ' . $e->getMessage()];
        } catch (\Exception $e) {
            // Manejo de otros errores
            return ['success' => false, 'message' => 'Unexpected error: ' . $e->getMessage()];
        }
    }
    
}