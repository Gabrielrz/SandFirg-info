
$(document).ready(function(){

//consulta para obtener las estdisticas y datos de venta
  var ajax_url='../../controlador/OutputControladores/C_Estadisticas.php';
  var ventasPorMes;
  var ventasPorTono;
    $.ajax({

        url:ajax_url,
        type: "POST",
        contentType: false,
        cache: false,
        processData:false,
        dataType:"json",

        error: function(jqXHR,textStatus,errorThrown){
            $('body').html(jqXHR.responseText);
      },
      success:function(respuesta){
          console.log("las ventas por cada mes son:"+respuesta.ventasPorMes);
          // console.log("las ventas por tono:"+respuesta.ventasPorTono[0]['titulo']);
          // console.log("las ventas por tono:"+respuesta.ventasPorTono[1]['titulo']);
          // console.log("las ventas por tono:"+respuesta.ventasPorTono[2]['titulo']);
          // console.log("las ventas por tono:"+respuesta.ventasPorTono[3]['titulo']);
          // console.log("las ventas por tono:"+respuesta.ventasPorTono[4]['titulo']);
          ventasPorMes=respuesta.ventasPorMes;


          var ctxL = document.getElementById("lineChart").getContext('2d');
          var gradientFill = ctxL.createLinearGradient(72, 233, 144, 1);
          gradientFill.addColorStop(0, "#48e990");
          gradientFill.addColorStop(1, "#48e990");
          var myLineChart = new Chart(ctxL, {
          type: 'line',
          data: {
            labels: [
                     "Enero", "Febrero", "Marzo",
                     "Abril", "Mayo", "Junio",
                     "Julio","Agosto","Septiembre",
                     "Octubre","Noviembre","Diciembre"
                   ],
            datasets: [
              {
                label: "Datos de las ventas de cada mes",
                data: ventasPorMes,
                backgroundColor: gradientFill,
                borderColor: [
                  '#48e990',
                ],
                borderWidth: 2,
                pointBorderColor: "#fff",
                pointBackgroundColor: "rgba(173, 53, 186, 0.1)",
              }
            ]
          },
          options: {
            responsive: true
          }
          });


        //informacion de estadisticas
        for(var i=0;i<respuesta.ventasPorTono.length;i++){
              var tr=$('<tr></tr>');
                tr.append('<th scope="row">'+(i+1)+'</th>');
                tr.append('<td >'+respuesta.ventasPorTono[i]['titulo']+'</td>');
                tr.append('<td >'+respuesta.ventasPorTono[i]['cantidad']+'</td>');
                tr.append('<td >'+respuesta.ventasPorTono[i]['mes']+'</td>');
             $('#tBodyEstadisticas').append(tr);
           }
      }


    });



});
