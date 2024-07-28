<?php
session_start();
date_default_timezone_set('Singapore');
include "dbFunctions.php";
include "sendEmail.php"; // Include the file containing sendEmail() function
include "csrfFunctions.php"; // Include the file containing CSRF functions

// Function to generate a random verification code
function generateVerificationCode() {
    $characters = '0123456789';
    $codeLength = 6; // Define the length of the verification code
    $code = '';
    for ($i = 0; $i < $codeLength; $i++) {
        $code .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $code;
}

// Function to send the 2FA code via email
function sendVerificationCode($to, $verificationCode) {
    $subject = "Two-Factor Authentication Code"; // Email subject
    $body = "Your verification code is: <strong>$verificationCode</strong>.<br><br>This code will expire in 5 minutes."; // Email body
    return sendEmail($to, $subject, $body);
}

// Function to check if the session has timed out
function checkSessionTimeout() {
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 300)) {
        session_unset();
        session_destroy();
        header("Location: login.php");
        exit;
    } else {
        $_SESSION['last_activity'] = time();
    }
}

// Check session timeout
checkSessionTimeout();

// Check if the user is logged in and 2FA verified
if (isset($_SESSION['userId']) && isset($_SESSION['2fa_verified']) && $_SESSION['2fa_verified']) {
    header("Location: index.php");
    exit;
}

// Check if the user is not logged in
if (!isset($_SESSION['userId'])) {
    header("Location: login.php");
    exit;
}

// Resend verification code
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['resend'])) {
    $csrfToken = $_POST['csrf_token'];
    if (!validateCsrfToken($csrfToken)) {
        die("Invalid CSRF token.");
    }

    $verificationCode = generateVerificationCode();
    $to = $_SESSION['email'];
    if (sendVerificationCode($to, $verificationCode)) {
        $_SESSION['2fa_code'] = $verificationCode;
        $expiry = date('Y-m-d H:i:s', strtotime('+5 minutes'));
        $_SESSION['2fa_expiry'] = $expiry;
        $userId = $_SESSION['userId'];
        $sql = "UPDATE users SET 2fa_code = '$verificationCode', 2fa_expiry = '$expiry' WHERE userId = $userId";
        mysqli_query($link, $sql);
        $message = "Verification code resent successfully.";
        $messageClass = "alert-success";
    } else {
        $message = "Failed to resend verification code. Please try again later.";
        $messageClass = "alert-danger";
    }
}

// Check if the 2FA code form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['resend'])) {
    $csrfToken = $_POST['csrf_token'];
    if (!validateCsrfToken($csrfToken)) {
        die("Invalid CSRF token.");
    }

    $enteredCode = $_POST['code'];
    $expectedCode = $_SESSION['2fa_code'];
    if ($enteredCode === $expectedCode) {
        $expiry = $_SESSION['2fa_expiry'];
        if (strtotime($expiry) > time()) {
            $_SESSION['2fa_verified'] = true;
            header("Location: index.php");
            exit;
        } else {
            $message = "Verification code has expired. Please request a new one.";
            $messageClass = "alert-danger";
            session_unset();
            session_destroy();
            header("Location: login.php");
            exit;
        }
    } else {
        $message = "Incorrect verification code. Please try again.";
        $messageClass = "alert-danger";
    }
}

// Generate and send the 2FA code via email only if it's not already set
if (!isset($_SESSION['2fa_code']) && !isset($_POST['resend'])) {
    $verificationCode = generateVerificationCode();
    $to = $_SESSION['email'];
    if (sendVerificationCode($to, $verificationCode)) {
        $_SESSION['2fa_code'] = $verificationCode;
        $expiry = date('Y-m-d H:i:s', strtotime('+5 minutes'));
        $_SESSION['2fa_expiry'] = $expiry;
        $userId = $_SESSION['userId'];
        $sql = "UPDATE users SET 2fa_code = '$verificationCode', 2fa_expiry = '$expiry' WHERE userId = $userId";
        mysqli_query($link, $sql);
    } else {
        $message = "Failed to send verification code. Please try again later.";
        $messageClass = "alert-danger";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Two-Factor Authentication</title>
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
        .login-container {
            max-width: 400px;
            margin: 50px auto 0;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        .login-container h1 {
            font-weight: bold;
            margin-bottom: 20px;
            text-align: center;
        }
        .form-control {
            background-color: #f0f0f0;
            color: #495057;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .alert {
            margin-top: 10px;
            padding: 10px;
            border-radius: 5px;
        }
        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }
        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
    </style>
</head>
<body>
<!-- Navbar Section-->
<?php include 'navbar.php'; ?>

<div class="container">
    <div class="login-container">
        <h1>Two-Factor Authentication</h1>
        <p>A 6-Digit verification code has been sent to your email.<br>Please enter the code below:</p>

        <?php if(isset($message)) { ?>
            <div class="alert <?php echo $messageClass; ?>" role="alert">
                <?php echo $message; ?>
            </div>
        <?php } ?>

        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="mb-3">
                <input type="text" name="code" placeholder="Enter verification code" class="form-control">
            </div>
            <!-- Add CSRF token as a hidden input field -->
            <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
            <button type="submit" class="btn btn-primary">Submit</button>
            <button type="submit" name="resend" class="btn btn-secondary" id="resend-button">Resend Code</button>
        </form>
    </div>
</div>

<!-- Import Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js"></script>
<!-- JavaScript to reload the page after 7 minutes -->
<script>
    setTimeout(function() {
        location.reload();
    }, 300000);
</script>
<!-- JavaScript to enable the resend button after 30 seconds -->
<script>
    setTimeout(function() {
        document.getElementById('resend-button').disabled = false;
    }, 30000);
</script>
</body>
</html>