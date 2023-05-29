<?php
require_once __DIR__.'/../../modelo/configuracion.php';
class C_Output_Ajustes
{

  function __construct()
  {

  }

  function cargarDatosAjustes(){
        $configuracion=new Configuracion();
        return $configuracion->getConfiguraciones();
  }
}
$ajustes=new C_Output_Ajustes();
$configuraciones=$ajustes->cargarDatosAjustes();
 ?>
