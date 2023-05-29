<?php
require_once __DIR__.'/../Controlador.php';
require_once __DIR__.'/../../modelo/Usuarios.php';
require_once __DIR__.'/../../modelo/Sonidos.php';
use Utilidades\Utilidades;


class C_Output_Sonido
{
	private $autor;
	private $sonido;
	public $idSonido;
	public $accesoSolicitud=false;
	public $mensaje;
	public $rs;
	function __construct()
	{
		$this->autor=Usuarios::getIdUsuario();
		$this->sonido=new Sonido();
		$this->idSonido=filter_input(INPUT_GET,'ident',FILTER_SANITIZE_SPECIAL_CHARS);
		$this->rs=array();
	}

	public function origenDeSolicitud(){
		return basename($_SERVER['PHP_SELF']);
	}

	public function showDatos(){

		switch ($this->origenDeSolicitud()) {
			case 'misTonos.php':

						$this->rs['mis_tonos'] = $this->sonido->getDatosSonidos($this->autor);
				break;
			case 'editarTono.php':
						if($this->sonido->comprobarSonido($this->autor,$this->idSonido)==false){
							$this->accesoSolicitud=false;
							$this->mensaje= "no tiene acceso a esta seccion";
						}else{
							$this->accesoSolicitud=true;
							$this->rs['datos_sonido']=$this->sonido->getSonidoAEditar($this->idSonido);
							$this->rs['datos_tiendas']=$this->sonido->getTiendas($this->idSonido);
							$this->rs['datos_botones']=json_decode($this->rs['datos_sonido']['tipoNegociacion']);
						}
				break;
			default:
				// code...
				break;
		}
	}

}

$c_o_sonido= new C_Output_Sonido();
$c_o_sonido->showDatos();






?>
