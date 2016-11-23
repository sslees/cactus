<!--
File: index.php
Author: Samuel Lees (sslees)
Date: 11/22/16
Class: CPE 458-01
Assignment: Final Project
-->

<html>
   <head>
      <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
      <script type="text/javascript">
         google.charts.load('current', {'packages':['corechart']});
         google.charts.setOnLoadCallback(drawChart);

         function drawChart() {
            var data = new google.visualization.arrayToDataTable([
               ['Time', 'Measurement'],
               <?php
                  $db = new SQLite3('../test.db');

                  $results = $db->query('SELECT * FROM raw_data');
                  while ($row = $results->fetchArray())
                     echo '[\'', date('r', $row[0]), '\', ', $row[1], '],';
               ?>
            ]);
            var options = {'title':'Measurements'};
            var chart = new google.visualization.LineChart(document.getElementById('chart_div'));

            chart.draw(data, options);
         }
      </script>
   </head>
   <body>
      <div id="chart_div"></div>
   </body>
</html>
