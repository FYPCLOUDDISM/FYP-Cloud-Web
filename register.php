<?php
session_start();
include "dbFunctions.php";

// Check if the user is already logged in, if yes, redirect to home page
if (isset($_SESSION['userId'])) {
    header("Location: index.php");
    exit;
}

$message = "";

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_SESSION['userId'])) {
    // Sanitize and validate inputs
    $theUsername = mysqli_real_escape_string($link, $_POST['username']);
    $thePassword = mysqli_real_escape_string($link, $_POST['password']);
    $theName = mysqli_real_escape_string($link, $_POST['name']);
    $theDOB = mysqli_real_escape_string($link, $_POST['DOB']);
    $theEmail = mysqli_real_escape_string($link, $_POST['email']);
    $nowDate = date("Y-m-d");

    // Calculate age
    $dobDate = new DateTime($theDOB);
    $currentDate = new DateTime($nowDate);
    $age = $dobDate->diff($currentDate)->y;

    // Validate inputs
    if (!filter_var($theEmail, FILTER_VALIDATE_EMAIL)) {
        $message .= "<h2><i class='fas fa-exclamation-circle'></i> Registration Failed</h2>";
        $message .= "<p>Invalid email address.</p>";
    } else if ($theDOB > $nowDate) {
        $message .= "<h2><i class='fas fa-exclamation-circle'></i> Registration Failed</h2>";
        $message .= "<p>Invalid Date of Birth.</p>";
    } else if ($age < 18) {
        $message .= "<h2><i class='fas fa-exclamation-circle'></i> Registration Failed</h2>";
        $message .= "<p>You must be at least 18 years old to register.</p>";
    } else {
        // Check if the username and email already exist
        $queryCheckUsername = "SELECT * FROM users WHERE username=?";
        $queryCheckEmail = "SELECT * FROM users WHERE email=?";
        $stmtCheckUsername = mysqli_prepare($link, $queryCheckUsername);
        $stmtCheckEmail = mysqli_prepare($link, $queryCheckEmail);
        mysqli_stmt_bind_param($stmtCheckUsername, "s", $theUsername);
        mysqli_stmt_bind_param($stmtCheckEmail, "s", $theEmail);
        mysqli_stmt_execute($stmtCheckUsername);
        mysqli_stmt_store_result($stmtCheckUsername); // Store the result set
        mysqli_stmt_execute($stmtCheckEmail);
        mysqli_stmt_store_result($stmtCheckEmail); // Store the result set
        $resultCheckUsername = mysqli_stmt_num_rows($stmtCheckUsername);
        $resultCheckEmail = mysqli_stmt_num_rows($stmtCheckEmail);

        if ($resultCheckEmail > 0 && $resultCheckUsername > 0) {
            $message .= "<h2><i class='fas fa-exclamation-circle'></i> Registration Failed</h2>";
            $message .= "<p>Username <b>'$theUsername'</b> and Email <b>'$theEmail'</b> exist.</p>";
        } else if ($resultCheckUsername > 0) {
            $message .= "<h2><i class='fas fa-exclamation-circle'></i> Registration Failed</h2>";
            $message .= "<p>Username <b>'$theUsername'</b> exists.</p>";
        } else if ($resultCheckEmail > 0) {
            $message .= "<h2><i class='fas fa-exclamation-circle'></i> Registration Failed</h2>";
            $message .= "<p>Email <b>'$theEmail'</b> exists.</p>";
        } else {
            // Insert the new user into the database using prepared statements
            $queryRegister = "INSERT INTO users (username, password, name, dob, email) VALUES (?, SHA1(?), ?, ?, ?)";
            $stmtRegister = mysqli_prepare($link, $queryRegister);
            mysqli_stmt_bind_param($stmtRegister, "sssss", $theUsername, $thePassword, $theName, $theDOB, $theEmail);
            $resultRegister = mysqli_stmt_execute($stmtRegister);

            if ($resultRegister) {
                $message .= '<h2><i class="fas fa-check-circle"></i> Registration Successful!</h2>';
                $message .= '<p><strong>Username:</strong> ' . $theUsername . '</p>';
                $message .= '<p><strong>Name:</strong> ' . $theName . '</p>';
                $message .= '<p><strong>Date of Birth:</strong> ' . $theDOB . '</p>';
                $message .= '<p><strong>Email:</strong> ' . $theEmail . '</p>';
                $message .= '<p>Go to <a href="login.php">Login</a> Page to Login Now!</p>';
            } else {
                $message .= '<h2><i class="fas fa-exclamation-circle"></i> Registration Failed</h2>';
                $message .= "<p>Sorry, there was an error during registration. Please try again later.</p>";
            }

            // Close the statement
            mysqli_stmt_close($stmtRegister);
        }
    }

    // Close the connection
    mysqli_close($link);
}
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
        body {
            background-color: #f0f0f0;
            font-family: 'Poppins', sans-serif;
        }
        .message-container {
            border-radius: 8px;
            padding: 30px; /* Increased padding for a larger box */
            margin-top: 20px;
            max-width: 700px; /* Ensures it doesn’t stretch too wide */
            margin-left: auto;
            margin-right: auto;
            font-size: 1.2rem; /* Larger font size */
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .success-message {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .failure-message {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .message-container i {
            font-size: 2rem; /* Larger icon size */
            margin-bottom: 10px;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            max-width: 700px;
            margin: auto;
        }
        .card-body {
            padding: 30px;
        }
        .form-control {
            border-radius: 8px;
        }
        .btn-primary, .btn-secondary {
            border-radius: 8px;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }
        .card-title {
            font-weight: 700;
        }
    </style>
</head>
<body>
    <!-- Navbar Section-->
    <?php include 'navbar.php'; ?> <!-- Include the navbar.php file -->
   
    <!-- Main container -->
    <div class="container mt-4">
        <!-- Message Container -->
        <?php if ($message) { ?>
            <div class="message-container <?php echo strpos($message, 'Failed') !== false ? 'failure-message' : 'success-message'; ?>">
                <?php echo $message; ?>
            </div>
        <?php } ?>
        <!-- Registration Container -->
        <div class="row justify-content-center mt-4">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <?php if (!isset($_SESSION['userId'])) { ?>
                        <h1 class="card-title text-center mb-4"><b>Registration Form</b></h1>
                            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                <!-- Username form fields -->
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username:</label>
                                    <input type="text" id="username" name="username" placeholder="Enter Username" class="form-control" required>
                                </div>
                                <!-- Password form fields -->
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password:</label>
                                    <input type="password" id="password" name="password" placeholder="Enter Password" class="form-control" required pattern="(?=.*\d)(?=.*[a-zA-Z])(?=.*[!@#$%^&*]).{8,}" title="Must contain at least one letter, one number, one special character, and be at least 8 characters long.">
                                </div>
                                <!-- Name form fields -->
                                <div class="mb-3">
                                    <label for="name" class="form-label">Full Name:</label>
                                    <input type="text" id="name" name="name" placeholder="Enter Full Name" class="form-control" required>
                                </div>
                                <!-- Date of Birth form fields -->
                                <div class="mb-3">
                                    <label for="DOB" class="form-label">Date of Birth:</label>
                                    <input type="date" id="DOB" name="DOB" class="form-control" required>
                                </div>
                                <!-- Email form fields -->
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email:</label>
                                    <input type="email" id="email" name="email" placeholder="Enter Email Address" class="form-control" required>
                                </div>
                                <!-- Submit and Reset buttons -->
                                <div class="mb-3 text-center">
                                    <button type="submit" class="btn btn-primary">Register</button>
                                    <button type="reset" class="btn btn-secondary">Reset</button>
                                </div>
                            </form>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>