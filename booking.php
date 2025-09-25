<?php
session_start();
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        die("You must be logged in to book.");
    }

    $user_id = $_SESSION['user_id'];
    $meeting_date = $_POST['meeting_date'] ?? '';
    $meeting_time = $_POST['meeting_time'] ?? '';

    // Sanitize input
    $meeting_date = mysqli_real_escape_string($conn, $meeting_date);
    $meeting_time = mysqli_real_escape_string($conn, $meeting_time);

    // Check if the user already has a pending booking
    $check_sql = "SELECT COUNT(*) FROM bookings WHERE user_id = ? AND status = 'pending'";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($pending_count);
    $stmt->fetch();
    $stmt->close();

    if ($pending_count > 0) {
        $_SESSION['booking_error'] = "You already have a pending booking. Please wait for it to be approved or declined.";
        header("Location: owner_dashboard.php");
        exit();
    }

    if (!empty($meeting_date) && !empty($meeting_time)) {
        $sql = "INSERT INTO bookings (user_id, meeting_date, meeting_time, status)
                VALUES (?, ?, ?, 'pending')";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $user_id, $meeting_date, $meeting_time);

        if ($stmt->execute()) {
            $_SESSION['booking_success'] = "Booking successful! You will get a notification once the meeting has been approved.";
        } else {
            $_SESSION['booking_error'] = "Error: " . $stmt->error;
        }

        $stmt->close();
        header("Location: owner_dashboard.php");
        exit();
    } else {
        echo "<script>alert('Please select date and time');</script>";
    }
}
    mysqli_close($conn);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <title>Shop Locator</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family:'Montserrat', sans-serif;
            background: #f9f9f9;
            margin: 0;
            padding: 0;
        }    
        /* NAVBAR STYLES */
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
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
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
        .logo {
            font-size: 1.6em;
            font-weight: bold;
        }
        .top-controls {
            display: flex;
            justify-content: flex-start;
            padding: 10px 20px;
            margin-bottom: -50px; 
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
            margin: 10px 0;
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
        .hero {
            text-align: center;
            padding: 100px 20px;
            background: #f4f4f4;
            width: 100%;
        }
        .hero h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }
        .hero p {
            font-size: 1.2em;
            margin-bottom: 20px;
        }
        .hero .view-services {
            padding: 12px 24px;
            font-size: 1.2em;
            background:rgb(42, 223, 51);
            color: white;
            border: none;
            cursor: pointer;
            transition: background 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        .hero .view-services:hover {
            background:rgb(0, 230, 38);
        }
        
        /* Meeting Request Form */
        .meeting-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .meeting-container h2 {
            font-size: 1.8em;
            margin-bottom: 10px;
        }
        .calendar-container {
            margin: 20px 0;
        }
        .calendar {
            width: 100%;
            border: none;
        }
        .time-slots {
            display: none;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            margin: 20px 0;
        }
        .time-slot {
            padding: 10px 15px;
            background: #f4f4f4;
            border: 1px solid #ccc;
            cursor: pointer;
            transition: background 0.3s;
        }
        .time-slot:hover, .time-slot.selected {
            background:rgb(19, 16, 15);
            color: white;
        }
        .submit-btn {
            display: none;
            padding: 12px 24px;
            font-size: 1.2em;
            background:rgb(12, 12, 12);
            color: white;
            border: none;
            cursor: pointer;
            transition: background 0.3s;
        }
        .submit-btn:hover {
            background:rgb(13, 170, 73);
        }
        .footer {
            background: #333;
            color: white;
            text-align: center;
            padding: 20px;
            width: 100%;
            margin-top: 40px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <div class="logo">SHOP LOCATOR</div>
        <div class="nav-links">
        <a href="notifications.php"><i class="fas fa-bell"></i>Notifications</a>
        <a href="account_settings.php"><i class="fas fa-user-cog"></i> Account Settings</a>
        <a href="owner_dashboard.php"><i class="fas fa-user-circle dropdown-icon"></i> Account</a>
        <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>
    </div>
    <div class="top-controls">
        <a href="#" class="back-btn" onclick="history.back(); return false;">
            <i class="fas fa-arrow-left"></i> Go Back
        </a>
    </div>
    <div class="meeting-container">
    <h2>Meeting Request Form</h2>
    <p>We look forward to meeting with you</p>
    
    <form method="POST" action="">
        <div class="calendar-container">
            <input type="date" class="calendar" id="meeting-date" name="meeting_date" required>
        </div>

        <div class="time-slots" id="time-slots">
            <input type="hidden" id="selected-time" name="meeting_time">
            <button type="button" class="time-slot">1:00 – 1:30 PM</button>
            <button type="button" class="time-slot">1:30 – 2:00 PM</button>
            <button type="button" class="time-slot">2:00 – 2:30 PM</button>
            <button type="button" class="time-slot">2:30 – 3:00 PM</button>
            <button type="button" class="time-slot">3:00 – 3:30 PM</button>
            <button type="button" class="time-slot">3:30 – 4:00 PM</button>
            <button type="button" class="time-slot">4:00 – 4:30 PM</button>
            <button type="button" class="time-slot">4:30 – 5:00 PM</button>
        </div>

        <button type="submit" class="submit-btn" id="submit-btn">Submit</button>
    </form>
    </div>
    <div class="footer">
    <p>&copy; <?= date("Y") ?> Shop Locator. All Rights Reserved.</p>
</div>
    <script>
    const dateInput = document.getElementById('meeting-date');
    const timeSlots = document.querySelectorAll('.time-slot');
    const timeSlotContainer = document.querySelector('.time-slots');
    const submitBtn = document.getElementById('submit-btn');
    const hiddenTimeInput = document.getElementById('selected-time');

    dateInput.addEventListener('change', function () {
        timeSlotContainer.style.display = 'flex';
        submitBtn.style.display = 'block';
    });

    timeSlots.forEach(slot => {
        slot.addEventListener('click', function () {
            timeSlots.forEach(btn => btn.classList.remove('selected'));
            this.classList.add('selected');
            hiddenTimeInput.value = this.innerText;
        });
    });
</script>

</body>
</html>
