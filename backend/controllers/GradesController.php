<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../models/GradesModel.php';

use Esmefis\Gradebook\getEnv;
use Esmefis\Gradebook\DBConnection;

class GradesController {
    private $gradesModel;

    public function __construct(DBConnection $dbConnection) {
        $this->gradesModel = new GradesModel($dbConnection);
    }

    public function getGrades($userID) {
        $grades = $this->gradesModel->getGrades($userID);
        if ($grades !== null) {
            $data = array();
            while ($row = $grades->fetch_assoc()) {
                $data[] = array(
                    'success' => true,
                    'grade_id' => $row['grade_id'],
                    'student_id' => $row['student_id'],
                    'student_name' => $row['student_name'],
                    'subject_id' => $row['subject_id'],
                    'subject_name' => $row['subject_name'],
                    'subject_child_id' => $row['subject_child_id'],
                    'subject_child_name' => $row['subject_child_name'],
                    'continuous_grade' => $row['continuous_grade'],
                    'exam_grade' => $row['exam_grade'],
                    'final_grade' => $row['final_grade'],
                    'update_at' => $row['update_at']
                );
            }
        } else {
            $data[] = array(
                'success' => false,
                'message' => 'No se encontraron calificaciones para el usuario'
            );
        }
        return $data;
    }
}

?>