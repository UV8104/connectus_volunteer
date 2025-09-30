<?php
session_start();
include 'db.php';

// ✅ Handle flash message
$message = "";
$message_type = "";
if (isset($_SESSION['flash_message'])) {
    $message = $_SESSION['flash_message'];
    $message_type = $_SESSION['flash_type'];
    unset($_SESSION['flash_message']);
    unset($_SESSION['flash_type']);
}

$sql = "SELECT ea.application_id, ea.user_id, ea.full_name, ea.phone, ea.address, ea.skills, ea.email, ea.username, ea.applied_at, ea.status, e.title, e.event_date 
        FROM user_applications ea
        JOIN events e ON ea.event_id = e.event_id
        ORDER BY ea.applied_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Applications</title>
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #eef2f7;
        padding: 20px;
    }
    .container {
        max-width: 1200px;
        margin: auto;
        background: #fff;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 6px 15px rgba(0,0,0,0.1);
    }
    h2 {
        text-align: center;
        color: #333;
        margin-bottom: 25px;
        font-size: 26px;
    }
    table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 10px;
    }
    th {
        background: #007bff;
        color: #fff;
        padding: 12px;
        text-align: left;
        border-radius: 6px 6px 0 0;
    }
    td {
        background: #fafafa;
        padding: 12px;
        border: 1px solid #eee;
        vertical-align: middle;
    }
    tr:hover td {
        background: #f5f9ff;
    }
    .btn-group {
        display: flex;
        gap: 10px; /* prevents overlap */
    }
    .btn {
        padding: 8px 15px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        color: white;
        transition: 0.2s ease-in-out;
        display: inline-block;
        text-align: center;
        min-width: 80px;
    }
    .btn-approve {
        background: #28a745;
    }
    .btn-deny {
        background: #dc3545;
    }
    .btn-approve:hover {
        background: #218838;
        transform: scale(1.05);
    }
    .btn-deny:hover {
        background: #c82333;
        transform: scale(1.05);
    }
    .flash {
        text-align: center;
        padding: 12px;
        margin-bottom: 20px;
        border-radius: 8px;
        font-weight: bold;
        font-size: 15px;
        animation: fadeIn 0.3s ease-in-out;
    }
    .success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    .error {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    @keyframes fadeIn {
        from {opacity: 0;}
        to {opacity: 1;}
    }
</style>
</head>
<body>
<div class="container">
    <h2>All Event Applications</h2>

    <?php if ($message): ?>
        <div class="flash <?php echo $message_type; ?>"><?php echo $message; ?></div>
        <script>
            setTimeout(() => {
                document.querySelector('.flash').style.display = 'none';
            }, 1000); // hide after 1 sec
        </script>
    <?php endif; ?>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Event</th>
                <th>User</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Skills</th>
                <th>Address</th>
                <th>Applied At</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['title']); ?> <br><small><?php echo $row['event_date']; ?></small></td>
                    <td><?php echo htmlspecialchars($row['full_name']); ?> (<?php echo $row['username']; ?>)</td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['phone']; ?></td>
                    <td><?php echo $row['skills']; ?></td>
                    <td><?php echo $row['address']; ?></td>
                    <td><?php echo $row['applied_at']; ?></td>
                    <td><strong><?php echo ucfirst($row['status']); ?></strong></td>
                    <td>
                        <div class="btn-group">
                            <a href="update_status.php?id=<?php echo $row['application_id']; ?>&action=approve" class="btn btn-approve">Approve</a>
                            <a href="update_status.php?id=<?php echo $row['application_id']; ?>&action=deny" class="btn btn-deny">Deny</a>
                        </div>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No applications yet.</p>
    <?php endif; ?>
</div>
</body>
</html>
