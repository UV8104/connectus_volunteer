<?php
session_start();
include 'db_users.php';
include 'db_events.php';

// ✅ Set correct timezone (important!)
date_default_timezone_set("Asia/Kolkata");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];

// Handle submission message (shows for 1 second only after submit)
$message = '';
$message_type = 'success';
if (isset($_SESSION['att_msg'])) {
    $message = $_SESSION['att_msg'];
    if (isset($_SESSION['att_msg_type'])) {
        $message_type = $_SESSION['att_msg_type'];
        unset($_SESSION['att_msg_type']);
    }
    unset($_SESSION['att_msg']);
}

// Fetch events applied by user + attendance info
$sql = "SELECT e.*, ua.user_id as ua_user_id, a.attendance_id, a.status, a.submitted_at, a.verified_at
        FROM events e
        JOIN user_applications ua ON e.event_id = ua.event_id
        LEFT JOIN attendance a 
          ON e.event_id = a.event_id AND a.user_id = ua.user_id
        WHERE ua.user_id = $user_id
        ORDER BY e.event_date ASC";
$result = $conn_events->query($sql);

// ✅ Function to check if checkbox should be enabled
function isCheckboxEnabled($event_date, $event_start_time) {
    $now = new DateTime("now");
    $eventDateTime = new DateTime("$event_date $event_start_time");

    // ✅ Allow only if today’s date matches AND current time >= start time
    return ($now->format("Y-m-d") === $event_date && $now >= $eventDateTime);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Attendance Module</title>
    <style>
        body { font-family: Arial; background: #f5f6fa; margin: 20px; }
        h2 { text-align: center; color: #2f3640; margin-bottom: 30px; }

        .message { padding: 10px 15px; border-radius: 5px; margin-bottom: 20px; text-align: center; opacity: 1; transition: opacity 0.5s; }
        .message.success { background: #27ae60; color: white; }
        .message.error { background: #e84118; color: white; }

        .top-right { position: fixed; top: 20px; right: 20px; z-index: 100; }
        .status-btn { padding: 8px 15px; background: #007bff; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; }
        .status-btn:hover { background: #0056b3; }

        .event { background: white; border-radius: 15px; box-shadow: 0 8px 20px rgba(0,0,0,0.08); padding: 25px; margin-bottom: 25px; }
        .event h3 { margin-top: 0; color: #3742fa; font-size: 22px; }
        .attendance-form { margin-top: 15px; }
        .attendance-form input[type="checkbox"] { transform: scale(1.3); margin-right: 10px; cursor: pointer; }
        .attendance-form button { padding: 6px 18px; background: #3742fa; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; }
        .attendance-form button:disabled { background: #ccc; cursor: not-allowed; }

        /* Status Circles */
        #status-container { display:none; position: fixed; top: 60px; right: 20px; background: white; padding: 20px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.15); max-width: 350px; max-height: 400px; overflow-y: auto; z-index: 100; }
        .status-card { background: #f1f2f6; padding: 12px; margin-bottom: 12px; border-radius: 10px; }
        .status-steps { display: flex; align-items: center; margin-top: 10px; }
        .circle { width: 25px; height: 25px; border-radius: 50%; border:2px dotted gray; display: flex; justify-content: center; align-items: center; background:red; }
        .circle.green { background: #2ecc71; border:2px dotted #2ecc71; }
        .line { flex:1; height:2px; border-top:2px dotted gray; margin:0 5px; }
        .line.green { border-top:2px dotted #2ecc71; }
        .status-labels { display:flex; justify-content:space-between; font-size:12px; margin-top:5px; }
        .status-dates { display:flex; justify-content:space-between; font-size:10px; color:#555; margin-top:2px; }

        .no-events { text-align:center; color:#e84118; font-weight:bold; margin-top:50px; font-size:18px; }
    </style>
</head>
<body>

<h2>Attendance Module</h2>

<div class="top-right">
    <button class="status-btn" onclick="toggleStatus()">Status</button>
</div>

<?php if($message){ ?>
    <div class="message <?php echo $message_type; ?>" id="msg-box"><?php echo $message; ?></div>
<?php } ?>

<div id="status-container">
    <?php
    $status_result = $conn_events->query("SELECT e.title, a.status, a.submitted_at, a.verified_at
                                          FROM events e
                                          JOIN attendance a ON e.event_id = a.event_id
                                          WHERE a.user_id=$user_id
                                          ORDER BY e.event_date ASC");
    if($status_result->num_rows==0){
        echo "<div class='message error'>You have not submitted the attendance yet.</div>";
    } else {
        while($s = $status_result->fetch_assoc()){
            $submitted_class = ($s['status']=='submitted' || $s['status']=='pending' || $s['status']=='done') ? 'green' : '';
            $pending_class = ($s['status']=='pending' || $s['status']=='done') ? 'green' : '';
            $done_class = ($s['status']=='done') ? 'green' : ($s['status']=='denied' ? '' : 'red');
    ?>
    <div class="status-card">
        <strong><?php echo htmlspecialchars($s['title']); ?></strong>
        <div class="status-steps">
            <div class="circle <?php echo $submitted_class; ?>"></div>
            <div class="line <?php echo $pending_class; ?>"></div>
            <div class="circle <?php echo $pending_class; ?>"></div>
            <div class="line <?php echo $done_class; ?>"></div>
            <div class="circle <?php echo $done_class; ?>"></div>
        </div>
        <div class="status-labels">
            <span>Submitted</span>
            <span>Pending</span>
            <span>Done</span>
        </div>
        <div class="status-dates">
            <span><?php echo $s['submitted_at']?:'--'; ?></span>
            <span><?php echo $s['verified_at']?:'--'; ?></span>
            <span><?php echo ($s['status']=='done'||$s['status']=='denied')?$s['verified_at']:'--'; ?></span>
        </div>
    </div>
    <?php }} ?>
</div>

<?php
if($result->num_rows==0){
    echo "<div class='no-events'>You have not applied for any events yet.</div>";
} else {
    while($event = $result->fetch_assoc()){
        $enable = isCheckboxEnabled($event['event_date'], $event['event_start_time']);
?>
<div class="event">
    <h3><?php echo htmlspecialchars($event['title']); ?></h3>
    <p><strong>Date:</strong> <?php echo $event['event_date']; ?></p>
    <p><strong>Start Time:</strong> <?php echo $event['event_start_time']; ?></p>

    <?php if(!$event['attendance_id']){ ?>
    <form method="post" action="attendance_submit.php" class="attendance-form">
        <input type="hidden" name="event_id" value="<?php echo $event['event_id']; ?>">
        <input type="checkbox" name="attend" <?php echo $enable?'':'disabled'; ?>> Mark Attendance
        <button type="submit" <?php echo $enable?'':'disabled'; ?>>Submit</button>
    </form>
    <?php } else { ?>
        <p><strong>Status:</strong> <?php echo ucfirst($event['status']); ?></p>
    <?php } ?>
</div>
<?php }} ?>

<script>
function toggleStatus(){
    var el = document.getElementById('status-container');
    el.style.display = (el.style.display==='block')?'none':'block';
}

// Auto-hide success/error message after 1 sec (only after form submission)
window.onload = function(){
    var msg = document.getElementById('msg-box');
    if(msg){
        setTimeout(function(){ msg.style.opacity=0; },1000);
    }
}
</script>

</body>
</html>
