<?php
session_start();
include "dbFunctions.php";
include "csrfFunctions.php"; // Include the file containing CSRF functions

// Check if the user is logged in
if (!isset($_SESSION['userId'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Check if the user has completed 2FA
if (!isset($_SESSION['2fa_verified'])) {
    // Redirect the user to the 2FA verification page
    header("Location: 2fa.php");
    exit;
}

// Sanitize the input to prevent SQL injection
$theId = isset($_GET['id']) ? mysqli_real_escape_string($link, $_GET['id']) : '';

// Validate the ID parameter
if (!is_numeric($theId) || $theId <= 0) {
    // Invalid ID, handle error or redirect
    header("Location: index.php");
    exit();
}

// Initialize variables for potential success message
$successMessage = "";

// If form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate CSRF token
    $csrfToken = $_POST['csrf_token'];
    if (!validateCsrfToken($csrfToken)) {
        die("Invalid CSRF token.");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Review</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        /* Custom CSS styles */
        body {
            background-color: #f0f0f0; /* Set background color to light gray */
            font-family: 'Poppins', sans-serif; /* Set font family to Poppins */
        }
        .star-rating {
            font-size: 2rem;
            color: #ccc;
            cursor: pointer;
            transition: color 0.2s ease-in-out;
        }
        .star-rating:hover,
        .star-rating:hover ~ .star-rating {
            color: #ffdd00;
        }
        .container {
            padding-top: 20px;
            max-width: 1100px;
        }
        .card-header {
            background-color: #007bff;
            color: #fff;
            font-weight: bold;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }
        .success-container {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
            padding: 10px 20px; /* Adjust padding as needed */
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .success-icon {
            font-size: 24px;
            margin-right: 10px;
        }
    </style>
</head>
<body>
<!-- Navbar Section-->
<?php include 'navbar.php'; ?> <!-- Include the navbar.php file -->

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header" style="font-weight:bold; color:black;">Add Review</div>
                <div class="card-body">
                    <?php if ($successMessage): ?>
                        <!-- Success container to display the success message -->
                        <div class="success-container">
                            <i class="fas fa-check-circle me-2 success-icon"></i><?php echo $successMessage; ?>
                        </div>
                    <?php endif; ?>
                    <form method="POST" action="doReviewAdd.php?id=<?php echo $theId; ?>" id="reviewForm">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                        <div class="mb-3">
                            <label for="username" class="form-label">Your Username:</label>
                            <input type="text" id="username" name="username" class="form-control" value="<?php echo isset($_SESSION['username']) ? $_SESSION['username'] : ''; ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="review" class="form-label">Your Review:</label>
                            <textarea id="review" name="review" class="form-control" rows="5" required></textarea>
                            <div class="invalid-feedback">Please provide your review.</div>
                        </div>
                        <div class="mb-3">
                            <label for="ratings" class="form-label">Ratings:</label>
                            <div class="star-rating" id="starRating">
                                <span class="star" data-rating="1">&#9733;</span>
                                <span class="star" data-rating="2">&#9733;</span>
                                <span class="star" data-rating="3">&#9733;</span>
                                <span class="star" data-rating="4">&#9733;</span>
                                <span class="star" data-rating="5">&#9733;</span>
                            </div>
                            <input type="hidden" id="selectedRating" name="ratings" value="5"> <!-- Hidden input to store selected rating -->
                            <div class="invalid-feedback">Please select a rating.</div>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Submit Review</button>
                            <a href="review.php?id=<?php echo $theId; ?>" class="btn btn-secondary">Go Back to Review</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Import Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const stars = document.querySelectorAll('.star-rating .star');

    stars.forEach(star => {
        star.addEventListener('click', () => {
            const rating = parseInt(star.getAttribute('data-rating'));
            document.getElementById('selectedRating').value = rating;

            // Reset all stars color
            stars.forEach(s => {
                s.style.color = "#ccc";
            });

            // Highlight selected stars
            for (let i = 0; i < rating; i++) {
                stars[i].style.color = "#ffdd00";
            }
        });
    });
</script>
</body>
</html>