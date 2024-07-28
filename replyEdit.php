<?php
session_start();
include "dbFunctions.php";
include "csrfFunctions.php"; // Include the file containing CSRF functions

// Check if the user is logged in
if (!isset($_SESSION['userId'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['2fa_verified'])) {
    // Redirect the user to the 2FA verification page
    header("Location: 2fa.php");
    exit();
}

// Check if the reply ID is provided in the URL
if (!isset($_GET['replyId'])) {
    echo "Reply ID not provided.";
    exit();
}

// Get the reply ID from the URL
$replyId = $_GET['replyId'];

// Fetch the reply details from the database
$query = "SELECT * FROM replies WHERE replyId = ?";
$stmt = mysqli_prepare($link, $query);
mysqli_stmt_bind_param($stmt, "i", $replyId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result) {
    echo "Error fetching reply details: " . mysqli_error($link);
    exit();
}

$reply = mysqli_fetch_assoc($result);

// Check if the logged-in user is the author of the reply
if ($_SESSION['userId'] != $reply['userId']) {
    echo "You are not authorized to edit this reply.";
    exit();
}

// Initialize variables for success message and error handling
$successMessage = "";
$errorMessage = "";

// If form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate CSRF token
    $csrfToken = $_POST['csrf_token'];
    if (!validateCsrfToken($csrfToken)) {
        die("Invalid CSRF token.");
    }

    // Sanitize and validate user input
    $newReply = htmlspecialchars($_POST['reply']);

    // Update the reply in the database
    $updateQuery = "UPDATE replies SET reply = ? WHERE replyId = ?";
    $updateStmt = mysqli_prepare($link, $updateQuery);
    mysqli_stmt_bind_param($updateStmt, "si", $newReply, $replyId);
    $updateResult = mysqli_stmt_execute($updateStmt);

    if (!$updateResult) {
        $errorMessage = "Error updating reply: " . mysqli_error($link);
    } else {
        $successMessage = "Reply updated successfully!";
    }
}

mysqli_close($link);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Reply</title>
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
            max-height: 400px; /* Adjust height as needed */
            width: 100%; /* Set the width to 100% for a wider card */
        }

        .card-body {
            padding: 30px; /* Adjust padding as needed */
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
        }
        .card-header {
            background-color: #007bff;
            color: #fff;
            font-weight: bold;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
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
            width: 100%;
        }
        .success-icon {
            font-size: 24px;
            margin-right: 10px;
        }
        .error-container {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
            padding: 10px 20px; /* Adjust padding as needed */
            border-radius: 10px;
            margin-bottom: 20px;
            width: 100%;
        }
        .error-icon {
            font-size: 24px;
            margin-right: 10px;
        }
    </style>
</head>
<body>
<!-- Navbar Section-->
<?php include 'navbar.php'; ?> <!-- Include the navbar.php file -->

<div class="container mt-3">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header" style="color:black;">Edit Reply</div>
                <div class="card-body">
                    <?php if ($successMessage): ?>
                    <!-- Success container to display the updated reply -->
                    <div class="success-container">
                        <i class="fas fa-check-circle me-2 success-icon"></i><?php echo $successMessage; ?>
                    </div>
                    <?php endif; ?>
                    <?php if ($errorMessage): ?>
                    <!-- Error container to display update errors -->
                    <div class="error-container">
                        <i class="fas fa-times-circle me-2 error-icon"></i><?php echo $errorMessage; ?>
                    </div>
                    <?php endif; ?>
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                        <div class="mb-3">
                            <label for="reply" class="form-label">Your Reply:</label>
                            <textarea id="reply" name="reply" class="form-control" rows="5" required><?php echo $reply['reply']; ?></textarea>
                            <div class="invalid-feedback">Please provide your reply.</div>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Edit</button>
                            <a href="forum.php" class="btn btn-secondary ms-3">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Import Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>