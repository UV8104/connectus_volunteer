<?php
// Connection to events_db
$servername = "localhost";
$dbUser = "root";
$dbPass = "";
$dbEvents = "events_db";

$conn_events = new mysqli($servername, $dbUser, $dbPass, $dbEvents);
if ($conn_events->connect_error) {
    die("Connection failed to events_db: " . $conn_events->connect_error);
}

// Make $conn point to events_db for backward compatibility
$conn = $conn_events;

// Connection to users database
$dbUsers = "users";
$conn_users = new mysqli($servername, $dbUser, $dbPass, $dbUsers);
if ($conn_users->connect_error) {
    die("Connection failed to users DB: " . $conn_users->connect_error);
}
?>
