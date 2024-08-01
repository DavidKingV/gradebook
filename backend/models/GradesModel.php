<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Esmefis\Gradebook\DBConnection;

class GradesModel {
    private $connection;

    public function __construct(DBConnection $dbConnection) {
        $this->connection = $dbConnection->getConnection();
    }

    public function getGrades($userID) {
    
        if (!$userID):return null;
        endif;

        $query = "SELECT 
        sg.id AS grade_id,
        s.id AS student_id,
        s.nombre AS student_name,
        sub.id AS subject_id,
        sub.nombre AS subject_name,
        NULL AS subject_child_id,
        NULL AS subject_child_name,
        sg.continuos_grade AS continuous_grade,
        sg.exam_grade AS exam_grade,
        sg.final_grade AS final_grade,
        sg.updated_at AS update_at
        FROM 
            student_grades sg
        JOIN 
            students s ON sg.id_student = s.id
        JOIN 
            subjects sub ON sg.id_subject = sub.id
        WHERE 
            sg.id_student = ?

        UNION

        SELECT 
            sgc.id AS grade_id,
            s.id AS student_id,
            s.nombre AS student_name,
            sub.id AS subject_id,
            sub.nombre AS subject_name,
            sub_child.id AS subject_child_id,
            sub_child.nombre AS subject_child_name,
            sgc.continuos_grade AS continuous_grade,
            sgc.exam_grade AS exam_grade,
            sgc.final_grade AS final_grade,
            sgc.updated_at AS update_at
        FROM 
            student_grades_child sgc
        JOIN 
            students s ON sgc.id_student = s.id
        JOIN 
            subjects sub ON sgc.id_subject = sub.id
        JOIN 
            subject_child sub_child ON sgc.id_subject_child = sub_child.id
        WHERE 
            sgc.id_student = ?";

        $stmt = $this->connection->prepare($query);
        if ($stmt === false) {
            return null;
        }
        $stmt->bind_param('ii', $userID, $userID);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            return null;
        }
        $stmt->close();
        return $result;
    }
}