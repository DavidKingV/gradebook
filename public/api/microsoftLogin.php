<?php
session_start();
require_once __DIR__ . '/../../vendor/autoload.php';

use Esmefis\Gradebook\getEnv;
use myPHPnotes\Microsoft\Auth;

getEnv::cargar();

$tenant = $_ENV['TENANT_ID'];
$client_id = $_ENV['CLIENT_ID'];
$client_secret = $_ENV['CLIENT_SECRET'];
$callback = $_ENV['CALLBACK_URL'];
$scopes = ["User.Read"];

$microsoft = new Auth($tenant, $client_id,  $client_secret, $callback, $scopes);

header("location: ". $microsoft->getAuthUrl()); //Redirecting to get access token

?>