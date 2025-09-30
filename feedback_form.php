<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // redirect if not logged in
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Feedback Form</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f6f9;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }
    .feedback-container {
      background: white;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      width: 400px;
    }
    .feedback-container h3 {
      text-align: center;
      margin-bottom: 20px;
      color: #333;
    }
    label {
      font-weight: bold;
      display: block;
      margin-top: 15px;
      margin-bottom: 8px;
      color: #444;
    }
    select, textarea {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 14px;
      margin-bottom: 10px;
    }
    textarea {
      resize: none;
    }
    button {
      width: 70%;
      padding: 12px;
      background: #007bff;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
    }
    button:hover {
      background: #0056b3;
    }

    /* ⭐ Star Rating */
    .star-rating {
      direction: rtl;
      display: flex;
      justify-content: center;
    }
    .star-rating input {
      display: none;
    }
    .star-rating label {
      font-size: 28px;
      color: #ccc;
      cursor: pointer;
      transition: color 0.3s;
    }
    .star-rating input:checked ~ label,
    .star-rating label:hover,
    .star-rating label:hover ~ label {
      color: gold;
    }
  </style>
</head>
<body>

  <div class="feedback-container">
    <form method="POST" action="feedback.php">
      <h3>Feedback Form</h3>

      <label>1.How Was the system/event easy to use or attend?</label>
      <select name="easy_to_use" required>
        <option value="Yes">Yes</option>
        <option value="No">No</option>
      </select>

      <label>2. Did you find it useful and helpful?</label>
      <select name="useful" required>
        <option value="Yes">Yes</option>
        <option value="No">No</option>
      </select>

      <label>3. What did you like the most?</label>
      <textarea name="likes" rows="3" required></textarea>

      <label>4. What improvements would you suggest?</label>
      <textarea name="suggestions" rows="3"></textarea>
       
              

       <!-- ⭐ Star Rating -->

       <label>5. How would you rate your overall experience?</label>
      <div class="star-rating">
        <input type="radio" id="star5" name="rating" value="5" required><label for="star5">★</label>
        <input type="radio" id="star4" name="rating" value="4"><label for="star4">★</label>
        <input type="radio" id="star3" name="rating" value="3"><label for="star3">★</label>
        <input type="radio" id="star2" name="rating" value="2"><label for="star2">★</label>
        <input type="radio" id="star1" name="rating" value="1"><label for="star1">★</label>
      </div>


      <button type="submit">Submit Feedback</button>
    </form>
  </div>

</body>
</html>
