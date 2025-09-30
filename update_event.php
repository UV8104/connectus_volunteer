<?php
include 'db.php';

// Check if ID is provided
if (isset($_GET['id'])) {
    $event_id = $_GET['id'];

    // Fetch the event
    $sql = "SELECT * FROM events WHERE event_id = $event_id";
    $result = $conn->query($sql);
    $event = $result->fetch_assoc();

    // If event not found, redirect back
    if (!$event) {
        header("Location: admin_events.php");
        exit;
    }

    // Check if update is allowed (at least 1 day before event)
    $today = new DateTime();
    $eventDate = new DateTime($event['event_date']);
    $diff_days = (int)$today->diff($eventDate)->format("%r%a"); // difference in days with sign

    $can_update = ($diff_days >= 1); // Update allowed only if 1+ day away

} else {
    // No ID provided, redirect back
    header("Location: admin_events.php");
    exit;
}

// Handle form submission
if ($can_update && isset($_POST['update_event'])) {
    $title = $_POST['title'];
    $event_date = $_POST['event_date'];
    $event_start_time = $_POST['event_start_time'];
    $event_time_end = $_POST['event_time_end'];
    $volunteer_needed = $_POST['volunteer_needed'];
    $providing = $_POST['providing'];
    $location = $_POST['location'];
    $description = $_POST['description'];
    $money = $_POST['money']; // new money field

    // Check if a new banner is uploaded
    if (!empty($_FILES['banner']['name'])) {
        $banner = time() . "_" . $_FILES['banner']['name'];
        move_uploaded_file($_FILES['banner']['tmp_name'], "uploads/" . $banner);

        $update_sql = "UPDATE events 
                       SET title='$title', event_date='$event_date', event_start_time='$event_start_time', event_time_end='$event_time_end', 
                           volunteer_needed='$volunteer_needed', providing='$providing', location='$location', 
                           description='$description', money='$money', banner='$banner' 
                       WHERE event_id=$event_id";
    } else {
        $update_sql = "UPDATE events 
                       SET title='$title', event_date='$event_date', event_start_time='$event_start_time', event_time_end='$event_time_end', 
                           volunteer_needed='$volunteer_needed', providing='$providing', location='$location', 
                           description='$description', money='$money'
                       WHERE event_id=$event_id";
    }

    if ($conn->query($update_sql) === TRUE) {
        echo "
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Event Updated</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background: #f8f9fa;
                    text-align: center;
                    padding: 100px;
                }
                .success {
                    display: inline-block;
                    padding: 15px 25px;
                    background: #28a745;
                    color: white;
                    font-size: 18px;
                    font-weight: bold;
                    border-radius: 6px;
                    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                }
            </style>
            <script>
                setTimeout(function(){
                    window.location.href = 'admin_events.php';
                }, 1000);
            </script>
        </head>
        <body>
            <div class='success'>✅ Event updated successfully!</div>
        </body>
        </html>
        ";
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
} elseif (!$can_update) {
    // Event too close, redirect silently
    header("Location: admin_events.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Update Event</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f8f9fa;
      margin: 0;
      padding: 0;
    }
    .container {
      width: 500px;
      margin: 40px auto;
      background: #fff;
      padding: 20px 30px;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #333;
    }
    label {
      display: block;
      font-weight: bold;
      margin-top: 10px;
      color: #444;
    }
    input, textarea, button {
      width: 100%;
      padding: 8px 10px;
      margin-top: 6px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 14px;
    }
    textarea {
      height: 80px;
      resize: none;
    }
    button {
      background: #007bff;
      color: #fff;
      border: none;
      margin-top: 15px;
      cursor: pointer;
      font-size: 16px;
      border-radius: 6px;
      transition: 0.3s;
    }
    button:hover {
      background: #0056b3;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Update Event</h2>

    <?php if ($can_update): ?>
      <form action="" method="POST" enctype="multipart/form-data">
        <label>Title:</label>
        <input type="text" name="title" value="<?php echo $event['title']; ?>" required><br><br>

        <label>Date:</label>
        <input type="date" name="event_date" value="<?php echo $event['event_date']; ?>" required><br><br>

        <label>Start Time:</label>
        <input type="time" name="event_start_time" value="<?php echo $event['event_start_time']; ?>" required><br><br>

        <label>End Time:</label>
        <input type="time" name="event_time_end" value="<?php echo $event['event_time_end']; ?>" required><br><br>

        <label>Volunteer Needed:</label>
        <input type="number" name="volunteer_needed" value="<?php echo $event['volunteer_needed']; ?>" required><br><br>

        <label>Providing:</label>
        <input type="text" name="providing" value="<?php echo $event['providing']; ?>"><br><br>

        <label>Location:</label>
        <input type="text" name="location" value="<?php echo $event['location']; ?>" required><br><br>

        <label>Description:</label>
        <textarea name="description" rows="4" required><?php echo $event['description']; ?></textarea><br><br>

        <label>Money:</label>
        <input type="number" step="0.01" name="money" value="<?php echo $event['money']; ?>" required><br><br>

        <label>Banner (Leave empty to keep old):</label>
        <input type="file" name="banner"><br><br>

        <button type="submit" name="update_event" class="update-btn">Update Event</button>
      </form>
    <?php endif; ?>

  </div>
</body>
</html>
