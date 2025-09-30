<?php
session_start();
require 'db.php';   // db.php should have $conn_users and $conn_events
$message = '';
$color = '';

// Only allow if email and code_verified exist
if (!isset($_SESSION['email']) || !isset($_SESSION['code_verified'])) {
    header("Location: forgot_password.php");
    exit();
}

$email = $_SESSION['email'];

if (isset($_POST['submit'])) {
    $password = $_POST['password'];
    $confirm  = $_POST['confirm'];

    if (strlen($password) < 8) {
        $message = "Password must be at least 8 characters";
        $color = "red";
    } elseif ($password != $confirm) {
        $message = "Passwords do not match";
        $color = "red";
    } else {
        $hashed = password_hash($password, PASSWORD_BCRYPT);

        // ✅ Use the correct connection for `users` DB
        $stmt = $conn_users->prepare(
            "UPDATE users_login 
             SET password=?, reset_code=NULL, code_expiry=NULL 
             WHERE email=?"
        );
        $stmt->bind_param("ss", $hashed, $email);

        if ($stmt->execute()) {
            unset($_SESSION['email']);
            unset($_SESSION['code_verified']);
            $message = "Password reset successful! You can login now.";
            $color = "green";
        } else {
            $message = "Something went wrong. Please try again.";
            $color = "red";
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
input[type=password], input[type=submit]{width:100%;padding:10px;margin:10px 0;border-radius:5px;border:1px solid #ccc;}
input[type=submit]{background:#28a745;color:#fff;border:none;cursor:pointer;}
.message{color:<?php echo $color;?>;font-weight:bold;}
.login-link{margin-top:10px;display:block;text-align:center;}
</style>
</head>
<body>
<div class="container">
<h2>Reset Password</h2>
<form method="POST">
    <input type="password" name="password" placeholder="New Password" required>
    <input type="password" name="confirm" placeholder="Confirm Password" required>
    <input type="submit" name="submit" value="Submit">
    <div class="message"><?php echo $message;?></div>
    <div class="login-link"><a href="login.php">Now you can login!</a></div>
</form>
</div>
</body>
</html>
