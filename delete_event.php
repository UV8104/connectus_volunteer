<?php
include 'db.php';

if (isset($_GET['id'])) {
    $event_id = $_GET['id'];

    // Fetch event details
    $sql = "SELECT banner, event_date FROM events WHERE event_id = $event_id";
    $result = $conn->query($sql);

    if ($result->num_rows == 0) {
        header("Location: admin_events.php");
        exit;
    } else {
        $row = $result->fetch_assoc();
        $event_start = $row['event_date'];

        // Check if event is deletable (at least 2 days away)
        $today = new DateTime();
        $startDate = new DateTime($event_start);
        $diff = $today->diff($startDate)->days;

        $can_delete = ($today < $startDate && $diff >= 2);
    }
}

// ✅ If delete confirmed
if (isset($_POST['confirm_delete']) && $_POST['confirm_delete'] == "yes") {
    $event_id = $_POST['event_id'];

    $sql = "SELECT banner, event_date FROM events WHERE event_id = $event_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $banner = $row['banner'];
        $event_start = $row['event_date'];

        $today = new DateTime();
        $startDate = new DateTime($event_start);
        $diff = $today->diff($startDate)->days;

        if ($today < $startDate && $diff >= 2) {
            // Delete the event
            $delete_sql = "DELETE FROM events WHERE event_id = $event_id";
            if ($conn->query($delete_sql) === TRUE) {
                if (!empty($banner) && file_exists("uploads/$banner")) {
                    unlink("uploads/$banner");
                }
            }
        }
    }
    // Always redirect silently
    header("Location: admin_events.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Delete Event</title>
<style>
  body {font-family: Arial, sans-serif; background:#f4f4f4;}
  .popup {
    position: fixed;
    top:0; left:0; right:0; bottom:0;
    background: rgba(0,0,0,0.5);
    display:flex;
    justify-content:center;
    align-items:center;
  }
  .popup-box {
    background:#fff;
    padding:20px;
    border-radius:8px;
    text-align:center;
    box-shadow:0 2px 8px rgba(0,0,0,0.3);
  }
  .popup-box h3 {margin-bottom:20px;}
  .popup-box button {
    padding:8px 15px;
    margin:5px;
    border:none;
    border-radius:5px;
    cursor:pointer;
  }
  .yes-btn {background:#dc3545; color:#fff;}
  .no-btn {background:#6c757d; color:#fff;}
</style>
</head>
<body>

<?php if (isset($can_delete) && $can_delete): ?>
<div class="popup">
  <div class="popup-box">
    <h3>Are you sure you want to delete this event?</h3>
    <form method="POST">
      <input type="hidden" name="event_id" value="<?php echo $event_id; ?>">
      <button type="submit" name="confirm_delete" value="yes" class="yes-btn">Yes</button>
      <button type="button" class="no-btn" onclick="window.location.href='admin_events.php'">No</button>
    </form>
  </div>
</div>
<?php else: ?>
<?php
// If deletion not allowed, redirect silently
header("Location: admin_events.php");
exit;
?>
<?php endif; ?>

</body>
</html>
