<!-- File: registerdevice.php
Author: Matthew Lindly (mlindly)
Date: 4/5/17
Class: CPE 462-10 LAB
Assignment: Senior Project
References:
 https://www.w3schools.com/php/php_form_complete.asp -->

<!DOCTYPE HTML>
<html>
<head>
<style>
.error {color: #FF0000;}
</style>
</head>
<body>

<h2>Register a Device Landing</h2>

<?php
// define variables and set to empty values
$uuidErr = $nicknameErr = $emailErr = $passwordErr = "";
$uuid = $nickname = $email = $newPassword = "";
$ip = $_SERVER['REMOTE_ADDR'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (empty($_POST["uuid"])) {
    $uuidErr = "UUID is required";
  } else {
    $uuid = test_input($_POST["uuid"]);
    // check if uuid is well-formed
  }
  if (empty($_POST["nickname"])) {
    $nicknameErr = "Device Name is required";
  } else {
    $nickname = test_input($_POST["nickname"]);
    // check if nickname is well-formed
  }
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
$sql = "UPDATE devices
 SET ip = null, user = '$email', nickname = '$nickname'
 WHERE ip = '$ip'";

if ($conn->query($sql) === TRUE) {
    echo "<br>";
    echo "New device registered successfully";
}
$conn->close();
?>

</body>
</html>
