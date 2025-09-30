<?php
session_start();
require_once "config.php"; // DB connection using PDO

// Make sure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch only this user's queries using PDO
$sql = "SELECT id, message, response, created_at 
        FROM queries 
        WHERE user_id = ? 
        ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Query Responses</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: start;
            padding: 40px 10px;
        }
        .container {
            max-width: 900px;
            width: 100%;
        }
        .page-title {
            text-align: center;
            font-size: 28px;
            font-weight: bold;
            color: #fff;
            margin-bottom: 40px;
            text-shadow: 0 2px 5px rgba(0,0,0,0.3);
        }
        .card {
            border-radius: 15px;
            margin-bottom: 25px;
            box-shadow: 0px 6px 15px rgba(0,0,0,0.2);
            overflow: hidden;
            border: none;
        }
        .card-header {
            font-weight: 600;
            background: #007bff;
            color: white;
            font-size: 18px;
            padding: 12px 20px;
        }
        .card-body {
            padding: 20px;
            background: #fff;
        }
        .no-query, .no-response {
            text-align: center;
            padding: 40px;
            font-size: 18px;
            font-weight: 500;
            color: #444;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0px 6px 15px rgba(0,0,0,0.15);
        }
        .back-link {
            display: inline-block;
            margin-top: 15px;
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="container">
    <h2 class="page-title">📩 My Query Responses</h2>

    <?php if (count($result) == 0): ?>
        <!-- Case 2: User has not asked any queries -->
        <div class="no-query">
            ❌ You have not asked any queries yet.
            <br>
            <a href="contact.php" class="back-link">← Back to Contact Page</a>
        </div>
    <?php else: ?>
        <?php foreach ($result as $row): ?>
            <div class="card">
                <div class="card-header">
                    Your Query
                </div>
                <div class="card-body">
                    <p><strong>Message:</strong> <?= htmlspecialchars($row['message']) ?></p>
                    <p><strong>Asked On:</strong> <?= date("d M Y, h:i A", strtotime($row['created_at'])) ?></p>

                    <?php if (!empty($row['response'])): ?>
                        <!-- Case 1: Admin has responded -->
                        <hr>
                        <p><strong>✅ Admin Response:</strong><br> <?= htmlspecialchars($row['response']) ?></p>
                    <?php else: ?>
                        <!-- Case 3: Admin has not responded -->
                        <div class="no-response mt-3">
                            ⏳ Admin has not responded yet.
                            <br>
                            <a href="contact.php" class="back-link">← Back to Contact Page</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</body>
</html>
