<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle shop removal from favorites
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_shop_id'])) {
    $shop_id = intval($_POST['remove_shop_id']);
    $stmt = $conn->prepare("DELETE FROM favorites WHERE user_id = ? AND shop_id = ?");
    $stmt->bind_param("ii", $user_id, $shop_id);
    $stmt->execute();
    $stmt->close();
    header("Location: favorites.php");
    exit();
}

// Fetch favorite shops for the user
$sql = "SELECT s.shop_id, s.shop_name, s.location, s.price, s.size, s.status,
               (SELECT image_path FROM shop_images WHERE shop_id = s.shop_id LIMIT 1) AS image_path
        FROM favorites f
        JOIN shops s ON f.shop_id = s.shop_id
        WHERE f.user_id = ?
        ORDER BY f.id DESC";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Prepare failed: " . $conn->error); 
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>



<!DOCTYPE html>
<html>
<head>
    <title>My Favorites</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f4f7;
            margin: 0;
            padding: 0;
        }

        .navbar {
            position: sticky;
            top: 0;
            background: white;
            color: #333;
            padding: 20px 200px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
        }
        .navbar a {
            color: #333;
            text-decoration: none;
            padding: 10px;
            transition: color 0.3s, text-decoration-color 0.3s;
            font-weight: bold;
        }
        .navbar a:hover {
            color: green;
            text-decoration: underline;
            text-decoration-color: green;
            text-underline-offset: 8px;
            font-weight: bold;
        }
        .container {
            max-width: 1200px;
            margin: auto;
            padding: 20px;
        }

        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 20px;
        }

        .card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .image-container {
            position: relative;
        }

        .image-container img {
            width: 100%;
            height: 220px;
            object-fit: cover;
        }

        .status-label {
            position: absolute;
            top: 10px;
            left: 10px;
            background: #28a745;
            color: white;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
        }

        .status-label.unavailable {
            background: #dc3545;
        }

        .fav-icon {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(255, 255, 255, 0.8);
            padding: 6px;
            border-radius: 50%;
            color: #e63946;
        }

        .card-content {
            padding: 16px;
        }

        .card-content h3 {
            margin: 0 0 10px;
            font-size: 18px;
        }

        .card-content p {
            margin: 6px 0;
            color: #555;
        }

        .card-content .location {
            color: #888;
            font-size: 14px;
        }

        .details-link {
            display: inline-block;
            margin-top: 10px;
            color: #6c5ce7;
            font-weight: bold;
            text-decoration: none;
        }

        .details-link:hover {
            text-decoration: underline;
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
    <h1>User Dashboard</h1>
    <div>
        <a href="favorites.php"><i class="fas fa-heart"></i> Favorites</a>
        <a href="notifications.php"><i class="fas fa-bell"></i> Notifications</a>
        <?php
        $redirectTarget = 'login.php?redirect=' . urlencode('owner_dashboard.php');
        if (isset($_SESSION['user_id'])) {
            $redirectTarget = $_SESSION['user_type'] === 'client' ? 'user_dashboard.php' : 'owner_dashboard.php';
        }
        ?>
        <a href="<?php echo $redirectTarget; ?>">
            <i class="fas fa-user-circle dropdown-icon"></i> Account
        </a>

        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<div class="container">
    <h2>My Favorite Shops</h2>
    <div class="card-grid">
    <?php if ($result->num_rows > 0): ?>
        <?php while ($shop = $result->fetch_assoc()): ?>
            <div class="shop-card">
            <div class="card">
            <div class="image-container">
                <img src="<?= htmlspecialchars($shop['image_path']) ?: 'default.jpg' ?>" alt="<?= htmlspecialchars($shop['shop_name']) ?>">
                <span class="status-label <?= strtolower($shop['status']) ?>"><?= ucfirst($shop['status']) ?></span>
                <span class="fav-icon" onclick="removeFavorite(<?= $shop['shop_id'] ?>, this)">
                    <i class="fas fa-heart"></i>
                </span>

            </div>
            <div class="card-content">
                <h3><?= htmlspecialchars($shop['shop_name']) ?></h3>
                <p class="location"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($shop['location']) ?></p>
                <p><?= htmlspecialchars($shop['size']) ?> sq ft • Ksh <?= number_format($shop['price']) ?>/month</p>
                <a href="view_shops.php?shop_id=<?= $shop['shop_id'] ?>"class="details-link">View Details</a>
            </div>
        </div>
        </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>You haven't added any favorites yet.</p>
    <?php endif; ?>
</div>
    </div>
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
        <li><a href="shop_connection.php">Find Shops</a></li>
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
    <p>&copy; 2025 Moon Gift Store. All rights reserved.</p>
  </div>
</footer>
<script>
function removeFavorite(shopId, iconElement) {
    if (!confirm("Remove this shop from your favorites?")) return;

    fetch('remove_favorite.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `shop_id=${encodeURIComponent(shopId)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove the entire shop card from the DOM
            const card = iconElement.closest('.shop-card');
            if (card) card.remove();
        } else {
            alert("Failed to remove from favorites.");
        }
    })
    .catch(error => {
        console.error("Error:", error);
        alert("An error occurred.");
    });
}
</script>

</body>
</html>
