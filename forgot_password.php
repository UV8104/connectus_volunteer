<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// 🔹 Database connection
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "users";  // change to your database name

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 🔹 Initialize variables
$message = "";
$color   = "";

// 🔹 Handle form submit
if (isset($_POST['submit'])) {
    $email = $_POST['email'];

    // Check if email exists
    $stmt = $conn->prepare("SELECT id FROM users_login WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Generate reset code (6-digit) and expiry time
        $code  = rand(100000, 999999);
        $expir = date("Y-m-d H:i:s", strtotime("+10 minutes"));

        // Save in DB
        $stmt2 = $conn->prepare("UPDATE users_login SET reset_code=?, code_expiry=? WHERE email=?");
        $stmt2->bind_param("sss", $code, $expir, $email);
        $stmt2->execute();

        // Send email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'your working email';           // your Gmail
            $mail->Password   = 'your app password';     // Gmail App Password
            $mail->SMTPSecure = 'tls';                            // use TLS
            $mail->Port       = 587;

            // 🔹 Fix SSL issue for local XAMPP
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer'       => false,
                    'verify_peer_name'  => false,
                    'allow_self_signed' => true
                ]
            ];

            $mail->setFrom('vijayutnoori81@gmail.com', 'Volunteer Management System');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Code';
            $mail->Body    = "Your 6-digit reset code is: <b>$code</b><br>This code will expire in 10 minutes.";

            $mail->send();

            $_SESSION['email'] = $email;
            header("Location: enter_code.php"); // go to code verification page
            exit;
        } catch (Exception $e) {
            $message = "Mailer Error: {$mail->ErrorInfo}";
            $color   = "red";
        }
    } else {
        $message = "Email does not exist!";
        $color   = "red";
    }
}
?>

<!-- HTML Form -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            width: 350px;
            text-align: center;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        input[type="email"] {
            width: 90%;
            padding: 10px;
            margin: 15px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            width: 95%;
            padding: 10px;
            background: green;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover { background: darkgreen; }
        .message { margin-top: 10px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Forgot Password</h2>
        <form method="POST">
            <input type="email" name="email" placeholder="Enter your email" required>
            <button type="submit" name="submit">Continue</button>
        </form>
        <?php if (!empty($message)) { ?>
            <p class="message" style="color: <?= $color; ?>;"><?= $message; ?></p>
        <?php } ?>
        <p>Want to login? <a href="login.php">Login</a></p>
    </div>
</body>
</html>

