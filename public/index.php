<!--
File: index.php
Author: Samuel Lees (sslees)
Date: 11/22/16
Class: CPE 458-01
Assignment: Final Project
References:
   http://raspberrywebserver.com/cgiscripting/rpi-temperature-logger/
    building-an-sqlite-temperature-logger.html
   https://google-developers.appspot.com/chart/interactive/docs/
-->

<html>
   <head>
      <script type="text/javascript"
       src="https://www.gstatic.com/charts/loader.js"></script>
      <script type="text/javascript">
         google.charts.load('current', {'packages':['corechart']});
         google.charts.setOnLoadCallback(drawChart);

         function drawChart() {
            var data = new google.visualization.arrayToDataTable([
               ['Time', '% Moisture'],
               <?php
                  $db = new SQLite3('../test.db');

                  $results = $db->query('SELECT * FROM measurements');
                  while ($row = $results->fetchArray())
                     echo '[new Date(', $row[0] * 1000, '), ', $row[1], '],';
               ?>
            ]);
            var options = {
               title: 'Moisture Measurements',
               animation: {
                  duration: 1000,
                  easing: 'out',
                  startup: true
               },
               vAxis: {
                  minValue: 0,
                  maxValue: 100
               }
            };
            var chart = new google.visualization.AreaChart(document.getElementById('chart_div'));

            chart.draw(data, options);
         }
      </script>
   </head>
   <body>
      <div id="chart_div"></div>
   </body>
</html>
