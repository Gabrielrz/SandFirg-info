<?php
require_once 'conectar.php';
/**
 *
 */
class Configuracion {
  protected $conexionBD;
  function __construct()
  {
    $this->conexionBD=Conectar::conexion();
    date_default_timezone_set('Europe/Madrid');
  }
  /*
  *deberia crear una funcion en otro
  *controlador (controlador/controlador)
  *que se encarge de introducir los datos
  *de la tabla configuraciones
  *requeridos inicialmente por la pagina
  *en caso de que por algun motivo la bbdd se borre
  *las tablas que necesitan de este proceso son
  * (configuraciones;roles;productos//tal vez)
  */


  public function getConfiguracion($clave){
    $sql="SELECT *
          FROM configuraciones
          WHERE clave=:clave";
    $consulta=$this->conexionBD->prepare($sql);
    $consulta->bindParam(':clave',$clave);
    $consulta->execute();
    $resultado=$consulta->fetch(PDO::FETCH_OBJ);
    return $resultado;
  }
  public function getConfiguraciones(){
    $sql="SELECT *
          FROM configuraciones";
    $consulta=$this->conexionBD->prepare($sql);
    $consulta->execute();
    $resultado=$consulta->fetchAll(PDO::FETCH_OBJ);
    return $resultado;
  }

  public function addConfiguracion($nombre,$config){
      $sql='INSERT INTO configuraciones (clave,valor,id_role)
  												values(:clave,:configuracion,:id_role)';
      $role=1;
      $insert=$this->conexionBD->prepare($sql);
      $insert->bindParam(':clave',$nombre);
      $insert->bindParam(':configuracion',$config);
      $insert->bindParam(':id_role',$role);
      return $insert->execute();
  }

  public function deleteConfiguracion($id){
    $sql="DELETE
          FROM configuraciones
          WHERE id=:id";
    $delete=$this->conexionBD->prepare($sql);
    $delete->bindParam(':id',$id);
    return $delete->execute();
  }


}


 ?>
