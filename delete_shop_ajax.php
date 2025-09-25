<?php
header('Content-Type: application/json');
require_once 'connection.php'; // Adjust if needed

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id'])) {
    echo json_encode(['success' => false, 'message' => 'No ID provided']);
    exit;
}

$shop_id = $data['id'];

// Prepare and execute delete
$stmt = $conn->prepare("DELETE FROM shops WHERE shop_id = ?");
$stmt->bind_param("i", $shop_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}

$stmt->close();
$conn->close();
?>
