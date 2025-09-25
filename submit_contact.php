<?php
session_start();
include 'connection.php';

// Get and sanitize form input
$name = htmlspecialchars(trim($_POST['name']));
$email = htmlspecialchars(trim($_POST['email']));
$phone_number = htmlspecialchars(trim($_POST['phone_number']));
$message = htmlspecialchars(trim($_POST['message']));
$consent = isset($_POST['consent']) ? 1 : 0;

// Basic validation
if (empty($name) || empty($email) || empty($phone_number) || !$consent) {
    $_SESSION['contact_error'] = "❌ Please fill in all required fields.";
    header("Location: index.php#special-section");
    exit();
}

// Insert into database
$sql = "INSERT INTO messages (name, email, phone_number, message, consent, submitted_at)
        VALUES (?, ?, ?, ?, ?, NOW())";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    $_SESSION['contact_error'] = "❌ Prepare failed: " . $conn->error;
    header("Location: index.php#special-section");
    exit();
}

$stmt->bind_param("ssssi", $name, $email, $phone_number, $message, $consent);

if ($stmt->execute()) {
    $_SESSION['contact_success'] = "✅ Thank you! Your message has been sent.";
} else {
    $_SESSION['contact_error'] = "❌ Something went wrong. Please try again.";
}

$stmt->close();
$conn->close();
header("Location: index.php#special-section");
exit();
