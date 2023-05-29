<?php
require_once __DIR__.'/../../controlador/OutputControladores/C_misTonos.php';
require_once __DIR__.'/../header.php';
?>



	<div id="contenedor_principal">

		<div id="contenedor_sonidos">

			<!--aqui va el bucle para rescatar la informacion de los tonos-->
			<?php
				foreach($c_o_sonido->rs['mis_tonos'] as $dSonido) {

				?>

							<div class="caja_sonido">
								<div class="zona titulo">
									<?='<p>'.$dSonido['titulo'].'</p>';?>
								</div>
								<figure class="zona imagen">
									<img src="<?php echo RUTAABSOULTA.'/'.$dSonido['imagen']; ?>" class="imagen_sonido">
								</figure>
								<div>
									<span class="zona opc_venta activo_<?=(boolval($dSonido['activado']))?'true':'false';?>">Activo:<?=(boolval($dSonido['activado']))?'si':'no';?></span>
								</div>

								<div class="zona btnes_t">
									<input type="hidden" name="hidden_rastro">
									<!--<a  href=""><?php echo $dSonido['id'];?>	</a>-->
									<a class="btn_editar" href="<?='editarTono.php?ident='.$dSonido["id"];?>">editar</a>
									<button class="btn_eliminar" data="<?=$dSonido["id"]; ?>">Eliminar</button>

								</div>

							</div>
				<?php
				}
			?>

		</div>


	</div>


	<div id="dialog_del_sonido" class="dialog error_modal" title="Error" data-del="">
		<p>estas a punto de eliminar el articulo seleccionado</p>
		<p>est accion no se podra rectificar</p>

		<span>¿Estas seguro.?</span>
	</div>





<?php
	GroupVistas::loadScriptsFooter();
Funciones::back();

?>
