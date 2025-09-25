<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
session_start();
require 'connection.php';

if (!isset($_POST['shop_id'], $_POST['location'], $_POST['action'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
    exit;
}

$shop_id = intval($_POST['shop_id']);
$location = $_POST['location'];
$action = $_POST['action'];

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// If not logged in, store favorite using session for demo purposes
if (!$user_id) {
    echo json_encode(['status' => 'error', 'message' => 'Login required to favorite.', 'redirect' => 'login.php']);
    exit;
}

if ($action === 'add') {
    $stmt = $conn->prepare("INSERT INTO favorites (user_id, shop_id, location, created_at) VALUES (?, ?, ?, NOW()) 
        ON DUPLICATE KEY UPDATE created_at = NOW()");
    $stmt->bind_param("iis", $user_id, $shop_id, $location);
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => "Shop at $location added to favorites."]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add favorite.']);
    }
    $stmt->close();
} elseif ($action === 'remove') {
    $stmt = $conn->prepare("DELETE FROM favorites WHERE user_id = ? AND shop_id = ?");
    $stmt->bind_param("ii", $user_id, $shop_id);
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => "Shop at $location removed from favorites."]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to remove favorite.']);
    }
    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid action.']);
}
?>
