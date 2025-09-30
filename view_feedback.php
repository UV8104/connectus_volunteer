<?php
session_start();
include 'db.php';

// Check if admin
if ($_SESSION['role'] != 'admin') {
    echo "Access denied!";
    exit();
}
$sql = "SELECT f.id, u.username, f.rating, f.easy_to_use, f.useful, f.likes, f.suggestions, f.created_at
        FROM feedback_tbl f
        JOIN users.users_login u ON f.user_id = u.id
        ORDER BY f.created_at DESC";


$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Feedback</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f0f2f5;
      margin: 0;
      padding: 0;
    }

    .container {
      max-width: 1100px;
      margin: 40px auto;
      padding: 20px;
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    h2 {
      text-align: center;
      color: #222;
      margin-bottom: 20px;
      font-size: 26px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      overflow: hidden;
      border-radius: 10px;
    }

    th, td {
      padding: 14px;
      text-align: left;
    }

    th {
      background: #007bff;
      color: #fff;
      font-weight: 600;
      font-size: 15px;
      text-transform: uppercase;
    }

    tr {
      border-bottom: 1px solid #e0e0e0;
    }

    tr:nth-child(even) {
      background: #f9fbfd;
    }

    tr:hover {
      background: #f1f7ff;
      transition: 0.2s ease-in-out;
    }

    td {
      font-size: 14px;
      color: #333;
    }

    .stars {
      color: #ffb400;
      font-size: 16px;
    }

    @media (max-width: 768px) {
      table, thead, tbody, th, td, tr {
        display: block;
      }
      th {
        display: none;
      }
      td {
        padding: 10px;
        border: none;
        position: relative;
        padding-left: 50%;
      }
      td::before {
        content: attr(data-label);
        position: absolute;
        left: 10px;
        font-weight: bold;
        text-transform: uppercase;
        color: #555;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>All User Feedback</h2>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>User</th>
          <th>Easy to Use?</th>
          <th>Useful?</th>
          <th>Likes</th>
          <th>Suggestions</th>
          <th>Rating</th>
          <th>Date</th>
        </tr>
      </thead>
      <tbody>
        <?php while($row = $result->fetch_assoc()) { ?>
        <tr>
          <td data-label="ID"><?= $row['id'] ?></td>
          <td data-label="User"><?= htmlspecialchars($row['username']) ?></td>
          <td data-label="Easy to Use"><?= $row['easy_to_use'] ?></td>
          <td data-label="Useful"><?= $row['useful'] ?></td>
          <td data-label="Likes"><?= htmlspecialchars($row['likes']) ?></td>
          <td data-label="Suggestions"><?= htmlspecialchars($row['suggestions']) ?></td>
          <td data-label="Rating">
            <span class="stars">
              <?= str_repeat("⭐", (int)$row['rating']); ?>
            </span>
          </td>
          <td data-label="Date"><?= $row['created_at'] ?></td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</body>
</html>
