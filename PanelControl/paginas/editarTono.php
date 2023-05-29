<?php
require_once __DIR__.'/../../controlador/OutputControladores/C_misTonos.php';
require_once(__DIR__.'/../header.php');
?>
<?php	if($c_o_sonido->accesoSolicitud){ ?>

	<div id="contenedor_principal">

			<div id="contenedor_publicacion">
				<h3 class="desc_form">edita la informacion del sonido</h3>

				<div class="caja_editar">
					<form id="form_edita_sonido" method="POST" enctype="multipart/form-data">
						<div class="wrapF">
							<section class="columna_p a">
									<div class="file-upload">
										<div class="image-upload-wrap">
											<input class="image-upload-input"  type='file'  name="input_portada_ed"  onchange="leerUrlPortada(this);" accept="image/*" />
											<div class="drag-text">
												<h3>Selecciona una imagen</h3>
											</div>
										</div>
										<div class="file-upload-content">
											<div class="box_file_image">
												<img class="file-upload-image" src="<?=RUTAABSOULTA.'/'.$c_o_sonido->rs['datos_sonido']['imagen'];?>"  alt="your image" />
											</div>
											<div class="image-title-wrap">
												<button type="button" onclick="removeUploadPortada()" class="remove-image">Remove <span class="image-title">Uploaded Image</span></button>
											</div>
										</div>
									</div>
							</section>
							<section class="columna_p b">
								<div class="file-upload">
									<div class="audio-upload-wrap">
										<input class="sound-upload-input" type='file' name="input_sonido-pubT"  accept="audio/*" />
										<div class="drag-text">
											<h3>Selecciona un sonido</h3>
										</div>
									</div>
									<div class="sound-upload-content">
										<img class="file-upload-sound" src="#" alt="your image" />
										<div class="sound-title-wrap">
											<span class="sound-title">sonido activo</span>
											<audio  id="audio_editor" controlslist="nodownload" preload="auto" src="<?php echo RUTAABSOULTA."/".$c_o_sonido->rs['datos_sonido']['sonido']; ?>"> navegador no activo</audio>
											<div class="contenedor_btns_pp">
												<button type="button" class="bton_play" onclick="controlAudio(this,false)"><i class="fas fa-play"></i></button>
												<button  type="button"class="bton_pause" onclick="controlAudio(this,true)"><i class="fas fa-pause"></i></button>
											</div>
										</div>
									</div>

								</div>
							</section>
						</div>
						<div class="wrapF">
							<label>Titulo:</label>
							<input type="text" name="input_titulo_ed" id="input_titulo_ed" value="<?=$c_o_sonido->rs['datos_sonido']['titulo'];?>" placeholder="Titulo del tono de llamada">
						</div>
						<div class="wrapF">
							<label>descripcion:</label>
							<textarea name="input_descripcion_ed" placeholder="digita una descripcion corta"><?= $c_o_sonido->rs['datos_sonido']['descripcion_corta'];?></textarea>
						</div>

						<div class="wrapF">
								<label class="switch">
								  <input type="checkbox" name="checkVender" id="checkVender" <?=in_array(Sonido::VENDER,$c_o_sonido->rs['datos_botones'])?'checked':'';?>>
								  <span class="slider round"></span>
									<span class="textSwitch">Vender aqui</span>
								</label>
						</div>
						<div class="wrapF">
							<label class="switch">
								<input type="checkbox" name="checkGratis" id="checkGratis" <?=in_array(Sonido::GRATIS,$c_o_sonido->rs['datos_botones'])?'checked':'';?>>
								<span class="slider round"></span>
								<span class="textSwitch">gratis</span>
							</label>
						</div>
						<div class="wrapF">
							<label class="switch">
								<input type="checkbox" name="checkIncluirTiendas" id="checkIncluirTiendas" <?=in_array(Sonido::TIENDAS,$c_o_sonido->rs['datos_botones'])?'checked':'';?>>
								<span class="slider round"></span>
								<span class="textSwitch">incluir tiendas</span>
							</label>
						</div>
						<div class="wrapF box_Tiendas">	<!-- to show box if  -->

							<label for="urlTienda">Datos de una tienda</label>
							<div id="contenedorTiendasOnline">

								<?php if(!empty($c_o_sonido->rs['datos_tiendas'])){

													foreach ($c_o_sonido->rs['datos_tiendas'] as $tienda) {
														echo '<input type="text" name="input_NombreTienda[]"  class="nombreTienda"  value="'.$tienda['nombre_tienda'].'">';
														echo '<input type="url" name="input_UrlTienda[]"   class="urlTienda"  value="'.$tienda['valor'].'">';
													}
											}else{
												echo '<input type="text" name="input_NombreTienda[]" class="nombreTienda" placeholder="nombre de tienda">';
												echo '<input type="url" name="input_UrlTienda[]"  class="urlTienda"  placeholder="URL">';
											}
								 ?>
							</div>
							<br>
							<input type="button"  id="btn_anadirTiendas" value="+">
						</div>

						<div class="wrapF box_Precio">
							<label>Precio:</label>
							<input type="text" name="precio" value="1.23$" disabled="true" readonly>
						</div>
						<div class="wrapF">
							<input type="hidden" name="accion" value="update">
							<input type="button" id="btn_aceptar_editar" value="guardar cambios" name="boton">
						</div>
					</form>
					<div id="respuestas"></div>
				</div>
			</div>
<?php }else{?> <p>NO SE HA ENCONTRADO NINGUN DATO</p> <?php }?>
	</div>

<?php
Funciones::back();
include '../footer.php';
?>
<script type="text/javascript" src="../js/config_pg_editaTono.js"></script>

</body>


</html>
