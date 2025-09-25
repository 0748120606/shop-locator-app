<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $phone_number = trim($_POST['phone_number']);

    $errors = [];

    // Validate inputs
    if (empty($full_name) || empty($email) || empty($username) || empty($phone_number)) {
        $errors[] = "All fields are required.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (empty($errors)) {
        $sql = "UPDATE users SET full_name = ?, email = ?, username = ?, phone_number = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $full_name, $email, $username, $phone_number, $user_id);

        if ($stmt->execute()) {
            // Redirect with success message
            header("Location: owner_dashboard.php?update=success");
            exit();
        } else {
            $errors[] = "Update failed. Please try again.";
        }

        $stmt->close();
    }
}

// Fetch current user info
$sql = "SELECT full_name, email, username, phone_number FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    echo "User not found.";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Account Information</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
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
        .account-form {
            margin: 0 auto;
            padding: 40px;
            max-width: 600px;
            margin: auto;
            background: white;
            margin-top: 40px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);

        }
        .account-form input, select {
            display: block;
            width: 100%;
            margin-bottom: 15px;
            padding: 12px 20px;     
            border: 1.5px solid #3333;   
            box-sizing: border-box;  
            border-radius: 10px; 
        }

        label {
            font-size: 1em;
            margin-top: 5px;
            display: block;
            font-weight:bold;
            color: #333333;
        }

        .account-form label {
            display: block;
            margin-bottom: 5px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .account-form button {
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
        .account-form button:hover{
            background: #27ae60;
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
                padding: 20px 15px;
            }
            .nav-links {
                flex-direction: column;
                gap: 10px;
            }
            .hero h1 {
                font-size: 2em;
            }
            .hero p {
                font-size: 1em;
            }
            .hero button {
                font-size: 0.9em;
            }
        }
        @media (max-width: 768px) {
        .container {
            padding: 20px;
            margin-top: 20px;
        }
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
    <div class="top-controls">
        <a href="#" class="back-btn" onclick="history.back(); return false;">
            <i class="fas fa-arrow-left"></i> Go Back
        </a>
    </div>

<div class="account-form">
    
<h2>Edit Account Information</h2>
    <form action="update_account.php" method="POST">
        <label for="full_name">Full Name:</label>
        <input type="text" name="full_name" id="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>

        <label for="email">Email:</label>
        <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

        <label for="username">Username:</label>
        <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>

        <label for="phone_number">Phone Number:</label>
        <input type="text" name="phone_number" id="phone_number" value="<?php echo htmlspecialchars($user['phone_number']); ?>" required>

        <div class="btn-button">
        <button type="submit">Update Info</button>
        </div>
    </form>
</div>
<div class="footer">
    <p>&copy; <?= date("Y") ?> Shop Locator. All Rights Reserved.</p>
</div>
</body>
</html>
