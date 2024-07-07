<?php
namespace Esmefis\Gradebook;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class verifyAuth {
    public static function verify($jwt){
        getEnv::cargar();
        
        $secretKey = $_ENV['SECRET_KEY'] ?? NULL;

        if(isset($_SESSION['uID'])&&isset($_COOKIE['AuthJWT'])){
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
}