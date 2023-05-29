<?php
include __DIR__.'/../../dependencias.php';
require __DIR__.'/../../controlador/OutputControladores/C_ajustes.php';
include __DIR__.'/../header.php';
 ?>
 <body>
           <section class="main">
             <div class="contenedor_principal">
               <section class="section_header">
                 <span class="titulo-pagina">ajustes</span>
                 <div class="box_btns_right">
                   <div class="box_position">
                     <div class="btns_right">
                       <button type="button" class="btn btn_blanco" id="btnEditarAjuste" name="button">Editar</button>
                       <button type="button" class="btn btn_blanco" id="btnDelAjuste" name="button">Borrar</button>
                       <button type="button" class="btn btn_blanco" id="btnAddAjuste" name="button">Añadir</button>
                     </div>
                   </div>
                 </div>
               </section>
               <section class="section_main">
                 <div class="box_ajustes">
                   <?php foreach($configuraciones as $configuracion){?>
                        <article class="box_ajuste">
                          <input class="inp_check" type="checkbox" name="inp_check" value="<?=$configuracion->id?>">
                          <span class="nombre_ajuste"><?=$configuracion->clave;?></span>
                          <input type="url" class="inp_ajuste" name="" value="<?=$configuracion->valor;?>">
                        </article>
                   <?php } ?>
                 </div>
               </section>
             </div>
           </section>

           <section id="content-modal" class="addAjuste">
               <div class="box-modal">
                 <span class="cerrar"><button class="modal-close">X</button></span>
                 <div class="box-modal-contenido screenOne">
                   <div class="titulo-modal">
                     <span>Añadir una configuracion</span>
                   </div>
                   <div class="contenedor-datos">
                     <form id="form_add_ajuste" class="form-configuraciones-add" method="POST">
                       <div class="row-ajuste">
                         <label for="nombre_config">Nombre de configuracion:</label>
                         <input type="text" name="nombre_config" value="">
                       </div>
                       <div class="row-ajuste">
                         <label for="inp_config">Ajustes:</label>
                         <input type="text" name="inp_config" value="">
                         <input type="hidden" name="accion" value="insert">
                       </div>
                     </form>
                   </div>
                   <div class="btn-modal-ajuste">
                     <button type="button" name="btngurdarAjuste" id="guardarAjuste" class="btnguardarAjuste">Crear</button>
                   </div>
                 </div>
                 <div class="mensajes">
                     <div class="mensaje"></div>
                 </div>
               </div>
           </section>





          <section id="content-modal" class="delAjuste">
               <div class="box-modal">
                 <span class="cerrar"><button class="modal-close">X</button></span>
                 <div class="box-modal-contenido screenOne">
                   <div class="titulo-modal">
                     <span>borrar configuracion</span>
                   </div>
                   <div class="contenedor-datos">
                     <form id="form_del_config" class="form-configuraciones-add" method="POST">
                       <div class="row-ajuste">
                         <label for="nombre_config">¿seguro que quieres borrar las configuraciones seleccionadas?</label>
                         <input type="hidden" name="checks_ids_config" class="checks_ids_config">
                         <input type="hidden" name="accion" value="delete">
                       </div>
                     </form>
                   </div>
                   <div class="btn-modal-ajuste">
                     <button type="button" name="btngurdarAjuste" id="borrarAjuste" class="btnguardarAjuste">confirmar</button>
                   </div>
                 </div>
                 <div class="mensajes">
                     <div class="mensaje"></div>
                 </div>
               </div>
          </section>




          <section id="content-modal" class="editAjuste">
              <div class="box-modal">
                <span class="cerrar"><button class="modal-close">X</button></span>
                <div class="box-modal-contenido screenOne">
                  <div class="titulo-modal">
                    <span>Editar una configuracion</span>
                  </div>
                  <div class="contenedor-datos">
                    <form id="form_ajustes_perfil" class="form-configuraciones-add" method="POST">
                      <div class="row-ajuste">
                        <label for="nombre_config">Nombre de configuracion:</label>
                        <input type="text" name="nombre_config" value="">
                      </div>
                      <div class="row-ajuste">
                        <label for="inp_config">Ajustes:</label>
                        <input type="text" name="inp_config" value="">
                      </div>
                    </form>
                  </div>
                  <div class="btn-modal-ajuste">
                    <button type="button" name="btngurdarAjuste" id="editarAjuste" class="btnguardarAjuste">guardar</button>
                  </div>
                </div>
                <div class="mensajes">
                    <div class="mensaje"></div>
                </div>
              </div>
          </section>
	<?=GroupVistas::loadScriptsFooter();?>
</body>
