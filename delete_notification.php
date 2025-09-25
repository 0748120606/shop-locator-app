<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['id'])) {
    echo 'error';
    exit;
}

$user_id = $_SESSION['user_id'];
$notification_id = intval($_POST['id']);

// Only delete if it belongs to the current user
$sql = "DELETE FROM notifications WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $notification_id, $user_id);

if ($stmt->execute()) {
    echo 'success';
} else {
    echo 'error';
}
?>
