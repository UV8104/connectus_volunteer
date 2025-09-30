<?php
session_start();
require_once "db.php"; // ✅ include your DB connection

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_SESSION['user_id'];
    $easy_to_use = $_POST['easy_to_use'];
    $useful = $_POST['useful'];
    $likes = $_POST['likes'];
    $suggestions = $_POST['suggestions'];
    $rating = $_POST['rating'];

    // ✅ Prepare SQL to prevent SQL injection
    $sql = "INSERT INTO feedback_tbl (user_id, easy_to_use, useful, likes, suggestions, rating) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssi", $user_id, $easy_to_use, $useful, $likes, $suggestions, $rating);

    if ($stmt->execute()) {
        echo "
        <div style='
            text-align:center;
            margin-top:50px;
            font-size:18px;
            color:green;
            font-weight:bold;
        '>
            ✅ Feedback submitted successfully!
        </div>
        <script>
            setTimeout(function(){
                window.location.href = 'feedback_form.php';
            }, 1000); // redirect after 1 second
        </script>";
    } else {
        echo "<div style='color:red; text-align:center; margin-top:50px;'>❌ Error: " . $stmt->error . "</div>";
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: feedback_form.php");
    exit();
}
?>
