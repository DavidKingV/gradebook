<?php
require_once __DIR__ . '/../../vendor/autoload.php';

session_start();

use Esmefis\Gradebook\getEnv;
use Esmefis\Gradebook\GetDateTime;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

getEnv::cargar();

class ScheduleModel {
    public static function getSchedules() {
        $client = new Client();
    
        $startDateTime = GetDateTime::getDateTime(0);
        $endDateTime = GetDateTime::getDateTime(1);
        $microsoftUrl = 'https://graph.microsoft.com/v1.0/me/calendarview?startdatetime=' . $startDateTime . '&enddatetime=' . $endDateTime;
    
        try {
            $response = $client->request('GET', $microsoftUrl, [
                'headers' => ['Authorization' => 'Bearer ' . $_SESSION["adnanhussainturki/microsoft"]["accessToken"]]
            ]);
    
            $responseArray = json_decode($response->getBody()->getContents(), true);
    
            $eventsArray = [];
    
            if (!empty($responseArray['value'])) {
                $events = $responseArray['value'];
    
                foreach ($events as $event) {
                    $eventArray = [
                        'success' => true,
                        'subject' => $event['subject'],
                        'start' => $event['start']['dateTime'],
                        'end' => $event['end']['dateTime'],
                        'joinUrl' => isset($event['onlineMeeting']['joinUrl']) ? $event['onlineMeeting']['joinUrl'] : null // Verifica si joinUrl está presente
                    ];
                    array_push($eventsArray, $eventArray);
                }
            } else {
                $eventsArray[] = [
                    'success' => false,
                    'message' => 'No hay eventos programados'
                ];
            }
    
            return $eventsArray;
    
        } catch (RequestException $e) {
            // Captura y maneja la excepción de GuzzleHttp
            if ($e->hasResponse() && $e->getResponse()->getStatusCode() === 401) {
                return array(['success' => false, 'message' => 'Token expirado']);
            } else {
                // Puedes manejar otros tipos de errores aquí
                return array(['success' => false, 'message' => 'Error en la solicitud: ' . $e->getMessage()]);
            }
        }
    }
}