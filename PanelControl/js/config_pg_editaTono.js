/*BLOQUE para la pagina editarTono, para previsualizar la imagen portada*/
function previewPortadas(url) {
        // para la imagen subida por el usuario
        $('.image-upload-wrap').hide();
        $('.file-upload-image').attr('src', url);
        $('.file-upload-content').show();
        //para la imagen predeterminada del audio
        $('.audio-upload-wrap').hide();
        $('.file-upload-sound').attr('src','/imagenes/predeterminado/background_sound_v3.png');
        $('.sound-upload-content').show();
}previewPortadas();

//EDITA LA INFORMACION DEL SONIDO ENVIADO
$("#btn_aceptar_editar").on('click',function(event){
  var esteform=document.getElementById('form_edita_sonido');
  var datosFormulario=new FormData(esteform);
  var dataAcciones = $('.image-upload-input').attr('data');
  datosFormulario.append('accionesDeCarga',dataAcciones);
  console.log(dataAcciones);
  var ajax_url='/controlador/InputControladores/C_Actualiza_Sonidos.php';
  $.ajax({
      url:ajax_url,
        type: "POST",
        data:datosFormulario,
        contentType: false,
        cache: false,
        processData:false,
        dataType:"json",
        error: function(jqXHR,textStatus,errorThrown){
            $('body').html(jqXHR.responseText);
            alert(textStatus);
            alert(errorThrown);
      },
      success:function(respuesta){
          $('#respuestas').empty();
          if(respuesta.status==false){
            for (var i =0;i<respuesta.mensaje.length;i++) {
              $('#respuestas').append("<div  class='alert alert-warning' role='alert'>"+respuesta.mensaje[i]+"</div>");
            }
          }else if(respuesta.status==true){
            $('#respuestas').append("<div  class='alert alert-success' role='alert'>"+respuesta.mensaje+"</div>");
          }
      }
  });
});
