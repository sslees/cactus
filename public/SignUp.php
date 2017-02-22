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
$firstNameErr = $lastNameErr = $emailErr = $newPasswordErr = $confirmedPasswordErr = "";
$firstName = $lastName = $email = $newPassword = $confirmedPassword = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (empty($_POST["firstName"])) {
    $firstNameErr = "First Name is required";
  } else {
    $firstName = test_input($_POST["firstName"]);
    // check if first name only contains letters and whitespace
    if (!preg_match("/^[a-zA-Z ]*$/",$firstName)) {
      $firstNameErr = "Only letters and white space allowed";
    }
  }

  if (empty($_POST["lastName"])) {
    $lastNameErr = "Last Name is required";
  } else {
    $lastName = test_input($_POST["lastName"]);
    // check if last name only contains letters and whitespace
    if (!preg_match("/^[a-zA-Z ]*$/",$lastName)) {
      $lastNameErr = "Only letters and white space allowed";
    }
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

  if (empty($_POST["confirmedPassword"])) {
    $confirmedPasswordErr = "Confirm Password is required";
  } else {
    $confirmedPassword = test_input($_POST["confirmedPassword"]);
    // check if confirmed passwords match
    if ($newPassword != $confirmedPassword) {
      $confirmedPasswordErr = "Passwords do not match";
    }
  }
}

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}
?>

<h2>Sign Up!</h2>
<p><span class="error">* required field.</span></p>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
  First Name: <input type="text" name="firstName" value="<?php echo $firstName;?>">
  <span class="error">* <?php echo $firstNameErr;?></span>
  <br><br>
  Last Name: <input type="text" name="lastName" value="<?php echo $lastName;?>">
  <span class="error">* <?php echo $lastNameErr;?></span>
  <br><br>
  E-mail: <input type="text" name="email" value="<?php echo $email;?>">
  <span class="error">* <?php echo $emailErr;?></span>
  <br><br>
  Password: <input type="password" name="newPassword" value="<?php echo $newPassword;?>">
  <span class="error">* <?php echo $newPasswordErr;?></span>
  <br><br>
  Confirmed Password: <input type="password" name="confirmedPassword" value="<?php echo $confirmedPassword;?>">
  <span class="error">* <?php echo $confirmedPasswordErr;?></span>
  <br><br>
  <input type="submit" name="submit" value="Submit">
  <br><br>
</form>

Already have an account?
<a href="Login.php">Login</a>
<br><br>

<?php
// echo "<h2>Your Input:</h2>";
// echo $firstName;
// echo "<br>";
// echo $lastName;
// echo "<br>";
// echo $email;
// echo "<br>";
// echo $newPassword;
// echo "<br>";
// echo $confirmedPassword;
// echo "<br>";
// echo "<br>";

$servername = "localhost";
$username = "matt";
$password = "vaporize-thank-dimple";
$dbname = "testing_matt";

if ($firstNameErr == "" && $lastNameErr == "" && $emailErr == "" && $newPasswordErr == "" && $confirmedPasswordErr == "" && $firstName != "" && $lastName != "" && $email != "" && $newPassword != "" && $confirmedPassword != "") {
  // Create connection
  $conn = new mysqli($servername, $username, $password, $dbname);
  // Check connection
  if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
  }

  $sql = "INSERT INTO Users (firstName, lastName, email, password)
  VALUES ('$firstName', '$lastName', '$email', '$newPassword')";

  if ($conn->query($sql) === TRUE) {
      echo "New user created successfully";
  } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
  }

  $conn->close();
}
?>

</body>
</html>
