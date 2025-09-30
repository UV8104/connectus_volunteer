<?php
session_start();

// Redirect if not admin
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
    die("Access denied. Admins only.");
}

// Database connection
$servername = "localhost";
$dbUser = "root";
$dbPass = "";
$dbName = "users"; // your DB name

$conn = new mysqli($servername, $dbUser, $dbPass, $dbName);
if($conn->connect_error){
    die("Database connection failed: ".$conn->connect_error);
}

// Fetch all user queries
$result = $conn->query("SELECT q.id, q.user_id, q.name, q.email, q.message, q.created_at, u.username 
                        FROM queries q 
                        JOIN users_login u ON q.user_id = u.id
                        ORDER BY q.created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User Queries - Admin Panel</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #f3f6f9;
    margin: 0;
    padding: 0;
}

.container {
    max-width: 1200px;
    margin: 40px auto;
    padding: 0 20px;
}

h1 {
    text-align: center;
    margin-bottom: 30px;
    color: #0072ff;
}

.query-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 20px;
}

.query-card {
    background: #fff;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    transition: transform 0.2s;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.query-card:hover {
    transform: translateY(-5px);
}

.query-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.query-header .user-name {
    font-weight: 600;
    color: #0072ff;
}

.query-header .date {
    font-size: 13px;
    color: #555;
}

.query-message {
    margin-bottom: 20px;
    color: #333;
    line-height: 1.5;
}

.query-email {
    font-size: 14px;
    color: #555;
    margin-bottom: 15px;
}

.btn-response {
    align-self: flex-start;
    padding: 10px 18px;
    background: #0072ff;
    color: #fff;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s;
    text-decoration: none;
}
.btn-response:hover {
    background: #0056b3;
    transform: scale(1.03);
}

@media(max-width: 576px){
    .query-grid {
        grid-template-columns: 1fr;
    }
}
</style>
</head>
<body>

<div class="container">
    <h1>User Queries</h1>
    <div class="query-grid">
        <?php if($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="query-card">
                    <div class="query-header">
                        <div class="user-name"><?= htmlspecialchars($row['username']); ?></div>
                        <div class="date"><?= date("d M Y, H:i", strtotime($row['created_at'])); ?></div>
                    </div>
                    <div class="query-email"><?= htmlspecialchars($row['email']); ?></div>
                    <div class="query-message"><?= nl2br(htmlspecialchars($row['message'])); ?></div>
                    <a href="admin_response.php?query_id=<?= $row['id']; ?>" class="btn-response">
                        <i class="fas fa-reply"></i> Respond
                    </a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="grid-column:1/-1; text-align:center; color:#555;">No queries found.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>

<?php $conn->close(); ?>
