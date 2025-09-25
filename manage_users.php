<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

// Search filter
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$where = "";
if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $where = "WHERE full_name LIKE '%$search%' OR email LIKE '%$search%' OR user_type LIKE '%$search%'";
}

// Pagination
$limit = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$total_result = $conn->query("SELECT COUNT(*) AS total FROM users $where");
$total_users = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_users / $limit);

$sql = "SELECT user_id, full_name, email, user_type, status FROM users $where ORDER BY user_id DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f6fa;
            margin: 0;
        }

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
            color: #fff;
            text-decoration: none;
            margin-left: 20px;
            font-weight: 500;
            transition: color 0.3s;
        }

        .navbar a:hover {
            color: #60a5fa;
        }

        .container {
            margin: 40px auto;
            padding:20px;
        }

        .card {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
            padding: 30px;
        }

        h2 {
            margin-top: 0;
            font-weight: 600;
            font-size: 22px;
            color: #1f2937;
        }

        form.search-form {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
        }

        input[type="text"] {
            padding: 10px;
            width: 250px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        button[type="submit"] {
            padding: 10px 18px;
            background-color: #2563eb;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        thead {
            background-color: #2563eb;
            color: white;
        }

        th, td {
            padding: 14px 16px;
            text-align: left;
            font-size: 15px;
        }

        tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }

        tbody tr:hover {
            background-color: #eef2f7;
        }

        .btn-delete {
            color: #dc2626;
            font-weight: 500;
            text-decoration: none;
        }

        .btn-delete i {
            margin-right: 6px;
        }

        .btn-delete:hover {
            text-decoration: underline;
        }

        .no-users {
            text-align: center;
            padding: 20px;
            color: #6b7280;
        }

        .pagination {
            margin-top: 25px;
            text-align: center;
        }

        .pagination a {
            padding: 8px 12px;
            margin: 0 5px;
            text-decoration: none;
            background: #e5e7eb;
            color: #1f2937;
            border-radius: 5px;
        }

        .pagination a.active {
            background: #2563eb;
            color: white;
            font-weight: bold;
        }

        .pagination a:hover {
            background: #93c5fd;
        }
    </style>
</head>
<body>

<div class="navbar">
    <h1>Manage Users</h1>
    <div>
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="manage_shops.php">Manage Shops</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="container">
    <div class="card">
        <h2>All Users</h2>

        <form class="search-form" method="get">
            <input type="text" name="search" placeholder="Search by name, email or type" value="<?= htmlspecialchars($search) ?>">
            <button type="submit">Search</button>
        </form>

        <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>User Type</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $user['user_id'] ?></td>
                    <td><?= htmlspecialchars($user['full_name']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= ucfirst($user['user_type']) ?></td>
                    <td>
                        <a href="view_user.php?user_id=<?= $user['user_id'] ?>" title="View User">
                            <i class="fas fa-eye" style="color:#2563eb; margin-right:10px;"></i>
                        </a>

                        <a href="delete_user.php?user_id=<?= $user['user_id'] ?>" class="btn-delete" title="Delete User" onclick="return confirm('Are you sure you want to delete this user?');">
                            <i class="fas fa-trash-alt"></i>
                        </a>

                        <a href="reset_password.php?user_id=<?= $user['user_id'] ?>" title="Reset Password" onclick="return confirm('Reset password for this user?');">
                            <i class="fas fa-key" style="color:#10b981; margin-left:10px;"></i>
                        </a>

                        <?php if ($user['status'] === 'active'): ?>
                            <a href="toggle_user_status.php?user_id=<?= $user['user_id'] ?>&action=deactivate" title="Suspend User" onclick="return confirm('Suspend this user?');">
                                <i class="fas fa-user-slash" style="color:#f59e0b; margin-left:10px;"></i>
                            </a>
                        <?php else: ?>
                            <a href="toggle_user_status.php?user_id=<?= $user['user_id'] ?>&action=activate" title="Activate User" onclick="return confirm('Activate this user?');">
                                <i class="fas fa-user-check" style="color:#22c55e; margin-left:10px;"></i>
                            </a>
                        <?php endif; ?>
                    </td>

                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
            <div class="no-users">No users found.</div>
        <?php endif; ?>

        <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a class="<?= $i === $page ? 'active' : '' ?>" href="?search=<?= urlencode($search) ?>&page=<?= $i ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
