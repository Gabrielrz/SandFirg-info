<?php

require_once __DIR__."/../../modelo/Usuarios.php";
require_once __DIR__.'/../../modelo/configuracion.php';
	$email=filter_input(INPUT_POST,'emaillog',FILTER_UNSAFE_RAW);//$_POST['emaillog'];
	$pass=filter_input(INPUT_POST,'passwordlog',FILTER_UNSAFE_RAW);//$_POST['passwordlog'];
	// $checkM=filter_input(INPUT_POST,'mantenerLogeo',FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE);



	$comprobarusuario=new Usuarios();
	$resultado=$comprobarusuario->comprobar($email,$pass);
	$url="";
	if($resultado==USUARIO_VALIDADO){

			// NOTE: comprobar opcion mantener
			// if($checkM){
			// 	// NOTE: guardar coockies de session y session en bbdd
			//
			//
			// }

			$url_sitio = (new Configuracion)->getConfiguracion("url_sitio");
			$url= $url_sitio->valor;
			$role = $comprobarusuario->getRole(Usuarios::getIdUsuario());
		if($role==Usuarios::ADMIN){
			$url_admin=(new Configuracion)->getConfiguracion("url_admin");
			$url.=$url_admin->valor;
		}else{
			$url_user=(new Configuracion)->getConfiguracion("url_user");
			$url.=$url_user->valor;
		}

		$envio=array(

			'validacion'=>true,
			'mensaje'=>'el inicio de sesion ha sido exitoso',
			'url_redireccion'=>$url,
			);

		echo json_encode($envio);

		//header('location:http://localhost/linksRingtones/PanelControl/control.php');//el header no sirve para esta parte por que el ajax lo toma como una linea de codigo en texto

	}else if($resultado==USUARIO_NO_ENCONTRADO){

		$envio=array(

			'validacion'=>false,
			'mensaje'=>'el usuario no ha sido localizado.'

			);

		echo json_encode($envio);

	}else if($resultado==CONTRASENA_INCORECTA){

		$envio=array(

			'validacion'=>false,
			'mensaje'=>'la contraseña no es correcta'

			);

		echo json_encode($envio);

	}







?>
