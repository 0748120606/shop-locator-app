<?php
$lat = $_GET['lat'];
$lon = $_GET['lon'];
$radius_km = 5;

$conn = new mysqli("localhost", "root", "", "shop_locator");

$sql = "SELECT shop_id, shop_name, location, latitude, longitude,
        (6371 * acos(
            cos(radians(?)) *
            cos(radians(latitude)) *
            cos(radians(longitude) - radians(?)) +
            sin(radians(?)) *
            sin(radians(latitude))
        )) AS distance
        FROM shops
        WHERE latitude IS NOT NULL AND longitude IS NOT NULL
        HAVING distance < ?
        ORDER BY distance ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("dddi", $lat, $lon, $lat, $radius_km);
$stmt->execute();
$result = $stmt->get_result();

$shops = [];
while ($row = $result->fetch_assoc()) {
    $shops[] = $row;
}

header('Content-Type: application/json');
echo json_encode($shops);
?>
