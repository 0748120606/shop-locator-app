<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    // Save the intended page
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    header('Location: login.php');
    exit();
}

if ($_SESSION['user_type'] !== 'owner') {
    header('Location: user_dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop Locator - Update Shop Listing</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Montserrat', sans-serif;
            margin: 0;
            padding: 0;
            background: #f9f9f9;
        }
        .navbar {
            position: sticky;
            top: 0;
            width: 100%;
            background: white;
            color: #333;
            padding: 25px 200px;
            display: flex;
            justify-content: space-between;
            align-items: center;
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
        .nav-links {
            display: flex;
            gap: 10px;
            font-size: 1em;
        }

        .hero {
            position: relative;
            background: url('images/img10.jpg') no-repeat center center/cover;
            width: 100%;
            max-width: 1350px;
            height: 500px;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: white;
        }

        .hero::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
        }
        .logo {
            font-size: 1.6em;
            font-weight: bold;
            white-space: nowrap;

        }
        .top-controls {
            display: flex;
            justify-content: flex-start;
            padding-left: 5%;
            margin-top: 30px;
            margin-bottom: -20px; 
        }
        a.back-btn {
                background: none;
                border: none;
                color: #7c3aed;
                font-size: 16px;
                cursor: pointer;
                display: inline-flex;
                align-items: left;
                gap: 6px;
                font-weight: 500;
                padding: 10px 15px;
                margin: -15px 0;
                text-decoration: none;
                transition: color 0.3s, text-decoration 0.3s;
        }

        a.back-btn i {
            font-size: 16px;
        }

        a.back-btn:hover {
            color: #22c55e;
            text-decoration: underline;
        }

        .hero-content {
            position: relative;
            z-index: 1;
        }
        
        .hero h1 {
            font-size: 3.5em;
            margin-bottom: 10px;
        }

        .hero p {
            font-size: 1.8em;
            margin-bottom: 20px;
        }


        .container {
            width: 70%;
            margin: 0 auto;
            background: white;
            padding: 30px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        .hero h1 {
            font-size: 3.5em;
            margin-bottom: 10px;
        }
        .info-section {
            display: flex;
            align-items: center;
            justify-content: center;
            max-width: 1250px;
            width: 100%;
            padding: 150px;
            padding-top:50px;
            padding-bottom:30px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            background-color:white;
        }
        .info-text{
            flex: 1;
            padding: 20px;
        }

        .info-text h2 {
            font-size: 1.5em;
            margin-bottom: 10px;
            color: green;
        }

        .info-text p {
            font-size: 1.2em;
            color:  #333333;
            line-height: 1.6;
        }
         #shopForm {
            display: none; /* Hide form initially */
            width: 50%; /* width of the form container */
            max-width: 900px; /* Sets a maximum width to prevent it from becoming too wide on larger screens */
            margin: 30px auto;
            background: white;
            padding: 30px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        .success-message {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            padding: 15px 20px;
            margin: 20px auto;
            width: 70%;
            text-align: center;
            border-radius: 5px;
            font-size: 1.1em;
            font-weight: 500;
        }


        /* Toggle Button */
        .btn-toggle {
            background-color:rgb(0, 179, 69);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            margin-bottom: 10px;
            transition: 0.3s;
        }

        .btn-toggle:hover {
            background-color:rgb(0, 255, 98);
        }

        .section {
            margin-bottom: 20px;
        }

        .section label {
            font-size: 1.2em;
            margin-bottom: 5px;
            display: block;
        }

        .section {
        margin-bottom: 20px;
        }

        .section label {
            font-size: 1.2em;
            margin-bottom: 8px;
            display: block;
            font-weight: bold;
        }

        .section input, 
        .section select, 
        .section textarea {
            width: 100%;
            padding: 12px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1em;
        }

        .btn-container {
            text-align: center;
            margin-top: 20px;
        }
        .section:last-child {
        margin-bottom: 40px; /* Increased bottom margin */
        }

        .section .file-upload {
            border: none;
            padding: 0;
        }

        .file-upload input {
            font-size: 1em;
        }

        .btn-container {
            text-align: center;
        }


        .feature-list {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .feature-list input {
            width: auto;
        }

        .feature-list label {
            font-size: 1em;
        }
        .section {
            margin-bottom: 20px;
        }

        .section label {
            font-size: 1em;
            margin-top: 5px;
            display: block;
            font-weight: bold;
        }

        .section input, 
        .section select, 
        .section textarea {
            width: 100%;
            padding: 12px;
            margin-top: 1px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1em;
        }

        /* Add Another Shop Button */
        #addShop {
            background-color: #7c3aed;
            color: white;
            padding: 10px 30px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            font-size: 1em;
            transition: background 0.3s;
        }

        #addShop:hover {
            background-color:rgb(43, 219, 73);
        }

        /* Submit Button */
        .btn-container {
            text-align: center;
            margin-top: 20px;
        }

        .btn-container button {
            background-color: #7c3aed;
            color: white;
            padding: 10px 30px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            font-size: 1em;
            transition: background 0.3s;
        }

        .btn-container button:hover {
            background-color:rgb(43, 219, 73);
        }

        .site-footer {
        background-color: #1f1f1f;
        color: #f0f0f0;
        padding: 40px 20px;
        font-family: 'Arial', sans-serif;
        margin-top:40px;
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
        .hamburger {
        display: none;
        font-size: 1.8em;
        cursor: pointer;
        }

        @media (max-width: 1000px) {
        .navbar {
            flex-direction: row;
            justify-content: space-between;
            padding: 15px 20px;
        }

        .hamburger {
            display: block;
            color: #333;
        }

        .nav-links {
            display: none;
            flex-direction: column;
            width: 100%;
            background: white;
            text-align: center;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            animation: slideDown 0.3s ease forwards;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            z-index: 999;
        }

        .nav-links.active {
            display: flex;
        }

        @keyframes slideDown {
            from {
            opacity: 0;
            transform: translateY(-10%);
            }
            to {
            opacity: 1;
            transform: translateY(0%);
            }
        }
        }
        @media (max-width: 768px) {
            #shopForm {
            display: none; 
            width: 100%;
            max-width: 900px; 
            margin: 30px auto;
            background: white;
            padding: 30px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
            .hero h1 {
                font-size: 2em;
            }
            .hero p {
                font-size: 1.2em;
            }
            .hero button {
                font-size: 0.9em;
            }
            .info-section {
                padding: 20px;
            }

            .info-text {
                font-size: 1rem;
            }

            .info-text h2 {
                font-size: 1.5rem;
            }
        }
        @media (max-width: 480px) {
        .info-text {
            font-size: 0.95rem;
        }

        .info-text h2 {
            font-size: 1.3rem;
        }
        }
    </style>
    
</head>
<body id="home">

    <div class="navbar">
  <div class="logo">SHOP LOCATOR</div>

  <div class="hamburger" onclick="toggleMenu()">
    <i class="fa fa-bars"></i>
  </div>

  <div class="nav-links" id="navLinks">
    <a href="index.php#home"><i class="fa-solid fa-house"></i> Home</a>
    <a href="index.php#about"><i class="fa-solid fa-circle-info"></i> About</a>
    <a href="how-it-works.php"><i class="fa-solid fa-gears"></i> How It Works</a>
    <a href="index.php#contact"><i class="fa-solid fa-envelope"></i> Contact</a>
    <?php
        $redirectTarget = 'login.php?redirect=' . urlencode('owner_dashboard.php');
        if (isset($_SESSION['user_id'])) {
            $redirectTarget = $_SESSION['user_type'] === 'client' ? 'user_dashboard.php' : 'owner_dashboard.php';
        }
        ?>
    <a href="<?php echo $redirectTarget; ?>"><i class="fas fa-user-circle dropdown-icon"></i> Account</a>
  </div>
</div>
    <?php
        if (isset($_SESSION['submission_success'])) {
            echo '
            <div id="success-msg" style="color: green; font-weight: bold;">
                Shop submittion successful!
            </div>
            <script>
                setTimeout(function() {
                    var msg = document.getElementById("success-msg");
                    if (msg) msg.style.display = "none";
                }, 3000); // 3000 ms = 3 seconds
            </script>';
            unset($_SESSION['submission_success']);
        }
    ?>
    <div class="hero">
        <div class="hero-content">
            <h1>Update Your Shop Listing</h1>
            <p>Ensure your shop details are up-to-date for better visibility</p>
        </div>
    </div>
    <div class="info-section">
        <div class="info-text">
            <h2>Update Shop Availability</h2>
            <p>
                With Shop Locator, shop owners have complete control over their listings. 
                Our platform empowers you to update the availability of your shop in real-time, 
                ensuring potential customers always have access to the latest information. 
                This feature minimizes confusion and enhances customer satisfaction, 
                as clients can see if a shop is available for sale or rent. Keeping your shop’s status current 
                not only saves time but also increases your chances of attracting interested clients. 
                Regular updates mean you can respond quickly to market demands and make informed decisions.Whether 
                you're listing a new shop or updating details of your existing shops, this page allows 
                you to manage your listings easily.
            </p>
        </div>
    </div>
    <div class="btn-container">
    <button class="btn-toggle" id="toggleForm">List Your Shops</button>
    <button class="btn-toggle" id="viewListings">View Listings</button>
 </div>
<!-- Shop Form (Initially Hidden) -->
<div class="page-content">
    <div id="shopForm">
        <form action="save_shops.php" method="post" enctype="multipart/form-data">
            <div class="shop-sections">
                <div class="section shop-entry">
                    <label>Shop Name:</label>
                    <input type="text" name="shop_name[0]" required>

                    <label>Location:</label>
                    <input type="text" name="location[0]" placeholder="Enter location e.g Westlands, Nairobi" required>

                    <label>Size (sq ft):</label>
                    <input type="number" name="size[0]" required>

                    <label>Price (Ksh):</label>
                    <input type="number" name="price[0]" required>

                    <label>Status:</label>
                    <select name="status[0]">
                        <option value="Available">Available</option>
                        <option value="Occupied">Occupied</option>
                    </select>

                    <label>Shop Type:</label>
                    <select name="shop_type[0]">
                        <option value="retail">Retail</option>
                        <option value="office">Office</option>
                        <option value="warehouse">Warehouse</option>
                        <option value="other">Other</option>
                    </select>

                    <label>Availability Date:</label>
                    <input type="date" name="availability_date[0]">

                    <!-- Multiple image upload input -->
                    <label>Upload Shop Images (Max 3):</label>
                    <input type="file" name="shop_images[0][]" accept="image/*" multiple required>

                    <label>Additional Notes:</label>
                    <textarea name="additional_notes[0]" rows="4" placeholder="Enter additional information about your Shop "></textarea>
                </div>
            </div>

            <div class="btn-container">
                <button type="button" id="addShop">Add Another Shop</button>
                <button type="submit">Save Changes</button>
            </div>
        </form>
    </div>
</div>
<div class="top-controls">
        <a href="#" class="back-btn" onclick="history.back(); return false;">
            <i class="fas fa-arrow-left"></i> Go Back
        </a>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
  function toggleMenu() {
    const nav = document.getElementById("navLinks");
    nav.classList.toggle("active");
  }

    $(document).ready(function () {
        const isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;

        function showFormAndScroll() {
            $('#shopForm').slideDown("slow", function () {
                $('html, body').animate({
                    scrollTop: $("#shopForm").offset().top - 100
                }, 600);
            });
        }

        // Check if redirected to #shopForm after login
        if (isLoggedIn && window.location.hash === "#shopForm") {
            showFormAndScroll();
        }

        $('#toggleForm').click(function () {
            if (!isLoggedIn) {
                const redirectUrl = encodeURIComponent(window.location.pathname + '#shopForm');
                window.location.href = 'login.php?redirect=' + redirectUrl;
            } else {
                showFormAndScroll();
            }
        });

        $('#viewListings').click(function () {
            if (!isLoggedIn) {
                const redirectUrl = encodeURIComponent('view_listings.php');
                window.location.href = 'login.php?redirect=' + redirectUrl;
            } else {
                window.location.href = 'view_listings.php';
            }
        });

        // Add another shop logic
        let shopIndex = 1;
        $('#addShop').click(function () {
            let newSection = $('.shop-entry:first').clone();

            newSection.find('input, select, textarea').each(function () {
                let name = $(this).attr('name');
                if (name) {
                    let baseName = name.split('[')[0];
                    if (name.includes('shop_images')) {
                        $(this).attr('name', 'shop_images[' + shopIndex + '][]');
                    } else {
                        $(this).attr('name', baseName + '[' + shopIndex + ']');
                    }
                }

                // Clear values
                if ($(this).is(':checkbox') || $(this).is(':radio')) {
                    $(this).prop('checked', false);
                } else {
                    $(this).val('');
                }
            });

            $('.shop-sections').append(newSection);
            shopIndex++;
        });
    });
</script>
</body>
</html>