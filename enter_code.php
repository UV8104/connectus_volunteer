<?php
session_start();
require 'db.php';
$message = '';
$color = 'green';

if (!isset($_SESSION['email'])) {
    header("Location: forgot_password.php");
    exit();
}

$email = $_SESSION['email'];

if (isset($_POST['submit'])) {
    $entered_code = $_POST['code'];

    if (strlen($entered_code) != 6) {
        $message = "Entered code is invalid";
        $color = "red";
    } else {
        // ✅ Use $conn_users for users database
        $stmt = $conn_users->prepare("SELECT reset_code, code_expiry FROM users_login WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if (!$result || $entered_code != $result['reset_code']) {
            $message = "You entered an invalid code";
            $color = "red";
        } elseif (strtotime($result['code_expiry']) < time()) {
            $message = "Code has expired, request a new one.";
            $color = "red";
        } else {
            $_SESSION['code_verified'] = true;
            header("Location: reset_password.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<style>
body{font-family:Arial;background:#f0f0f0;}
.container{width:400px;margin:100px auto;padding:30px;background:#fff;border-radius:8px;box-shadow:0 0 10px rgba(0,0,0,0.1);}
input[type=text], input[type=submit]{width:100%;padding:10px;margin:10px 0;border-radius:5px;border:1px solid #ccc;}
input[type=submit]{background:#28a745;color:#fff;border:none;cursor:pointer;}
.message{color:<?php echo $color;?>;font-weight:bold;}
</style>
</head>
<body>
<div class="container">
<h2>Enter 6-digit Code</h2>
<p class="message">6-digit code sent to your email</p>
<form method="POST">
<input type="text" name="code" placeholder="Enter 6-digit code" required>
<input type="submit" name="submit" value="Submit">
<div class="message"><?php echo $message;?></div>
</form>
</div>
</body>
</html>
