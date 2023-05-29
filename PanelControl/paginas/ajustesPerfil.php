<?php
include(__DIR__.'/../header.php');

require __DIR__.'/../../controlador/OutputControladores/C_Paypal.php';
require __DIR__.'/../../controlador/OutputControladores/C_stripe.php';

	// NOTE: datos obtenidos de la clase usuarios que se instancia en el header
	$nickname='';
	if(!empty($usuario['nickname'])){
		$nickname=$usuario['nickname'];
	}
	$nombre_usuario='';
	if(!empty($usuario['nombre'])){
		$nombre_usuario=$usuario['nombre'];
	}
	$email_usuario='';
	if(!empty($usuario['email'])){
		$email_usuario=$usuario['email'];
	}
	$cuenta_paypal='';
	if(!empty($usuario['cuenta'])){
		$cuenta_paypal=$usuario['cuenta'];
	}

	$imagen_avatar='';
	if(!empty($usuario['imagen_avatar'])){
		$imagen_avatar=$usuario['imagen_avatar'];
	}


?>



<div id="contenedor_principal">

	<div id="contenedor-perfil">
		<!-- <p>contenedor perfil</p> -->



		<div class="ajustes-perfil">
			<p>ajustes perfil</p>



			<form  method="post" id="form_ajustes_perfil"  enctype="multipart/form-data">

				<div class="contenedor_imagen">
					<!-- <p>contenedor imagen</p> -->
					<figure class="marco_imagen">
						<img src="<?php echo RUTAABSOULTA.'/'.$imagen_avatar; ?>" onerror="this.style.display='none'" id="imagen_perfil" >
					</figure>
				</div>
				<div class=" btn_cargar">
					<label id="btn_foto-usuario" for="input_foto-usuario">Cambiar foto</label>
					<input type="file" id="input_foto-usuario"  name="ajustes_foto_usuario">
				</div>
				<div class="wrapF">
					<label>Nombre de usuario:</label>
					<input type="text" id="input_nickname" value="<?php echo $nickname; ?>" name="ajustes_nickname" disabled>
				</div>
				<div class="wrapF">
					<label>Nombre:</label>
					<input type="text" value="<?php echo $nombre_usuario; ?>" name="ajustes_nombre">
				</div>
				<div class="wrapF">
					<label>Correo electronico:</label>
					<input type="e-mail" value="<?php echo $email_usuario; ?>" name="ajustes_correo">
				</div>


				<div class="wrapF">
						<?php switch ($c_Paypal->getTipoDeAccion()) {
							case 'get_partner':?>
											<label>añadir cuenta de pago:</label>
												<div dir="ltr" style="text-align: left;" trbidi="on">
													<script>
														(function(d, s, id) {
															var js, ref = d.getElementsByTagName(s)[0];
															if (!d.getElementById(id)) {
																js = d.createElement(s);
																js.id = id;
																js.async = true;
																js.src = "https://www.paypal.com/webapps/merchantboarding/js/lib/lightbox/partner.js";
																ref.parentNode.insertBefore(js, ref);
															}
														}(document, "script", "paypal-js"));
													</script>
													<a data-paypal-button="true" href="<?=$c_Paypal->action_url;?>&displayMode=minibrowser" target="PPFrame">conecte con paypal</a>
											</div>
					<?php break;?>
					<?php case 'show_partner' : ?>
											<label> id merchant:</label>
											<input type="text" name="" disabled value="<?=$c_Paypal->merchantVendor; ?>">
					<?php break; ?>
					<?php case 'get_account':?>
											<label>Añade una cuenta Paypal:</label>
											<input type="text" name="cuenta_paypal" value="">
					<?php	break; ?>
					<?php case 'show_account': ?>
											<label> Cuenta Paypal:</label>
											<input type="text" name="cuenta_paypal"   value="<?=$c_Paypal->cuenta_paypal; ?>">
					<?php break; ?>

					<?php 	} ?>
				</div>
				<div class="wrapF">
					<?php switch ($c_stripe->getTipoDeAccion()){
					  		case 'get_account_stripe': ?>
											<label>Añadir cuenta stripe:</label>
											<button class="btn_stripe" type="button" name="button">conectar cuenta</button>
											<span class="respuesta_stripe" style="display:none;"></span>
											<a href="mailto:contacto@sandfirg.com" class="enlace_sandfirg" style="display:none;">info</a>

					<?php break; ?>
					<?php case 'show_status_stripe': ?>
											<label>Cuenta Stripe:</label>
											<input type="text" name="cuenta_paypal" disabled  value="<?=$c_stripe->email_asociado; ?>">

					<?php break; ?>
					<?php case 'incomplete_status_stripe': ?>
											<label class="incomplete_status_stripe">Cuenta stripe incompleta:<i class="fas fa-exclamation-triangle"></i></label>
											<button class="btn_stripe" type="button" name="button">continuar proceso</button>
											<span class="respuesta_stripe" style="display:none;"></span>
											<a href="mailto:contacto@sandfirg.com" class="enlace_sandfirg" style="display:none;">info</a>

					<?php break; ?>
					<?php  } ?>
				</div>
				<div class="wrapF">
					<label>Nueva contraseña:</label>
					<input type="password" name="ajustes_nueva_password">
				</div>
				<div class="wrapF">
					<input type="button" id="btn_actualizar_usuario" value="Actualizar">
				</div>
				<div id="respuestas"></div>

			</form>
		</div>

	</div>
</div>

<div id="dialog_link_stripe" title="validacion">
  <small class="form-text text-muted">ingrese sus datos para acceder a el link de referencia</small>

  <form method="post" id="reauth_user">
    <fieldset>
			<div class="form-group">
				<label for="email">Email</label>
	      <input type="text" name="email" id="email" value="<?=$email_usuario?>" disabled class="form-control">
			</div>
      <div class="form-group">
	      <label for="password">Password</label>
	      <input type="password" name="password" id="password"  class="form-control">
			</div>
			<div class="alert alert-light mensajes_validacion" role="alert"></div>
    </fieldset>
  </form>
</div>

<?php Funciones::back(); ?>
<?=GroupVistas::loadScriptsFooter();?>
