$(document).ready(function(){

  previewImageProfile();
  configuracionBtnTogleSwitch();
  eventsBtnAddTiendas();
  var path=window.location.pathname;
  switch (true) {
    case path.includes('/publicaUnTono'):
        eventsEstadoDePortada();
        imagenPredeterminadaPublicaTono();
      break;
    case path.includes('/editarTono'):
      eventsEstadoDePortada();
      break;
    case path.includes('/misTonos'):
      borrarSonido('#dialog_del_sonido');
    break;
    case path.includes('/ajustesPerfil'):
      btnStripeCrearCuenta();
  }
});

/*
*@method : se encarga de crear una imagen predeterminada de la plataforma en publicaTono,
*
*/
function imagenPredeterminadaPublicaTono(){
  $('.image-upload-wrap').hide();
  $('.file-upload-image').attr('src', '/imagenes/predeterminado/portada-predeterminada_v1.png');
  $('.file-upload-content').show();
  $(".image-upload-input").attr('data','predeterminada');
}
/**
*se encarga de leer y crear una preview de la imagen subida(se llama en la view)
*/
function leerUrlPortada(input) {
    if(input.files && input.files[0]){
      var reader = new FileReader();
      reader.onload = function(e){
        $('.image-upload-wrap').hide();
        $('.file-upload-image').attr('src', e.target.result);
        $('.file-upload-content').show();
        $('.image-title').html(input.files[0].name);
      };
      reader.readAsDataURL(input.files[0]);
    }else{
      removeUploadPortada();
    }
}
/**
*encargado de los cambios en la portada
*/
function eventsEstadoDePortada(){
  $(".image-upload-input").attr('data','neutral');
  $(".image-upload-input").on('change',function(event){ $('.image-upload-input').attr('data','update'); });
  $(".remove-image").on('click',function(event){ $(".image-upload-input").attr('data','remove'); });
}

/**
*controla el boton del audio cargado (boton de la preview)(se llama en la view)
*@param activo al ser el boton del play el que se preciono, toma como referencia ese boton para activar la funcion
*@param audio si el audio acaba se activa la funcion onended
*@param g es el boton activado en ese momento
*/
function controlAudio(g,activo) {
    var audio=document.getElementById("audio_editor");
    if(!activo){
        audio.play();
        g.style.display="none";
        g.nextElementSibling.style.display="inline";
        audio.onended=function(){
          g.style.display="inline";
          g.nextElementSibling.style.display="none";
        }
    }else if(activo){
        audio.pause();
        g.style.display="none";
        g.previousElementSibling.style.display="inline";
    }
}

/**
*controla la seleccion de una imagen en la pagina ajustesPerfil
*/
function previewImageProfile(){
  var readURL = function(input) {
      if (input.files && input.files[0]) {
          var reader = new FileReader();
          reader.onload = function (e) {
              $('#imagen_perfil').fadeIn("slow").attr('src', e.target.result);
          }
          reader.readAsDataURL(input.files[0]);
      }
  }
  $('#input_foto-usuario').on('change',function(event){
       var path=$('#input_foto-usuario').val();
         readURL(this);
  });
}

/**
*funcion para el boton remover portada (se llama desde view)
*/
function removeUploadPortada() {
    $('.image-upload-input')[0].value=null;
    $('.file-upload-content').hide();
    $('.file-upload-image').attr('src','#');
    $('.image-upload-wrap').show();
    /*
    $('.image-upload-input').replaceWith($('.image-upload-input').clone());
    no hace falta por que value = null lo borra
    */
}
function configuracionBtnTogleSwitch(){
  var btnVenderAqui=$("#checkVender");
  var btnGratis=$("#checkGratis");
  var btnIncluirTiendas=$("#checkIncluirTiendas");
  $('.box_Tiendas').hide();
  $('.box_Precio').hide();
  if(btnVenderAqui.is(':checked')||btnIncluirTiendas.is(':checked')){
    $('.box_Tiendas').show();
    $('.box_Precio').show();
  }
  $('#checkVender').on('change',function(event){
    if(btnVenderAqui.is(':checked')){
      $('.box_Precio').show();
      btnGratis.prop('checked', false);
    }else{
      $('.box_Precio').hide();
    }
  });
  $('#checkGratis').on('change',function(event){
    if(btnGratis.is(':checked')) {
      $('.box_Tiendas').hide();
      $('.box_Precio').hide();
      btnVenderAqui.prop('checked', false);
      btnIncluirTiendas.prop('checked',false);
    }else{
    }
  });
  $('#checkIncluirTiendas').on('change',function(event){
    if(btnIncluirTiendas.is(':checked')){
      $('.box_Tiendas').show();
      btnGratis.prop('checked',false);
    }else{
      $('.box_Tiendas').hide();
    }

  });
}

/**
*BLOQUE de codigo para la pagina PUBLICATONO para anadir tiendas
*/
function eventsBtnAddTiendas(){
  $("#btn_anadirTiendas").on('click',function(event){
    var htmlInput="<input type='text' class='nombreTienda'"
                  +"name='input_NombreTienda[]' placeholder='Nombre de Tienda'>"
                  +"<input type='url' class='urlTienda'  name='input_UrlTienda[]'"
                  +"placeholder='URL'>";
     $('#contenedorTiendasOnline').append(htmlInput);
  });
}

/**
*Carga la imagen preview del sonido cuando se carga uno y
*tambien carga el sonido en local para poder escucharlo
*se llama tanto en publicarTono como en EditarTono
*/
function cargaImagenSonido(input){
  if(input.files && input.files[0]){
    var reader = new FileReader();
    reader.onload = function(e){
      $('.audio-upload-wrap').hide();
      $('.file-upload-sound').attr('src', '/imagenes/predeterminado/background_sound_v3.png');
      $('.sound-upload-content').show();
      $('.sound-title').html(input.files[0].name);
      var data = new Uint8Array(e.target.result);
      var blob = new Blob([data], {type: 'audio/*'});
      $('#audio_editor').attr('src',webkitURL.createObjectURL(blob));
    };
    reader.readAsArrayBuffer(input.files[0]);
  }else{
    // removeUploadPortada();
  }
}

function borrarSonido(){
  $('#dialog_del_sonido').dialog({
        autoOpen: false,
        width:400,
        dialogClass: "alert",
        modal:true,
        buttons: {
        "borrar": async function() {
            var datos=new FormData();
            datos.append('accion',"rmSonido");
            datos.append('id',$(this).attr('data-del'));
            var data = await requestFetch('/controlador/InputControladores/C_Actualiza_Sonidos.php',datos);
            if(data.response==200){
              $("#dialog_del_sonido").dialog( "close" );
              location.reload();
            }
        },
        Cancel: function() {
          $( this ).dialog( "close" );
        }
      }
      });
    $('.btn_eliminar').on('click',function(){
      $('#dialog_del_sonido').dialog('open');
      let data=$(this).attr('data');
      $("#dialog_del_sonido").attr('data-del',data);
    });

}



function btnStripeCrearCuenta(){
	$('#dialog_link_stripe').dialog({
				autoOpen: false,
				width:400,
				dialogClass: "alert",
				modal:true,
				buttons: {
				"acceptar": async function() {
					var datos = new FormData(document.getElementById('reauth_user'));
				  datos.append('orden',"link_stripe");
					var data = await requestFetch('/controlador/InputControladores/C_stripe.php',datos);
          if(data.status){
              if(data.redirect!=false){
                $('.mensajes_validacion').empty();
                $('.mensajes_validacion').append(data.mensaje);
                window.location.href=data.redirect;
              }else{
                $('.respuesta_stripe').html(data.mensaje).show('slow');
                $('.enlace_sandfirg').show('slow');
              }
              $(this).dialog( "close" );
              $('.btn_stripe').hide('slow');
          }else{
            $('.mensajes_validacion').empty();
            $('.mensajes_validacion').append(data.mensaje);
          }
				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			}
			});
		$('.btn_stripe').on('click',function(){
				$('#dialog_link_stripe').dialog('open');
		});


}


function requestFetch(url,datos){
  return fetch(url, {
      method: 'post',
      body: datos,
    }).then(function(res) {
      return res.json();
    }).then(function(data) {
      return data;
    });
}
