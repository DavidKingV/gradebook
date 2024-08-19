<?php
require_once __DIR__ . '/../../vendor/autoload.php';

date_default_timezone_set('America/Monterrey');

use Esmefis\Gradebook\DBConnection;
use Esmefis\Gradebook\getEnv;
use Google\Client;
use Google\Service\Calendar;

getEnv::cargar();

class PracticalModel{
    private $connection;

    public function __construct(DBConnection $dbConnection){
        $this->connection = $dbConnection->getConnection();
    }

    public function practicalHours($data){
        parse_str($data, $studentArray);
        $studentID = $_SESSION['studentID'] ?? $_SESSION['uID'] ?? null;

        if($studentID !== null){
            $sql = "SELECT * FROM students WHERE id = ?";
            $stmt = $this->connection->prepare($sql);
            $stmt->bind_param('i', $studentID);
            $stmt->execute();
            $result = $stmt->get_result();
           
            if($result->num_rows === 0){                
                return null;
            } else {
                $stmt->close();

                try {
                    $practicalSql = "INSERT INTO practical_hours (student_id, date, start, end) VALUES (?, ?, ?, ?)";
                    $practicalStmt = $this->connection->prepare($practicalSql);
                    
                    // Verifica si la preparación de la consulta fue exitosa
                    if (!$practicalStmt) {
                        throw new Exception("Error al preparar la consulta: " . $this->connection->error);
                    }
                    
                    $practicalStmt->bind_param('isss', $studentID, $studentArray['dateStart'], $studentArray['timeStart'], $studentArray['timeEnd']);
                    $practicalStmt->execute();
                
                    if ($practicalStmt->affected_rows === 0) {
                        $practicalStmt->close();
                        return ['success' => false, 'message' => 'Error al agregar horas de prácticas'];
                    } else {
                        $practicalStmt->close();
                        return ['success' => true, 'message' => 'Horas de prácticas agregadas correctamente'];
                    }
                } catch (mysqli_sql_exception $e) {
                    // Manejo de excepciones específicas de MySQL
                    return ['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()];
                } catch (Exception $e) {
                    // Manejo de otras excepciones
                    return ['success' => false, 'message' => 'Error inesperado: ' . $e->getMessage()];
                }
            }
        } else {
            return null;
        }
    }

    public function addGoogleCalendarEvent($data) {
        putenv('GOOGLE_APPLICATION_CREDENTIALS=' . __DIR__ . '/json/credentials.json');
        parse_str($data, $eventArray);

        $client = new Google_Client();
        $client->useApplicationDefaultCredentials();
        $client->setScopes(['https://www.googleapis.com/auth/calendar']);
        $calendarService = new Google_Service_Calendar($client);
        $id_calendar=$_ENV['ID_CALENDAR'];

        $datetime_start = new DateTime($eventArray['dateStart'] . 'T' . $eventArray['timeStart']);
        $datetime_end = new DateTime($eventArray['dateStart'] . 'T' . $eventArray['timeEnd']);

        $time_start =$datetime_start->format(\DateTime::RFC3339);
        $time_end =$datetime_end->format(\DateTime::RFC3339);

        $event = new Google_Service_Calendar_Event();
        $event->setSummary('Practicas Clinicas');
        $event->setDescription($eventArray['studentName']);

        $start = new Google_Service_Calendar_EventDateTime();
        $start->setDateTime($time_start);
        $event->setStart($start);

        $end = new Google_Service_Calendar_EventDateTime();
        $end->setDateTime($time_end);
        $event->setEnd($end);

        try{
            $createdEvent = $calendarService->events->insert($id_calendar, $event);
            return $createdEvent->getId();
        }catch(Exception $e){        
            return null;
        }
    }
    
}