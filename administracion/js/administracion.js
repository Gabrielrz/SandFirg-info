
$(document).ready(function(){
    $("#e_p_i").on('click',function(event){//envio de pago individual
        //$('.close_modal').modal('hide');//cambiar por otra funcion

        var ajax_url='../../controlador/InputControladores/crear-pago.php';
        var form=document.getElementById('formIndividual');
        var mes=$('select[name=selector_mes]').val();
        var datosVerif=new FormData(form);
        datosVerif.append("accion","true");
        datosVerif.append('mes',mes);
        //{"accion":"true"}//para enviar los datos de esta manera hay que borrar el parametro ajax processData
          $.ajax({
              url:ajax_url,
              type: "POST",
              data:datosVerif,
              contentType: false,
              cache: false,
              processData:false,
              dataType:"json",
              error: function(jqXHR,textStatus,errorThrown){
                  $('body').html(jqXHR.responseText);
              },
              success:function(respuesta){
                    if(respuesta.status=='error'){
                          $('.mensaje').html(respuesta.mensaje);
                    }else{
                          $('.mensaje').html(respuesta);
                    }
                }
          });
    });
});
