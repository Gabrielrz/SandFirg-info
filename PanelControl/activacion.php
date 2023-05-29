 <?php
   include('header.php');
?>
<div id="contenedor-pt">
      <div class="box_simulate_modal">
        <div class="box_activacion_mensaje">
          <span>aun no esta activada tu cuenta, para activar tu cuenta confirma el mensaje de activacion enviado a tu correo.</span>
          <span>si no has recibido un mensaje de activacion presiona este <a href="#" onclick="reenvioMesajeActivacion()">enlace</a> para enviar uno nuevo.</span>
        </div>
      </div>

    <div class="position-fixed top-50 right-50 p-3" style="z-index: 5; right: 200px; bottom: 100px;">
      <div id="liveToast" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true" data-delay="2000">
        <div class="toast-header">
          <img src="/imagenes/predeterminado/logov6s.png" class="rounded w-25" alt="...">
          <strong class="mr-auto">SandFirg</strong>
          <small></small>
          <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="toast-body">
          Hola, Se ha enviado un nuevo correo.
        </div>
      </div>
    </div>
</div>


<div class="st-footer">
</div>
<script type="text/javascript">
    jQuery.noConflict();
    function reenvioMesajeActivacion(){
        var datos=new FormData();
        datos.append('tipoSolicitud',120);
      return fetch('/controlador/OutputControladores/ControladorActivacion.php', {
        method: 'POST',
        body: datos,
      }).then(function(res) {
        return res.json();
      }).then(function(data) {
        if(data.ei==true){
          $(document).ready(function(){
              $('.toast').toast('show');
          });
        }
        return data;
      });

    }

</script>
