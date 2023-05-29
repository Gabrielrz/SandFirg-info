<?php
include __DIR__.'/../../dependencias.php';
include __DIR__.'/../header.php';
 ?>
 <body>
           <section class="main">
             <div class="contenedor_principal">
               <section class="section_header">
                 <span class="titulo-pagina">Control de productos</span>

               </section>
               <section class="section_main">



                   <div class="box_organizado right">
                    <div class="table-wrapper">

                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                   <th>
                                       <span class="custom-checkbox">
                                         <input type="checkbox" id="selectAll">
                                         <label for="selectAll"></label>
                                       </span>
                                    </th>
                                    <th>#</th>
                                    <th>Nombre Sonido</th>
                                    <th>Status</th>
                                    <th>Usuario</th>
                                    <th>opciones</th>
                                </tr>
                            </thead>
                            <tbody>

                                <tr data-status="active">
                                    <td>
                                      <span class="custom-checkbox">
                                        <input type="checkbox" id="checkbox1" name="options[]" value="1">
                                        <label for="checkbox1"></label>
                                      </span>
                                    </td>
                                    <td>1</td>
                                    <td>marimba mask off</td>
                                    <td>desactivado</td>
                                    <td>@gmail.com</td>
                                    <td><a data="<?='enlace' ?>" class="btn btn-sm manage">gestionar</a></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                   </div>


               </section>
             </div>
           </section>



           <section id="content-modal" class="gestionarUsuario">
               <div class="box-modal">
                   <span class="cerrar"><button class="modal-close">X</button></span>
                   <div class="box-modal-contenido screenOne">
                       <div class="contenedor-datos">

                       </div>
                   </div>


                   

               </div>

           </section>

</body>
