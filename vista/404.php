<!DOCTYPE html>
<html lang="es" dir="ltr">
	<?php require __DIR__.'/../dependencias.php'; ?>

	<body>
		<div id="containergeneral" class="notranslate"  >
		 <?php include __DIR__.'/header.php'; ?>
			<main class="main">
				<section class="box portada">
					<div class="content-central">
						<div class="slogan">
							<span>Bienvenido</span>
							<span style="font-size: 40px; "> a <?=Funciones::tituloPlataforma(); ?></span>
						</div>
						<div class="buttPublicaTono">
			        			<a href="PanelControl/control.php" class="activemor">Publica un tono de llamada</a>
						</div>
					</div>
				</section>

        <section class="error_page_not_found">
          <span>Error 404</span>
          <h3>pagina web no encontrada</h3>
        </section>

			</main>
		<?php include __DIR__.'/footer.php'; ?>
		</div><!--FIN CONTAINER GENERAL-->





		</body>
	</html>
