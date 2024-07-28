<?php
    session_start();
    include "dbFunctions.php";

    // create sql query
    $queryMovies = "SELECT * from antivirus";

    // execute sql query
    $result = mysqli_query($link, $queryMovies) or die ("Error querying database");

    // close connection
    mysqli_close($link);
    
    // process the result
    while ($row = mysqli_fetch_assoc($result)){
        $arrAv [] = $row;
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

<!-- Main Content Section -->
<div class="container py-5">
    <h2 class="text-center mb-5"><b>List of Antivirus Software</b></h2>
    <div class="row row-cols-1 row-cols-md-3 g-4 mt-5">
        <?php foreach ($arrAv as $index => $av): ?>
            <div class="col">
                <div class="card" data-bs-toggle="modal" data-bs-target="#modal<?php echo $index; ?>">
                    <img src="images/<?php echo $av['picture']; ?>" class="card-img-top" alt="<?php echo $av['avName']; ?>" style="height: 300px;">
                    <div class="card-body" style="background-color: lightgray;">
                        <h5 class="card-title"><b><?php echo $av['avName']; ?></b></h5>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Modal Section -->
<?php foreach ($arrAv as $index => $av): ?>
    <div class="modal fade" id="modal<?php echo $index; ?>" tabindex="-1" aria-labelledby="modalTitle<?php echo $index; ?>" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <!-- Modal Title -->
                    <h5 class="modal-title" id="modalTitle<?php echo $index; ?>"><b><?php echo $av['avName']; ?></b></h5>
                    <!-- Close Button -->
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Modal Body Content (Image and Description) -->
                    <div class="row">
                        <div class="col-md-4">
                            <!-- Image -->
                            <img src="images/<?php echo $av['picture']; ?>" class="img-fluid" alt="<?php echo $av['avName']; ?>">
                        </div>
                        <div class="col-md-8">
                            <!-- Description -->
                            <p><b>From:</b> <?php echo $av['avCreator']; ?></p>
                            <p><?php echo $av['description']; ?></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-center">
                    <!-- Modal Footer Buttons (See Reviews and Main Website) -->
                    <a href="review.php?id=<?php echo $av['avId']; ?>" class="btn btn-primary">See Reviews</a>
                    <a href="<?php echo $av['avLink']; ?>" target="_blank" class="btn btn-primary">Main Website</a>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<!-- Import Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>