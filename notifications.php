<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch notifications with ID for deletion
$sql = "SELECT id, message, created_at FROM notifications WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Your Notifications</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
            padding: 0;
            margin: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f9f9f9;
            color: #333;
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            position: sticky;
            top: 0;
            width: 100%;
            background: white;
            color: #333;
            padding: 25px 200px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .navbar a {
            color: #333;
            text-decoration: none;
            padding: 10px;
            transition: color 0.3s, text-decoration-color 0.3s;
            font-weight: bold;
        }
        .navbar a:hover {
            color: green;
            text-decoration: underline;
            text-decoration-color: green;
            text-underline-offset: 8px;
            font-weight: bold;
        }
        
        .logo {
            font-size: 1.6em;
            font-weight: bold;
        }
        .page-wrapper {
            flex: 1;
        }
        .container {
            max-width: 800px;
            margin: 40px auto;
            padding: 0 20px;
        }
        .notification {
            position: relative;
            background: #fff;
            border-left: 5px solid #7c3aed;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 15px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }

        .notification small {
            color: #666;
            display: block;
            margin-top: 10px;
            font-size: 14px;
        }

        .dismiss-btn {
            position: absolute;
            top: 12px;
            right: 15px;
            font-size: 18px;
            color: #aaa;
            cursor: pointer;
            transition: color 0.2s;
        }

        .dismiss-btn:hover {
            color: #e74c3c;
        }

        .empty-state {
            background: #fff;
            border-radius: 12px;
            text-align: center;
            padding: 40px 30px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        }

        .empty-state i {
            font-size: 40px;
            color: #d1d5db;
            margin-bottom: 15px;
        }

        .empty-state h3 {
            font-size: 20px;
            margin-bottom: 10px;
        }

        .empty-state p {
            color: #6b7280;
            font-size: 16px;
        }

        .empty-state a.button {
            display: inline-block;
            margin-top: 20px;
            background-color: #7c3aed;
            color: #fff;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.3s;
        }

        .empty-state a.button:hover {
            background-color: #5b21b6;
        }
        .footer {
            background: #333;
            color: white;
            text-align: center;
            padding: 20px;
            width: 100%;
            margin-top: 40px;
        }
        
        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                text-align: center;
                padding: 20px;
            }
            .nav-links {
                flex-direction: column;
                gap: 10px;
            }
        }
        /* Responsive tweaks */
                .table-responsive {
            overflow-x: auto;
        }
    </style>
</head>
<body>
    
<!-- Navbar -->
<div class="navbar">
        <div class="logo">SHOP LOCATOR</div>
        <div class="nav-links">
        <a href="notifications.php"><i class="fas fa-bell"></i>Notifications</a>
        <a href="account_settings.php"><i class="fas fa-user-cog"></i> Account Settings</a>
        <a href="owner_dashboard.php"><i class="fas fa-user-circle dropdown-icon"></i> Account</a>
        <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>
    </div>
<div class="page-wrapper">
<div class="container">
    <h2>Your Notifications</h2>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="notification" id="notif-<?= $row['id'] ?>">
                <span class="dismiss-btn" onclick="deleteNotification(<?= $row['id'] ?>)">
                    <i class="fas fa-times-circle"></i>
                </span>
                <?= htmlspecialchars($row['message']) ?><br>
                <small>Received on <?= date('M d, Y H:i', strtotime($row['created_at'])) ?></small>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-bell-slash"></i>
            <h3>No notifications yet</h3>
            <p>You don't have any new notifications at the moment. Come back later or explore available shops.</p>
        </div>
    <?php endif; ?>
</div>
</div>
<div class="footer">
    <p>&copy; <?= date("Y") ?> Shop Locator. All Rights Reserved.</p>
</div>
<script>
function deleteNotification(id) {
    fetch('delete_notification.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + encodeURIComponent(id)
    })
    .then(response => response.text())
    .then(data => {
        if (data.trim() === 'success') {
            document.getElementById('notif-' + id).remove();
        } else {
            alert('Failed to delete notification.');
        }
    });
}
</script>

</body>
</html>
