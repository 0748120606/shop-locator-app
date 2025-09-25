<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            display: flex;
            flex-direction: column;
            align-items: center;
            color: #333333;
        }
        html{
        scroll-behavior: smooth;
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
        background:rgb(5, 126, 25);
        color: white;
        text-decoration: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background 0.3s;
         }

        .view-services:hover {
            background:rgb(0, 230, 96);
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
            background-color:rgb(8, 100, 206);
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
    
        .footer {
            background: #333;
            color: white;
            text-align: center;
            padding: 15px;
            width: 100%;
        }
        
        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                text-align: center;
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
    </style>
</head>
<body id="home">
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