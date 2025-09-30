<?php
include 'db.php';

// Get today's date
$today = date("Y-m-d");
$cutoff = date('Y-m-d', strtotime('-7 days')); // events older than 7 days

// --- Start transaction for safe deletion
$conn->begin_transaction();

try {
    // 1) Delete attendance rows linked to old events
    $sqlDeleteAttendance = "
        DELETE attendance
        FROM attendance
        JOIN events ON attendance.event_id = events.event_id
        WHERE events.event_date < ?
    ";
    $stmt = $conn->prepare($sqlDeleteAttendance);
    $stmt->bind_param("s", $cutoff);
    $stmt->execute();
    $stmt->close();

    // 2) Delete old events
    $sqlDeleteEvents = "DELETE FROM events WHERE event_date < ?";
    $stmt2 = $conn->prepare($sqlDeleteEvents);
    $stmt2->bind_param("s", $cutoff);
    $stmt2->execute();
    $stmt2->close();

    // Commit changes
    $conn->commit();
} catch (Exception $e) {
    // Rollback on error
    $conn->rollback();
    error_log("Auto-delete old events failed: " . $e->getMessage());
}

// Check which type of events to show (default upcoming)
$type = isset($_GET['type']) ? $_GET['type'] : 'upcoming';

if ($type == "previous") {
    // Show only events from the last 7 days
    $sql = "SELECT * FROM events 
            WHERE event_date < '$today' 
            AND event_date >= DATE_SUB('$today', INTERVAL 7 DAY) 
            ORDER BY event_date DESC";
    $heading = "Previous Events (Last 7 Days)";
} else {
    // Upcoming events
    $sql = "SELECT * FROM events WHERE event_date >= '$today' ORDER BY event_date ASC";
    $heading = "Upcoming Events";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - <?php echo $heading; ?></title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f9f9f9;
      margin: 0;
      padding: 20px;
    }
    h2 {
      text-align: center;
      margin-bottom: 20px;
      font-size: 26px;
    }
    .events-container {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 20px;
      max-width: 900px;
      margin: auto;
    }
    .event-card {
      background: white;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      overflow: hidden;
      width: 10cm;
      transition: transform 0.2s;
    }
    .event-card:hover {
      transform: translateY(-5px);
    }
    .event-banner {
      width: 100%;
      height: 10cm;
      overflow: hidden;
    }
    .event-banner img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    .event-details {
      padding: 15px;
    }
    .event-details h3 {
      margin: 0 0 10px;
      color: #333;
      font-size: 20px;
    }
    .event-details p {
      margin: 4px 0;
      color: #555;
      font-size: 14px;
    }
    .btn-container {
      margin-top: 12px;
      text-align: center;
    }
    .btn {
      display: inline-block;
      padding: 8px 14px;
      margin: 0 5px;
      border-radius: 6px;
      text-decoration: none;
      font-size: 14px;
      font-weight: bold;
    }
    .btn-edit {
      background-color: #28a745;
      color: white;
    }
    .btn-delete {
      background-color: #dc3545;
      color: white;
    }
    .warning {
      color: red;
      text-align: center;
      font-size: 14px;
      margin-bottom: 15px;
    }
  </style>
</head>
<body>

  <h2><?php echo $heading; ?></h2>

  <?php if ($type == "previous"): ?>
    <p class="warning">⚠️ Only events from the last 7 days are shown.</p>
  <?php endif; ?>

  <div class="events-container">
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
    ?>
      <div class="event-card">
        <div class="event-banner">
          <?php if (!empty($row['banner'])): ?>
            <img src="uploads/<?php echo $row['banner']; ?>" alt="Event Banner">
          <?php else: ?>
            <img src="uploads/default.jpg" alt="Default Banner">
          <?php endif; ?>
        </div>
        <div class="event-details">
          <h3><?php echo $row['title']; ?></h3>
          <p><strong>Date:</strong> <?php echo $row['event_date']; ?></p>
          <p><strong>Time:</strong> <?php echo $row['event_start_time'] . " - " . $row['event_time_end']; ?></p>
          <p><strong>Volunteers Needed:</strong> <?php echo $row['volunteer_needed']; ?></p>
          <p><strong>Providing:</strong> <?php echo $row['providing']; ?></p>
          <p><strong>Money:</strong> ₹<?php echo $row['money']; ?></p>
          <p><strong>Location:</strong> <?php echo $row['location']; ?></p>
          <p><strong>Description:</strong> <?php echo $row['description']; ?></p>

          <?php if ($type == "upcoming"): ?>
          <div class="btn-container">
            <a href="update_event.php?id=<?php echo $row['event_id']; ?>" class="btn btn-edit">Update</a>
            <a href="delete_event.php?id=<?php echo $row['event_id']; ?>" class="btn btn-delete">Delete</a>
          </div>
          <?php endif; ?>
        </div>
      </div>
    <?php
        }
    } else {
        echo "<p style='text-align:center;'>No " . strtolower($heading) . " found.</p>";
    }
    ?>
  </div>

</body>
</html>
