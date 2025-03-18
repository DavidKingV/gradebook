<?php
require_once __DIR__ . '/../../vendor/autoload.php';

session_start();

use Esmefis\Gradebook\DBConnection;
use Esmefis\Gradebook\getEnv;
use Esmefis\Gradebook\GetDateTime;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

getEnv::cargar();

class ScheduleModel {
    private $connection;

    public function __construct(DBConnection $dbConnection) {
        $this->connection = $dbConnection->getConnection();
    }

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

    public function getEvents($groupId){
        try{
            $sql = "SELECT * FROM schedules WHERE id_group = ?";
            $stmt = $this->connection->prepare($sql);

            if(!$stmt){
                throw new Exception('Error al preparar la consulta');
            }

            $stmt->bind_param('i', $groupId);

            if (!$stmt->execute()) {
                throw new Exception("Error ejecutando sentencia " . $stmt->error);
            }

            $result = $stmt->get_result();

            if($result->num_rows === 0){
                return ['success' => false, 'message' => 'No se encontraron eventos'];
            }

            $events = [];

            foreach($result as $row){
                $events[] = [
                    'id'      => $row['id'],
                    'title'   => $row['title'], // título del rowo mostrado en calendario
                    'start'   => $row['date'] . 'T' . $row['start'],
                    'end'     => $row['date'] . 'T' . $row['end'],
                    'allDay'  => false,
                ];
            }

            return ['success' => true, 'events' => $events];

        }catch(\Exception $e){
            return ['success' => false, 'message' => 'Error en la consulta SQL: ' . $e->getMessage()];
        }
    }

    public function getEventDetails($eventId){
        try{
            $sql = "SELECT * FROM schedules WHERE id = ?";
            $stmt = $this->connection->prepare($sql);

            if(!$stmt){
                throw new Exception('Error al preparar la consulta');
            }

            $stmt->bind_param('i', $eventId);

            if (!$stmt->execute()) {
                throw new Exception("Error ejecutando sentencia " . $stmt->error);
            }

            $result = $stmt->get_result();

            if($result->num_rows === 0){
                return ['success' => false, 'message' => 'No se encontraron eventos'];
            }

            $event = $result->fetch_assoc();

            return ['success' => true, 'event' => $event];

        }catch(\Exception $e){
            return ['success' => false, 'message' => 'Error en la consulta SQL: ' . $e->getMessage()];
        }
    }
}