	<?=GroupVistas::loadScriptsFooter();?>
    <footer class="footer home" >
	    <section id="mediocontacto">
	    </section>
        <div id="control_cajas">
            <div class="caja redes">
                <h3 class="titulos_footer">Redes Sociales</h3>
                <ul>
                    <li><a href="<?=Funciones::configuracion('url_facebook'); ?>"><i class="fab fa-facebook facebook1 iconFab"></i></a></li>
                    <li><a href="<?=Funciones::configuracion('url_instagram'); ?>"><i class="fab fa-instagram instagram1 iconFab"></i></a></li>
                    <li><a href="<?=Funciones::configuracion('url_youtube'); ?>"><i class="fab fa-youtube youtube1 iconFab"></i></a></li>
                </ul>
            </div>
            <div class="caja mensiones">
                <h3 class="footer_descripciones"><?=date('Y'); ?> Ringtones. Copyright © todos los derechos cedidos</h3>
								<h3><a href="https://www.iubenda.com/privacy-policy/37351130" class="iubenda-white iubenda-noiframe iubenda-embed iubenda-noiframe " title="Política de Privacidad ">Política de Privacidad</a><script type="text/javascript">(function (w,d) {var loader = function () {var s = d.createElement("script"), tag = d.getElementsByTagName("script")[0]; s.src="https://cdn.iubenda.com/iubenda.js"; tag.parentNode.insertBefore(s,tag);}; if(w.addEventListener){w.addEventListener("load", loader, false);}else if(w.attachEvent){w.attachEvent("onload", loader);}else{w.onload = loader;}})(window, document);</script></h3>
            </div>
            <div class="caja info">
              <h3 class="titulos_footer">informacion</h3>
              <ul>
                  <li><?=Funciones::configuracion('correo_contacto'); ?></li>
                  <li><?=Funciones::configuracion('telefono_contacto'); ?></li>
              </ul>
            </div>
            <!-- <div class="caja_logo">
                <figure class="logo">
                </figure>
                <span class="desc_logo">desarrollado por MR.Tones</span>
            </div> -->
        </div>
    </footer>
<!-- DEPENDENCIAS SCRIPTS -->
