<?php
session_start();
session_unset(); // Clear all session variables
session_destroy(); // Destroy the session

// Optionally clear cookies (if using remember me)
setcookie("email", "", time() - 3600, "/");
setcookie("password", "", time() - 3600, "/");

// Redirect to homepage
header("Location: index.php");
exit();
?>
