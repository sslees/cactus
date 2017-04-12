<!-- File: resetpassword.php
Author: Matthew Lindly (mlindly)
Date: 4/5/17
Class: CPE 462-10 LAB
Assignment: Senior Project
References:
 https://www.w3schools.com/php/php_form_complete.asp
 https://hugh.blog/2012/04/23/simple-way-to-generate-a-random-password-in-php/-->

<!DOCTYPE HTML>
<html>
<head>
<style>
.error {color: #FF0000;}
</style>
</head>
<body>

<h2>Check Email</h2>

<?php
function random_password( $length = 8 ) {
  $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
  $password = substr( str_shuffle( $chars ), 0, $length );
  return $password;
}

// define variables and set to empty values
$emailErr = "";
$email = "";

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
        $temporaryPassword = random_password(8);
        $hashedPassword = password_hash($temporaryPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE users
         SET password_hash = '$hashedPassword'
         WHERE email = '$email'";
        // echo $sql;

        if ($conn->query($sql) === TRUE) {
          // // the message
          // $msg = "The temporary password for " . $row["email"] . " is: " . $temporaryPassword . "\n";
          // Message
          $message = "
          <html>
          <body>
            <h1>SmartGarden Temporary Password</h1>
            Hi " . $row["first_name"] . ",<br />
            Your temporary password is: " . $temporaryPassword . "
          </body>
          </html>
          ";

          // To send HTML mail, the Content-type header must be set
          $headers[] = 'MIME-Version: 1.0';
          $headers[] = 'Content-type: text/html; charset=iso-8859-1';

          // Additional headers
          $headers[] = 'From: SmartGarden <noreply@SmartGarden.com>';

          // send email
          mail($row["email"],"SmartGarden Password Reset",$message, implode("\r\n", $headers));
          echo "<br>";
          // echo "Your password is: " . $row["password_hash"]. "<br>" ;
          echo "Password reset email sent successfully";
          // echo "<br>";
          // echo "Here's your info:";
          // echo "<br>";
          // echo "id: " . $row["id"]. " - Name: " . $row["first_name"]. " " . $row["last_name"]. "<br>";
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
