<?php
session_start();
include "dbFunctions.php";

// Function to sanitize input data
function sanitizeInput($data) {
    // Remove whitespace from the beginning and end of string
    $data = trim($data);
    // Convert special characters to HTML entities
    $data = htmlspecialchars($data);
    return $data;
}

// Check if 'id' parameter is set and sanitize it
if(isset($_GET['id'])) {
    $theId = sanitizeInput($_GET['id']);
} else {
    // Handle if 'id' parameter is not set
    header("Location: index.php");
    exit();
}

// Prepare the SQL query to retrieve reviews
$queryReviews = "SELECT R.reviewId as reviewId, R.review as review, R.rating as ratings, R.datePosted as datePosted, A.avName as avName, U.userId as userId, U.name as name, U.username as username
                FROM reviews R
                INNER JOIN antivirus A ON R.avId = A.avId
                INNER JOIN users U ON R.userId = U.userId
                WHERE A.avId = ?";

// Prepare the SQL query to retrieve antivirus details
$queryAntivirus = "SELECT * FROM antivirus WHERE avId = ?";

// Initialize prepared statement for reviews
$stmtReviews = mysqli_prepare($link, $queryReviews);
if (!$stmtReviews) {
    // Handle error in prepared statement for reviews
    die("Error in preparing statement: " . mysqli_error($link));
}

// Bind parameter for reviews statement
mysqli_stmt_bind_param($stmtReviews, "i", $theId);

// Execute the reviews query
if (!mysqli_stmt_execute($stmtReviews)) {
    // Handle error in executing reviews query
    die("Error executing statement: " . mysqli_error($link));
}

// Get the reviews result set
$resultReviews = mysqli_stmt_get_result($stmtReviews);

// Process the reviews result set
$arrReview = [];
while ($row = mysqli_fetch_assoc($resultReviews)) {
    $arrReview[] = $row;
}

// Free the reviews result set
mysqli_free_result($resultReviews);

// Initialize prepared statement for antivirus
$stmtAntivirus = mysqli_prepare($link, $queryAntivirus);
if (!$stmtAntivirus) {
    // Handle error in prepared statement for antivirus
    die("Error in preparing statement: " . mysqli_error($link));
}

// Bind parameter for antivirus statement
mysqli_stmt_bind_param($stmtAntivirus, "i", $theId);

// Execute the antivirus query
if (!mysqli_stmt_execute($stmtAntivirus)) {
    // Handle error in executing antivirus query
    die("Error executing statement: " . mysqli_error($link));
}

// Get the antivirus result set
$resultAntivirus = mysqli_stmt_get_result($stmtAntivirus);

// Get the antivirus name
$avName = "";
if (mysqli_num_rows($resultAntivirus) > 0) {
    $antivirus = mysqli_fetch_assoc($resultAntivirus);
    $avName = $antivirus['avName'];
}

// Free the antivirus result set
mysqli_free_result($resultAntivirus);

// Close the prepared statements
mysqli_stmt_close($stmtReviews);
mysqli_stmt_close($stmtAntivirus);

// Close the connection
mysqli_close($link);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FYP Antivirus Review</title>
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
            max-width: 800px;
            margin: 0 auto;
        }
        .card {
            border: none;
            border-radius: 10px;
            transition: all 0.3s ease;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            cursor: pointer; /* Make the card clickable */
            height: 300px; /* Adjust height as needed */
            width: 100%; /* Set the width to 100% for a wider card */
        }

        .card-body {
            padding: 30px; /* Adjust padding as needed */
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
        }
        .card-title {
            margin-bottom: 10px;
        }
        .card-text {
            margin-bottom: 15px;
            line-height: 1.5; /* Adjust line height for better readability */
        }
        .ratings {
            margin-top: auto;
            margin-bottom: 0;
        }
        .edit-delete-buttons {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
</head>
<body>
<!-- Navbar Section-->
<?php include 'navbar.php'; ?> <!-- Include the navbar.php file -->
  
<!-- Main Content Section -->
<div class="container py-5">
    <?php
    if (isset($arrReview) && count($arrReview) > 0) {
        ?>
        <h2 class="mb-4"><b>Reviews for <?php echo $avName; ?></b></h2>
        <div class="row row-cols-1 row-cols-md-2 g-4">
            <?php foreach ($arrReview as $review): ?>
            <div class="col">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title" style="font-weight:bold;">
                            <?php if (file_exists('pfp/' . $review['userId'] . '.jpg')): ?>
                                <img src="<?php echo 'pfp/' . $review['userId'] . '.jpg'; ?>" alt="Profile Picture" style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; margin-right: 10px;">
                            <?php else: ?>
                                <img src="pfp/avatar.jpg" style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; margin-right: 10px;">
                            <?php endif; ?>
                            <?php echo $review['username']; ?>
                        </h5>
                        <p class="card-text">Review: <?php echo $review['review']; ?></p>
                        <p class="card-text ratings">Ratings:
                            <?php
                            $rating = $review['ratings'];
                            for ($j = 1; $j <= $rating; $j++) {
                                echo '<i class="fas fa-star"></i>';
                            }
                            ?>
                        </p>
                        <p class="card-text">Date Posted: <?php echo $review['datePosted']; ?></p>
                        <div class="d-flex justify-content-end"> <!-- Align buttons to the right -->
                            <?php if (isset($_SESSION['userId']) && $_SESSION['userId'] == $review['userId'] && isset($_SESSION['2fa_verified']) && $_SESSION['2fa_verified'] == true): ?>
                            <div class="edit-delete-buttons">
                                <a href="reviewEdit.php?id=<?php echo $review['reviewId']; ?>" class="btn btn-primary me-2">Edit</a> <!-- Add margin to separate buttons -->
                                <a href="reviewDelete.php?id=<?php echo $review['reviewId']; ?>" class="btn btn-danger">Delete</a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php
    } else {
        echo '<h2 class="text-center mb-5">No reviews found for ' . $avName . '</h2>';
    }
    ?>
    <div class="text-center mt-4">
        <a href="reviewAdd.php?id=<?php echo $theId; ?>" class="btn btn-primary">Add Review</a>
    </div>
</div>

<!-- Import Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
