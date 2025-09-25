<?php
session_start();
include 'connection.php';

// ✅ Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Fetch summary data
$totalShops = $conn->query("SELECT COUNT(*) as total FROM shops")->fetch_assoc()['total'];
$totalUsers = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];
$totalFavorites = $conn->query("SELECT COUNT(*) as total FROM favorites")->fetch_assoc()['total'];
$totalRatings = $conn->query("SELECT COUNT(*) as total FROM ratings")->fetch_assoc()['total'];
$totalBookings = $conn->query("SELECT COUNT(*) as total FROM bookings")->fetch_assoc()['total'];
$totalMessages = $conn->query("SELECT COUNT(*) as total FROM messages")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: #f4f7f9;
    }

    .sidebar {
      position: fixed;
      display: flex;
      flex-direction: column;
      width: 220px;
      background: #1f2937;
      color: white;
      height: 100vh;
      padding: 20px;
      box-shadow: 2px 0 5px rgba(0,0,0,0.1);
    }

    .sidebar h2 {
      color: #60a5fa;
      text-align: center;
      margin-bottom: 30px;
    }

    .sidebar a {
      color: white;
      transition: background 0.3s;
      color: white;
      text-decoration: none;
      display: block;
      margin: 10px 0;
      padding: 10px;
      border-radius: 5px;
    }

    .sidebar a:hover {
      background: #28a745;
    }

    .content {
      margin-left: 250px;
      padding: 30px;
    }

    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .header h1 {
      margin: 0;
      font-size: 28px;
    }

    .cards {
      margin-top: 40px;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 30px;
    }

    .card {
      background: white;
      padding: 30px 20px;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      text-align: center;
      transition: transform 0.3s, box-shadow 0.3s;
    }

    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }

    .card i {
      font-size: 36px;
      color: #60a5fa;
      margin-bottom: 10px;
    }

    .card h2 {
      margin: 10px 0;
      font-size: 26px;
    }

    .card p {
      color: #777;
      font-size: 16px;
    }

    .action-btn {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      padding: 12px 18px;
      background: #28a745;
      color: white;
      font-weight: 600;
      border-radius: 8px;
      text-decoration: none;
      box-shadow: 0 3px 6px rgba(0,0,0,0.1);
      transition: background 0.3s;
      margin-right: 10px;
      margin-top: 10px;
    }
    .action-btn:hover {
      background: #218838;
    }

    .activity-feed {
      list-style: none;
      padding: 0;
      margin-top: 20px;
    }
    .activity-feed li {
      background: white;
      border-left: 5px solid #28a745;
      padding: 15px;
      margin-bottom: 10px;
      border-radius: 6px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    }
    .activity-feed li i {
      color: #28a745;
      margin-right: 10px;
    }
    .activity-feed li small {
      display: block;
      color: #888;
      margin-top: 5px;
    }

    @media (max-width: 768px) {
      .sidebar {
        width: 100%;
        height: auto;
        position: relative;
        flex-direction: row;
        flex-wrap: wrap;
      }
      .sidebar a {
        flex: 1 1 100px;
        text-align: center;
      }
      .content {
        margin-left: 0;
        padding: 20px;
      }
    }
  </style>
</head>
<body>

<div class="sidebar">
  <h2>Admin Panel</h2>
  <a href="admin_dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
  <a href="manage_shops.php"><i class="fas fa-store"></i> Manage Shops</a>
  <a href="manage_users.php"><i class="fas fa-users"></i> Manage Users</a>
  <a href="pending_shops.php"><i class="fas fa-hourglass-half"></i> Pending Approvals</a>
  <a href="send_notification.php"><i class= "fas fa-paper-plane"></i> Send Notifications</a>
  <a href="view_bookings.php"><i class="fas fa-calendar-check"></i> Bookings</a>
  <a href="view_messages.php"><i class="fas fa-envelope"></i> Messages</a>
  <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<div class="content">
  <div class="header">
    <h1>Welcome, <?= htmlspecialchars($_SESSION['admin_username']) ?> 👋</h1>
  </div>

  <div class="cards">
    <div class="card">
      <i class="fas fa-store"></i>
      <h2><?= $totalShops ?></h2>
      <p>Total Shops</p>
    </div>
    <div class="card">
      <i class="fas fa-users"></i>
      <h2><?= $totalUsers ?></h2>
      <p>Total Users</p>
    </div>
    <div class="card">
      <i class="fas fa-heart"></i>
      <h2><?= $totalFavorites ?></h2>
      <p>Total Favorites</p>
    </div>
    <div class="card">
      <i class="fas fa-star"></i>
      <h2><?= $totalRatings ?></h2>
      <p>Total Ratings</p>
    </div>
    <div class="card">
      <i class="fas fa-calendar-check"></i>
      <h2><?= $totalBookings ?></h2>
      <p>Total Bookings</p>
    </div>
    <div class="card">
      <i class="fas fa-envelope"></i>
      <h2><?= $totalMessages ?></h2>
      <p>Total Messages</p>
    </div>
  </div>

  <!-- Quick Action Section -->
  <div style="margin-top: 40px;">
    <h2>Quick Action</h2>
    <a href="add_user.php" class="action-btn"><i class="fas fa-user-plus"></i> Add New User</a>
    <a href="report/shop_performance_report.php" class="action-btn"><i class="fas fa-chart-line"></i> Shop Performance Report</a>
    <a href="report/user_activity_report.php" class="action-btn"><i class="fas fa-user-clock"></i> User Activity Report</a>
    <a href="report/reviews_report.php" class="action-btn"><i class="fas fa-comments"></i> Ratings & Reviews Report</a>
    <a href="report/booking_report.php" class="action-btn"><i class="fas fa-book"></i> Bookings Report</a>
  </div>

  <!-- Recent Activities -->
  <div style="margin-top: 50px;">
    <h2>Recent Activities</h2>
    <ul class="activity-feed">
      <?php
      $recentLogs = $conn->query("SELECT action, details, created_at FROM admin_logs ORDER BY created_at DESC LIMIT 5");
      while ($log = $recentLogs->fetch_assoc()): ?>
        <li>
          <i class="fas fa-history"></i>
          <strong><?= htmlspecialchars($log['action']) ?></strong> —
          <?= htmlspecialchars($log['details']) ?> <br>
          <small><?= date("M d, Y h:i A", strtotime($log['created_at'])) ?></small>
        </li>
      <?php endwhile; ?>
    </ul>
  </div>

</div>

</body>
</html>
