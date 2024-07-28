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
    header("Location: 2fa.php"); // Redirect to 2FA verification page if not verified
    exit();
}

// Check if the user ID is provided in the URL
if (!isset($_GET['userId'])) {
    header("Location: userEdit.php"); // Redirect to user edit page if user ID is not provided
    exit();
}

$userId = $_SESSION['userId'];

// Check if the user is trying to delete their own account
if ($userId != $_GET['userId']) {
    header("Location: userEdit.php"); // Redirect to user edit page if trying to delete another user's account
    exit();
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate CSRF token
    $csrfToken = $_POST['csrf_token'];
    if (!validateCsrfToken($csrfToken)) {
        die("Invalid CSRF token.");
    }

    // Begin a transaction to ensure all-or-nothing deletion
    mysqli_begin_transaction($link);

    try {
        // Prepare and execute the deletion operations
        $deleteReviewsQuery = "DELETE FROM reviews WHERE userId = ?";
        $deleteQuestionsQuery = "DELETE FROM questions WHERE userId = ?";
        $deleteRepliesQuery = "DELETE FROM replies WHERE userId = ?";
        $deleteUserQuery = "DELETE FROM users WHERE userId = ?";

        // Delete reviews
        $stmt = mysqli_prepare($link, $deleteReviewsQuery);
        mysqli_stmt_bind_param($stmt, "i", $userId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Delete forum posts
        $stmt = mysqli_prepare($link, $deleteQuestionsQuery);
        mysqli_stmt_bind_param($stmt, "i", $userId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Delete forum replies
        $stmt = mysqli_prepare($link, $deleteRepliesQuery);
        mysqli_stmt_bind_param($stmt, "i", $userId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Delete user
        $stmt = mysqli_prepare($link, $deleteUserQuery);
        mysqli_stmt_bind_param($stmt, "i", $userId);
        $deleteResult = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        if ($deleteResult) {
            // Delete the profile picture file
            $profilePicPath = 'pfp/' . $userId . '.jpg';
            if (file_exists($profilePicPath)) {
                unlink($profilePicPath);
            }

            // Commit the transaction
            mysqli_commit($link);

            // User account successfully deleted, redirect to logout
            header("Location: logout.php");
            exit();
        } else {
            throw new Exception("Error deleting user account.");
        }
    } catch (Exception $e) {
        // Rollback the transaction in case of error
        mysqli_rollback($link);
        $errorMessage = "Error deleting account: " . $e->getMessage();
    }

    // Close the statement and connection
    mysqli_close($link);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Account</title>
    <!-- Import Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Import Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
            max-width: 550px; /* Limit maximum width */
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

<!-- Main Content Section -->
<div class="container mt-5">
    <div class="card">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0">Delete Account</h5>
        </div>
        <div class="card-body">
            <?php if(isset($errorMessage)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $errorMessage; ?>
                </div>
            <?php endif; ?>
            <p>Are you sure you want to delete your account?</p>
            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                <button type="submit" class="btn btn-danger">Delete Account</button>
                <a href="userEdit.php?userId=<?php echo $_SESSION['userId']; ?>" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>

<!-- Import Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>