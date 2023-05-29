<?php
include('../header.php');

require('../../controlador/OutputControladores/C_misCampanas.php');
?>

<!--Las campañas sirven para publicitar los sonidos en primera instancia en la propia pagina
(en el futuro creo que se puede añadir publicidad hacia otros sitios),se pondran en -->


<!--para el tema de la temporizacion se puede crear un controlador en el que se obtiene la fecha limite puesta de la base de datos
	y la fecha mundial o del servidor y lo unico que se hace es que cuando se entre en esta pagina en el controlador
	se resta la fecha mundial a la de la base de datos y luego se le muestra al usuario mediante un reloj en javascript-->

<div id="contenedor_principal">
	<!-- <p>las campañas sirven para posicionar en primera plana tus sonidos durante un determinado
	periodo de tiempo </p> -->


	<div id="contenedor_campanias">
		<span class="cmpn txt_info">Pagina en proceso de construccion<i class="fas fa-hard-hat"></i></span>

<!-- <?php if($campanaT['activado']==1){  ?>
		<div class="campania">

			<div class="wrap">
				<h1><?php echo $campanaT['titulo']?></h1>
			</div>
			<div class="wrap">
				<h1><?php echo $campanaT['descripcion']?></h1>
			</div>
			<div class="wrap">
				<h1>Precio:<?php echo $campanaT['precio']."$";?></h1>
			</div>
			<div class="wrap" id="tiempo">
				<h1>Tiempo de actividad</h1>
			</div>

			<div class="wrap btnCompra">
				<div class="contenedor_centro">
					<input type="button" name="btn_comprar_cam" id="btn_comprar_cam" value="comprar">
				</div>
			</div>
		</div>
<?php }?> -->

	</div>



</div>
<script type="text/javascript" src="../js/paginaCampanas.js">
</script>









<?php
Funciones::back();
?>
