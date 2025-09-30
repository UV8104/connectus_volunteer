<?php
session_start();
require_once 'config.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user  = trim($_POST['username']);
    $email = trim($_POST['email']);
    $pass  = trim($_POST['password']);
    $cpass = trim($_POST['confirm_password']);

    if (strlen($user) < 3) {
        $error = "❌ Username must be at least 3 characters long.";
    } elseif (strlen($pass) < 8) {
        $error = "❌ Password must be at least 8 characters long.";
    } elseif (!preg_match("/^[a-zA-Z0-9._%+-]+@gmail\.com$/", $email)) {
        $error = "❌ Email must be a valid Gmail address (ending with @gmail.com).";
    } elseif ($pass !== $cpass) {
        $error = "❌ Passwords do not match.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users_login WHERE username = ? OR email = ?");
            $stmt->execute([$user, $email]);

            if ($stmt->rowCount() > 0) {
                $error = "⚠️ Username or Email already exists.";
            } else {
                $hashedPassword = password_hash($pass, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users_login (username, email, password) VALUES (?, ?, ?)");
                $stmt->execute([$user, $email, $hashedPassword]);
                $_SESSION['success'] = "✅ Registration successful! Please login.";
                header("Location: login.php");
                exit();
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Register</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    background: linear-gradient(135deg, #6a11cb, #2575fc);
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.card {
    max-width: 450px;
    width: 100%;
    padding: 30px;
    border-radius: 20px;
    box-shadow: 0px 8px 20px rgba(0,0,0,0.2);
}

.form-control {
    border-radius: 12px;
    height: 45px;
}
</style>
</head>
<body>

<div class="card">
<h3 class="text-center mb-4">Create Account</h3>

<?php if (!empty($error)): ?>
<div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<form method="POST">
    <!-- Username -->
    <div class="mb-3">
        <label class="form-label">Username</label>
        <input type="text" name="username" class="form-control" required>
    </div>

    <!-- Email -->
    <div class="mb-3">
        <label class="form-label">Email (@gmail.com)</label>
        <input type="email" name="email" class="form-control" required>
    </div>

    <!-- Password -->
    <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" id="password" name="password" class="form-control" required>
    </div>

    <!-- Confirm Password -->
    <div class="mb-3">
        <label class="form-label">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary w-100">Register</button>
</form>

<p class="text-center mt-3">Already have an account? <a href="login.php">Login</a></p>
</div>

</body>
</html>
