<?php

require __DIR__ . '/../../vendor/autoload.php';
require __DIR__.'/../../modelo/Productos.php';

require_once __DIR__.'/../../modelo/configuracion.php';
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;

class C_O_Descarga
{
  private $producto;
  private $fileSystem;
  function __construct()
  {
    $adapter = new Local(__DIR__.'/../../');
    $this->fileSystem = new Filesystem($adapter);
    $this->producto=new ProductosM();

  }


  public function capturarArchivo(){
    // NOTE: codificar un token de descarga unico
        $id_venta=filter_input(INPUT_GET,'IDV',FILTER_SANITIZE_SPECIAL_CHARS);
        $status=filter_input(INPUT_GET,'status',FILTER_SANITIZE_SPECIAL_CHARS);
        if($status=='COMPLETED'){
          $url_sitio=(new Configuracion)->getConfiguracion("url_sitio");
          $sonido=$this->producto->obtenerProductoPorIdVenta($id_venta);
          $url_sonido=$sonido['sonido'];
          $filename=__DIR__.'/../../'.$url_sonido;
          // print_r($filename);
          // print_r($url_sonido);

          if(file_exists($filename)){
            //Define header information
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header("Cache-Control: no-cache, must-revalidate");
            header("Expires: 0");
            header('Content-Disposition: attachment; filename="'.basename($filename).'"');
            header('Content-Length: ' . filesize($filename));
            header('Pragma: public');
            flush();
            readfile($filename);
            die();
          }else{
            return 'ha habido un problema, intentelo mas tarde';
          }
        }

}




}

$c_o_descarga=new C_O_Descarga();
$c_o_descarga->capturarArchivo();
