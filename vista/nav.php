<nav class="menu" >
	<div id="icono-menu-publico" onclick="this.classList.toggle('cambiar');
	document.getElementById('menu').classList.toggle('show_menu');">
			<div class="colm b1"></div>
			<div class="colm b2"></div>
			<div class="colm b3"></div>
	</div>
		<div class="logo"><a href="<?=Funciones::configuracion('url_sitio');?>">
			<figure>
				<img src="/imagenes/predeterminado/logov6s.png" alt="">
			</figure> </a>
		</div>
		<div class="nav-publico" id="menu" >
			<ul class="despliega">
				<li><a  href="<?= RUTAABSOULTA.'/index.php';?>" class="activa"><i class="fas fa-home"></i> inicio</a></li>
				<li><a  href="<?= RUTAABSOULTA.'/PanelControl/control.php';?>" class="activa"><i class="fas fa-sign-in-alt"></i> login</a></li>
			</ul>
		</div>

</nav>
