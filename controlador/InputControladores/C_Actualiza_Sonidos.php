<?php
require __DIR__.'/../../vendor/autoload.php';

header('Content-Type: application/json');
require_once __DIR__.'/archivos.class.php';
require_once __DIR__. '/../../modelo/Sonidos.php';
require_once __DIR__.'/../../modelo/Usuarios.php';
require_once __DIR__.'/../Controlador.php';
require_once __DIR__.'/../../modelo/configuracion.php';
use Google\Cloud\Vision\V1\Feature\Type;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Google\Cloud\Vision\V1\Likelihood;
use Utilidades\Utilidades;
class C_Input_Sonidos{

	protected $sonido;
	protected $usuario;
	public $errores;
	public $mensajes;
	private $controlArchivos;
	protected $imagenCargada;
	protected $sonidoCargado;
	private $tipoNegociacion;
	protected $id_tono;
	protected $accionesDeCarga;
	protected $activado;
	private $imagenPredeterminada;
	const VENDER=1;
	const GRATIS=2;
	const TIENDAS=3;
	const UNKNOWN=0;
  const VERY_UNLIKELY=1;
  const UNLIKELY=2;
  const POSSIBLE=3;
  const LIKELY=4;
  const VERY_LIKELY=5;

	public function __construct(){
		$this->sonido=new Sonido();
		$this->usuario=new Usuarios();
		$this->controlArchivos=new ControlArchivos();
		$this->errores=array();
		$this->imagenCargada=false;
		$this->sonidoCargado=false;
		$this->imagenPredeterminada='imagenes/predeterminado/portada-predeterminada_v1.png';
		$this->activado=false;
		$this->tipoNegociacion=array();
	}
	/**
	*@method cargarDatosSonido SE INSERTA LA DESCRIPCION DEL SONIDO
	*/
	public function crearSonido(){
		if($this->getAccion()=='insert'){
				if($this->validacion()){
							$autor=Usuarios::getIdUsuario();
							if($this->accionesDeCarga=='predeterminada'){
								// NOTE: no he encontrado la manera de enviarla como subida de imagen asi que por el momento la
								//recoge de la propia ubicacion
								$this->imagenCargada=$this->imagenPredeterminada;
							}

							$this->activado = ($this->detectExplicitContent($this->imagenCargada)==false)? true : false;

							$id_tono=$this->sonido->cargarDatosSonido($autor,
																									$this->imagenCargada,
																									$this->sonidoCargado,
																									$this->titulo,
																									$this->descripcion,
																									$this->activado);
							$this->eventListenerBtnGratis();
							$this->eventListenerBtnVentas();
							$this->eventListenerBtnTiendas($id_tono);

							$this->sonido->setTipoNegociacion(json_encode($this->tipoNegociacion),$id_tono);
							$this->mensajes[]="tono enviado con exito, sera revisado y activado :)";
							$url_user=(new Configuracion)->getConfiguracion("url_sitio")->valor;
							echo Utilidades::mensaje(true,$this->mensajes,$url_user.'/PanelControl/paginas/misTonos.php');
				}
		}
	}



    /**
    * @method:detectExplicitContent: detecta el % de contenido explicito en la imagen:
    * NOTE:en un futuro modificar el metodo para que el anuncio se pueda clasificar y enviar al admin
    **/
    public function detectExplicitContent($urlFile){

    $path= json_decode(file_get_contents(__DIR__.'/../../priv/sandfirg-key.json'),true);
     // Storage::get('images/PpXLU3GVEIpL0FzdbVmeHiWUq9ep4u16dd09EIeu.png');
      $config = [
              'credentials' => $path,
          ];
      $imageAnnotator = new ImageAnnotatorClient($config);

       # annotate the image
       $image =fopen(__DIR__.'/../../'.$urlFile,'r');
       $response = $imageAnnotator->safeSearchDetection($image);
       $safe = $response->getSafeSearchAnnotation();

       $res=array('ADULT_CONTENT'   =>  $safe->getAdult(),
                  'MEDICAL_CONTENT' =>  $safe->getMedical(),
                  'SPOOF_CONTENT'   =>  $safe->getSpoof(),
                  'VIOLENCE_CONTENT'=>  $safe->getViolence(),
                  'RACY_CONTENT'    =>  $safe->getRacy(),
                );
       # names of likelihood from google.cloud.vision.enums
       $likelihoodName = ['UNKNOWN', 'VERY_UNLIKELY', 'UNLIKELY',
       'POSSIBLE', 'LIKELY', 'VERY_LIKELY'];

       $imageAnnotator->close();

       foreach ($res as $key => $value) {
         if($value==self::POSSIBLE||$value==self::LIKELY||$value==self::VERY_LIKELY){
           return $res;
         }else{
           return false;
         }
       }

    }

	public function actualizarSonido(){
		if($this->getAccion()=='update'){
			if($this->validacion()){
				// NOTE: no se permite actualizar el sonido
				$id_tono=$this->sonido->getIdSonidoActual();
				// echo $id_tono;
				$this->sonido->actualizarTitulo($this->titulo,$id_tono);
				$this->sonido->actualizarDescripcion($this->descripcion,$id_tono);
				if($this->accionesDeCarga!=='neutral'){//si no es neutro

					if(	$this->detectExplicitContent($this->imagenCargada)==false){
						$this->sonido->actualizarEstadoActivadoSonido($id_tono,true);
					}else{
						$this->sonido->actualizarEstadoActivadoSonido($id_tono,false);
					}
					$this->sonido->actualizarPortada($this->imagenCargada,$id_tono);
				}
				$this->eventListenerBtnGratis();
				$this->eventListenerBtnVentas();
				$this->eventListenerBtnTiendas($id_tono);
				$this->sonido->setTipoNegociacion(json_encode($this->tipoNegociacion),$id_tono);
				$this->mensajes[]="datos guardados :)";
				echo Utilidades::mensaje(true,$this->mensajes);

			}
		}
	}

	public function borrarSonido(){
		if($this->getAccion()=='rmSonido'){
				$autor=Usuarios::getIdUsuario();
				$id=filter_input(INPUT_POST,'id',FILTER_SANITIZE_SPECIAL_CHARS);
				$rs=$this->sonido->borrarSonido($id,$autor);
				if($rs!=0){
					echo json_encode(array('response'=>200,));
				}else{
					echo json_encode(array('response'=>400,));
				}
		}
	}

	public function setTipoNegociacion($tipoNegociacion){
		array_push($this->tipoNegociacion,$tipoNegociacion);
	}


	public function eventListenerBtnGratis(){
		$btnGratis=filter_input(INPUT_POST,'checkGratis',FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE);
		if($btnGratis){
				$this->setTipoNegociacion(self::GRATIS);
		}
	}
	public function eventListenerBtnVentas(){
		$btnVenta=filter_input(INPUT_POST,'checkVender',FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE);
		if($btnVenta){
				$autor=Usuarios::getIdUsuario();
				if($this->usuario->comprobarMPPaypal($autor)||$this->usuario->comprobarMPStripe($autor)){//si existe una cuenta paypal
					$this->setTipoNegociacion(self::VENDER);
				}else{
					$this->setTipoNegociacion(self::GRATIS);
					$this->mensajes[]="para poder vender este tono necesitas ingresar una cuenta paypal en el perfil de ajustes, se enviara en modo gratis";
				}
		}
	}
	public function eventListenerBtnTiendas($id_tono){
		$btnTiendas=filter_input(INPUT_POST,'checkIncluirTiendas',FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE);
		if($btnTiendas){
			$this->setTipoNegociacion(self::TIENDAS);
			$this->setTiendas($id_tono);
		}
	}


	/**
	*carga las tiendas en caso de tener alguna
	*/
	public function setTiendas($id_tono){
			$nombresTiendas=$_POST['input_NombreTienda'];
			$urlsTiendas=$_POST['input_UrlTienda'];
			if (!empty($nombresTiendas)&&!empty($urlsTiendas)) {//si hay tiendas que insertar
				$idsTiendas=$this->sonido->getIdsTiendas($id_tono);
				for ($i=0; $i <count($nombresTiendas); $i++) {
							$nombreTienda = filter_var($nombresTiendas[$i] ,FILTER_SANITIZE_STRING);
							$urlTienda = filter_var($urlsTiendas[$i], FILTER_SANITIZE_URL);
							if($nombreTienda!=""&&$urlTienda!=""){//si se han digitado nombres y urls de tiendas
										if(!filter_var($urlTienda, FILTER_VALIDATE_URL)){//si la url no es valida
												$errores[]='la url no es valida';
										}else{
										$idTienda=(array_key_exists($i, $idsTiendas))? $idsTiendas[$i]: null;
										$this->sonido->cargarTienda($idTienda,$id_tono,$nombreTienda,$urlTienda);
										}
							}
				}
			}
	}


	/**
	*metodo provisional( IDEA:el objetivo es dar los valores a validar desde un array por un parametro que recibe el metodo )
	*/
	public function validacion(){
		switch ($this->getAccion()) {
			case 'insert':
					$this->accionesDeCarga=filter_input(INPUT_POST,'accionesDeCarga',FILTER_SANITIZE_SPECIAL_CHARS);
					$this->sonidoCargado = $this->cargarAudio();
					$this->titulo = filter_input(INPUT_POST,'inputTitulo',FILTER_SANITIZE_SPECIAL_CHARS);
					$this->descripcion = filter_input(INPUT_POST,'inputDescripcion',FILTER_SANITIZE_SPECIAL_CHARS);

					if($this->accionesDeCarga=='update'){
						$this->imagenCargada = $this->cargarPortada('input_imagen_portada');

						if(!$this->imagenCargada){
							$this->errores[]='se necesita una portada para el sonido!';
						}
					}
					if($this->accionesDeCarga=='remove'){
						$this->errores[]='se necesita una portada para el sonido!';
					}else	if (!$this->sonidoCargado){
						 $this->errores[]='se necesita un sonido!';

						// array_push($this->errores,'se necesita sonido!');
						// $this->errores=array_merge($this->errores,$this->controlArchivos->getErrores());

					}else if(empty($this->titulo)){
						$this->errores[]='se necesita un titulo de sonido';
					}else if(empty($this->descripcion)){
						$this->errores[]='se necesita una descripcion';
					}
				break;
			case 'update':

					$this->titulo = filter_input(INPUT_POST,'input_titulo_ed',FILTER_SANITIZE_SPECIAL_CHARS);
					$this->descripcion = filter_input(INPUT_POST,'input_descripcion_ed',FILTER_SANITIZE_SPECIAL_CHARS);
					$this->accionesDeCarga=filter_input(INPUT_POST,'accionesDeCarga',FILTER_SANITIZE_SPECIAL_CHARS);
					if($this->accionesDeCarga=='remove'){
							$this->errores[]='se necesita una portada para el sonido!';
					}else if($this->accionesDeCarga=='update'){
						$this->imagenCargada = $this->cargarPortada('input_portada_ed');
						if(!$this->imagenCargada){
							$this->errores[]='se necesita una portada para el sonido!';
						}
					}
					if(empty($this->titulo)){
						$this->errores[]='el titulo no puede quedar vacío..';
					}else if(empty($this->descripcion)){
						$this->errores[]='el campo descripcion no puede quedar vacío..';
					}
				break;
		}
		if(count($this->errores)!=0){
				echo Utilidades::mensaje(false,$this->errores);
		 }else{ return true; }
	}




	private function getAccion(){
		$accion= filter_input(INPUT_POST,'accion',FILTER_SANITIZE_SPECIAL_CHARS);
		return $accion;
	}


	/**
	*@method cargarAudio() CONTROLA LA CARGA DEL SONIDO
	*$respuesta si la respuesta recibida es un false
	*/
	public function cargarAudio(){

			 if($_FILES['input_sonido-pubT']['error']===UPLOAD_ERR_OK){
			 	$respuesta=$this->controlArchivos->cargaDeArchivos($_FILES['input_sonido-pubT'],'remixes/uploads/','sonido');
			 	if(!$respuesta){
			 			 $this->errores[]=$this->controlArchivos->getErrores();
						 $sonidoCargado=false;

			 	}else{
			 		$sonidoCargado=$respuesta;
			 	}
			 }else{
				 $this->errores[]='algo ha salido mal';
			 	 $sonidoCargado=false;
			 }
			 return $sonidoCargado;

	}



	/**
	*
	*@method cargarPortada (CONTROLA LA CARGA DE LA PORTADA)
	*@method cargaDeArchivos (si se ha cargado una imagen intenta moverlo a la carpeta principal)
	*@param $respuesta (si la respuesta recibida es un false(si no se ha podido cargar el archivo se obtien los errores))
	*/
	public function cargarPortada($nombre){
			if($_FILES[$nombre]['error']===UPLOAD_ERR_OK){

				$respuesta=$this->controlArchivos->cargaDeArchivos($_FILES[$nombre],'imagenes/uploadsPortadas/','imagen');
				if(!$respuesta){
					$this->errores[]=$this->controlArchivos->getErrores();
					$imagenCargada=false;
				}else{
					$imagenCargada=$respuesta;
				}
			}else{
				$imagenCargada=false;
			}
		return $imagenCargada;
	}








}
$c_input_sonido=new C_Input_Sonidos();
$c_input_sonido->crearSonido();
$c_input_sonido->actualizarSonido();
$c_input_sonido->borrarSonido();

?>
