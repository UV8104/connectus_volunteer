<?php
session_start();
include 'db.php'; // contains $conn_events and $conn_users (users DB connection)

// ✅ Fetch all verified attendance
$sql = "SELECT va.user_id, u.username, u.email, e.title, e.event_date, va.verified_date
        FROM verified_attendance va
        JOIN users.users_login u ON va.user_id = u.id
        JOIN events e ON va.event_id = e.event_id
        WHERE va.action = 'verify'
        ORDER BY va.verified_date DESC";

$result = $conn_events->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Verified Attendance Report</title>
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
            margin-bottom: 20px;
        }
        table {
            border-collapse: collapse;
            width: 90%;
            margin: 0 auto 30px auto;
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
        .status {
            color: #10b981; /* green */
            font-weight: bold;
        }
    </style>
</head>
<body>

<h2>✅ Verified Attendance Report</h2>

<?php if ($result->num_rows == 0): ?>
    <p style="text-align:center; font-weight:bold; color:#ef4444;">No verified attendance yet.</p>
<?php else: ?>
    <table>
        <tr>
            <th>Username</th>
            <th>Email</th>
            <th>Event</th>
            <th>Event Date</th>
            <th>Verified On</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['username']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td><?= $row['event_date'] ?></td>
                <td class="status"><?= $row['verified_date'] ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php endif; ?>

</body>
</html>
