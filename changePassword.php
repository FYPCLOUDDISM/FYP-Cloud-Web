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

// Initialize error and success messages
$errorMessage = "";
$successMessage = "";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate CSRF token
    $csrfToken = $_POST['csrf_token'];
    if (!validateCsrfToken($csrfToken)) {
        die("Invalid CSRF token.");
    }

    // Retrieve form data and sanitize it
    $newPassword = mysqli_real_escape_string($link, $_POST['newPassword']);
    $confirmNewPassword = mysqli_real_escape_string($link, $_POST['confirmNewPassword']);
    $userId = $_SESSION['userId'];

    // Validate the new password
    if (strlen($newPassword) < 8) {
        $errorMessage = "New password must be at least 8 characters long!";
    } elseif ($newPassword !== $confirmNewPassword) {
        $errorMessage = "New passwords do not match!";
    } else {
        // Update the password in the database
        $updateQuery = "UPDATE users SET password = SHA1(?) WHERE userId = ?";
        $stmt = mysqli_prepare($link, $updateQuery);
        mysqli_stmt_bind_param($stmt, "si", $newPassword, $userId);
        $updateResult = mysqli_stmt_execute($stmt);

        if ($updateResult) {
            $successMessage = "<i class='fas fa-check-circle me-2 success-icon'></i>Password updated successfully!";
            // Clear error message if the operation was successful
            $errorMessage = "";
        } else {
            $errorMessage = "Error updating password: " . mysqli_error($link);
        }

        // Close the statement
        mysqli_stmt_close($stmt);
    }
}

mysqli_close($link);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
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
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Change Password</h5>
        </div>
        <div class="card-body">
            <?php if(!empty($errorMessage)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $errorMessage; ?>
                </div>
            <?php endif; ?>
            <?php if(!empty($successMessage)): ?>
                <div class="alert alert-success" role="alert">
                    <?php echo $successMessage; ?>
                </div>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="newPassword" class="form-label">New Password</label>
                    <input type="password" class="form-control" id="newPassword" name="newPassword" required pattern="(?=.*\d)(?=.*[a-zA-Z])(?=.*[!@#$%^&*]).{8,}" title="Password must contain at least one number, one letter, one special character, and be at least 8 characters long">
                </div>
                <div class="mb-3">
                    <label for="confirmNewPassword" class="form-label">Confirm New Password</label>
                    <input type="password" class="form-control" id="confirmNewPassword" name="confirmNewPassword" required pattern="(?=.*\d)(?=.*[a-zA-Z])(?=.*[!@#$%^&*]).{8,}" title="Password must contain at least one number, one letter, one special character, and be at least 8 characters long">
                </div>
                <!-- Add CSRF token as a hidden input field -->
                <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                <!-- (?=.*\d): At least one digit.
                     (?=.*[a-zA-Z]): At least one letter (uppercase or lowercase).
                     (?=.*[!@#$%^&*]): At least one special character from the provided set.
                     .{8,}: At least 8 characters in total.
                -->
                <div class="mb-2">
                    <button type="submit" class="btn btn-primary me-2">Change Password</button>
                    <a href="userEdit.php?userId=<?php echo $_SESSION['userId']; ?>" class="btn btn-warning me-2">Edit Account</a>
                    <a href="userDelete.php?userId=<?php echo $_SESSION['userId']; ?>" class="btn btn-danger">Delete Account</a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Import Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>