<?php
session_start();
include 'db.php'; // contains $conn_events (events_db) and $conn_users (users DB)

date_default_timezone_set('Asia/Kolkata'); // Set timezone

// Example admin login session
if (!isset($_SESSION['admin_id'])) {
    $_SESSION['admin_id'] = 1; // mock admin login
}

// Handle Verify / Deny action
if (isset($_POST['action'])) {
    $user_id = intval($_POST['user_id']);
    $event_id = intval($_POST['event_id']);
    $action = $_POST['action'];

    // Check if already verified or denied
    $check = $conn_events->query("SELECT action FROM verified_attendance WHERE user_id=$user_id AND event_id=$event_id");

    if ($check->num_rows > 0) {
        $row = $check->fetch_assoc();
        if ($row['action'] === 'verify') {
            $_SESSION['message'] = "Verification already done";
        } elseif ($row['action'] === 'deny') {
            $_SESSION['message'] = "You denied already";
        }
        $_SESSION['msg_type'] = "success"; // green message
    } else {
        if ($action == "verify") {
            $conn_events->query("INSERT INTO verified_attendance (user_id, event_id, verified_date, action) VALUES ($user_id, $event_id, NOW(), 'verify')");
            $_SESSION['message'] = "Attendance verified successfully";
            $_SESSION['msg_type'] = "success";
        } elseif ($action == "deny") {
            $conn_events->query("INSERT INTO verified_attendance (user_id, event_id, verified_date, action) VALUES ($user_id, $event_id, NOW(), 'deny')");
            $_SESSION['message'] = "You denied the attendance";
            $_SESSION['msg_type'] = "success"; // green message
        }
    }

    header("Location: admin_attendance.php");
    exit();
}

// Fetch all applied users for all events
$sql = "SELECT ua.user_id, ua.username, ua.event_id, e.title, e.event_date, e.event_start_time
        FROM user_applications ua
        JOIN events e ON ua.event_id = e.event_id
        ORDER BY e.event_date ASC, ua.username ASC";
$result = $conn_events->query($sql);

$currentDateTime = new DateTime(); // Current datetime
$currentDate = $currentDateTime->format('Y-m-d'); // today's date
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Attendance</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f9fafb;
            margin: 0;
            padding: 20px;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .msg {
            padding: 12px 20px;
            margin: 15px auto;
            border-radius: 8px;
            text-align: center;
            width: 50%;
            font-weight: bold;
            animation: fadeOut 1s ease-in-out forwards;
            animation-delay: 1s; /* disappear after 1 sec */
        }
        .success { background: #d1fae5; color: #065f46; border: 1px solid #10b981; }
        @keyframes fadeOut { to { opacity: 0; visibility: hidden; } }
        table {
            border-collapse: collapse;
            width: 90%;
            margin: 20px auto;
            background: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-radius: 10px;
            overflow: hidden;
        }
        th {
            background: #2563eb;
            color: white;
            padding: 12px;
            text-align: center;
        }
        td {
            border-bottom: 1px solid #eee;
            padding: 10px;
            text-align: center;
        }
        tr:hover { background: #f1f5f9; }
        button {
            padding: 8px 15px;
            margin: 3px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            transition: 0.2s ease;
        }
        button[name="action"][value="verify"] {
            background: #10b981; color: white;
        }
        button[name="action"][value="verify"]:hover { background: #059669; }
        button[name="action"][value="deny"] {
            background: #ef4444; color: white;
        }
        button[name="action"][value="deny"]:hover { background: #dc2626; }
        button:disabled { background: #d1d5db !important; cursor: not-allowed; }
    </style>
</head>
<body>

<h2>📋 Admin Attendance Panel</h2>

<?php
if (isset($_SESSION['message'])) {
    echo "<div class='msg success'>".$_SESSION['message']."</div>";
    unset($_SESSION['message']);
    unset($_SESSION['msg_type']);
}
?>

<table>
    <tr>
        <th>User</th>
        <th>Event</th>
        <th>Event Date</th>
        <th>Start Time</th>
        <th>Action</th>
    </tr>
    <?php while($row = $result->fetch_assoc()): ?>
        <?php
        // Create DateTime objects for event start and end of the day
        $eventStartDateTime = new DateTime($row['event_date'] . ' ' . $row['event_start_time']);
        $eventEndDateTime   = new DateTime($row['event_date'] . ' 23:59:59');

        // Check verified_attendance table
        $check = $conn_events->query("SELECT action FROM verified_attendance WHERE user_id={$row['user_id']} AND event_id={$row['event_id']}");
        $alreadyAction = $check->num_rows > 0 ? $check->fetch_assoc()['action'] : '';

        // Enable button only if current datetime is on event date and between start and midnight
        $enableButton = ($currentDateTime >= $eventStartDateTime && $currentDateTime <= $eventEndDateTime && !$alreadyAction);
        ?>
        <tr>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td><?= htmlspecialchars($row['title']) ?></td>
            <td><?= $row['event_date'] ?></td>
            <td><?= $row['event_start_time'] ?></td>
            <td>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="user_id" value="<?= $row['user_id'] ?>">
                    <input type="hidden" name="event_id" value="<?= $row['event_id'] ?>">
                    <button type="submit" name="action" value="verify" <?= $enableButton ? "" : "disabled" ?>>Attendance Verified</button>
                    <button type="submit" name="action" value="deny" <?= $enableButton ? "" : "disabled" ?>>Deny</button>
                </form>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

</body>
</html>
