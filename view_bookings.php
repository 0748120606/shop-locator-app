<?php
include 'connection.php';

// Handle Approve / Decline with message
if (isset($_POST['action']) && isset($_POST['booking_id'])) {
    $booking_id = (int)$_POST['booking_id'];
    $action = $_POST['action'];
    
    if ($action == 'approve') {
        $status = 'approved';
        $notification_message = "Your meeting has been approved. Please check your schedule.";
    } else if ($action == 'decline') {
        $status = 'declined';
        $notification_message = "Your meeting request was declined. Please schedule another meeting.";
    }

    // Update booking status
    $stmt = $conn->prepare("UPDATE bookings SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $booking_id);
    $stmt->execute();

    // Fetch user's phone
    $stmt = $conn->prepare("SELECT users.phone_number FROM bookings 
        INNER JOIN users ON bookings.user_id = users.user_id 
        WHERE bookings.id = ?");
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $userPhone = $result->fetch_assoc()['phone_number'];

    // Insert notification
    $stmt = $conn->prepare("INSERT INTO notifications (user_phone, message) VALUES (?, ?)");
    $stmt->bind_param("ss", $userPhone, $notification_message);
    $stmt->execute();

    echo "<script>alert('Booking $status and notification sent.'); window.location.href='view_bookings.php';</script>";
}

// Filters
$search = $_GET['search'] ?? '';
$filter = $_GET['status'] ?? '';

$query = "SELECT bookings.*, users.full_name, users.phone_number FROM bookings 
          INNER JOIN users ON bookings.user_id = users.user_id 
          WHERE (users.full_name LIKE ? OR users.phone_number LIKE ?)";
$params = ["%$search%", "%$search%"];

if ($filter) {
    $query .= " AND bookings.status = ?";
    $params[] = $filter;
}

$query .= " ORDER BY bookings.created_at DESC";

$stmt = $conn->prepare($query);
$types = str_repeat("s", count($params));
$stmt->bind_param($types, ...$params);
$stmt->execute();
$bookings = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - Bookings</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
        font-family: 'Segoe UI', sans-serif;
        background: #f3f4f6;
        margin: 0;
        padding: 0;
        display: flex;
    }

    .sidebar {
        width: 220px;
        background: #1f2937;
        color: white;
        height: 100vh;
        padding: 20px;
        box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        position: fixed;
    }

    .sidebar h2 {
        color: #60a5fa;
        margin-bottom: 20px;
    }

    .sidebar a {
        color: white;
        text-decoration: none;
        display: block;
        margin: 10px 0;
        padding: 10px;
        border-radius: 5px;
    }

    .sidebar a:hover {
        background: #374151;
    }

    .container {
        margin-left: 240px;
        padding: 40px;
        flex-grow: 1;
    }

    h2 {
        color: #111827;
    }

    .filters {
        margin-bottom: 20px;
    }

    .filters input, .filters select {
        padding: 8px;
        margin-right: 10px;
        border: 1px solid #d1d5db;
        border-radius: 5px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        border-radius: 8px;
        overflow: hidden;
    }

    table th, table td {
        padding: 12px;
        border-bottom: 1px solid #e5e7eb;
        text-align: center;
    }

    table th {
        background: #e0f2fe;
        color: #0369a1;
    }

    tr:hover {
        background: #f9fafb;
    }

    button {
        padding: 6px 12px;
        margin: 2px;
        border: none;
        border-radius: 5px;
        font-weight: bold;
        cursor: pointer;
    }

    .approve-btn { background: #4ade80; color: white; }
    .decline-btn { background: #f87171; color: white; }
  </style>
</head>
<body>

<div class="sidebar">
  <h2>Admin Panel</h2>
  <a href="admin_dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
  <a href="manage_shops.php"><i class="fas fa-store"></i> Manage Shops</a>
  <a href="manage_users.php"><i class="fas fa-users"></i> Manage Users</a>
  <a href="pending_shops.php"><i class="fas fa-hourglass-half"></i> Pending Approvals</a>
  <a href="view_bookings.php"><i class="fas fa-calendar-check"></i> Bookings</a>
  <a href="view_messages.php"><i class="fas fa-envelope"></i> Messages</a>
  <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<div class="container">
  <h2>Bookings</h2>

  <form method="get" class="filters">
    <input type="text" name="search" placeholder="Search by name or phone" value="<?= htmlspecialchars($search) ?>">
    <select name="status">
      <option value="">All Status</option>
      <option value="pending" <?= $filter === 'pending' ? 'selected' : '' ?>>Pending</option>
      <option value="approved" <?= $filter === 'approved' ? 'selected' : '' ?>>Approved</option>
      <option value="declined" <?= $filter === 'declined' ? 'selected' : '' ?>>Declined</option>
    </select>
    <button type="submit">Filter</button>
  </form>

  <?php if ($bookings && $bookings->num_rows > 0): ?>
    <table>
      <thead>
        <tr>
          <th>Name</th>
          <th>Phone</th>
          <th>Date</th>
          <th>Time</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php while($row = $bookings->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['full_name']) ?></td>
          <td><?= htmlspecialchars($row['phone_number']) ?></td>
          <td><?= htmlspecialchars($row['meeting_date']) ?></td>
          <td><?= htmlspecialchars($row['meeting_time']) ?></td>
          <td><?= ucfirst($row['status']) ?></td>
          <td>
            <?php if ($row['status'] === 'pending'): ?>
              <form method="post" style="display:inline;">
                <input type="hidden" name="booking_id" value="<?= $row['id'] ?>">
                <button type="submit" name="action" value="approve" class="approve-btn">Approve</button>
                <button type="submit" name="action" value="decline" class="decline-btn">Decline</button>
              </form>
            <?php else: ?>
              <?= ucfirst($row['status']) ?>
            <?php endif; ?>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p>No bookings found.</p>
  <?php endif; ?>
</div>

</body>
</html>
