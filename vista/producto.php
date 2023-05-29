<?php
/*crear un nuevo documento para la relacion entre documentos con require*/
require_once __DIR__.'/../controlador/OutputControladores/C_Productos.php';
?>
<div id="contenedor_productos">
  <?php
   $datos=C_Productos::getDatos();
   if(isset($datos)){
      foreach($datos['items'] as $tono):?>

            <div class="box_producto" >
              <div class="producto">
                    <div class="conjunto_logrep">
                        <figure class="portada_sonido">
                            <img src="<?php echo $tono['imagen'];?>" class="logo_producto" alt="no se encontro la imagen" >
                        </figure>

                        <div class="estilo_reproductor" >
                            <div class="caja_interna">
                                <audio  src=""   controlslist="nodownload" preload="auto" class="myaudio" data_id="<?=$tono['id'];?>" >su navegador no esta adaptado para reproducir este tipo de audios</audio>
                                <button  class="bton_play" onclick="controlAudio(this,<?=$tono['id']?>,false)"><i class="fas fa-play"></i></button>
                                <button  class="bton_pause" onclick="controlAudio(this,<?=$tono['id']?>,true)"><i class="fas fa-pause"></i></button>
                            </div>
                        </div>
                    </div>
                    <i class="fas fa-eye butt"  id="<?=$tono['id'];?>"></i>
                    <div class="container"><!--contenedor de solo descripciones y datos -->
                         <h3 class="titl_rmx"><?=$tono['titulo'];?></h3>
                         <p class="desc_rmx"><?=$tono['descripcion'];?></p>
                    </div>
                    <div class="container_aside"><!-- contenedor de tiendas y compras -->
                      <?php foreach ($tono['botonesDeProducto'] as $boton): ?>
                            <?=$boton;?>
                      <?php endforeach; ?>
                    </div>
              </div>
            </div>
    <?php endforeach; ?>
<?php } ?>
</div>
<div id="more_productos"></div>
