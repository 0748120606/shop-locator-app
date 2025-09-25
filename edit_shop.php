<?php
session_start();
require_once('connection.php');

if (!isset($_GET['id'])) {
    echo "No shop ID provided.";
    exit();
}

$shop_id = (int)$_GET['id'];

$shop_query = "SELECT * FROM shops WHERE shop_id = ?";
$shop_stmt = $conn->prepare($shop_query);
$shop_stmt->bind_param('i', $shop_id);
$shop_stmt->execute();
$shop_result = $shop_stmt->get_result();
$shop = $shop_result->fetch_assoc();

if (!$shop) {
    echo "Shop not found!";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shop_name = $_POST['shop_name'];
    $location = $_POST['location'];
    $size = $_POST['size'];
    $price = $_POST['price'];
    $status = $_POST['status'];

    $update_query = "UPDATE shops SET shop_name = ?, location = ?, size = ?, price = ?, status = ?, updated_at = NOW() WHERE shop_id = ?";
    $update_stmt = $conn->prepare($update_query);

    if (!$update_stmt) {
        die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    }

    $update_stmt->bind_param("sssdsi", $shop_name, $location, $size, $price, $status, $shop_id);
    $update_stmt->execute();

    if ($update_stmt->affected_rows > 0) {
        header("Location: view_listings.php");
        exit();
    } else {
        echo "<p style='color:orange;'>No changes were made.</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Shop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f9f9f9;
            color: #333;
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

        h2 {
            text-align: center;
            margin-bottom: 30px;
        }
        .page-content {
            padding-top: 1px;
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
        .container {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
        }


        form {
            margin: 0 auto;
            padding: 40px;
            max-width: 600px;
            margin: auto;
            background: white;
            margin-top: 40px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        input, select {
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
            font-weight: bold;
        }

        button {
            background-color: #7c3aed;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            font-size: 1em;
            transition: background 0.3s;
        }
        
        .btn-button {
            text-align: center;
        }

        button:hover {
            background-color: rgb(43, 219, 73);
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
            <a href="notifications.php"><i class="fas fa-bell"></i> Notifications</a>
            <a href="account_settings.php"><i class="fas fa-user-cog"></i> Account Settings</a>
            <a href="owner_dashboard.php"><i class="fas fa-user-circle"></i> Account</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <div class="page-content">
    <div class="top-controls">
        <a href="#" class="back-btn" onclick="history.back(); return false;">
            <i class="fas fa-arrow-left"></i> Go Back
        </a>
    </div>

    <!-- Edit Shop Form -->
    <div class="container">
    <form method="POST">
        <h2>Edit Shop Listing</h2>

        <label for="shop_name">Shop Name</label>
        <input type="text" name="shop_name" id="shop_name" value="<?php echo htmlspecialchars($shop['shop_name']); ?>" required>

        <label for="location">Location</label>
        <input type="text" name="location" id="location" value="<?php echo htmlspecialchars($shop['location']); ?>" required>

        <label for="size">Size</label>
        <input type="text" name="size" id="size" value="<?php echo htmlspecialchars($shop['size']); ?>" required>

        <label for="price">Price</label>
        <input type="number" name="price" id="price" step="0.01" value="<?php echo htmlspecialchars($shop['price']); ?>" required>

        <label for="status">Status</label>
        <select name="status" id="status" required>
            <option value="available" <?php echo ($shop['status'] === 'available') ? 'selected' : ''; ?>>Available</option>
            <option value="occupied" <?php echo ($shop['status'] === 'occupied') ? 'selected' : ''; ?>>Occupied</option>
        </select>

        <div class="btn-button">
            <button type="submit">Save Changes</button>
        </div>
    </form>
    </div>
    </div>
    <div class="footer">
    <p>&copy; <?= date("Y") ?> Shop Locator. All Rights Reserved.</p>
</div>
</body>
</html>
