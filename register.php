<?php
session_start();

// Database configuration
$host = "localhost";
$dbname = "users";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}

$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = trim($_POST['username']);
    $email = trim($_POST['email']);
    $pass = $_POST['password'];
    $cpass = $_POST['confirm_password'];

    // Check password match
    if ($pass !== $cpass) {
        $error = "Password mismatch";
    } else {
        // Hash the password
        $hashedPassword = password_hash($pass, PASSWORD_DEFAULT);

        // Insert into database securely
        $stmt = $pdo->prepare("INSERT INTO users_login (username, email, password) VALUES (:username, :email, :password)");
        try {
            $stmt->execute([
                ':username' => $user,
                ':email' => $email,
                ':password' => $hashedPassword
            ]);

            // Redirect to login page after success
            header("Location: login.php");
            exit();
        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Registration</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: linear-gradient(to right, #6a11cb, #2575fc);
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .form-box {
      background: #fff;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.2);
      width: 320px;
    }
    .form-box h2 {
      text-align: center;
      color: #333;
    }
    .form-box input {
      width: 100%;
      padding: 10px;
      margin: 8px 0;
      border: 1px solid #ccc;
      border-radius: 8px;
    }
    .form-box button {
      width: 100%;
      padding: 10px;
      border: none;
      background: #2575fc;
      color: white;
      border-radius: 8px;
      cursor: pointer;
      font-size: 16px;
    }
    .form-box button:hover {
      background: #6a11cb;
    }
    .error {
      color: red;
      font-size: 14px;
      margin-top: -5px;
      margin-bottom: 10px;
    }
  </style>
</head>
<body>
  <div class="form-box">
    <h2>Sign Up</h2>
    <form method="POST" action="">
      <input type="text" name="username" placeholder="Username" required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <input type="password" name="confirm_password" placeholder="Confirm Password" required>
      <?php if (!empty($error)): ?>
        <div class="error"><?php echo $error; ?></div>
      <?php endif; ?>
      <button type="submit">Sign Up</button>
    </form>
  </div>
</body>
</html>
