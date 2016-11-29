<!DOCTYPE html>

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
                  $db = new SQLite3('../data.sqlite3');

                  $results = $db->query('SELECT * FROM measurements WHERE ' .
                   'rowid % 10000000 = 0');
                  while ($row = $results->fetchArray())
                     echo '[new Date(', $row[0] * 1000, '), ', $row[1], '],';
               ?>
            ]);
            var options = {
               title: 'Moisture Measurements',
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
      <form action="" method="POST">
         <table style="width: 70%; margin: auto">
            <tr>
               <td>change scale:</td>
               <td style="text-align: center">
                  <button name="scale" value="day">Today</button>
               </td>
               <td style="text-align: center">
                  <button name="scale" value="week">This Week</button>
               </td>
               <td style="text-align: center">
                  <button name="scale" value="month">This Month</button>
               </td>
               <td style="text-align: center">
                  <button name="scale" value="3month">3 Months</button>
               </td>
               <td style="text-align: center">
                  <button name="scale" value="6month">6 Months</button>
               </td>
               <td style="text-align: center">
                  <button name="scale" value="year">This Year</button>
               </td>
            </tr>
            <tr>
               <td>current scale: <?php echo $_POST['scale'] ?></td>
            </tr>
         </table>
      </form>
   </body>
</html>
