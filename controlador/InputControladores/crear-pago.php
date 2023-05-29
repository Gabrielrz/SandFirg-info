<?php
namespace Sample;
require __DIR__.'/../../vendor/autoload.php';
require_once __DIR__.'/../../modelo/Pagos.php';
require_once __DIR__.'/../../modelo/Usuarios.php';
require_once __DIR__.'/../Controlador.php';
require_once __DIR__.'/PayPalClient.php';
use miAppPaypal\PayPalClient;
use Pagos;
use Usuarios;
use Utilidades;
use PaypalPayoutsSDK\Payouts\PayoutsPostRequest;
//header('Content-Type: application/json');

class CreatePayoutSample
{

    private const CUENTA_INVALIDA='400';



   public static function getDatosRequeridos($id_usuario,$mes){
        $mes = ($mes=='current')? date('n'): $mes;

        $usuario=new Usuarios();
        $usuario=$usuario->getUsuarioPorId($id_usuario);
        $pagos=new Pagos();
        $ventas_en_bruto=$pagos->getSumVentasPorMes($id_usuario,$mes);
        $comision_porcentual=$pagos->getComisionIndividual($id_usuario);
        $comisionTotal=$pagos->getComisionTotal($ventas_en_bruto['total'],$comision_porcentual['comision']);
        $monto_de_pago=$pagos->getTotalPago($ventas_en_bruto['total'],$comisionTotal);
        $cantidadVentas=$pagos->getVentasPorMes($id_usuario,$mes);

        // //la fecha de pago sera la fecha en la que se cree el pago

        $datosDePago=array( 'id_autor'=>$id_usuario,
                              'monto_de_pago'=>$monto_de_pago,
                              'cantidad_de_ventas'=>$cantidadVentas['cantidad'],
                              'fecha_pago'=>$pagos->getFechaActual(),
                              'cantidad_comision'=>$comisionTotal,
                              'cuenta_de_pago'=>$usuario['cuenta'],
                              'sujeto'=>'Pago de prueba',
                              'email'=>$usuario['email'],
                              'nota'=>'nota del pago',
                              'moneda'=>'EUR',
                              'mes_de_pago'=>$mes,
                              );
        //print_r($datosDePago);
        return $datosDePago;
  }


    public static function buildRequestBody($datos){

        if(!empty($datos['cuenta_de_pago'])){
          return json_decode(
              '{
                  "sender_batch_header":
                  {
                    "email_subject": "SDK payouts test txn"
                  },
                  "items": [
                  {
                    "recipient_type": "PAYPAL_ID",
                    "receiver": "'.$datos["cuenta_de_pago"].'",
                    "note": "'.$datos["nota"].'",
                    "sender_item_id": "Test_txn_12",
                    "amount":
                    {
                      "currency": "'.$datos["moneda"].'",
                      "value": "'.$datos["monto_de_pago"].'"
                    }
                  }]
                }',
              true);
        }else{

          return self::CUENTA_INVALIDA;
        }

    }
    public static function crearPago($datos){
      $creaPagos=new Pagos();
      $id_pago=$creaPagos->addBDPago($datos['id_autor'],
                            $datos['monto_de_pago'],
                            $datos['cantidad_de_ventas'],
                            $datos['fecha_pago'],
                            $datos['cantidad_comision'],
                            $datos['status_code'],
                            $datos['payout_batch_id']);
    $filas_afectadas=$creaPagos->updateVentas($id_pago,$datos['id_autor'],$datos['mes_de_pago']);
    return $filas_afectadas;
    }

    public static function CreatePayout($debug=false)
    {
        $request = new PayoutsPostRequest();
        $datos=self::getDatosRequeridos(filter_input(INPUT_POST,'id_autor',FILTER_SANITIZE_SPECIAL_CHARS),
                                        filter_input(INPUT_POST,'mes',FILTER_SANITIZE_SPECIAL_CHARS));
        if(($body=self::buildRequestBody($datos))==self::CUENTA_INVALIDA){
          Utilidades::mensaje('error','este usuario no tiene una cuenta de pago registrada');
        }else{
            $request->body = $body;
            $client = PayPalClient::client();
            $response = $client->execute($request);

            $datos['status_code']=$response->statusCode;
            $datos['payout_batch_id']=$response->result->batch_header->payout_batch_id;
            $filas_afectadas=self::crearPago($datos);


            if($debug){
                print "Status Code: {$response->statusCode}\n";
                print "Status: {$response->result->batch_header->batch_status}\n";
                print "Batch ID: {$response->result->batch_header->payout_batch_id}\n";
                print 'filas afectadas:'.$filas_afectadas;
                print "Links:\n";
                foreach($response->result->links as $link)
                {
                    print "\t{$link->rel}: {$link->href}\tCall Type: {$link->method}\n";
                }
                // To toggle printing the whole response body comment/uncomment below line
                echo json_encode($response->result, JSON_PRETTY_PRINT), "\n";
            }
            return $response;
        }
    }

}
$accion=filter_input(INPUT_POST,'accion',FILTER_SANITIZE_SPECIAL_CHARS);
if($accion==="true"){
    if (!count(debug_backtrace())) {
        CreatePayoutSample::CreatePayout(true);
    }
}
 ?>
