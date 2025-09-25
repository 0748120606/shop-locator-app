<?php
session_start();
include 'connection.php';

$user_id = $_SESSION['user_id'] ?? null;

// Handle filters
$location = isset($_GET['location']) ? trim($_GET['location']) : '';
$max_price = isset($_GET['price']) ? (int)$_GET['price'] : 100000;
$max_size = isset($_GET['size']) ? (int)$_GET['size'] : 300;

// Pagination settings
$limit = 6; // shops per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Base SQL parts
$base_sql = "FROM shops s
             JOIN owners o ON s.user_id = o.user_id
             JOIN users u ON o.user_id = u.user_id
             WHERE s.status = 'available' AND s.approved = 1";

$conditions = [];
$params = [];
$types = '';

// Apply filters
if (!empty($location)) {
    $conditions[] = "s.location = ?";
    $types .= 's';
    $params[] = $location;
}
if (!empty($max_price)) {
    $conditions[] = "s.price <= ?";
    $types .= 'i';
    $params[] = $max_price;
}
if (!empty($max_size)) {
    $conditions[] = "s.size <= ?";
    $types .= 'i';
    $params[] = $max_size;
}
if ($conditions) {
    $base_sql .= " AND " . implode(" AND ", $conditions);
}

// Total count query for pagination
$count_sql = "SELECT COUNT(*) as total " . $base_sql;
$count_stmt = $conn->prepare($count_sql);
if ($params) {
    $count_stmt->bind_param($types, ...$params);
}
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_shops = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_shops / $limit);

// Final shop query with LIMIT and OFFSET
$sql = "SELECT s.shop_id, s.shop_name, s.location, s.size, s.price, s.additional_notes, s.availability_date,
               u.phone_number,
               (SELECT image_path FROM shop_images WHERE shop_id = s.shop_id LIMIT 1) AS image_url
        " . $base_sql . "
        ORDER BY s.price ASC
        LIMIT ? OFFSET ?";

$params[] = $limit;
$params[] = $offset;
$types .= 'ii';

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Fetch shop data
$shops = [];
while ($row = $result->fetch_assoc()) {
    // Average rating
    $rating_sql = "SELECT AVG(rating) as avg_rating FROM ratings WHERE shop_id = ?";
    $rating_stmt = $conn->prepare($rating_sql);
    $rating_stmt->bind_param("i", $row['shop_id']);
    $rating_stmt->execute();
    $rating_result = $rating_stmt->get_result();
    $rating_row = $rating_result->fetch_assoc();
    $row['avg_rating'] = round($rating_row['avg_rating'], 1) ?: "Not yet rated";

    // Check favorite
    if ($user_id) {
        $fav_sql = "SELECT 1 FROM favorites WHERE user_id = ? AND shop_id = ?";
        $fav_stmt = $conn->prepare($fav_sql);
        $fav_stmt->bind_param("ii", $user_id, $row['shop_id']);
        $fav_stmt->execute();
        $fav_result = $fav_stmt->get_result();
        $row['is_favorited'] = $fav_result->num_rows > 0;
    } else {
        $row['is_favorited'] = false;
    }

    $shops[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Available Shops</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Montserrat', sans-serif;
            overflow-x: hidden;
        }

        .navbar {
            position: sticky;
            top: 0;
            width: 100%;
            background: white;
            flex-direction: row;
            padding: 25px 200px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        @media (max-width: 1115px) {
            .navbar {
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
                padding: 15px 20px;
            }
        }

        .logo {
            font-size: 1.6em;
            font-weight: bold;
            color: #333;
            font-size: 1.6em;
            white-space: nowrap;
        }
    
        .hamburger {
            display: none;
            font-size: 24px;
            cursor: pointer;
            color: #333;
        }

        .nav-links {
            display: flex;
            gap: 10px;
            font-size: 1em;
        }

        .nav-links a {
            text-decoration: none;
            color: #333;
            font-weight: 600;
            padding: 8px 10px;
            transition: color 0.3s;
        }

        .nav-links a:hover {
            color: green;
            text-decoration: underline;
            text-decoration-color: green;
            text-underline-offset: 6px;
        }

        /* Responsive - Small Screens */
        @media (max-width: 868px) {
            .navbar {
                padding: 15px 20px; 
                flex-direction: row; 
                justify-content: space-between; 
                align-items: center;
            }
            .hamburger {
                display: block;
            }

            .nav-links {
                display: none;
                flex-direction: column;
                align-items: center;
                position: absolute;
                top: 100%;
                left: 0;
                width: 100%;
                background: white;
                padding: 20px 0;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                animation: slideDown 0.3s ease-in-out;
                z-index: 999;
            }

            .nav-links.show {
                display: flex;
            }

            @keyframes slideDown {
                from {
                    opacity: 0;
                    transform: translateY(-10px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        }
        h1 {
            margin-top: 20px;
            text-align: center;
            color:green;
            font-family:'Montserrat', sans-serif;
        }

        .services-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
            gap: 30px;
            margin-top: 40px;
            padding:20px;
        }

        @media (max-width: 1024px) {
            .services-container {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 600px) {
            .services-container {
                grid-template-columns: 1fr;
            }
        }

        .shop-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.08);
            display: flex;
            flex-direction: column;
            transition: 0.3s ease;
        }

        .shop-card:hover {
            transform: scale(1.02);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }

        .shop-image {
            width: 100%;
            margin-bottom: 15px;
            position: relative;
        }

        .favorite-icon {
            position: absolute;
            top: 10px;
            right: 10px;
            background: white;
            padding: 6px;
            border-radius: 50%;
            box-shadow: 0 2px 5px rgba(0,0,0,0.15);
            cursor: pointer;
            z-index: 10;
        }

        .favorite-icon.favorited .fa-heart {
            color: red;
        }

        .favorite-icon .fa-heart {
            color: #ccc;
        }

        .shop-image img {
            width: 100%;
            height: 220px;
            object-fit: cover;
        }
        .shop-info{
            padding: 18px;
            padding-top:1px;
        }

        .shop-info h2 {
            font-size: 20px;
            margin-bottom: 8px;
        }
        .desc{
            text-align:center;
            font-size:1.2em;
            color:#333333;
        }

        .shop-info p {
            margin: 4px 0;
            font-size: 14px;
        }

        .rating {
            font-size: 14px;
            color: #ff9800;
        }
        .shop-actions {
            display: flex;
            gap: 16px; /* space between buttons */
            margin-top: 10px;
            font-weight: bold;
        }

        .shop-actions a {
            background: none;
            color:  #7c3aed;
            padding: 6px 12px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 20px; /* pill shape */
            transition: all 0.3s ease;
        }

        /* WhatsApp link base */
        .whatsapp-link {
            color: #555;
        }

        /* WhatsApp on hover */
        .whatsapp-link:hover {
            background-color: #25D36620; /* light green background */
            color: #25D366;
        }

        /* View Shop base */
        .view-link {
            color: #7c3aed; /* purple or site primary */
        }

        /* View Shop hover */
        .view-link:hover {
            background-color: #7c3aed20; /* light background */
            color: #7c3aed;
        }

        #notification {
            position: fixed;
            bottom: 20px;
            right: 30px;
            background: white;
            color: #333333;
            padding: 12px 20px;
            border-radius: 8px;
            display: none;
            font-size: 14px;
            z-index: 9999;
        }
        .toggle-btn {
            background-color: #7c3aed;
            color: white;
            border: none;
            padding: 12px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 8px;
            transition: background-color 0.3s ease;
        }

        .toggle-btn:hover {
            background-color: #27ae60;
        }

        .filter-panel {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            font-family: 'Montserrat', sans-serif;
        }

        .filter-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
            font-size: 18px;
            margin-bottom: 15px;
        }

        .reset-btn {
            background: none;
            border: none;
            color: #9b59b6;
            font-weight: 600;
            cursor: pointer;
        }

        .filter-panel label {
            display: block;
            margin-top: 12px;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .filter-panel select,
        .filter-panel input[type="range"] {
            width: 100%;
            padding: 8px;
            border-radius: 6px;
            border: 1px solid #ccc;
            outline: none;
            box-sizing: border-box;
            margin-bottom: 5px;
        }

        output {
            display: block;
            text-align: right;
            font-size: 14px;
            color: #555;
            margin-bottom: 10px;
        }

        .apply-btn {
            width: 100%;
            padding: 12px;
            background-color: #9b59b6;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
        }
        .app-btn:hover {
        background-color: #27ae60;
        }
        .top-controls {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-bottom: 20px;
        }

        .back-btn {
        background-color: var(--primary);
        color: #7c3aed;
        padding: 12px 20px;
        font-size: 16px;
        border: none;
        border-radius: var(--radius);
        cursor: pointer;
        transition: background 0.3s ease;
    }

    .back-btn:hover {
        background-color: var(--accent);
    }

        .site-footer {
            background-color: #1f1f1f;
            color: #f0f0f0;
            padding: 40px 20px;
            font-family: 'Arial', sans-serif;
            position: sticky;
            margin-top:150px;
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
            .pagination {
                display: flex;
                justify-content: center;
                margin: 40px 0;
                flex-wrap: wrap;
                gap: 8px;
            }

            .pagination a,
            .pagination span {
                padding: 10px 16px;
                background-color: #fff;
                color: #333;
                border-radius: 6px;
                border: 1px solid #ccc;
                text-decoration: none;
                font-weight: 500;
                transition: all 0.3s ease;
            }

            .pagination a:hover {
                background-color: #7c3aed20;
                color: #7c3aed;
                border-color: #7c3aed;
            }

            .pagination .active {
                background-color: #7c3aed;
                color: white;
                border-color: #7c3aed;
            }

            @media (max-width: 600px) {
                .pagination a,
                .pagination span {
                    padding: 8px 12px;
                    font-size: 14px;
                }
            }

            /* Responsive Design */
            @media (max-width: 768px) {
            .footer-container {
                flex-direction: column;
                text-align: center;
                align-items: center;
            }

            .footer-column {
                max-width: 100%;
            }

            .social-icons {
                justify-content: center;
            }
            }
    </style>
</head>
<body>
<div class="navbar">
    <div class="logo">SHOP LOCATOR</div>
    <div class="hamburger" onclick="toggleMenu()">
        <i class="fas fa-bars"></i>
    </div>
    <div class="nav-links" id="navLinks">
        <a href="index.php#home"><i class="fa-solid fa-house"></i> Home</a>
        <a href="index.php#about"><i class="fa-solid fa-circle-info"></i> About</a>
        <a href="index.php#services"><i class="fa-solid fa-shop"></i> Services</a>
        <a href="favorites.php"><i class="fas fa-heart"></i> Favorites</a>
        <a href="shop_connection.php">Find Shops</a>
    </div>
</div>

<h1>Find the Perfect Shop Space for<br> Your Business</h1>
<p class="desc">Connect with shop owners and find available retail spaces without the <br>hassle. Search, filter, and contact owners directly.<p>
<?php if (empty($shops)): ?>
    <p style="text-align:center;">No shops found for your criteria.</p>
<?php else: ?>
<!-- Top Controls: Back & Filter Buttons -->
<div class="top-controls">
  <button id="backBtn" class="back-btn" onclick="clearFilters()" style="display: none;">
    <i class="fa fa-arrow-left"></i> Back to Shops
  </button>

  <button id="toggleBtn" class="toggle-btn" onclick="toggleFilter()">
    <i class="fa fa-filter"></i> Show Filter
  </button>
</div>

<!-- FILTER PANEL (hidden initially) -->
<div class="filter-panel" id="filterPanel" style="display: none;">
    <div class="filter-header">
        <i class="fa fa-filter"></i> Filters
        <button class="reset-btn" type="button" onclick="resetFilters()">Reset</button>
    </div>

    <form method="GET" id="filterForm" class="filter-form">
        <!-- Location -->
        <label for="location">Location</label>
        <select name="location" id="location">
            <option value="">All Locations</option>
            <option value="New York">Nairobi</option>
            <option value="Los Angeles">Mombasa</option>
        </select>

        <!-- Price Range -->
        <label for="priceRange">Price Range (Kes/month)</label>
        <input type="range" name="price" id="priceRange" min="0" max="100000" value="100000" oninput="priceOutput.value = 'Kes0 - Kes' + this.value">
        <output id="priceOutput">Kes0 - Kes100000</output>

        <!-- Size Range -->
        <label for="sizeRange">Size (sq ft)</label>
        <input type="range" name="size" id="sizeRange" min="0" max="300" value="300" oninput="sizeOutput.value = '0 - ' + this.value + ' sq ft'">
        <output id="sizeOutput">0 - 300 sq ft</output>

        <!-- Apply Button -->
        <button type="submit" class="apply-btn">Apply Filters</button>
    </form>
</div>

    <?php if ($result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="shop-card">
            <h3><?= htmlspecialchars($row['shop_name']) ?></h3>
            <p><strong>Location:</strong> <?= htmlspecialchars($row['location']) ?></p>
            <p><strong>Price:</strong> ₦<?= number_format($row['price']) ?></p>
            <p><strong>Size:</strong> <?= htmlspecialchars($row['size']) ?> sq ft</p>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>No shops found for the selected filters.</p>
<?php endif; ?>
</div>

    <div class="services-container">
        <?php foreach ($shops as $shop): ?>
            <div class="shop-card">
                <div class="shop-image">
                    <img src="<?= htmlspecialchars($shop['image_url']) ?>" alt="Shop Image">
                    <div class="favorite-icon <?= isset($shop['is_favorited']) && $shop['is_favorited'] ? 'favorited' : '' ?>"
                        onclick="toggleFavorite(this, <?= $shop['shop_id'] ?>, '<?= htmlspecialchars($shop['location'], ENT_QUOTES) ?>')">
                        <i class="fa-solid fa-heart"></i>
                    </div>


                </div>
                <div class="shop-info">
                    <h2><?= htmlspecialchars($shop['shop_name']) ?></h2>
                    <p><i class="fa-solid fa-location-dot" style="color:#007B3A;"></i> <?= htmlspecialchars($shop['location']) ?></p>
                    <p><strong>Price:</strong> Ksh <?= number_format($shop['price']) ?></p>
                    <p><strong>Size:</strong> <?= htmlspecialchars($shop['size']) ?> sq ft</p>
                    <p><strong>Available From:</strong> <?= htmlspecialchars($shop['availability_date']) ?></p>
                    <div class="rating"><i class="fas fa-star"></i> <?= $shop['avg_rating'] ?></div>
                    <div class="shop-actions">
                        <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $shop['phone_number']) ?>" 
                        target="_blank" class="whatsapp-link">
                        <i class="fab fa-whatsapp"></i> WhatsApp
                        </a>
                        <a href="view_shops.php?shop_id=<?= $shop['shop_id'] ?>" class="view-link">View Shop</a>
                    </div>

                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div id="notification"></div>
<!-- Font Awesome CDN -->
<link
  rel="stylesheet"
  href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <button class="back-btn" onclick="history.back()">← Go Back</button>

<?php if ($total_pages > 1): ?>
    <div class="pagination">
        <?php
        $queryParams = $_GET;
        $queryParams['page'] = 1;
        $firstUrl = '?' . http_build_query($queryParams);
        ?>

        <a href="<?= $firstUrl ?>"><i class="fas fa-angle-double-left"></i></a>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <?php
            $queryParams['page'] = $i;
            $pageUrl = '?' . http_build_query($queryParams);
            ?>
            <?php if ($i == $page): ?>
                <span class="active"><?= $i ?></span>
            <?php else: ?>
                <a href="<?= $pageUrl ?>"><?= $i ?></a>
            <?php endif; ?>
        <?php endfor; ?>

        <?php
        $queryParams['page'] = $total_pages;
        $lastUrl = '?' . http_build_query($queryParams);
        ?>
        <a href="<?= $lastUrl ?>"><i class="fas fa-angle-double-right"></i></a>
    </div>
<?php endif; ?>

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
  <p>&copy; <?php echo date('Y'); ?> Shop Locator. All rights reserved.</p>
  </div>
</footer>
<script>
    function toggleMenu() {
        const nav = document.getElementById("navLinks");
        nav.classList.toggle("show");
    }
function toggleFavorite(element, shopId, location) {
    console.log("Favorite toggled:", { shopId, location });

    const action = element.classList.contains('favorited') ? 'remove' : 'add';

    const formData = new FormData();
    formData.append('shop_id', shopId);
    formData.append('location', location);
    formData.append('action', action);

    fetch('toggle_favorite.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        console.log("Server response:", data);
        if (data.status === 'success') {
            element.classList.toggle('favorited');
        }
        showNotification(data.message);
        if (data.redirect) {
            setTimeout(() => {
                window.location.href = data.redirect;
            }, 1000);
        }
    })
    .catch(error => {
        console.error("Error:", error);
        showNotification("Something went wrong.");
    });
}

function showNotification(message) {
    const notif = document.createElement('div');
    notif.innerText = message;
    notif.style.cssText = `
        position: fixed;
        bottom: 20px;
        right: 20px;
        background:white;
        color:  #7c3aed;
        padding: 20px 40px;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        z-index: 9999;
    `;
    document.body.appendChild(notif);
    setTimeout(() => notif.remove(), 3000);
}
   function toggleFilter() {
    const panel = document.getElementById("filterPanel");
    const btn = document.getElementById("toggleBtn");

    if (panel.style.display === "none" || panel.style.display === "") {
      panel.style.display = "block";
      btn.innerHTML = '<i class="fa fa-times"></i> Hide Filter';
    } else {
      panel.style.display = "none";
      btn.innerHTML = '<i class="fa fa-filter"></i> Show Filter';
    }
  }

  function resetFilters() {
    document.getElementById("filterForm").reset();
    document.getElementById("priceOutput").value = "Kes0 - Kes100000";
    document.getElementById("sizeOutput").value = "0 - 300 sq ft";
  }

  function clearFilters() {
    // Redirect to same page without query string
    window.location.href = window.location.pathname;
  }

  // On page load, determine if filters are active
  window.addEventListener("DOMContentLoaded", () => {
    const urlParams = new URLSearchParams(window.location.search);
    const hasFilters = urlParams.get("location") || urlParams.get("price") || urlParams.get("size");

    const panel = document.getElementById("filterPanel");
    const toggleBtn = document.getElementById("toggleBtn");
    const backBtn = document.getElementById("backBtn");

    if (hasFilters) {
      panel.style.display = "none";
      toggleBtn.innerHTML = '<i class="fa fa-filter"></i> Show Filter';
      backBtn.style.display = "inline-flex";
    } else {
      backBtn.style.display = "none";
    }
  });
</script>
</body>
</html>
