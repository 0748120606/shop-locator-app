<?php
include 'connection.php';
session_start(); // Ensure session is started to access admin_id

if (isset($_GET['shop_id'])) {
    $shop_id = intval($_GET['shop_id']);

    // Approve the shop
    $stmt = $conn->prepare("UPDATE shops SET approved = 1 WHERE shop_id = ?");
    $stmt->bind_param("i", $shop_id);

    if ($stmt->execute()) {
        // Get user_id, shop_name, and location
        $shop_stmt = $conn->prepare("SELECT user_id, shop_name, location FROM shops WHERE shop_id = ?");
        $shop_stmt->bind_param("i", $shop_id);
        $shop_stmt->execute();
        $shop_result = $shop_stmt->get_result();

        if ($shop = $shop_result->fetch_assoc()) {
            $user_id = $shop['user_id'];
            $shop_name = $shop['shop_name'];
            $location = $shop['location'];

            // Prepare notification message
            $message = "🎉 Congratulations, your shop \"$shop_name\" in $location has been approved!";

            // Insert notification
            $notif_stmt = $conn->prepare("INSERT INTO notifications (user_id, message, created_at, is_read) VALUES (?, ?, NOW(), 0)");
            $notif_stmt->bind_param("is", $user_id, $message);
            $notif_stmt->execute();
            $notif_stmt->close();

            // Log the admin action
            $adminId = $_SESSION['admin_id']; // Ensure admin_id is stored in session
            $action = "Approved Shop";
            $details = "Shop ID: $shop_id | Shop Name: $shop_name | Location: $location";

            $log_stmt = $conn->prepare("INSERT INTO admin_logs (admin_id, action, details) VALUES (?, ?, ?)");
            $log_stmt->bind_param("iss", $adminId, $action, $details);
            $log_stmt->execute();
            $log_stmt->close();
        }

        $shop_stmt->close();

        // Redirect after success
        header("Location: pending_shops.php?success=1");
        exit();
    } else {
        // Redirect with error if approval failed
        header("Location: pending_shops.php?error=1");
        exit();
    }

    $stmt->close();
}
?>
