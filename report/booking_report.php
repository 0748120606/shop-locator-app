<?php
session_start();
include 'connection.php';

// Ensure admin is logged in
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
  $where .= " AND (s.shop_name LIKE '%$search%' OR u.full_name LIKE '%$search%')";
}
if (!empty($date_from) && !empty($date_to)) {
  $where .= " AND b.created_at BETWEEN '$date_from' AND '$date_to'";
}

$query = "SELECT b.booking_id, b.meeting_date, b.meeting_time, b.status, b.created_at,
                 s.shop_name,
                 u.full_name AS user_name
          FROM bookings b
          JOIN users u ON b.user_id = u.user_id
          LEFT JOIN shops s ON b.shop_id = s.shop_id
          $where
          ORDER BY b.created_at DESC";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Bookings Report</title>
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
  <h1>Bookings Report</h1>

  <form method="get">
    <input type="text" name="search" placeholder="Search by shop or user name" value="<?= htmlspecialchars($search) ?>">
    <input type="date" name="from" value="<?= htmlspecialchars($date_from) ?>">
    <input type="date" name="to" value="<?= htmlspecialchars($date_to) ?>">
    <button type="submit">Filter</button>
    <button type="button" class="export-btn" onclick="exportTableToExcel('reportTable')">Export to Excel</button>
    <button type="button" class="export-btn" onclick="window.print()">Export to PDF</button>
  </form>

  <table id="reportTable">
    <thead>
      <tr>
        <th>Booking ID</th>
        <th>User</th>
        <th>Shop</th>
        <th>Date</th>
        <th>Time</th>
        <th>Status</th>
        <th>Created At</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= $row['booking_id'] ?></td>
            <td><?= htmlspecialchars($row['user_name']) ?></td>
            <td><?= htmlspecialchars($row['shop_name'] ?? 'N/A') ?></td>
            <td><?= htmlspecialchars($row['meeting_date']) ?></td>
            <td><?= htmlspecialchars($row['meeting_time']) ?></td>
            <td><?= ucfirst($row['status']) ?></td>
            <td><?= date('M d, Y h:i A', strtotime($row['created_at'])) ?></td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="7">No bookings found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

  <a class="back-link" href="../admin_dashboard.php">&larr; Back to Dashboard</a>

  <script>
    function exportTableToExcel(tableID, filename = 'bookings_report.xls') {
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
