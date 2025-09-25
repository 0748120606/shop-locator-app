<?php
// view_shops.php
include 'connection.php';

if (!isset($_GET['shop_id'])) {
    echo "No shop ID provided.";
    exit;
}

$shop_id = intval($_GET['shop_id']);

//  fetch full_name, email, phone_number from users table
$sql = "SELECT s.shop_name, s.location, s.size, s.price, s.additional_notes, s.availability_date,
               u.full_name, u.email, u.phone_number,
               (SELECT image_path FROM shop_images WHERE shop_id = s.shop_id LIMIT 1) AS image_url
        FROM shops s
        JOIN users u ON s.user_id = u.user_id
        WHERE s.shop_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $shop_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Shop not found.";
    exit;
}

$shop = $result->fetch_assoc();

$phone_number = preg_replace('/^0/', '254', $shop['phone_number']);
?>


<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($shop['shop_name']) ?> - Shop Details</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

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

        /* navbar layout */
    .navbar {
        position: sticky;
        top: 0;
        width: 100%;
        background: white;
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
        white-space: nowrap;
    }

    /* Hamburger icon */
    .hamburger {
        display: none;
        font-size: 24px;
        cursor: pointer;
        color: #333;
    }

    /* Navigation links */
    .nav-links {
        display: flex;
        gap: 10px;
        font-size:1.2em;
        font-weight:bold;

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

    /* Mobile Styles */
    @media (max-width: 850px) {
        .navbar {
            flex-direction: row; /* stays in a row */
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
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
            font-size:1.2em;
            gap: 10px;
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

        .top-section {
            flex-direction: column;
            padding: 40px 20px;
        }

        .left-image,
        .right-details {
            width: 100%;
            padding: 0;
        }

        .right-details {
            margin-top: 20px;
            flex: unset;
            width: 100%;
        }

        .right-details > div {
            margin-bottom: 10px;
        }

        .badge {
            font-size: 13px;
            padding: 4px 10px;
            text-align: center;
        }

        body {
            font-size: 14px;
            padding: 0 10px;
        }
        .container {
            max-width: 1200px;
            margin: 40px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        .header {
            padding: 20px 30px;
            border-bottom: 1px solid #eee;
            font-size: 24px;
            font-weight: 600;
            background: #f9fafb;
        }

        .top-section {
            display: flex;
            flex-wrap: wrap;
            padding: 30px;
        }

        .left-image {
            flex: 2;
            padding-right: 30px;
        }

        .left-image img {
            width: 100%;
            border-radius: 10px;
            height: 400px;
            object-fit: cover;
        }

        .right-details {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .badge {
            background-color: #d1fae5;
            color: #065f46;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 14px;
            display: inline-block;
            width: fit-content;
        }

        .price, .size, .listed {
            font-size: 16px;
            margin-bottom: 6px;
        }

        .contact-btn {
            background-color: #7c3aed;
            color: white;
            text-decoration: none;
            padding: 12px;
            text-align: center;
            border-radius: 8px;
            font-weight: 600;
            transition: background 0.3s;
        }

        .contact-btn:hover {
            background-color: #25D366;
        }

        .description {
            padding: 30px;
            border-top: 1px solid #eee;
            background: #fcfcfc;
        }

        .description h3 {
            margin-top: 0;
            font-size: 20px;
        }

        .description p {
            margin: 10px 0 0 0;
            line-height: 1.6;
        }
        /* Star display (readonly) */
        .fa-star,
        .fa-star-o {
            color: #f5b301;
            font-size: 18px;
            margin-right: 2px;
        }

        /* Interactive rating stars (form) */
        #star-select {
            display: flex;
            gap: 5px;
            margin-top: 5px;
        }

        #star-select .rate-star {
            font-size: 26px;
            color: #f5b301;
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        #star-select .rate-star:hover {
            transform: scale(1.2);
        }

        /* Form styling */
        #rating-form {
            margin-top: 20px;
            max-width: 600px;
            background: #f9f9f9;
            padding: 16px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        #rating-form label {
            font-weight: bold;
            margin-bottom: 6px;
            display: inline-block;
        }

        #rating-form textarea {
            width: 100%;
            resize: vertical;
            border: 1px solid #ccc;
            padding: 10px;
            border-radius: 6px;
            font-size: 14px;
        }

        #rating-form button {
            background-color: #7c3aed;
            color: white;
            padding: 10px 18px;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        #rating-form button:hover {
            background-color: #5b23d4;
        }

        /* Review card */
        .review-item {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }

        .review-item strong {
            font-size: 16px;
        }

        .review-item p {
            margin: 5px 0 0 0;
            font-size: 15px;
            line-height: 1.4;
        }
        .back-btn {
            background: none;
            border: none;
            color: #7c3aed;
            font-size: 16px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: text-decoration 0.3s;
        }

        .back-btn i {
            font-size: 16px;
        }

        .back-btn:hover {
            text-decoration: underline;
        }
        .top-controls {
        display: flex;
        justify-content: flex-start; /* or center */
        padding: 10px 20px;
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
        
        @media (max-width: 768px) {
        body {
            font-size: 14px;
            padding: 0 10px;
        }
        }
        @media (max-width: 768px) {
            .top-section {
                flex-direction: column;
                padding: 40px 20px;
            }

            .top-section img {
                width: 100%;
                height: auto;
            }
        }
        @media (max-width: 768px) {
            .right-details {
                margin-top: 20px;
                flex: unset;
                width: 100%;
            }
                        .right-details > div {
                margin-bottom: 10px;
            }

            .badge {
                font-size: 13px;
                padding: 4px 10px;
                text-align: center;
            }
                }@media (max-width: 768px) {
            .shop-details-container {
                flex-direction: column;
                align-items: flex-start;
            }

            .left-image, .right-details {
                width: 100%;
                padding: 0;
            }
        }
</style>
</head>
<body>
    <!-- Navbar -->
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
<div class="top-controls">
<a href="shop_connection.php" class="back-btn">
    <i class="fa fa-arrow-left"></i> Back to Shops
</a>
</div>
<div class="container">
    <div class="header"><?= htmlspecialchars($shop['shop_name']) ?></div>

    <!-- TOP SECTION: Image + Shop Info -->
    <div class="top-section">
        <!-- LEFT -->
        <div class="left-image">
            <img src="<?= htmlspecialchars($shop['image_url']) ?>" alt="Shop Image">
        </div>

        <!-- RIGHT -->
        <div class="right-details">
            <span class="badge">Available</span>
            <div><i class="fa-solid fa-location-dot" style="color:#1b4332;"></i> <?= htmlspecialchars($shop['location']) ?></div>
            <div class="size"><strong>Size:</strong> <?= htmlspecialchars($shop['size']) ?> sq ft</div>
            <div class="price"><strong>Monthly Price:</strong> Ksh <?= number_format($shop['price']) ?></div>
            <div class="listed"><strong>Listed on:</strong> <?= htmlspecialchars($shop['availability_date']) ?></div>
            <!-- Average Rating -->
         <?php
        $rating_result = $conn->query("SELECT ROUND(AVG(rating),1) AS avg_rating, COUNT(*) AS total FROM ratings WHERE shop_id = $shop_id");
        $rating_data = $rating_result->fetch_assoc();
        ?>
        <div style="margin-bottom: 20px;">
        <?php if ($rating_data['avg_rating']): ?>
    <strong>Average Rating:</strong>
    <?php
        $avg = $rating_data['avg_rating'];
        $fullStars = floor($avg);
        $halfStar = ($avg - $fullStars >= 0.5) ? 1 : 0;
        $emptyStars = 5 - $fullStars - $halfStar;
    ?>

    <?php for ($i = 0; $i < $fullStars; $i++): ?>
        <i class="fa fa-star" style="color: #f5b301;"></i>
    <?php endfor; ?>
    <?php if ($halfStar): ?>
        <i class="fa fa-star-half-o" style="color: #f5b301;"></i>
    <?php endif; ?>
    <?php for ($i = 0; $i < $emptyStars; $i++): ?>
        <i class="fa fa-star-o" style="color: #f5b301;"></i>
    <?php endfor; ?>

    (<?= $rating_data['avg_rating'] ?> / 5 based on <?= $rating_data['total'] ?> ratings)
<?php else: ?>
    <strong>Average Rating:</strong> Not rated yet
<?php endif; ?>

        </div>
        <div><strong>Owner:</strong> <?= htmlspecialchars($shop['full_name']) ?></div>
        <div><strong>Email:</strong> <?= htmlspecialchars($shop['email']) ?></div>

            <div>
                <a class="contact-btn" href="https://wa.me/<?= $phone_number ?>" target="_blank">

                    Contact Owner via WhatsApp
                </a>
            </div>
        </div>
    </div>

    <!--Reviews, and Rating Form -->
    <div class="description">
        <!-- Shop Description -->
        <div style="margin-top: 40px;">
            <h3>About This Shop</h3>
            <p><?= nl2br(htmlspecialchars($shop['additional_notes'])) ?></p>
        </div>
        <!-- Ratings & Reviews -->
<h3>Ratings & Reviews</h3>
<?php
$ratings_sql = "SELECT u.full_name, r.rating, r.review, r.date_posted 
                FROM ratings r 
                JOIN users u ON r.user_id = u.user_id 
                WHERE r.shop_id = ? 
                ORDER BY r.date_posted DESC";

$ratings_q = $conn->prepare($ratings_sql);
$ratings_q->bind_param("i", $shop_id);
$ratings_q->execute();
$ratings_res = $ratings_q->get_result();

if ($ratings_res->num_rows > 0):
    while ($row = $ratings_res->fetch_assoc()):
?>
    <div style="margin-bottom: 20px; border-bottom: 1px solid #ddd; padding-bottom: 10px;">
        <strong><?= htmlspecialchars($row['full_name']) ?></strong> 
        - 
        <?php
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $row['rating']) {
                echo '<i class="fa fa-star" style="color: #f5b301;"></i>';
            } else {
                echo '<i class="fa fa-star-o" style="color: #f5b301;"></i>';
            }
        }
        ?>
        <small style="color: gray;"><?= date("M d, Y", strtotime($row['date_posted'])) ?></small>
        <p><?= nl2br(htmlspecialchars($row['review'])) ?></p>
    </div>
<?php
    endwhile;
else:
    echo "<p>No reviews yet. Be the first to rate this shop!</p>";
endif;
?>

<!-- Rating Form -->
<h3>Rate This Shop</h3>
<form action="rate_shop.php" method="POST" id="rating-form">
    <input type="hidden" name="shop_id" value="<?= $shop_id ?>">
    <input type="hidden" name="user_id" value="<?= $_SESSION['user_id'] ?? 0 ?>">
    <input type="hidden" name="rating" id="rating-value" required>

    <label>Your Rating:</label><br>
    <div id="star-select">
        <?php for ($i = 1; $i <= 5; $i++): ?>
            <i class="fa fa-star-o rate-star" data-value="<?= $i ?>" style="font-size: 24px; color: #f5b301; cursor: pointer;"></i>
        <?php endfor; ?>
    </div>

    <br><br>
    <label for="review">Leave a review (optional):</label><br>
    <textarea name="review" id="review" rows="3" style="width: 100%; max-width: 500px; padding: 8px; border-radius: 6px;"></textarea>

    <br><br>
    <button type="submit" style="padding: 8px 16px; background-color: #7c3aed; color: white; border: none; border-radius: 6px; cursor: pointer;">Submit</button>
</form>
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
    <p>&copy; <?php echo date('Y'); ?> Shop Locator. All rights reserved.</p>
  </div>
</footer>
<script>
    function toggleMenu() {
        const nav = document.getElementById("navLinks");
        nav.classList.toggle("show");
    }
    const stars = document.querySelectorAll('.rate-star');
    const ratingInput = document.getElementById('rating-value');

    stars.forEach(star => {
        star.addEventListener('click', function () {
            const value = this.getAttribute('data-value');
            ratingInput.value = value;

            stars.forEach(s => {
                s.classList.remove('fa-star');
                s.classList.add('fa-star-o');
            });

            for (let i = 0; i < value; i++) {
                stars[i].classList.remove('fa-star-o');
                stars[i].classList.add('fa-star');
            }
        });
    });
</script>
</body>
</html>
