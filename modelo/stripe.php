<?php
require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/Usuarios.php';
use Dotenv\Dotenv;
use Stripe\StripeClient;
use Stripe\PaymentIntent;
/**
 *
 */
class StripeModel {


  private static $SK_STRIPE;
  private static $PK_STRIPE;
  static $WHSEC_STRIPE;
  private $dontenv;
  private $stripe;
  private $conexion;
  public const CUENTA_INCOMPLETA='nfcn_cuenta_stripe_incompleta';
  public const PAGO_INCOMPLETO='nfcn_pago_stripe_incompleto';
  public const PAGO_COMPLETADO='nfcn_pago_stripe_completo';
  function __construct()
  {
    try{
        $this->conexion=Conectar::conexion();
        $dotenv = Dotenv::createImmutable(__DIR__.'/../');
        $dotenv->safeLoad();
        self::$SK_STRIPE = $_ENV['SK_STRIPE'];
        self::$PK_STRIPE = $_ENV['PK_STRIPE'];
        self::$WHSEC_STRIPE = $_ENV['WHSEC_STRIPE'];
        $this->stripe = new StripeClient(self::$SK_STRIPE);
      }catch(\Exception $e){
        echo "cndb:".$e->getMessage();
      }
  }



  function crearCuentaStripeUsuario($idUsuario,$type){
    try{
      $datosUsuario=(new Usuarios)->getUsuarioPorId($idUsuario);
      $rsStripe = $this->stripe->accounts->create([
          'type' => $type,
          'email' => $datosUsuario['email'],
          'capabilities' => [
            'card_payments' => ['requested' => true],
            'transfers' => ['requested' => true],
          ],
      ]);
      $statusCuenta='';
      $rs=$this->crearBDcuentaStripe($rsStripe->id,$statusCuenta,$idUsuario,$type);
      return ($rs!=0)? $rsStripe->id : false;
    }catch(\Exception $e){
      echo "spdb:".$e->getMessage();
    }

  }


  function getLinks($accountId,$refresh_url,$return_url){
    try{
        return $this->stripe->accountLinks->create(
          [
            'account' => $accountId,
            'refresh_url' => $refresh_url,
            'return_url' => $return_url,
            'type' => 'account_onboarding',
          ]
        );
    }catch(\Exception $e){
      error_log('crLink:'.$e->getMessage());
      // echo 'crLink:'.$e->getMessage();
      return $e->getMessage();
    }
  }


  function crearBDcuentaStripe($idCuenta,$statusCuenta,$idUsuario,$tipoCuenta){
    $sql='INSERT INTO stripe (id_cuenta,id_usuario,status_cuenta,tipo_cuenta)
          values(:id_cuenta,:id_usuario,:status_cuenta,:tipo_cuenta)';
      try {
          $insert=$this->conexion->prepare($sql);
          $insert->bindParam(':id_cuenta',$idCuenta);
          $insert->bindParam(':id_usuario',$idUsuario);
          $insert->bindParam(':status_cuenta',$statusCuenta);
          $insert->bindParam(':tipo_cuenta',$tipoCuenta);
          $insert->execute();
          return $insert->rowCount();
      } catch (\Exception $e) {
          echo "crdb:".$e->getMessage();
      }
  }

  function updateBDcuentaStripe($status,$idCuenta){
        $sql='UPDATE stripe SET status_cuenta=:status WHERE id_cuenta=:id_cuenta';
      try {
          $update=$this->conexion->prepare($sql);
          $update->bindParam(':status',$status);
          $update->bindParam(':id_cuenta',$idCuenta);
          $update->execute();
          $row=$update->rowCount();
          return ($row<=0)? false : true;
      } catch (\Exception $e) {
          error_log("updb:".$e->getMessage());
          echo "updb:".$e->getMessage();

      }
  }

  function getCuentaDBStripe($idUsuario){
    try{
        $sql='SELECT * FROM stripe WHERE id_usuario=:id_usuario';
          $consulta=$this->conexion->prepare($sql);
          $consulta->bindParam(':id_usuario',$idUsuario);
          $consulta->execute();
          $row=$consulta->rowCount();
          return ($row<=0)? false : $consulta->fetch(PDO::FETCH_OBJ);
    } catch (\Exception $e) {
      return "db:".$e->getMessage();
    }
  }

  /**
  *@method: retorna una cuenta de stripe mediante id
  */
  function getCuentaStripe($idCuenta){
    return $this->stripe->accounts->retrieve($idCuenta);
  }


  /**
  *@method obtiene la cuenta del usuario mediante el id de cuenta stripe
  **/
  function getIdUsuario($idCuenta){
    $sql='SELECT id_usuario FROM stripe WHERE id_cuenta=:id_cuenta';
    try {
        $select=$this->conexion->prepare($sql);
        $select->bindParam(':id_cuenta',$idCuenta);
        $select->execute();
        $row=$select->rowCount();
        return ($row<=0)? false : $select->fetch(PDO::FETCH_OBJ);
    } catch (\Exception $e) {
        error_log("sldb:".$e->getMessage());
        echo "sldb:".$e->getMessage();
    }
  }



  function crearPaymentIntent($precio,$comision,$idCuenta){
    try {
      $precio=str_replace('.','',$precio);
      $comision=str_replace('.','',$comision);

      return $this->stripe->paymentIntents->create([
            'amount' => $precio,
            'currency' => 'eur',
            'automatic_payment_methods' => [
              'enabled' => 'true',
            ],
            'application_fee_amount' => $comision,
            'transfer_data' => [
              'destination' => $idCuenta,
            ],
          ]);
    } catch (\Exception $e) {
        error_log('intent:'.$e->getMessage());
        echo $e->getMessage();
    }
  }


  function recuperarPaymentIntent($idPayment){
    try {
        return $this->stripe->paymentIntents->retrieve($idPayment);
    } catch (\Exception $e) {
      error_log('retrieveIntent:'.$e->getMessage());
      echo "retrieveIntent:".$e->getMessage();
      return false;
    }
  }

}
