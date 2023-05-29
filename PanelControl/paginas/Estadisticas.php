<?php
include('../header.php');
?>

<!-- en esta pagina deberian visualizarse cuantas veces se ha vendido cada sonido en cada mes-->
  <div id="contenedor_principal">
    <div class="contenedor_estadisticas">
      <div class="info_Estadisticas">
       <table class="table table-dark">
         <thead>
           <tr>
             <th scope="col">#</th>
             <th scope="col">Tono</th>
             <th scope="col">Ventas</th>
             <th scope="col">mes</th>
           </tr>
         </thead>
         <tbody id="tBodyEstadisticas">
         </tbody>
       </table>
      </div>
      <div class="graficas_l">
        <canvas id="lineChart"></canvas>
      </div>
    </div>



  </div>


<?php
  Funciones::back();
  Funciones::addScriptsEstadisticasFooter();
?>
