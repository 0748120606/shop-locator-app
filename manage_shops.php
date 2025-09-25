<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

// Filters and search
$search = $_GET['search'] ?? '';
$statusFilter = $_GET['status'] ?? '';
$anomalyFilter = $_GET['anomaly'] ?? '';
$entriesPerPage = isset($_GET['entries']) ? (int)$_GET['entries'] : 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $entriesPerPage;

// Build WHERE clause
$where = "WHERE 1";
if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $where .= " AND (s.shop_name LIKE '%$search%' OR s.location LIKE '%$search%')";
}
if (!empty($statusFilter)) {
    if ($statusFilter === 'Declined') {
        $where .= " AND s.approved = 0";
    } else {
        $where .= " AND s.status = '$statusFilter' AND s.approved = 1";
    }
} else {
    $where .= " AND s.approved IS NOT NULL";
}
if ($anomalyFilter === 'flagged') {
    $where .= " AND s.anomaly_flag = 1";
} elseif ($anomalyFilter === 'not_flagged') {
    $where .= " AND (s.anomaly_flag = 0 OR s.anomaly_flag IS NULL)";
}

// Count total results
$countSql = "SELECT COUNT(*) as total FROM shops s JOIN users u ON s.user_id = u.user_id $where";
$countResult = $conn->query($countSql);
$totalRows = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $entriesPerPage);

// Fetch results
$sql = "SELECT s.shop_id, s.shop_name, s.location, s.price, s.size, s.status, s.anomaly_flag,
               u.full_name AS owner_name
        FROM shops s
        JOIN users u ON s.user_id = u.user_id
        $where
        ORDER BY s.shop_id DESC
        LIMIT $entriesPerPage OFFSET $offset";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Shops</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --bg-light: #f2f4f7;
            --bg-dark: #1e1e1e;
            --text-dark: #333;
            --text-light: #f2f2f2;
            --primary: #60a5fa;
            --danger: #dc3545;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: var(--bg-light);
            color: var(--text-dark);
            transition: background 0.3s, color 0.3s;
        }
        .dark-mode { background: var(--bg-dark); color: var(--text-light); }
        .navbar {
            background: #1f2937;
            color: white;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
            font-weight: bold;
        }
        .container { padding: 20px; }
        h2 { margin-bottom: 20px; }
        .filter-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 15px;
        }
        input, select {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        .controls {
            margin-bottom: 15px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        .dark-toggle { background-color: #444; color: white; }
        thead { background: var(--primary); color: white; }
        th, td {
            padding: 14px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        td a {
            color: #007BFF;
            text-decoration: none;
            font-weight: bold;
        }
        .btn-delete { color: var(--danger); }
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
        .flagged {
            color: red;
            font-weight: bold;
        }
        .pagination {
            margin-top: 20px;
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }
        .pagination a {
            padding: 8px 12px;
            background: #eee;
            border-radius: 4px;
            text-decoration: none;
            color: #333;
        }
        .pagination .current {
            background: var(--primary);
            color: white;
        }
        .export-excel {
            background-color: #28a745;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .export-pdf {
            background-color: #f44336;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        @media screen and (max-width: 600px) {
            .filter-bar, .controls { flex-direction: column; }
            table, thead, tbody, th, td, tr { display: block; }
            th { display: none; }
            td {
                position: relative;
                padding-left: 50%;
            }
            td::before {
                position: absolute;
                top: 12px;
                left: 10px;
                width: 45%;
                font-weight: bold;
            }
            td:nth-child(1)::before { content: "ID"; }
            td:nth-child(2)::before { content: "Shop Name"; }
            td:nth-child(3)::before { content: "Location"; }
            td:nth-child(4)::before { content: "Price"; }
            td:nth-child(5)::before { content: "Size"; }
            td:nth-child(6)::before { content: "Owner"; }
            td:nth-child(7)::before { content: "Status"; }
            td:nth-child(8)::before { content: "Actions"; }
        }
    </style>
</head>
<body>

<div class="navbar">
    <h1>Manage Shops</h1>
    <div>
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="manage_users.php">Manage Users</a>
        <a href="logout.php">Logout</a>
        <button class="dark-toggle" onclick="toggleDarkMode()">🌓</button>
    </div>
</div>

<div class="container">
    <h2>All Listed Shops</h2>
    <form method="get" class="filter-bar">
        <input type="text" name="search" placeholder="Search by name or location" value="<?= htmlspecialchars($search) ?>">
        <select name="status">
            <option value="">All Statuses</option>
            <option value="Available" <?= $statusFilter === 'Available' ? 'selected' : '' ?>>Available</option>
            <option value="Occupied" <?= $statusFilter === 'Occupied' ? 'selected' : '' ?>>Occupied</option>
            <option value="Declined" <?= $statusFilter === 'Declined' ? 'selected' : '' ?>>Declined</option>
        </select>
        <select name="anomaly">
            <option value="">All Shops</option>
            <option value="flagged" <?= $anomalyFilter === 'flagged' ? 'selected' : '' ?>>Flagged (Anomalous)</option>
            <option value="not_flagged" <?= $anomalyFilter === 'not_flagged' ? 'selected' : '' ?>>Not Flagged</option>
        </select>
        <select name="entries">
            <?php foreach ([5, 10, 20, 50] as $num): ?>
                <option value="<?= $num ?>" <?= $entriesPerPage == $num ? 'selected' : '' ?>><?= $num ?> per page</option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Apply</button>
    </form>

    <div class="controls">
        <button class="export-excel" onclick="exportTableToExcel('shopTable')">
            <i class="fas fa-file-excel"></i> Export to Excel
        </button>
        <button class="export-pdf" onclick="window.print()">
            <i class="fas fa-file-pdf"></i> Export to PDF
        </button>
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
        <?php if ($result->num_rows > 0): ?>
            <?php while ($shop = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $shop['shop_id'] ?></td>
                    <td>
                        <?= htmlspecialchars($shop['shop_name']) ?>
                        <?php if ($shop['anomaly_flag'] == 1): ?>
                            <span class="flagged">(Flagged)</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($shop['location']) ?></td>
                    <td><?= number_format($shop['price']) ?></td>
                    <td><?= htmlspecialchars($shop['size']) ?></td>
                    <td><?= htmlspecialchars($shop['owner_name']) ?></td>
                    <td>
                        <span class="status <?= $shop['status'] === 'available' ? 'available' : 'unavailable' ?>">
                            <?= ucfirst($shop['status']) ?>
                        </span>
                    </td>
                    <td>
                        <a href="edit_shop.php?shop_id=<?= $shop['shop_id'] ?>"><i class="fas fa-edit"></i> Edit</a> |
                        <a href="#" class="btn-delete" data-shop-id="<?= $shop['shop_id'] ?>">
                            <i class="fas fa-trash-alt"></i> Delete
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="8">No shops found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a class="<?= $i == $page ? 'current' : '' ?>" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>
</div>

<script>
function exportTableToExcel(tableID, filename = '') {
    const dataType = 'application/vnd.ms-excel';
    const table = document.getElementById(tableID);
    const html = table.outerHTML.replace(/ /g, '%20');
    filename = filename ? filename + '.xls' : 'shops_data.xls';
    const link = document.createElement('a');
    link.href = 'data:' + dataType + ', ' + html;
    link.download = filename;
    link.click();
}
function toggleDarkMode() {
    document.body.classList.toggle('dark-mode');
}
document.addEventListener("DOMContentLoaded", function () {
    const deleteButtons = document.querySelectorAll(".btn-delete");
    deleteButtons.forEach(button => {
        button.addEventListener("click", function (e) {
            e.preventDefault();
            const shopId = this.getAttribute("data-shop-id");
            if (!confirm("Delete this shop?")) return;
            fetch('delete_shops.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'shop_id=' + shopId
            }).then(() => location.reload());
        });
    });
});
</script>
</body>
</html>
