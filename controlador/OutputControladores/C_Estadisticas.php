<?php
require __DIR__.'/../../modelo/Usuarios.php';//aqui si se requiere llamar a la clase por que se utiliza ajax
require __DIR__.'/../../modelo/Productos.php';


      $productos = new ProductosM();
      $idAutor=Usuarios::getIdUsuario();
      $ventasPorMes = array();

      //el metodo selecciona todas las venta que se realizaron en un mes
      $resultado=$productos->obtenerVentasPorMes($idAutor,"12");

      //necesito devolver el numero de registros que existen en total por cada mes del anio en curso
      //desde la fecha de registo

      // IDEA:
      /*esto se puede hacer de dos maneras.
      1.haciendo una consulta de conteo a la base de datos segun la fecha en mysql --escogida como mas eficiente--
      2. contando en este controlador el numero de registros que existen por cada fecha que sea igual a la que se le índique
        es decir si fechaBDVenta=='enero 2019'{ count++ y guarda en un array para devolver}*/

      //obtener ventas por cada mes
      for ($i=0; $i <12 ; $i++) {
        $resultado=$productos->obtenerVentasPorMes($idAutor,($i+1));
      $ventasPorMes[$i] = (is_array($resultado))?comprobarVentas($i): "0";

      }


      function comprobarVentas($i){
         if($resultado['total']==0){//si no existen ventas en ese mes
              $ventasPorMes[$i]=0;
          }else{
              $ventasPorMes[$i]=intval($resultado['total']."0");
          }
          return $ventasPorMes[$i];
      }


      //obtener las ventas de cada tono por mes y año
      $ventasPorTono=$productos->obtenerVentasDeTono($idAutor);


      $respuesta = array('ventasPorMes'=>$ventasPorMes,
                         'ventasPorTono'=>$ventasPorTono,);
      echo json_encode($respuesta);
