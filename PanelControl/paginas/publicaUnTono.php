<?php
require_once __DIR__.'/../../controlador/OutputControladores/C_detalles_de_entrada.php';
include '../header.php';
// echo phpinfo();
?>
	<div id="contenedor_principal">
		<div id="contenedor_publicacion">
			<h3 class="desc_form">Introduce la informacion del sonido</h3>
			<div class="publicaTono">
				<form id="form_envia_sonido" method="post" enctype="multipart/form-data">
					<div class="wrapF">
						<section class="columna_p a">
								<!-- <label id="lbl_imagen-pubT" for="input_imagen-pubT">selecciona una imagen</label>
								<input type="file" id="input_imagen-pubT" name="input_imagen_portada" accept="image/png, image/jpeg,image/webp"> -->
								<div class="file-upload">
								  <!-- <button class="file-upload-btn" type="button" onclick="$('.file-upload-input').trigger( 'click' )">Add Image</button> -->
								  <div class="image-upload-wrap">
								    <input class="image-upload-input"  type='file' name="input_imagen_portada" onchange="leerUrlPortada(this);" accept="image/*" />
								    <div class="drag-text">
								      <h3>Selecciona una imagen</h3>
								    </div>
								  </div>
								  <div class="file-upload-content">
										<div class="box_file_image">
											<img class="file-upload-image" src="#" alt="your image" />
										</div>
								    <div class="image-title-wrap">
								      <button type="button" onclick="removeUploadPortada()" class="remove-image">Eliminar <span class="image-title">Imagen Cargada</span></button>
								    </div>
								  </div>
								</div>
						</section>
						<section class="columna_p b">
							<div class="file-upload">
								<!-- <label id="lbl_sonido-pubT" for="input_sonido-pubT">SONIDO</label>
								<input type="file" id="input_sonido-pubT"  name="input_sonido-pubT" accept="audio/*"> -->
								<div class="audio-upload-wrap">
									<input class="sound-upload-input" type='file' name="input_sonido-pubT" onchange="cargaImagenSonido(this);" accept="audio/*" />
									<div class="drag-text">
										<h3>Selecciona un sonido</h3>
									</div>
								</div>
								<div class="sound-upload-content">
									<img class="file-upload-sound" src="#" alt="your image" />
									<div class="sound-title-wrap">
										<span class="sound-title">Uploaded sound</span>
										<audio  id="audio_editor" controlslist="nodownload" preload="auto" src="#"> navegador no activo</audio>
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
						<input type="text" name="inputTitulo" placeholder="Titulo del tono de llamada">
					</div>
					<div class="wrapF">
						<label>descripcion:</label>
						<textarea name="inputDescripcion" placeholder="digita una descripcion corta"></textarea>
					</div>
					<div class="wrapF">
							<label class="switch">
							  <input type="checkbox" name="checkVender" id="checkVender">
							  <span class="slider round"></span>
								<span class="textSwitch">Vender aqui</span>
							</label>
					</div>
					<div class="wrapF">
						<label class="switch">
							<input type="checkbox" name="checkGratis" id="checkGratis" checked>
							<span class="slider round"></span>
							<span class="textSwitch">gratis</span>
						</label>
					</div>
					<div class="wrapF">
						<label class="switch">
							<input type="checkbox" name="checkIncluirTiendas" id="checkIncluirTiendas">
							<span class="slider round"></span>
							<span class="textSwitch">incluir tiendas</span>
						</label>
					</div>
					<div class="wrapF box_Tiendas">
						<label for="urlTienda">URL de Una Tienda Online</label>
						<div id="contenedorTiendasOnline">
							<input type="text" class="nombreTienda" name="input_NombreTienda[]" placeholder="nombre de tienda" >
							<input type="url" class="urlTienda"  name="input_UrlTienda[]" placeholder="URL">

						</div>
						<br>
						<input type="button"  id="btn_anadirTiendas" value="+">
					</div>

					<div class="wrapF box_Precio">
						<label>Precio:</label>
						<input type="text" name="precio" value="<?=$c_o_detalles->detallesProducto['precio'] ?>" disabled="true" readonly>
					</div>
					<div class="wrapF">
						<input type="hidden" name="accion" value="insert">
						<input type="button" id="btnEnviaSonido" value="publicar/enviar" name="boton">
					</div>
				</form>
				<div id="respuestas"></div>
			</div>
		</div>
</div>

</body>
<?php
Funciones::back();
include '../footer.php';
?>

</html>
