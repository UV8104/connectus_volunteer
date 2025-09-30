<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "events_db";

// Create connection
$conn_events = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn_events->connect_error) {
    die("Connection failed: " . $conn_events->connect_error);
}
?>
