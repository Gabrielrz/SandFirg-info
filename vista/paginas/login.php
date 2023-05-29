
	<?php
		require __DIR__.'/../../dependencias.php';
		include __DIR__.'/../header.php';
	?>




<div id="contenedor-prin">
	<!-- <p>contenedor-prin</p> -->
	<div id="contenedor-medio">
		<!-- <p>contenedor-medio</p> -->

		<div class="contenedor-cent login">
			<!-- <p>contenedor-cent login</p> -->
			<div class="icon_log">
				<i class="fas fa-user-friends"></i>
			</div>
			<form method="post" id="form-login" enctype="multipart/form-data">
				<section class="fila-login row_inp">
					<label for="emaillog" >Correo electronico:</label>
					<input type="e-mail" id="emaillog" name="emaillog">
				</section class="fila-login">
				<section class="row_inp">
					<div class="elem_inp_password">
						<label for="passlog">Contraseña:</label>
						<input type="password"  id="passlog" name="passwordlog">
						<i class="far fa-eye-slash" onclick="viewPass(this)"></i>
					</div>
				</section>
				<!-- <section class="row_inp">
					<label for="mantenerLogeo" id="lbl_mantenerLogeo">mantener
						<input type="checkbox"  id="mantenerLogeo"  name="mantenerLogeo">
						<span class="check"></span>
					</label>
				</section> -->
				<section class="row_inp">
					<input type="button" id="btnlogear" value="Iniciar Sesion">
					<input type="button" onclick="pasar_al_registro()" id="btnRegistrar" name="btnRegistrar"  value='registrarse'>
				</section>
			</form>
			<div id="content-respuesta" style="display: none;"> </div>
		</div>
		<div class="contenedor-cent registro" >
			<!-- <p>contenedor-cent registro</p> -->
			<button id="btnVolver" onclick="volver_al_login()"><i class="fas fa-arrow-left"></i></button>

			<form mathod="post" id="form-registrar" enctype="multipart/form-data">
				<section class="row_inp">
					<label for="nombre_reg"  >Nombre:</label>
					<input type="text" name="nombre_registro" id="nombre_reg">
				</section>
				<section class="row_inp">
					<label for="nickname_reg"  >Nombre de usuario:</label>
					<input type="text" name="nickname_registro" id="nickname_reg">
				</section>
				<section class="row_inp">
					<label for="email_reg"  >Correo electronico:</label>
					<input type="e-mail" name="email_registro" id="email_reg">
				</section>
				<section class="row_inp">
					<div class="elem_inp_password">
						<label for="pass_reg"  >Contraseña:</label>
						<input type="password" name="pass_registro" id="pass_reg">
						<i class="far fa-eye-slash"  onclick="viewPass(this)"></i>
					</div>
				</section>
				<section class="row_inp">
					<div class="elem_inp_password">
						<label for="re_pass_reg"  >Confirmar contraseña:</label>
						<input type="password" name="confirm_pass_registro" id="re_pass_reg">
						<i class="far fa-eye-slash"  onclick="viewPass(this)"></i>
					</div>
				</section>
				<!-- <input type="button" name="confirmar_registro" id="confirmar_registro" value="Registrar"> -->
				<button class="btn btn-primary" id="confirmar_registro" type="button"  value="Registrar">
					<span class="spinner-border spinner-border-sm" role="status" style="display:none;"id="spinner" aria-hidden="true"></span>
						<!-- <span class="spinner-border spinner-border-sm"  role="status" aria-hidden="true"></span> -->
						Registrar
				</button>
			</form>
			<div id="content-respuesta-reg" style='display: none;'></div>
		</div>

	</div>
</div>
