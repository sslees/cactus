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
?>

<h2>Sign In!</h2>
<p><span class="error">* required field.</span></p>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
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
$username = "matt";
$password = "vaporize-thank-dimple";
$dbname = "testing_matt";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$sql = "SELECT * FROM Users WHERE email = '$email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
      if ($row["password"] == $newPassword) {
        echo "Welcome " . $row["firstName"]. " " . $row["lastName"]. "! (id: " . $row["id"]. ")<br>" ;
        // echo "<br>";
        // echo "Here's your info:";
        // echo "<br>";
        // echo "id: " . $row["id"]. " - Name: " . $row["firstName"]. " " . $row["lastName"]. "<br>";
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
