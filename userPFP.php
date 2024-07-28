<?php
session_start();
include "dbFunctions.php";
include "csrfFunctions.php"; // Include the file containing CSRF functions

// Check if the user is logged in
if (!isset($_SESSION['userId'])) {
    header("Location: login.php");
    exit();
}

// Initialize variables
$message = '';
$currentProfilePicture = isset($_SESSION['profilePicture']) ? $_SESSION['profilePicture'] : '';

// Define maximum file size limit (5 MB in bytes)
$maxFileSize = 5 * 1024 * 1024; // 5 MB

// Handle file upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["profilePicture"])) {
    // Validate CSRF token
    $csrfToken = $_POST['csrf_token'];
    if (!validateCsrfToken($csrfToken)) {
        die("Invalid CSRF token.");
    }

    $uploadOk = 1;
    $targetDir = "pfp/";
    $userId = $_SESSION['userId'];

    // Set a unique file name with user's userId
    $fileName = basename($_FILES["profilePicture"]["name"]);
    $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
    $targetFile = $targetDir . $userId . '.jpg'; // Force file extension to be jpg

    // Check file size
    if ($_FILES["profilePicture"]["size"] > $maxFileSize) {
        $message = "<div class='alert alert-danger' role='alert'>Sorry, your file is too large. Maximum size allowed is 5 MB.</div>";
        $uploadOk = 0;
    }

    // Allow only specific image file types
    $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif' , 'image/jpg'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $fileMimeType = finfo_file($finfo, $_FILES["profilePicture"]["tmp_name"]);
    finfo_close($finfo);

    if (!in_array($fileMimeType, $allowedMimeTypes)) {
        $message = "<div class='alert alert-danger' role='alert'>Invalid file type. Only JPG, PNG, and GIF files are allowed.</div>";
        $uploadOk = 0;
    }

    // If file upload is valid, move the file to the target directory
    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["profilePicture"]["tmp_name"], $targetFile)) {
            $message = "<div class='alert alert-success' role='alert'>File uploaded successfully!</div>";
            // Update session variable with new profile picture path
            $_SESSION['profilePicture'] = $targetFile;
            // Update current profile picture for immediate display
            $currentProfilePicture = $targetFile;
        } else {
            $message = "<div class='alert alert-danger' role='alert'>Sorry, there was an error uploading your file.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Profile Picture</title>
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
            flex-direction: column; /* Align items in a column */
        }
        .card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Add box shadow for modern look */
            border-radius: 10px; /* Round the corners */
            width: 100%; /* Adjust width to fit container */
            max-width: 500px; /* Limit maximum width */
            margin-bottom: 1rem; /* Add space below the card */
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .message-container {
            width: 100%; /* Match the width of the card */
            display: flex;
            justify-content: center;
            align-items: center;
            max-width: 500px; /* Limit maximum width */
            margin-bottom: 1rem; /* Add space below the message */
        }
        .profile-picture {
            width: 150px; /* Set a fixed width */
            height: 150px; /* Set a fixed height */
            border-radius: 50%; /* Make the picture round */
            object-fit: cover; /* Ensure the image covers the area */
            margin-bottom: 1rem; /* Add space below the image */
        }
    </style>
</head>
<body>
<!-- Navbar Section-->
<?php include 'navbar.php'; ?> <!-- Include the navbar.php file -->

<!-- Main Content Section -->
<div class="container mt-5">
    <div class="card mt-3">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Upload Profile Picture</h5>
        </div>
        <div class="card-body">
            <!-- Success or Error Message Section -->
            <?php echo $message; ?>
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
                <div class="mb-3 text-center">
                    <p>
                        <?php if (!empty($currentProfilePicture) && file_exists($currentProfilePicture)): ?>
                            <a href="<?php echo $currentProfilePicture; ?>" target="_blank">
                                <img src="<?php echo $currentProfilePicture; ?>" class="profile-picture" alt="Profile Picture">
                            </a>
                        <?php else: ?>
                            <img src="pfp/avatar.jpg" class="profile-picture" alt="Default Profile Picture">
                        <?php endif; ?>
                    </p>
                    <!-- Display Current Profile Picture -->
                    <label for="profilePicture" class="form-label">Choose a new profile picture:</label>
                    <input type="file" class="form-control" id="profilePicture" name="profilePicture" accept="image/jpeg,image/png,image/gif" required>
                </div>
                <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                <button type="submit" class="btn btn-primary">Upload</button> <!-- Default size button -->
                <a href="deletePFP.php?userId=<?php echo $_SESSION['userId']; ?>" class="btn btn-danger">Delete</a>
            </form>
        </div>
    </div>
</div>

<!-- Import Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>