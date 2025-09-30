<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: home.php");
    exit();
}

// Get user info from session
$user_id = $_SESSION['user_id'];
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'User';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'guest';

// Handle logout
if (isset($_GET['logout']) && $_GET['logout'] == 'true') {
    session_unset();
    session_destroy();
    header("Location: home.php");
    exit();
}

// Connect to database
include 'db.php';

$today = date("Y-m-d");

// Count total upcoming events
$sql_total = "SELECT COUNT(*) as total_upcoming FROM events WHERE event_date >= '$today'";
$result_total = $conn->query($sql_total);
$row_total = $result_total->fetch_assoc();
$total_upcoming = $row_total['total_upcoming'];

// Get the user's next upcoming event application
$sql_user_event = "
    SELECT e.*, ua.status 
    FROM events e
    INNER JOIN user_applications ua ON e.event_id = ua.event_id
    WHERE ua.user_id = $user_id
      AND e.event_date >= '$today'
    ORDER BY e.event_date ASC
    LIMIT 1
";
$result_user_event = $conn->query($sql_user_event);
$upcoming_event = $result_user_event->fetch_assoc();

// Fetch ONLY UPCOMING events to display in a list
$upcoming_events_sql = "SELECT * FROM events WHERE event_date >= '$today' ORDER BY event_date ASC";
$upcoming_events_result = $conn->query($upcoming_events_sql);

?>
<!DOCTYPE html>
<html lang="en">
<head> 
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0"> 
<title>VM System Dashboard</title> 
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    /* Global Variables */
    :root {
        --sidebar-bg: #000000;
        --sidebar-hover: #222222;
        --primary: #3b82f6;
        --secondary: #64748b;
        --text-light: #f8fafc;
        --text-dark: #1e293b;
        --content-bg: #f1f5f9;
        --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    /* === SCROLLING & LAYOUT FIXES === */
    body {
        background-color: var(--content-bg);
        overflow: hidden; /* Prevents scrollbars on the main body */
    }

    .dashboard {
        display: flex;
        min-height: 100vh;
    }

    .main-content {
        flex: 1;
        margin-left: 250px;
        transition: margin-left 0.3s ease;
        height: 100vh;
        display: flex;
        flex-direction: column;
    }

    .content {
        padding: 20px 30px;
        flex-grow: 1;
        overflow-y: auto; /* Adds vertical scrollbar ONLY when needed */
    }

    /* Sidebar Styles */
    .sidebar {
        width: 250px;
        background: var(--sidebar-bg);
        color: var(--text-light);
        transition: all 0.3s ease;
        position: fixed;
        height: 100%;
        z-index: 100;
        box-shadow: var(--shadow);
        left: 0;
    }

    .sidebar-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 20px;
        height: 70px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .logo img {
        max-width: 120px;
        height: auto;
    }

    .sidebar-menu {
        list-style: none;
        padding: 10px 0;
    }

    .menu-item {
        padding: 12px 20px;
        display: flex;
        align-items: center;
        gap: 15px;
        cursor: pointer;
        transition: all 0.2s;
        border-left: 4px solid transparent;
    }

    .menu-item:hover, .menu-item.active {
        background: var(--sidebar-hover);
        border-left: 4px solid var(--primary);
    }
    
    .menu-item i { width: 24px; text-align: center; }
    .menu-text { transition: opacity 0.3s; }

    /* Top Bar Styles */
    .topbar {
        background: white;
        color: var(--text-dark);
        padding: 0 30px;
        height: 70px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: var(--shadow);
        position: sticky;
        top: 0;
        z-index: 90;
        flex-shrink: 0;
    }

    .page-title { font-size: 24px; font-weight: 600; }
    .topbar-menu { display: flex; align-items: center; gap: 20px; }
    .topbar-item a { text-decoration: none; color: inherit; display: flex; align-items: center; gap: 5px; }
    .dropdown { position: relative; }

    .dropdown-menu {
        position: absolute; top: calc(100% + 10px); right: 0;
        background: white; width: 200px; border-radius: 6px;
        box-shadow: var(--shadow); padding: 10px 0;
        z-index: 100; display: none; animation: fadeIn 0.3s ease;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .dropdown-menu.show { display: block; }
    .dropdown-item {
        padding: 10px 20px; transition: background 0.2s;
        display: flex; align-items: center; gap: 10px;
        color: var(--text-dark); text-decoration: none;
    }
    .dropdown-item:hover { background: var(--content-bg); }

    .user-avatar {
        width: 40px; height: 40px; background: var(--primary); color: white;
        border-radius: 50%; display: flex; align-items: center; justify-content: center;
        font-weight: bold; cursor: pointer; transition: transform 0.2s;
    }
    .user-avatar:hover { transform: scale(1.1); }
    .user-menu { width: 220px; }
    .user-header { padding: 15px 20px; border-bottom: 1px solid rgba(0, 0, 0, 0.1); }
    .user-name { font-weight: 600; margin-top: 5px; }
    .divider { height: 1px; background: rgba(0, 0, 0, 0.1); margin: 8px 0; }

    /* Content Area Styling */
    .header-content h2 { margin-bottom: 20px; }
    .card-grid { display: flex; flex-wrap: wrap; gap: 20px; }

    .card {
        background: white; border-radius: 10px; padding: 20px; box-shadow: var(--shadow);
        transition: transform 0.2s; flex: 1 1 300px; max-width: 400px;
    }
    .card:hover { transform: translateY(-5px); }
    .card-header {
        font-size: 18px; font-weight: 600; margin-bottom: 15px; color: var(--text-dark);
        display: flex; align-items: center; gap: 10px;
    }
    .card-header i { color: var(--primary); }
    .stat { display: flex; align-items: center; justify-content: space-between; }
    .stat-value { font-size: 28px; font-weight: 700; color: var(--primary); }
    .stat-details { display: flex; flex-direction: column; align-items: flex-start; gap: 8px; }
    
    .triangle-btn {
        position: absolute; top: 50%; right: 15px;
        width: 0; height: 0;
        border-left: 12px solid var(--primary);
        border-top: 8px solid transparent;
        border-bottom: 8px solid transparent;
        transform: translateY(-50%);
        cursor: pointer;
        transition: transform 0.2s;
    }
    .triangle-btn:hover { transform: translateY(-50%) scale(1.2); }

    /* === IMAGE CAROUSEL CSS === */
    .image-carousel-container {
        position: relative;
        max-width: 800px;
        margin: 30px auto;
        padding: 20px;
        background-color: #000000;
        border-radius: 10px;
        box-shadow: var(--shadow);
    }

    .image-carousel-viewport {
        position: relative;
        overflow: hidden;
        width: 100%;
        aspect-ratio: 16 / 9; /* Rectangular Shape */
        border-radius: 6px;
        background-color: #ffffff;
    }
    
    .image-carousel-slider {
        display: flex;
        height: 100%;
        transition: transform 0.5s ease-in-out;
    }

    .carousel-item {
        min-width: 100%;
        height: 100%;
        box-sizing: border-box;
    }

    .carousel-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .carousel-btn {
        position: absolute; top: 50%; transform: translateY(-50%);
        background: rgba(255, 255, 255, 0.3);
        color: black;
        border: none;
        width: 40px; height: 40px; border-radius: 50%;
        font-size: 20px; cursor: pointer; z-index: 10;
        transition: background-color 0.2s;
    }
    .carousel-btn:hover { background: rgba(255, 255, 255, 0.6); }
    .prev-btn { left: 30px; }
    .next-btn { right: 30px; }
    
    /* === UPCOMING EVENTS LIST STYLES === */
    .upcoming-events-container {
        background-color: #ffffff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: var(--shadow);
        margin-top: 30px;
    }

    .upcoming-events-container h3 {
        font-size: 22px;
        margin-bottom: 20px;
        color: var(--text-dark);
    }

    .events-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .event-list-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 15px 0;
        border-bottom: 1px solid #e2e8f0;
    }

    .event-list-item:last-child {
        border-bottom: none;
    }

    .event-item-details .title {
        font-weight: 600;
        color: var(--text-dark);
        display: block;
        margin-bottom: 4px;
    }

    .event-item-details .date {
        font-size: 14px;
        color: var(--secondary);
    }

    .view-event-btn {
        background-color: var(--primary);
        color: white;
        padding: 8px 16px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        transition: background-color 0.2s;
    }

    .view-event-btn:hover {
        background-color: #2563eb;
    }

    /* Logout Modal */
    .modal {
        display: none; position: fixed; top: 0; left: 0;
        width: 100%; height: 100%; background: rgba(0, 0, 0, 0.6);
        align-items: center; justify-content: center; z-index: 9999;
    }
    .modal-content {
        background: #fff; padding: 30px; border-radius: 10px; text-align: center;
        min-width: 320px; box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    }
    .modal-buttons { margin-top: 20px; display: flex; gap: 10px; justify-content: center; }
    .modal-btn {
        padding: 10px 20px; border: none; border-radius: 5px;
        cursor: pointer; font-weight: bold; transition: opacity 0.2s;
    }
    .modal-btn:hover { opacity: 0.8; }
    .btn-confirm { background: #e74c3c; color: white; }
    .btn-cancel { background: #bdc3c7; color: var(--text-dark); }
</style>
</head>
<body>
    <div class="dashboard">
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <img src="contactus.jpg" alt="Logo">
                </div>
            </div>
            
            <ul class="sidebar-menu">
                <li class="menu-item active"> <i class="fas fa-home"></i> <span class="menu-text">Dashboard</span> </li>
                <li class="menu-item" onclick="window.location.href='my_applications.php'"> <i class="fas fa-calendar-alt"></i> <span class="menu-text">My Events</span> </li>
                <li class="menu-item" onclick="window.location.href='user_attendance.php'"> <i class="fas fa-check-circle"></i> <span class="menu-text">Attendance</span> </li>
                <li class="menu-item"> <i class="fas fa-comments"></i> <span class="menu-text">Alerts</span> </li>
                <?php if ($role === 'admin'): ?>
                <li class="menu-item"> <i class="fas fa-users-cog"></i> <span class="menu-text">Admin Panel</span> </li>
                <?php endif; ?>
            </ul>
        </div>

        <div class="main-content">
            <div class="topbar">
                <h1 class="page-title">Dashboard</h1>
                <div class="topbar-menu">
                    <div class="topbar-item dropdown">
                        <a href="#" class="dropdown-toggle"> <i class="fas fa-calendar-day"></i> <span>Events</span> </a>
                        <div class="dropdown-menu">
                            <a href="events.php?type=upcoming" class="dropdown-item">Upcoming Events</a>
                            <a href="events.php?type=previous" class="dropdown-item">Previous Events</a>
                        </div>
                    </div>
                    <div class="topbar-item"> <a href="feedback.php"> <i class="fas fa-comment-dots"></i> <span>Feedback</span> </a> </div>
                    <div class="topbar-item"> <a href="contact.php"> <i class="fas fa-phone-alt"></i> <span>Contact</span> </a> </div>
                    <div class="topbar-item dropdown">
                        <div class="user-avatar" id="userAvatar" title="Hi <?php echo htmlspecialchars($username); ?>">
                            <?php echo strtoupper(substr($username, 0, 1)); ?>
                        </div>
                        <div class="dropdown-menu user-menu">
                            <div class="user-header">
                                <div>Hi,</div>
                                <div class="user-name"><?php echo htmlspecialchars($username); ?></div>
                            </div>
                            <div class="divider"></div>
                            <a href="profile.php" class="dropdown-item"> <i class="fas fa-user"></i> Profile </a>
                            <a href="#" class="dropdown-item" onclick="openLogoutModal()"> <i class="fas fa-sign-out-alt"></i> Logout </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content">
                <div class="header-content">
                    <h2>Welcome to VM System, <?php echo htmlspecialchars($username); ?>!</h2>
                </div>

                <div class="card-grid">
                    <div class="card" style="position: relative;">
                        <div class="card-header">
                            <i class="fas fa-calendar-check"></i>
                            <span>My Next Event</span>
                        </div>
                        <?php if ($upcoming_event): ?>
                            <div class="stat-details">
                                <div><strong>Title:</strong> <?php echo htmlspecialchars($upcoming_event['title']); ?></div>
                                <div><strong>Date:</strong> <?php echo date("d M Y", strtotime($upcoming_event['event_date'])); ?></div>
                                <div><strong>Time:</strong> <?php echo date("g:i A", strtotime($upcoming_event['event_start_time'])); ?></div>
                                <div><strong>Status:</strong> <?php echo htmlspecialchars($upcoming_event['status']); ?></div>
                            </div>
                        <?php else: ?>
                            <div class="stat-details">
                                <div>No upcoming events applied for. <a href="events.php">Apply now!</a></div>
                            </div>
                        <?php endif; ?>
                        <a href="my_applications.php" class="triangle-btn" title="View all my applications"></a>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-calendar"></i>
                            <span>Total Upcoming Events</span>
                        </div>
                        <div class="stat">
                            <div class="stat-value"><?php echo $total_upcoming; ?></div>
                        </div>
                    </div>
                </div>

                <div class="image-carousel-container">
                    <div class="image-carousel-viewport">
                        <div class="image-carousel-slider">
                            <div class="carousel-item"><img src="user/images/image1.jpg" alt="Volunteer Event 1"></div>
                            <div class="carousel-item"><img src="user/images/image2.jpg" alt="Volunteer Event 2"></div>
                            <div class="carousel-item"><img src="user/images/image3.jpg" alt="Volunteer Event 3"></div>
                        </div>
                    </div>
                    <button class="carousel-btn prev-btn"><i class="fas fa-chevron-left"></i></button>
                    <button class="carousel-btn next-btn"><i class="fas fa-chevron-right"></i></button>
                </div>
                
                <div class="upcoming-events-container">
                    <h3>Upcoming Events</h3>
                    <ul class="events-list">
                        <?php if ($upcoming_events_result->num_rows > 0): ?>
                            <?php while ($event = $upcoming_events_result->fetch_assoc()): ?>
                                <li class="event-list-item">
                                    <div class="event-item-details">
                                        <span class="title"><?php echo htmlspecialchars($event['title']); ?></span>
                                        <span class="date">
                                            <i class="fas fa-calendar-alt"></i> 
                                            <?php echo date("d M Y", strtotime($event['event_date'])); ?>
                                        </span>
                                    </div>
                                    <a href="events.php?id=<?php echo $event['event_id']; ?>" class="view-event-btn">View</a>
                                </li>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <li class="event-list-item">
                                <p>No upcoming events scheduled.</p>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>

            </div> </div> </div> <div id="logoutModal" class="modal">
        <div class="modal-content">
            <p style="margin-bottom:20px; font-size:18px;">Are you sure you want to logout?</p>
            <div class="modal-buttons">
                <button class="modal-btn btn-cancel" onclick="closeLogoutModal()">Cancel</button>
                <button class="modal-btn btn-confirm" onclick="window.location.href='?logout=true'">Yes, Logout</button>
            </div>
        </div>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Dropdown Menu Logic ---
        document.querySelectorAll('.dropdown-toggle, #userAvatar').forEach(toggle => {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const dropdownMenu = this.nextElementSibling;
                const isShowing = dropdownMenu.classList.contains('show');
                document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                    menu.classList.remove('show');
                });
                if (!isShowing) {
                    dropdownMenu.classList.add('show');
                }
            });
        });

        document.addEventListener('click', function() {
            document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                menu.classList.remove('show');
            });
        });

        // --- IMAGE CAROUSEL SCRIPT ---
        const carouselSlider = document.querySelector('.image-carousel-slider');
        if (carouselSlider) {
            const items = document.querySelectorAll('.carousel-item');
            const totalItems = items.length;
            const prevBtn = document.querySelector('.prev-btn');
            const nextBtn = document.querySelector('.next-btn');
            let currentIndex = 0;

            function updateCarousel() {
                carouselSlider.style.transform = `translateX(-${currentIndex * 100}%)`;
            }

            nextBtn.addEventListener('click', () => {
                currentIndex = (currentIndex + 1) % totalItems;
                updateCarousel();
            });

            prevBtn.addEventListener('click', () => {
                currentIndex = (currentIndex - 1 + totalItems) % totalItems;
                updateCarousel();
            });
        }
    });

    // --- Modal Logic ---
    function openLogoutModal() {
        const modal = document.getElementById("logoutModal");
        modal.style.display = "flex";
    }
    
    function closeLogoutModal() {
        const modal = document.getElementById("logoutModal");
        modal.style.display = "none";
    }
    </script>
</body>
</html>