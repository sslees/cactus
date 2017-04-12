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

<?php
// define variables and set to empty values
$uuidErr = $nameErr = $emailErr = $passwordErr = "";
$uuid = $name = $email = $newPassword = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (empty($_POST["uuid"])) {
    $uuidErr = "UUID is required";
  } else {
    $uuid = test_input($_POST["uuid"]);
    // check if uuid is well-formed
  }
  if (empty($_POST["name"])) {
    $nameErr = "Device Name is required";
  } else {
    $name = test_input($_POST["name"]);
    // check if name is well-formed
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
?>

<h2>Register a Device</h2>

<?php
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
$ip = $_SERVER['REMOTE_ADDR'];
// echo $ip;
$sql = "SELECT * FROM devices WHERE ip = '$ip'";
// echo $sql;
$result = $conn->query($sql);
// echo $result;

if ($result->num_rows > 0) {
    // output data of each row
  echo "New devices on your network:<br>";
  while($row = $result->fetch_assoc()) {
    echo $row["uuid"]. "<br>";
    // echo "<br>";
    // echo "Here's your info:";
    // echo "<br>";
    // echo "id: " . $row["id"]. " - Name: " . $row["first_name"]. " " . $row["last_name"]. "<br>";
  }
} else {
    echo "<br>";
    echo "No new devices found on your network";
}
$conn->close();
?>

<p><span class="error">* required field.</span></p>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
  UUID: <input type="text" name="uuid" value="<?php echo $uuid;?>">
  <span class="error">* <?php echo $uuidErr;?></span>
  <br><br>
  Device Name: <input type="text" name="name" value="<?php echo $name;?>">
  <span class="error">* <?php echo $nameErr;?></span>
  <br><br>
  E-mail: <input type="text" name="email" value="<?php echo $email;?>">
  <span class="error">* <?php echo $emailErr;?></span>
  <br><br>
  Password: <input type="password" name="newPassword" value="<?php echo $newPassword;?>">
  <span class="error">* <?php echo $newPasswordErr;?></span>
  <a href="resetpassword.php">Forgot Password</a>
  <br><br>
  <input type="submit" name="submit" value="Submit">
  <br><br>
</form>

Don’t have an account?
<a href="signup.php">Sign up</a>
<br><br>

<?php
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
      if ($row["password_hash"] == $newPassword) {
        $sql = "UPDATE devices
         SET ip = null, user = '$email', nickname = '$name'
         WHERE uuid = '$uuid'";
        // echo $sql;

        if ($conn->query($sql) === TRUE) {
            echo "<br>";
            // echo "Your password is: " . $row["password_hash"]. "<br>" ;
            echo "New device registered successfully";
            // echo "<br>";
            // echo "New user created successfully";
        }
        // echo "<br>";
        // echo "Here's your info:";
        // echo "<br>";
        // echo "id: " . $row["id"]. " - Name: " . $row["first_name"]. " " . $row["last_name"]. "<br>";
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
$conn->close();
?>

</body>
</html>
