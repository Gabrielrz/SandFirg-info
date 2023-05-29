//bloque de codigo para el temporizador de las campanas

ajax_url="../../controlador/OutputControladores/temporizador.php";
$.ajax({

    url:ajax_url,
      type: "POST",
      data:{verf:"alpha"},
      contentType: false,
      cache: false,
      processData:false,
      dataType:"json",
      error: function(jqXHR,textStatus,errorThrown){

          $('body').html(jqXHR.responseText);
          alert(textStatus);
          alert(errorThrown);
    },
    success:function(fechaBD){
        $("#tiempo").html(fechaBD.anio);

    }


});
