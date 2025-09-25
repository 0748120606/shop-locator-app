<?php
// Include your database connection
include 'connection.php';

// Corrected SQL to join with the users table to get phone_number
$sql = "SELECT 
            shops.shop_id, 
            shops.shop_name, 
            shops.location, 
            shops.price, 
            shops.additional_notes,
            (SELECT image_path FROM shop_images WHERE shop_id = shops.shop_id LIMIT 1) AS image
        FROM shops
        JOIN users ON shops.user_id = users.user_id
        ORDER BY shops.shop_id DESC";

$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Available Shops for Rent</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 20px;
        }

        .shop-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            margin-bottom: 25px;
            padding: 20px;
            display: flex;
            gap: 20px;
        }

        .shop-card img {
            width: 250px;
            height: 180px;
            object-fit: cover;
            border-radius: 10px;
        }

        .shop-details {
            flex: 1;
        }

        .shop-details h2 {
            margin: 0 0 10px;
            color: green;
        }

        .whatsapp-link {
            display: inline-block;
            margin-top: 12px;
            background: #25D366;
            color: white;
            padding: 10px 16px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
        }

        .whatsapp-link:hover {
            background: #1ebe57;
        }
    </style>
</head>
<body>
    <h1>Available Shops for Rent</h1>

    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="shop-card">
            <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="Shop Image">
            <div class="shop-details">
                <h2><?php echo htmlspecialchars($row['shop_name']); ?></h2>
                <p><strong>Location:</strong> <?php echo htmlspecialchars($row['location']); ?></p>
                <p><strong>Price:</strong> Ksh <?php echo number_format($row['price']); ?></p>
                <p><?php echo nl2br(htmlspecialchars($row['additional_notes'])); ?></p>

                <?php
                    // Format the phone number to international for WhatsApp
                    $phone = preg_replace('/[^0-9]/', '', $row['phone_number']);
                    if (strlen($phone) == 10 && str_starts_with($phone, '0')) {
                        $phone = '254' . substr($phone, 1);
                    }
                ?>
                <a class="whatsapp-link" target="_blank" href="https://wa.me/<?php echo $phone; ?>">
                    Contact via WhatsApp
                </a>
            </div>
        </div>
    <?php endwhile; ?>
</body>
</html>
