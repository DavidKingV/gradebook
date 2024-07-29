<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Esmefis\Gradebook\getEnv;

getEnv::cargar();

class SessionModel {
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
        } else {
            session_unset();
            session_destroy();  

            return array("success" => true, "microsoftLogout" => true, "message" => "Cerrando sesión de Microsoft");
        }
    }
}
