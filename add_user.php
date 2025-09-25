<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

$error = $success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $user_type = $_POST['user_type'];
    $token = bin2hex(random_bytes(16));

    $verification_link = "http://yourdomain.com/verify_email.php?token=$token";

    if (empty($full_name) || empty($email) || empty($password) || empty($user_type)) {
        $error = "All fields are required.";
    } else {
        $check = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "Email already exists.";
        } else {
            $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, user_type, verification_token) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $full_name, $email, $password, $user_type, $token);

            if ($stmt->execute()) {
                $subject = "Verify Your Email";
                $message = "Hi $full_name,\n\nPlease verify your email by clicking the link below:\n$verification_link\n\nThanks!";
                $headers = "From: no-reply@yourdomain.com";

                mail($email, $subject, $message, $headers);

                $success = "User added successfully. A verification email has been sent.";
            } else {
                $error = "Error: " . $stmt->error;
            }
            $stmt->close();
        }
        $check->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add New User</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f3f4f6;
      padding: 40px;
    }
    .form-container {
      background: #fff;
      padding: 30px;
      max-width: 500px;
      margin: auto;
      border-radius: 8px;
      box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }
    h2 {
      margin-bottom: 20px;
      color: #333;
    }
    label {
      display: block;
      margin-top: 12px;
      font-weight: bold;
    }
    input, select {
      width: 100%;
      padding: 10px;
      margin-top: 6px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }
    button {
      margin-top: 20px;
      padding: 12px;
      width: 100%;
      background: #28a745;
      color: white;
      border: none;
      font-size: 16px;
      border-radius: 6px;
      cursor: pointer;
    }
    .message {
      margin-top: 15px;
      padding: 10px;
      border-radius: 5px;
    }
    .success { background: #d4edda; color: #155724; }
    .error { background: #f8d7da; color: #721c24; }
    a {
      display: inline-block;
      margin-top: 20px;
      text-decoration: none;
      color: #28a745;
    }
  </style>
</head>
<body>
  <div class="form-container">
    <h2>Add New User</h2>

    <?php if ($success): ?>
      <div class="message success"><?= $success ?></div>
    <?php elseif ($error): ?>
      <div class="message error"><?= $error ?></div>
    <?php endif; ?>

    <form method="post">
      <label>Full Name:</label>
      <input type="text" name="full_name" required>

      <label>Email:</label>
      <input type="email" name="email" required>

      <label>Password:</label>
      <input type="password" name="password" required>

      <label>User Type:</label>
      <select name="user_type" required>
        <option value="">Select type</option>
        <option value="admin">Admin</option>
        <option value="owner">Owner</option>
      </select>

      <button type="submit">Add User</button>
    </form>

    <a href="admin_dashboard.php">&larr; Back to Dashboard</a>
  </div>
</body>
</html>
