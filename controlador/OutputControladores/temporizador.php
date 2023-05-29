<?php

  class controladorTemporizador{

    function __construct()
    {

    }



    public function obtenerFecha(){
          //$fechaBD="2019-12-20 12:00:00";

       date_default_timezone_set('Europe/Madrid');
       //23:59:59
      /* $date1 = new DateTime("2019-12-16");
       $date2 = new DateTime("now");
       $diff = $date2->diff($date1);
       //echo $diff->days . ' dias '. $diff->h." horas ". $diff->i ." minutos ".$diff->s ." segundos ";
       //echo date_default_timezone_get();
       //echo "hola mundo";*/
        $infoFechas = array('anio' => "2019",
                            'mes' => "12",
                            'dia' => "20",
                            'hora' => "11",
                            'minuto' => "59",
                            'segundo' => "59",);
      echo json_encode($infoFechas);
    }

  }

$temporizador=new controladorTemporizador();
$temporizador->obtenerFecha();
