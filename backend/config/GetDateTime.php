<?php
namespace Esmefis\Gradebook;

date_default_timezone_set('America/Mexico_City');

class GetDateTime {
    public static function getDateTime($hours) {
        //se obntiene la fecha y hora actual en la zona horaria de la Ciudad de México y se convierte a UTC
        $date = new \DateTime('now', new \DateTimeZone('America/Mexico_City'));
        $date->setTimezone(new \DateTimeZone('UTC'));
        //se agrega la cantidad de horas que se desean agregar a la fecha y hora actual
        $date->modify('+' . $hours . ' hours');
        //se regresa la fecha y hora en formato ISO 8601
        return $date->format('Y-m-d\TH:i:s\Z');
    }
}
?>