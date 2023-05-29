<?php

namespace Sample;
include __DIR__ . '/../../modelo/Productos.php';
require_once __DIR__.'/../../modelo/configuracion.php';
require __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__.'/PayPalClient.php';
require_once __DIR__.'/PagoUnico.php';
//1. Import the PayPal SDK client that was created in `Set up Server-Side SDK`.
use miAppPaypal\PayPalClient;
use PayPalCheckoutSdk\Orders\OrdersGetRequest;
use ProductosM;
use Configuracion;
use pagoUnico\CrearPagoUnico;
class GetOrder
{


  public static function getOrder($orderId,$id_tono)
  {
      $res=[];
      // 3. Call PayPal to get the transaction details
      try{
          $client = PayPalClient::client();
          $response = $client->execute(new OrdersGetRequest($orderId));

          //conexion con la BD de mi servidor
          $ProductosM=new ProductosM();
          $id_venta=$response->result->id;
          $status=$response->result->status;

          $datosSonido=$ProductosM->getDetallesSonido($id_tono);//// WARNING: esta mal, se puede cambiar el id de tono ident desde js al enviar
          //la solicitud

          //establece la fecha actual.
          date_default_timezone_set('Europe/Madrid');
          $fecha_actual=date_create("now");
          $fecha_actual=date_format($fecha_actual,"Y-m-d");
          session_name('producto');
          session_start();
          $row=$ProductosM->guardarVentaTono($id_venta,$status,$id_tono,$fecha_actual,$datosSonido['precio']);//se guarda la venta en la BBDD
          $url_sitio=(new Configuracion)->getConfiguracion("url_sitio");

          $res['order']=$response->result;
          $res['status']='OK';
          $res['url_descarga']=$url_sitio->valor."/controlador/OutputControladores/C_Descarga.php?status=".$status."&IDV=".$id_venta;

          $ProductosM->enviarProductoPorEmail($response->result->payer->email_address,$res['url_descarga']);
          $monto_de_pago=(floatval($_SESSION['precio']) - floatval($_SESSION['comision']));
          //anadir la integracion de pago

            if($_SESSION['tipo_accion']=='account'){
                  $datos = array('cuenta_de_pago' => $_SESSION['destinatario'],
                                 'nota'=>'',
                                 'moneda'=>'EUR',
                                 'monto_de_pago'=>$monto_de_pago,
                                 'email_subject'=>'pago de SandFirg por tu tono en venta:'.$datosSonido['titulo'],
                                 'cantidad_comision'=>$_SESSION['comision'],
                                 'fecha_pago'=>$fecha_actual,
                                 'id_autor'=>$datosSonido['id_autor'],
                                 'cantidad_de_ventas'=>'1',
                               );
                $res['resPago']=CrearPagoUnico::CreatePayout(false,$datos);
            }

    }catch(\Exception $e){
        $res['error']='mensaje de error:'.$e->getMessage();
    }
      //echo json_encode($response->result, JSON_PRETTY_PRINT);
    echo json_encode($res,JSON_PRETTY_PRINT);
  }



}

if (!count(debug_backtrace()))
{
  //recibe los datos enviados desde el js de los botones de la funcion onAprove
  $data = json_decode( file_get_contents( 'php://input' ), true );

  GetOrder::getOrder($data['orderID'],$data['id_tono'], true);

  //desde aui tendria que enviar la direccion ha donde tiene que ir una vez que se guarde en la base de datos y se complete el pago
}
?>
