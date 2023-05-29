<?php
$usuario=$classUsuario->getUsuario();//obtiene datos en array del usuario logeado actual
?>

<div id='nav_panel_control' >
	<div id="icono-menu" onclick="this.classList.toggle('cambiar');
	document.getElementById('menu').classList.toggle('show_menu');">
			<div class="colm b1"></div>
			<div class="colm b2"></div>
			<div class="colm b3"></div>
	</div>
	<div id='contenedor_avatar'>
		<figure>
			<img id="img_avatar" src="/<?=$usuario['imagen_avatar'];?>" >
		</figure>
	</div>
	<div class="icon_mobile_notificaciones">
		<a href="#" onclick="document.getElementById('box_notificaciones').classList.toggle('show_notificaciones');"> <i class="fas fa-inbox" ></i>
			<?=Funciones::countNotificaciones() ?>
		</a>
	</div>
	<div class="box_nav">  <?php // IDEA: este div se utilizara para crear un efecto de sombra cuando se habra el menu ?>
		<nav class="nav_wrap" id="menu">
			<ul class="nav_ul">
				<li>
					<a href="#" class="icon_notificaciones" onclick="document.getElementById('box_notificaciones').classList.toggle('show_notificaciones');"> <i class="fas fa-inbox" ></i>
						<?=Funciones::countNotificaciones() ?>
					</a>
					<div class="box_content_notificaciones"  id="box_notificaciones">
						<div class="decoration_box_notificaciones">
						</div>
						<div class="box_notificaciones">
							<ul class="nav_notificaciones">
							<?=Funciones::loadNotificacionesDeNav() ?>
							</ul>
						</div>
				</div>
				</li>
				<li><a href="<?=Funciones::inicio('');?>"><i class="fas fa-home"></i> Inicio</a></li><!--este menu es un poco inecesario creo que lo quitare-->
				<li><a href="<?=Funciones::salir('');?>"><i class="fas fa-sign-out-alt"></i> Salir</a></li>
			</ul>
		</nav>
	</div>
</div>
