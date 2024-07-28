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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate CSRF token
    $csrfToken = $_POST['csrf_token'];
    if (!validateCsrfToken($csrfToken)) {
        die("Invalid CSRF token.");
    }

    // Validate form data
    $userId = $_SESSION['userId'];
    $question = trim($_POST['question']); // Trim whitespace from the beginning and end of the input

    // Perform server-side validation
    if (empty($question)) {
        $error = "Please provide your question.";
    } elseif (strlen($question) > 255) {
        $error = "Question length exceeds the limit.";
    } else {
        // Prepare and bind parameters
        $stmt = mysqli_prepare($link, "INSERT INTO questions (userId, question) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, "is", $userId, $question);

        // Execute the statement
        if (mysqli_stmt_execute($stmt)) {
            // Set success message
            $success = "Question added successfully!";
        } else {
            // Handle the error
            $error = "Error adding question.";
        }

        // Close the statement
        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Question</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        /* Custom CSS styles */
        body {
            background-color: #f0f0f0; /* Set background color to light gray */
            font-family: 'Poppins', sans-serif; /* Set font family to Poppins */
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
                <div class="card-header" style="font-weight:bold;">Add Question</div>
                <div class="card-body">
                    <?php if(isset($error)): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo $error; ?>
                        </div>
                    <?php elseif(isset($success)): ?>
                        <div class="alert alert-success" role="alert">
                            <i class="fas fa-check-circle me-2 success-icon"></i><?php echo $success; ?>
                        </div>
                    <?php endif; ?>
                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <!-- Add CSRF token as a hidden input field -->
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                        <div class="mb-3">
                            <label for="username" class="form-label">Your Username:</label>
                            <input type="text" id="username" name="username" class="form-control" value="<?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : ''; ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="question" class="form-label">Your Question:</label>
                            <textarea id="question" name="question" class="form-control" rows="5" required></textarea>
                            <div class="invalid-feedback">Please provide your question.</div>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Submit Question</button>
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