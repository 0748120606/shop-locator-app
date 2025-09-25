<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop Locator</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
                <!-- Accessibility Sidebar -->
        <div id="accessibility-sidebar" class="accessibility-sidebar hidden">
        <h4>Accessibility Tools</h4>
        <button onclick="increaseText()">🔼 Increase Text</button>
        <button onclick="decreaseText()">🔽 Decrease Text</button>
        <button onclick="toggleGrayscale()">🖤 Grayscale</button>
        <button onclick="toggleHighContrast()">⚫ High Contrast</button>
        <button onclick="toggleNegativeContrast()">🔴 Negative Contrast</button>
        <button onclick="toggleLightBackground()">🌕 Light Background</button>
        <button onclick="toggleUnderline()">🔗 Links Underline</button>
        <button onclick="toggleReadableFont()">🅰️ A Readable Font</button>
        <button onclick="resetAccessibility()">🔁 Reset</button>
        </div>

        <!-- Toggle Button (Top Right) -->
       <button id="toggle-accessibility" onclick="toggleSidebar()"><i class="fa-solid fa-wheelchair"></i></button>
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
                    /* Sidebar Styling */
            .accessibility-sidebar {
            position: fixed;
            top: 100px;
            right: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 12px rgba(0, 0, 0, 0.1);
            padding: 15px;
            width: 230px;
            z-index: 9999;
            transition: all 0.3s ease;
            font-family: 'Montserrat', sans-serif;
            }

            .accessibility-sidebar h4 {
            font-weight: 600;
            margin-bottom: 10px;
            color: #333;
            }

            .accessibility-sidebar button {
            display: block;
            width: 100%;
            margin: 6px 0;
            padding: 10px;
            border: none;
            border-radius: 6px;
            background: #f4f4f4;
            color: #222;
            font-weight: 500;
            cursor: pointer;
            transition: 0.3s;
            }

            .accessibility-sidebar button:hover {
            background: #0d6efd;
            color: #fff;
            }

            /* Toggle Button Styling */
            #toggle-accessibility {
            position: fixed;
            top: 150px;    
            right: 10px;
            background: #0d6efd;
            color: white;
            border: none;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            font-size: 35px;
            cursor: pointer;
            z-index: 10000;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            }
            /* Hidden Class */
            .hidden {
            display: none;
            }

            /* Accessibility Classes */
            .high-contrast {
            filter: contrast(1.5);
            background: #000 !important;
            color: #fff !important;
            }

            .grayscale {
            filter: grayscale(100%);
            }

            .negative-contrast {
            filter: invert(100%);
            }

            .light-background {
            background-color: #fff !important;
            color: #111 !important;
            }

            .underline-links a {
            text-decoration: underline !important;
            }

            .readable-font {
            font-family: Arial, sans-serif !important;
            }

            body.large-text {
            font-size: 1.25em;
            }

            body.small-text {
            font-size: 0.85em;
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
            .hamburger {
            display: none;
            font-size: 28px;
            cursor: pointer;
            }

        /* Responsive Navigation */
        @media (max-width: 915px) {
        .nav-links {
            position: absolute;
            top: 65px;
            right: 20px;
            flex-direction: column;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            display: none;
        }

        .nav-links.show {
            display: flex;
        }

        .hamburger {
            display: block;
        }
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
        .logo {
            font-size: 1.6em;
            font-weight: bold;
            white-space: nowrap;
        }
        .hero h1 {
          font-size: clamp(2rem, 6vw, 3.5rem);
        }
        .hero p {
          font-size: clamp(1rem, 4vw, 1.8rem);
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
        
        .view-services {
        display: inline-block;
        padding: 12px 24px;
        font-size: 1.2em;
        background: #7c3aed;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background 0.3s;
         }

        .view-services:hover {
            background: #7c3aed;
         }

        .about-section {
            display: flex;
            align-items: center;
            justify-content: center;
            max-width: 1350px;
            width: 100%;
            padding: 150px;
            padding-top:50px;
            padding-bottom:80px;
        }
        .about-text {
            flex: 1;
            padding: 20px;

        }
        .about-text h2 {
            color: green;
            font-size:1.2em;
        }
        .about-text p {
            font-size:1.4em;
        }
        .about-text h1 {
            font-size:2em;
            padding-top:10px;
            padding-bottom:10px;
            color:black;
        }
        .about-image {
            flex: 1;
            text-align: center;
        }
        .about-image img {
            width: 70%;
            height: 600px;
            border-radius: none;
        }
        .services {
        text-align: left;
        width: 100%;
        max-width: 1350px;
        margin: auto;
        padding: 170px;
        padding-top:50px;
        padding-bottom:50px;
        background: rgba(200, 200, 200, 0.2);
        }

        .services-container {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
            width: 100%;
            padding:40px 0;
        }
        .small-text{
            font-family:'Montserrat', sans-serif;
            font-weight:bold;
            padding:10px 0;
            font-size:1.2em;
            color:green;
        }
        .large-text{
            font-family:'Montserrat', sans-serif;
            padding:10px 0;
            font-size:2.4em;
            color:black;
        }
        .service-card {
            flex: 1;
            box-sizing:inherit;
            max-width: 30%;
            text-align: left;
            background: white;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .service-card img {
            width: 100%;
            height: 250px;
            display: block;
            margin-bottom: 15px;
        }
        .service-card a {
            font-size: 1.4em;
            font-weight: bold;
            text-decoration: none;
            color: black;
            display: block;
            padding:10px;
        }
        .service-card p {
            font-size: 1.2em;
            margin-top: 5px;
            padding:0 15px;
            padding-bottom:30px;
        }
        .service-card .link-icon {
            font-size: 1.2em;
            transition: color 0.3s ease, transform 0.3s ease;
        }

        .service-card:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        .service-card:hover a {
            color: green;
            transform: translateX(3px);
        }
        .contact {
            width: 50%;
            margin: 50px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        .contact label{
            font-size:18px;
            font-weight:bold;
        }
        .contact button{
            font-size:18px;
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background-color: #7c3aed;
            color: white;
            border: none;
            padding: 8px 18px;
            cursor: pointer;
            border-radius: 5px;
        }
        button:hover {
            background-color: #28a745;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table td {
            padding: 10px;
            border-bottom: 1px solid #ccc;
        }
        a {
            text-decoration: underline;
            color: inherit;
            font-weight:bold;
        }
        a:hover {
            color: green;
        }
        .hours {
            margin: 50px;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            background: rgba(200, 200, 200, 0.2);
        }
        .wrapper {
            width: 100%;
            max-width: 1350px;
            margin: auto;
            padding:150px;
            padding-left:85px;
            padding-bottom:50px;
            padding-top:0;
            padding-right:120px;
            display: flex;
            justify-content: space-between;
        
        }
        .text-container {
            padding-top:50px;
            width: 100%;
            text-align: left;
            padding-left:170px;
        }
        .checkbox-container {
            display: flex;
            align-items: center;
            margin-top: 12px;
        }
        .checkbox-container input {
            width: 20px;
            height: 20px;
            margin-right: 10px;
        }
        .bottom-left-message {
            position: fixed;
            bottom: 20px;
            left: 20px;
            background-color: #4CAF50;
            color: white;
            padding: 15px 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
            font-family: Arial, sans-serif;
            z-index: 9999;
            animation: fadeOut 5s forwards;
        }
        .close-btn {
            margin-left: auto;
            font-size: 20px;
            font-weight: bold;
            cursor: pointer;
            color: white;
            background: none;
            border: none;
        }

        @keyframes fadeOut {
            0% { opacity: 1; }
            80% { opacity: 1; }
            100% { opacity: 0; display: none; }
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

        
        @media (max-width: 768px) {
            .navbar {
                flex-direction: row;
                text-align: center;
                padding: 20px 15px;
            }
            .nav-links {
                flex-direction: column;
                gap: 10px;
            }
            .hero h1 {
                font-size: 2em;
            }
            .hero p {
                font-size: 1em;
            }
            .hero button {
                font-size: 0.9em;
            }
        }
        @media (max-width: 768px) {
            body {
                font-size: 14px;
                padding: 0 10px;
            }

            .container {
                margin: 20px auto;
                padding: 10px;
            }
        }
        @media (max-width: 1040px) {
            .about-section {
                flex-direction: column;
                padding: 40px 20px;
            }

            .about-image img {
                width: 50%;
                height: 70%;
            }

            .about-text, .about-image {
                width: 100%;
                padding: 10px 0;
            }

            .about-text h1 {
                font-size: 1.6em;
            }

            .about-text p {
                font-size: 1.1em;
            }
            }@media (max-width: 768px) {
                .services-container {
                    flex-direction: column;
                    padding: 20px 10px;
                }
                .service-card {
                    max-width: 80%;
                    margin-bottom: 20px;
                }
            }
            @media (max-width: 1040px) {
                .services {
                    padding: 20px 20px;
                }
            }
            @media (max-width: 768px) {
                .wrapper {
                    flex-direction: column;
                    padding: 20px;
                }
                .text-container {
                    padding-left: 0;
                    text-align: center;
                    padding-top: 20px;
                }
                .contact {
                    width: 100%;
                    margin: 20px auto;
                }
            }
    </style>
</head>
<body id="home">
    <div class="navbar">
        <div class="logo">SHOP LOCATOR</div>
        <div class="hamburger" onclick="toggleMenu()">☰</div>
        <div class="nav-links">
        <a href="#home"><i class="fa-solid fa-house"></i> Home</a>
        <a href="#about"><i class="fa-solid fa-circle-info"></i> About</a>
        <a href="#services"><i class="fa-solid fa-shop"></i> Services</a>
        <a href="howitworks.php"><i class="fa-solid fa-gears"></i> How It Works</a>
        <a href="#contact"><i class="fa-solid fa-envelope"></i> Contact</a>
        </div>
    </div>
    <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
    <div class="message-box success-message">
        <span class="close-btn" onclick="this.parentElement.style.display='none';">&times;</span>
        ✅ Your message was submitted successfully!
        </div>
    <?php elseif (isset($_GET['error'])): ?>
        <div class="message-box error-message">
            <span class="close-btn" onclick="this.parentElement.style.display='none';">&times;</span>
            ❌ Something went wrong. Please try again.
        </div>
    <?php endif; ?>

    <div class="hero">
        <div class="hero-content">
            <h1>Find Your Perfect Shop</h1>
            <p>Connecting buyers and sellers effortlessly</p>
            <a href="#services" class="view-services">View Services</a>
        </div>
    </div>
    <div id="about" class="about-section">
        <div class="about-text">
            <h2>Connecting Shops & Customers<h2>
            <h1>Your ideal shop awaits you.</h1>
            <p>Shop Locator is your go-to application for bridging the gap between shop owners and potential customers. We streamline the process of buying and renting shops, saving you both time and money. Shop owners can easily update their shop availability, while customers can effortlessly search for shops based on location, size, and pricing. Join us in revolutionizing the way shops connect with their ideal patrons!</p>
            <br><button>Get in Touch</button>
        </div>
        <div class="about-image">
            <img src="images/stores2.WEBP" alt="Shops and Customers">
        </div>
    </div>
    
    <div class="services" id="services">
    <p class="small-text">Find your perfect shop</p>
    <h2 class="large-text">Connecting buyers and sellers effortlessly.</h2>

    <div class="services-container">
        <div class="service-card">
            <img src="images/shop-connect3.WEBP" alt="Shop connect">
            <a href="shop_connection.php">Shop Rental Connection<span class="link-icon">→</span></a>
            <p>Easily find shops available for rent in your area</p>
        </div>

        <div class="service-card">
            <img src="images/shop-listing2.WEBP" alt="Shop listing">
            <a href="listing_form.php">Shop listing updates<span class="link-icon">→</span></a>
            <p>Keep your shop listings current and visible to the customers</p>
        </div>

        <div class="service-card">
            <img src="images/shop-search2.WEBP" alt="Shop search">
            <a href="location_based_search.php">Location-Based shop search<span class="link-icon">→</span></a>
            <p>Find shops based on your desired location and criteria</p>
        </div>
    </div>
</div>
<div class="text-container"  id="contact">
<h2 class="small-text">Get in touch</h2>
<p class="large-text">We're here to assist you!</p>
</div>
<div class="wrapper">
<div class="contact">
    <form action="submit_contact.php" method="POST">
        <label for="name">Name *</label>
        <input type="text" id="name" name="name" placeholder="Moses Paul" required>

        <label for="email">Email address *</label>
        <input type="email" id="email" name="email" placeholder="email@website.com" required>

        <label for="phone">Phone number *</label>
        <input type="tel" id="phone" name="phone_number" placeholder="555-555-5555" required>

        <label for="message">Message</label>
        <textarea id="message" name="message" rows="4"></textarea>

        <div class="checkbox-container">
            <input type="checkbox" id="consent" name="consent" required>
            <label for="consent">I allow this website to store my submission so they can respond to my inquiry. *</label>
        </div>
        
        <button type="submit">Submit</button>
    </form>

    </div>

    <div class="container hours">
        <h2>Get in touch</h2><br>
        <p>Email: <a href="mailto:maingimoses20@gmail.com" style="text-decoration: underline;">maingimoses20@gmail.com</a></p><br>
        <h3>Hours</h3>
        <table align="center">
            <tr><td>Monday</td><td>9:00am – 10:00pm</td></tr>
            <tr><td>Tuesday</td><td>9:00am – 10:00pm</td></tr>
            <tr><td>Wednesday</td><td>9:00am – 10:00pm</td></tr>
            <tr><td>Thursday</td><td>9:00am – 10:00pm</td></tr>
            <tr><td>Friday</td><td>9:00am – 10:00pm</td></tr>
            <tr><td>Saturday</td><td>9:00am – 6:00pm</td></tr>
            <tr><td>Sunday</td><td>9:00am – 12:00pm</td></tr>
        </table>
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
</body>
<script>
    function toggleMenu() {
    const navLinks = document.querySelector('.nav-links');
    navLinks.classList.toggle('show');
  }
    //accessibility
            function toggleSidebar() {
            const sidebar = document.getElementById("accessibility-sidebar");
            sidebar.classList.toggle("hidden");
        }

        function increaseText() {
            document.body.classList.remove("small-text");
            document.body.classList.add("large-text");
        }

        function decreaseText() {
            document.body.classList.remove("large-text");
            document.body.classList.add("small-text");
        }

        function toggleGrayscale() {
            document.body.classList.toggle("grayscale");
        }

        function toggleHighContrast() {
            document.body.classList.toggle("high-contrast");
        }

        function toggleNegativeContrast() {
            document.body.classList.toggle("negative-contrast");
        }

        function toggleLightBackground() {
            document.body.classList.toggle("light-background");
        }

        function toggleUnderline() {
            document.body.classList.toggle("underline-links");
        }

        function toggleReadableFont() {
            document.body.classList.toggle("readable-font");
        }

        function resetAccessibility() {
            document.body.className = '';
        }
    document.querySelectorAll('.nav-links a').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        e.preventDefault(); // Prevent instant jump

        const targetId = this.getAttribute('href').substring(1); // Remove #
        const targetSection = document.getElementById(targetId);

        if (targetSection) {
            let targetPosition = targetSection.offsetTop - 50; // Adjust for navbar height
            let startPosition = window.pageYOffset;
            let distance = targetPosition - startPosition;
            let duration = 3000; // Adjust duration 
            let startTime = null;

            function animation(currentTime) {
                if (startTime === null) startTime = currentTime;
                let timeElapsed = currentTime - startTime;
                let run = ease(timeElapsed, startPosition, distance, duration);
                window.scrollTo(0, run);
                if (timeElapsed < duration) requestAnimationFrame(animation);
            }

            function ease(t, b, c, d) {
                t /= d / 2;
                if (t < 1) return c / 2 * t * t + b;
                t--;
                return -c / 2 * (t * (t - 2) - 1) + b;
            }

            requestAnimationFrame(animation);
        }
    });
});

</script>

</html>
