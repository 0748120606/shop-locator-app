<?php
// Get the shop_id from the URL
if (!isset($_GET['shop_id'])) {
    die("Shop ID not provided in URL!");
}

$shop_id = $_GET['shop_id'];

// Delete the shop from the database
require_once('connection.php');

$query = "DELETE FROM shops WHERE shop_id = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param('i', $shop_id);

if ($stmt->execute()) {
    // Redirect if successful
    header("Location: view_listings.php");
    exit();
} else {
    echo "Error deleting shop: " . $stmt->error;
}
?>