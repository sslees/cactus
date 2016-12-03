<!DOCTYPE html>

<!--
File: index.php
Author: Samuel Lees (sslees) and Matthew Lindly (mlindly)
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
         google.charts.load('current', {'packages': ['corechart', 'gauge']});
         google.charts.setOnLoadCallback(drawHistoryChart);
         google.charts.setOnLoadCallback(drawMinimumChart);
         google.charts.setOnLoadCallback(drawCurrentChart);
         google.charts.setOnLoadCallback(drawMaximumChart);

         function drawMinimumChart() {
            var data = google.visualization.arrayToDataTable(
               [['Label', 'Value'], ['', <?php
                  $db = new SQLite3('../data.sqlite3');

                  echo $db->query('SELECT measurement FROM stats WHERE ' .
                   'parameter = \'minimum\';')->fetchArray()[0];
               ?>]]
            );
            var options = {
               height: 450,
               yellowFrom: 0, yellowTo: 30,
               greenFrom: 20, greenTo: 80, greenColor: '#009900',
               redFrom: 70, redTo: 100, redColor: '#0099FF',
               minorTicks: 5
            };
            var chart = new google.visualization.Gauge(
             document.getElementById('minimum_chart_div'));

            chart.draw(data, options);
         }

         function drawCurrentChart() {
            var data = google.visualization.arrayToDataTable(
               [['Label', 'Value'], ['', <?php
                  $db = new SQLite3('../data.sqlite3');

                  echo $db->query('SELECT measurement FROM stats WHERE ' .
                   'parameter = \'current\';')->fetchArray()[0];
               ?>]]
            );
            var options = {
               height: 450,
               yellowFrom: 0, yellowTo: 30,
               greenFrom: 20, greenTo: 80, greenColor: '#009900',
               redFrom: 70, redTo: 100, redColor: '#0099FF',
               minorTicks: 5
            };
            var chart = new google.visualization.Gauge(
             document.getElementById('current_chart_div'));

            chart.draw(data, options);
         }

         function drawMaximumChart() {
            var data = google.visualization.arrayToDataTable(
               [['Label', 'Value'], ['', <?php
                  $db = new SQLite3('../data.sqlite3');

                  echo $db->query('SELECT measurement FROM stats WHERE ' .
                   'parameter = \'maximum\';')->fetchArray()[0];
               ?>]]
            );
            var options = {
               height: 450,
               yellowFrom: 0, yellowTo: 30,
               greenFrom: 20, greenTo: 80, greenColor: '#009900',
               redFrom: 70, redTo: 100, redColor: '#0099FF',
               minorTicks: 5
            };
            var chart = new google.visualization.Gauge(
             document.getElementById('maximum_chart_div'));

            chart.draw(data, options);
         }

         function drawHistoryChart() {
            var data = new google.visualization.arrayToDataTable([
               ['Time', '% Moisture'],
               <?php
                  if (isset($_POST['scale']) and
                   $_POST['scale'] == 'year') {
                     $results = $db->query('SELECT * FROM measurements WHERE ' .
                      'timestamp > strftime(\'%s\',\'now\',\'-1 year\') AND ' .
                      'timestamp <= strftime(\'%s\', \'now\') AND ' .
                      'rowid % 525 = 0;');
                  } elseif (isset($_POST['scale']) and
                   $_POST['scale'] == 'month') {
                     $results = $db->query('SELECT * FROM measurements WHERE ' .
                      'timestamp > strftime(\'%s\',\'now\',\'-1 month\') AND ' .
                      'timestamp <= strftime(\'%s\', \'now\') AND ' .
                      'rowid % 160 = 0;');
                  } elseif (isset($_POST['scale']) and
                   $_POST['scale'] == 'week') {
                     $results = $db->query('SELECT * FROM measurements WHERE ' .
                      'timestamp > strftime(\'%s\',\'now\',\'-7 day\') AND ' .
                      'timestamp <= strftime(\'%s\', \'now\') AND ' .
                      'rowid % 40 = 0;');
                  } elseif (isset($_POST['scale']) and
                   $_POST['scale'] == 'day') {
                     $results = $db->query('SELECT * FROM measurements WHERE ' .
                      'timestamp > strftime(\'%s\',\'now\',\'-1 day\') AND ' .
                      'timestamp <= strftime(\'%s\', \'now\') AND ' .
                      'rowid % 5 = 0;');
                  } else {
                     $results = $db->query('SELECT * FROM measurements WHERE ' .
                      'timestamp > strftime(\'%s\',\'now\',\'-1 hour\') AND ' .
                      'timestamp <= strftime(\'%s\', \'now\');');
                  }

                  while ($row = $results->fetchArray())
                     echo '[new Date(', $row[0] * 1000, '), ', $row[1], '],';

                  $db->close();
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
            var chart = new google.visualization.AreaChart(
             document.getElementById('chart_div'));

            chart.draw(data, options);
         }
      </script>
   </head>
   <body>
      <table style="width: 60%; margin: auto">
         <tr>
            <td style="text-align: center; vertical-align: bottom; width: 25%">
               <div id="minimum_chart_div"></div>
            </td>
            <td style="text-align: center; vertical-align: bottom; width: 50%">
               <div id="current_chart_div"></div>
            </td>
            <td style="text-align: center; vertical-align: bottom; width: 25%">
               <div id="maximum_chart_div"></div>
            </td>
         </tr>
         <tr>
            <td style="text-align: center; font-family: arial; font-size: 2vw;
             vertical-align: top">24-Hr. Min.</td>
            <td style="text-align: center; font-family: arial; font-size: 4vw;
             vertical-align: top">Current</td>
            <td style="text-align: center; font-family: arial; font-size: 2vw;
             vertical-align: top">24-Hr. Max.</td>
         </tr>
      </table>
      <div id="chart_div"></div>
      <form action="" method="POST">
         <table style="width: 70%; margin: auto">
            <tr>
               <td>Scale:</td>
               <td style="text-align: center">
                  <button name="scale" value="hour">Past Hour</button>
               </td>
               <td style="text-align: center">
                  <button name="scale" value="day">Past Day</button>
               </td>
               <td style="text-align: center">
                  <button name="scale" value="week">Past Week</button>
               </td>
               <td style="text-align: center">
                  <button name="scale" value="month">Past Month</button>
               </td>
               <td style="text-align: center">
                  <button name="scale" value="year">Past Year</button>
               </td>
            </tr>
         </table>
      </form>
   </body>
</html>
