<?php
include("../../dependencias.php");
require_once __DIR__.'/../../controlador/OutputControladores/C_Productos.php';
include __DIR__.'/../header.php';


	$datos=C_Productos::getDatos();


?>


<div id="containergeneral">

	 <div class="contenidoPago">
		<div class="box_contenidoPago">
			<div class="column">
				<div class="box_page box_descripcion">
					<article class="titulo">
						<span><?= $datos['titulo'];?></span>
					</article>
					<article class="box_page descripcion">
						<span><?=$datos['descripcion_corta']; ?></span>
					</article>
				</div>
				<figure class="box portada_page">
					<img  alt="imagen no encontrada" src="<?='/'.$datos['imagen'];?>">
					<div class="box box_reproductor">
						<button type="button" class="page_btn page_bton_play" onclick="controlAudio(this,false)"><i class="fas fa-play"></i></button>
						<button  type="button"class="page_btn page_bton_pause" onclick="controlAudio(this,true)"><i class="fas fa-pause"></i></button>
					</div>
				<?php if ($c_productos->isTipo(ProductosM::VENDER)): ?>
					<div class="box box_precio">
						<span class="text_precio">Precio: <?=$datos['precio']; ?></span>
					</div>
				<?php endif; ?>
				</figure>
				<div class="box box_reproductor" style="display:none;">
					<audio class="reproductor_audio myaudio" style="display:none;" id="audio" controlslist="nodownload" dataFile="<?=$datos['dataFile'] ?>" data_id="<?=$datos['id']; ?>" controls preload="auto" src=""> navegador no activo</audio>
				</div>
			</div>
			<div class="column">
					<?php if ($c_productos->isTipo(ProductosM::VENDER)): ?>

						<div class="box_page">
							<button type="button" name="button" class="btn_comprar" onclick="listenerComprar(this)">Comprar</button>
						</div>
						<div class="box_payment_method">
							<div class="box_page box_align">
								<form id="payment_form_stripe" >
									<div id="payment-element">
										<!-- Elements will create form elements here -->

									</div>
									<button id="procesar_pago" class="btn_comprar btn_procesar">
										<div class="spinner hidden" id="spinner"></div>
										<span id="button-text">procesar pago</span>
									</button>
									<div id="payment_message">
										<!-- Display error message to your customers here -->
									</div>
								</form>
							</div>
							<div class="box_page separador">
									<span class=""> o </span>
							</div>
							<div class="box_page box_align">
								<div class="metodo_pago">
								</div>
							</div>
					</div>
					<?php endif; ?>
					<?php if($c_productos->isTipo(ProductosM::TIENDAS)){?>
						<article class="box box_tiendas">
							<div class="scroll_tiendas">
						<?php foreach ($datos['tiendas'] as $tienda): ?>
										<a class="btn_tienda" href=<?=$tienda['valor']?> > <i class="fas fa-store"></i> <span><?=$tienda['nombre_tienda'];?></span></a>

						<?php endforeach; ?>
							</div>
						</article>
					<?php } ?>

					<?php if ($c_productos->isTipo(ProductosM::GRATIS)): ?>
						<div class="box_page box_descarga">
							<div class="descarga">
								<a  class="a_descarga" href="<?='/'.$datos['sonido'] ?> " download> <i class="fas fa-download"></i> Descargalo gratis</a>
							</div>
						</div>
					<?php endif; ?>

			</div>
		</div>

	</div>


	          <section id="content-modal" class="modal-info">
	              <div class="box-modal">
	                <span class="cerrar"><button class="modal-close">X</button></span>
	                <div class="box-modal-contenido">
										<section class="sec sec_titulo">
											<p>Gracias por su compra, la descarga iniciara en unos segunos!</p>
										</section>
										<section class="sec sec_animacion_loader">
											<i class="fas fa-spinner loader_anim"></i>
										</section>
										<section class="sec sec_subtitulo">
										 <p>Si la descarga no inicia tras unos segundos por favor, pinche en el siguiente icono de enlace. </p>
									 </section>
									 <section class="sec sec_link">
										 <a href="#" class="link_dwnl_s"><i class="fas fa-download"></i></a>
									 </section>
	                </div>
	              </div>
	          </section>
</div>
<?php if($c_productos->isTipo(ProductosM::VENDER)): ?>
	<?php if ($c_productos->getTipoAccion()=='partner'):?>
		<script src="https://www.paypal.com/sdk/js?&client-id=<?=$_ENV['CLIENT_ID'] ?>&merchant-id=<?=$datos['merchantIdInPayPal'] ?>&currency=EUR"></script>
	<?php elseif($c_productos->getTipoAccion()=='account'):?>
		<script src="https://www.paypal.com/sdk/js?&client-id=<?=$_ENV['CLIENT_ID'] ?>&currency=EUR"></script>

	<?php endif; ?>
	<script>
				paypal.Buttons({
						createOrder: function() {
								return fetch('../../controlador/InputControladores/create-paypal-transaction.php', {
								    method: 'post',
								  }).then(function(res) {
								    return res.json();
								  }).then(function(data) {
								    return data.id; // Use the same key name for order ID on the client and server
								  });
						},
				    onApprove: function(data, actions) {
      						return actions.order.capture().then(function(details) {
								        return fetch('../../controlador/InputControladores/paypal-transaction-complete.php', {
									          method: 'post',
									          headers: {
									            'content-type': 'application/json'
									          },
									          body: JSON.stringify({
									            orderID: data.orderID,
									            id_tono:<?= filter_input(INPUT_GET,'ident',FILTER_SANITIZE_SPECIAL_CHARS);?>
									          })
								        }).then(function (res){
														return res.json();
												}).then(function (data){
													modalPagosDownload($('.box-modal'),$('.modal-info'),data.url_descarga);
													window.location.href=data.url_descarga;
												});

				      		});
				    }
  			}).render('.metodo_pago');

	</script>
<?php endif; ?>
<?=GroupVistas::loadScriptsFooter();?>
