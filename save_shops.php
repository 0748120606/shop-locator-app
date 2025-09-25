<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Error: User not logged in.");
}

$_SESSION['submission_success'] = true;

$user_id = $_SESSION['user_id'];

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "shop_locator";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Nominatim geocoding function
function geocode_location($location) {
    $url = "https://nominatim.openstreetmap.org/search?format=json&q=" . urlencode($location);

    $opts = [
        "http" => [
            "header" => "User-Agent: ShopLocator/1.0\r\n"
        ]
    ];
    $context = stream_context_create($opts);
    $response = file_get_contents($url, false, $context);

    if ($response) {
        $data = json_decode($response, true);
        if (isset($data[0])) {
            return [
                'lat' => (float)$data[0]['lat'],
                'lon' => (float)$data[0]['lon']
            ];
        }
    }
    return ['lat' => null, 'lon' => null];
}

$_SESSION['success'] = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $shop_names = $_POST['shop_name'];
    $locations = $_POST['location'];
    $sizes = $_POST['size'];
    $prices = $_POST['price'];
    $statuses = $_POST['status'];
    $shop_types = $_POST['shop_type'];
    $availability_dates = $_POST['availability_date'];
    $additional_notes = isset($_POST['additional_notes']) ? $_POST['additional_notes'] : [];

    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $sql = "INSERT INTO shops (user_id, shop_name, location, latitude, longitude, size, price, status, shop_type, availability_date, additional_notes, approved) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NULL)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Error preparing SQL statement: " . $conn->error);
    }

    foreach ($shop_names as $index => $shop_name) {
        $location = $locations[$index];
        $size = $sizes[$index];
        $price = $prices[$index];
        $status = $statuses[$index];
        $shop_type = $shop_types[$index];
        $availability_date = $availability_dates[$index];
        $note = isset($additional_notes[$index]) ? $additional_notes[$index] : '';

        // Geocode the location
        $coords = geocode_location($location);
        $latitude = $coords['lat'];
        $longitude = $coords['lon'];

        $stmt->bind_param(
            "issddisssss",
            $user_id,
            $shop_name,
            $location,
            $latitude,
            $longitude,
            $size,
            $price,
            $status,
            $shop_type,
            $availability_date,
            $note
        );

        if ($stmt->execute()) {
            $_SESSION['success'][] = "Shop '$shop_name' added successfully.";
            $shop_id = $conn->insert_id;

            // ✅ Call the Python AI script to evaluate the shop
            $python = "C:\\Users\\user\\AppData\\Local\\Programs\\Python\\Python313\\python.exe";
            $script = "evaluate_shop.py";
            $command = "$python $script $shop_id";
            shell_exec($command . " > nul 2>&1"); // Silently execute, suppressing output

            // Or for debugging, use:
            // $output = shell_exec($command . " 2>&1");
            // echo "<pre>$output</pre>";

            // Handle multiple images for this shop (max 3)
            if (isset($_FILES['shop_images']['name'][$index])) {
                $imageNames = $_FILES['shop_images']['name'][$index];
                $imageTmpNames = $_FILES['shop_images']['tmp_name'][$index];
                $imageErrors = $_FILES['shop_images']['error'][$index];

                $maxImages = 3;
                $uploadedCount = 0;

                foreach ($imageNames as $i => $imageName) {
                    if ($uploadedCount >= $maxImages) {
                        break;
                    }

                    if ($imageErrors[$i] === 0 && !empty($imageTmpNames[$i])) {
                        $ext = pathinfo($imageName, PATHINFO_EXTENSION);
                        $uniqueName = uniqid('', true) . '.' . $ext;
                        $path = $upload_dir . $uniqueName;

                        if (move_uploaded_file($imageTmpNames[$i], $path)) {
                            $sql_img = "INSERT INTO shop_images (shop_id, image_path) VALUES (?, ?)";
                            $stmt_img = $conn->prepare($sql_img);
                            if ($stmt_img) {
                                $stmt_img->bind_param("is", $shop_id, $path);
                                $stmt_img->execute();
                                $stmt_img->close();
                            }
                            $uploadedCount++;
                        }
                    }
                }
            }
        } else {
            $_SESSION['success'][] = "Error adding shop '$shop_name': " . $stmt->error;
        }
    }

    $stmt->close();
    $conn->close();

    header("Location: listing_form.php");
    exit;
}
?>
