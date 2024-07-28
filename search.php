<?php
// Start or resume the session
session_start();

// Include database functions file
include "dbFunctions.php";

// Function to sanitize input data
function sanitizeInput($data) {
    // Remove whitespace from the beginning and end of string
    $data = trim($data);
    // Convert special characters to HTML entities
    $data = htmlspecialchars($data);
    return $data;
}

// Check if the search query parameter is set and not empty
if(isset($_GET['search']) && !empty($_GET['search'])) {
    // Get the search query and sanitize it
    $searchQuery = sanitizeInput($_GET['search']);

    // Create SQL query to search for antivirus data based on the sanitized search query
    $query = "SELECT * FROM antivirus WHERE avName LIKE ?";

    // Initialize a prepared statement
    $stmt = mysqli_prepare($link, $query);

    // Bind the search query parameter to the prepared statement
    mysqli_stmt_bind_param($stmt, "s", $param);

    // Set the parameter
    $param = "%" . $searchQuery . "%";

    // Execute the prepared statement
    mysqli_stmt_execute($stmt);

    // Get the result
    $result = mysqli_stmt_get_result($stmt);

    // Process the result and store it in an array
    while ($row = mysqli_fetch_assoc($result)){
        $searchResults [] = $row;
    }

    // Close the prepared statement
    mysqli_stmt_close($stmt);

    // Close database connection
    mysqli_close($link);
} else {
    // If search query parameter is not set or empty, redirect back to the homepage
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - FYP Antivirus Review</title>
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
        .card {
            border: none;
            border-radius: 10px;
            transition: all 0.3s ease;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            height: 100%;
            cursor: pointer; /* Make the card clickable */
        }
        .card img {
            height: 200px; /* Adjust the height as needed */
            object-fit: cover;
            border-radius: 10px 10px 0 0;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.1);
        }
        .modal-content {
            border-radius: 10px;
        }
    </style>
</head>
<body>
<!-- Navbar Section-->
<?php include 'navbar.php'; ?> <!-- Include the navbar.php file -->

<div class="container py-5">
    <!-- Search Results Heading -->
    <h2 class="text-center mb-5"><b>Search Results for "<?php echo $searchQuery; ?>"</b></h2>
    <!-- Display Search Results -->
    <div class="row row-cols-1 row-cols-md-3 g-4 mt-5">
        <?php if(!empty($searchResults)): ?>
            <?php foreach ($searchResults as $index => $result): ?>
                <div class="col">
                    <div class="card" data-bs-toggle="modal" data-bs-target="#modal<?php echo $index; ?>">
                        <img src="images/<?php echo $result['picture']; ?>" class="card-img-top" alt="<?php echo $result['avName']; ?>" style="height: 300px;">
                        <div class="card-body">
                            <h5 class="card-title"><b><?php echo $result['avName']; ?></b></h5>
                        </div>
                    </div>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="modal<?php echo $index; ?>" tabindex="-1" aria-labelledby="modalTitle<?php echo $index; ?>" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalTitle<?php echo $index; ?>"><b><?php echo $result['avName']; ?></b></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <img src="images/<?php echo $result['picture']; ?>" class="img-fluid" alt="<?php echo $result['avName']; ?>">
                                    </div>
                                    <div class="col-md-8">
                                        <p><b>From:</b> <?php echo $result['avCreator']; ?></p>
                                        <p><?php echo $result['description']; ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer d-flex justify-content-center">
                                <a href="review.php?id=<?php echo $result['avId']; ?>" class="btn btn-primary">See Reviews</a>
                                <a href="<?php echo $result['avLink']; ?>" target="_blank" class="btn btn-primary">Main Website</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col">
                <p class="text-center">No results found.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Import Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>