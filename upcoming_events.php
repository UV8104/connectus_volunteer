<?php
include 'db.php';

// Fetch only upcoming events (today or future)
$sql = "SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Upcoming Events</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f3f4f6;
      margin: 0;
      padding: 20px;
    }

    h2 {
      text-align: center;
      color: #2563eb;
      margin-bottom: 30px;
    }

    .event-card {
      display: flex;
      background: white;
      border-radius: 12px;
      padding: 15px;
      margin: 20px auto;
      max-width: 800px;
      box-shadow: 0px 4px 10px rgba(0,0,0,0.1);
      align-items: center;
    }

    .event-card img {
      width: 250px;  /* fixed banner width */
      height: 200px; /* fixed banner height */
      object-fit: cover;
      border-radius: 10px;
      margin-right: 20px;
    }

    .event-details {
      flex: 1;
    }

    .event-details h3 {
      margin: 0 0 10px 0;
      font-size: 18px;
      color: #111827;
      text-transform: capitalize;
    }

    .event-details p {
      margin: 5px 0;
      font-size: 20px;
      color: #374151;
    }

    .apply-btn {
      display: inline-block;
      margin-top: 10px;
      background: #6366f1;
      color: white;
      padding: 8px 15px;
      border: none;
      border-radius: 8px;
      text-decoration: none;
      font-size: 14px;
    }

    .apply-btn:hover {
      background: #4f46e5;
    }

    .no-events {
      text-align: center;
      font-size: 20px;
      color: #dc2626;
      margin-top: 50px;
    }
  </style>
</head>
<body>

<h2>Upcoming Events</h2>

<?php
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
?>
  <div class="event-card">
    <img src="uploads/<?php echo $row['banner']; ?>" alt="Event Banner">
    <div class="event-details">
      <h3><?php echo htmlspecialchars($row['title']); ?></h3>
      <p><strong>Date:</strong> <?php echo $row['event_date']; ?></p>
      <p><strong>Time:</strong> <?php echo $row['event_start_time']; ?> - <?php echo $row['event_time_end']; ?></p>
      <p><strong>Volunteers Needed:</strong> <?php echo $row['volunteer_needed']; ?></p>
      <p><strong>Providing:</strong> <?php echo $row['providing']; ?></p>
      <p><strong>Money:</strong> ₹<?php echo $row['money']; ?></p>
      <p><strong>Location:</strong> <?php echo $row['location']; ?></p>
      <p><strong>Description:</strong> <?php echo $row['description']; ?></p>
      
      <a href="apply_volunteer.php?event_id=<?php echo $row['event_id']; ?>" class="apply-btn">Apply as Volunteer</a>
    </div>
  </div>
<?php
  }
} else {
  echo "<p class='no-events'>No upcoming events found.</p>";
}
$conn->close();
?>

</body>
</html>





