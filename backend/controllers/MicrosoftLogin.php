<?php
session_start();
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/SessionController.php';

use Esmefis\Gradebook\DBConnection;
use Esmefis\Gradebook\GetUserData;
use myPHPnotes\Microsoft\Models\User;
use myPHPnotes\Microsoft\Auth;
use myPHPnotes\Microsoft\Handlers\Session;

$dbConnection = new DBConnection();
$connection = $dbConnection->getConnection();
$userData = new GetUserData($connection);

$microsoft = new Auth(Session::get("tenant_id"),Session::get("client_id"),  Session::get("client_secret"), Session::get("redirect_uri"), Session::get("scopes"));

try {
    $tokens = $microsoft->getToken($_REQUEST['code'], Session::get("state"));
    $microsoft->setAccessToken($tokens->access_token);
    $user = (new User);

    if($user->data) {
        $userName = $user->data->getDisplayName();
        $userEmail = $user->data->getMail();

        if($userName && $userEmail) {
            $userData->getMicrosoftUserData($tokens->access_token);
            echo "<script>
                window.opener.postMessage({ accessToken: '{$tokens->access_token}' }, '*');
                window.close();
            </script>";
        } else {
            throw new Exception('User data cannot be saved in the database');
        }

    } else {
        throw new Exception('User data not found');
    }
} catch (Exception $e) {
    echo "<script>
        window.opener.postMessage({ error: '{$e->getMessage()}' }, '*');
        window.close();
        </script>";
}
?>