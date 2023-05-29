<?php
require_once __DIR__.'/../modelo/configuracion.php';
require_once __DIR__.'/../modelo/Usuarios.php';
  /**
   */
  class Autenticar
  {

    function __construct()
    {
      // code...
    }

    private function comprobarPase(){
        // IDEA: este metodo se encargaria de en cuanto se habra la pagina o url
        //comprobaria si las cookies perteneces al usuario y de ser asi comprobar el token, la expiracion
        //y de estar todo correcto actualizar la session user_id para permitir el acceso sin logeo
    }

    function httpPost($url, $data){
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        return $response;
        // $rs=$this->httpPost('http://linksringtones.local/controlador/OutputControladores/ControladorActivacion.php',array('tipoSolicitud'=>100));
        // echo $rs;
       // include __DIR__.'/../PanelControl/activacion.php'; //estaba en el if isActive
   }

    /**
    *@method middle: se encarga de diferenciar el tipo de acceso para los distintos roles segun el tipo de vistas
    *
    */
    function middle($groupVistas,$currentLocation){

      $vistasPrivadas=$groupVistas[Vista::U_PRIVADA];
      if(array_search($currentLocation,$vistasPrivadas)!==false){
        if(empty($_SESSION['user_id'])){
          session_name('login');
          session_start();
        }
        if(!isset($_SESSION['user_id'])){//si no ha iniciado session
          header('Location:/vista/paginas/login.php');
        }else if((new Usuarios())->isActive($_SESSION['user_id'],$_SESSION['user_email'])==false){
          header('Location:/PanelControl/activacion.php');
        }
      }
      if(array_search($currentLocation,$groupVistas[Vista::U_ADMIN])!==false){
        $url_sitio = (new Configuracion)->getConfiguracion("url_sitio");
        $url= $url_sitio->valor;
        $role = (new Usuarios())->getRole(Usuarios::getIdUsuario());
          if($role!==Usuarios::ADMIN){
            $url_user=(new Configuracion)->getConfiguracion("url_user");
            $url.=$url_user->valor;
            header('Location:'.$url);
          }
      }


      // if(array_search($currentLocation,$groupVistas[Vista::U_PUBLICA])!==false){// WARNING: se crea un bucle de peticiones para la pagina que se intenta pedit
      //   $url_sitio = (new Configuracion)->getConfiguracion("url_sitio");
      //   $url= $url_sitio->valor;
      //   $headers = get_headers($url, 1);
      //   if ($headers[0] == 'HTTP/1.1 404 Not Found') {
      //         echo "hola";
      //     }
      //
      // }

    }



  }


 ?>
