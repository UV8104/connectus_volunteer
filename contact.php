<?php
session_start();

// Redirect if not logged in
if(!isset($_SESSION['user_id'])){
    die("You must be logged in to send a query.");
}

// Database connection
$servername = "localhost";
$dbUser = "root";
$dbPass = "";
$dbName = "users"; // database name

$conn = new mysqli($servername, $dbUser, $dbPass, $dbName);
if($conn->connect_error){
    die("Database connection failed: ".$conn->connect_error);
}

// Fetch logged-in user info
$user_id = $_SESSION['user_id'];
$user_query = $conn->query("SELECT username, email FROM users_login WHERE id='$user_id'");
if($user_query->num_rows == 0){
    die("User not found.");
}
$user = $user_query->fetch_assoc();

// Handle form submission
$formFeedback = "";
$feedbackColor = "green";

if($_SERVER['REQUEST_METHOD'] == "POST"){
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if(empty($name) || empty($email) || empty($message)){
        $formFeedback = "All fields are required.";
        $feedbackColor = "red";
    } elseif($name !== $user['username'] || $email !== $user['email']){
        $formFeedback = "Entered Name or Email does not match your registered account.";
        $feedbackColor = "red";
    } else {
        $stmt = $conn->prepare("INSERT INTO queries (user_id, name, email, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $user_id, $name, $email, $message);
        if($stmt->execute()){
            $formFeedback = "Your query has been sent. We will get back to you soon.";
            $feedbackColor = "green";
        } else {
            $formFeedback = "Database error: " . $stmt->error;
            $feedbackColor = "red";
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Contact Us - Volunteer Trust</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
/* Reset */
* { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
body { background-color: #f3f6f9; color: #333; }

/* Top Bar */
.top-bar {
    background: #0072ff;
    color: #fff;
    padding: 12px 20px;
    display: flex;
    justify-content: flex-end;
    align-items: center;
}
.top-bar .response-link {
    background: #fff;
    color: #0072ff;
    padding: 8px 14px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s;
}
.top-bar .response-link:hover { background: #e6e6e6; }

/* Container */
.contact-page {
    display: flex;
    flex-wrap: wrap;
    max-width: 1200px;
    margin: 30px auto;
    gap: 0;
    border-radius: 15px;
    overflow: hidden;
    background: #ADD8E6;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

/* Admin Info Panel */
.admin-info {
    flex: 0 0 40%;
    background: linear-gradient(135deg, #ADD8E6, #6495ED);
    color: #fff;
    padding: 30px 25px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    position: relative;
}
.admin-info::after {
    content: "";
    position: absolute;
    top: 10%;
    right: 0;
    width: 1px;
    height: 80%;
    background: rgba(255,255,255,0.5);
}
.contact-item { display: flex; align-items: center; margin: 15px 0; font-size: 16px; }
.contact-item i { margin-right: 12px; background: rgba(255,255,255,0.2); padding: 10px; border-radius: 50%; font-size: 16px; }
.social-links { margin-top: 20px; }
.social-links a { margin: 0 10px; color: #fff; font-size: 18px; transition: transform 0.3s, color 0.3s; }
.social-links a:hover { color: #ffe600; transform: scale(1.2); }

/* Query Form Panel */
.query-form {
    flex: 0 0 60%;
    padding: 35px 30px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}
.query-form h2 { margin-bottom: 25px; font-size: 24px; color: #0072ff; text-align: center; }
.query-form label { display: block; margin-bottom: 8px; font-weight: 600; }
.query-form input, .query-form textarea {
    width: 100%;
    padding: 14px 15px;
    margin-bottom: 18px;
    border-radius: 10px;
    border: 1px solid #ccc;
    font-size: 15px;
    transition: all 0.3s;
}
.query-form input:focus, .query-form textarea:focus {
    border-color: #0072ff;
    box-shadow: 0 0 8px rgba(0,114,255,0.3);
    outline: none;
}
.query-form button {
    background: #0072ff;
    color: #fff;
    padding: 14px 20px;
    border: none;
    border-radius: 10px;
    font-size: 16px;
    cursor: pointer;
    width: 100%;
    transition: all 0.3s;
}
.query-form button:hover { background: #0056b3; transform: scale(1.03); }
.form-feedback { margin-top: 12px; font-weight: bold; text-align: center; color: <?= $feedbackColor; ?>; }

/* Responsive */
@media (max-width: 991px) {
    .contact-page { flex-direction: column; }
    .admin-info, .query-form { flex: 0 0 100%; }
    .admin-info::after { display: none; }
}
@media (max-width: 576px) {
    .query-form h2 { font-size: 20px; }
    .contact-item i { font-size: 14px; padding: 8px; }
    .social-links a { font-size: 16px; margin: 0 8px; }
}
</style>
</head>
<body>

<!-- Top Bar -->
<div class="top-bar">
    <a href="user_response.php" class="response-link">Response</a>
</div>

<div class="contact-page">
    <!-- Admin Info -->
    <div class="admin-info">
        <h2>Contact here</h2>
        <div class="contact-item"><i class="fas fa-user-shield"></i> Managed by: Volunteer Trust</div>
        <div class="contact-item"><i class="fas fa-map-marker-alt"></i> 123 Main Street, Dadar West, Mumbai-400066, Maharashtra, India</div>
        <div class="contact-item"><i class="fas fa-phone"></i> +91-9876543210, 9988001122, 9988001133</div>
        <div class="contact-item"><i class="fas fa-envelope"></i> info@volunteer.org</div>
        <div class="social-links">
            <a href="#"><i class="fab fa-facebook-f"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
        </div>
    </div>

    <!-- User Query Form -->
    <div class="query-form">
        <h2>Send Us a Query</h2>
        <form method="POST" id="contactForm">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($_POST['name'] ?? $user['username']); ?>" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? $user['email']); ?>" required>

            <label for="message">Message</label>
            <textarea id="message" name="message" rows="6" placeholder="Your Query" required><?= htmlspecialchars($_POST['message'] ?? ''); ?></textarea>

            <button type="submit">Submit</button>
            <p class="form-feedback"><?= htmlspecialchars($formFeedback); ?></p>
        </form>
    </div>
</div>

</body>
</html>
