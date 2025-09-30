<?php
// Database configuration
$host = "localhost";   // usually 'localhost'
$dbname = "users";     // your database name
$username = "root";    // default username in XAMPP/WAMP
$password = "";        // default password is empty in XAMPP

try {
    // Create PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // Set error reporting mode
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}
?>
