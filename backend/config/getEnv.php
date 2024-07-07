<?php

namespace Esmefis\Gradebook;

class getEnv{

    public static function cargar(){
        $dotEnv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
        $dotEnv->load();
    }

}

?>