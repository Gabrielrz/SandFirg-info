<?php
require_once __DIR__.'/conectar.php';

/**
 *
 */
class DatosPaypal{
  private $conexion;
  const PAYPAL_PARTNER=100;
  const PAYPAL_ACCOUNT=200;
  function __construct()
  {
    $this->conexion=Conectar::conexion();
  }

  function getTokenDeAcceso(){
    // NOTE: token de acceso que se usa en los controladores
        $client_id = $_ENV["CLIENT_ID"] ?: "";
        $secret_id = $_ENV["CLIENT_SECRET"] ?: "";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $_ENV['URL_ACCESS_TOKEN'].'/v1/oauth2/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
        curl_setopt($ch, CURLOPT_USERPWD, $client_id . ':' . $secret_id);

        $headers = array();
        $headers[] = 'Accept: application/json';
        $headers[] = 'Accept-Language: en_US';
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        return json_decode($result);
  }
  public static function staticGetTokenDeAcceso(){
    // NOTE: token de acceso que se usa en los controladores
        $client_id = $_ENV["CLIENT_ID"] ?: "";
        $secret_id = $_ENV["CLIENT_SECRET"] ?: "";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $_ENV['URL_ACCESS_TOKEN'].'/v1/oauth2/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
        curl_setopt($ch, CURLOPT_USERPWD, $client_id . ':' . $secret_id);

        $headers = array();
        $headers[] = 'Accept: application/json';
        $headers[] = 'Accept-Language: en_US';
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        return json_decode($result);
  }
  public function getDatos($id_usuario){


  }


  public function addDatosPaypal($id_usuario,$datos){

    $sql='INSERT INTO paypal_datos(id_usuario,datos_paypal) VALUES(:id_usuario,:datos_paypal)';
    $insert=$this->conexion->prepare($sql);
    $insert->bindParam(":id_usuario",$id_usuario);
    $insert->bindParam(":datos_paypal",$datos);
    $insert->execute();
    $idInsert=$this->conexion->lastInsertId();
    return $idInsert;


    // NOTE: los datos deben ir codificados con json_encode()

  }



  public function getDatosPaypal($id_usuario){
    // NOTE: devolver consulta si existen o false si no existen
    $sql='SELECT datos_paypal FROM paypal_datos WHERE id_usuario=:id_usuario';
    $consulta=$this->conexion->prepare($sql);
    $consulta->bindParam(":id_usuario",$id_usuario);
    $consulta->execute();
    $row=$consulta->rowCount();
    return ($row<=0)? false : $consulta->fetch();
  }

  public function getDatosPaypalPorSonido($idTono){
    $sql='SELECT datos_paypal FROM paypal_datos,tonos WHERE id_usuario=tonos.id_autor AND tonos.id=:id_tono';
    try {
        $select=$this->conexion->prepare($sql);
        $select->bindParam(":id_tono",$idTono);
        $select->execute();
        $row=$select->rowCount();
        return ($row<=0)? false : json_decode($select->fetch(PDO::FETCH_OBJ)->datos_paypal);
    } catch (\Exception $e) {
        error_log("sldb:".$e->getMessage());
        echo "sldb:".$e->getMessage();
    }
  }




}



 ?>
