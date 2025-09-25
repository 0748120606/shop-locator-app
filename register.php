<?php
include_once("connection.php");
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Get user type from URL (owner or client)
$user_type = isset($_GET['type']) && in_array($_GET['type'], ['owner', 'client']) ? $_GET['type'] : null;

if ($_SERVER["REQUEST_METHOD"] == "POST" && $user_type) {
    if (!empty($_POST['full_name']) && !empty($_POST['phone_number']) && !empty($_POST['email']) && !empty($_POST['password'])) {
        $full_name = $_POST['full_name'];
        $phone_number = $_POST['phone_number'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        try {
            $conn->begin_transaction();

            // Insert into users table
            $stmt_users = $conn->prepare("INSERT INTO users (full_name, phone_number, user_type, email, password) VALUES (?, ?, ?, ?, ?)");
            $stmt_users->bind_param("sssss", $full_name, $phone_number, $user_type, $email, $password);
            $stmt_users->execute();
            $user_id = $stmt_users->insert_id;

            // Insert into owners or clients table
            if ($user_type == 'owner') {
                $stmt_extra = $conn->prepare("INSERT INTO owners (user_id) VALUES (?)");
            } else {
                $stmt_extra = $conn->prepare("INSERT INTO clients (user_id) VALUES (?)");
            }
            $stmt_extra->bind_param("i", $user_id);
            $stmt_extra->execute();

            // Commit transaction
            $conn->commit();

            // Success message and redirect to login
            echo "<script>
                alert('Registration successful! Redirecting to login...');
                window.location.href = 'login.php';
            </script>";
            exit;

        } catch (Exception $e) {
            $conn->rollback();
            echo "<script>alert('Error: " . addslashes($e->getMessage()) . "');</script>";
        }
    } else {
        echo "<script>alert('Please fill in all required fields.');</script>";
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Signup Page</title>
    <style>   
    body { 
        font-family: Calibri, Helvetica, sans-serif;  
        background-image: url('images/img10.jpg');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 0;
    }

    button {   
        background-color: green;   
        width: 100%;  
        color: azure;   
        padding: 15px;   
        margin: 10px 0px;   
        border: none;   
        cursor: pointer;   
        border-radius: 10px;
    }   

    form {   
        width: 300px;  
        height: auto;
        margin-top: 50px;
    }   

    @media only screen and (max-width: 767px) {
        form {
            width: 90%;
            padding: 10px;
        }
    }

    input[type=text], input[type=password] {   
        width: 100%;   
        margin: 8px 0;  
        padding: 12px 20px;   
        display: inline-block;   
        border: 2px solid #77DD77;   
        box-sizing: border-box;  
        border-radius: 10px; 
    }  

    button:hover {   
        opacity: 0.7;   
    }   

    .container {   
        padding: 25px;   
        background-color: rgba(255, 255, 255, 0.7); 
        max-width: 500px;  
        border-radius: 20px;
        width: 90%; 
        text-align: left;
        font-weight: bold;
        font-size: 1em;
    }

    h5 {
        color: #333333;
        text-align: center;
        font-family: Arial, Helvetica, sans-serif;
        font-weight: bold;
        font-size: 1em;
    }
    </style>   
</head>
<body>
<form method="POST" action="register.php?type=<?php echo htmlspecialchars($_GET['type'] ?? ''); ?>">
        <div class="container">
            <h5>Register</h5>

            <!-- User Details -->
            <label>Full Name: </label>
            <input type="text" placeholder="Full Name" name="full_name" required>

            <!-- Owner Phone Number Details -->
            <label>Phone Number: </label>
            <input type="text" placeholder="Phone Number" name="phone_number" required>

            <label>Email: </label>
            <input type="text" placeholder="e.g abc@gmail.com" name="email" required>

            <label>Password: </label>
            <input type="password" placeholder="Your Password" name="password" required>

            <button type="submit">Create Account</button>
            <button type="button" class="cancelbtn">Cancel</button>
        </div>
    </form>
</body>
</html>
