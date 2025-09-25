<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>How It Works - Shop Locator</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Montserrat', sans-serif;
      background-color: #f8f9fa;
      color: #333;
      line-height: 1.6;
    }

    .container {
      max-width: 1100px;
      margin: 0 auto;
      padding: 20px;
    }

    /* Navbar Styles */
    .navbar {
      background-color: #ffffff;
      padding: 15px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 2px 6px rgba(0,0,0,0.08);
      position: sticky;
      top: 0;
      z-index: 1000;
    }

    .navbar .logo {
      font-weight: 600;
      font-size: 24px;
      color: #2e7d32;
    }

    .nav-links {
      display: flex;
      gap: 20px;
    }

    .nav-links a {
      text-decoration: none;
      color: #333;
      font-weight: 500;
      transition: color 0.3s ease;
    }

    .nav-links a:hover {
      color: #2e7d32;
    }

    .hamburger {
      display: none;
      font-size: 28px;
      cursor: pointer;
    }

    /* Responsive Navigation */
    @media (max-width: 768px) {
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

    /* Section Styles */
    .how-it-works {
      padding: 60px 20px 40px;
    }

    .how-it-works h2 {
      font-size: 36px;
      text-align: center;
      color: #2e7d32;
      margin-bottom: 40px;
    }

    .features-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
      gap: 20px;
    }

    .feature {
      background: #fff;
      padding: 25px 20px;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.05);
      transition: transform 0.3s ease;
    }

    .feature:hover {
      transform: translateY(-5px);
    }

    .feature h3 {
      color: #2e7d32;
      font-size: 20px;
      margin-bottom: 10px;
    }

    .feature p {
      color: #555;
      font-size: 15px;
    }

    footer {
      background: #e9ecef;
      text-align: center;
      padding: 20px;
      font-size: 14px;
      color: #666;
      margin-top: 60px;
    }
  </style>
</head>
<body>

  <!-- Navbar -->
  <div class="navbar">
    <div class="logo">Shop Locator</div>
    <div class="hamburger" onclick="toggleMenu()">☰</div>
    <div class="nav-links" id="navLinks">
      <a href="index.php">Home</a>
      <a href="view_shops.php">Browse Shops</a>
      <a href="how_it_works.php">How It Works</a>
      <a href="contact.php">Contact</a>
    </div>
  </div>

  <!-- Main Content -->
  <section class="how-it-works container">
    <h2>How It Works</h2>
    <div class="features-grid">
      <div class="feature">
        <h3>Location-Based Search</h3>
        <p>Find shops by specific locations and areas to match your business needs.</p>
      </div>
      <div class="feature">
        <h3>Size Filtering</h3>
        <p>Filter shops by size to ensure the space fits your business requirements.</p>
      </div>
      <div class="feature">
        <h3>Price Range Options</h3>
        <p>Set your budget and find shops within your price range.</p>
      </div>
      <div class="feature">
        <h3>Availability Status</h3>
        <p>See which shops are currently available or occupied at a glance.</p>
      </div>
      <div class="feature">
        <h3>Detailed Information</h3>
        <p>View comprehensive details, photos, and amenities for each shop.</p>
      </div>
      <div class="feature">
        <h3>Favorites & Ratings</h3>
        <p>Save favorites and see ratings from other users to make informed decisions.</p>
      </div>
      <div class="feature">
        <h3>Availability Alerts</h3>
        <p>Get notified when a shop matching your criteria becomes available.</p>
      </div>
      <div class="feature">
        <h3>Direct Contact</h3>
        <p>Connect directly with shop owners through WhatsApp for quick communication.</p>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer>
    <p>&copy; <?php echo date('Y'); ?> Shop Locator. All rights reserved.</p>
  </footer>

  <!-- Script -->
  <script>
    function toggleMenu() {
      document.getElementById('navLinks').classList.toggle('show');
    }
  </script>

</body>
</html>
