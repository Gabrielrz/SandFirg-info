<?php
require_once __DIR__.'/../../modelo/stripe.php';
require_once __DIR__.'/../../modelo/Usuarios.php';
require_once __DIR__.'/../../modelo/configuracion.php';
require_once __DIR__.'/../Controlador.php';
use Utilidades\Utilidades;
/**
 *
 */
class C_O_Stripe
{
  public $accion;
  private $stripe;
  public $email_asociado;
  function __construct()
  {
    $this->configuracion = new Configuracion();
    $this->stripe = new StripeModel();
    $this->usuarios = new Usuarios();
  }


  public function setTipoAccion($accion){
    $this->accion=$accion;
  }
  public function getTipoDeAccion(){
    return $this->accion;
  }
  function manejarEstadosDeCuenta(){
    //comprueba el estado del proceso de crear la cuenta mediante el enlace
    $res=$this->stripe->getCuentaDBStripe(Usuarios::getIdUsuario());
    if($res!=false){
      $account_obj=$this->stripe->getCuentaStripe($res->id_cuenta);
      if($account_obj->details_submitted){
        $this->setTipoAccion('show_status_stripe');
        $this->email_asociado=$account_obj->email;
        if($account_obj->charges_enabled==false){
          $this->setTipoAccion('incomplete_status_stripe');
        }
      }else{
        $this->setTipoAccion('get_account_stripe');
      }

    }else{
      $this->setTipoAccion('get_account_stripe');

    }

  }

  function __initMain(){
      $this->manejarEstadosDeCuenta();
  }
}
$c_stripe = new C_O_Stripe();
$c_stripe->__initMain();
