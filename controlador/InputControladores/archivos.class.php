<?php

class ControlArchivos{



	public $errores=array();

	function __construct()
	{

	}

	public function getErrores()
	{
		return $this->errores;
	}
	//si no se carga el archivo retorna un false
	//si no se puede mover la imagen a la ruta especificada retorna los motivos/errores
	//si todo ha salido bien retorna la ruta donde se movio el archivo.
	public function cargaDeArchivos($archivo,$directorio_base,$tipo){//los errores no se pueden ver fuera de la funcion
		//lo que podria hacer es si existen errores que los retorne pero eso eliminaria al false

		//$_FILES['input_imagen_portada'];

					// antes estaba como /Linksringtones/ en  ""
					$directorio_de_carga = $_SERVER['DOCUMENT_ROOT']."/".$directorio_base;


					$id=uniqid('Sandfirg_',true);
					$ruta_de_carga = $directorio_de_carga.$id.basename($archivo["name"]);
					$return_url = $directorio_base.$id.basename($archivo["name"]);
					$uploadOk = 1;
					$extension_de_archivo = strtolower(pathinfo($ruta_de_carga,PATHINFO_EXTENSION));
					
					if ($tipo=='imagen') {
						// comprueba si el archivo es una imagen
						    $check = getimagesize($archivo["tmp_name"]);
						    if($check !== false) {
						        $uploadOk = 1;
						    } else {
						        $this->errores[]='el archivo no es un/a'.$tipo;
						        $uploadOk = 0;
						    }
					}
					// Ccomprueba si archivo ya existe
					if (file_exists($ruta_de_carga)) {
					    unlink($ruta_de_carga);
					}



					if($tipo=='imagen'){

						if ($archivo["size"] > 500000) {
						    $this->errores[]='el archivo es demasiado grande';
						    $uploadOk = 0;
						}

						if($extension_de_archivo != "jpg" && $extension_de_archivo != "png" && $extension_de_archivo != "jpeg"
						&& $extension_de_archivo != "gif" && $extension_de_archivo != "webp") {
						   $this->errores[]='solo se pueden cargar imagenes de tipo jpg,png,jpeg,gif';
						    $uploadOk = 0;
						}
					}else if($tipo=='sonido'){
						if ($archivo["size"] > 5000000||$archivo['error']===UPLOAD_ERR_INI_SIZE) {
						    $this->errores[]='el archivo sonido es demasiado grande';
						    $uploadOk = 0;
						}
						if($extension_de_archivo != "wav" && $extension_de_archivo != "mp3") {
					   		$this->errores[]='solo se pueden cargar sonidos de tipo wav,mp3';
					    	$uploadOk = 0;
						}
					}

					if ($uploadOk == 0) {
					    $this->errores[]='el archivo no ha sido cargado';
					} else {

					    if (move_uploaded_file($archivo["tmp_name"], $ruta_de_carga)) {

					    //	$ruta_base=$directorio_base.basename($archivo["name"]);
							 	return $return_url;

					    } else {
						       $this->errores[]='ha ocurrido un error durante la carga del archivo';
									 return false;
					    }
					}


	}

}
?>
