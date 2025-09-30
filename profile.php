<?php
// profile.php
session_start();
include 'db.php'; // Database connection

// Redirect to login if the user is not authenticated
if (!isset($_SESSION['username']) || !isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$user_email = $_SESSION['email'];
$message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $phone     = trim($_POST['phone']);
    $address   = trim($_POST['address']);
    $skills    = trim($_POST['skills']);

    // Basic validation
    if (!preg_match("/^[0-9]{10}$/", $phone)) {
        $message = "Phone number must be 10 digits!";
    } else {
        // Check if profile exists
        $check_stmt = $conn->prepare("SELECT id FROM profile_tbl WHERE email = ?");
        $check_stmt->bind_param("s", $user_email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        $profile_exists = $check_result->num_rows > 0;
        $check_stmt->close();

        if ($profile_exists && isset($_POST['update'])) {
            // Update
            $update_stmt = $conn->prepare("UPDATE profile_tbl 
                                           SET full_name=?, phone=?, address=?, skills=? 
                                           WHERE email=?");
            $update_stmt->bind_param("sssss", $full_name, $phone, $address, $skills, $user_email);
            if ($update_stmt->execute()) {
                // Redirect after update
                header("Location: user_dashboard.php?msg=Profile updated successfully");
                exit();
            } else {
                $message = "Error updating profile!";
            }
            $update_stmt->close();

        } elseif (!$profile_exists && isset($_POST['submit'])) {
            // Insert new
            $insert_stmt = $conn->prepare("INSERT INTO profile_tbl 
                                           (username, email, full_name, phone, address, skills) 
                                           VALUES (?, ?, ?, ?, ?, ?)");
            $insert_stmt->bind_param("ssssss", $username, $user_email, $full_name, $phone, $address, $skills);
            if ($insert_stmt->execute()) {
                // Redirect after insert
                header("Location: user_dashboard.php?msg=Profile created successfully");
                exit();
            } else {
                $message = "Error creating profile!";
            }
            $insert_stmt->close();
        }
    }
}

// Fetch existing profile data
$profile_data = ['full_name' => '', 'phone' => '', 'address' => '', 'skills' => ''];
$profile_exists = false;

$profile_stmt = $conn->prepare("SELECT * FROM profile_tbl WHERE email = ?");
$profile_stmt->bind_param("s", $user_email);
$profile_stmt->execute();
$profile_result = $profile_stmt->get_result();

if ($profile_result->num_rows > 0) {
    $profile_data = $profile_result->fetch_assoc();
    $profile_exists = true;
}
$profile_stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profile</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
        .container { max-width: 500px; margin: auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);}
        h2 { text-align: center; margin-bottom: 20px; }
        label { display: block; margin: 10px 0 5px; }
        input, textarea { width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 4px; }
        input[readonly] { background: #eee; }
        button { width: 100%; padding: 10px; border: none; border-radius: 4px; cursor: pointer; }
        .btn-submit { background: #28a745; color: #fff; }
        .btn-submit:hover { background: #218838; }
        .btn-update { background: #007bff; color: #fff; }
        .btn-update:hover { background: #0069d9; }
        .message { text-align: center; margin-bottom: 15px; color: green; }
        .error { color: red; text-align: center; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>User Profile</h2>
        <?php if (!empty($message)): ?>
            <div class="<?php echo strpos($message, 'Error') === false ? 'message' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <form method="POST">
            <label>Username:</label>
            <input type="text" value="<?php echo htmlspecialchars($username); ?>" readonly>
            
            <label>Email:</label>
            <input type="email" value="<?php echo htmlspecialchars($user_email); ?>" readonly>
            
            <label>Full Name:</label>
            <input type="text" name="full_name" value="<?php echo htmlspecialchars($profile_data['full_name']); ?>" required>
            
            <label>Phone Number:</label>
            <input type="text" name="phone" value="<?php echo htmlspecialchars($profile_data['phone']); ?>" required>
            
            <label>Address:</label>
            <textarea name="address" rows="3" required><?php echo htmlspecialchars($profile_data['address']); ?></textarea>
            
            <label>Skills:</label>
            <input type="text" name="skills" value="<?php echo htmlspecialchars($profile_data['skills']); ?>" required>
            
            <?php if ($profile_exists): ?>
                <button type="submit" name="update" class="btn-update">Update Profile</button>
            <?php else: ?>
                <button type="submit" name="submit" class="btn-submit">Submit Profile</button>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>
