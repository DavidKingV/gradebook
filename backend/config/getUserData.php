<?php
namespace Esmefis\Gradebook;

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

    public function findMicrosoftUserByAccessToken($accessToken){
        $stmt = $this->connection->prepare("SELECT * FROM microsoft_users WHERE microsoft_access_token = ?");
        if ($stmt === false) {
            throw new \Exception($this->connection->error);
        }

        $stmt->bind_param("s", $accessToken);
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
            return array(
                "success" => true, 
                "userName" => $user['nombre'], 
                "email" => $user['email'], 
                "phone" => $user['telefono']
            );
        } else {
            return array("success" => false, "message" => "Usuario no encontrado");
        }

    }

    public function getMicrosoftUserData($accessToken){
        $user = $this->userRepository->findMicrosoftUserByAccessToken($accessToken);

        if ($user) {
            return array(
                "success" => true, 
                "userName" => $user['microsoft_user_name'], 
                "userEmail" => $user['microsoft_user_email']
            );
        } else {
            return array("success" => false, "message" => "Usuario no encontrado");
        }

    }
    
}