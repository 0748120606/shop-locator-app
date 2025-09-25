<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['admin_logged_in']) || !isset($_GET['user_id'])) {
    header("Location: manage_users.php");
    exit();
}

$user_id = intval($_GET['user_id']);
$conn->query("ALTER TABLE users ADD COLUMN IF NOT EXISTS is_active TINYINT(1) DEFAULT 1");

$current = $conn->query("SELECT is_active FROM users WHERE user_id = $user_id")->fetch_assoc()['is_active'];
$new_status = $current ? 0 : 1;

$stmt = $conn->prepare("UPDATE users SET is_active = ? WHERE user_id = ?");
$stmt->bind_param("ii", $new_status, $user_id);

if ($stmt->execute()) {
    header("Location: manage_users.php?msg=status_updated");
} else {
    echo "Failed to update user status.";
}
