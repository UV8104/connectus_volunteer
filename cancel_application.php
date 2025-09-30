<?php
session_start();
include 'db.php';

if (!isset($_GET['id'])) {
    header("Location: my_applications.php");
    exit();
}

$app_id = intval($_GET['id']);

// ✅ Prevent cancellation if event is today or past
$sql = "SELECT event_date FROM user_applications ua JOIN events e ON ua.event_id = e.event_id WHERE ua.application_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $app_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0) {
    $_SESSION['flash_message'] = "Invalid application!";
    $_SESSION['flash_type'] = "error";
    header("Location: my_applications.php");
    exit;
}

$row = $result->fetch_assoc();
$today = date("Y-m-d");
if($today >= $row['event_date']) {
    $_SESSION['flash_message'] = "You can't cancel the event on or after event date";
    $_SESSION['flash_type'] = "error";
    header("Location: my_applications.php");
    exit;
}

// ✅ Delete application
$stmt = $conn->prepare("DELETE FROM user_applications WHERE application_id = ?");
$stmt->bind_param("i", $app_id);
$stmt->execute();

$_SESSION['flash_message'] = "Event application canceled successfully.";
$_SESSION['flash_type'] = "success";

$stmt->close();
header("Location: my_applications.php");
exit();
?>
