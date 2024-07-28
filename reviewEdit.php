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
    echo "You are not authorized to edit this review.";
    exit();
}

// If form is submitted for review update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate CSRF token
    $csrfToken = $_POST['csrf_token'];
    if (!validateCsrfToken($csrfToken)) {
        die("Invalid CSRF token.");
    }

    $newReview = $_POST['review'];
    $newRating = $_POST['ratings'];

    // Update review in the database
    $updateQuery = "UPDATE reviews SET review = ?, rating = ? WHERE reviewId = ?";
    $updateStmt = mysqli_prepare($link, $updateQuery);
    mysqli_stmt_bind_param($updateStmt, "sii", $newReview, $newRating, $reviewId);
    $updateResult = mysqli_stmt_execute($updateStmt);

    if ($updateResult) {
        // Fetch updated review details
        $updatedReviewQuery = "SELECT * FROM reviews WHERE reviewId = ?";
        $updatedStmt = mysqli_prepare($link, $updatedReviewQuery);
        mysqli_stmt_bind_param($updatedStmt, "i", $reviewId);
        mysqli_stmt_execute($updatedStmt);
        $updatedResult = mysqli_stmt_get_result($updatedStmt);
        $updatedReview = mysqli_fetch_assoc($updatedResult);
    } else {
        echo "Error updating review: " . mysqli_error($link);
    }
}

mysqli_close($link);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Review</title>
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
        .card {
            border: none;
            border-radius: 15px;
            background-color: #fff;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #007bff;
            color: #fff;
            font-weight: bold;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }
        .card-body {
            padding: 20px;
        }
        .form-control {
            border-radius: 10px;
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
<!-- Navbar Section-->
<?php include 'navbar.php'; ?> <!-- Include the navbar.php file -->

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header" style="color:black;">Edit Review</div>
                <div class="card-body">
                    <?php if (isset($updatedReview)): ?>
                    <!-- Success container to display the updated review -->
                    <div class="alert alert-success" role="alert">
                        <h4><i class="fas fa-check-circle me-2 success-icon"></i>Review Updated Successfully!</h4>
                        <p>Your review has been successfully updated. Here are the details:</p>
                        <hr>
                        <p class="mb-0">Review: <?php echo $updatedReview['review']; ?></p>
                        <p class="mb-0">Ratings: <?php echo $updatedReview['rating']; ?></p>
                        <p class="mb-0">Date Posted: <?php echo $updatedReview['datePosted']; ?></p>
                    </div>
                    <?php endif; ?>
                    <form method="POST" action="reviewEdit.php?id=<?php echo $reviewId; ?>" id="reviewForm">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                        <div class="mb-3">
                            <label for="review" class="form-label">Your Review:</label>
                            <textarea id="review" name="review" class="form-control" rows="5" required><?php echo $row['review']; ?></textarea>
                            <div class="invalid-feedback">Please provide your review.</div>
                        </div>
                        <div class="mb-3">
                            <label for="ratings" class="form-label">Ratings:</label>
                            <div class="star-rating" id="starRating">
                                <?php
                                $rating = $row['rating'];
                                for ($i = 1; $i <= 5; $i++) {
                                    echo '<span class="star" data-rating="' . $i . '"';
                                    if ($i <= $rating) {
                                        echo ' style="color: #ffdd00;"';
                                    }
                                    echo '>&#9733;</span>';
                                }
                                ?>
                            </div>
                            <input type="hidden" id="selectedRating" name="ratings" value="<?php echo $rating; ?>"> <!-- Hidden input to store selected rating -->
                            <div class="invalid-feedback">Please select a rating.</div>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Update Review</button>
                            <a href="review.php?id=<?php echo $row['avId']; ?>" class="btn btn-secondary">Go Back to Review</a>
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