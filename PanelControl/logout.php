<?php

	switch ($_GET['confirmacion']) {
		case true:
			echo salirYOrdernar();

			break;

		case false:
			echo 'no valido';
			break;

		default:

			break;
	}



	function salirYOrdernar(){
		session_name('login');
		session_start();
		$_SESSION['user_id']=null;
		session_destroy();
		header('Location:'.$_GET['link']);
	}
