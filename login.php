<?php
include_once("connection.php");
session_start();

if (isset($_GET['redirect'])) {
    $_SESSION['redirect_to'] = $_GET['redirect'];
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (!empty($email) && !empty($password)) {
        $email = mysqli_real_escape_string($conn, $email);

        $sql = "SELECT user_id, full_name, user_type, password FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['user_type'] = $user['user_type'];
        
                // Remember me option
                if (isset($_POST['remember'])) {
                    setcookie("email", $email, time() + (86400 * 30), "/");
                    setcookie("password", $password, time() + (86400 * 30), "/");
                }
               // Redirect to intended destination if set
                if (isset($_SESSION['redirect_after_login'])) {
                    $redirectTo = $_SESSION['redirect_after_login'];
                    unset($_SESSION['redirect_after_login']);
                    
                    //  Only allow internal redirects
                    if (strpos($redirectTo, '/') === 0) {
                        header("Location: $redirectTo");
                        exit();
                    }
                }
                // Redirect based on user type
                if ($user['user_type'] == 'owner') {
                    header("Location: owner_dashboard.php");
                } else {
                    header("Location: user_dashboard.php");
                }
                exit();
            } else {
                $error = "Invalid email or password!";        
            }
        } else {
            $error = "No account found with this email!";
        }
    } else {
        $error = "Email and password are required!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

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
    <form method="POST" action="login.php">
        <div class="container">
            <h5>Shop locator</h5>

            <label>Email: </label>
            <input type="text" placeholder="Enter Email" name="email" required value="<?php echo isset($_COOKIE['email']) ? $_COOKIE['email'] : ''; ?>">

            <label>Password:</label>
            <div style="position: relative;">
                <input type="password" placeholder="Enter Password" name="password" id="password" required 
                    value="<?php echo isset($_COOKIE['password']) ? $_COOKIE['password'] : ''; ?>">
                <span onclick="togglePassword()" 
                    style="position:absolute; right:10px; top:50%; transform:translateY(-50%); cursor:pointer;">
                    <i class="fa-solid fa-eye" id="toggleIcon"></i>
                </span>
            </div>

            <label>
                <input type="checkbox" name="remember" <?php echo isset($_COOKIE['email']) ? "checked" : ""; ?>> Remember Me
            </label>

            <button type="submit">Login</button>

            <?php 
            if (isset($error)) {
                echo "<div style='color: red; font-weight: bold;'>$error</div>";
            }
            ?>

        <p style="text-align:center;">
            <a href="forgot_password.php" style="margin-right: 10px;">Forgot Password?</a> | 
            <a href="#" onclick="document.getElementById('roleModal').style.display='block'; return false;">Register</a>
        </p>

        </div>
    </form>
    <!-- Role Selection Modal -->
<div id="roleModal" style="display:none; position: fixed; top:0; left:0; width:100%; height:100%; background-color: rgba(0,0,0,0.5); z-index:999;">
  <div style="background:#fff; padding: 20px; border-radius: 10px; width:300px; margin:100px auto; text-align:center;">
    <h3>Select Role</h3>
    <button onclick="location.href='register.php?type=owner'" style="margin: 10px; background-color: green; color: white;">Register as Owner</button>
    <button onclick="location.href='register.php?type=client'" style="margin: 10px; background-color: green; color: white;">Register as Client</button>
    <br><br>
    <a href="#" onclick="document.getElementById('roleModal').style.display='none'" style="color:red;">Cancel</a>
  </div>
</div>
<script>
function togglePassword() {
    const pwd = document.getElementById("password");
    const icon = document.getElementById("toggleIcon");
    if (pwd.type === "password") {
        pwd.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    } else {
        pwd.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    }
}
</script>

</body>
</html>
