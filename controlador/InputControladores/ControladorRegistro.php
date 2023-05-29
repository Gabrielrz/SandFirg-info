<?php
require_once __DIR__.'/../../vendor/autoload.php';
require_once __DIR__.'/../../modelo/Usuarios.php';
require_once __DIR__.'/../../modelo/configuracion.php';
require_once __DIR__.'/../Controlador.php';
require_once __DIR__.'/../../modelo/rulesValidation.php';
require_once __DIR__.'/../../modelo/stripe.php';
use Rakit\Validation\Validator;
use Utilidades\Utilidades;
/**
 *
 */
class C_I_Registro
{

	private $validator;
	private $nombre;
	private $nickname;
	private $email;
	private $pass;
	private $rePass;
	private $usuario;
	private $stripe;
	function __construct()
	{
		$this->validator = new Validator([
						'required' => ':attribute no puede estar vacio',
						'email' => 'el campo :attribute no es valido',
						'min' => 'el minimo de valores para el campo :attribute es :min',
						'same' => 'el campo :attribute no es igual.',
					]);
		$this->validator->addValidator('unique', new UniqueRule());
		$this->nombre=filter_input(INPUT_POST,'nombre_registro',FILTER_SANITIZE_STRING);
		$this->nickname=filter_input(INPUT_POST,'nickname_registro',FILTER_SANITIZE_STRING);
		$this->email=filter_input(INPUT_POST,'email_registro',FILTER_SANITIZE_STRING);
		$this->pass=filter_input(INPUT_POST,'pass_registro',FILTER_SANITIZE_STRING);
		$this->rePass=filter_input(INPUT_POST,'confirm_pass_registro',FILTER_SANITIZE_STRING);
		$this->usuario=new Usuarios();
		$this->stripe=new StripeModel();
	}


	public function registro(){
		if($this->validacion()){
				$tokenActivacion=$this->usuario->generarToken();
				$resultado=$this->usuario->registrarUsuario($this->nombre,$this->email,$this->nickname,$this->pass,$tokenActivacion);
				if($resultado==USUARIO_REGISTRADO){
									$login=$this->usuario->comprobar($this->email,$this->pass);
									$res=$this->usuario->enviarMensajeActivacion('activacion',$tokenActivacion,$this->email);
									if($res==true&&$login==USUARIO_VALIDADO){
											$res=$this->stripe->getCuentaDBStripe(Usuarios::getIdUsuario());
											if($res==false){
													
												 $this->stripe->crearCuentaStripeUsuario(Usuarios::getIdUsuario(),'express');
											}
											$url_user=(new Configuracion)->getConfiguracion("url_user");
											echo  Utilidades::mensaje(true,
																	'se ha registrado con exito y se ha enviado un mensaje de activacion',
																	$url_user->valor);
									}
				}
		}
	}

	public function validacion(){

		$validation = $this->validator->make($_POST + $_FILES, [
				'nombre_registro'        => 'required',
				'nickname_registro'      => 'required|unique:usuarios,nickname,exception@mail.com',
				'email_registro'				 => 'required|email|unique:usuarios,email,exception@mail.com',
				'pass_registro' 				 => 'required|min:6',
				'confirm_pass_registro'	 => 'required|same:pass_registro',
		]);
		$validation->setAliases([
		'nombre_registro' 					=> 'nombre',
		'nickname_registro' 				=> 'nickname ',
		'email_registro'						=> 'email',
		'pass_registro'							=> 'constraseña',
		'confirm_pass_registro'			=> 'contraseña de confirmacion',
		]);
		$validation->validate();

		if ($validation->fails()) {
		    $errors = $validation->errors();
		    echo Utilidades::mensaje(false,$errors->all()[0]);
		} else {
		   return true;
		}

	}



}

$c_registro=new C_I_Registro();
$c_registro->registro();
