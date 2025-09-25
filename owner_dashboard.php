<?php
session_start();

// Include your DB connection file
require_once "connection.php"; // Make sure this path is correct and $conn is available

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = basename($_SERVER['PHP_SELF']);
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$owner_name = 'Shop Owner';

// Fetch full_name from database
if ($user_id) {
    $stmt = $conn->prepare("SELECT full_name FROM users WHERE user_id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($fetched_name);

        if ($stmt->fetch()) {
            $owner_name = $fetched_name;
        }

        $stmt->close();
    } else {
        die("Prepare failed: " . $conn->error);
    }
}

$notification_count = 0;
$notif_stmt = $conn->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
if ($notif_stmt) {
    $notif_stmt->bind_param("i", $user_id);
    $notif_stmt->execute();
    $notif_stmt->bind_result($notification_count);
    $notif_stmt->fetch();
    $notif_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Owner Dashboard - Shop Locator</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f9fafb;
            color: #333;
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .wrapper {
            min-height: 100%;
            flex-direction: column;
            flex: 1;
        }

        .navbar {
            position: sticky;
            top: 0;
            background: white;
            color: #333;
            padding: 25px 200px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
        }

        .navbar .logo {
            font-size: 1.6em;
            font-weight: bold;
            color: #333;
        }

        .nav-links {
            display: flex;
            align-items: center;
        }

        .nav-links a {
            text-decoration: none;
            color: #333;
            font-weight: bold;
            margin-left: 20px;
            transition: color 0.3s;
        }

        .nav-links a:hover {
            color: green;
        }

        .hamburger {
            display: none;
            flex-direction: column;
            cursor: pointer;
        }

        .hamburger div {
            width: 25px;
            height: 3px;
            background-color: #333;
            margin: 4px 0;
            transition: 0.4s;
        }

        .dashboard-container {
            max-width: 1100px;
            margin: 40px auto;
            padding: 20px;
            flex: 1;
        }

        .welcome {
            font-size: 1.2em;
            color: #27ae60;
            margin-bottom: 20px;
        }

        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 25px;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            text-align: center;
            transition: transform 0.3s;
        }

        .card:hover {
            transform: scale(1.03);
        }

        .card i {
            font-size: 2em;
            color: #27ae60;
            margin-bottom: 10px;
        }

        .card h3 {
            font-size: 1.2em;
            margin-bottom: 10px;
        }

        .card p {
            font-size: 0.95em;
            color: #555;
        }

        .card a {
            display: inline-block;
            margin-top: 15px;
            text-decoration: none;
            color: #fff;
            background: #7c3aed;
            padding: 8px 20px;
            border-radius: 5px;
        }

        .card a:hover {
            background: #27ae60;
        }

        .notification-icon {
            position: relative;
            margin-left: 20px;
            color: #333;
            text-decoration: none;
        }

        .notif-count {
            position: absolute;
            top: -6px;
            right: -10px;
            background: #27ae60;
            color: white;
            font-size: 12px;
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 50%;
        }

        .footer {
            text-align: center;
            margin-top: 60px;
            padding: 20px;
            background: #333;
            color: white;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .accessibility-sidebar {
            position: fixed;
            top: 100px;
            right: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 12px rgba(0, 0, 0, 0.1);
            padding: 15px;
            width: 230px;
            z-index: 9999;
            transition: all 0.3s ease;
            font-family: 'Montserrat', sans-serif;
        }

        .accessibility-sidebar h4 {
            font-weight: 600;
            margin-bottom: 10px;
            color: #333;
        }

        .accessibility-sidebar button {
            display: block;
            width: 100%;
            margin: 6px 0;
            padding: 10px;
            border: none;
            border-radius: 6px;
            background: #f4f4f4;
            color: #222;
            font-weight: 500;
            cursor: pointer;
            transition: 0.3s;
        }

        .accessibility-sidebar button:hover {
            background: #0d6efd;
            color: #fff;
        }

        #toggle-accessibility {
            position: fixed;
            top: 70px;
            right: 20px;
            background: #0d6efd;
            color: white;
            border: none;
            border-radius: 50%;
            width: 45px;
            height: 45px;
            font-size: 20px;
            cursor: pointer;
            z-index: 10000;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
         @media (max-width: 1115px) {
            .navbar {
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
                padding: 15px 20px;
            }
        }

        @media (max-width: 1020px) {
            .navbar {
                display: flex;
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
                padding: 15px 20px;
            }

            .hamburger {
                display: flex;
            }

            .nav-links {
                position: fixed;
                top: 70px;
                left: -250px;
                background-color: white;
                height: auto;
                max-height: 300px; 
                width: 220px;
                padding: 20px 0;
                flex-direction: column;
                align-items: flex-start;
                transition: left 0.3s ease;
                z-index: 999;
                box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            }
            .nav-links a {
                margin: 15px 20px;
                width: 100%;
                text-align: left;
                font-size: 1.2em;
            }

            .nav-links.active {
                left: 0;
            }

            .dashboard-container {
                padding: 15px;
            }

            body {
                font-size: 14px;
                padding: 0;
            }
        }
    </style>
</head>
<body>
<div class="wrapper">
<div class="navbar">
    <div class="logo">SHOP LOCATOR</div>
    <div class="hamburger" onclick="toggleMenu()">
        <div></div>
        <div></div>
        <div></div>
    </div>
    <div class="nav-links">
        <a href="notifications.php" class="notification-icon">
            <span style="position: relative;">
                <i class="fas fa-bell"></i>
                <?php if ($notification_count > 0): ?>
                    <span class="notif-count"><?= $notification_count ?></span>
                <?php endif; ?>
            </span>
        </a>

        <a href="view_listings.php"><i class="fa-solid fa-shop"></i> View Listings</a>
        <a href="booking.php"><i class="fa-solid fa-calendar-check"></i> Book Meeting</a>
        <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </div>
</div>
<?php
if (isset($_SESSION['booking_success'])) {
    echo '<div id="booking-message" class="flash-message" style="background: #d4edda; color: #155724; padding: 15px; margin: 20px auto; max-width: 800px; border: 1px solid #c3e6cb; border-radius: 5px; text-align: center;">' . $_SESSION['booking_success'] . '</div>';
    unset($_SESSION['booking_success']);
}
if (isset($_SESSION['booking_error'])) {
    echo '<div id="booking-message" class="flash-message" style="background: #f8d7da; color: #721c24; padding: 15px; margin: 20px auto; max-width: 800px; border: 1px solid #f5c6cb; border-radius: 5px; text-align: center;">' . $_SESSION['booking_error'] . '</div>';
    unset($_SESSION['booking_error']);
}
?>
<div class="dashboard-container">
    <div class="welcome">Welcome, <?= htmlspecialchars($owner_name) ?>!</div>
    <?php if (isset($_GET['update']) && $_GET['update'] === 'success'): ?>
        <div class="success-message">✅ Account updated successfully!</div>
    <?php endif; ?>
    <div class="card-grid">
        <div class="card">
            <i class="fa-solid fa-store"></i>
            <h3>Manage Listings</h3>
            <p>Edit, update, or remove your shop listings anytime.</p>
            <a href="view_listings.php">Manage</a>
        </div>
        <div class="card">
            <i class="fa-solid fa-plus"></i>
            <h3>Add New Shop</h3>
            <p>List a new shop to attract potential clients.</p>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="listing_form.php#shopForm">List Your Shop(s)</a>
            <?php else: ?>
                <a href="login.php?redirect=listing_form.php%23shop-form">List Your Shop</a>
            <?php endif; ?>
        </div>
        <div class="card">
            <i class="fa-solid fa-user-gear"></i>
            <h3>Account Settings</h3>
            <p>Update your profile and account information.</p>
            <a href="account_settings.php">Settings</a>
        </div>
        <div class="card">
            <i class="fa-solid fa-lock"></i>
            <h3>Change Password</h3>
            <p>Keep your account secure with a new password.</p>
            <a href="change_password.php">Change</a>
        </div>
    </div>
</div>
</div>
<div class="footer">
    &copy; 2025 Shop Locator. All rights reserved.
</div>
<script>
    function toggleMenu() {
        const navLinks = document.querySelector('.nav-links');
        navLinks.classList.toggle('active');
    }

    window.addEventListener("DOMContentLoaded", () => {
        document.querySelectorAll(".flash-message").forEach((message) => {
            setTimeout(() => {
                message.style.transition = "opacity 0.5s ease-out";
                message.style.opacity = "0";
                setTimeout(() => message.remove(), 500);
            }, 5000);
        });
    });
</script>
</body>
</html>
