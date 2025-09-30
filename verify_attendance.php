<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

if (isset($_POST['attendance_id'], $_POST['action'])) {
    $attendance_id = intval($_POST['attendance_id']);
    $action = $_POST['action'];

    // Fetch attendance record
    $stmt = $conn->prepare("SELECT * FROM attendance WHERE id = ?");
    $stmt->bind_param("i", $attendance_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $attendance = $result->fetch_assoc();

    if ($attendance) {
        if ($action === "verify") {
            // ✅ Move to verified_attendance
            $insert = $conn->prepare("INSERT INTO verified_attendance (attendance_id, user_id, event_id) VALUES (?, ?, ?)");
            $insert->bind_param("iii", $attendance['id'], $attendance['user_id'], $attendance['event_id']);
            $insert->execute();

            // Update attendance status
            $update = $conn->prepare("UPDATE attendance SET verified = 'Verified' WHERE id = ?");
            $update->bind_param("i", $attendance_id);
            $update->execute();

            $_SESSION['message'] = "Attendance verified successfully!";
            $_SESSION['msg_type'] = "success";

        } elseif ($action === "deny") {
            // Update attendance status to denied
            $update = $conn->prepare("UPDATE attendance SET verified = 'Denied', status = 'Denied' WHERE id = ?");
            $update->bind_param("i", $attendance_id);
            $update->execute();

            $_SESSION['message'] = "Attendance denied!";
            $_SESSION['msg_type'] = "error";
        }
    }
}

header("Location: admin_attendance.php");
exit();
?>
