<?php
session_start();
include "dbFunctions.php";
include "csrfFunctions.php"; // Include the file containing CSRF functions

// Check if the user is logged in
if (!isset($_SESSION['userId'])) {
    header("Location: login.php");
    exit();
}

// Check if the user has completed 2FA
if (!isset($_SESSION['2fa_verified'])) {
    // Redirect the user to the 2FA verification page
    header("Location: 2fa.php");
    exit();
}

// Check if the review ID is provided in the URL
if (!isset($_GET['id'])) {
    echo "Review ID not provided.";
    exit();
}

// Get the review ID from the URL
$reviewId = $_GET['id'];

// Fetch the review details from the database
$query = "SELECT * FROM reviews WHERE reviewId = ?";
$stmt = mysqli_prepare($link, $query);
mysqli_stmt_bind_param($stmt, "i", $reviewId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result) {
    echo "Error fetching review details: " . mysqli_error($link);
    exit();
}

$row = mysqli_fetch_assoc($result);

// Check if the logged-in user is the author of the review
if ($_SESSION['userId'] != $row['userId']) {
    echo "You are not authorized to delete this review.";
    exit();
}

// If confirmed for deletion
if (isset($_POST['confirm'])) {
    // Validate CSRF token
    $csrfToken = $_POST['csrf_token'];
    if (!validateCsrfToken($csrfToken)) {
        die("Invalid CSRF token.");
    }

    // Delete the review from the database
    $deleteQuery = "DELETE FROM reviews WHERE reviewId = ?";
    $deleteStmt = mysqli_prepare($link, $deleteQuery);
    mysqli_stmt_bind_param($deleteStmt, "i", $reviewId);
    $deleteResult = mysqli_stmt_execute($deleteStmt);

    if (!$deleteResult) {
        echo "Error deleting review: " . mysqli_error($link);
        exit();
    }

    // Redirect back to the antivirus page
    header("Location: review.php?id=" . $row['avId']);
    exit();
}

mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Review</title>
    <!-- Import Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Import Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Import Google Fonts (Poppins) -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        /* Custom CSS styles */
        body {
            background-color: #f0f0f0; /* Set background color to light gray */
            font-family: 'Poppins', sans-serif; /* Set font family to Poppins */
        }
        .container {
            max-width: 500px;
            margin: 0 auto;
            padding-top: 50px;
        }
        .card {
            border: none;
            border-radius: 10px;
            transition: all 0.3s ease;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            cursor: pointer; /* Make the card clickable */
            max-width: 500px;
            margin: 0 auto;
        }
        .card-header {
            background-color: #dc3545;
            color: #fff;
            font-weight: bold;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }
        .card-body {
            padding: 30px;
        }
        .btn-confirm {
            margin-right: 10px;
        }
    </style>
</head>
<body>
<!-- Navbar Section-->
<?php include 'navbar.php'; ?> <!-- Include the navbar.php file -->

<div class="container">
    <div class="card">
        <div class="card-header" style="color:black;">Delete Review</div>
        <div class="card-body">
            <p>Are you sure you want to delete this review?</p>
            <form method="post">
                <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                <button type="submit" class="btn btn-danger btn-confirm" name="confirm">Yes, Delete Review</button>
                <a href="review.php?id=<?php echo $row['avId']; ?>" class="btn btn-primary">Cancel</a>
            </form>
        </div>
    </div>
</div>

<!-- Import Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>