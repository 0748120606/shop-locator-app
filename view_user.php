<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['admin_logged_in']) || !isset($_GET['user_id'])) {
    header("Location: manage_users.php");
    exit();
}

$user_id = intval($_GET['user_id']);
$stmt = $conn->prepare("SELECT user_id, full_name, email, user_type FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "User not found.";
    exit();
}

$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head><title>View User</title></head>
<body>
    <h2>User Details</h2>
    <p><strong>ID:</strong> <?= $user['user_id'] ?></p>
    <p><strong>Name:</strong> <?= htmlspecialchars($user['full_name']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
    <p><strong>User Type:</strong> <?= htmlspecialchars($user['user_type']) ?></p>
    <a href="manage_users.php">← Back</a>
</body>
</html>
