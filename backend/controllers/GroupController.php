<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../models/GroupModel.php';

use Esmefis\Gradebook\DBConnection;

class GroupController {
    private $groupModel;

    public function __construct(DBConnection $dbConnection) {
        $this->groupModel = new GroupModel($dbConnection);
    }

    public function getGroupMaterial($groupId) {
        $resultado = $this->groupModel->getGroupMaterial($groupId);
        return $resultado;
    }
}

?>