<?php
require_once __DIR__.'/../../modelo/Productos.php';
class C_Output_Detalles
{
  private $productos;
  public $detallesProducto;
  function __construct()
  {
    $this->productos = new ProductosM();
  }
  function solicitud(){

  }
  public function origenDeSolicitud(){
		return basename($_SERVER['PHP_SELF']);
	}
  function getDetallesDeProducto(){
    return $this->productos->obtenerInfoProducto('tono');

  }
  public function __initMain(){
    switch ($this->origenDeSolicitud()) {
      case 'publicaUnTono.php':

        $this->detallesProducto = $this->getDetallesDeProducto();

        break;
      default:
        // code...
        break;
    }
  }
}
$c_o_detalles = new C_Output_Detalles();
$c_o_detalles->__initMain();







?>
