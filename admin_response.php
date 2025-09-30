<?php
session_start();
require_once 'config.php';

// ✅ Handle response submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['query_id'])) {
    $query_id = $_POST['query_id'];
    $response = trim($_POST['response']);

    $stmt = $pdo->prepare("UPDATE queries SET response = ?, responded_at = NOW() WHERE id = ?");
    $stmt->execute([$response, $query_id]);

    $_SESSION['success'] = "✅ Response sent!";
    header("Location: admin_response.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin - Manage Queries</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
    <h3>Manage Queries</h3>
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <?php
    $stmt = $pdo->query("SELECT q.*, u.username 
                         FROM queries q 
                         JOIN users_login u ON q.user_id = u.id 
                         ORDER BY q.created_at DESC");
    while ($row = $stmt->fetch()) {
        echo "<div class='card p-3 mb-3'>";
        echo "<strong>User:</strong> " . htmlspecialchars($row['username']) . " (" . $row['email'] . ")<br>";
        echo "<strong>Query:</strong> " . htmlspecialchars($row['message']) . "<br>";
        echo "<small><i>Asked on: " . $row['created_at'] . "</i></small><br><br>";

        if ($row['response']) {
            echo "<strong>Response:</strong> " . htmlspecialchars($row['response']) . "<br>";
            if (!empty($row['responded_at'])) {
                echo "<small><i>Replied on: " . $row['responded_at'] . "</i></small>";
            }
        } else {
            echo "<form method='POST' class='mt-2'>";
            echo "<input type='hidden' name='query_id' value='" . $row['id'] . "'>";
            echo "<textarea name='response' class='form-control mb-2' required placeholder='Write response...'></textarea>";
            echo "<button type='submit' class='btn btn-success btn-sm'>Send Response</button>";
            echo "</form>";
        }
        echo "</div>";
    }
    ?>
</div>
</body>
</html>
