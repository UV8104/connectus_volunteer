<?php
session_start();
require_once 'config.php'; // DB connection

$error = "";

// Handle login form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        // Check user in DB
        $stmt = $pdo->prepare("SELECT * FROM users_login WHERE username = :username OR email = :username LIMIT 1");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // ✅ Save user info in session
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['username']  = $user['username'];
            $_SESSION['email']     = $user['email'];
            $_SESSION['role']      = $user['role'];    // admin/user

            // Redirect by role
            if ($user['role'] === 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: user_dashboard.php");
            }
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Please enter both fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <!-- ✅ Font Awesome CDN for professional icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      background: linear-gradient(to right, #ADD8E6, #6495ED);
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
      padding: 0;
    }
    .login-box {
      background: #fff;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
      width: 100%;
      max-width: 350px;   /* ✅ good on laptops */
      min-width: 300px;   /* ✅ not too small on mobile */
      box-sizing: border-box;
    }
    .login-box h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #333;
    }
    input {
      width: 100%;
      padding: 10px;
      margin: 8px 0;
      border: 1px solid #ccc;
      border-radius: 6px;
      box-sizing: border-box;
    }
    button {
      width: 100%;
      padding: 10px;
      background: #007bff;
      border: none;
      color: white;
      font-size: 16px;
      border-radius: 6px;
      cursor: pointer;
      box-sizing: border-box;
    }
    button:hover {
      background: #0056b3;
    }
    .error {
      color: red;
      font-size: 14px;
      margin: 5px 0;
    }
    .create-account {
      margin-top: 12px;
      text-align: center;
    }
    .create-account a {
      color: #007bff;
      text-decoration: none;
    }
    /* Password field with eye icon */
    .password-wrapper {
  position: relative;
  width: 100%;
  box-sizing: border-box; /* ✅ Fixes overflow */
}
.password-wrapper input {
  width: 100%;
  padding-right: 40px;  /* space for eye icon */
  box-sizing: border-box; /* ✅ Prevents overflow */
}
.toggle-password {
  position: absolute;
  right: 10px;
  top: 50%;
  transform: translateY(-50%);
  cursor: pointer;
  color: #555;
  font-size: 18px;
}

    .toggle-password:hover {
      color: #000;
    }
    /* Forgot password link */
    .forgot-password {
      text-align: right;
      margin: -5px 0 10px 0;
    }
    .forgot-password a {
      font-size: 14px;
      color: #007bff;
      text-decoration: none;
    }
    .forgot-password a:hover {
      text-decoration: underline;
    }
    /* ✅ Mobile responsiveness */
    @media (max-width: 480px) {
      body {
        padding: 10px;
      }
      .login-box {
        padding: 20px;
        max-width: 90%;  /* scales smoothly */
      }
      .login-box h2 {
        font-size: 22px;
      }
      button {
        font-size: 16px;
      }
    }
  </style>
  <script>
    // Disable right-click
    document.addEventListener('contextmenu', function(e) {
      e.preventDefault();
      alert("Right click is disabled on this page!");
    });

    // Toggle password visibility with icon change
    function togglePassword() {
      const passwordField = document.getElementById("password");
      const toggleIcon = document.getElementById("toggleIcon");
      
      if (passwordField.type === "password") {
        passwordField.type = "text";
        toggleIcon.classList.remove("fa-eye");
        toggleIcon.classList.add("fa-eye-slash");
      } else {
        passwordField.type = "password";
        toggleIcon.classList.remove("fa-eye-slash");
        toggleIcon.classList.add("fa-eye");
      }
    }
  </script>
</head>
<body>
  <div class="login-box">
    <h2>Login</h2>
    <form method="POST" action="">
      <input type="text" name="username" placeholder="Username or Email" required>

      <!-- Password with professional eye icon -->
      <div class="password-wrapper">
        <input type="password" id="password" name="password" placeholder="Password" required>
        <i class="fa-solid fa-eye toggle-password" id="toggleIcon" onclick="togglePassword()"></i>
      </div>

      <!-- Forgot password link -->
      <div class="forgot-password">
        <a href="forgot_password.php">Forgot Password?</a>
      </div>

      <?php if (!empty($error)): ?>
        <div class="error"><?php echo $error; ?></div>
      <?php endif; ?>

      <button type="submit">Login</button>
    </form>
    <div class="create-account">
      <p>Don't have an account? <a href="registration.php">Create Account</a></p>
    </div>
  </div>
</body>
</html>
