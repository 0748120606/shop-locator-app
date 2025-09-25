<?php
session_start();
include '../connection.php';

// Check admin session
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../admin_login.php");
    exit();
}

// Filter by date and search
$search = $_GET['search'] ?? '';
$date_from = $_GET['from'] ?? '';
$date_to = $_GET['to'] ?? '';

$where = "WHERE 1";
if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $where .= " AND s.shop_name LIKE '%$search%'";
}
if (!empty($date_from) && !empty($date_to)) {
    $where .= " AND s.created_at BETWEEN '$date_from' AND '$date_to'";
}

$query = "SELECT s.shop_name,
                 (SELECT COUNT(*) FROM bookings b WHERE b.shop_id = s.shop_id) AS total_bookings,
                 (SELECT ROUND(AVG(r.rating), 2) FROM ratings r WHERE r.shop_id = s.shop_id) AS avg_rating,
                 (SELECT COUNT(*) FROM favorites f WHERE f.shop_id = s.shop_id) AS total_favorites
          FROM shops s
          $where
          ORDER BY total_bookings DESC";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Shop Performance Report</title>
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
    table { width: 100%; border-collapse: collapse; background: #fff; }
    th, td { padding: 12px; border: 1px solid #ccc; text-align: center; }
    th { background: #28a745; color: white; }
    tr:nth-child(even) { background: #f9f9f9; }
    .back-link { display: inline-block; margin-top: 20px; color: #28a745; text-decoration: none; }
    .back-link:hover { text-decoration: underline; }
    .export-btn { background-color: #007bff; color: white; margin-left: 10px; }
  </style>
</head>
<body>
  <h1>Shop Performance Report</h1>

  <form method="get">
    <input type="text" name="search" placeholder="Search by shop name" value="<?= htmlspecialchars($search) ?>">
    <input type="date" name="from" value="<?= htmlspecialchars($date_from) ?>">
    <input type="date" name="to" value="<?= htmlspecialchars($date_to) ?>">
    <button type="submit">Filter</button>
    <button type="button" class="export-btn" onclick="exportTableToExcel('reportTable')">Export to Excel</button>
    <button type="button" class="export-btn" onclick="window.print()">Export to PDF</button>
  </form>

  <table id="reportTable">
    <thead>
      <tr>
        <th>Shop Name</th>
        <th>Total Bookings</th>
        <th>Average Rating</th>
        <th>Total Favorites</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row['shop_name']) ?></td>
            <td><?= $row['total_bookings'] !== null ? $row['total_bookings'] : 0 ?></td>
            <td><?= $row['avg_rating'] !== null ? number_format($row['avg_rating'], 2) : 'N/A' ?></td>
            <td><?= $row['total_favorites'] !== null ? $row['total_favorites'] : 0 ?></td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="4">No data available</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

  <a class="back-link" href="../admin_dashboard.php">&larr; Back to Dashboard</a>

  <script>
    function exportTableToExcel(tableID, filename = 'shop_performance_report.xls') {
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
