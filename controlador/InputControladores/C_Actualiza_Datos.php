<?php
include("../../modelo/Usuarios.php");
include __DIR__.'/archivos.class.php';
require_once __DIR__.'/../Controlador.php';//usar esta clase en un futuro para validar los datos // NOTE: ESTA EN USO SOLO para mensajes de salida
require_once __DIR__.'/../../vendor/autoload.php';
require_once __DIR__.'/../../modelo/datosPaypal.php';
use Rakit\Validation\Validator;

//el namespace se llama utilidades
use Utilidades\Utilidades;

class C_Input_Datos_User{

	private $usuario;
	public $nombre;
	public $correo;
	public $cuenta_paypal;
	public $password;
	public $foto_perfil;
	private $archivo;
	private $validator;
	private $modeloDatosPaypal;
	private $arrayDatosPaypal;
	function __construct(){
			$this->usuario=new Usuarios();
			$this->nombre=filter_input(INPUT_POST,'ajustes_nombre',FILTER_SANITIZE_SPECIAL_CHARS);
			$this->correo=filter_input(INPUT_POST,'ajustes_correo',FILTER_SANITIZE_SPECIAL_CHARS);
			$this->cuenta_paypal=filter_input(INPUT_POST,'cuenta_paypal',FILTER_SANITIZE_SPECIAL_CHARS);
			$this->password=filter_input(INPUT_POST,'ajustes_nueva_password',FILTER_SANITIZE_SPECIAL_CHARS);
			$this->foto_perfil=filter_input(INPUT_POST,'ajustes_foto_usuario',FILTER_SANITIZE_SPECIAL_CHARS);
			$this->archivo=new ControlArchivos();
			$this->modeloDatosPaypal=new DatosPaypal();

			$this->validator = new Validator([
							'required' => ':attribute no puede estar vacio',
							'email' => 'el campo :attribute no es valido',
						]);
	}

	/**
	*@param $this->cuenta_paypal: se actualiza en la tabla usuarios y en la tabla datos paypal
	*.(en caso de que no se tenga acceso a la api de registro de paypal)
	*/
	public function actualizarDatos(){

		if($this->validacion('update')){
			if($this->usuario->updateUsuario(
												$this->nombre,
												$this->correo,
												$this->cuenta_paypal)==DATOS_ACTUALIZADOS){

						$this->cargaImagen('ajustes_foto_usuario');
						$this->actualizarCuentaPaypal();
						echo  Utilidades::mensaje(true,'todo correcto');

			}
		}

	}


	public function validacion($tipo_validacion){
		switch ($tipo_validacion) {
			case 'insert':
				// codigo para datos esencialmente requeridos
				break;
			case 'update':
					$validation = $this->validator->make($_POST + $_FILES, [
							'ajustes_nombre'        => 'required',
							'ajustes_correo'        => 'required|email',
							'ajustes_nueva_password'=> 'nullable|min:6',
							'ajustes_foto_usuario'  => 'nullable|uploaded_file:0,500K,png,jpeg',
							'cuenta_paypal'					=> 'nullable|email',
					]);
				break;
		}

		$validation->setAliases([
		'ajustes_nombre' 							=> 'nombre',
		'ajustes_correo' 							=> 'correo',
		'ajustes_nueva_password'			=> 'contraseña',
		'ajustes_foto_usuario'				=> 'foto',
		'cuenta_paypal'								=> 'cuenta paypal',
		]);
		$validation->validate();
		if ($validation->fails()) {
		    $errors = $validation->errors();
		    echo Utilidades::mensaje(false,$errors->all());
		} else {
		   return true;
		}
	}


	/**
	*@method actualizarCuentaPaypal: carga los datos de una cuenta paypal en la
	*tabla datosPaypal en formato json
	*en caso de que no exista acceso a la api de registro de paypal para partners
	*/
	public function actualizarCuentaPaypal(){//// WARNING: no se puede actualizar si ya hay datos
		if(!empty($this->cuenta_paypal)){//si hay una cuenta paypal
			$this->arrayDatosPaypal=array('cuenta_paypal'=>$this->cuenta_paypal,'tipo_registro'=>DatosPaypal::PAYPAL_ACCOUNT);
			if ($this->modeloDatosPaypal->getDatosPaypal(Usuarios::getIdUsuario())==false) {
					$datos=json_encode($this->arrayDatosPaypal);
	        $this->modeloDatosPaypal->addDatosPaypal(Usuarios::getIdUsuario(),$datos);
	    }
		}
	}





	/**
	*@method cargaImagen: carga una imagen si existe alguna.
	*
	*/
	function cargaImagen($nombre){
		if($_FILES[$nombre]['error']===UPLOAD_ERR_OK){//si hay algun archivo para cargar
				$respuesta=$this->archivo->cargaDeArchivos($_FILES[$nombre],"imagenes/uploads/","imagen");
				if($respuesta==false){//si el archivo no ha sido cargado
						echo Utilidades::mensaje(false, $this->archivo->getErrores());
				}else{
						$estaCargado=$this->usuario->updateImagenAvatar($respuesta);
						return $estaCargado;
				}
		}
	}

}
$c_i_user=new  C_Input_Datos_User();
$c_i_user->actualizarDatos();













?>
