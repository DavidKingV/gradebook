<?php
require_once __DIR__ . '/../../vendor/autoload.php';

//session_start();

use Esmefis\Gradebook\DBConnection;
use Esmefis\Gradebook\getEnv;
use Esmefis\Gradebook\GetDateTime;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

getEnv::cargar();

class GroupModel {
    private $connection;

    public function __construct(DBConnection $dbConnection) {
        $this->connection = $dbConnection->getConnection();
    }

    public function getGroupMaterial($groupId) {
        try {
            $sql = "SELECT * FROM groupsMaterial WHERE idGroup = ?";
            $stmt = $this->connection->prepare($sql);
            
            if(!$stmt) {
                throw new Exception("Error al preparar la consulta");
            }

            $stmt->bind_param('i', $groupId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                return array(['success' => false, 'message' => 'No hay horarios para este grupo']);
            }
            
            $materials = [];
            while ($row = $result->fetch_assoc()) {
                $materials[] = [
                    'success' => true,
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'url' => $row['url'],
                    'lastUpdate' => $row['lastUpdate']
                ];
            };

            $stmt->close();

            return $materials;
        } catch(Exception $e) {
            return array(['success' => false, 'message' => 'Error al obtener los materiales de este grupo' . $e->getMessage()]);
        }
    }

    public function verifyTypeGroup(string $studentId): array
{
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    try {
        $sql = "
            SELECT c.subarea
            FROM students s
            JOIN groups g ON s.id_group = g.id
            JOIN carreers c ON g.id_carreer = c.id
            WHERE s.id = ?
        ";
        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param('s', $studentId);
        $stmt->execute();

        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows === 0) {
            return ['success' => false, 'message' => 'No se encontró grupo para ese estudiante'];
        }

        return ['success' => true, 'type' => $result->fetch_assoc()['subarea']];
    } catch (mysqli_sql_exception $e) {
        return ['success' => false, 'message' => 'Error en la consulta: ' . $e->getMessage()];
    }
}
    
}