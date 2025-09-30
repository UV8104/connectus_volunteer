<?php
session_start();
include 'db.php';

if (!isset($_GET['id']) || !isset($_GET['action'])) {
    header("Location: manage_applications.php");
    exit;
}

$app_id = intval($_GET['id']);
$action = $_GET['action'];

// Fetch application with event date
$sql = "SELECT ea.status, e.event_date 
        FROM user_applications ea
        JOIN events e ON ea.event_id = e.event_id
        WHERE ea.application_id = $app_id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    $_SESSION['flash_message'] = "Invalid application!";
    $_SESSION['flash_type'] = "error";
    header("Location: manage_applications.php");
    exit;
}

$row = $result->fetch_assoc();
$current_status = $row['status'];
$event_date = $row['event_date'];
$today = date("Y-m-d");

// Restrict on event day or after
if ($today >= $event_date) {
    $_SESSION['flash_message'] = "You can't approve and deny any user";
    $_SESSION['flash_type'] = "error";
    header("Location: manage_applications.php");
    exit;
}

// Approve logic
if ($action == "approve") {
    if ($current_status == "approved") {
        $_SESSION['flash_message'] = "You already approved user";
        $_SESSION['flash_type'] = "success";
    } elseif ($current_status == "denied") {
        $_SESSION['flash_message'] = "User already denied";
        $_SESSION['flash_type'] = "error";
    } else {
        $conn->query("UPDATE user_applications SET status='approved' WHERE application_id=$app_id");
        $_SESSION['flash_message'] = "You are approved user";
        $_SESSION['flash_type'] = "success";
    }
}

// Deny logic
if ($action == "deny") {
    if ($current_status == "denied") {
        $_SESSION['flash_message'] = "You already denied user";
        $_SESSION['flash_type'] = "error";
    } elseif ($current_status == "approved") {
        $_SESSION['flash_message'] = "User already approved";
        $_SESSION['flash_type'] = "success";
    } else {
        $conn->query("UPDATE user_applications SET status='denied' WHERE application_id=$app_id");
        $_SESSION['flash_message'] = "You deny user";
        $_SESSION['flash_type'] = "error";
    }
}

header("Location: manage_applications.php");
exit;
