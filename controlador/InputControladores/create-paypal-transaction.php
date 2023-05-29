<?php

namespace Sample\CaptureIntentExamples;

require __DIR__ . '/../../vendor/autoload.php';
//1. Import the PayPal SDK client that was created in `Set up Server-Side SDK`.
require_once __DIR__.'/PayPalClient.php';
use miAppPaypal\PayPalClient;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;

 require_once __DIR__.'/../../modelo/datosPaypal.php';
 require_once __DIR__.'/../../modelo/Productos.php';

 use datosPaypal;
 use ProductosM;
class CreateOrder
{
//  NOTE: reemplazar create order para que lo maneje paypal en lugar de mi propio servidor
   public function __construct()
  {

  }
// 2. Set up your server to receive a call from the client
  /**
   *This is the sample function to create an order. It uses the
   *JSON body returned by buildRequestBody() to create a new order.
   */
  public static function createOrder($debug=false)
  {
    session_name('producto');
    session_start();
      //print_r("id de producto:".$_SESSION['id_sonido_venta']);
    $request = new OrdersCreateRequest();
    $request->prefer('return=representation');
    $datos=(new ProductosM())->obtenerInfoProducto('tono');
    // IDEA:configurar si  el pago es por partner o por cuenta normal
    if($_SESSION['tipo_accion']=='partner'){
      $destino=array('merchant_id' => $_SESSION['destinatario']);
      $request->body = self::buildRequestBody($destino,$datos['precio'],$datos['comision']);

    }else if($_SESSION['tipo_accion']=='account'){
      $destino=array('email_address' => $_SESSION['destinatario']);
      $request->body = self::buildRequestBodyNormal($datos['precio']);
      $_SESSION['comision']=$datos['comision'];
      $_SESSION['precio']=$datos['precio'];

    }
   // 3. Call PayPal to set up a transaction
    $client = PayPalClient::client();
    $response = $client->execute($request);
    if ($debug)
    {
      print "Status Code: {$response->statusCode}\n";
      print "Status: {$response->result->status}\n";
      print "Order ID: {$response->result->id}\n";
      print "Intent: {$response->result->intent}\n";
      print "Links:\n";
      foreach($response->result->links as $link)
      {
        print "\t{$link->rel}: {$link->href}\tCall Type: {$link->method}\n";
      }

      // To print the whole response body, uncomment the following line
    }
    echo json_encode($response->result, JSON_PRETTY_PRINT);
    return $response;
  }

  /**
     *
     * NOTE: para una INTEGRACION de PLATFORMS Y MARKETPLACES
     *
     */
    private static function buildRequestBody($destino,$precio,$comision)
    {
        return array(
            'intent' => 'CAPTURE',
            'purchase_units' =>
                array(
                    0 =>
                        array(
                            'amount' =>
                                array(
                                    'currency_code' => 'EUR',
                                    'value' => $precio,
                                ),
                            'payment_instruction' =>
                                array(
                                      'disbursement_mode' => 'INSTANT',
                                      'platform_fees' => array(
                                        0 => array(
                                              'amount' => array(
                                                    'currency_code' => 'EUR',
                                                    'value' => $comision
                                                  )
                                            )
                                      )
                               ),
                              'payee' =>$destino,
                        )
                ),

        );
    }

    /**
    *
    *NOTE: PARA UNA INTEGRACION DE CUENTA NORMAL
    *
    */
    private static function buildRequestBodyNormal($precio)
    {
        return array(
            'intent' => 'CAPTURE',
            'purchase_units' =>
                array(
                    0 =>
                        array(
                            'amount' =>
                                array(
                                    'currency_code' => 'EUR',
                                    'value' => $precio,
                                ),
                        )
                ),
        );
    }





}

if (!count(debug_backtrace()))
{
  CreateOrder::createOrder(false);
}




 //    Array
 // (
 //     [intent] => CAPTURE
 //     [purchase_units] => Array
 //         (
 //             [0] => Array
 //                 (
 //                     [amount] => Array
 //                         (
 //                             [currency_code] => USD
 //                             [value] => 100.00
 //                         )
 //
 //                     [payee] => Array
 //                         (
 //                             [email_address] => seller@example.com
 //                         )
 //
 //                     [payment_instruction] => Array
 //                         (
 //                             [disbursement_mode] => INSTANT
 //                             [platform_fees] => Array
 //                                 (
 //                                     [0] => Array
 //                                         (
 //                                             [amount] => Array
 //                                                 (
 //                                                     [currency_code] => USD
 //                                                     [value] => 25.00
 //                                                 )
 //
 //                                         )
 //
 //                                 )
 //
 //                         )
 //
 //                 )
 //
 //         )
 //
 // )
?>
