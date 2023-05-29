<?php
//require __DIR__.'/../../modelo/Usuarios.php';//no se requiere ya que ya existe un objeto llamado en el header por la clase funciones
require __DIR__.'/../../modelo/Productos.php';

class C_O_Ganancias
{
 // IDEA: los datos deben ser de la tabla pagos realizados y pendientes
// NOTE: ñadir una columna en la que se especifique el estado de los pagos

   private $productos;
   public $meses = array();
   function __construct()
   {
     $this->productos=new ProductosM();
     $this->meses=['enero',
                  'febrero',
                  'marzo',
                  'abril',
                  'mayo',
                  'junio',
                  'julio',
                  'agosto',
                  'septiembre',
                  'octubre',
                  'noviembre',
                  'diciembre'];
   }


   public function SumaDePagosTotales(){
     $idAutor=Usuarios::getIdUsuario();
     $productoInfo=new ProductosM();
     $totalG=$productoInfo->obtenerTotalGanancias($idAutor);
     $totalG = (is_array($totalG))? $totalG['total'] : 0;

     $totalGPM = array();//total de ganancias por mes
      foreach ($this->meses as $key => $mes) {
        $val = ( is_array($res = $productoInfo->obtenerGananciasPorMes($idAutor,$key) ))?$res['total']:0;
        //si es un array retorna lo que hay dentro si no es 0
        $totalGPM[ $this->meses[$key] ] = $val;
      }
     $productoInfo->obtenerInfoProducto('tono');
     $comision = $productoInfo->obtenerInfoProducto('tono')['comision'];
     $precio  = $productoInfo->obtenerInfoProducto('tono')['precio'];
     $totalCalculado=$productoInfo->getTotalPago($totalG,$comision,$precio);
     $rs['totalG']=$totalG;
     $rs['totalGPM']=$totalGPM;
     $rs['subTotal']=$totalCalculado;
     $rs['comision']=$comision;
     $rs['precio']= $precio;
     return $rs;
   }



}

$outputGanancias=new C_O_Ganancias();
$rs=$outputGanancias->SumaDePagosTotales();
 ?>
