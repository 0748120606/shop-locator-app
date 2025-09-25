<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

$sql = "SELECT s.shop_id, s.shop_name, s.location, s.price, s.size, s.status, u.full_name 
        FROM shops s
        JOIN users u ON s.user_id = u.user_id
        WHERE s.approved IS NULL
        ORDER BY s.shop_id DESC";

$result = $conn->query($sql);
if (!$result) {
    die("Database query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pending Shops</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #1a73e8;
            --danger-color: #e53935;
            --success-color: #43a047;
            --bg-color: #f4f6f9;
            --text-color: #333;
            --card-bg: #fff;
        }

        body.dark-mode {
            --bg-color: #121212;
            --text-color: #f0f0f0;
            --card-bg: #1e1e1e;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: var(--bg-color);
            color: var(--text-color);
            margin: 0;
        }

        .navbar {
            background-color: #1a1a1a;
            color: white;
            padding: 16px 20px;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            align-items: center;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
        }

        .navbar a:hover {
            color: #ffc107;
        }

        .container {
            padding: 20px;
        }

        h2 {
            margin-bottom: 20px;
        }

        .controls {
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        button {
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }

        .dark-toggle {
            background-color: #444;
            color: white;
        }

        .export-excel {
            background-color: var(--primary-color);
            color: white;
        }

        .export-pdf {
            background-color: #f44336;
            color: white;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: var(--card-bg);
            overflow-x: auto;
        }

        thead {
            background-color: var(--primary-color);
            color: white;
        }

        th, td {
            padding: 14px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        tr:hover {
            background-color: rgba(0, 0, 0, 0.03);
        }

        .actions a {
            padding: 6px 10px;
            margin-right: 5px;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .btn-view { background: #6c757d; }
        .btn-approve { background: var(--success-color); }
        .btn-decline { background: var(--danger-color); }

        .btn-view:hover { background: #5a6268; }
        .btn-approve:hover { background: #388e3c; }
        .btn-decline:hover { background: #c62828; }

        .no-results {
            text-align: center;
            padding: 30px;
            color: #888;
        }

        @media screen and (max-width: 768px) {
            table, thead, tbody, th, td, tr {
                display: block;
            }

            th {
                background: var(--primary-color);
                color: white;
            }

            td {
                padding: 10px;
                border-bottom: 1px solid #ccc;
                position: relative;
            }

            td:before {
                content: attr(data-label);
                font-weight: bold;
                display: block;
                color: #555;
                margin-bottom: 5px;
            }

            thead {
                display: none;
            }

            .actions {
                display: flex;
                flex-direction: column;
                gap: 5px;
            }
        }
    </style>
</head>
<body>

<div class="navbar">
    <h1>Pending Shop Approvals</h1>
    <div>
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="manage_shops.php">Manage Shops</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="container">
    <h2>Shops Awaiting Approval</h2>
    <div class="controls">
        <button class="dark-toggle" onclick="toggleDarkMode()">Toggle Dark Mode</button>
        <button class="export-excel" onclick="exportTableToExcel('shopTable')">Export to Excel</button>
        <button class="export-pdf" onclick="window.print()">Export to PDF</button>
    </div>

    <table id="shopTable">
        <thead>
        <tr>
            <th>ID</th>
            <th>Shop Name</th>
            <th>Location</th>
            <th>Price</th>
            <th>Size</th>
            <th>Owner</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($shop = $result->fetch_assoc()): ?>
                <tr>
                    <td data-label="ID"><?= $shop['shop_id'] ?></td>
                    <td data-label="Shop Name"><?= htmlspecialchars($shop['shop_name']) ?></td>
                    <td data-label="Location"><?= htmlspecialchars($shop['location']) ?></td>
                    <td data-label="Price">Ksh <?= number_format($shop['price']) ?></td>
                    <td data-label="Size"><?= htmlspecialchars($shop['size']) ?></td>
                    <td data-label="Owner"><?= htmlspecialchars($shop['full_name']) ?></td>
                    <td data-label="Status"><?= htmlspecialchars($shop['status']) ?></td>
                    <td data-label="Actions" class="actions">
                        <a class="btn-view" href="view_shops.php?shop_id=<?= $shop['shop_id'] ?>" target="_blank"><i class="fas fa-eye"></i> View</a>
                        <a class="btn-approve" href="approve_shop.php?shop_id=<?= $shop['shop_id'] ?>" onclick="return confirm('Approve this shop?');"><i class="fas fa-check-circle"></i> Approve</a>
                        <a class="btn-decline" href="decline_shop.php?shop_id=<?= $shop['shop_id'] ?>" onclick="return confirm('Decline this shop?');"><i class="fas fa-times-circle"></i> Decline</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="8" class="no-results">No pending shops found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
    function toggleDarkMode() {
        document.body.classList.toggle('dark-mode');
    }

    function exportTableToExcel(tableID, filename = 'pending_shops') {
        const table = document.getElementById(tableID);
        const html = table.outerHTML.replace(/ /g, '%20');
        const url = 'data:application/vnd.ms-excel,' + html;
        const a = document.createElement('a');
        a.href = url;
        a.download = filename + '.xls';
        a.click();
    }
</script>

</body>
</html>
