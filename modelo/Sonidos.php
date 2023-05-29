<?php
require __DIR__.'/Productos.php';


class Sonido {

	private $con;
	private $idsTiendas = array();
	private static $instance;
	const VENDER=1;
  const GRATIS=2;
  const TIENDAS=3;
	public function __construct(){

		require_once("conectar.php");
		$this->con=Conectar::conexion();


	}




	public function setSonido(){



	}

	public function setTitulo(){

	}


	public function setPortada(){

	}


	public function setDescripcion(){

	}

	public function setOpcionVenta(){

	}

	//si no se carga una imagen deberia cargarse la imagen por defecto
	public function cargarDatosSonido($autor,$imagen,$sonido,$titulo,$descripcionC,$activado=false){

		//se selecciona la informacion del producto
		$infoProducto=ProductosM::getInfoProducto('tono',$this->con);
		$precio=$infoProducto['precio'];
		$insert=$this->con->prepare('INSERT INTO tonos (id_autor,imagen,sonido,titulo,descripcion_corta,precio,activado)
												values(:id_autor,:imagen,:sonido,:titulo,:descripcionC,:precio,:activado)' );

			$insert->bindParam(':id_autor',$autor);
			$insert->bindParam(':imagen',$imagen);
			$insert->bindParam(':sonido',$sonido);
			$insert->bindParam(':titulo',$titulo);
			$insert->bindParam(':descripcionC',$descripcionC);
			$insert->bindParam(':precio',$precio);
			$insert->bindParam(':activado',$activado, PDO::PARAM_INT);
			// $insert->bindParam(':tipoNegociacion',$tipoNegociacion);
			$insert->execute();
			$idInsert=$this->con->lastInsertId();
			return $idInsert;

	}

	public function actualizarEstadoActivadoSonido($id,$activado){
		$sql='UPDATE  tonos
										SET activado=:activado
										WHERE id=:id_sonido';
		try {
			$actualizaSonido=$this->con->prepare($sql);
			$actualizaSonido->bindParam(':activado',$activado);
			$actualizaSonido->bindParam(':id_sonido',$id);
			$actualizaSonido->execute();
			return $actualizaSonido->rowCount();
		} catch (\Exception $e) {
			return $e->getMessage();
		}

	}


	public function borrarSonido($id,$id_autor){
			$sql='DELETE FROM tonos WHERE id=:id AND id_autor=:id_autor';
			$rm=$this->con->prepare($sql);
			$rm->bindParam(':id',$id);
			$rm->bindParam(':id_autor',$id_autor);
			$rm->execute();
			return $rm->rowCount();
	}

	/**
	*Cuando ejecutamos esta query, nuestra base de datos intenta insertar una línea en nuestra tabla,
	 *pero si la Clave de la tabla ya existe no puede hacerlo. Y en vez de devolver un error,
	 *actualiza el campo que  necesitamos actualizar.
	 *
	 */
	public function cargarTienda($id,$id_tono,$nombre,$valor){
				$sql='INSERT INTO tiendas_online (id,id_tono,nombre_tienda,valor)
						VALUES(:id,:id_tono,:nombre_tienda,:valor) ON DUPLICATE KEY
						UPDATE nombre_tienda=:nombre_tienda,valor=:valor';
				$insert=$this->con->prepare($sql);
				$insert->bindParam(':id',$id);
				$insert->bindParam(':id_tono',$id_tono);
				$insert->bindParam(':nombre_tienda',$nombre);
				$insert->bindParam(':valor',$valor);
				$insert->execute();
	}


	public function getTiendas($id_tono){
        $consulta=$this->con->prepare("SELECT * FROM tiendas_online WHERE id_tono=:id");
        $consulta->bindParam(":id",$id_tono);
        $consulta->execute();
        $resultado=$consulta->fetchAll();
        return $resultado;
    }
    public function setIdsTiendas($idsTiendasSet){
    	$this->idsTiendas=$idsTiendasSet;
    }
    public function getIdsTiendas($id_tono){
		$consulta=$this->con->prepare("SELECT id FROM tiendas_online WHERE id_tono=:id");
        $consulta->bindParam(":id",$id_tono);
        $consulta->execute();
        $resultado=$consulta->fetchAll();

        //guarda los ids de las tiendas que se estan solicitando
        foreach ($resultado as $tienda) {
        	  $this->idsTiendas[]=$tienda['id'];
        }

        return $this->idsTiendas;


    }
	public function getDatosSonidos($autor){//funcion para la pagina misTonos.php

		$getTonos=$this->con->prepare(' SELECT *
										FROM tonos
										WHERE id_autor=:id_autor');

		$getTonos->bindParam(':id_autor',$autor);
		$getTonos->execute();
		$tonos=$getTonos->fetchAll();

		return $tonos;



	}
/**
* NOTE: al usar la constante __DIR__ hago referencia a la ruta del fichero
* actual desde donde se ejecuta.
*/
	public function getDataFileAudio($sonido){
		$dataFile=base64_encode(file_get_contents(__DIR__.'/../'.$sonido['sonido']));
		$dataFile='data: '.mime_content_type(__DIR__."/../".$sonido['sonido']).';base64,'.$dataFile;
		return $dataFile;
	}




	public function getSonidoAEditar($identificador){
		$getTono=$this->con->prepare(' SELECT *
										FROM tonos
										WHERE id=:id_sonido');

		$getTono->bindParam(':id_sonido',$identificador);
		$getTono->execute();
		$tono=$getTono->fetch();





			session_name('login');
			session_start();
			$_SESSION['idSonido']=$identificador;
			session_write_close();

		// echo "edit:".$_SESSION['idSonido'];
	 // echo session_id();
		return $tono;
	}



	public function comprobarSonido($autor,$identificador){


		$comprobar=$this->con->prepare(' SELECT *
										FROM tonos
										WHERE id=:id_sonido AND id_autor=:id_autor');

		$comprobar->bindParam(':id_autor',$autor);
		$comprobar->bindParam(':id_sonido',$identificador);
		$comprobar->execute();
		$resultado=$comprobar->fetch();

		if (empty($resultado['id'])==true) {//si el sonido no es de este usuario
			return false;
		}else{
			return true;
		}



	}





	public function actualizarTitulo($nuevo_titulo,$identificador){

		try {
			$actualizaSonido=$this->con->prepare(' UPDATE  tonos
											SET titulo=:titulo
											WHERE id=:id_sonido');
			$actualizaSonido->bindParam(':titulo',$nuevo_titulo);
			$actualizaSonido->bindParam('id_sonido',$identificador);
			$actualizaSonido->execute();
			return true;
		}catch(\Exception $e) {
			return $e->getMessage();
		}
	}

	public function actualizarDescripcion($nueva_descripcion,$identificador){

		try {
			$actualizaSonido=$this->con->prepare(' UPDATE  tonos
											SET descripcion_corta=:descripcion_corta
											WHERE id=:id_sonido');
			$actualizaSonido->bindParam(':descripcion_corta',$nueva_descripcion);
			$actualizaSonido->bindParam('id_sonido',$identificador);
			$actualizaSonido->execute();
			return true;

	} catch (\Exception $e) {
			return $e->getMessage();
		}
	}


	public function actualizarPortada($imagen,$identificador){


		try {
			$actualizaPortada=$this->con->prepare('UPDATE tonos SET imagen=:imagen WHERE id=:id_sonido');
			$actualizaPortada->bindParam(':imagen',$imagen);
			$actualizaPortada->bindParam(':id_sonido',$identificador);
			$actualizaPortada->execute();
			//$resultado=$consulta->fetch();
			return true;
		} catch (\Exception $e) {
			return $e->getMessage();
		}


	}

	public function setTipoNegociacion($tipoNegociacion,$identificador){
		try {

		$actualizaSonido=$this->con->prepare(' UPDATE  tonos
										SET tipoNegociacion=:tipoNegociacion
										WHERE id=:id_sonido');
		$actualizaSonido->bindParam(':tipoNegociacion',$tipoNegociacion);
		$actualizaSonido->bindParam('id_sonido',$identificador);
		$actualizaSonido->execute();
		return "true";
	} catch (\Exception $e) {
			return $e->getMessage();
		}
	}


	public  function getIdSonidoActual(){
		/*entra aqui cuando la sesion se ha cerrado automaticamente y no la encuentra*/
		if(empty($_SESSION['idSonido'])){
			session_name('login');
			session_start();
		}
		if(!empty($_SESSION['idSonido'])){
			$id_sonido_actual=$_SESSION['idSonido'];
			session_write_close();
		}else {
			$id_sonido_actual=false;
		}
		// echo session_id();
		// echo "id:".$id_sonido_actual;
		return $id_sonido_actual;
	}







}


































?>
