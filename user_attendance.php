<?php
session_start();
include 'db.php'; // contains $conn_events and $conn_users

// Example user login session
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 2; // mock login
}
$user_id = $_SESSION['user_id'];

// ✅ Fetch all applied events from user_applications table
$sql = "SELECT ua.event_id, e.title, e.event_date, e.event_start_time
        FROM user_applications ua
        JOIN events e ON ua.event_id = e.event_id
        WHERE ua.user_id = $user_id
        ORDER BY e.event_date ASC, e.event_start_time ASC";
$result = $conn_events->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Attendance</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f9fafb;
            margin: 20px;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .msg {
            color: red;
            margin-top: 20px;
            font-weight: bold;
            text-align: center;
        }
        table {
            border-collapse: collapse;
            width: 90%;
            margin: 20px auto;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
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
        .icon {
            font-size: 18px;
        }
        .verified { color: #10b981; } /* green check */
        .denied { color: #ef4444; }   /* red cross */
        .pending { color: #f59e0b; }  /* orange for pending */
    </style>
</head>
<body>

<h2>📋 User Attendance Panel</h2>

<?php if ($result->num_rows == 0): ?>
    <div class="msg">You have not applied for any events yet</div>
<?php else: ?>
    <table>
        <tr>
            <th>Event</th>
            <th>Date</th>
            <th>Start Time</th>
            <th>Attendance Status</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
            <?php
            $vid = $row['event_id'];

            // Check if admin has verified/denied this user's attendance
            $check = $conn_events->query("SELECT action FROM verified_attendance WHERE user_id=$user_id AND event_id=$vid");
            $statusIcon = "<span class='icon pending'>⌛</span>"; // default pending

            if ($check->num_rows > 0) {
                $vRow = $check->fetch_assoc();
                if ($vRow['action'] == 'verify') {
                    $statusIcon = "<span class='icon verified'>✅ Verified</span>";
                } elseif ($vRow['action'] == 'deny') {
                    $statusIcon = "<span class='icon denied'>❌ Denied</span>";
                }
            }
            ?>
            <tr>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td><?= $row['event_date'] ?></td>
                <td><?= $row['event_start_time'] ?></td>
                <td><?= $statusIcon ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php endif; ?>

</body>
</html>
