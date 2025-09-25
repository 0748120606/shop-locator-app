<?php
$conn = new mysqli("localhost", "root", "", "shop_locator");

$sql = "SELECT shop_id, shop_name, location, latitude, longitude 
        FROM shops 
        WHERE latitude IS NOT NULL AND longitude IS NOT NULL";

$result = $conn->query($sql);

$shops = [];
while ($row = $result->fetch_assoc()) {
    $shops[] = $row;
}

header('Content-Type: application/json');
echo json_encode($shops);
?>
