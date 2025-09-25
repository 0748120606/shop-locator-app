<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone_number = trim($_POST['phone_number']);
    $errors = [];

    if (empty($full_name) || empty($email) || empty($phone_number)) {
        $errors[] = "All fields are required.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (empty($errors)) {
        $sql = "UPDATE users SET full_name = ?, email = ?, phone_number = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $full_name, $email, $phone_number, $user_id);

        if ($stmt->execute()) {
            $success = "Account updated successfully.";
        } else {
            $errors[] = "Update failed. Please try again.";
        }

        $stmt->close();
    }
}

// Fetch user data
$sql = "SELECT full_name, email, phone_number FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Account</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            padding: 20px;
            background: #f7f7f7;
        }

        .container {
            max-width: 500px;
            margin: auto;
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }

        h2 {
            margin-bottom: 20px;
            text-align: center;
            color: #333;
        }

        input[type="text"], input[type="email"] {
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #7c3aed;
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background: #5b23d4;
        }

        .error {
            color: red;
            margin-bottom: 15px;
        }

        .success {
            color: green;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Update Account</h2>

    <?php
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<div class='error'>• $error</div>";
        }
    }

    if (isset($success)) {
        echo "<div class='success'>$success</div>";
    }
    ?>

    <form method="POST" action="update_account.php">
        <label>Full Name:</label>
        <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" required>

        <label>Email:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

        <label>Phone Number:</label>
        <input type="text" name="phone_number" value="<?= htmlspecialchars($user['phone_number']) ?>" required>

        <button type="submit">Update Account</button>
    </form>
</div>
</body>
</html>
