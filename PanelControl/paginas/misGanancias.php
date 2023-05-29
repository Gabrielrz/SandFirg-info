<?php
include('../header.php');
require('../../controlador/OutputControladores/C_misGanancias.php');
?>

  <div id="contenedor_principal">
    <div class="contenedor_ganancias">
          <div class="primera_vista">
            <h2 class="lay_title">Ganancia total: </h2>
            <span class="lay_sub_title">total bruto:</span>

            <h3 class="lay_money"> <?=$rs['totalG'];?> <span>$</span> </h3>
            <span class="lay_sub_title">Sub total:</span>
            <h3 class="lay_money"><?=$rs['subTotal']; ?> <span>$</span></h3>

            <span class="lay_sub_title">Comision aplicada:</span>
            <h3 class="lay_money"><?=$rs['comision']; ?> <span>$</span></h3>

            <span class="lay_sub_title">precio de venta:</span>
            <h3 class="lay_money"><?=$rs['precio']; ?> <span>$</span></h3>
          </div>

        <div class="caja_ganancias" >
          <table class="table table-striped table-valign-middle table_ganancias">
            <tr>
              <th>mes</th>
              <th>ganancias</th>
              <th>estado de pago</th>
            </tr>
            <?php foreach ($outputGanancias->meses as $mes): ?>
                  <tr>
                    <td><?=$mes;?></td>
                    <td>
                      <?php if ($rs['totalGPM'][$mes]==false) {
                          echo '0';
                      }else {
                          echo $rs['totalGPM'][$mes];
                      } ?>
                    </td>
                    <td></td>
                  </tr>
            <?php endforeach; ?>
          </table>
        </div>
    </div>
</div>




<?php
Funciones::back();

?>
