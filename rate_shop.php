<?php
include 'connection.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $shop_id = intval($_POST['shop_id']);
    $user_id = intval($_POST['user_id']);
    $rating = intval($_POST['rating']);
    $review = trim($_POST['review']);

    if ($rating < 1 || $rating > 5) {
        die("Invalid rating value.");
    }

    $stmt = $conn->prepare("INSERT INTO ratings (shop_id, user_id, rating, review, date_posted) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("iiis", $shop_id, $user_id, $rating, $review);

    if ($stmt->execute()) {
        header("Location: view_shops.php?shop_id=" . $shop_id . "&rated=1");
        exit;
    } else {
        echo "Failed to submit rating.";
    }
}
?>
