<!DOCTYPE html>
<html lang="es" dir="ltr">
	<?php require __DIR__.'/../../dependencias.php';
	require_once(__DIR__.'/../../controlador/OutputControladores/C_inicio.php');

	 ?>



	<body>
		<div id="containergeneral" class="notranslate"  >
		 <?php include __DIR__.'/../header.php'; ?>
			<main class="main">
				<section class="box portada">
					<div class="content-central">
						<div class="slogan">
							<span>Bienvenido</span>
							<span style="font-size: 40px; "> a <?=Funciones::tituloPlataforma(); ?></span>
						</div>

					</div>
				</section>
				<div class="buttPublicaTono">
								<a href="PanelControl/control.php" class="activemor">Publica un tono de llamada</a>
				</div>
				<!-- <div id="separador">
					<div class="separador_recurso">
					</div>
				</div> -->
				<section class="box secundario" id="boxSecundario">
					<?php require_once(__DIR__."/../producto.php");	?>
					<!-- <div class="paginacion">
						<ul class="paginacion-ul">
							<a href="#">«</a>
							<a class="activo" href="#">1</a>
							<a href="#">2</a>
							<a href="#">3</a>
							<a href="#">4</a>
							<a href="#">5</a>
							<a href="#">6</a>
							<a href="#">»</a>
						</ul>
					</div> -->
				</section>

			</main>
		<?php include __DIR__.'/../footer.php'; ?>
		</div><!--FIN CONTAINER GENERAL-->





		</body>
	</html>
