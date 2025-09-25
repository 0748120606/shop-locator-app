<?php
require 'connection.php';
$sql = "SELECT shop_name, latitude, longitude FROM shops WHERE status = 'available'";
$result = $conn->query($sql);

$shops = [];
while ($row = $result->fetch_assoc()) {
    $shops[] = $row;
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Shop Map</title>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
  <style>
    #map { height: 500px; }
  </style>
</head>
<body>
  <h2>Available Shops Map</h2>
  <div id="map"></div>

  <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
  <script>
    var map = L.map('map').setView([-1.2921, 36.8219], 12); // Centered on Nairobi

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap'
    }).addTo(map);

    var shops = <?php echo json_encode($shops); ?>;

    shops.forEach(function(shop) {
      if (shop.latitude && shop.longitude) {
        L.marker([shop.latitude, shop.longitude])
          .addTo(map)
          .bindPopup(shop.shop_name);
      }
    });
  </script>
</body>
</html>
