$(document).ready(function(){

	$("#btn_actualizar_usuario").on('click',function(event){
		var esteform=document.getElementById('form_ajustes_perfil');
		var datosFormulario=new FormData(esteform);
		var ajax_url='/controlador/InputControladores/C_Actualiza_Datos.php';
			$.ajax({
				url:ajax_url,
			   	type: "POST",
			   	data:datosFormulario,
			   	contentType: false,
			   	cache: false,
			   	processData:false,
			   	dataType:"json",
			   	error: function(jqXHR,textStatus,errorThrown){
				   		console.log(jqXHR.responseText);
							console.log(textStatus);
							console.log(errorThrown);
				},
				success:function(respuesta){
						if(respuesta.status==false){
							for (var i =0;i<respuesta.mensaje.length;i++) {
								$('#respuestas').html("<div  class='alert alert-warning' role='alert'>"+respuesta.mensaje[i]+'</div>');
							}
						}else{
							$('#respuestas').html("<div  class='alert alert-success' role='alert'>"+respuesta.mensaje+'</div>');
						}
				}
			});
	});



	$("#btnEnviaSonido").on('click',function(event){
		//alert(objeto.url);
		var esteform=document.getElementById('form_envia_sonido');
		var datosFormulario=new FormData(esteform);
		var dataAcciones = $('.image-upload-input').attr('data');
	  datosFormulario.append('accionesDeCarga',dataAcciones);
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
							$('#respuestas').append(jqXHR.responseText);
							console.log(textStatus);
							console.log(errorThrown);
				},
				success:function(respuesta){
					$('#respuestas').empty();

					if(respuesta.status==false){//diferenciar exito  para estilos de texto
						for (var i =0;i<respuesta.mensaje.length;i++) {
							console.log(respuesta.mensaje);
							$('#respuestas').append("<div  class='alert alert-warning' role='alert'>"+respuesta.mensaje[i]+"</div>");
						}
					}else{
						$('#respuestas').html('<div  class="alert alert-success" role="alert">'+respuesta.mensaje+'</div>');
						 setTimeout(function(){
							 window.location.href=respuesta.redirect;
						 },5000);
					}
				}
		});
	});
});
