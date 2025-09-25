<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

if (!isset($_GET['user_id'])) {
    echo "User ID is missing.";
    exit();
}

$user_id = intval($_GET['user_id']);

// Fetch user info
$user_sql = "SELECT * FROM users WHERE user_id = $user_id";
$user_result = $conn->query($user_sql);

if ($user_result->num_rows == 0) {
    echo "User not found.";
    exit();
}
$user = $user_result->fetch_assoc();

// Fetch user's shops
$shops_sql = "SELECT * FROM shops WHERE user_id = $user_id ORDER BY shop_id DESC";
$shops_result = $conn->query($shops_sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Details</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f4f7;
            margin: 0;
            padding: 0;
        }

        .navbar {
            background: #1a1a1a;
            color: white;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
            font-weight: bold;
        }

        .container {
            padding: 40px;
        }

        h2 {
            margin-bottom: 20px;
        }

        .user-info {
            background: white;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            border-radius: 10px;
        }

        .user-info p {
            margin: 8px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        table thead {
            background-color: #28a745;
            color: white;
        }

        table th, table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .status {
            font-weight: bold;
            padding: 5px 10px;
            border-radius: 6px;
        }

        .available {
            background: #d4edda;
            color: #155724;
        }

        .unavailable {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>

<div class="navbar">
    <h1>User Details</h1>
    <div>
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="manage_users.php">Back to Users</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="container">
    <div class="user-info">
        <h2><?= htmlspecialchars($user['name']) ?></h2>
        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
        <p><strong>Phone Number:</strong> <?= htmlspecialchars($user['phone_number']) ?></p>
        <p><strong>User ID:</strong> <?= $user['user_id'] ?></p>
    </div>

    <h3>Shops Listed by This User</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Shop Name</th>
                <th>Location</th>
                <th>Price</th>
                <th>Size</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($shops_result->num_rows > 0): ?>
            <?php while ($shop = $shops_result->fetch_assoc()): ?>
                <tr>
                    <td><?= $shop['shop_id'] ?></td>
                    <td><?= htmlspecialchars($shop['shop_name']) ?></td>
                    <td><?= htmlspecialchars($shop['location']) ?></td>
                    <td>Ksh <?= number_format($shop['price']) ?></td>
                    <td><?= htmlspecialchars($shop['size']) ?> sq ft</td>
                    <td>
                        <span class="status <?= $shop['status'] === 'available' ? 'available' : 'unavailable' ?>">
                            <?= ucfirst($shop['status']) ?>
                        </span>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="6">This user has not listed any shops.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
