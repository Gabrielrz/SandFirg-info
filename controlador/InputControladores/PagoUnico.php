<?php
namespace pagoUnico;
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
header('Content-Type: application/json');

class CrearPagoUnico
{

    private const CUENTA_INVALIDA='400';





    public static function buildRequestBody($datos){

        if(!empty($datos['cuenta_de_pago'])){
          return json_decode(
              '{
                  "sender_batch_header":
                  {
                    "email_subject": "'.$datos['email_subject'].'"
                  },
                  "items": [
                  {
                    "recipient_type": "EMAIL",
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
    /**
    *sube un pago en la bbdd y actuliza las ventas echas para invalidarlas
    *es decir para identificarlas como ya pagadas con el id del pago en bbdd
    *
    */
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

    public static function CreatePayout($debug=false,$datos)
    {
        $request = new PayoutsPostRequest();
        if(($body=self::buildRequestBody($datos))==self::CUENTA_INVALIDA){
          Utilidades::mensaje('error','este usuario no tiene una cuenta de pago registrada');
        }else{
          try{
            $request->body = $body;
            $client = PayPalClient::client();
            $response = $client->execute($request);

            $datos['status_code']=$response->statusCode;
            $datos['payout_batch_id']=$response->result->batch_header->payout_batch_id;
            $datos['mes_de_pago']=date('n');
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
            return "respuesta pago:".$response->statusCode;
          }catch(\Exception $e){
            //deberia guardar pago fallido en la base de datos
            //aunque si no se actualiza la table ventas ya quedaria registro
            //ya quedara registro por que no se actualiza con el id del pago en tabla ventas
            ///se guarda la venta en la BBDD en paypal-transaction-complete
            return "errores de pago:".$e->getMessage();
          }
        }
    }

}

// if (!count(debug_backtrace())) {
//     CreatePayoutSample::CreatePayout(true);
// }

 ?>
