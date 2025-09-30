<?php
// Start session
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Event</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #ffecd2, #fcb69f);
            margin: 0;
            padding: 0;
        }
        .form-container {
            width: 500px;
            margin: 50px auto;
            background: #fff;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0px 4px 15px rgba(0,0,0,0.2);
        }
        h2 {
            text-align: center;
            color: #ff5722;
        }
        label {
            font-weight: bold;
            margin-top: 10px;
            display: block;
            color: #333;
        }
        input, textarea, select {
            width: 95%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 10px;
            border: 1px solid #ccc;
        }
        button {
            width: 100%;
            padding: 12px;
            margin-top: 15px;
            background: #ff5722;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
        }
        button:hover {
            background: #e64a19;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Create Event</h2>
    <form action="store_event.php" method="POST" enctype="multipart/form-data">

        <label>Banner</label>
        <input type="file" name="banner" required>

        <label>Event Title</label>
        <input type="text" name="title" required>

        <label>Date</label>
        <input type="date" name="event_date" required>
        
          <label>Event Start Time:</label>
         <input type="time" name="event_start_time">

          <label>Event End Time:</label>
         <input type="time" name="event_time_end">

        <label>Volunteers Needed</label>
        <input type="number" name="volunteers_needed" required>

        <label>Providing (Snacks, Certificates, etc.)</label>
        <input type="text" name="providing">

        <label>Description</label>
        <textarea name="description" rows="4" required></textarea>

        <label>Money (if any)</label>
        <input type="number" step="0.01" name="money">

        <label>Location</label>
        <input type="text" name="location" required>

        <button type="submit" name="create_event">Create Event</button>
    </form>
</div>

</body>
</html>
