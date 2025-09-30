<?php
session_start();
include 'db.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$user_email = $_SESSION['email'];

// Fetch user applications with event info
$sql = "SELECT ea.application_id, ea.user_id, ea.full_name, ea.phone, ea.address, ea.skills, ea.applied_at, ea.status, e.title, e.event_date 
        FROM user_applications ea
        JOIN events e ON ea.event_id = e.event_id
        WHERE ea.email = ?
        ORDER BY ea.applied_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Applications</title>
<style>
body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #eef2f7; padding: 20px; }
.container { max-width: 1000px; margin: auto; background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 6px 15px rgba(0,0,0,0.1); }
h2 { text-align: center; margin-bottom: 25px; color: #333; font-size: 26px; }
table { width: 100%; border-collapse: separate; border-spacing: 0 10px; }
th { background: #007bff; color: #fff; padding: 12px; text-align: left; border-radius: 6px 6px 0 0; }
td { background: #fafafa; padding: 12px; border: 1px solid #eee; vertical-align: middle; }
tr:hover td { background: #f5f9ff; }
.btn-cancel { padding: 8px 15px; border-radius: 6px; text-decoration: none; font-weight: 500; color: white; background: #dc3545; cursor: pointer; }
.btn-cancel:hover { background: #c82333; transform: scale(1.05); }
.btn-cancel-disabled { background: #ccc; color: #666; cursor: not-allowed; transform: none !important; }
.flash { text-align: center; padding: 12px; margin-bottom: 20px; border-radius: 8px; font-weight: bold; font-size: 15px; display:none; }
.flash.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
.flash.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

/* Modal */
.modal { display: none; position: fixed; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.5); justify-content:center; align-items:center; }
.modal-content { background: #fff; padding: 20px; border-radius:10px; width:350px; text-align:center; box-shadow: 0 5px 15px rgba(0,0,0,0.3); }
.modal-buttons { margin-top: 20px; display:flex; justify-content:space-around; }
.btn { padding: 8px 15px; border-radius: 5px; cursor: pointer; border: none; }
.btn-yes { background: #28a745; color:white; }
.btn-yes:hover { background: #218838; }
.btn-no { background: #6c757d; color:white; }
.btn-no:hover { background: #5a6268; }
</style>
</head>
<body>

<div class="container">
    <h2>My Event Applications</h2>

    <div id="flash-message" class="flash"></div>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Event</th>
                <th>Date</th>
                <th>Full Name</th>
                <th>Phone</th>
                <th>Skills</th>
                <th>Address</th>
                <th>Applied At</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <?php $today = date("Y-m-d"); ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo $row['event_date']; ?></td>
                    <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                    <td><?php echo $row['phone']; ?></td>
                    <td><?php echo $row['skills']; ?></td>
                    <td><?php echo $row['address']; ?></td>
                    <td><?php echo $row['applied_at']; ?></td>
                    <td>
                        <?php
                        if ($today > $row['event_date']) {
                            echo "-";
                        } else {
                            echo ucfirst($row['status'] ?? 'Pending');
                        }
                        ?>
                    </td>
                    <td>
                        <?php 
                            $isDisabled = ($today >= $row['event_date']);
                            $btnClass = $isDisabled ? "btn-cancel-disabled" : "btn-cancel";
                        ?>
                        <button class="<?php echo $btnClass; ?>" 
                                onclick="<?php echo $isDisabled ? 'return false;' : "handleCancel('".$row['application_id']."','".$row['event_date']."')"; ?>"
                                <?php echo $isDisabled ? 'disabled' : ''; ?>>
                            Cancel
                        </button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>You haven’t applied to any events yet.</p>
    <?php endif; ?>
</div>

<!-- Modal -->
<div id="cancelModal" class="modal">
  <div class="modal-content">
    <p>Are you sure you want to cancel this event?</p>
    <div class="modal-buttons">
      <button id="confirmYes" class="btn btn-yes">Yes</button>
      <button id="confirmNo" class="btn btn-no">No</button>
    </div>
  </div>
</div>

<script>
function handleCancel(appId, eventDate) {
    let today = new Date().toISOString().split('T')[0];

    if(today === eventDate) {
        showFlash("You can't cancel the event on event date", "error", 2000);
        return;
    }

    let modal = document.getElementById("cancelModal");
    modal.style.display = "flex";

    document.getElementById("confirmYes").onclick = function() {
        modal.style.display = "none";
        showFlash("Your event canceled", "success", 1000);
        setTimeout(() => {
            window.location.href = "cancel_application.php?id=" + appId;
        }, 1000);
    }

    document.getElementById("confirmNo").onclick = function() {
        modal.style.display = "none";
        window.location.href = "my_applications.php";
    }
}

function showFlash(message, type, duration=2000) {
    let flash = document.getElementById("flash-message");
    flash.textContent = message;
    flash.className = "flash " + type;
    flash.style.display = "block";
    setTimeout(() => { flash.style.display = "none"; }, duration);
}
</script>

</body>
</html>
