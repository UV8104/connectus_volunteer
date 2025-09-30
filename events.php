<?php
include 'db.php';

// Get today's date
$today = date("Y-m-d");

// ✅ Delete events older than 7 days
$deleteOldEvents = "DELETE FROM events WHERE event_date < DATE_SUB('$today', INTERVAL 7 DAY)";
$conn->query($deleteOldEvents);

// Get type from URL (default = upcoming)
$type = isset($_GET['type']) ? $_GET['type'] : 'upcoming';

if ($type == "previous") {
    $sql = "SELECT * FROM events 
            WHERE event_date < '$today' 
              AND event_date >= DATE_SUB('$today', INTERVAL 7 DAY) 
            ORDER BY event_date DESC";
    $heading = "Previous Events";
    $showApply = false; // hide button
} else {
    $sql = "SELECT * FROM events 
            WHERE event_date >= '$today' 
            ORDER BY event_date ASC";
    $heading = "Upcoming Events";
    $showApply = true; // show button
}

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?php echo $heading; ?></title>
  <style>
    body { 
        font-family: Arial, sans-serif; 
        background: #f3f3f3; 
        padding: 20px; 
    }
    .container { 
        max-width: 1000px; 
        margin: auto; 
    }
    h1 { 
        text-align: center; 
        margin-bottom: 30px; 
    }
    .event-card { 
        width: 10cm;                /* fixed width */
        margin: 20px auto;          /* center + spacing */
        border-radius: 12px; 
        overflow: hidden; 
        box-shadow: 0 4px 12px rgba(0,0,0,0.1); 
        background: #fff; 
        transition: transform 0.2s ease-in-out;
    }
    .event-card:hover {
        transform: translateY(-5px);
    }
    .event-banner {
        width: 100%;
        height: auto;               /* ✅ auto height for banner */
        max-height: 250px;          /* optional limit */
        overflow: hidden;
    }
    .event-banner img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .event-details { 
        padding: 12px 15px; 
        /* ✅ flexible height, all content visible */
    }
    .event-details h3 { 
        margin-top: 0; 
        margin-bottom: 10px; 
        font-size: 18px; 
        color: #333; 
    }
    .event-details p { 
        margin: 5px 0; 
        font-size: 14px; 
        color: #555; 
        line-height: 1.4;
    }
    .event-details strong {
        color: #222;
    }
    .no-events { 
        text-align: center; 
        font-size: 20px; 
        color: gray; 
        margin-top: 50px; 
    }
    .apply-btn {
        display: inline-block;
        padding: 8px 16px;
        margin-top: 10px;
        background: #007bff;
        color: #fff;
        text-decoration: none;
        border-radius: 6px;
        transition: background 0.2s ease-in-out;
        font-size: 14px;
    }
    .apply-btn:hover {
        background: #0056b3;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1><?php echo $heading; ?></h1>

    <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="event-card">
                <div class="event-banner">
                    <?php if (!empty($row['banner'])): ?>
                        <img src="uploads/<?php echo $row['banner']; ?>" alt="Event Banner">
                    <?php else: ?>
                        <img src="uploads/default.jpg" alt="Default Banner">
                    <?php endif; ?>
                </div>
                <div class="event-details">
                    <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                    <p><strong>Date:</strong> <?php echo $row['event_date']; ?></p>
                    <p><strong>Time:</strong> <?php echo $row['event_start_time']; ?> - <?php echo $row['event_time_end']; ?></p>
                    <p><strong>Volunteer Needed:</strong> <?php echo $row['volunteer_needed']; ?></p>
                    <p><strong>Providing:</strong> <?php echo $row['providing']; ?></p>
                    <p><strong>Money:</strong> ₹<?php echo $row['money']; ?></p>
                    <p><strong>Location:</strong> <?php echo $row['location']; ?></p>
                    <p><strong>Description:</strong> <?php echo $row['description']; ?></p>

                    <div style="margin-top: 10px; display: flex; gap: 8px;">
    <?php if ($showApply): ?>
        <a href="apply_event.php?event_id=<?php echo $row['event_id']; ?>" class="apply-btn">Apply</a>
    <?php endif; ?>
    <a href="event_details.php?event_id=<?php echo $row['event_id']; ?>" class="apply-btn" style="background:#28a745;">More Details</a>
</div>


                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="no-events">No <?php echo strtolower($heading); ?> found.</p>
    <?php endif; ?>
  </div>
</body>
</html>
