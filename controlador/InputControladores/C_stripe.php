<?php
require_once __DIR__.'/../../vendor/autoload.php';
require_once __DIR__.'/../../modelo/stripe.php';
require_once __DIR__.'/../../modelo/Usuarios.php';
require_once __DIR__.'/../../modelo/configuracion.php';
require_once __DIR__.'/../Controlador.php';
use Utilidades\Utilidades;
use Rakit\Validation\Validator;
/**
 *
 */
class C_I_Stripe
{
  private $stripe;
  private $usuarios;
  private $configuracion;
  private $validator;
  private $email;
  private $pass;
  function __construct()
  {
      $this->configuracion = new Configuracion();
      $this->stripe = new StripeModel();
      $this->usuarios = new Usuarios();
      $this->validator = new Validator([
							'required' => ':attribute no puede estar vacio',
							'email' => 'el campo :attribute no es valido',
						]);
      $this->email=$this->usuarios->getEmailUsuario();
  		$this->pass=filter_input(INPUT_POST,'password',FILTER_SANITIZE_STRING);
  }

    function __initMain(){
      switch (filter_input(INPUT_POST,'orden',FILTER_SANITIZE_STRING)) {
        case 'link_stripe':
              $this->generarLinkStripe();
          break;

        default:
          // code...
          break;
      }
    }

    function generarLinkStripe(){
      //comprueba si existen datos en la bbdd
      //y autenticar al usuario
      if($this->validacion()){
        	$login=$this->usuarios->comprobar($this->email,$this->pass);
          if($login==USUARIO_VALIDADO){
              $res=$this->stripe->getCuentaDBStripe(Usuarios::getIdUsuario());
              if($res==false){//si no existen crea una cuenta express rapida
                $res = new stdClass;
                $res->id_cuenta = $this->stripe->crearCuentaStripeUsuario(Usuarios::getIdUsuario(),'express');
              }

              $config=$this->configuracion->getConfiguracion('url_sitio');
              $refresh_url=$config->valor.'/PanelControl/paginas/ajustesPerfil.php';
              $return_url=$config->valor.'/PanelControl/paginas/ajustesPerfil.php';
              $resStripe=$this->stripe->getLinks($res->id_cuenta,$refresh_url,$return_url);
              if(is_object($resStripe)){
                echo Utilidades::mensaje(true,'redireccionando...',$resStripe->url);
              }else{
                echo Utilidades::mensaje(true,$resStripe);
              }
          }else{
            echo Utilidades::mensaje(false,'validacion incorrecta');
          }
     }


    }



    public function validacion(){
			$validation = $this->validator->make($_POST, [
					'password'=> 'required',
			]);
  		$validation->setAliases([
  		'password'			=> 'contraseña',
  		]);
  		$validation->validate();
  		if ($validation->fails()) {
  		    $errors = $validation->errors();
  		    echo Utilidades::mensaje(false,$errors->all());
  		} else {
  		   return true;
  		}
  	}





}
$c_stripe=new C_I_Stripe();
$c_stripe->__initMain();
