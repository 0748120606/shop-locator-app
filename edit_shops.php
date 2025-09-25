<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop Locator - View & Edit Shops</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Add your existing styles */
        .shop-listing {
            width: 100%;
            max-width: 1200px;
            margin: auto;
            padding: 20px;
            background-color: #f9f9f9;
        }
        .shop-listing table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .shop-listing th, .shop-listing td {
            padding: 15px;
            border: 1px solid #ddd;
            text-align: center;
        }
        .shop-listing th {
            background-color: #f4f4f4;
        }
        .shop-listing .btn-edit, .shop-listing .btn-delete {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            margin: 5px;
        }
        .shop-listing .btn-delete {
            background-color: #f44336;
        }
        .shop-listing .btn-edit:hover, .shop-listing .btn-delete:hover {
            opacity: 0.8;
        }
        .shop-listing h2 {
            font-size: 2em;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="logo">SHOP LOCATOR</div>
        <div class="nav-links">
            <a href="#home">Home</a>
            <a href="#about">About</a>
            <a href="#services">Services</a>
            <a href="how-it-works.php">How It Works</a>
            <a href="#contact">Contact</a>
        </div>
    </div>

    <div class="shop-listing">
        <h2>Your Listed Shops</h2>
        <table>
            <thead>
                <tr>
                    <th>Shop Name</th>
                    <th>Location</th>
                    <th>Size</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Example row of listed shop -->
                <tr>
                    <td>Shop 1</td>
                    <td>Location A</td>
                    <td>500 sq ft</td>
                    <td>$2000/month</td>
                    <td>Available</td>
                    <td>
                        <a href="edit-shop.php?shop_id=1" class="btn-edit">Edit</a>
                        <button class="btn-delete" onclick="deleteShop(1)">Delete</button>
                    </td>
                </tr>
                <tr>
                    <td>Shop 2</td>
                    <td>Location B</td>
                    <td>300 sq ft</td>
                    <td>$1500/month</td>
                    <td>Occupied</td>
                    <td>
                        <a href="edit-shop.php?shop_id=2" class="btn-edit">Edit</a>
                        <button class="btn-delete" onclick="deleteShop(2)">Delete</button>
                    </td>
                </tr>
                <!-- More rows can be dynamically added here -->
            </tbody>
        </table>
    </div>

    <script>
        // JavaScript to handle the deletion of shops
        function deleteShop(shopId) {
            if (confirm('Are you sure you want to delete this shop?')) {
                // Send a request to the server to delete the shop (this can be a form submission or API request)
                alert('Shop ' + shopId + ' deleted!');
            }
        }
    </script>
</body>
</html>
