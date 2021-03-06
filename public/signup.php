<!-- File: signup.php
Author: Matthew Lindly (mlindly)
Date: 4/5/17
Class: CPE 462-10 LAB
Assignment: Senior Project
References:
 https://www.w3schools.com/php/php_form_complete.asp
 http://php.net/manual/en/function.mail.php-->

<!DOCTYPE HTML>
<html>
<head>
<style>
.error {color: #FF0000;}
</style>
</head>
<body>

<h2>Sign Up Landing</h2>

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
$username = "cactus";
$password = "c@c7u$";
$dbname = "cactus";

if ($firstNameErr == "" && $lastNameErr == "" && $emailErr == "" && $newPasswordErr == "" && $confirmedPasswordErr == "" && $firstName != "" && $lastName != "" && $email != "" && $newPassword != "" && $confirmedPassword != "") {
  // Create connection
  $conn = new mysqli($servername, $username, $password, $dbname);
  // Check connection
  if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
  }
  $sql = "SELECT * FROM users WHERE email = '$email'";
  $result = $conn->query($sql);

  if ($result->num_rows == 0) {
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (email, first_name, last_name, password_hash)
    VALUES ('$email', '$firstName', '$lastName', '$hashedPassword')";

    if ($conn->query($sql) === TRUE) {
        // // the message
        // $msg = "Welcome to SmartGarden, " . $firstName . "!\n";
        // Message
        $message = "
        <html>
        <body>
          <h1>Welcome to SmartGarden, " . $firstName . "!</h1>
        </body>
        </html>
        ";

        // To send HTML mail, the Content-type header must be set
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/html; charset=iso-8859-1';

        // Additional headers
        $headers[] = 'From: SmartGarden <noreply@SmartGarden.com>';

        // send email
        mail($email,"Welcome to SmartGarden!", $message, implode("\r\n", $headers));
        // echo "<br>";
        // echo "Your password is: " . $row["password_hash"]. "<br>" ;
        echo "New user email sent successfully";
        // echo "<br>";
        // echo "New user created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
  }
  else {
    while($row = $result->fetch_assoc()) {
      if ($row["email"] == $email) {
        // echo "<br>";
        echo "Email address already in use";
        // echo "<br>";
        // echo "Here's your info:";
        // echo "<br>";
        // echo "id: " . $row["id"]. " - Name: " . $row["first_name"]. " " . $row["last_name"]. "<br>";
      }
    }
  }

  $conn->close();
}
?>

</body>
</html>
