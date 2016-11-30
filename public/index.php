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

                  if (isset($_POST['scale']) and
                   $_POST['scale'] == 'year') {
                     $results = $db->query('SELECT * FROM measurements WHERE ' .
                      'timestamp > strftime(\'%s\',\'now\',\'-1 year\') AND ' .
                      'timestamp < strftime(\'%s\', \'now\') AND ' .
                      'rowid % 105 = 0;');
                  } elseif (isset($_POST['scale']) and
                   $_POST['scale'] == '6month') {
                     $results = $db->query('SELECT * FROM measurements WHERE ' .
                      'timestamp > strftime(\'%s\',\'now\',\'-6 month\') AND ' .
                      'timestamp < strftime(\'%s\', \'now\') AND ' .
                      'rowid % 51 = 0;');
                  } elseif (isset($_POST['scale']) and
                   $_POST['scale'] == '3month') {
                     $results = $db->query('SELECT * FROM measurements WHERE ' .
                      'timestamp > strftime(\'%s\',\'now\',\'-3 month\') AND ' .
                      'timestamp < strftime(\'%s\', \'now\') AND ' .
                      'rowid % 25 = 0;');
                  } elseif (isset($_POST['scale']) and
                   $_POST['scale'] == 'month') {
                     $results = $db->query('SELECT * FROM measurements WHERE ' .
                      'timestamp > strftime(\'%s\',\'now\',\'-1 month\') AND ' .
                      'timestamp < strftime(\'%s\', \'now\') AND ' .
                      'rowid % 8 = 0;');
                  } elseif (isset($_POST['scale']) and
                   $_POST['scale'] == 'week') {
                     $results = $db->query('SELECT * FROM measurements WHERE ' .
                      'timestamp > strftime(\'%s\',\'now\',\'-7 day\') AND ' .
                      'timestamp < strftime(\'%s\', \'now\') AND ' .
                      'rowid % 2 = 0;');
                  } else {
                     $results = $db->query('SELECT * FROM measurements WHERE ' .
                      'timestamp > strftime(\'%s\',\'now\',\'-1 day\') AND ' .
                      'timestamp < strftime(\'%s\', \'now\');');
                  }

                  while ($row = $results->fetchArray())
                     echo '[new Date(', $row[0] * 1000, '), ', $row[1], '],';
               ?>
            ]);
            var options = {
               title: 'Historical Moisture Measurements',
               legend: {
                  position: 'none'
               },
               vAxis: {
                  title: '% Moisture',
                  maxValue: 100,
                  minValue: 0
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
               <td>Scale:</td>
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
         </table>
      </form>
   </body>
</html>
