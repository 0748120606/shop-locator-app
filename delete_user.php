<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['admin_logged_in']) || !isset($_GET['user_id'])) {
    header("Location: manage_users.php");
    exit();
}

$user_id = intval($_GET['user_id']);
$stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    header("Location: manage_users.php?msg=deleted");
} else {
    echo "Failed to delete user.";
}
