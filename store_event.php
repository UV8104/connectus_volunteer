<?php
include 'db.php'; // DB connection file

if (isset($_POST['create_event'])) {
    $title = $_POST['title'];
    $event_date = $_POST['event_date'];
    $start_time = $_POST['event_start_time'];
    $end_time = $_POST['event_time_end'];
    $volunteers_needed = $_POST['volunteers_needed'];
    $providing = $_POST['providing'];
    $description = $_POST['description'];
    $money = !empty($_POST['money']) ? $_POST['money'] : NULL;
    $location = $_POST['location'];

    // ✅ Compare Event Date with Today's Date
    $today = date("Y-m-d");
    if (strtotime($event_date) <= strtotime($today)) {
        echo "<p style='color: red; text-align:center; font-size:18px;'>
                Event should be created at least one day before the event date. 
                Past or same-day events are not allowed.
              </p>";
        // ❌ Stop here – don't insert, don't redirect
        exit();
    }

    // Banner Upload
    $banner = NULL;
    if (!empty($_FILES['banner']['name'])) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $banner = time() . "_" . basename($_FILES['banner']['name']);
        $targetFile = $targetDir . $banner;
        move_uploaded_file($_FILES['banner']['tmp_name'], $targetFile);
    }

    // Insert Query
    $sql = "INSERT INTO events (title, event_date, event_start_time, event_time_end, volunteer_needed, providing, description, money, location, banner)
            VALUES ('$title', '$event_date', '$start_time', '$end_time', '$volunteers_needed', '$providing', '$description', '$money', '$location', '$banner')";

    // ✅ Success / Error Handling
    if ($conn->query($sql) === TRUE) {
        echo "<p style='color: green; text-align:center; font-size:18px;'>Event created successfully!</p>";
        echo "<script>
                setTimeout(function(){
                    window.location.href='admin_dashboard.php';
                }, 2000); // redirect only after success
              </script>";
    } else {
        echo "<p style='color: red; text-align:center; font-size:18px;'>Error: " . $conn->error . "</p>";
    }
}
?>
