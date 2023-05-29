<?php
require_once __DIR__.'/../../vendor/autoload.php';
require_once __DIR__.'/../../modelo/stripe.php';
require_once __DIR__.'/../../modelo/Usuarios.php';
require_once __DIR__.'/../../modelo/configuracion.php';
require_once __DIR__.'/../../modelo/notificacion.php';
require_once __DIR__.'/../../modelo/Productos.php';
require_once __DIR__.'/../../modelo/configuracion.php';
require_once __DIR__.'/../Controlador.php';
require_once __DIR__.'/../../modelo/Pagos.php';
use Utilidades\Utilidades;
use Stripe\Webhook;
class C_StripeHook
{

  private $stripe;
  private $notificacion;
  private $productos;
  private $configuracion;
  function __construct(){

    $this->stripe = new StripeModel();
    $this->notificacion = new Notificacion();
    $this->productos = new ProductosM();
    $this->configuracion = new Configuracion();
  }

  function capturarHook(){

    $payload = @file_get_contents('php://input');
    $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
    $event = null;

    try {
      $event = Webhook::constructEvent($payload, $sig_header, $this->stripe::$WHSEC_STRIPE);

      $this->handleDeauthorization($event);


    } catch(\UnexpectedValueException $e) {
      // Invalid payload
      http_response_code(400);
      exit();
    } catch(\Stripe\Exception\SignatureVerificationException $e) {
      // Invalid signature
      http_response_code(400);
      exit();
    } catch(\Exception $e){
      error_log("stripe_error:".$e->getMessage());
      echo $e->getMessage();
      http_response_code(400);
      return $e->getMessage();
    }


  }

  function handleDeauthorization($event) {
    // Clean up account state.

    switch ($event->type) {
        case 'account.updated':
            $account = $event->data->object;
            error_log($account->id);
            if ($account->details_submitted) {

                $id_usuario=$this->stripe->getIdUsuario($account->id)->id_usuario;// WARNING: implementar logica en caso de que no se encuentre el usuario
                if($account->charges_enabled==false){

                  $mensaje='su cuenta aun esta incompleta, por favor continue con el proceso de cuenta de stripe, cargos inhabilitados';
                  $this->notificacion->crearNotificacion(StripeModel::CUENTA_INCOMPLETA,
                                                    $id_usuario,
                                                    Notificacion::NOTREAD,
                                                    Notificacion::IMPORTANT,
                                                    $mensaje);
                  $this->stripe->updateBDcuentaStripe("INCOMPLETED",$account->id);
                  error_log('entro');
                }else{
                  $this->stripe->updateBDcuentaStripe("COMPLETED",$account->id);
                  $this->notificacion->setnotificacionLeida(StripeModel::CUENTA_INCOMPLETA,$id_usuario);
                }
            }

        break;
        case 'payment_intent.succeeded':
            $paymentIntent = $event->data->object;
            // error_log("payment:".$paymentIntent);
            // error_log('cuenta:'.$paymentIntent->transfer_data->destination);
            $pago=new Pagos();

            date_default_timezone_set('Europe/Madrid');
            $fecha_actual=date_create("now");
            $fecha_actual=date_format($fecha_actual,"Y-m-d");

            $idAutor=$this->stripe->getIdUsuario($paymentIntent->transfer_data->destination)->id_usuario;
            $venta=$pago->getVenta($paymentIntent->id);
            if($venta!=false){
              //$paymentIntent->amount preico cobrado sin formato
              //$paymentIntent->application_fee_amount comision de plataforma aplicada  sin formato
              $comision=number_format($paymentIntent->application_fee_amount/100,2);
              $monto_pagado = (floatval($venta->precio) - floatval($comision));
              $id_pago=$pago->addBDPago($idAutor,
                                    $monto_pagado,
                                    '1',
                                    $fecha_actual,
                                    $comision,
                                    '201',
                                    $paymentIntent->id);
            $filas_afectadas=$pago->updateVenta($venta->id,$id_pago,'COMPLETED');
            }



          break;
        default:
          echo 'Received unknown event type ' . $event->type;
        break;
    }
    http_response_code(200);
  }

  function __initMain(){

      $this->capturarHook();

  }
}

$c_stripe_hook = new C_StripeHook();
$c_stripe_hook->__initMain();
