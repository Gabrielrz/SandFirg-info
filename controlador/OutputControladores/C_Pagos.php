<?php

require_once __DIR__.'/../../modelo/Pagos.php';
require_once __DIR__.'/../../modelo/configuracion.php';
require_once __DIR__.'/../../modelo/Usuarios.php';
/**
 *
 */
class C_Pagos
{

    public function __construct()
    {

    }

    function getFechaPago(){
      $pagos=new Pagos();
      return $pagos->getFechaPago();
    }
    function getFechaBase(){
        $pagos=new Pagos();
      return $pagos->getFechaBase();
    }
    function getFechaActual(){
      $pagos=new Pagos();

      return $pagos->getFechaActual();
    }




    public function obtenerConfiguracion($nombreConfig){
      $configuracion=new Configuracion();
      return $configuracion->getConfiguracion($nombreConfig);
    }

    function getDatosDeVentasPorUsuario($fecha_base,$fecha_fin,$mes){
      $datosVentas=array();
      $pagos=new Pagos();
      $usuariosConVentas=$pagos->obtenerUsuariosConVentas();
      $mes = ($mes=='current')?date('n'):'';
// echo $mes;
// IDEA:en caso de que ya se ubiera pagado y quisiera comprobar el pago del mes actual
// IDEA: deberia poder entrar a los datos y visualizar el pago realizado sin poder pagar
// IDEA: hay que cambiar el envio de datos para mas opciones de visualizacion
// IDEA: podria poner otra opcion en el selector del mes en el que solo muestre los pagos realizados en la primera screen
      foreach ($usuariosConVentas as $usuario) {
        $comision=$pagos->getComisionIndividual($usuario['id']);
        $ventas=$pagos->getSumVentas($usuario['id']);

        $valueVentas=(array_key_exists($mes,$ventas))?$ventas[$mes][0]['total']: '0';

        // $comisionTotal=$pagos->getComisionTotal($ventas[$mes][0]['total'],$comision['comision']);
        // $total_a_pagar=$pagos->getTotalPago($ventas[$mes][0]['total'],$comisionTotal);
        $cantidadVentas=$pagos->getVentas($usuario['id']);

        $infoDePagos=$pagos->getPagos($usuario['id']);
        $datosVentas[]=array('id_autor'=>$usuario['id'],
                             'nombre_autor'=>$usuario['nombre'],
                             'ventas_este_mes'=>$valueVentas,
                             'cuenta_de_pago'=>$usuario['cuenta'],
                             'fecha_actual'=>$this->getFechaActual(),
                            );
      }
      //print_r($datosVentas);
      return $datosVentas;
    }


    function getDatosModalScreenUno($id){
      $pagos=new Pagos();
      $meses_de_ventas=$pagos->getMesesConPagosPendientes($id);
      $infoDePagos=$pagos->getPagos($id);
      echo json_encode(array('datos'=>$meses_de_ventas,
                             'info_de_pagos'=>$infoDePagos,
                             'status'=>'ok'));
    }
    function getDatosModalIndividual($id_usuario,$mes){/*este metodo es llamado por una funcion ajax*/

        if ($id_usuario!=null) {
            $mes = ($mes=='current')? date('n'): $mes;
            $pagos=new Pagos();
            $usuario=(new Usuarios())->getUsuarioPorId($id_usuario);
            $comision=$pagos->getComisionIndividual($id_usuario);
            $ventas=$pagos->getSumVentas($id_usuario);

            if(array_key_exists($mes,$ventas)){
                $comisionTotal=$pagos->getComisionTotal($ventas[$mes][0]['total'],$comision['comision']);
                $total_a_pagar=$pagos->getTotalPago($ventas[$mes][0]['total'],$comisionTotal);
                $cantidadVentas=$pagos->getVentas($id_usuario);
                $totalesPYC=$pagos->getTotalesPYC($id_usuario);
                $infoDePagos=$pagos->getPagos($id_usuario);
                $datosVentas=array('id_autor'=>$id_usuario,
                                     'nombre_autor'=>$usuario['nombre'],
                                     'cantidad_vendido'=>$cantidadVentas[$mes][0]['cantidad'],
                                     'ventas_este_mes'=>$ventas[$mes][0]['total'],
                                     'ventas_por_mes'=>$ventas,
                                     'comision_aplicada'=>$comision['comision'],
                                     'comision_actual_obtenida'=>$comisionTotal,
                                     'total_a_pagar'=>$total_a_pagar,
                                     'total_pagado'=>$totalesPYC['totalPadago'],
                                     'cuenta_de_pago'=>$usuario['cuenta'],
                                     'info_de_pagos'=>$infoDePagos,
                                     'total_recaudado'=>$totalesPYC['totalRecaudado'],
                                     'fecha_actual'=>$this->getFechaActual(),
                                    );

              echo json_encode(array('datos'=>$datosVentas,
                                     'status'=>'ok'));
            }else{
              echo json_encode(array('status'=>'error',
                                     'mensaje'=>'este mes no contiene ventas o ya estan pagadas'));
            }
        }
    }

    /**
    *funcion se encarga de llamar a los metodos que requieren de ajax y fetch
    */
    function __initAF($funcion){

      switch ($funcion) {
        case 'getDatosModalIndividual':
          $this->getDatosModalIndividual(filter_input(INPUT_POST,'id_usuario',FILTER_SANITIZE_SPECIAL_CHARS),filter_input(INPUT_POST,'mes_seleccionado',FILTER_SANITIZE_SPECIAL_CHARS));
          break;
        case 'getDatosModalScreenUno':
          $this->getDatosModalScreenUno(filter_input(INPUT_POST,'id',FILTER_SANITIZE_SPECIAL_CHARS));
          break;
      }

    }




}
  $c_pagos=new C_Pagos();
  $datosV=$c_pagos->getDatosDeVentasPorUsuario($c_pagos->getFechaPago(),$c_pagos->getFechaActual(),'current');
  $c_pagos->__initAF(filter_input(INPUT_POST,'function',FILTER_SANITIZE_SPECIAL_CHARS));
 ?>
