<?php
session_start();
include '../connection.php';

// Check admin session
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../admin_login.php");
    exit();
}

$query = "SELECT s.shop_name, COUNT(b.booking_id) AS total_bookings, 
                 AVG(r.rating) AS avg_rating, 
                 COUNT(f.favorite_id) AS total_favorites
          FROM shops s
          LEFT JOIN bookings b ON s.shop_id = b.shop_id
          LEFT JOIN ratings r ON s.shop_id = r.shop_id
          LEFT JOIN favorites f ON s.shop_id = f.shop_id
          GROUP BY s.shop_id
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
    table { width: 100%; border-collapse: collapse; background: #fff; }
    th, td { padding: 12px; border: 1px solid #ccc; text-align: center; }
    th { background: #28a745; color: white; }
    tr:nth-child(even) { background: #f9f9f9; }
    .back-link { display: inline-block; margin-top: 20px; color: #28a745; text-decoration: none; }
    .back-link:hover { text-decoration: underline; }
  </style>
</head>
<body>
  <h1>Shop Performance Report</h1>
  <table>
    <thead>
      <tr>
        <th>Shop Name</th>
        <th>Total Bookings</th>
        <th>Average Rating</th>
        <th>Total Favorites</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['shop_name']) ?></td>
          <td><?= $row['total_bookings'] ?></td>
          <td><?= number_format($row['avg_rating'], 2) ?></td>
          <td><?= $row['total_favorites'] ?></td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

  <a class="back-link" href="../view_bookings.php">&larr; Back to Dashboard</a>
</body>
</html>
