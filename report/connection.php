<?php
// Database connection
$servername = "localhost";
$username = "root";  
$password = ""; 
$dbname = "shop_locator";

// Creating connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Checking connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}