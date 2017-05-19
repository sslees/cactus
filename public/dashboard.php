<!-- File: dashboard.php
Author: Matthew Lindly (mlindly)
Date: 4/5/17
Class: CPE 462-10 LAB
Assignment: Senior Project
References:
 https://www.w3schools.com/php/php_form_complete.asp
 https://www.w3schools.com/bootstrap/bootstrap_templates.asp -->

<!DOCTYPE HTML>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <link rel="stylesheet" href="phase2/assets/css/main.css" />
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<style>
.error {color: #FF0000;}

/* Full-width input fields */
input[type=text], input[type=password] {
    width: 100%;
    padding: 12px 20px;
    margin: 8px 0;
    display: inline-block;
    border: 1px solid #ccc;
    box-sizing: border-box;
}

/* Set a style for all buttons */
button {
    position: absolute;
    right: 0px;
    background-color: #4CAF50;
    color: white;
    padding: 0 18px;
    /*margin: 8px 0;*/
    border: none;
    cursor: pointer;
    width: 100%;
    height: 44px;
}

button:hover {
    opacity: 0.8;
}

/* Extra styles for the cancel button */
.gotosignupbtn {
    width: auto;
    padding: 10px 18px;
    background-color: #f44336;
}

.gotosigninbtn {
    width: auto;
    padding: 14px 20px;
    background-color: #f44336;
}

.gotoresetpasswordbtn {
    /*float:right;*/
/*    width: auto;
    padding: 10px 18px;
    background-color: #ffcc00;*/
}

.registerdevicebtn {
    position: relative;
    height: auto;
    padding: 14px 20px;
    margin: 8px 0;
}

/* Float cancel and signup buttons and add an equal width */
.gotosigninbtn,.signupbtn,.resetpasswordbtn {float:left;width:50%}

/* Center the image and position the close button */
/*.imgcontainer {
    text-align: center;
    margin: 24px 0 12px 0;
    position: relative;
}

img.avatar {
    width: 40%;
    border-radius: 50%;
}*/

.container2 {
    padding: 18px;
}

span.psw {
    float: right;
    padding-top: 16px;
}

/* The Modal (background) */
.modal {
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 1; /* Sit on top */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgb(0,0,0); /* Fallback color */
    background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
    padding-top: 60px;
}

/* Modal Content/Box */
.modal-content {
    background-color: #fefefe;
    margin: 5% auto 15% auto; /* 5% from the top, 15% from the bottom and centered */
    border: 1px solid #888;
    width: 80%; /* Could be more or less, depending on screen size */
}

/* The Close Button (x) */
.close {
    position: absolute;
    right: 25px;
    top: 0;
    color: #000;
    font-size: 35px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: red;
    cursor: pointer;
}

/* Clear floats */
.clearfix::after {
    content: "";
    clear: both;
    display: table;
}

/* Add Zoom Animation */
.animate {
    -webkit-animation: animatezoom 0.6s;
    animation: animatezoom 0.6s
}

@-webkit-keyframes animatezoom {
    from {-webkit-transform: scale(0)}
    to {-webkit-transform: scale(1)}
}

@keyframes animatezoom {
    from {transform: scale(0)}
    to {transform: scale(1)}
}

/* Change styles for span and cancel button on extra small screens */
@media screen and (max-width: 300px) {
    span.psw {
       display: block;
       float: none;
    }
    /*.gotosigninbtn {
       width: 100%;
    }*/
    .gotosigninbtn, .signupbtn, .resetpasswordbtn {
       width: 100%;
    }
}

img {
  display: block;
  margin: 0 auto;
}

/* Set height of the grid so .sidenav can be 100% (adjust as needed) */
.row.content {height: 550px}

/* On small screens, set height to 'auto' for the grid */
@media screen and (max-width: 767px) {
  .row.content {height: auto;}
}

table {
      margin: 0 0 0 0;
      width: 100%;
}
</style>
</head>
<body>


    <!-- Wrapper -->
      <div id="wrapper">

        <!-- Main -->
          <div id="main">

            <!-- One -->
              <section id="about">
                <div class="container">
                  <header class="major">
                    <h2>Dashboard</h2>
                  </header>

<!-- <button onclick="document.getElementById('id01').style.display='block'" style="width:auto;">My Account</button> -->

<?php
// define variables and set to empty values
$emailErr = $passwordErr = "";
$email = $newPassword = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (empty($_POST["email"])) {
    $emailErr = "Email is required";
  } else {
    $email = test_input($_POST["email"]);
    // check if e-mail address is well-formed
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $emailErr = "Invalid email format";
    }
  }
  if (empty($_POST["newPassword"])) {
    $newPasswordErr = "Password is required";
  } else {
    $newPassword = test_input($_POST["newPassword"]);
    // check if newPassword is well-formed
  }
}

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

$servername = "localhost";
$username = "cactus";
$password = "c@c7u$";
$dbname = "cactus";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$sql = "SELECT * FROM users WHERE email = '$email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
      if (password_verify($newPassword, $row["password_hash"])) {
      ?>
        <!-- echo "<br>Welcome " . $row["first_name"]. " " . $row["last_name"]. "!<br><br>" ; -->
                    <p>
        <div id="id01" class="modal">
          <form class="modal-content animate" action="/registerdevice.php" method="POST">
            <div class="container2">
              <?php
              echo "Welcome " . $row["first_name"]. " " . $row["last_name"]. "!<br>" ;

              $ip = $_SERVER['REMOTE_ADDR'];
              $sql = "SELECT * FROM devices WHERE ip = '$ip'";
              $result = $conn->query($sql);

              if ($result->num_rows > 1) {
                echo "<label><b>Too many new devices found on your network</b></label>";
              }
              elseif ($result->num_rows == 1 || 1) { ?>
                <br>
                <label><b>Register a New Device</b></label>
                <input type="text" placeholder="Device Name" name="nickname" required>
                <input type="hidden" name="email" value="<?php echo $_POST["email"];?>">
                <div class="clearfix">
                  <button type="submit" class="registerdevicebtn">Register Device</button>
                </div>
              <?php
              } else {
                  echo "<label><b>No new devices found on your network</b></label>";
              }
              ?>
              <form class="modal-content animate" action="/registerIFTTTkey.php" method="POST">
                   <br>
                   <label><b>Setup IFTTT Integration</b></label>
                   <input type="text" placeholder="IFTTT Maker Channel Key" name="key" required>
                   <input type="hidden" name="email" value="<?php echo $_POST["email"];?>">
                   <div class="clearfix">
                     <button type="submit" class="registerdevicebtn">Enable IFTTT Notifications</button>
                   </div>
             </form>
             <!-- <form class="modal-content animate" action="/changepassword.php" method="POST"> -->
                   <br>
                   <label><b>Change Your Password</b></label>
                   <input type="password" placeholder="Old Password" name="oldpassword" required>
                   <input type="password" placeholder="New Password" name="newpassword" required>
                   <input type="password" placeholder="Confirm New Password" name="confirmnewpassword" required>
                   <input type="hidden" name="email" value="<?php echo $_POST["email"];?>">
                   <div class="clearfix">
                     <button type="submit" class="registerdevicebtn">Change Password</button>
                   </div>
             <!-- </form> -->
            </div>
          </form>



        </div>
      <?php
      $sql = "SELECT * FROM devices WHERE user = '$email'";
      $result = $conn->query($sql);

      // echo $sql;
      if ($result->num_rows > 0) {
          // output data of each row
          while($row = $result->fetch_assoc()) {
            ?>
            <div class="container-fluid">
              <div class="col-sm-12">
                <div class="well">
            <?php
            echo "<h4>". $row["nickname"]. "</h4>";
            for ($i = 0; $i < 8; $i++) {
               $sqlAvg = "select 1023 - avg(value) from measurements where timestamp >= DATE_SUB(UTC_TIMESTAMP, INTERVAL 1 DAY) and device = '" . $row["uuid"] . "' and channel = " . $i . ";";
               $resultAvg = $conn->query($sqlAvg);
               $rowAvg = $resultAvg->fetch_assoc();
               // echo $sqlAvg;
               // if (!$rowAvg["1023 - max(value)"]) {
               //  echo "here";
               //  echo $rowAvg["1023 - max(value)"] / 10.23;
               //  echo "now";
               // }
               // echo "here";
               // echo round($rowAvg["1023 - avg(value)"] / 10.23, 2);
               if ($rowAvg["1023 - avg(value)"] < 900 && $rowAvg["1023 - avg(value)"] || 1) {
                  $sqlMin = "select 1023 - max(value) from measurements where timestamp >= DATE_SUB(UTC_TIMESTAMP, INTERVAL 1 DAY) and device = '" . $row["uuid"] . "' and channel = " . $i . ";";
                  $resultMin = $conn->query($sqlMin);
                  $rowMin = $resultMin->fetch_assoc();
                  $sqlCur = "select 1023 - value from measurements where timestamp = (select max(timestamp) from measurements where device = '" . $row["uuid"] . "' and channel = " . $i . ") and device = '" . $row["uuid"] . "' and channel = " . $i . ";";
                  $resultCur = $conn->query($sqlCur);
                  $rowCur = $resultCur->fetch_assoc();
                  $sqlMax = "select 1023 - min(value) from measurements where timestamp >= DATE_SUB(UTC_TIMESTAMP, INTERVAL 1 DAY) and device = '" . $row["uuid"] . "' and channel = " . $i . ";";
                  $resultMax = $conn->query($sqlMax);
                  $rowMax = $resultMax->fetch_assoc();
                  // echo $sqlMax;
                  if ($i != 0) {
                    echo "<br>";
                  }
                  echo "<h5><a href=gui.php?device=". $row["uuid"]. "&channel=". $i. ">Sensor ". ($i+1). "</a></h5>";
                  echo "<table><tr><td style='text-align: left;'>Min: ". round($rowMin["1023 - max(value)"] / 10.23, 2). "%</td>";
                  echo "<td style='text-align: center;'>Current: ". round($rowCur["1023 - value"] / 10.23, 2). "%</td>";
                  echo "<td style='text-align: right;'>Max: ". round($rowMax["1023 - min(value)"] / 10.23, 2). "%</td></tr></table>";
                  // echo "<br>";
               }
            }
            ?>


                </div>
              </div>
            </div>
                  </p>
            <?php
            // echo "<br>";
          }
      } else {
          // echo "<br>";
          echo "No devices found";
      }
      $conn->close();
      }
      else {
        echo "<br>";
        echo "Incorrect Password";
      }
    }
} elseif ($email != "") {
    echo "<br>";
    echo "Email address not found";
}
?>

                </div>
              </section>
          </div>
        <!-- Footer -->
          <section id="footer">
            <div class="container">
              <ul class="copyright">
                <li>Copyright &copy; 2017 mlindly. All rights reserved.</li><li>Design: <a href="http://html5up.net">HTML5 UP</a></li><li>Fall 2016 - Spring 2017</li>
              </ul>
            </div>
          </section>

      </div>
    <script>
      // Get the modals
      var modal = document.getElementById('id01');


      // When the user clicks anywhere outside of the modals, close it
      window.onclick = function(event) {
          if (event.target == modal) {
              modal.style.display = "none";
          }
      }
    </script>
    <div id="titleBar">
    <!--   <a href="#header" class="toggle">
      </a> -->
      <button onclick="document.getElementById('id01').style.display='block'" style="width:auto;">My Account</button>
      <span class="title">SmartGarden</span>
    </div>
  </body>
</html>
