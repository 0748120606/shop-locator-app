<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

if (isset($_GET['shop_id'])) {
    $shop_id = intval($_GET['shop_id']);

    // Decline the shop
    $sql = "UPDATE shops SET approved = 0 WHERE shop_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $shop_id);

    if ($stmt->execute()) {
        // Get shop details for logging
        $shop_stmt = $conn->prepare("SELECT shop_name, location FROM shops WHERE shop_id = ?");
        $shop_stmt->bind_param("i", $shop_id);
        $shop_stmt->execute();
        $shop_result = $shop_stmt->get_result();

        if ($shop = $shop_result->fetch_assoc()) {
            $shop_name = $shop['shop_name'];
            $location = $shop['location'];

            // Log the admin action
            $adminId = $_SESSION['admin_id']; // Assuming admin_id is stored in session
            $action = "Declined Shop";
            $details = "Shop ID: $shop_id | Shop Name: $shop_name | Location: $location";

            $log_stmt = $conn->prepare("INSERT INTO admin_logs (admin_id, action, details) VALUES (?, ?, ?)");
            $log_stmt->bind_param("iss", $adminId, $action, $details);
            $log_stmt->execute();
            $log_stmt->close();
        }

        $shop_stmt->close();

        header("Location: pending_shops.php?declined=1");
    } else {
        echo "Error declining shop.";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "No shop ID provided.";
}
?>
