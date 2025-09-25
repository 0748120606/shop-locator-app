<?php
include 'connection.php';

// Handle Reply
if (isset($_POST['reply']) && isset($_POST['phone']) && isset($_POST['reply_message'])) {
    $userPhone = $_POST['phone'];
    $replyMessage = $_POST['reply_message'];

    $stmt = $conn->prepare("INSERT INTO notifications (user_phone, message) VALUES (?, ?)");
    $stmt->bind_param("ss", $userPhone, $replyMessage);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Reply sent successfully.');window.location.href='view_messages.php';</script>";
}

// Fetch messages
$messages = $conn->query("SELECT * FROM messages ORDER BY submitted_at DESC");

// Predefined reply options
$predefinedReplies = [
    "Thank you for contacting us. We will get back to you shortly.",
    "Your message has been received. We are reviewing your request.",
    "Your booking has been approved. Congratulations!",
    "Unfortunately, we cannot approve your booking at this time.",
    "Please provide more details regarding your request. Thank you!"
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Messages - Admin Panel</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #f4f7f9;
      margin: 0;
      padding: 0;
    }
    .container {
      max-width: 1200px;
      margin: 50px auto;
      padding: 20px;
      background: white;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    h2 {
      text-align: center;
      margin-bottom: 30px;
      font-size: 32px;
      color: #28a745;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    th, td {
      padding: 15px;
      border: 1px solid #ddd;
      text-align: left;
    }
    th {
      background-color: #28a745;
      color: white;
    }
    tr:nth-child(even) {
      background-color: #f9f9f9;
    }
    .reply-form {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }
    select, textarea, button {
      width: 100%;
      padding: 8px;
      border-radius: 6px;
      border: 1px solid #ccc;
    }
    button {
      background-color: #28a745;
      color: white;
      font-weight: bold;
      cursor: pointer;
      transition: background 0.3s;
    }
    button:hover {
      background-color: #218838;
    }
    @media (max-width: 768px) {
      th, td {
        font-size: 14px;
        padding: 10px;
      }
      h2 {
        font-size: 26px;
      }
    }
  </style>
</head>
<body>

<div class="container">
  <h2><i class="fas fa-envelope"></i> User Messages</h2>

  <?php if ($messages && $messages->num_rows > 0): ?>
    <div style="overflow-x:auto;">
      <table>
        <thead>
          <tr>
            <th>User Name</th>
            <th>Phone</th>
            <th>Message</th>
            <th>Reply</th>
          </tr>
        </thead>
        <tbody>
        <?php while($row = $messages->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['phone_number']) ?></td>
            <td><?= nl2br(htmlspecialchars($row['message'])) ?></td>
            <td>
              <form method="post" class="reply-form">
                <input type="hidden" name="phone" value="<?= htmlspecialchars($row['phone_number']) ?>">
                <select name="reply_message" required>
                  <option value="">Select a reply...</option>
                  <?php foreach ($predefinedReplies as $reply): ?>
                    <option value="<?= htmlspecialchars($reply) ?>"><?= htmlspecialchars($reply) ?></option>
                  <?php endforeach; ?>
                </select>
                <button type="submit" name="reply"><i class="fas fa-paper-plane"></i> Send Reply</button>
              </form>
            </td>
          </tr>
        <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <p style="text-align: center; font-size: 18px;">No messages available.</p>
  <?php endif; ?>
</div>

</body>
</html>
