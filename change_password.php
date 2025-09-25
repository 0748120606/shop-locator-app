<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    // Fetch current password hash
    $stmt = $conn->prepare("SELECT password FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!password_verify($current, $user['password'])) {
        $message = "Current password is incorrect.";
    } elseif ($new !== $confirm) {
        $message = "New passwords do not match.";
    } else {
        // Update password
        $hashed = password_hash($new, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
        $stmt->bind_param("si", $hashed, $user_id);
        if ($stmt->execute()) {
            $message = "Password changed successfully.";
        } else {
            $message = "Failed to update password.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Change Password</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family:'Montserrat', sans-serif;
            background: #f9f9f9;
            margin: 0;
            padding: 0;
        }

        /* NAVBAR STYLES */
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
        .nav-links {
            display: flex;
            gap: 10px;
            font-size: 1em;
        }
        .logo {
            font-size: 1.6em;
            font-weight: bold;
        }
        .top-controls {
            display: flex;
            justify-content: flex-start;
            padding: 10px 20px;
            margin-bottom: -50px; 
        }

        a.back-btn {
            background: none;
            border: none;
            color: #7c3aed;
            font-size: 16px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-weight: 500;
            padding: 10px 15px;
            margin: 10px 0;
            text-decoration: none;
            transition: color 0.3s, text-decoration 0.3s;
        }

        a.back-btn i {
            font-size: 16px;
        }

        a.back-btn:hover {
            color: #22c55e;
            text-decoration: underline;
        }
        .page-wrapper {
            flex: 1;
        }
        .container {
            width: 90%;
            max-width: 600px;
            margin: 40px auto 0 auto;
            padding: 40px;
            background: white;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            border-radius: 8px;
        }


        h2 {
            margin-bottom: 20px;
            text-align:center;
        }
        form label {
            display: block;
            margin-bottom: 5px;
            font-weight:bold;
            color:#333333;
        }

        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        button {
            background:  #7c3aed;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }
        .btn-button {
            text-align: center;
        }
        button:hover{
            background: #27ae60;
        }

        .message {
            margin-bottom: 20px;
            padding: 10px;
            background: #fff3cd;
            color: #856404;
            border-left: 4px solid #ffc107;
            border-radius: 5px;
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
            .container {
                padding: 20px;
                margin-top: 20px;
                width: 95%;
            }
        }

    </style>
</head>
<body>
<div class="navbar">
    <div class="logo">SHOP LOCATOR</div>
    <div class="nav-links">
        <a href="notifications.php"><i class="fas fa-bell"></i> Notifications</a>

        <?php
        $redirectTarget = 'login.php?redirect=' . urlencode('owner_dashboard.php');
        if (isset($_SESSION['user_id'])) {
            $redirectTarget = $_SESSION['user_type'] === 'client' ? 'user_dashboard.php' : 'owner_dashboard.php';
        }
        ?>
        <a href="<?php echo $redirectTarget; ?>">
            <i class="fas fa-user-circle dropdown-icon"></i> Account
        </a>

        <a href="account_settings.php"><i class="fas fa-user-cog"></i> Account Settings</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>
<div class="top-controls">
        <a href="#" class="back-btn" onclick="history.back(); return false;">
            <i class="fas fa-arrow-left"></i> Go Back
        </a>
    </div>
<div class="container">
    <h2>Change Password</h2>

    <?php if ($message): ?>
        <div class="message"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <label>Current Password:</label>
        <input type="password" name="current_password" required>

        <label>New Password:</label>
        <input type="password" name="new_password" required>

        <label>Confirm New Password:</label>
        <input type="password" name="confirm_password" required>
        <div class=btn-button>
        <button type="submit">Update Password</button>
        <div>
    </form>
    </div>
    </div>
    </div>
    <div class="footer">
    <p>&copy; <?= date("Y") ?> Shop Locator. All Rights Reserved.</p>
        </div>
</body>
</html>
