<?php
    require __DIR__.'/../../modelo/Usuarios.php';

    class C_Output_Activacion
    {
      public $tipoSolicitud;
      const HTTPSOL=120;
      const HTTPMENS=100;
      function __construct($tipoSolicitud)
      {
        $this->tipoSolicitud=$tipoSolicitud;

        if($this->tipoSolicitud==self::HTTPMENS){
          $this->mensajeUserActivacion();
        }else if($this->tipoSolicitud==self::HTTPSOL){
          $rs=(new Usuarios)->reEnviarMensajeActivacion();

          echo json_encode(array('ei' =>$rs ));
        }else{
          $this->activacionDeUsuarioPorCorreo();
        }
      }


      public function activacionDeUsuarioPorCorreo(){
        $mensaje="enhorabuena";
        $tokenID=$_GET['tokenID'];
        $email=$_GET['email'];
        $usuario=new Usuarios();
        $tokenBD=$usuario->obtenerTokenActivacion($email);
        if($tokenBD!=""){//si no existe el token(por que no existe este email)
            if($tokenID === $tokenBD){
                $usuario->activarUsuario($email,$tokenID,true);
                header('Location:/PanelControl/control.php');
            }else{
                $mensaje="token no valido, no se ha podido completar la activacion..";
            }
        }else{
              $mensaje="algo anda mal...email no valido, el token no existe";
        }
        echo $mensaje;
      }


      public function mensajeUserActivacion(){
        // $dp=new DOMDocument("1.0");
        // $dp->loadHTMLFile(__DIR__.'/../../PanelControl/activacion.php');
        $mensaje='<div class="notificacion"><div class="position-relative w-100 alert alert-warning alert-dismissible fade show" role="alert">
                    <strong>aun no has activado tu cuenta</strong> se te ha enviado un correo para activar tu cuenta
                    o has click en el enlace para enviar uno nuevo
                  </div></div>';
        // echo $dp->saveHTML();
        echo $mensaje;
      }

    }

$c_o_activacion=New C_Output_Activacion(filter_input(INPUT_POST,'tipoSolicitud',FILTER_SANITIZE_SPECIAL_CHARS));
?>
