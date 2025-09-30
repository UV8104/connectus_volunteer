<?php
session_start();
include 'db.php';

// ✅ Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// ✅ Check if event_id is sent
if (!isset($_POST['event_id'])) {
    $_SESSION['message'] = "Invalid request!";
    $_SESSION['msg_type'] = "error";
    header("Location: attendance.php");
    exit();
}

$event_id = intval($_POST['event_id']);

// ✅ Check if already submitted attendance
$check = $conn->prepare("SELECT * FROM attendance WHERE user_id = ? AND event_id = ?");
$check->bind_param("ii", $user_id, $event_id);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    // Already marked
    $_SESSION['message'] = "✅ You already submitted your attendance!";
    $_SESSION['msg_type'] = "info";
} else {
    // Insert attendance
    $insert = $conn->prepare("INSERT INTO attendance (user_id, event_id, status, submitted_at) VALUES (?, ?, 'submitted', NOW())");
    $insert->bind_param("ii", $user_id, $event_id);

    if ($insert->execute()) {
        $_SESSION['message'] = "Attendance submitted successfully!";
        $_SESSION['msg_type'] = "success";
    } else {
        $_SESSION['message'] = "Error submitting attendance: " . $insert->error;
        $_SESSION['msg_type'] = "error";
    }
    $insert->close();
}

$check->close();
$conn->close();

// ✅ Redirect back
header("Location: attendance.php");
exit();
?>
