<?php
include 'connection.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $stmt = $conn->prepare("SELECT user_id FROM users WHERE verification_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($user_id);
        $stmt->fetch();

        $update = $conn->prepare("UPDATE users SET email_verified = 1, verification_token = NULL WHERE user_id = ?");
        $update->bind_param("i", $user_id);
        $update->execute();

        echo "✅ Email verified successfully.";
    } else {
        echo "❌ Invalid or expired verification link.";
    }
} else {
    echo "❌ No token provided.";
}
?>
