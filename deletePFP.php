<?php
session_start();
include "dbFunctions.php";
include "csrfFunctions.php"; // Include the file containing CSRF functions

// Check if the user is logged in
if (!isset($_SESSION['userId'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Check if the user has completed 2FA verification
if (!isset($_SESSION['2fa_verified']) || !$_SESSION['2fa_verified']) {
    header("Location: 2fa.php");
    exit();
}

// Get the user ID from session
$userId = $_SESSION['userId'];

// Check if the user ID matches the session user ID
if ($userId != $_GET['userId']) {
    header("Location: userPFP.php?userId=$userId"); // Redirect to user PFP page if trying to delete another user's profile picture
    exit();
}

// Initialize message variable
$message = '';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate CSRF token
    $csrfToken = $_POST['csrf_token'];
    if (!validateCsrfToken($csrfToken)) {
        die("Invalid CSRF token.");
    }

    // Check if the user's profile picture exists
    if (isset($_SESSION['profilePicture']) && !empty($_SESSION['profilePicture']) && file_exists($_SESSION['profilePicture'])) {
        // Attempt to delete the profile picture file
        if (unlink($_SESSION['profilePicture'])) {
            // Clear the session variable
            unset($_SESSION['profilePicture']);
            // Redirect to userPFP.php after successful deletion
            header("Location: userPFP.php?userId=$userId");
            exit();
        } else {
            $message = "<div class='alert alert-danger' role='alert'>Failed to delete profile picture.</div>";
        }
    } else {
        $message = "<div class='alert alert-warning' role='alert'>No profile picture found to delete.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Profile Picture</title>
    <!-- Import Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Import Google Fonts (Poppins) -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        /* Custom CSS styles */
        body {
            background-color: #f8f9fa; /* Set background color to light gray */
            font-family: 'Poppins', sans-serif; /* Set font family to Poppins */
        }
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Add box shadow for modern look */
            border-radius: 10px; /* Round the corners */
            width: 100%; /* Adjust width to fit container */
            max-width: 500px; /* Limit maximum width */
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
    </style>
</head>
<body>
<!-- Navbar -->
<?php include 'navbar.php'; ?>

<!-- Main Content Section -->
<div class="container mt-5">
    <div class="card mt-3">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0">Delete Profile Picture</h5>
        </div>
        <div class="card-body">
            <?php if(isset($message)): ?>
                <?php echo $message; ?>
            <?php endif; ?>
            <p>Are you sure you want to delete your profile picture?</p>
            <form method="POST" action="">
                <!-- Add CSRF token as a hidden input field -->
                <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                <button type="submit" class="btn btn-danger">Delete</button>
                <a href="userPFP.php?userId=<?php echo $_SESSION['userId']; ?>" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>

<!-- Import Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>