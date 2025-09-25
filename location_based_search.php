<!DOCTYPE html>
<html>
<head>
  <title>Shop Locator - Location Based Search</title>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Leaflet CSS -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <style>
    #map { height: 500px; width: 100%; }
    #searchBar { margin: 10px; padding: 10px; width: 300px; }
    body {
            font-family: 'Montserrat', sans-serif;
            margin: 0;
            padding: 0;
            background: #f9f9f9;
        }
      .site-footer {
        background-color: #1f1f1f;
        color: #f0f0f0;
        padding: 40px 20px;
        font-family: 'Arial', sans-serif;
        }

        .footer-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        gap: 30px;
        max-width: 1200px;
        margin: auto;
        }

        .footer-column {
        flex: 1;
        min-width: 220px;
        }

        .footer-column h4 {
        font-size: 18px;
        margin-bottom: 15px;
        color: #fff;
        border-bottom: 2px solid #444;
        padding-bottom: 5px;
        }

        .footer-column ul {
        list-style: none;
        padding: 0;
        }

        .footer-column ul li {
        margin-bottom: 10px;
        color: #ccc;
        }

        .footer-column a {
        color: #ccc;
        text-decoration: none;
        }

        .footer-column a:hover {
        text-decoration: underline;
        color: #fff;
        }

        .footer-column i {
        margin-right: 8px;
        color: #f09d51;
        }

        .social-icons a {
        color: #ccc;
        font-size: 20px;
        margin-right: 15px;
        transition: color 0.3s;
        }

        .social-icons a:hover {
        color: #f09d51;
        }

        .footer-bottom {
        text-align: center;
        margin-top: 40px;
        border-top: 1px solid #333;
        padding-top: 15px;
        font-size: 14px;
        color: #bbb;
        }

  </style>
</head>
<body>

<h2>Find Shops by Location</h2>
<input type="text" id="searchBar" placeholder="Enter location (e.g. Westlands, Nairobi)" />
<div id="map"></div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
  // Initialize the map centered on Nairobi
  const map = L.map('map').setView([-1.2921, 36.8219], 13);

  // Add OpenStreetMap tile layer
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
  }).addTo(map);

  // Load all shop markers from DB when page loads
  function loadAllShops() {
    fetch('get_all_shops.php')
      .then(response => response.json())
      .then(shops => {
        shops.forEach(shop => {
          L.marker([shop.latitude, shop.longitude], {
            icon: L.icon({
              iconUrl: 'https://maps.gstatic.com/mapfiles/ms2/micons/red-dot.png',
              iconSize: [24, 24],
              iconAnchor: [12, 24]
            })
          }).addTo(map)
            .bindPopup(`<b>${shop.shop_name}</b><br>${shop.location}<br>
              <a href="view_shops.php?shop_id=${shop.shop_id}" target="_blank">View Shop</a>`);
        });
      });
  }

  // Call immediately on page load
  loadAllShops();

  // Geocode location input using Nominatim
  async function geocodeLocation(query) {
    const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`);
    const data = await response.json();
    return data[0]; // First result
  }

  // When user presses Enter
  document.getElementById('searchBar').addEventListener('keypress', async function (e) {
    if (e.key === 'Enter') {
      const location = this.value;
      const result = await geocodeLocation(location);
      if (result) {
        const lat = parseFloat(result.lat);
        const lon = parseFloat(result.lon);
        map.setView([lat, lon], 15);

        // Remove old search marker if needed
        if (window.searchMarker) {
          map.removeLayer(window.searchMarker);
        }

        // Add only the search marker
        window.searchMarker = L.marker([lat, lon], {
          icon: L.icon({
            iconUrl: 'https://maps.gstatic.com/mapfiles/ms2/micons/blue-dot.png',
            iconSize: [24, 24],
            iconAnchor: [12, 24]
          })
        }).addTo(map)
          .bindPopup(`Search Location: ${result.display_name}`)
          .openPopup();

        // 🔍 Try to load nearby shops from server
        const res = await fetch(`get_nearby_shops.php?lat=${lat}&lon=${lon}`);
        const shops = await res.json();

        if (shops.length === 0) {
          alert("No shops found near this location.");
          return;
        }

        // Only if shops exist, add them to the map
        shops.forEach(shop => {
          L.marker([shop.latitude, shop.longitude], {
            icon: L.icon({
              iconUrl: 'https://maps.gstatic.com/mapfiles/ms2/micons/red-dot.png',
              iconSize: [24, 24],
              iconAnchor: [12, 24]
            })
          }).addTo(map)
            .bindPopup(`<b>${shop.shop_name}</b><br>${shop.location}<br>
              <a href="view_shops.php?shop_id=${shop.shop_id}" target="_blank">View Shop</a>`);
        });

      } else {
        alert("Location not found.");
      }
    }
  });
</script>

<!-- Footer content outside the script block -->
<footer class="site-footer">
  <div class="footer-container">
    <div class="footer-column">
      <h4>Shop Locator</h4>
      <p>Connecting shop owners with clients, making space discovery simple and efficient.</p>
    </div>

    <div class="footer-column">
      <h4>Quick Links</h4>
      <ul>
        <li><a href="index.php#home">Home</a></li>
        <li><a href="#">Find Shops</a></li>
        <li><a href="index.php#contact">Contact</a></li>
      </ul>
    </div>

    <div class="footer-column">
      <h4>Contact</h4>
      <ul>
        <li><i class="fas fa-envelope"></i> support@shoplocator.com</li>
        <li><i class="fas fa-phone"></i> +254 748 120 606</li>
        <li><i class="fas fa-map-marker-alt"></i> Nairobi, Kenya</li>
      </ul>
    </div>

    <div class="footer-column">
      <h4>Follow Us</h4>
      <div class="social-icons">
        <a href="#"><i class="fab fa-facebook-f"></i></a>
        <a href="#"><i class="fab fa-instagram"></i></a>
        <a href="#"><i class="fab fa-x-twitter"></i></a>
      </div>
    </div>
  </div>

  <div class="footer-bottom">
    <p>&copy; 2025 Shop Locator. All rights reserved.</p>
  </div>
</footer>

</body>
</html>
