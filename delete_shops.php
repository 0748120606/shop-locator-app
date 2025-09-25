<?php
session_start();
require_once('connection.php');

header('Content-Type: application/json');

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || !isset($_SESSION['admin_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized access."]);
    exit();
}

// Validate shop_id
$shop_id = filter_input(INPUT_POST, 'shop_id', FILTER_VALIDATE_INT);
if (!$shop_id) {
    echo json_encode(["status" => "error", "message" => "Invalid Shop ID."]);
    exit();
}

try {
    $conn->begin_transaction();

    // Delete from shops
    $delete_stmt = $conn->prepare("DELETE FROM shops WHERE shop_id = ?");
    $delete_stmt->bind_param("i", $shop_id);
    $delete_stmt->execute();
    $delete_stmt->close();

    // Log admin action
    $admin_id = $_SESSION['admin_id'];
    $action = "Deleted Shop";
    $details = "Shop ID: $shop_id deleted.";

    $log_stmt = $conn->prepare("INSERT INTO admin_logs (admin_id, action, details) VALUES (?, ?, ?)");
    $log_stmt->bind_param("iss", $admin_id, $action, $details);
    if (!$log_stmt->execute()) {
        error_log("Failed to insert admin log: " . $log_stmt->error);
    }
    $log_stmt->close();

    $conn->commit();

    echo json_encode(["status" => "success", "message" => "Shop deleted successfully."]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(["status" => "error", "message" => "Failed to delete shop."]);
}

$conn->close();
