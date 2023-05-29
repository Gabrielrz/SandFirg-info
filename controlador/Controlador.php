<?php
namespace Utilidades;

class Utilidades{

  const HTTP_PHP='xmlhttprequest';
  public function __construct()
  {

  }

  /**
  *@method : se utiliza para determinar el tipo de solicitud que se requiere
  * y determinar la ubicacion
  *del archivo que hizo la solicitud.
  *@param TDS: significa tipo de solicitud y es el dato que se busca cuando
  *una solicitud se envia.
  * NOTE: NO EN USO
  */
  // public function origenDeSolicitud(){
  //   $tds=filter_input(INPUT_POST,'TDS',FILTER_SANITIZE_SPECIAL_CHARS)
  //   if($tds==self::HTTP_PHP){
  //
  //   }
  // }


  public static function mensaje($validacion,$mensaje,$redirect=false){
				 return  json_encode(array(
				 'status'=>$validacion,
				 'mensaje'=>$mensaje,
         'redirect'=>$redirect,
				 ));
	}
  public static function responseJson($validacion,array $resvalues,$redirect=false){
    try{
      $res = array(
      'status'=>$validacion,
      'redirect'=>$redirect,
      );
      return json_encode(array_merge($res,$resvalues));
    }catch(\Exception $e){
      return json_encode(array('error' => $e->getMessage()));
    }
 }
  public static function mensajesDePlataforma($validacion,$mensaje,$tipo){
    if($tipo=="json"){
      return  json_encode(array(
      'status'=>$validacion,
      'mensaje'=>$mensaje,
      ));
    }else if($tipo=='html'){
      return array(
        'status'=>$validacion,
        'mensaje'=>$mensaje,
      );
    }

  }



  public function validador($values,$filtros,$mensajes){/* IDEA: en construccion*/
		//estos arrays van en el metodo validacion

		foreach ($values as $value) {
			if(array_search('sonido',$value['nombres'])!=false){
				$this->validarSonido();
			}
			 if(array_search('imagen',$value['nombres'])!=false){
				$this->validarImagen();
			}
			$dato=filter_input($value['tipo'],$value['nombres'],$filtros[$value['nombres']]);
			if(!$dato||empty($dato)){
				return $this->mensaje(false,$mensajes[$value['nombres']]);
			}
		}
	}



  public function setMailBox(){

  }
  public function getMailBox(){

  }


}





?>
