<?php
session_start();
include 'db.php';

// ✅ Check login
if (!isset($_SESSION['username']) || !isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$user_email = $_SESSION['email'];

// ✅ Get event id
if (!isset($_GET['event_id'])) {
    die("Invalid request!");
}
$event_id = intval($_GET['event_id']);

// ✅ Fetch current event details (date + time)
$event_sql = $conn->prepare("SELECT event_date, event_start_time FROM events WHERE event_id = ?");
$event_sql->bind_param("i", $event_id);
$event_sql->execute();
$event_res = $event_sql->get_result();
if ($event_res->num_rows == 0) {
    die("Event not found!");
}
$event = $event_res->fetch_assoc();
$event_date = $event['event_date'];
$event_time = $event['event_start_time'];
$event_datetime = strtotime("$event_date $event_time");

// ✅ Fetch user profile if requested
$profile_data = ['full_name'=>'', 'phone'=>'', 'address'=>'', 'skills'=>''];
if (isset($_GET['fetch']) && $_GET['fetch'] == 1) {
    $stmt = $conn->prepare("SELECT full_name, phone, address, skills FROM profile_tbl WHERE email = ?");
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $profile_data = $result->fetch_assoc();
    }
    $stmt->close();
}

// ✅ Handle form submission
$message = "";
$message_type = "error"; // default red
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $skills = trim($_POST['skills']);

    // 🔹 Validate phone number (10 digits)
    if (!preg_match("/^[0-9]{10}$/", $phone)) {
        $message = "Invalid phone number! Must be 10 digits.";
    } else {
        // 🔹 Check 12-hour deadline
        $now = time();
        $deadline = $event_datetime - (12 * 3600); // 12 hours before event start
        if ($now > $deadline) {
            $message = "⛔ You cannot apply! Applications close 12 hours before the event.";
        } else {
            // 🔹 Check duplicate application for same event
            $check = $conn->prepare("SELECT user_id FROM user_applications WHERE event_id = ? AND email = ?");
            $check->bind_param("is", $event_id, $user_email);
            $check->execute();
            $res = $check->get_result();

            if ($res->num_rows > 0) {
                $message = "⚠️ You have already applied for this event!";
            } else {
                // 🔹 Check for conflict with another event (same date & start time)
                $conflict = $conn->prepare("SELECT ua.user_id 
                    FROM user_applications ua
                    JOIN events e ON ua.event_id = e.event_id
                    WHERE ua.email = ? 
                      AND e.event_date = ? 
                      AND e.event_start_time = ?");
                $conflict->bind_param("sss", $user_email, $event_date, $event_time);
                $conflict->execute();
                $conf_res = $conflict->get_result();

                if ($conf_res->num_rows > 0) {
                    $message = "⚠️ You cannot apply! You already applied for another event at the same date & time.";
                } else {
                    // 🔹 Insert new application
                   $insert = $conn->prepare("INSERT INTO user_applications 
    (user_id, event_id, username, email, full_name, phone, address, skills, applied_at) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
$insert->bind_param("iissssss", $_SESSION['user_id'], $event_id, $username, $user_email, $full_name, $phone, $address, $skills);

                    if ($insert->execute()) {
                        $message = "✅ Application submitted successfully!";
                        $message_type = "success";

                        // ✅ Success message with spinner + redirect
                        echo "
                        <div style='
                            color: green;
                            text-align: center;
                            font-size: 18px;
                            font-weight: bold;
                            margin-top: 50px;
                        '>
                            $message
                            <div class='spinner'></div>
                            <p style='font-size:14px;color:#555;'>Redirecting to dashboard...</p>
                        </div>
                        <style>
                            .spinner {
                                margin: 20px auto;
                                border: 4px solid #f3f3f3;
                                border-top: 4px solid green;
                                border-radius: 50%;
                                width: 30px;
                                height: 30px;
                                animation: spin 1s linear infinite;
                            }
                            @keyframes spin {
                                0% { transform: rotate(0deg); }
                                100% { transform: rotate(360deg); }
                            }
                        </style>
                        <script>
                            setTimeout(function(){
                                window.location.href = 'user_dashboard.php';
                            }, 1000);
                        </script>";
                        exit();
                    } else {
                        $message = "❌ Error submitting application!";
                    }
                    $insert->close();
                }
                $conflict->close();
            }
            $check->close();
        }
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Apply for Event</title>
    <style>
        body { font-family: Arial, sans-serif; background: #eef2f7; padding: 20px; }
        .container { max-width: 600px; margin: auto; background: #fff; padding: 20px; border-radius: 8px;
                     box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #333; }
        label { display: block; margin: 8px 0 4px; }
        input, textarea { width: 100%; padding: 8px; margin-bottom: 12px; border: 1px solid #ccc; border-radius: 5px; }
        button { padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer; }
        .btn-submit { background: #28a745; color: white; }
        .btn-submit:hover { background: #218838; }
        .btn-fetch { background: #007bff; color: white; margin-bottom: 10px; text-decoration: none; display: inline-block; padding: 8px 12px; border-radius: 5px; }
        .btn-fetch:hover { background: #0056b3; }
        .error { color: red; text-align: center; font-weight: bold; }
        .success { color: green; text-align: center; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Apply for Event</h2>
        <?php if ($message && $message_type !== "success"): ?>
            <p class="error"><?php echo $message; ?></p>
        <?php endif; ?>
        
        <a href="apply_event.php?event_id=<?php echo $event_id; ?>&fetch=1" class="btn-fetch">Fetch Profile Details</a>
        
        <form method="POST">
            <label>Username:</label>
            <input type="text" value="<?php echo htmlspecialchars($username); ?>" readonly>

            <label>Email:</label>
            <input type="email" value="<?php echo htmlspecialchars($user_email); ?>" readonly>

            <label>Full Name:</label>
            <input type="text" name="full_name" value="<?php echo htmlspecialchars($profile_data['full_name']); ?>" required>

            <label>Phone:</label>
            <input type="text" name="phone" value="<?php echo htmlspecialchars($profile_data['phone']); ?>" required>

            <label>Address:</label>
            <textarea name="address" rows="3" required><?php echo htmlspecialchars($profile_data['address']); ?></textarea>

            <label>Skills:</label>
            <input type="text" name="skills" value="<?php echo htmlspecialchars($profile_data['skills']); ?>" required>

            <button type="submit" class="btn-submit">Submit Application</button>
        </form>
    </div>
</body>
</html>
