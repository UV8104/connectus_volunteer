<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// NOTE: You must have a 'db.php' file that connects to your database.
if (file_exists('db.php')) {
    include 'db.php';
} else {
    die("Database connection file not found. Please create a 'db.php' file.");
}


// Example: Username from session
if (!isset($_SESSION['username'])) {
    $_SESSION['username'] = "Admin"; // fallback if not logged in
}
$username = $_SESSION['username'];
$firstLetter = strtoupper(substr($username, 0, 1));

// --- QUERIES FOR DASHBOARD CONTENT ---
$today = date("Y-m-d");

// Card 1: Count total number of upcoming events
$result_upcoming = $conn->query("SELECT COUNT(*) as count FROM events WHERE event_date >= '$today'");
$upcoming_count = $result_upcoming->fetch_assoc()['count'];



// Card 3: Count verified records from the 'verified_attendance' table
// This is the correct line
$result_verified = $conn->query("SELECT COUNT(*) as count FROM verified_attendance WHERE action = 'verify'");
$verified_count = $result_verified->fetch_assoc()['count']; // --- FIX #2: Corrected variable name

// Card 4: Count total user applications
$result_apps = $conn->query("SELECT COUNT(*) as count FROM user_applications");
$apps_count = $result_apps->fetch_assoc()['count'];


// List: Get all upcoming events
$upcoming_events_list = $conn->query("SELECT event_id, title, event_date FROM events WHERE event_date >= '$today' ORDER BY event_date ASC");

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      display: flex;
    }
    .sidebar {
      width: 220px;
      background: #000; /* changed to black */
      color: white;
      height: 100vh;
      position: fixed;
      top: 0;
      left: 0;
      padding-top: 20px;
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    /* Logo Image */
    .sidebar .logo {
      width: 150px;
      height: auto;
      margin-bottom: 30px;
    }
    .sidebar a {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 15px;
      color: white;
      text-decoration: none;
      transition: 0.3s;
      width: 90%;
      border-radius: 5px;
      margin-bottom: 5px;
    }
    .sidebar a:hover {
      background: #333; /* hover effect */
    }

    /* Topbar */
    .topbar {
      margin-left: 220px;
      background: white;
      padding: 10px 20px;
      display: flex;
      justify-content: flex-end;
      align-items: center;
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      height: 60px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      gap: 25px; /* spacing between topbar modules */
    }
    .topbar a {
      text-decoration: none;
      color: #333;
      font-weight: bold;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .topbar a:hover {
      color: purple;
    }

    /* Avatar + dropdown */
    .avatar {
      width: 40px;
      height: 40px;
      background: pink;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
      cursor: pointer;
      position: relative;
    }
    .dropdown {
      display: none;
      position: absolute;
      right: 0;
      top: 50px;
      background: white;
      border: 1px solid #ccc;
      border-radius: 5px;
      min-width: 180px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.2);
      z-index: 10;
      text-align: center;
    }
    .dropdown p {
      margin: 10px 0;
      font-weight: bold;
      color: purple;
    }
    .dropdown hr {
      margin: 5px 0;
      border: 0.5px solid #ddd;
    }
    .dropdown button {
      width: 100%;
      padding: 10px;
      border: none;
      background: #ff4d4d;
      color: white;
      cursor: pointer;
    }
    .dropdown button:hover {
      background: #cc0000;
    }

    /* Content */
    .content {
      margin-left: 220px;
      margin-top: 60px; /* Height of topbar */
      padding: 20px;
      flex: 1;
      background: #f9f9f9;
      height: calc(100vh - 60px); /* Full height minus topbar */
      overflow-y: auto; /* This makes the content area scrollable */
    }
    
    /* === NEW: Styles for Dashboard Cards === */
    .card-container {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        margin-bottom: 20px;
    }
    .stat-card {
        background: white;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.05);
        flex: 1 1 200px;
        text-align: center;
        color: #333;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.1);
    }
    .stat-card .icon {
        font-size: 2.5em;
        color: purple;
        margin-bottom: 15px;
    }
    .stat-card h3 {
        font-size: 2.5em;
        margin: 0 0 5px 0;
        color: #333;
    }
    .stat-card p {
        margin: 0;
        font-size: 1em;
        color: #666;
        font-weight: bold;
    }

    /* === NEW: Styles for Upcoming Events List === */
    .events-list-container {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.05);
    }
    .events-list-container h2 {
        margin-top: 0;
        color: purple;
        border-bottom: 2px solid #f0f0f0;
        padding-bottom: 10px;
        font-size: 1.5em;
    }
    .event-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 10px;
        border-bottom: 1px solid #eee;
    }
    .event-item:last-child {
        border-bottom: none;
    }
    .event-details {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }
    .event-title {
        font-weight: bold;
        font-size: 1.1em;
        color: #333;
    }
    .event-date {
        color: #777;
        font-size: 0.9em;
    }
    .view-btn {
        background: purple;
        color: white;
        padding: 8px 15px;
        border-radius: 5px;
        text-decoration: none;
        font-weight: bold;
        transition: background 0.3s;
    }
    .view-btn:hover {
        background: #5a005a;
        color: white;
    }
    .no-events-message {
        text-align: center;
        padding: 20px;
        color: #888;
    }

    /* Logout Modal */
    .modal {
      display: none;
      position: fixed;
      top: 0; left: 0; right:0; bottom:0;
      background: rgba(0,0,0,0.5);
      justify-content: center;
      align-items: center;
      z-index: 100;
    }
    .modal-content {
      background: white;
      padding: 20px;
      border-radius: 10px;
      text-align: center;
      width: 300px;
      animation: pop 0.3s ease-in-out;
    }
    @keyframes pop {
      from { transform: scale(0.8); opacity: 0; }
      to { transform: scale(1); opacity: 1; }
    }
    .modal-content h3 {
      margin-bottom: 15px;
      color: purple;
    }
    .modal-buttons {
      display: flex;
      justify-content: space-around;
      margin-top: 20px;
    }
    .modal-buttons button {
      padding: 8px 15px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-weight: bold;
    }
    .yes-btn { background: #4CAF50; color: white; }
    .yes-btn:hover { background: #45a049; }
    .no-btn { background: #f44336; color: white; }
    .no-btn:hover { background: #da190b; }
  </style>
</head>
<body>

  <div class="sidebar">
    <img src="contactus.jpg" alt="Logo" class="logo">
    <a href="admin_events.php?type=upcoming"><i class="fas fa-calendar-plus"></i> Upcoming Events</a>
    <a href="admin_events.php?type=previous"><i class="fas fa-calendar-check"></i> Previous Events</a>

    <a href="admin_notifications.php"><i class="fas fa-bell"></i> Send Notifications</a>
    <a href="admin_attendance.php"><i class="fas fa-tasks"></i> Attendance records</a>
    <a href="verified_report.php"><i class="fas fa-clipboard-check"></i> Attended records</a>
    <a href="view_user_profile.php"><i class="fas fa-users"></i> View User Profile</a>
    <a href="admin_applications.php"><i class="fas fa-file-alt"></i> User Applications</a>
  </div>

  <div class="topbar">
    <a href="create_event.php"><i class="fas fa-plus-circle"></i> Create Event</a>
    <a href="view_feedback.php"><i class="fas fa-comments"></i> View Feedback</a>
    <a href="volunteers_contact.php"><i class="fas fa-address-book"></i> Volunteers Contact</a>
    <div style="position: relative; margin-left:10px;">
      <div class="avatar" onclick="toggleDropdown()"><?php echo $firstLetter; ?></div>
      <div class="dropdown" id="dropdown">
        <p>Hi, <?php echo htmlspecialchars($username); ?></p>
        <hr>
        <button onclick="confirmLogout()">Logout</button>
      </div>
    </div>
  </div>

  <div class="content">
    <h1 style="margin-bottom: 25px;">Admin Dashboard</h1>
   <div class="card-container">
    <div class="stat-card">
        <div class="icon"><i class="fas fa-calendar-alt"></i></div>
        <h3><?php echo $upcoming_count; ?></h3>
        <p>Total Upcoming Events</p>
    </div>
    
    <div class="stat-card">
        <div class="icon"><i class="fas fa-user-check"></i></div>
        <h3><?php echo $verified_count; ?></h3> <p>Total Attended (Verified)</p>
    </div>
     <div class="stat-card">
        <div class="icon"><i class="fas fa-file-invoice"></i></div>
        <h3><?php echo $apps_count; ?></h3>
        <p>Total User Applications</p>
    </div>
</div>


    <div class="events-list-container">
        <h2>Upcoming Events</h2>
        <?php if ($upcoming_events_list->num_rows > 0): ?>
            <?php while($event = $upcoming_events_list->fetch_assoc()): ?>
                <div class="event-item">
                    <div class="event-details">
                        <span class="event-title"><?php echo htmlspecialchars($event['title']); ?></span>
                        <span class="event-date">
                            <i class="fas fa-calendar-day"></i> Date: <?php echo date("d M, Y", strtotime($event['event_date'])); ?>
                        </span>
                    </div>
                    <a href="admin_events.php" class="view-btn">View</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="no-events-message">No upcoming events found.</p>
        <?php endif; ?>
    </div>
  </div>
  <div class="modal" id="logoutModal">
    <div class="modal-content">
      <h3>Are you sure you want to logout?</h3>
      <div class="modal-buttons">
        <button class="yes-btn" onclick="window.location.href='logout.php'">Yes</button>
        <button class="no-btn" onclick="closeModal()">No</button>
      </div>
    </div>
  </div>

  <script>
    // Disable right-click
    document.addEventListener("contextmenu", function(e) {
      e.preventDefault();
    });

    function toggleDropdown() {
      let dropdown = document.getElementById("dropdown");
      dropdown.style.display = (dropdown.style.display === "block") ? "none" : "block";
    }
    function confirmLogout() {
      document.getElementById("logoutModal").style.display = "flex";
    }
    function closeModal() {
      document.getElementById("logoutModal").style.display = "none";
    }
    
    // Close dropdown if clicked outside
    window.onclick = function(event) {
      if (!event.target.matches('.avatar')) {
        let dropdowns = document.getElementsByClassName("dropdown");
        for (let i = 0; i < dropdowns.length; i++) {
          let openDropdown = dropdowns[i];
          if (openDropdown.style.display === "block") {
            openDropdown.style.display = "none";
          }
        }
      }
    }
  </script>

</body>
</html>