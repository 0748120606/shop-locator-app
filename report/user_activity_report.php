<?php
session_start();
include '../connection.php';

// Check admin session
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../admin_login.php");
    exit();
}

$search = $_GET['search'] ?? '';
$date_from = $_GET['from'] ?? '';
$date_to = $_GET['to'] ?? '';

$where = "WHERE 1";
if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $where .= " AND (u.full_name LIKE '%$search%' OR u.email LIKE '%$search%')";
}
if (!empty($date_from) && !empty($date_to)) {
    $where .= " AND DATE(u.created_at) BETWEEN '$date_from' AND '$date_to'";
}

$query = "SELECT u.user_id, u.full_name, u.email, u.user_type, 
            (SELECT COUNT(*) FROM favorites f WHERE f.user_id = u.user_id) AS total_favorites,
            (SELECT COUNT(*) FROM ratings r WHERE r.user_id = u.user_id) AS total_ratings,
            (SELECT COUNT(*) FROM bookings b WHERE b.user_id = u.user_id) AS total_bookings
          FROM users u
          $where
          ORDER BY total_bookings DESC";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Activity Report</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
    h1 { color: #333; }
    form { margin-bottom: 20px; }
    input[type="text"], input[type="date"] {
      padding: 8px;
      margin-right: 10px;
    }
    button {
      padding: 8px 16px;
      background: #28a745;
      color: white;
      border: none;
      cursor: pointer;
    }
    .export-btn { background-color: #007bff; color: white; margin-left: 10px; }
    table { width: 100%; border-collapse: collapse; background: #fff; }
    th, td { padding: 12px; border: 1px solid #ccc; text-align: center; }
    th { background: #28a745; color: white; }
    tr:nth-child(even) { background: #f9f9f9; }
    .back-link { display: inline-block; margin-top: 20px; color: #28a745; text-decoration: none; }
    .back-link:hover { text-decoration: underline; }
  </style>
</head>
<body>
  <h1>User Activity Report</h1>

  <form method="get">
    <input type="text" name="search" placeholder="Search by name or email" value="<?= htmlspecialchars($search) ?>">
    <input type="date" name="from" value="<?= htmlspecialchars($date_from) ?>">
    <input type="date" name="to" value="<?= htmlspecialchars($date_to) ?>">
    <button type="submit">Filter</button>
    <button type="button" class="export-btn" onclick="exportTableToExcel('reportTable')">Export to Excel</button>
    <button type="button" class="export-btn" onclick="window.print()">Export to PDF</button>
  </form>

  <table id="reportTable">
    <thead>
      <tr>
        <th>User ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>User Type</th>
        <th>Favorites</th>
        <th>Ratings</th>
        <th>Bookings</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= $row['user_id'] ?></td>
            <td><?= htmlspecialchars($row['full_name']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= ucfirst($row['user_type']) ?></td>
            <td><?= $row['total_favorites'] ?></td>
            <td><?= $row['total_ratings'] ?></td>
            <td><?= $row['total_bookings'] ?></td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="7">No data available</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

  <a class="back-link" href="../admin_dashboard.php">&larr; Back to Dashboard</a>

  <script>
    function exportTableToExcel(tableID, filename = 'user_activity_report.xls') {
      const table = document.getElementById(tableID);
      const html = table.outerHTML.replace(/ /g, '%20');
      const link = document.createElement('a');
      link.href = 'data:application/vnd.ms-excel,' + html;
      link.download = filename;
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    }
  </script>
</body>
</html>
