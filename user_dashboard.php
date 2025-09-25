<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Fetch recent notifications
$notifications = $conn->query("SELECT message, created_at FROM notifications WHERE user_id = $userId ORDER BY created_at DESC LIMIT 5");

// Get user full name
$userQuery = $conn->query("SELECT full_name FROM users WHERE user_id = $userId");
$user = $userQuery->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .navbar {
            background: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 30px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .navbar h2 {
            margin: 0;
            color: #28a745;
        }

        .nav-icons {
            display: flex;
            align-items: center;
        }

        .dropdown-container {
            position: relative;
            margin-left: 20px;
        }

        .dropdown-icon {
            font-size: 20px;
            color: #333;
            cursor: pointer;
        }

        .dropdown {
            position: absolute;
            top: 30px;
            right: 0;
            background: white;
            border: 1px solid #ccc;
            border-radius: 6px;
            padding: 10px;
            display: none;
            width: 250px;
            z-index: 1000;
        }

        .dropdown-container:hover .dropdown {
            display: block;
        }

        .dropdown a {
            display: block;
            color: #333;
            padding: 8px;
            text-decoration: none;
        }

        .dropdown a:hover {
            background: #f1f1f1;
        }

        .dashboard-content {
            padding: 40px;
        }

        .card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .card h3 {
            margin-top: 0;
        }

        .notification-item {
            border-bottom: 1px solid #eee;
            padding: 10px 0;
        }

        .notification-item:last-child {
            border-bottom: none;
        }

        .notification-time {
            font-size: 12px;
            color: #888;
        }

        a.fav-link {
            text-decoration: none;
            color: #28a745;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="navbar">
    <h2>Shop Locator</h2>
    <div class="nav-icons">
        <!-- Notifications -->
        <div class="dropdown-container">
            <i class="fas fa-bell dropdown-icon"></i>
            <div class="dropdown">
                <strong>Notifications</strong>
                <hr>
                <?php if ($notifications->num_rows > 0): ?>
                    <?php while ($note = $notifications->fetch_assoc()): ?>
                        <div class="notification-item">
                            <?= htmlspecialchars($note['message']) ?>
                            <div class="notification-time"><?= date('M d, Y H:i', strtotime($note['created_at'])) ?></div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No notifications yet.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Favorites -->
        <div class="dropdown-container">
            <a href="favorites.php" title="Favorite Shops"><i class="fas fa-heart dropdown-icon"></i></a>
        </div>

        <!-- Account Settings -->
        <div class="dropdown-container">
            <i class="fas fa-user-circle dropdown-icon"></i>
            <div class="dropdown">
                <span style="display:block; padding:8px; color: #555;">👋 <?= htmlspecialchars($user['full_name']) ?></span>
                <hr>
                <a href="account_settings.php"><i class="fas fa-user-cog"></i> Account Settings</a>
                <a href="change_password.php"><i class="fas fa-key"></i> Change Password</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </div>
</div>

<div class="dashboard-content">
    <div class="card">
        <h3>Welcome, <?= htmlspecialchars($user['full_name']) ?>!</h3>
        <p>Use the menu above to manage your account, view your favorites, or check your notifications.</p>
    </div>

    <div class="card">
        <h3>Your Quick Links</h3>
        <ul>
            <li><a class="fav-link" href="favorites.php">❤️ View Favorite Shops</a></li>
            <li><a class="fav-link" href="account_settings.php">🛠️ Update Account Information</a></li>
            <li><a class="fav-link" href="change_password.php">🔐 Change Your Password</a></li>
        </ul>
    </div>
</div>

</body>
</html>
