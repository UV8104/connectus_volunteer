<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "users";

// Create connection
$conn_users = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn_users->connect_error) {
    die("Connection failed: " . $conn_users->connect_error);
}
?>
