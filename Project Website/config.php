<?php
$host = 'localhost';
$db = 'user_auth';
$user = 'root';
$pass = ''; // Leave this blank for XAMPP unless you've set a password

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
