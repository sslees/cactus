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
      <!-- <meta http-equiv="refresh" content="5"> -->

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
                  $db = new PDO("mysql:host=localhost;dbname=cactus", "cactus",
                   "c@c7u$");

                  echo round($db->query('select 1023 - max(value) from measurements where timestamp >= date_sub(utc_timestamp, interval 1 day) and device = \'' . $_GET['device'] . '\' and channel = ' . $_GET['channel'] . ';')->fetch()[0] / 10.23, 2);

                  $db = null;
               ?>]]
            );
            var options = {
               height: 450,
               yellowFrom: 0, yellowTo: 30,
               greenFrom: 30, greenTo: 60, greenColor: '#009900',
               redFrom: 60, redTo: 100, redColor: '#0099FF', // actually blue
               minorTicks: 5
            };
            var chart = new google.visualization.Gauge(
             document.getElementById('minimum_chart_div'));

            chart.draw(data, options);
         }

         function drawCurrentChart() {
            var data = google.visualization.arrayToDataTable(
               [['Label', 'Value'], ['', <?php
                  $db = new PDO("mysql:host=localhost;dbname=cactus", "cactus",
                   "c@c7u$");

                  echo round($db->query('select 1023 - value from measurements where timestamp = (select max(timestamp) from measurements where device = \'' . $_GET['device'] . '\' and channel = ' . $_GET['channel'] . ') and device = \'' . $_GET['device'] . '\' and channel = ' . $_GET['channel'] . ';')->fetch()[0] / 10.23, 2);

                  $db = null;
               ?>]]
            );
            var options = {
               height: 450,
               yellowFrom: 0, yellowTo: 30,
               greenFrom: 30, greenTo: 60, greenColor: '#009900',
               redFrom: 60, redTo: 100, redColor: '#0099FF', // actually blue
               minorTicks: 5
            };
            var chart = new google.visualization.Gauge(
             document.getElementById('current_chart_div'));

            chart.draw(data, options);
         }

         function drawMaximumChart() {
            var data = google.visualization.arrayToDataTable(
               [['Label', 'Value'], ['', <?php
                  $db = new PDO("mysql:host=localhost;dbname=cactus", "cactus",
                   "c@c7u$");

                  echo round($db->query('select 1023 - min(value) from measurements where timestamp >= date_sub(utc_timestamp, interval 1 day) and device = \'' . $_GET['device'] . '\' and channel = ' . $_GET['channel'] . ';')->fetch()[0] / 10.23, 2);

                  $db = null;
               ?>]]
            );
            var options = {
               height: 450,
               yellowFrom: 0, yellowTo: 30,
               greenFrom: 30, greenTo: 60, greenColor: '#009900',
               redFrom: 60, redTo: 100, redColor: '#0099FF', // actually blue
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
                  $db = new PDO("mysql:host=localhost;dbname=cactus", "cactus",
                   "c@c7u$");

                  $db->exec('set @a = 0;');
                  if (isset($_POST['scale']) and
                   $_POST['scale'] == 'month') {
                     $results = $db->query('select year(timestamp), month(timestamp) - 1, day(timestamp), HOUR(timestamp), minute(timestamp), second(timestamp), 1023 - value from measurements where (@a := @a + 1) % 730 = 0 and device = \'' . $_GET['device'] . '\' and channel = ' . $_GET['channel'] . ' and timestamp >= date_sub(utc_timestamp, interval 1 month);');
                  } elseif (isset($_POST['scale']) and
                   $_POST['scale'] == 'week') {
                     $results = $db->query('select year(timestamp), month(timestamp) - 1, day(timestamp), HOUR(timestamp), minute(timestamp), second(timestamp), 1023 - value from measurements where (@a := @a + 1) % 168 = 0 and device = \'' . $_GET['device'] . '\' and channel = ' . $_GET['channel'] . ' and timestamp >= date_sub(utc_timestamp, interval 1 week);');
                  } elseif (isset($_POST['scale']) and
                   $_POST['scale'] == 'day') {
                     $results = $db->query('select year(timestamp), month(timestamp) - 1, day(timestamp), HOUR(timestamp), minute(timestamp), second(timestamp), 1023 - value from measurements where (@a := @a + 1) % 24 = 0 and device = \'' . $_GET['device'] . '\' and channel = ' . $_GET['channel'] . ' and timestamp >= date_sub(utc_timestamp, interval 1 day);');
                  } else {
                     $results = $db->query('select year(timestamp), month(timestamp) - 1, day(timestamp), HOUR(timestamp), minute(timestamp), second(timestamp), 1023 - value from measurements where device = \'' . $_GET['device'] . '\' and channel = ' . $_GET['channel'] . ' and timestamp >= date_sub(utc_timestamp, interval 1 hour);');
                  }

                  while ($row = $results->fetch())
                     echo '[new Date(Date.UTC(', $row[0], ', ', $row[1], ', ', $row[2], ', ', $row[3], ', ', $row[4], ', ', $row[5], ')), ', round($row[6] / 10.23, 2), '],';

                  $db = null;
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
            <td style="align-content: center; vertical-align: bottom;
             width: 25%">
               <div id="minimum_chart_div"></div>
            </td>
            <td style="align-content: center; vertical-align: bottom;
             width: 50%">
               <div id="current_chart_div"></div>
            </td>
            <td style="align-content: center; vertical-align: bottom;
             width: 25%">
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
            </tr>
         </table>
      </form>
   </body>
</html>
