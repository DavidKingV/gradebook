<?php
namespace Esmefis\Gradebook;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

getEnv::cargar();

class verifyAuth {
    public static function LocalSession($jwt){
        
        $secretKey = $_ENV['SECRET_KEY'] ?? NULL;

        if(isset($_SESSION['uID'])&&isset($_COOKIE['LoSessionToken'])){
            try {
                $decoded = JWT::decode($jwt, new Key($secretKey, 'HS256'));     
                return array('success' => true, 'uID' => $decoded->uID);
            } catch (\Exception $e) {
                return array('success' => false, 'message' => $e->getMessage());
            }
        }else{
            return array('success' => false, 'message' => 'No tiene permisos para realizar esta acción');
        }
    }

    public static function MicrosoftSession($accessToken){

        $microsoftAccessToken = $_SESSION["adnanhussainturki/microsoft"]["accessToken"] ?? NULL;

        if(isset($microsoftAccessToken)){
            if($accessToken == $microsoftAccessToken):
                return array('success' => true, 'accessToken' => $microsoftAccessToken);
            else:
                return array('success' => false, 'message' => 'El token de acceso de Microsoft Teams no coincide');
            endif;
        }else{
            return array('success' => false, 'message' => 'La sesión de Microsoft Teams ha expirado');
        }

    }
}