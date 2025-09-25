<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

$error = $success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $message = trim($_POST['message']);
    $target = $_POST['target'];

    if (empty($title) || empty($message) || empty($target)) {
        $error = "All fields are required.";
    } else {
        if ($target === 'all') {
            $stmt = $conn->prepare("INSERT INTO notifications (user_id, title, message) VALUES (NULL, ?, ?)");
            $stmt->bind_param("ss", $title, $message);
        } else {
            $stmt = $conn->prepare("INSERT INTO notifications (user_id, title, message) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $target, $title, $message);
        }

        if ($stmt->execute()) {
            $success = "✅ Notification sent successfully.";
        } else {
            $error = "❌ Failed to send notification.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Send Notification</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f3f4f6;
      margin: 0;
      padding: 40px;
    }

    .form-container {
      background-color: #fff;
      padding: 30px;
      max-width: 600px;
      margin: auto;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.08);
    }

    h2 {
      text-align: center;
      color: #1f2937;
      margin-bottom: 20px;
    }

    label {
      font-weight: 600;
      display: block;
      margin-top: 15px;
      color: #374151;
    }

    input, textarea, select {
      width: 100%;
      padding: 12px;
      margin-top: 6px;
      border: 1px solid #d1d5db;
      border-radius: 6px;
      font-size: 15px;
    }

    button {
      margin-top: 25px;
      width: 100%;
      padding: 12px;
      background-color: #28a745;
      color: white;
      font-size: 16px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }

    button:hover {
      background-color: #218838;
    }

    .message {
      margin-top: 20px;
      padding: 12px;
      border-radius: 6px;
      font-size: 14px;
    }

    .success {
      background-color: #d4edda;
      color: #155724;
    }

    .error {
      background-color: #f8d7da;
      color: #721c24;
    }

    a.back {
      display: inline-block;
      margin-top: 25px;
      text-decoration: none;
      color: #2563eb;
      text-align: center;
      width: 100%;
    }
  </style>
</head>
<body>

<div class="form-container">
  <h2>📢 Send Notification</h2>

  <?php if ($success): ?>
    <div class="message success"><?= $success ?></div>
  <?php elseif ($error): ?>
    <div class="message error"><?= $error ?></div>
  <?php endif; ?>

  <form method="POST">
    <label for="title">Notification Title:</label>
    <input type="text" name="title" id="title" required>

    <label for="message">Message:</label>
    <textarea name="message" id="message" rows="5" required></textarea>

    <label for="target">Send To:</label>
    <select name="target" id="target" required>
      <option value="all">All Users</option>
      <?php
      $res = $conn->query("SELECT user_id, full_name FROM users");
      while ($row = $res->fetch_assoc()) {
          echo "<option value='{$row['user_id']}'>" . htmlspecialchars($row['full_name']) . "</option>";
      }
      ?>
    </select>

    <button type="submit">Send Notification</button>
  </form>

  <a href="admin_dashboard.php" class="back">&larr; Back to Dashboard</a>
</div>

</body>
</html>
