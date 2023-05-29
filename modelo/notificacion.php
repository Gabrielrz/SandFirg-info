<?php
require_once "conectar.php";
/**
 *
 */
class Notificacion
{
  protected $conexion;
  public const NOTREAD=0;
  public const READED=1;
  public const IMPORTANT=100;
  public const NORMAL=10;
  public const CONSIDERABLE=50;
  function __construct()
  {
    $this->conexion=Conectar::conexion();
    date_default_timezone_set('Europe/Madrid');
  }


    public function crearNotificacion($idNotificacion,$idUsuario,$statusNotificacion,$gradoNotificacion,$dataNotificacion){
      if($this->comprobarNotificacion($idNotificacion,$idUsuario)==false){
        $sql='INSERT INTO notificaciones (id_notificacion,id_usuario,status_notificacion,grado_notificacion,data_notificacion)
              values(:id_notificacion,:id_usuario,:status_notificacion,:grado_notificacion,:data_notificacion)';
          try {
              $insert=$this->conexion->prepare($sql);
              $insert->bindParam(':id_notificacion',$idNotificacion);
              $insert->bindParam(':id_usuario',$idUsuario);
              $insert->bindParam(':status_notificacion',$statusNotificacion);
              $insert->bindParam(':grado_notificacion',$gradoNotificacion);
              $insert->bindParam(':data_notificacion',$dataNotificacion);
              $insert->execute();
              $row=$insert->rowCount();
              return ($row<=0)? false : true;
          } catch (\Exception $e) {
              error_log("crdbNotificacion:".$e->getMessage());
              echo "crdbNotificacion:".$e->getMessage();
          }
      }
    }

    public function comprobarNotificacion($idNotificacion,$idUsuario){
      $sql='SELECT id FROM notificaciones WHERE id_notificacion=:id_notificacion AND id_usuario=:id_usuario';
      try {
          $select=$this->conexion->prepare($sql);
          $select->bindParam(':id_notificacion',$idNotificacion);
          $select->bindParam(':id_usuario',$idUsuario);
          $select->execute();
          $row=$select->rowCount();
          return ($row<=0)? false : true;
      } catch (\Exception $e) {
          error_log("sldb:".$e->getMessage());
          echo "sldb:".$e->getMessage();
      }
    }
    public function setnotificacionLeida($idNotificacion,$idUsuario){
      $sql='UPDATE notificaciones SET status_notificacion=:status WHERE id_notificacion=:id_notificacion AND id_usuario=:id_usuario';
    try {
        $update=$this->conexion->prepare($sql);
        $status=self::READED;
        $update->bindParam(':status',$status);
        $update->bindParam(':id_notificacion',$idNotificacion);
        $update->bindParam(':id_usuario',$idUsuario);
        $update->execute();
        $row=$update->rowCount();
        return ($row<=0)? false : true;
    } catch (\Exception $e) {
        error_log("updb:".$e->getMessage());
        echo "updb:".$e->getMessage();
    }

    }


    public function getNotificaciones($idUsuario){
      $sql='SELECT * FROM notificaciones WHERE id_usuario=:id_usuario AND status_notificacion=:status';
      try {
          $select=$this->conexion->prepare($sql);
          $select->bindParam(':id_usuario',$idUsuario);
          $status=self::NOTREAD;
          $select->bindParam(':status',$status);
          $select->execute();
          $row=$select->rowCount();
          return ($row<=0)? false : $select->fetchAll(PDO::FETCH_OBJ);
      } catch (\Exception $e) {
          error_log("sldbnotificacion:".$e->getMessage());
          echo "sldbnotificacion:".$e->getMessage();
      }
    }

}
