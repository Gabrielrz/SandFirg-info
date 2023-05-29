
$(document).ready(function(){
		// checkbox de la tabla principal
	$('[data-toggle="tooltip"]').tooltip();
	// Select/Deselect checkboxes
	var checkbox = $('table tbody input[type="checkbox"]');
	$("#selectAll").click(function(){
		if(this.checked){
			checkbox.each(function(){
				this.checked = true;
			});
		} else{
			checkbox.each(function(){
				this.checked = false;
			});
		}
	});
	checkbox.click(function(){
		if(!this.checked){
			$("#selectAll").prop("checked", false);
		}
	});

});
/*CODIGO PARA LA VENTANA MODAL*/
$(document).ready(function(){
	 startModalWindow();
	 traspasarIdDeReferencia();// NOTE: envia el id del usuario seleccionado a la ventana modal(boton)
	 getDataOnCLickManage();
  });
function startModalWindow(){


		abrirWindowModal($('.gestionarUsuario'),$('.box-modal'),'.manage');/*usuario individual*/

		var screenTwo=$('.screenTwo');
		var screenTree=$('.screenTree');
		$('.continue_B').on('click',function(){
			screenTwo.animate({'left':'-100%'},'fast','swing');
			screenTree.animate({'right':'0px'},'fast','swing');
			$('.box-modal').animate({'height':'80vh'});
		});

		abrirWindowModal($('.gestionGlobal'),$('.box-modal'),'.btn-pagar');/*multiples usuarios*/
		cargarDatosIndividuales();
	}
	function abrirWindowModal(modalId,boxModal,buttonId){

		$(buttonId).on('click',function(){/*abrir*/
				modalId.css({'display':'block',
										'width':'100%',
										'height':'100%'
										});
				if(window.innerWidth<=600){
						boxModal.css({'width':'100%'});
				}else{
					boxModal.css({'width':'50%','height':'90vh'});
				}

		});
		$('.modal-close').on('click',function(){
					boxModal.css({'width':'100px'});
					modalId.hide('fast');
					screens=".box-modal-contenido";//screenOne y screenTwo comparten la misma clase
					$(screens).removeAttr('style');
					$('.mensaje').html('');

		});
	}


function traspasarIdDeReferencia(){
		$('.manage').on('click',function(){
			var id=$(this).attr('data');
			$('.continue_A').attr('data',id);
		});
}

async function getDataOnCLickManage(){
	$('.manage').on('click',async function(){
		var id=$(this).attr('data');
		info=new FormData();
		info.append('id',id);
		info.append('function','getDatosModalScreenUno');
		var res = await requestDatos(info,'../../controlador/OutputControladores/C_pagos.php');
		$('select[name=selector_mes] option').removeClass('class_pago_pendiente');
		if(res.status=='ok'){
			res.datos.forEach((item, i) => {
					console.log(item.mes);
					$('select[name=selector_mes] option[value='+item.mes+']').addClass('class_pago_pendiente');
			});
			cargarDatosEnTablaPagosRealizados(res,'.tablaUsIndividual');
		}

	});
}

 async function cargarDatosIndividuales(){
	$(".continue_A").on('click',async function(event){//envio de pago individual

			var ajax_url='../../controlador/OutputControladores/C_pagos.php';
			var id=$(this).attr('data');
			var mes_seleccionado=$('.selector_mes').val();
			var datosVerif=new FormData();
			datosVerif.append("id_usuario",id);
			datosVerif.append('mes_seleccionado',mes_seleccionado);
			datosVerif.append('function','getDatosModalIndividual');
			//{"accion":"true"}//para enviar los datos de esta manera hay que borrar el parametro ajax processData
			var requestdatos = await requestDatos(datosVerif,ajax_url);
			cargarDatosEnVista(requestdatos,'.tablaUsIndividual');


	});
}
function requestDatos(formData,ajax_url){
	return fetch(ajax_url, {
			method: 'post',
			body: formData,
		}).then(function(res) {
			return res.json();
		}).then(function(data) {
			// console.log(data);
			return data;
		});


}
function cargarDatosEnTablaPagosRealizados(respuesta,tabla){
		if(respuesta.status=='ok'){
					var datos_pagos=respuesta.info_de_pagos;
					console.log(respuesta);
					$(tabla+"> tbody").html("");
					for (var i = 0; i < datos_pagos.length; i++) {
						var tr='<tr>'
						+'<td>'+i+'</td>'
						+'<td>'+datos_pagos[i]['fecha_pago']+'</td>'
						+'<td>'+datos_pagos[i]['monto_pago']+'</td>'
						+'<td>'+datos_pagos[i]['cantidad_comision']+'</td>'
						+'<td>'+datos_pagos[i]['status_code']+'</td>'
						+'</tr>';
						$(tabla+"> tbody").append(tr);
					}
		}
}
/**
*funcion: carga los datos en una tabla en especifico:(modal tabla pagos realizados)
*/
function cargarDatosEnVista(respuesta,tabla){
	if(respuesta.status=='ok'){


		$('.nombre_set_modal').text(respuesta.datos['nombre_autor']);
		$('[name="inp_totalPagoU"]').val(respuesta.datos['total_pagado']);//total pagado todo el tiempo
		$('[name="inp_TotalComisionU"]').val(respuesta.datos['total_recaudado']);//total recaudado todo el tiempo
		$('[name="inp_totalVendidoU"]').val(respuesta.datos['ventas_este_mes']);
		$('[name="inp_comisionAplicadaU"]').val(respuesta.datos['comision_aplicada']);
		//datos screen 2
		$('[name="inp_totalBrutoV"]').val(respuesta.datos['ventas_este_mes']);
		$('[name="inp_totalTransformTonos"]').val(respuesta.datos['cantidad_vendido']);
		$('[name="inp_comisionAplicadaEdi"]').val(respuesta.datos['comision_aplicada']);
		$('[name="inp_comisionAplicadaEdiTranform"]').val(respuesta.datos['comision_actual_obtenida']);
		$('[name="inp_fechaActual"]').val(respuesta.datos['fecha_actual']);
		$('[name="inp_TotalAPagar"]').val(respuesta.datos['total_a_pagar']);
		$('[name="id_autor"]').val(respuesta.datos['id_autor']);//*debil
		// cargarDatosEnTablas(respuesta.datos['info_de_pagos'],'.tablaUsIndividual');



		$('.screenOne').animate({'left':'-100%'},'fast','swing');
		$('.screenTwo').animate({'right':'0px'},'fast','swing');


	}else if(respuesta.status=='error'){
		$('.mensaje').html(respuesta.mensaje);
	}
}

function animacionDeModalScreen(){

}
