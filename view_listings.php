<?php
// Include the database connection
include 'connection.php'; 
session_start();

if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = basename($_SERVER['PHP_SELF']);
    header("Location: login.php");
    exit();
}


$user_id = $_SESSION['user_id'];

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$query = "SELECT s.shop_name, s.location, s.size, s.price, s.status, s.shop_id, s.approved 
          FROM shops s
          JOIN owners o ON s.user_id = o.user_id
          JOIN users u ON o.user_id = u.user_id
          WHERE u.user_id = ?";


$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Query Preparation Failed: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Listings</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body { 
            height: 100%;
        }

        body {
            font-family:'Montserrat', sans-serif;
            background: #f5f5f5;
            color: #333;
            min-height: 100vh;
            display: flex;       
            flex-direction: column; 
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
         @media (max-width: 1115px) {
            .navbar {
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
                padding: 15px 20px;
            }
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
        .page-content {
            padding-top: 1px;
        }
        .top-controls {
            display: flex;
            justify-content: flex-start;
            padding-left: 5%;
            margin-top: 30px;
            margin-bottom: -20px; 
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


        /* TABLE STYLES */
        .table-container {
            width: 90%;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
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
            background: #4CAF50;
            color: white;
        }
        td .btn,
        td .btn_danger {
            margin-right: 5px;
            display: inline-block;
        }
        .btn {
            color: white;
            background: #7c3aed;
            padding: 5px 10px;
            border-radius: 5px;
            text-decoration: none;
        }

        .btn:hover {
            background:rgb(0, 230, 96);
        }

        .btn_danger {
            color: white;
            background: rgb(224, 88, 88);
            padding: 5px 10px;
            border-radius: 5px;
            text-decoration: none;
        }

        .btn_danger:hover {
            background: rgb(235, 8, 8);
        }

        main {
            flex: 1; /* pushes the footer to the bottom */
        }

        .footer {
            background: #333;
            color: white;
            text-align: center;
            padding: 20px;
            width: 100%;
            margin-top: 40px;
        }

        
        /* Responsive tweaks */
            .table-responsive {
            overflow-x: auto;
            }
            @media (max-width: 768px) {
        .table-container {
            padding: 10px;
            width: 95%;
        }

        table {
            font-size: 0.85em;
            min-width: 600px; /* Triggers horizontal scroll */
        }

        td:last-child {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .btn,
        .btn_danger {
            width: 100%;
            margin: 4px 0;
            text-align: center;
        }

        .top-controls {
            justify-content: center;
            padding-left: 0;
            margin-bottom: 0;
            text-align: center;
        }

        a.back-btn {
            justify-content: center;
        }
    }
    .hamburger {
        display: none;
        font-size: 24px;
        cursor: pointer;
    }

    .nav-links a.active {
        color: green;
        text-decoration: underline;
        text-underline-offset: 8px;
    }

    /* Responsive menu */
        @media (max-width: 930px) {
        .navbar {
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
        }
        .hamburger {
            display: block;
        }
        .nav-links {
            display: none;
            flex-direction: column;
            align-items: center;
            width: 100%;
            background: white;
            position: absolute;
            top: 70px;
            left: 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            position: fixed;

        }
        .nav-links.active {
            display: flex;
        }
    }

    </style>
</head>
<body>

    <!-- Navbar -->
    <div class="navbar">
    <div class="logo">SHOP LOCATOR</div>
    <div class="hamburger" onclick="toggleMenu()">
        <i class="fas fa-bars"></i>
    </div>
    <div class="nav-links" id="navLinks">
        <a href="notifications.php" class="<?= basename($_SERVER['PHP_SELF']) == 'notifications.php' ? 'active' : '' ?>"><i class="fas fa-bell"></i> Notifications</a>
        <a href="account_settings.php" class="<?= basename($_SERVER['PHP_SELF']) == 'account_settings.php' ? 'active' : '' ?>"><i class="fas fa-user-cog"></i> Account Settings</a>
        <a href="owner_dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) == 'owner_dashboard.php' ? 'active' : '' ?>"><i class="fas fa-user-circle"></i> Account</a>
        <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </div>
</div>

    <div class="top-controls">
        <a href="#" class="back-btn" onclick="history.back(); return false;">
            <i class="fas fa-arrow-left"></i> Go Back
        </a>
    </div>
    <main>
    <div class="table-container">
        <h2>Your Listed Shops</h2>

        <?php if ($result->num_rows > 0): ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Shop Name</th>
                            <th>Location</th>
                            <th>Size</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Approval Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['shop_name']); ?></td>
                                <td><?= htmlspecialchars($row['location']); ?></td>
                                <td><?= htmlspecialchars($row['size']); ?> sq ft</td>
                                <td><?= htmlspecialchars($row['price']); ?> Ksh</td>
                                <td><?= htmlspecialchars($row['status']); ?></td>
                                <td>
                                    <?php
                                        if (is_null($row['approved'])) {
                                            echo "<span style='color: orange;'>Pending</span>";
                                        } elseif ($row['approved'] == 1) {
                                            echo "<span style='color: green;'>Approved</span>";
                                        } else {
                                            echo "<span style='color: red;'>Declined</span>";
                                        }
                                    ?>
                                </td>
                                <td>
                                    <a href="edit_shop.php?id=<?= $row['shop_id']; ?>" class="btn"><i class="fas fa-edit"></i> Edit</a>
                                    <a href="delete_shop.php?shop_id=<?= $row['shop_id']; ?>" class="btn_danger" onclick="return confirm('Are you sure?')"><i class="fas fa-trash-alt"></i> Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>No shops listed yet.</p>
        <?php endif; ?>
    </div>
</main>

<div class="footer">
    <p>&copy; <?= date("Y") ?> Shop Locator. All Rights Reserved.</p>
</div>
<script>
    function toggleMenu() {
        document.getElementById("navLinks").classList.toggle("active");
    }
</script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
