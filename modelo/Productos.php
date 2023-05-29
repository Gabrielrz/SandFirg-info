<?php
require_once __DIR__.'/Sonidos.php';
require_once __DIR__.'/email.php';
/*
 *
 *esta clase se usa para el manejo de venta del producto y obtener
 *datos para informar al usuario (interactua con el cliente y el vendedor)
 *clase modelo para producto de ususario
*/
class ProductosM extends Sonido{


    private $conexion;
	// private $tiendasP;
    public function __construct(){

        require_once("conectar.php");
        $this->conexion=Conectar::conexion();

        // $redis = new Redis();
        // $redis->conexionnect('localhost', 6379);
        // echo $redis->ping("conexion");
        //$this->tiendasP=array();
        date_default_timezone_set('Europe/Madrid');
    }

    public function getProductoSonidosForScroll($activado,$page,$limit=12){//limite de 12 dividido en la vista

      $consulta=$this->conexion->prepare('SELECT * FROM tonos WHERE activado=:activado ORDER BY id DESC LIMIT :section,:limite');
      $consulta->bindParam(':activado',$activado);
      $consulta->bindParam(':section',$page, PDO::PARAM_INT);
      $consulta->bindParam(':limite',$limit, PDO::PARAM_INT);
      $consulta->execute();

      $res = [];
      $items = [];
      $n = $consulta->rowCount();

      if($n){
        while ($row = $consulta->fetch(PDO::FETCH_ASSOC)) {
          $dataFile=$this->getDataFileAudio($row);
          $item=array('id'=>$row['id'],
                      'imagen'=>$row['imagen'],
                      'sonidoURL'=>$row['sonido'],
                      // 'dataFileSondio'=>$dataFile,
                      'titulo'=>$row['titulo'],
                      'descripcion'=>$row['descripcion_corta'],
                      'tipoNegociacion'=>$row['tipoNegociacion'],
                    );
          array_push($items,$item);
        }
        $res['response']="200";
        //array_push($res,array('response'=>"200"));
        $res['items']=$items;
        //array_push($res,array('page'=>$page+$n));
        $res['page']=$page+$n;
        return $res;

      }else{
        $res['response']="400";
        //array_push($res,array('response'=>"400"));
        return $res;
      }
    }

    /**
    *@method iniciarSessionDeVenta: sirve para guardar datos de un sonido que esta
    *en venta y al cual se ha accedio en su respectiva pagina
    *beneficios: no paso los ids ni merchant_id ni ningun dato sensible a la vista html.
    *mas facilidd de manejo, datos en un solo punto de una sola session.
    */
    public function iniciarSessionDeVenta($id,$destinatario,$tipo){

      if(empty($_SESSION['id_sonido_venta'])){

        session_name('producto');
        session_start();
        $_SESSION['id_sonido_venta']=$id;
        $_SESSION['destinatario']=$destinatario;
        $_SESSION['tipo_accion']=$tipo;
      }
    }

    public function getSonidoEnVentaPorId($id){
      try {
        $sql='SELECT * FROM tonos WHERE id=:id_sonido';
        $consulta=$this->conexion->prepare($sql);
        $consulta->bindParam(':id_sonido',$id);
        $consulta->execute();
        $row=$consulta->rowCount();
        $resultado = ($row==0)? false : $consulta->fetch(PDO::FETCH_ASSOC);
        if($resultado!=false){
          $resultado['dataFile']=$this->getDataFileAudio($resultado);
        }
        return $resultado;
      } catch (\Exception $e) {
        return "slbd:".$e->getMessage();
      }
    }


	public function getDetallesSonido($id_seleccionado){


		$consulta=$this->conexion->prepare(' SELECT *
										FROM tonos
										WHERE id=:id_seleccionado');

		$consulta->bindParam(':id_seleccionado',$id_seleccionado);
		$consulta->execute();
		$tono=$consulta->fetch();
		return $tono;



	}
  /**
  *@method: se ejecuta al momento de comprar el producto
  *se obtiene la informacion del producto para su descarga
  *
  */
  public function obtenerProductoPorIdVenta($id_venta){
    $sql='SELECT * FROM  tonos,ventas_tonos
    WHERE (ventas_tonos.id_venta=:id_venta AND tonos.id=ventas_tonos.id_tono) AND ventas_tonos.status="COMPLETED"';
    $consulta=$this->conexion->prepare($sql);
    $consulta->bindParam(':id_venta',$id_venta);
    $consulta->execute();
    $rs=$consulta->fetch();
    return $rs;
  }

  public function obtenerInfoProducto($tipo){
    $sql=' SELECT *  FROM productos WHERE tipo=:tipo';
    try {
        $select=$this->conexion->prepare($sql);
        $select->bindParam(':tipo',$tipo);
        $select->execute();
        $row=$select->rowCount();
        return ($row==0)? false : $select->fetch();
    } catch (\Exception $e) {
        error_log("sldb:".$e->getMessage());
        echo "sldb:".$e->getMessage();
    }
  }
  public static function getInfoProducto($tipo,$tok){

    $consulta=$tok->prepare(' SELECT *
                    FROM productos
                    WHERE tipo=:tipo');

    $consulta->bindParam(':tipo',$tipo);
    $consulta->execute();
    $producto=$consulta->fetch();
    return $producto;
  }

    public function getTiendas($id){
        $consulta=$this->conexion->prepare("SELECT *
                                      FROM tiendas_online
                                      WHERE id_tono=:id");
        $consulta->bindParam(":id",$id);
        $consulta->execute();
        $resultado=$consulta->fetchAll();
        return $resultado;
    }

    /**
    *@method inserta una venta en la tabla ventas_tonos
    *
    */
    public function guardarVentaTono($id_venta,$status,$id_tono,$fecha,$precio){
        try {
          $insert=$this->conexion->prepare('INSERT INTO ventas_tonos (id_venta,status,id_tono,fecha_venta,precio)
                                              values(:id_venta,:status,:id_tono,:fecha_venta,:precio)' );
          $insert->bindParam(':id_venta',$id_venta);
          $insert->bindParam(':status',$status);
          $insert->bindParam(':id_tono',$id_tono);
          $insert->bindParam(':fecha_venta',$fecha);
          $insert->bindParam(':precio',$precio);
          $insert->execute();
          return $insert->rowCount();
        } catch (\Exception $e) {
          return false;
        }


    }

    public function obtenerVentasAll($id_autor){
      /*selecciona las ventas de los tonos cuyo autor sea igual al indicado*/
      $consulta=$this->conexion->prepare("SELECT *
                                    FROM ventas_tonos,tonos
                                    WHERE ventas_tonos.id_tono=tonos.id AND tonos.id_autor=:id_autor");
      $consulta->bindParam(':id_autor',$id_autor);
  		$consulta->execute();
  		$result=$consulta->fetch();
      return $result;
    }



    public function obtenerVentasPorMes($id_autor,$fecha){
      //formato de fecha yy-mm-dd
      //selecciona las ventas de los tonos que pertenescan a este usuario y que se
      //hayan vendido en esta fecha
      try {
        $anioActual=date('Y');

        $consulta=$this->conexion->prepare("SELECT count(ventas_tonos.id) as total,
                                      MONTH(ventas_tonos.fecha_venta) as mesVenta,
                                      YEAR(ventas_tonos.fecha_venta) as anioVenta
                                      FROM ventas_tonos,tonos
                                      WHERE (ventas_tonos.id_tono=tonos.id
                                            AND tonos.id_autor=:id_autor) AND status='COMPLETED'
                                      GROUP BY mesVenta,anioVenta
                                      HAVING mesVenta=:fecha AND anioVenta=:anio"
                                      );
        $consulta->bindParam(':id_autor',$id_autor);
        $consulta->bindParam(':fecha',$fecha);
        $consulta->bindParam(':anio',$anioActual);
        $consulta->execute();
        $result=$consulta->fetch();
        return $result;
      } catch (\Exception $e) {
        return array('success'=>false, 'msg'=>$e->getMessage());
      }


    }

    public function obtenerVentasDeTono($id_autor){
      //ventas por mes de un tono en especifico
      $anioActual=date('Y');

      $consulta=$this->conexion->prepare("SELECT  tonos.id as tono_id,
                                    COUNT(ventas_tonos.id) as cantidad,
                                    tonos.titulo as titulo,
                                    MONTH(ventas_tonos.fecha_venta) as mes,
                                    YEAR(ventas_tonos.fecha_venta) as anioVenta
                                    FROM ventas_tonos
                                    LEFT JOIN tonos ON ventas_tonos.id_tono=tonos.id
                                    WHERE tonos.id_autor=:id_autor AND status='COMPLETED'
                                    GROUP BY tono_id,mes,anioVenta
                                    HAVING anioVenta=:anio");

      $consulta->bindParam(':id_autor',$id_autor);
      $consulta->bindParam(':anio',$anioActual);
  		$consulta->execute();
  		$result=$consulta->fetchAll(PDO::FETCH_ASSOC);
      return $result;
    }


    public function enviarProductoPorEmail($emailUsuario,$url){
      $email = new Email();
      $mensajeSF=new DOMDocument("1.0");//SF significa sin filtrar(por que aun no se ha anadido ninguna url)
			$mensajeSF->loadHTMLFile(__DIR__.'/mensajeCompra.html');
      $base_url=$url;
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
      $resultado=$email->enviarEmail($_ENV['MAIL_EMAIL'],$emailUsuario,$mensaje,
                                  "melodia comprada","sandFirg.com",$base_url);
    }


    /**
    *@method usado para el controlador C_O_Ganancias (ususario)
    */
    public function obtenerTotalGanancias($id_autor){

      // IDEA: este metodo devolvera el total de ganancias de un usuario,
      //independientemente de si ha resivido un pago o no
      $anio=date('Y');//recoge el anio actual
      $consulta=$this->conexion->prepare('SELECT  IFNULL(SUM(ventas_tonos.precio),0) as total,
                                            YEAR(ventas_tonos.fecha_venta) as  anio
                                    FROM ventas_tonos,tonos
                                    WHERE  (ventas_tonos.id_tono=tonos.id AND  tonos.id_autor=:id_autor) and status="COMPLETED"
                                    GROUP BY anio
                                    HAVING anio=:anio;
                                    ');
      $consulta->bindParam(':id_autor',$id_autor);
      $consulta->bindParam(':anio',$anio);

      $consulta->execute();
      $result=$consulta->fetch();
      return $result;
    }

    public function obtenerGananciasPorMes($id_autor,$mes){
      // IDEA: devolvera  la suma total por cada mes de las ventas(aplicando comision)
      // realizadas como vista previa  si el pago aun no esta completado(no pendiente)
      // NOTE: tabla pagos y ventas
      $anio=date('Y');//recoge el anio actual
      $consulta=$this->conexion->prepare('SELECT SUM(ventas_tonos.precio) as total,
                                    MONTH(ventas_tonos.fecha_venta) as mes,
                                    YEAR(ventas_tonos.fecha_venta) as  anio
                                    FROM ventas_tonos,tonos
                                    WHERE (ventas_tonos.id_tono=tonos.id AND tonos.id_autor=:id_autor) AND status="COMPLETED"
                                    GROUP BY mes,anio
                                    HAVING (mes=:mes AND anio=:anio) ');
      $consulta->bindParam(':id_autor',$id_autor);
      $consulta->bindParam(':mes',$mes);
      $consulta->bindParam(':anio',$anio);
      $consulta->execute();
      $result=$consulta->fetch();
      return $result;
    }



    /**
    *total a pagar restando la comision
    **/
    public function getTotalPago($ventas,$comision,$cantidad){
      // setlocale(LC_MONETARY, 'en_US');
      switch (true) {
        case ($ventas > 0)://(0=0) false=true
            $rest=($ventas/$cantidad);
            $comisionTotal = ($rest*$comision);

            $result=$ventas-$comisionTotal;
            $result=number_format($result,2);
          break;
        case ($ventas <= 0):
          $result=0;
          break;
          default:
            $result=0;
          break;
      }
      return $result;
    }


    public function getIdAutor($idProducto){
      $sql='SELECT id_autor FROM tonos WHERE id=:id_producto';
      try {
          $select=$this->conexion->prepare($sql);
          $select->bindParam(':id_producto',$idProducto);
          $select->execute();
          $row=$select->rowCount();
          return ($row<=0)? false : $select->fetch(PDO::FETCH_OBJ)->id_autor;
      } catch (\Exception $e) {
          error_log("sldb:".$e->getMessage());
          echo "sldb:".$e->getMessage();
      }
    }









}









?>
