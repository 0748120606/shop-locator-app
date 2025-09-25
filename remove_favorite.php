<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['shop_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Unauthorized or invalid request']);
    exit();
}

$user_id = $_SESSION['user_id'];
$shop_id = intval($_POST['shop_id']);

$stmt = $conn->prepare("DELETE FROM favorites WHERE user_id = ? AND shop_id = ?");
$stmt->bind_param("ii", $user_id, $shop_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to remove favorite']);
}

$stmt->close();
$conn->close();
