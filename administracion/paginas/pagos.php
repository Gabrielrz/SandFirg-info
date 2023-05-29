<?php
   include __DIR__.'/../../dependencias.php';
   include __DIR__.'/../header.php';
   require __DIR__.'/../../controlador/OutputControladores/C_Pagos.php';
 ?>
<body>
          <section class="main">
            <div class="contenedor_pagos">
              <span class="titulo-pagina">Pagos</span>
              <aside class="box_organizado left">
                <div class="form busqueda">
                  <form action="<?php echo $_SERVER['PHP_SELF']; ?>"  method="post">
                      <section class="seccion uno">
                        <label for="inp_comision">Comision Aplicada:</label>
                        <input type="text" name="inp_comision" id="inp_comision" value="10%">
                      </section>
                      <section class="seccion dos">
                        <label for="inp_comision">fecha de pagos:</label>
                        <input type="date" name="inp_comision" id="inp_comision" value="<?=$c_pagos->getFechaPago();?>">
                      </section>
                      <section class="seccion btn-aside">
                        <button type="button" name="button">corregir/editar</button>
                      </section>
                      <section class="seccion tres">
                        <label for="inp_comision">Total recaudado:</label>
                        <input type="text" name="inp_comision" id="inp_comision" value="2.000$">
                      </section>
                      <section class="seccion cuatro">
                        <label for="inp_comision">Saldo en cuenta:</label>
                        <input type="text" name="inp_comision" id="inp_comision" value="20.000$">
                      </section>
                      <section class="seccion btn-aside">
                        <button type="button" name="button">imprimir</button>
                      </section>

                  </form>
                </div>
              </aside>
              <div class="box_organizado right">
               <div class="table-wrapper">
                 <form  action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                         <div class="table-title">
                             <div class="row">

                                 <div class="col-sm-5s">
                                     <div class="grupo-btns" data-toggle="buttons">
                                         <label class="btn groupA">
                                             <input type="radio" hidden='true' name="status" value="all" checked="checked"> todos
                                         </label>
                                         <label class="btn groupA">
                                             <input type="radio" hidden='true'  name="status" value="active"> pagados
                                         </label>
                                         <label class="btn groupA">
                                             <input type="radio"  hidden='true' name="status" value="inactive"> no pagados
                                         </label>
                                     </div>
                                 </div>
                                 <div class="col-sm-5">
                                  <a href="#verificacion-modal" class="btn btn-pagar" data-toggle="modal"><i class="material-icons">&#xE147;</i> <span>Pagar a todos</span></a>
                                  <div id="respuestas">
                                  </div>
                                </div>
                             </div>
                         </div>
                  </form>
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
                               <th>Nombre</th>
                               <th>ventas este mes</th>
                               <th>cuenta</th>
                               <th>opciones</th>
                           </tr>
                       </thead>
                       <tbody>
                         <?php
                         foreach ($datosV as $datos) {
                          ?>
                           <tr data-status="active">
                               <td>
                                 <span class="custom-checkbox">
                                   <input type="checkbox" id="checkbox1" name="options[]" value="1">
                                   <label for="checkbox1"></label>
                                 </span>
                               </td>
                               <td><?=$datos['id_autor']; ?></td>
                               <td><a href="#"><?=$datos['nombre_autor']?></a></td>
                               <td><?=$datos['ventas_este_mes'];?></td>
                               <td><?=$datos['cuenta_de_pago'];?></td>
                               <td><a data="<?=$datos['id_autor']; ?>" class="btn btn-sm manage">gestionar</a></td>
                           </tr>
                      <?php } ?>
                       </tbody>
                   </table>
               </div>
              </div>
          </div>
          </section>


          <section id="content-modal" class="gestionarUsuario">
              <div class="box-modal">
                  <span class="cerrar"><button class="modal-close">X</button></span>
                  <div class="box-modal-contenido screenOne">
                      <div class="contenedor-datos">
                        <form class="form_selecciona_mes"  method="post">
                          <section class="colForm centrado">
                            <div class="rowForm">
                              <span class="text-Info posicioncenterH">Selecciona el mes de referencia</span>
                              <select class="selector_mes" name="selector_mes">
                                <option value="current">Mes Actual</option>
                                <option value="1">enero</option>
                                <option value="2">febrero</option>
                                <option value="3">marzo</option>
                                <option value="4">abril</option>
                                <option value="5">mayo</option>
                                <option value="6">junio </option>
                                <option value="7">julio</option>
                                <option value="8">agosto</option>
                                <option value="9">septiembre</option>
                                <option value="10">octubre</option>
                                <option value="11">noviembre</option>
                                <option value="12">diciembre</option>
                              </select>
                            </div>
                            <div class="rowForm">
                              <input type="hidden" name="id_autor">
                              <button type="button" data=""  name="btn_continuar_dos" class="btn_de_accion posicioncenterH close_modal continue_A">continuar</button>
                            </div>
                            <div class="rowForm">
                              <div class="contenedor-tabla">
                                <div class="titulo-azul">
                                  <span>Pagos realizados</span>
                                </div>
                                <div class="subcontendor-tabla">
                                    <table class="table table-striped table-head-fixed table-hover tablaUsIndividual">
                                      <thead>
                                          <tr>
                                              <th>#</th>
                                              <th>fecha</th>
                                              <th>cantidad</th>
                                              <th>Comision</th>
                                              <th>Estado</th>
                                          </tr>
                                      </thead>
                                      <tbody>
                                      </tbody>
                                    </table>
                                </div>
                              </div>
                            </div>
                            <div class="rowForm">
                              <div class="respuesta">
                                <span class="mensaje"></span>
                              </div>
                            </div>
                          </section>
                        </form>
                      </div>
                  </div>

                  <div class="box-modal-contenido screenTwo">
                    <div class="titulo-modal">
                      <span class="nombre_set_modal">Nombre</span>
                    </div>
                    <div class="contenedor-datos">
                      <form class="form-datos-Usuario" action="" method="">
                        <section class="colForm uno">
                          <span class="titulo-separador">Datos totales globales</span>
                          <div class="rowForm">
                            <label for="">Total Pagado:</label>
                            <input type="text" name="inp_totalPagoU" value="">
                          </div>
                          <div class="rowForm">
                            <label for="">Comision total:</label>
                            <input type="text" name="inp_TotalComisionU" value="">
                          </div>
                          <span class="titulo-separador">Datos totales de este mes</span>
                          <div class="rowForm">
                            <label for="">Total vendido</label>
                            <input type="text" name="inp_totalVendidoU" value="">
                          </div>
                          <div class="rowForm">
                            <label for="">Comision Aplicada</label>
                            <input type="text" name="inp_comisionAplicadaU" value="">
                          </div>
                        </section>
                        <section class="colForm dos">
                            <button type="button" name="btn_pagar_ahora" class="btn_de_accion posicionCenterV continue_B">pagar ahora</button>
                        </section>
                      </form>
                    </div>

                  </div>

                  <div class="box-modal-contenido screenTree">
                    <div class="titulo-modal">
                      <span>Datos actuales</span>
                    </div>
                    <div class="contenedor-datos">
                        <form class="form-pagar-usuario" id="formIndividual"  method="POST">
                        <section class="colForm uno">

                          <div class="rowForm">
                            <label for="">total en bruto vendido:</label>
                            <input type="text" name="inp_totalBrutoV" value="">
                            <input type="text" name="inp_totalTransformTonos" value="">
                          </div>
                          <div class="rowForm">
                            <label for="">Comision Aplicada:</label>
                            <input type="text" name="inp_comisionAplicadaEdi" value="">
                            <input type="text" name="inp_comisionAplicadaEdiTranform" value="">
                          </div>
                          <div class="rowForm">
                            <label for="">Fecha actual:</label>
                            <input type="date"  name="inp_fechaActual" value="">
                          </div>
                          <div class="rowForm">
                            <label for="">Total a Pagar</label>
                            <input type="text" name="inp_TotalAPagar" value="">
                          </div>
                        </section>
                        <section class="colForm dos">
                          <button type="button" name="button" class="btn_de_accion posicionCenterV">Editar</button>
                        </section>
                        <section class="colForm tres">
                          <div class="rowForm">
                            <span class="text-Info posicioncenterH">Introduzca la contraseña para verificacion</span>
                            <input type="password" name="inp_password_verif" class="inp_password_verif posicioncenterH" value="123456789">
                          </div>
                          <div class="rowForm">
                            <input type="hidden" name="id_autor" >
                            <button type="button" name="btn_enviar_pago" id="e_p_i" class="btn_de_accion posicioncenterH close_modal">enviar pago</button>
                          </div>
                        </section>
                      </form>
                    </div>
                  </div>

              </div>

          </section>


          <section id="content-modal" class="gestionGlobal">
              <div class="box-modal">
                <span class="cerrar"><button class="modal-close">X</button></span>
                <div class="box-modal-contenido screenOne">
                  <div class="titulo-modal">
                    <span>Pago Global</span>
                  </div>
                  <div class="contenedor-datos">
                    <form class="form-datos-Usuario" action="" method="">
                      <section class="colForm uno">
                        <span class="titulo-separador">Datos totales globales</span>
                        <div class="rowForm">
                          <label for="">Total en bruto:</label>
                          <input type="text" name="inp_TBP_id3" value="240.00$">
                        </div>
                        <div class="rowForm">
                          <label for="">Comision Recaudada:</label>
                          <input type="text" name="inp_CA_id3" value="10%">
                          <input type="text" name="inp_TU_id3" value="27.00$">
                        </div>

                        <div class="rowForm">
                          <label for="">Fecha actual:</label>
                          <input type="date"  name="inp_FA_id3" value="2020-03-02">
                        </div>
                        <div class="rowForm">
                          <label for="">Total a pagar:</label>
                          <input type="text"  name="inp_TP_id3" value="100.00$">
                        </div>
                      </section>
                      <section class="colForm dos">
                        <button type="button" name="button" class="btn_de_accion posicionCenterV">Editar</button>
                        <button type="button" class="btn_de_accion posicionCenterV continue">pagar ahora</button>
                      </section>
                    </form>
                  </div>
                  <div class="contenedor-tabla">
                    <div class="titulo-azul">
                      <span>Usuarios a pagar:</span>
                        <input name="inp_info_UP" class="inp_ifo_users" value="25" disabled></input>
                    </div>
                    <div class="subcontendor-tabla">
                        <table class="table table-striped table-head-fixed table-hover">
                          <thead>
                              <tr>
                                  <th>#</th>
                                  <th>Comision(10%)</th>
                                  <th>usuario</th>
                                  <th>vendido</th>
                                  <th>cantidad</th>
                              </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td>1</td>
                              <td>13.00$</td>
                              <td>mercu@paypal.com</td>
                              <td>100.00$</td>
                              <td>97</td>
                            </tr>
                            <tr>
                              <td>2</td>
                              <td>13.00$</td>
                              <td>mercu@paypal.com</td>
                              <td>100.00$</td>
                              <td>97</td>
                            </tr>
                            <tr>
                              <td>3</td>
                              <td>13.00$</td>
                              <td>mercu@paypal.com</td>
                              <td>100.00$</td>
                              <td>97</td>
                            </tr>
                          </tbody>
                        </table>
                    </div>
                  </div>
                </div>
                <div class="box-modal-contenido screenTwo">
                  <div class="titulo-modal">
                    <span>confirmar pago global</span>
                  </div>
                  <div class="contenedor-datos">
                    <form class="form-pagar-usuario" action="form-confirma-pass-global" method="">
                      <section class="colForm tres">
                        <div class="rowForm">
                          <span class="text-Info posicioncenterH">Introduzca la contraseña para verificacion</span>
                          <input type="password" name="inp_password_verif" class="inp_password_verif posicioncenterH" value="123456789">
                        </div>
                        <div class="rowForm">
                          <button type="button" name="btn_enviar_pago" id="e_p_g" class="btn_de_accion posicioncenterH">enviar pago</button>
                        </div>
                      </section>
                    </form>
                  </div>
                </div>
                <div class="mensajes">
                    <div class="mensaje"></div>
                </div>
              </div>

          </section>

	<?=GroupVistas::loadScriptsFooter();?>
</body>
