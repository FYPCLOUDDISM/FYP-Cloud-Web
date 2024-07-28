<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - FYP Antivirus Review</title>
    <!-- Import Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Import Google Fonts (Poppins) -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
    <!-- Import Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Custom CSS styles */
        body {
            background-color: #f0f0f0; /* Set background color to light gray */
            font-family: 'Poppins', sans-serif; /* Set font family to Poppins */
        }
        .container {           
            padding-top: 20px;
            padding-bottom: 20px;
        }
        .email-container {
            background-color: #f8f9fa; /* Set container background color to match the navbar */
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 600px; /* Limit container width */
            margin: 0 auto; /* Center container horizontally */
        }
        .email-link {
            color: #007bff; /* Set email link color to blue */
            font-size: 20px;
            font-weight: bold;
            text-decoration: none;
        }
        .email-link:hover {
            color: #0056b3; /* Set hover color to darker blue */
        }
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<!-- Navbar Section-->
<?php include 'navbar.php'; ?> <!-- Include the navbar.php file -->

<!-- Main Content Section -->
<div class="container mt-4 d-flex justify-content-center"> <!-- Added d-flex justify-content-center -->
    <div class="email-container text-center" style="max-width: 600px;">
        <i class="far fa-smile box-circle-solid mt-3 mb-3" style="font-size: 40px; color: #007bff;"></i>
        <h2 class="mb-3" style="font-size: 24px; font-weight: bold;">Contact Us</h2>
        <p style="font-size: 16px;">If you have any inquiries, feel free to email us at:</p>
        <a href="mailto:fypclouddism24@gmail.com" class="email-link">fypclouddism24@gmail.com</a>
    </div>
</div>

<!-- Import Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js"></script>
<!-- Import Font Awesome JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
</body>
</html>