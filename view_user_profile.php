<?php
// admin_profiles.php
session_start();
include 'db.php'; // database connection

// ✅ Optional: only allow admin (you can set a session role at login)
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access denied! Only admins can view this page.");
}

// Fetch all user profiles
$sql = "SELECT username, email, full_name, phone, address, skills FROM profile_tbl";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - User Profiles</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f9; margin: 0; padding: 0; }
        .container { max-width: 1000px; margin: 50px auto; background: #fff; padding: 20px; border-radius: 10px; 
                     box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; margin-bottom: 20px; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #007bff; color: white; }
        tr:hover { background: #f1f1f1; }
        .btn-back { display: inline-block; margin-top: 15px; padding: 8px 15px; 
                    background: #007bff; color: white; text-decoration: none; border-radius: 5px; }
        .btn-back:hover { background: #0056b3; }
    </style>
</head>
<body>
    <div class="container">
        <h2>All User Profiles</h2>
        <?php if ($result->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Full Name</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>Skills</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['phone']); ?></td>
                        <td><?php echo htmlspecialchars($row['address']); ?></td>
                        <td><?php echo htmlspecialchars($row['skills']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p style="text-align:center;">No user profiles found.</p>
        <?php endif; ?>
        <a href="admin_dashboard.php" class="btn-back">⬅ Back to Dashboard</a>
    </div>
</body>
</html>
<?php $conn->close(); ?>
