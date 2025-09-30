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

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {

        // Event datetime
        $eventDateTime = new DateTime($row['event_date'] . ' ' . $row['event_start_time'], new DateTimeZone('Asia/Kolkata'));

        // Calculate difference in hours
        $interval = $currentDateTime->diff($eventDateTime);
        $hoursDiff = ($interval->days * 24) + $interval->h + ($interval->i / 60); // convert to hours

        // Send reminder if event is approx 10 hours away (10 ± 0.5 hours)
        if ($hoursDiff >= 9.5 && $hoursDiff <= 10.5) {

            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.example.com'; 
                $mail->SMTPAuth = true;
                $mail->Username = '';
                $mail->Password = '';
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom('noreply@example.com', 'Event Reminder');
                $mail->addAddress($row['email']);

                $mail->isHTML(true);
                $mail->Subject = "Reminder: Your Event is Coming Up!";
                $mail->Body = "
                    <p>Hi there,</p>
                    <p>This is a friendly reminder that you are registered for the event <strong>{$row['title']}</strong> happening on <strong>{$row['event_date']} at {$row['event_start_time']}</strong>.</p>
                    <p>Be ready and make sure to attend on time!</p>
                    <p>See you there!</p>
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
