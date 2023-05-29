<?php
require_once __DIR__.'/email.php';
require_once "conectar.php";
require __DIR__.'/../vendor/autoload.php';

	define('USUARIO_ENCONTRADO','1');
	define('USUARIO_NO_ENCONTRADO','2');
	define('CONTRASENA_CORECTA','3');
	define('CONTRASENA_INCORECTA','4');
	define('USUARIO_VALIDADO', '5');
	define('ERROR_USUARIO_YA_REGISTRADO','6');
	define('ERROR_EMAIL_YA_REGISTRADO', '7');
	define('USUARIO_REGISTRADO','8');
	define('DATOS_ACTUALIZADOS', '9');


class Usuarios{

	protected $con;//// WARNING: se utiliza en productos
	const ADMIN='1';
	const USER='2';
	public $email;
	public function __construct(){
		$this->con=Conectar::conexion();
		date_default_timezone_set('Europe/Madrid');//para la clase pagos
	}

	public function comprobar($email,$pass){
		$consultaUs=$this->con->prepare('SELECT * FROM usuarios WHERE email=:email');
		$consultaUs->bindParam(':email',$email);
		//$consultaUs->execute([$email]);
		$consultaUs->execute();
		$usuario=$consultaUs->fetch();
		session_name('login');
		session_start();

		if(!empty($usuario['id'])){//si se encontro un id

			//if($usuario['pass']==$pass){
			if(password_verify($pass,$usuario['pass'])){
				$_SESSION['user_id']=$usuario['id'];
				$_SESSION['user_role']=$usuario['id_role'];
				$_SESSION['user_email']=$usuario['email'];
				return USUARIO_VALIDADO;
			}else{
				return CONTRASENA_INCORECTA;
			}

		}else{
			return USUARIO_NO_ENCONTRADO;
		}
	}


	public function validarRegistro($nickname,$email){
		$comprobarUs=$this->con->prepare('SELECT * FROM usuarios WHERE nickname=:nickname OR email=:email');
		$comprobarUs->bindParam(':nickname',$nickname);
		$comprobarUs->bindParam(':email',$email);
		$comprobarUs->execute();
		$usuario_en=$comprobarUs->fetch();
		if(!empty($usuario_en['id'])){//si el usuario existe
			if($usuario_en['email']==$email){
				return ERROR_EMAIL_YA_REGISTRADO;
			}else if($usuario_en['nickname']==$nickname){
				return ERROR_USUARIO_YA_REGISTRADO;
			}
		}else{
			return USUARIO_NO_ENCONTRADO;
		}
	}
	public function registrarUsuario($nombre,$email,$nickname,$password,$tokenID){

		try {
			$registroUs=$this->con->prepare('INSERT INTO usuarios (nombre,nickname,email,pass,tokenActivacion,fecha_registro)
																			values(:nombre,:nickname,:email,:pass,:token_activacion,:fecha_registro)'
																		 );
			$registroUs->bindParam(':nombre',$nombre);
			$registroUs->bindParam(':nickname',$nickname);
			$registroUs->bindParam(':email',$email);
			$pswdHash=password_hash($password,PASSWORD_BCRYPT);
			$registroUs->bindParam(':pass',$pswdHash);
			$registroUs->bindParam(':token_activacion',$tokenID);
			date_default_timezone_set('Europe/Madrid');
      $fecha_registro=date_create("now");
      $fecha_registro=date_format($fecha_registro,"Y-m-d");
			$registroUs->bindParam(':fecha_registro',$fecha_registro);
			$registroUs->execute();
			$row=$registroUs->rowCount();
	    return ($row!=0)? USUARIO_REGISTRADO :false;
		} catch (\Exception $e) {
				return $e->getMessage();
		}
	}

	public function getRole($id_usuario){
		$sql='SELECT id_role FROM usuarios WHERE id=:id_usuario';
		$consulta=$this->con->prepare($sql);
		$consulta->bindParam(':id_usuario',$id_usuario);
		$consulta->execute();
		$resultado=$consulta->fetch();
		return $resultado['id_role'];
	}


	public function enviarMensajeActivacion($paginaActivacion,$tokenID,$emailUsuario){
		try {
			$this->email=new Email();
			$base_url=$this->obtenerConfiguracion("url_sitio");
			$base_url.='/controlador/OutputControladores/ControladorActivacion.php?tokenID='.$tokenID.'&&email='.$emailUsuario;

			$mensajeSF=new DOMDocument("1.0");//SF significa sin filtrar(por que aun no se ha anadido ninguna url)
			$mensajeSF->loadHTMLFile(__DIR__.'/mensajeActivacion.html');
			$enlace=$mensajeSF->getElementById("enlaceAc");
			$attr=$mensajeSF->createAttribute("href");
			$attr->value= htmlspecialchars($base_url);//se anade la url de la pagina(controlador) de activacion del usuario
			$enlace->appendChild($attr);

			$enlace_ab=$mensajeSF->getElementById("enlaceAb");
			$abAttr=$mensajeSF->createAttribute("href");
			$abAttr->value=htmlspecialchars($base_url);
			$enlace_ab->textContent=filter_var($base_url,FILTER_SANITIZE_URL);
			$enlace_ab->appendChild($abAttr);

			$mensaje= $mensajeSF->saveHTML();

		$resultado=$this->email->enviarEmail($_ENV['MAIL_EMAIL'],$emailUsuario,$mensaje,
																"verificacion cuenta","correo de verificacion de cuenta");

				return $resultado;
		} catch (\Exception $e) {
			return $e->getMessage();
		}

	}

	public function reEnviarMensajeActivacion(){
		$token=$this->generarToken();
		$email=$this->getEmailUsuario();
		if($this->actualizarTockenActivacion($token)!=0){
			return 	$this->enviarMensajeActivacion('',$token,$email);
		}else{
			return false;
		}
	}
	public function actualizarTockenActivacion($newToken){
		try {
			$actualizacion=$this->con->prepare('UPDATE usuarios SET tokenActivacion=:token WHERE id=:id');
			$id_usuario=$this->get_id_usuario();
			$actualizacion->bindParam(':token',$newToken);
			$actualizacion->bindParam(':id',$id_usuario);
			$actualizacion->execute();
			return $actualizacion->rowCount();
		} catch (\Exception $e) {
			return $e->getMessage();
		}
	}
	public function getEmailUsuario(){
		$sql='SELECT email FROM usuarios	WHERE id=:id';
		$consulta=$this->con->prepare($sql);
		$id_usuario=$this->get_id_usuario();
		$consulta->bindParam(':id',$id_usuario);
		$consulta->execute();
		return $consulta->fetch(PDO::FETCH_OBJ)->email;
	}

	public function activarUsuario($email,$tokenID,$activado=false){

		$consulta=$this->con->prepare('UPDATE usuarios  SET activado=:activado WHERE email=:email AND tokenActivacion=:tokenID');

		$consulta->bindParam(':activado',$activado);
		$consulta->bindParam(':email',$email);
		$consulta->bindParam(':tokenID',$tokenID);
		$consulta->execute();
		//$resultado=$consulta->fetch();
	}




/*falta un metodo obtener configuracion por seccion*/
/**
*metodo creado para obtener configuraciones relacionadas con el usuario vendedor
*metodo en clases modelo(pagos)
*/
	public function obtenerConfiguracion($clave){
		$consulta=$this->con->prepare('SELECT valor FROM configuraciones WHERE clave=:clave');
		$consulta->bindParam(':clave',$clave);
		$consulta->execute();
		$resultado=$consulta->fetch();
		extract($resultado);//genera una variable con el nombre de la clave del array asociativo($valor)
		return $valor;
	}




	public function generarToken(){
				$token=md5(uniqid());
				return $token;
	}

	public function obtenerTokenActivacion($email){
		//para no comprometer el id de usuario es mejor usar el email
		//de esta manera se garantiza la verificacion del email

				$consulta=$this->con->prepare('SELECT tokenActivacion FROM usuarios WHERE email=:email');
				$consulta->bindParam(':email',$email);
				$consulta->execute();
				$resultado=$consulta->fetch();
				if($resultado!=false){//si el resultado existe
					extract($resultado);
					return $tokenActivacion;
				}else{
					return "";
				}

	}

	/**
	*@method:comprueba si el usuario ha sido verificado y esta activo
	*/
	public function isActive($id,$email){
		try {
			$sql='SELECT activado FROM usuarios	WHERE email=:email AND id=:id';
			$consulta=$this->con->prepare($sql);
			$consulta->bindParam(':email',$email);
			$consulta->bindParam(':id',$id);
			$consulta->execute();
			return $consulta->fetch(PDO::FETCH_OBJ)->activado;
		} catch (\Exception $e) {
			return $e->getMessage();
		}
	}


	public function get_id_usuario(){

		if(empty($_SESSION['user_id'])){//entra aqui por que la sesion se cierra automaticamente y no la encuentra
			session_name('login');
			session_start();
		}
		if(!empty($_SESSION['user_id'])){
			$ID_usuario=$_SESSION['user_id'];
			session_write_close();//// WARNING: este metodo se usa en varias consultas sql y varios metodos
		}else{
			$ID_usuario=0;
		}

		return $ID_usuario;

	}
	public static function getIdUsuario(){

		if(empty($_SESSION['user_id'])){//entra aqui por que la sesion se cierra automaticamente y no la encuentra
			session_name('login');
			session_start();

		}
		if(!empty($_SESSION['user_id'])){
			$ID_usuario=$_SESSION['user_id'];
			session_write_close();//// WARNING: este metodo se usa en varias consultas sql y varios metodos
		}else{
			$ID_usuario=false;//esto esta mal deberia retornarse en caso de fallo a la pagina principal
		}
		return $ID_usuario;

	}

	public function getImagenAvatar(){

		$consulta=$this->con->prepare('SELECT imagen_avatar FROM usuarios WHERE id=?');
		$id_usuario=$this->get_id_usuario();
		$consulta->execute([$id_usuario]);
		$imagen_avatar=$consulta->fetch();

		return $imagen_avatar;

	}


	public function getUsuario(){
		try {
			$consulta=$this->con->prepare('SELECT * FROM usuarios WHERE id=:id');
			$id_usuario=$this->get_id_usuario();
			$consulta->bindParam(':id',$id_usuario);
			$consulta->execute();
			$row=$consulta->rowCount();
			return ($row<=0)? false : $consulta->fetch();;
		} catch (\Exception $e) {
				echo $e->getMessage();
		}
	}

	public function getUsuarioPorId($id_usuario){
		$sql='SELECT * FROM usuarios WHERE id=:id_usuario';
			$consulta=$this->con->prepare($sql);
			$consulta->bindParam(':id_usuario',$id_usuario);
			$consulta->execute();
			$resultado=$consulta->fetch(PDO::FETCH_ASSOC);
			return $resultado;
	}
	public function obtenerUsuarios(){
		$consulta=$this->con->prepare('SELECT * FROM usuarios');
		$consulta->execute();
		$usuario=$consulta->fetchAll();
		return $usuario;
	}

	public  function setCuentaPaypal($cuenta){
		$id_usuario=$this->get_id_usuario();
		$actualizacion=$this->con->prepare('UPDATE usuarios SET cuenta=:cuenta WHERE id=:id');
		$actualizacion->bindParam(':cuenta',$cuenta);
		$actualizacion->bindParam(':id',$id_usuario);
		$actualizacion->execute();
		if($actualizacion->rowCount()!=0){
			return true;
		}else{
			return false;
		}
	}
	public function updateUsuario($nombre,$email,$cuenta){

		try {
				$actualizacion=$this->con->prepare('UPDATE usuarios SET nombre=:nombre,email=:email,cuenta=:cuenta WHERE id=:id');
				$id_usuario=$this->get_id_usuario();
				$actualizacion->bindParam(':nombre',$nombre);
				$actualizacion->bindParam(':email',$email);
				$actualizacion->bindParam(':cuenta',$cuenta);
				$actualizacion->bindParam(':id',$id_usuario);
				$actualizacion->execute();
				//$resultado=$consulta->fetch();
				return DATOS_ACTUALIZADOS;
		}catch(\Exception $e) {
				return $e->getMessage();
		}
	}

	public function updateImagenAvatar($imagen){


		try {

				$actualizacionImagen=$this->con->prepare('UPDATE usuarios SET imagen_avatar=:imagen WHERE id=:id');
				$id_usuario=$this->get_id_usuario();
				$actualizacionImagen->bindParam(':imagen',$imagen);
				$actualizacionImagen->bindParam(':id',$id_usuario);
				$actualizacionImagen->execute();
				//$resultado=$consulta->fetch();
				return true;
		}catch (\Exception $e) {
				return $e->getMessage();
		}
	}

	/**
	*@method comprueba el medio de pago paypal, comprueba si tiene una cuenta paypal
	*asociada
	*
	*/
	 function comprobarMPPaypal($idUsuario){

			$sql='SELECT * FROM paypal_datos WHERE id_usuario=:id_usuario';
			try {
					$select=$this->con->prepare($sql);
					$select->bindParam(':id_usuario',$idUsuario);
					$select->execute();
					$row=$select->rowCount();
					return ($row<=0)? false : true;
			} catch (\Exception $e) {
					error_log("sldb:".$e->getMessage());
					echo "sldb:".$e->getMessage();
			}
	}
	/**
	*@method comprueba si tiene una cuenta stripe conectada
	*
	*/
	 function comprobarMPStripe($idUsuario){
		$sql='SELECT * FROM stripe WHERE id_usuario=:id_usuario and status_cuenta="COMPLETED"';
		try {
				$select=$this->con->prepare($sql);
				$select->bindParam(':id_usuario',$idUsuario);
				$select->execute();
				$row=$select->rowCount();
				return ($row<=0)? false : true;
		} catch (\Exception $e) {
				error_log("sldb:".$e->getMessage());
				echo "sldb:".$e->getMessage();
		}
	}




	public static function obtenerFechaRegistro(){

	}

	public function insertInfo(){

	}

	public function updateInfo(){

	}

	public function comprobarClase(){


		return 'funciona';
	}



}
