<?php
include 'db.php'; // Database connection

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Current date and time
$currentDateTime = new DateTime('now', new DateTimeZone('Asia/Kolkata'));

// Fetch approved users and their events
$sql = "
    SELECT ua.user_id, ua.email, ua.event_id, e.title, e.event_date, e.event_start_time
    FROM users_applications ua
    JOIN events e ON ua.event_id = e.event_id
    WHERE ua.status = 'approved'
";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {

        // Event datetime
        $eventDateTime = new DateTime(
            $row['event_date'] . ' ' . $row['event_start_time'],
            new DateTimeZone('Asia/Kolkata')
        );

        // Time difference in seconds
        $diffSeconds = $eventDateTime->getTimestamp() - $currentDateTime->getTimestamp();
        $hoursDiff   = $diffSeconds / 3600;

        // Send reminder if event is about 10 hours away (±10 minutes tolerance)
        if ($hoursDiff <= 10 && $hoursDiff >= 9.83) {

            $mail = new PHPMailer(true);
            try {
                // SMTP settings
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'vijayutnoori81@gmail.com';           // your Gmail
                $mail->Password   = 'ouon vtlq wyhp xtaw';    // App Password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                // Sender & recipient
                $mail->setFrom('vijayutnoori81@gmail.com', 'Event Reminder');
                $mail->addAddress($row['email']);

                // Email content
                $mail->isHTML(true);
                $mail->Subject = "Reminder: {$row['title']} is starting soon!";
                $mail->Body    = "
                    <p>Hi,</p>
                    <p>This is a reminder that you are registered for the event 
                    <strong>{$row['title']}</strong> happening on 
                    <strong>{$row['event_date']} at {$row['event_start_time']}</strong>.</p>
                    <p>Please be on time!</p>
                ";

                $mail->send();
                echo "Reminder email sent to {$row['email']} for {$row['title']}<br>";

            } catch (Exception $e) {
                echo "Email could not be sent to {$row['email']}. Error: {$mail->ErrorInfo}<br>";
            }
        }
    }
}

$conn->close();
?>
