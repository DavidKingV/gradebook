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

    public function getMicrosoftUserData($accessToken) {

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $client = new Client();
    
        try {
            // Obtener datos del usuario
            $userResponse = $client->get('https://graph.microsoft.com/v1.0/me', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                ]
            ]);
            $userData = json_decode($userResponse->getBody()->getContents(), true);
    
            if ($userData === null) {
                return ['success' => false, 'message' => 'Error al decodificar la respuesta de la API'];
            }else{
                $_SESSION['userMicrosoft'] = $userData['id'];
                $_SESSION['userName'] = $userData['displayName'];
                $_SESSION['userEmail'] = $userData['mail'];
            }
            
            // Consulta SQL para obtener el ID del estudiante
            $userId = "SELECT student_id FROM microsoft_students WHERE id = ?";
            $stmt = $this->connection->prepare($userId);
            
            $stmt->bind_param('s', $userData['id']);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();
                if (!empty($row['student_id'])) {
                    $_SESSION['studentID'] = $row['student_id'];
                }
            }
    
            $stmt->close();
            
            return ['success' => true, 'message' => 'Datos obtenidos correctamente'];
    
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            if ($statusCode == 401) {
                return ['success' => false, 'message' => 'Error de autenticación: ' . $e->getMessage()];
            } else if ($statusCode == 404) {
                return ['success' => false, 'message' => 'Error del cliente: ' . $e->getMessage()];
            } else {
                return ['success' => false, 'message' => 'Error inesperado: ' . $e->getMessage()];
            }
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            return ['success' => false, 'message' => 'Error en la solicitud: ' . $e->getMessage()];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error en la consulta SQL: ' . $e->getMessage()];
        }
    }
    

    public function getProfilePhotoMicrosoft($accessToken) {
        $client = new Client();
    
        try {
            // Obtener foto del usuario
            $photoResponse = $client->get('https://graph.microsoft.com/v1.0/me/photo/$value', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Accept' => 'image/jpeg'
                ]
            ]);
            $photo = base64_encode($photoResponse->getBody()->getContents());
            $url = 'data:image/jpeg;base64,' . $photo;
    
            $_SESSION['userPhoto'] = $url;
            return ['success' => true, 'message' => 'Foto de perfil obtenida correctamente'];
    
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            if ($statusCode == 401) {
                return ['success' => false, 'message' => 'Error de autenticación: ' . $e->getMessage()];
            } else if ($statusCode == 404) {
                $_SESSION['userPhoto'] = $_ENV['DEFAULT_PROFILE_PHOTO'];
                return ['success' => true , 'message' => 'Sin foto de perfil, usando la foto por defecto'];
            } else {
                return ['success' => false, 'message' => 'Error del cliente: ' . $e->getMessage()];
            }
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            return ['success' => false, 'message' => 'Error del servidor: ' . $e->getMessage()];
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            return ['success' => false, 'message' => 'Error en la solicitud: ' . $e->getMessage()];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error inesperado: ' . $e->getMessage()];
        }
    }
    
    
    
}