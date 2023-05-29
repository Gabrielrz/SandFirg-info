<?php



class Campana {

	private $con;


	public function __construct(){

		require_once("conectar.php");
		$this->con=Conectar::conexion();


	}



	public function getCampana($id_camp){


		$consulta=$this->con->prepare('SELECT * FROM campanias');
		$consulta->execute();
		$camp=$consulta->fetch();


		return $camp;

	}

	public function getFechasCamp(){
		$consulta=$this->con->prepare('SELECT fecha_inicion,fecha_fin FROM campanias');
		$consulta->execute();
		$camp=$consulta->fetch();
		return $camp;
	}



}
