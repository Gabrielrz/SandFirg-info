<?php
require_once __DIR__.'/Usuarios.php';
require_once __DIR__.'/configuracion.php';
class Pagos extends Usuarios{
/*no hace falta llamar al contructor si tengo una clase extendida con lo necesario*/
  private  $autores = array();
  private $montoPagos = array();
  private $cantidadVentas = array();
  private $fecha_pagos = array();
  private $cantidad_comision = array();

  protected $fecha_pago;
  protected $fecha_base;
  protected $fecha_actual;
  public const STATUS_PAGO_INCOMPLETO='INCOMPLETED';
  public const STATUS_PAGO_COMPLETO='COMPLETED';
  public function __construct()
  {
    parent::__construct();

    $this->fecha_base=new DateTime($this->fecha_pago);//fecha base = fecha de pago + 1 mes
    $this->fecha_base->modify('+1 month');
    $this->fecha_base=$this->fecha_base->format('Y-m-d');
  }

  public function addBDPago($id_usuario,$monto_de_pago,$cantidad_ventas,$fecha_pago,$cantidad_comision,$status_code,$payout_batch_id){
            $sql="INSERT INTO pagos (id_autor,monto_pago,cantidad_ventas,fecha_pago,cantidad_comision,status_code,payout_batch_id)
                  values(:id_usuario,:monto_de_pago,:cantidad_ventas,:fecha_pago,:cantidad_comision,:status_code,:payout_batch_id)";
            $insert=$this->con->prepare($sql);
            $insert->bindParam(':id_usuario',$id_usuario);
            $insert->bindParam(':monto_de_pago',$monto_de_pago);
            $insert->bindParam(':cantidad_ventas',$cantidad_ventas);
            $insert->bindParam(':fecha_pago',$fecha_pago);
            $insert->bindParam(':cantidad_comision',$cantidad_comision);
            $insert->bindParam(':status_code',$status_code);
            $insert->bindParam(':payout_batch_id',$payout_batch_id);
            $insert->execute();
            $idInsert=$this->con->lastInsertId();
      			return $idInsert;
  }

  /**
  *@method: updateVentas:; este metodo se ocupa de actualizar todas las ventas
  *con el id del pago realizado subseleccionando el id de aquellas ventas cuyo
  *id_pago es null( es decir selecciona las ventas que no tienen un pago)
  *indicando el mes y el año en especifico
  * NOTE: consulta mal echa, hay que cambiarla o usar una mas simple
  */
  public function updateVentas($id_pago,$id_autor,$mes){
    $anio=date('Y');
    $sql='UPDATE ventas_tonos SET id_pago=:id_pago WHERE id IN (SELECT  subconsulta.id_venta
                                FROM (SELECT ventas_tonos.id as id_venta
                                      FROM ventas_tonos,tonos
                                WHERE (ventas_tonos.id_tono=tonos.id AND tonos.id_autor=:id_autor)
                                      and (ventas_tonos.id_pago is null)
                                      and MONTH(ventas_tonos.fecha_venta)=:mes and YEAR(ventas_tonos.fecha_venta)=:anio ) as subconsulta)';
    $update=$this->con->prepare($sql);
    $update->bindParam(':id_pago',$id_pago);
    $update->bindParam(':id_autor',$id_autor);
    $update->bindParam(':mes',$mes);
    $update->bindParam(':anio',$anio);
    $update->execute();
    return $update->rowCount();
  }
  /**
  *@method inserta una venta en la tabla ventas_tonos
  *@param id_venta es el id puede ser el id de venta de paypal o id de venta de paymentIntent de stripe
  */
  public function guardarVentaTono($id_venta,$status,$id_tono,$fecha,$precio){
      try {
        $insert=$this->con->prepare('INSERT INTO ventas_tonos (id_venta,status,id_tono,fecha_venta,precio)
                                            values(:id_venta,:status,:id_tono,:fecha_venta,:precio)' );
        $insert->bindParam(':id_venta',$id_venta);
        $insert->bindParam(':status',$status);
        $insert->bindParam(':id_tono',$id_tono);
        $insert->bindParam(':fecha_venta',$fecha);
        $insert->bindParam(':precio',$precio);
        $insert->execute();
        $row = $insert->rowCount();
        return ($row<=0)? false : $this->con->lastInsertId();
      } catch (\Exception $e) {
        return false;
      }


  }

  /**
  *@method actualiza una venta en especifico con el id del pago
  */
  public function updateVenta($idVenta,$idPago,$status){
    try {
      $sql='UPDATE ventas_tonos SET id_pago=:id_pago,status=:status WHERE id=:id_venta ';
      $update=$this->con->prepare($sql);
      $update->bindParam(':id_venta',$idVenta);
      $update->bindParam(':id_pago',$idPago);
      $update->bindParam(':status',$status);
      $update->execute();
      $row = $update->rowCount();
      return ($row<=0)? false : true;
    } catch (\Exception $e) {
      return false;
    }
  }
  /**
  *@method selecciona una venta en especifico mediante el id de venta
  */
  public function getVenta($idVenta){
    try {
      $sql='SELECT  * FROM ventas_tonos WHERE id_venta=:id_venta ';
      $select=$this->con->prepare($sql);
      $select->bindParam(':id_venta',$idVenta);
      $select->execute();
      $row = $select->rowCount();
      return ($row<=0)? false : $select->fetch(PDO::FETCH_OBJ);
    } catch (\Exception $e) {
      return false;
    }
  }
  /**
  *
  *@method comprobacionDeEstadoDeVentasPagadas:: este metodo se encarga de consultar
  *si se ha actualizado las ventas con el id de pago correctamente
  *
  */
  public function comprobacionDeEstadoDeVentasPagadas(){
    $sql='SELECT ventas_tonos.id as id_venta,ventas_tonos.id_pago
                                       FROM ventas_tonos,tonos
                                 WHERE (ventas_tonos.id_tono=tonos.id AND tonos.id_autor=:id_autor)
                                   and (ventas_tonos.id_pago=:id_pago)
                                 and MONTH(ventas_tonos.fecha_venta)=:mes and YEAR(ventas_tonos.fecha_venta)=:anio';

  }

  // IDEA: el punto de comparacion se produce cuando la fecha de pago es mayor que la fecha actual
  function getFechaPago(){
    $this->fecha_pago=new DateTime('now');//se obtiene el dia del mes en que se tiene que pagar
    $this->fecha_pago->setDate($this->fecha_pago->format('Y'),$this->fecha_pago->format('m'),$this->obtenerConfiguracion('dia_de_pago')->valor);
    if($this->fecha_pago->format('Y-m-d') > $this->getFechaActual()){//si la fecha de pago es mayor,retorna la ultima fecha de pago(en el mismo mes)
      $this->fecha_pago->modify('-1 month');
    }
    $this->fecha_pago=$this->fecha_pago->format('Y-m-d');
    return $this->fecha_pago;
    // NOTE: solucionar el error del if(si la fecha no es mayor se posiciona en el mismo mes)
    // IDEA: seria mejor buscar todos los pagos de cada mes pendientes
    // NOTE: los datos de ventas del usuario no concuerdan con las fechas de pago del administrador(solucionar)
  }
  function getFechaBase(){
    return $this->fecha_base;
  }
  function getFechaActual(){
    $this->fecha_actual=new DateTime('now');
    //$this->fecha_actual->setDate($this->fecha_actual->format('Y'),$this->fecha_actual->format('m'),'4');//pruebas
    $this->fecha_actual=$this->fecha_actual->format('Y-m-d');
    return $this->fecha_actual;
  }

  /**
  *calcula el porcentaje de comision del total de las ventas
  */
  public function porcentaje($porcentaje,$valor){
    $resultado=$porcentaje*$valor;
    return $resultado;
  }
  public function getComisionTotal($ventas,$comision){
    $total=0;
    $comisionTotal= $this->porcentaje(floatval($comision),floatval($ventas));
    return $comisionTotal;
  }
  /**
  *total a pagar restando la comision
  **/
  public function getTotalPago($ventas,$comisionTotal){
    $result=$ventas-$comisionTotal;
    //setlocale(LC_MONETARY, 'en_US');
    $result=number_format($result,2);
    return $result;
  }

  public function obtenerConfiguracion($nombreConfig){
    $configuracion=new Configuracion();
    return $configuracion->getConfiguracion($nombreConfig);
  }

  /**
  *@method getMesesConPagosPendientes obtiene todos los meses de un usuario en
  * los que existen pagos pendientes
  */
public function getMesesConPagosPendientes($id_usuario){
    $anio=date('Y');
    $sql='SELECT MONTH(ventas_tonos.fecha_venta) as mes,
                 YEAR(ventas_tonos.fecha_venta) as  anio
          FROM ventas_tonos,tonos
          WHERE (ventas_tonos.id_tono=tonos.id AND tonos.id_autor=:id_autor)
                and (ventas_tonos.id_pago is null)
          GROUP BY mes
          HAVING  anio=:anio';
    $consulta=$this->con->prepare($sql);
    $consulta->bindParam(':anio',$anio);
    $consulta->bindParam(':id_autor',$id_usuario);
    $consulta->execute();
    $result=$consulta->fetchAll(PDO::FETCH_ASSOC);
    // echo "<pre>";
    // print_r($result);
    // echo "</pre>";
    return $result;
}

public function getVentas($id_autor){/*obtiene las ventas de cada usuario por cada mes*/
  date_default_timezone_set('Europe/Madrid');
  $anio=date('Y');
  $consulta=$this->con->prepare('SELECT MONTH(ventas_tonos.fecha_venta) as mes,
                                        YEAR(ventas_tonos.fecha_venta) as  anio,
                                        IFNULL(count(*),0) as cantidad
                                FROM ventas_tonos,tonos
                                WHERE (ventas_tonos.id_tono=tonos.id AND tonos.id_autor=:id_autor)
                                      and (ventas_tonos.id_pago is null)
                                GROUP BY mes,anio
                                HAVING  anio=:anio
                                      ');//(is null) =>es para seleccionar los que no han sido pagados todavia
  $consulta->bindParam(':id_autor',$id_autor);
  $consulta->bindParam(':anio',$anio);
  $consulta->execute();
  $result=$consulta->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);

  return $result;

}

/**
*@method getSumVentas devuelve la suma de las ventas de cada mes que no han sido
*pagadas
*
*/
public function getSumVentas($id_autor){
  date_default_timezone_set('Europe/Madrid');
  $anio=date('Y');
  $consulta=$this->con->prepare('SELECT MONTH(ventas_tonos.fecha_venta) as mes,
                                        YEAR(ventas_tonos.fecha_venta) as  anio,
                                        IFNULL(sum(ventas_tonos.precio),0) as total,
                                        tonos.id_autor as autor
                                FROM ventas_tonos,tonos
                                WHERE (ventas_tonos.id_tono=tonos.id AND tonos.id_autor=:id_autor)
                                      and (ventas_tonos.id_pago is null)
                                GROUP BY mes,anio
                                HAVING  anio=:anio
                                      ');  //  and (ventas_tonos.fecha_venta BETWEEN :fecha_base AND :fecha_fin)
  $consulta->bindParam(':id_autor',$id_autor);
  $consulta->bindParam(':anio',$anio);
  $consulta->execute();
  $result=$consulta->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);
  return $result;
}

public function getSumVentasPorMes($id_usuario,$mes){
  $anio=date('Y');
  $sql='SELECT MONTH(ventas_tonos.fecha_venta) as mes,
                                        YEAR(ventas_tonos.fecha_venta) as  anio,
                                        IFNULL(sum(ventas_tonos.precio),0) as total,
                                        tonos.id_autor as autor
                                FROM ventas_tonos,tonos
                                WHERE (ventas_tonos.id_tono=tonos.id AND tonos.id_autor=:id_autor)
                                      and (ventas_tonos.id_pago is null)
                                GROUP BY mes,anio
                                HAVING  mes=:mes AND anio=:anio';
  $consulta=$this->con->prepare($sql);
  $consulta->bindParam(':id_autor',$id_usuario);
  $consulta->bindParam(':anio',$anio);
  $consulta->bindParam(':mes',$mes);
  $consulta->execute();
  $result=$consulta->fetch(PDO::FETCH_ASSOC);
  return $result;
}
public function getVentasPorMes($id_autor,$mes){
  date_default_timezone_set('Europe/Madrid');
  $anio=date('Y');
  $consulta=$this->con->prepare('SELECT MONTH(ventas_tonos.fecha_venta) as mes,
                                        YEAR(ventas_tonos.fecha_venta) as  anio,
                                        IFNULL(count(*),0) as cantidad
                                FROM ventas_tonos,tonos
                                WHERE (ventas_tonos.id_tono=tonos.id AND tonos.id_autor=:id_autor)
                                      and (ventas_tonos.id_pago is null)
                                GROUP BY mes,anio
                                HAVING  (anio=:anio AND mes=:mes)
                                      ');//(is null) =>es para seleccionar los que no han sido pagados todavia
  $consulta->bindParam(':id_autor',$id_autor);
  $consulta->bindParam(':anio',$anio);
  $consulta->bindParam(':mes',$mes);
  $consulta->execute();
  $result=$consulta->fetch(PDO::FETCH_ASSOC);

  return $result;

}

/**
*@method getTotalesPYC: consulta la tabla pagos y obtiene el total pagado
* y la comision total obtenida
*/
public function getTotalesPYC($id_autor){

  $sql="SELECT IFNULL(sum(monto_pago),0) as totalPadago,
               IFNULL(sum(cantidad_comision),0) as totalRecaudado
        FROM pagos
        WHERE id_autor=:id_autor";
  $consulta=$this->con->prepare($sql);
  $consulta->bindParam(':id_autor',$id_autor);
  $consulta->execute();
  $result=$consulta->fetch(PDO::FETCH_ASSOC);
  return $result;
}


public function getPagos($id_autor){
  $sql="SELECT *
        FROM pagos
        WHERE id_autor=:id_autor";
  $consulta=$this->con->prepare($sql);
  $consulta->bindParam(':id_autor',$id_autor);
  $consulta->execute();
  $result=$consulta->fetchAll();
  return $result;
}

/*
*la comision tiene que que estar escrita en dos sitios
*para cada usuario en especifico ya que el porcentaje
*se aplica al conjunto de las ventas de este usuario.(en la tabla usuarios);
*y para todos los tonos en general(en la tabla productos->comision);
*/

public function comisionSC(){/*seguro*/
    $consulta=$this->con->prepare('SELECT comision FROM productos where tipo="tono"');
    $consulta->execute();
    $result=$consulta->fetch();
    return $result;
}
public function getComisionIndividual($id_usuario){
  $consulta=$this->con->prepare('SELECT comision FROM usuarios where id=:id_usuario');
  $consulta->bindParam(':id_usuario',$id_usuario);
  $consulta->execute();
  $result=$consulta->fetch();
  return $result;
}


  /**
  *obtiene usuarios que tienen o alguna vez tuvieron alguna venta
  */
  public function obtenerUsuariosConVentas(){
    $consulta=$this->con->prepare('SELECT DISTINCT usuarios.*
                                  FROM usuarios,ventas_tonos,tonos
                                  WHERE usuarios.id=tonos.id_autor and tonos.id=ventas_tonos.id_tono');
		$consulta->execute();
		$usuario=$consulta->fetchAll();
		return $usuario;
  }




}



 ?>
