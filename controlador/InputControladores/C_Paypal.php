<?php
// NOTE:aqui se reciben los datos retornados una vez el vendedor se ha registrado en PayPal
// NOTE: hay que hacer una redireccion una vez se hayan guardado los datos a la vista ajustesPerfil;
require_once __DIR__.'/../../modelo/configuracion.php';
require_once __DIR__.'/../../modelo/datosPaypal.php';
require_once __DIR__.'/../../modelo/Usuarios.php';

/**
 *
 */
class C_I_Paypal{
  protected $configuracion;
  private $arrayDatosPaypal;
  private $modeloDatosPaypal;
  function __construct(){
    $this->configuracion= new Configuracion();
    $this->modeloDatosPaypal=new DatosPaypal();
  }


  public function recibirDatos(){
    // NOTE: se reciben los datos y se almacenan y luego se redirecciona
    $merchantID=filter_input(INPUT_GET,'merchantId',FILTER_SANITIZE_SPECIAL_CHARS);
    $this->arrayDatosPaypal=array(
                            'merchantId'=>filter_input(INPUT_GET,'merchantId',FILTER_SANITIZE_SPECIAL_CHARS),
                            'merchantIdInPayPal'=>filter_input(INPUT_GET,'merchantIdInPayPal',FILTER_SANITIZE_SPECIAL_CHARS),
                            'permissionsGranted'=>filter_input(INPUT_GET,'permissionsGranted',FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE),
                            'accountStatus'=>filter_input(INPUT_GET,'accountStatus',FILTER_SANITIZE_SPECIAL_CHARS),
                            'consentStatus'=>filter_input(INPUT_GET,'consentStatus',FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE),
                            'productIntentId'=>filter_input(INPUT_GET,'productIntentId',FILTER_SANITIZE_SPECIAL_CHARS),
                            'isEmailConfirmed'=>filter_input(INPUT_GET,'isEmailConfirmed',FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE),
                            'returnMessage'=>filter_input(INPUT_GET,'returnMessage',FILTER_SANITIZE_SPECIAL_CHARS),
                            'riskStatus'=>filter_input(INPUT_GET,'riskStatus',FILTER_SANITIZE_SPECIAL_CHARS),
                            'tipo_registro'=>DatosPaypal::PAYPAL_PARTNER,
                                    );
    $datos=json_encode($this->arrayDatosPaypal);
    if ($this->modeloDatosPaypal->getDatosPaypal(Usuarios::getIdUsuario())==false) {
        $this->modeloDatosPaypal->addDatosPaypal(Usuarios::getIdUsuario(),$datos);

        (new Usuarios())->setCuentaPaypal(filter_input(INPUT_GET,'merchantIdInPayPal',FILTER_SANITIZE_SPECIAL_CHARS));
    }
    $url_sitio=$this->configuracion->getConfiguracion("url_sitio");
    $url_redireccion=$url_sitio->valor.'/PanelControl/paginas/ajustesPerfil.php';
    header('Location:'.$url_redireccion);
    // echo "<pre>";
    // print_r($datos);
    // echo "</pre>";
  }


}

$c_i_paypal=new C_I_Paypal();
$c_i_paypal->recibirDatos();



 ?>
