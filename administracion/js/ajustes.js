$(document).ready(function(){
  var modalAdd=$('.addAjuste');
  var modalDel=$('.delAjuste');
  var close=$('#modal-close');
  var boxmodal=$('.box-modal');
  abrirWindowModal(modalAdd,boxmodal,'#btnEditarAjuste');
  abrirWindowModal(modalDel,boxmodal,'#btnDelAjuste');
  abrirWindowModal(modalAdd,boxmodal,'#btnAddAjuste');
  enviarDatosConfiguracion();
  getIdChecked();
  borrarConfiguracion();
});
function enviarDatosConfiguracion(){
  $("#guardarAjuste").on('click',function(event){
    var datosFormulario=new FormData(document.getElementById('form_add_ajuste'));
    var ajax_url='../../controlador/InputControladores/C_ajustes.php';
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
        },
        success:function(respuesta){
            if(respuesta.exito==false){
              for (var i =0;i<respuesta.mensaje.length;i++) {
                $('.mensaje').html(respuesta.mensaje[i]);
              }
            }else{
              $('.mensaje').html(respuesta.mensaje);
            }
        }
      });
  });
}

function getIdChecked(){
  $('.inp_check').on('click',function(){
    var selecteditems = [];
    $(".box_ajuste").find("input:checked").each(function (i, ob) {
        selecteditems.push($(ob).val());
    });
    $('.checks_ids_config').val(selecteditems);
  });

}
function borrarConfiguracion(){
  $("#borrarAjuste").on('click',function(event){
    var datosFormulario=new FormData(document.getElementById('form_del_config'));
    var ajax_url='../../controlador/InputControladores/C_ajustes.php';
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
        },
        success:function(respuesta){
            if(respuesta.exito==false){
              for (var i =0;i<respuesta.mensaje.length;i++) {
                $('.mensaje').html(respuesta.mensaje[i]);
              }
            }else{
              $('.mensaje').html(respuesta.mensaje);
            }
        }
      });
  });
}
