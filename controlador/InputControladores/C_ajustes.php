<?php
require __DIR__.'/../../modelo/configuracion.php';

class C_Input_Ajustes{

  protected $configuracion;
  function __construct()
  {
    $this->configuracion=new Configuracion();
  }
  function mensajes($validacion,$mensaje){
     return json_encode(array(
      'validacion'=>$validacion,
      'mensaje'=>$mensaje));
  }
  function crearConfiguracion(){
    $accion=filter_input(INPUT_POST,'accion',FILTER_SANITIZE_SPECIAL_CHARS);
      if($accion=='insert'){
        $nombre_config=filter_input(INPUT_POST,'nombre_config',FILTER_SANITIZE_SPECIAL_CHARS);
        $inp_config=filter_input(INPUT_POST,'inp_config',FILTER_SANITIZE_SPECIAL_CHARS);

        if(empty($nombre_config)||empty($inp_config)){
          echo $this->mensajes(true,'los datos estan incompletos!');
        }else{
          if($this->configuracion->addConfiguracion($nombre_config,$inp_config)){
              echo $this->mensajes(true,'configuracion creada con exito!');
          }
        }
      }
  }

  function borrarConfiguracion(){
    $accion=filter_input(INPUT_POST,'accion',FILTER_SANITIZE_SPECIAL_CHARS);
      if($accion=='delete'){
        //$ids=filter_input(INPUT_POST,'checks_ids_config',FILTER_SANITIZE_SPECIAL_CHARS);
        $ids=explode(',',filter_input(INPUT_POST,'checks_ids_config',FILTER_SANITIZE_SPECIAL_CHARS));
        for($i=0;$i<count($ids);$i++){
          if($this->configuracion->deleteConfiguracion($ids[$i])){
            echo $this->mensajes(true,'configuracion eliminada con exito!');
          }
        }
      }
  }
}
$cinputajustes=new C_Input_Ajustes();
$cinputajustes->crearConfiguracion();
$cinputajustes->borrarConfiguracion();

?>
