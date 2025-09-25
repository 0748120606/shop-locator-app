<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['admin_logged_in']) || !isset($_GET['user_id'])) {
    header("Location: manage_users.php");
    exit();
}

$user_id = intval($_GET['user_id']);
$default_password = password_hash('default123', PASSWORD_DEFAULT);

$stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
$stmt->bind_param("si", $default_password, $user_id);

if ($stmt->execute()) {
    header("Location: manage_users.php?msg=password_reset");
} else {
    echo "Failed to reset password.";
}
