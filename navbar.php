<?php
// Check if the user is logged in
if (isset($_SESSION['userId']) && !empty($_SESSION['userId'])) {
    $_SESSION['profilePicture'] = 'pfp/' . $_SESSION['userId'] . '.jpg'; // Update session variable for profile picture
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
            font-family: 'Poppins', sans-serif;
        }
        .navbar {
            background-color: #f8f9fa;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 15px 0;
        }
        .navbar-brand {
            font-weight: bold;
            color: #007bff !important;
            font-size: 24px;
        }
        .nav-link {
            color: #444;
            font-weight: bold;
            font-size: 16px;
            transition: color 0.3s ease;
            margin-right: 10px; /* Adjust spacing between nav links */
        }
        .nav-link:hover {
            color: #007bff;
        }
        .search-container {
            display: flex;
            align-items: center;
        }
        .search-container form {
            margin-bottom: 0;
        }
        .dropdown-menu {
            padding: 10px 0; /* Adjust dropdown menu padding */
        }
        .dropdown-menu a.dropdown-item {
            padding: 8px 20px; /* Adjust dropdown item padding */
            font-size: 14px; /* Adjust dropdown item font size */
        }
    </style>
</head>
<body>
    <!-- Navbar Section -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <!-- Brand Link -->
            <a class="navbar-brand" href="index.php">FYP Antivirus Review</a>
            <!-- Navbar Toggler -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <!-- Navbar Collapsible Content -->
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- User/Login Links -->
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <?php if (!isset($_SESSION['userId'])): ?>
                            <a class="nav-link" href="login.php">Login/Register</a>
                        <?php else: ?>
                            <div class="dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <?php if (isset($_SESSION['profilePicture']) && file_exists($_SESSION['profilePicture'])): ?>
                                        <img src="<?php echo $_SESSION['profilePicture']; ?>" style="width: 25px; height: 25px; border-radius: 50%; object-fit: cover; margin-right: 5px;">
                                    <?php else: ?>
                                        <img src="pfp/avatar.jpg" style="width: 25px; height: 25px; border-radius: 50%; object-fit: cover; margin-right: 5px;">
                                    <?php endif; ?>
                                    <?php echo $_SESSION['name']; ?>
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <li><a class="dropdown-item" href="userEdit.php?userId=<?php echo $_SESSION['userId']; ?>">Edit Account Details</a></li>
                                    <li><a class="dropdown-item" href="userPFP.php?userId=<?php echo $_SESSION['userId']; ?>">Edit Profile Picture</a></li>
                                    <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </li>
                    <!-- Forum and Contact Us Links -->
                    <li class="nav-item">
                        <a class="nav-link" href="forum.php">Forum</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contactUs.php">Contact Us</a>
                    </li>
                </ul>
                <!-- Search Form -->
                <form class="d-flex ms-auto" action="search.php" method="get">
                    <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search" name="search">
                    <button class="btn btn-outline-primary" type="submit"><i class="fa fa-search"></i></button>
                </form>
            </div>
        </div>
    </nav>
</body>
</html>