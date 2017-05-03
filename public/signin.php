<!-- File: signin.php
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

<h2>Dashboard</h2>

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

// echo "<h2>Your Input:</h2>";
// echo "Email input: " . $email;
// echo "<br>";
// echo "newPassword input: " . $newPassword;
// echo "<br>";

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
        echo "Welcome " . $row["first_name"]. " " . $row["last_name"]. "!<br>" ;
        // echo "<br>";
        // echo "Here's your info:";
        // echo "<br>";
        // echo "id: " . $row["id"]. " - Name: " . $row["first_name"]. " " . $row["last_name"]. "<br>";
      }
      if (!password_verify($newPassword, $row["password_hash"])) {
        echo "<br>";
        echo "Incorrect Password";
      }
    }
} elseif ($email != "") {
    echo "<br>";
    echo "Email address not found";
}

$sql = "SELECT * FROM devices WHERE user = '$email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // echo "here". $result->num_rows;
    // output data of each row
    while($row = $result->fetch_assoc()) {
      echo "Device nickname: " . $row["nickname"]. "<br>" ;
      // echo "<br>";
      // echo "Here's your info:";
      // echo "<br>";
      // echo "id: " . $row["id"]. " - Name: " . $row["first_name"]. " " . $row["last_name"]. "<br>";
    }
} else {
    echo "<br>";
    echo "No devices found";
}
$conn->close();
?>

</body>
</html>
