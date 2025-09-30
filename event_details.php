<?php
include 'db.php';

// ✅ Get event ID from URL
if (!isset($_GET['event_id'])) {
    die("Invalid request!");
}
$event_id = intval($_GET['event_id']);

// ✅ Fetch event from DB
$sql = "SELECT * FROM events WHERE event_id = $event_id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    die("Event not found!");
}

$event = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?php echo htmlspecialchars($event['title']); ?> - More Details</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
    /* Reset & Base */
    * { box-sizing: border-box; margin:0; padding:0; }
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(to bottom, #e0eafc, #cfdef3);
        padding: 30px;
    }
    a { text-decoration: none; }

    /* Container */
    .container {
        max-width: 950px;
        margin: auto;
        background: #fff;
        border-radius: 15px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        overflow: hidden;
        padding: 30px 40px;
        animation: fadeIn 0.7s ease-in-out;
    }
    @keyframes fadeIn {
        from {opacity:0; transform: translateY(20px);}
        to {opacity:1; transform: translateY(0);}
    }

    /* Heading */
    h1 {
        text-align: center;
        font-size: 2.3rem;
        color: #1a1a1a;
        margin-bottom: 10px;
    }
    .organizer {
        text-align: center;
        font-weight: bold;
        font-size: 1.1rem;
        color: #007bff;
        margin-bottom: 30px;
    }

    /* Rules Section */
    .rules-section {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    .rules-section h3 {
        color: #fff;
        background: linear-gradient(90deg, #007bff, #00c6ff);
        padding: 10px 15px;
        border-radius: 10px;
        text-align: center;
        margin-bottom: 15px;
        font-size: 1.4rem;
        box-shadow: 0 3px 8px rgba(0,0,0,0.2);
    }

    /* Individual Rule Card */
    .rule-block {
        background: #f9f9f9;
        border-left: 6px solid #007bff;
        padding: 20px 25px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        transition: transform 0.3s, box-shadow 0.3s;
    }
    .rule-block:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }
    .rule-block strong {
        font-size: 1.1rem;
        color: #333;
    }
    .rule-block p {
        margin: 8px 0;
        font-size: 0.95rem;
        line-height: 1.6;
        color: #555;
    }
    .do, .dont {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        font-weight: bold;
    }
    .do i { color: green; margin-top:2px; }
    .dont i { color: red; margin-top:2px; }

    /* Back Button */
    .back-btn {
        display: inline-block;
        margin-top: 25px;
        padding: 12px 25px;
        background: linear-gradient(90deg, #007bff, #00c6ff);
        color: #fff;
        font-weight: bold;
        text-align: center;
        border-radius: 8px;
        transition: background 0.3s, transform 0.2s;
    }
    .back-btn:hover {
        background: linear-gradient(90deg, #0056b3, #00a1d6);
        transform: translateY(-3px);
    }

    /* Responsive */
    @media(max-width:768px){
        .container { padding:20px; }
        .rules-section h3 { font-size:1.2rem; }
    }
</style>
</head>
<body>
<div class="container">
    <h1><?php echo htmlspecialchars($event['title']); ?></h1>
    <div class="organizer">Event Managed by VOLUNTEER TRUST</div>

    <div class="rules-section">
        <h3>Rules & Regulations for Volunteers</h3>

        <!-- 1. Date -->
        <div class="rule-block">
            <p><strong>1. Date</strong></p>
            <p class="do"><i class="fa-solid fa-check"></i> Do: Note the exact date of the event and mark it on your calendar. Arrive on the specified date; don’t come on another day.</p>
            <p class="dont"><i class="fa-solid fa-xmark"></i> Don’t: Miss the event date without informing the organizer. Assume the event is on a different date than mentioned.</p>
        </div>

        <!-- 2. Timing -->
        <div class="rule-block">
            <p><strong>2. Timing (Start and End)</strong></p>
            <p class="do"><i class="fa-solid fa-check"></i> Do: Arrive at least 15–30 minutes before the start time for registration and briefing. Stay until the official end time unless instructed otherwise.</p>
            <p class="dont"><i class="fa-solid fa-xmark"></i> Don’t: Arrive late or leave early without permission. Ignore the schedule provided by the organizers.</p>
        </div>

        <!-- 3. Money / Financial Rules -->
        <div class="rule-block">
            <p><strong>3. Money / Financial Rules</strong></p>
            <p class="do"><i class="fa-solid fa-check"></i> Do: If reimbursements or stipends are provided, collect them according to instructions. Pay any participation fees (if applicable) before the event.</p>
            <p class="dont"><i class="fa-solid fa-xmark"></i> Don’t: Expect unmentioned payments or reimbursements. Misuse organizational funds or donations.</p>
        </div>

        <!-- 4. Providing -->
        <div class="rule-block">
            <p><strong>4. Providing (Food, Safety Kits, Materials, etc.)</strong></p>
            <p class="do"><i class="fa-solid fa-check"></i> Do: Use the items provided responsibly (e.g., safety kits, gloves, t-shirts, stationery). Collect food or refreshments only as per instructions. Follow any guidelines for handling organizational equipment or materials.</p>
            <p class="dont"><i class="fa-solid fa-xmark"></i> Don’t: Waste, damage, or take materials unnecessarily. Share or distribute organizational items without permission.</p>
        </div>

        <!-- 5. Location -->
        <div class="rule-block">
            <p><strong>5. Location</strong></p>
            <p class="do"><i class="fa-solid fa-check"></i> Do: Reach the venue at the given address; check for landmarks if needed. Follow parking, entry, and seating instructions. Familiarize yourself with the venue layout for safety purposes.</p>
            <p class="dont"><i class="fa-solid fa-xmark"></i> Don’t: Go to the wrong location or enter restricted areas. Ignore directions given by organizers or venue staff.</p>
        </div>
    </div>

    <a href="events.php" class="back-btn">Back to Events</a>
</div>
</body>
</html>
