<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__.'/../../modelo/Productos.php';
require_once __DIR__.'/../../modelo/configuracion.php';
require_once __DIR__.'/../../modelo/datosPaypal.php';
require_once __DIR__.'/../../modelo/stripe.php';
require_once __DIR__.'/../../modelo/Pagos.php';
require_once __DIR__.'/../Controlador.php';
use Utilidades\Utilidades;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Formatter\IntlMoneyFormatter;
use Money\Money;
use Money\Parser\IntlMoneyParser;


class C_Productos
{
	private $productosM;
	public static $datos;
	public $tipo_accion;
	private $stripe;
	public $stripeIntentSecret;
	public $accesibilidad=false;
	private $money;
	private $currencies;
	private $numberFormatter;
	private $moneyFormatter;
	private $moneyParser;
	public function __construct()
	{
		$this->productosM=new ProductosM();
		$this->stripe = new StripeModel();

		$this->currencies = new ISOCurrencies();
		$this->numberFormatter = new \NumberFormatter('en_US', \NumberFormatter::CURRENCY);
		$this->moneyFormatter = new IntlMoneyFormatter($this->numberFormatter, $this->currencies);
		$this->moneyParser = new IntlMoneyParser($this->numberFormatter, $this->currencies);
	}
	public function getDatosForPaginacion(){
			try{
					$res=$this->productosM->getProductoSonidosForScroll($page=0,$limit=10);
					foreach ($res['items'] as $item => $producto) {//carga el array de datos con nuevos datos actualizados
						$productosConBotones[] = $this->crearBotonesDelProducto($producto);
					}
					$res['items']=$productosConBotones;
					return $res;
			}catch(Exception $e){
					 echo 'tonos no encontrados...';
			}

	}
	public function getAccesibilidad(){
		//deberia ser controlado por el middleware
		return $this->accesibilidad;
	}
	/**
	*@method extrae e instancia los datos y objetos necesarios para el metodo de pagos
	*de paypal
	*
	*/
	private function putPaypalData($ident){
		$rs=(new DatosPaypal())->getDatosPaypalPorSonido($ident);
		if($rs!=false){
			if($rs->tipo_registro==DatosPaypal::PAYPAL_PARTNER){
					$this->setTipoDeAccion('partner');
			}else if($rs->tipo_registro==DatosPaypal::PAYPAL_ACCOUNT) {
					$this->setTipoDeAccion('account');
			}
			$this->productosM->iniciarSessionDeVenta($ident,$rs->cuenta_paypal,$this->getTipoAccion());
		}
	}



	/**
	*@method : extrae e instancia los datos necesarios para implementar los
	*la pacarela de pagos de stripe de cada producto
	* NOTE: este metodo deberia estar en el controlador C_Stripe de salida
	*/
	public function putStripeData($ident){
		date_default_timezone_set('Europe/Madrid');
		$fecha_actual=date_create("now");
		$fecha_actual=date_format($fecha_actual,"Y-m-d");

		$id_usuario = $this->productosM->getIdAutor($ident);
		$rs = $this->stripe->getCuentaDBStripe($id_usuario);
		if($rs!=false){

	    $datos=$this->productosM->obtenerInfoProducto('tono');
			$intent = $this->stripe->crearPaymentIntent($datos['precio'],$datos['comision'],$rs->id_cuenta);
			(new Pagos())->guardarVentaTono($intent->id,StripeModel::PAGO_INCOMPLETO,$ident,$fecha_actual,$datos['precio']);
			$this->stripeIntentSecret = $intent->client_secret;
			$res['clientSecret'] = $this->stripeIntentSecret;
			echo  Utilidades::responseJson(true,$res);
		}else{

			echo  Utilidades::mensaje(false,$rs);
		}

	}
	/**
	*@method: comprueba la venta y descarga el producto comprado, // NOTE: intracion solo para stripe
	* NOTE: este metodo deberia estar en controlador C_stripe de salida
	**/
	public function downloadProductoIS($paymentIntent){
		//print_r($paymentIntent);
		$paymentIntent = json_decode($paymentIntent,false);
		$venta=(new Pagos())->getVenta($paymentIntent->id);
		if($venta->status==Pagos::STATUS_PAGO_COMPLETO){
			$url_sitio=(new Configuracion)->getConfiguracion("url_sitio");
			$res['status']=true;
			$res['url_descarga']=$url_sitio->valor."/controlador/OutputControladores/C_Descarga.php?status=".$venta->status."&IDV=".$venta->id_venta;
			echo json_encode($res);
		}else{
			$paymentIntent=$this->stripe->recuperarPaymentIntent($paymentIntent->id);
			if($paymentIntent!=false){
				if($paymentIntent->status=='succeeded'){
					$comision=number_format($paymentIntent->application_fee_amount/100,2);
					$monto_pagado = (floatval($venta->precio) - floatval($comision));
					$idAutor=$this->stripe->getIdUsuario($paymentIntent->transfer_data->destination)->id_usuario;
					$pago=new Pagos();
					date_default_timezone_set('Europe/Madrid');
					$fecha_actual=date_create("now");
					$fecha_actual=date_format($fecha_actual,"Y-m-d");
					$id_pago=$pago->addBDPago($idAutor,
																$monto_pagado,
																'1',
																$fecha_actual,
																$comision,
																'201',
																$paymentIntent->id);
			  	$rsUp=$pago->updateVenta($venta->id,$id_pago,'COMPLETED');
					if($rsUp==true){
						$url_sitio=(new Configuracion)->getConfiguracion("url_sitio");
						$res['status']=true;
						$res['url_descarga']=$url_sitio->valor."/controlador/OutputControladores/C_Descarga.php?status=".'COMPLETED'."&IDV=".$venta->id_venta;
						echo json_encode($res);
					}

				}else{
					$res['status']=false;
					$res['mensaje']='no encontrado';
					echo json_encode($res);
				}
			}

		}

	}
	public function getDatosForScroll($page=1,$limit=10){
			try{
					$res=$this->productosM->getProductoSonidosForScroll(true,$page,$limit);
					if($res['response']==200){
						foreach ($res['items'] as $item => $producto) {//carga el array de datos con nuevos datos actualizados
							$productosConBotones[] = $this->crearBotonesDelProducto($producto);
						}
						$res['items']=$productosConBotones;
					}
					echo json_encode($res);

			}catch(\Exception $e){
				echo $e->getMessage();
				http_response_code(400);
			}
	}



	function funcionloadAudio(){
		$id=filter_input(INPUT_POST,'id',FILTER_SANITIZE_SPECIAL_CHARS);
		echo json_encode($this->productosM->getSonidoEnVentaPorId($id));
	}



	/**
	*@method datosDeViewPago: muestra los datos requeridos para la view pago
	*/
	public function datosDeViewPago($id){/*funcion individual llamada por ajax o api fetch*/
				$datos=$this->productosM->getSonidoEnVentaPorId($id);
				return $datos;
	}

	public function isTipo($tipo){
		if(is_array(self::$datos)){
			if(in_array($tipo,json_decode(self::$datos['tipoNegociacion']))){
				return true;
			}else{
				return false;
			}
		}
	}

	public function crearBotonesDelProducto($producto){
		$tiendas=$this->productosM->getTiendas($producto['id']);
		$producto['botonesDeProducto']=array();//creo un array dentro de los datos
		$producto['url_page']='vista/paginas/page.php?ident='.$producto['id'];

		foreach (json_decode($producto['tipoNegociacion']) as $tipo) {
			if($tipo==ProductosM::VENDER){
				array_push($producto['botonesDeProducto'],'<button class="btn"><a href="vista/paginas/page.php?ident='.$producto['id'].'" ><i class="fas fa-shopping-cart"></i></a></button>');
			}
			if($tipo==ProductosM::GRATIS){//$producto['sonido']
				array_push($producto['botonesDeProducto'],'<button class="btn"><a href="'.$producto['sonidoURL'].'" download ><i class="fas fa-download"></i></a></button>');
			}
			if ($tipo==ProductosM::TIENDAS) {
				if(!empty($tiendas)){
					foreach ($tiendas as $tienda) {
						array_push($producto['botonesDeProducto'],'<button class="btn"><a href="'.$tienda['valor'].'" ><i class="fas fa-store"></i></a><span class="tooltiptext">'.$tienda['nombre_tienda'].'</span></button>');
					}
				}
			}
		}
		return $producto;
	}

	public static function getDatos(){
			return self::$datos;
	}
	public function setTipoDeAccion($tipo_accion){
		$this->tipo_accion=$tipo_accion;
	}
	public function getTipoAccion(){
		return $this->tipo_accion;
	}
	public function getCurrentFunction(){
		$funcion = filter_input(INPUT_POST,'func',FILTER_SANITIZE_SPECIAL_CHARS);
		if(empty($funcion)){
			$funcion  = basename($_SERVER['PHP_SELF']);
		}
		return $funcion;
	}
	/**
	*al llamar al metodo por js las respuestas no aparecen en la pagina de incio
	*o index, apareceran en respuesta json
	*/
	public function _initMain(){
				// $config_modoPaginacion=(new Configuracion())->getConfiguracion('modo_paginacion');

				switch ($this->getCurrentFunction()) {
					case 'page.php':
									$ident = filter_input(INPUT_GET,'ident',FILTER_SANITIZE_SPECIAL_CHARS);//// WARNING: causa errores si se elimina de url
									if($ident!=null){
										self::$datos=$this->datosDeViewPago($ident);
										if(self::$datos!=false){
											$this->accesibilidad=true;
											if(!$this->isTipo(ProductosM::GRATIS)){//SI NO ES GRATIS
												self::$datos['sonido']=400;//este dato es una url
											}
											if($this->isTipo(ProductosM::VENDER)){

												$money = $this->moneyParser->parse("$".self::$datos['precio']);
												self::$datos['precio'] =  $this->moneyFormatter->format($money); // outputs $1.00
												$this->putPaypalData($ident);

											}else {
												self::$datos['merchantIdInPayPal']=400;
											}
											if($this->isTipo(ProductosM::TIENDAS)){
													self::$datos['tiendas']=$this->productosM->getTiendas(self::$datos['id']);
											}else{
												self::$datos['tiendas']=400;
											}
										}else{
											// $this->accesibilidad=false;
											http_response_code(404);
											// header('HTTP/1.0 404 Not Found');
											die;
											//en caso de que no se ecuentre el producto // NOTE: deberia ser controlado por middleware
										}
									}else{
										// $this->accesibilidad=false;
										http_response_code(403);
										// header('HTTP/1.0 404 Not Found');
										die;
									}
					break;
					case 'loadAudio':
						$this->funcionloadAudio();
					break;
					case 'getDatosForScroll':
						$this->getDatosForScroll(filter_input(INPUT_POST,'page',FILTER_SANITIZE_SPECIAL_CHARS),filter_input(INPUT_POST,'limit',FILTER_SANITIZE_SPECIAL_CHARS));
					break;
					case 'getStripe':
						$this->putStripeData(filter_input(INPUT_POST,'ident',FILTER_SANITIZE_SPECIAL_CHARS));
					break;
					case 'download':
						$this->downloadProductoIS(filter_input(INPUT_POST,'paymentIntent'));
					break;
					default:
						http_response_code(400);
					break;
				}


	}





}

$c_productos = new C_Productos();
$c_productos->_initMain();

?>
