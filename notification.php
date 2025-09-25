<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch notifications for the user
$sql = "SELECT message, created_at FROM notifications WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Notifications</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        .navbar {
            background: #343a40;
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
        }

        .container {
            padding: 40px;
        }

        .notification {
            background: white;
            border-left: 5px solid #007bff;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        .notification small {
            color: #666;
            display: block;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="navbar">
    <h1>User Dashboard</h1>
    <div>
        <a href="favorites.php"><i class="fas fa-heart"></i> Favorites</a>
        <a href="notifications.php"><i class="fas fa-bell"></i> Notifications</a>
        <a href="account_settings.php"><i class="fas fa-user-cog"></i> Account Settings</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>
<div class="container">
    <h2>Your Notifications</h2>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="notification">
                <?= htmlspecialchars($row['message']) ?>
                <small>Received on <?= date('M d, Y H:i', strtotime($row['created_at'])) ?></small>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No notifications found.</p>
    <?php endif; ?>
</div>
<div class="footer">
    &copy; 2025 Shop Locator. All rights reserved.
</div>
</body>
</html>
