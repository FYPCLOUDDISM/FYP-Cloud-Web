<?php
session_start();
include "dbFunctions.php";
include "csrfFunctions.php"; // Include csrfFunctions.php to use CSRF token functions

// Generate CSRF token
generateCsrfToken();

// Fetch questions with their replies (assuming this part remains the same)
$queryQuestions = "SELECT Q.*, U.username 
                   FROM questions Q
                   INNER JOIN users U 
                   ON Q.userId = U.userId";
$stmtQuestions = mysqli_prepare($link, $queryQuestions);
mysqli_stmt_execute($stmtQuestions);
$resultQuestions = mysqli_stmt_get_result($stmtQuestions);

$questions = [];
while ($question = mysqli_fetch_assoc($resultQuestions)) {
    $questionId = $question['quesId'];
    $queryReplies = "SELECT R.*, U.username 
                     FROM replies R
                     INNER JOIN users U 
                     ON R.userId = U.userId
                     WHERE R.quesId = ?";
    $stmtReplies = mysqli_prepare($link, $queryReplies);
    mysqli_stmt_bind_param($stmtReplies, "i", $questionId);
    mysqli_stmt_execute($stmtReplies);
    $resultReplies = mysqli_stmt_get_result($stmtReplies);

    $replies = [];
    while ($reply = mysqli_fetch_assoc($resultReplies)) {
        $replies[] = $reply;
    }

    $question['replies'] = $replies;
    $questions[] = $question;
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
        .reply-box {
            background-color: #f9f9f9;
            padding: 8px;
            border-radius: 8px;
            margin-top: 10px;
        }
        .reply-box textarea {
            width: 100%;
            height: 80px;
            resize: none;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 5px;
        }
        .reply-box button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 7px 15px;
            cursor: pointer;
            border-radius: 5px;
        }
        .add-question-btn {
            margin-bottom: 10px; /* Reduced spacing */
            margin-top: -20px; /* Adjusted position */
            margin-right: auto;
            display: block;
            margin-left: auto;
        }
        .edit-btn,
        .delete-btn {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 7px 15px;
            cursor: pointer;
            border-radius: 5px;
            margin-right: 5px;
            float: right;
        }
        .modal-header {
            background-color: #007bff;
            color: #fff;
            border-radius: 10px 10px 0 0;
        }
        .modal-title {
            font-weight: bold;
        }
        .modal-body {
            padding: 20px;
        }
        .modal-footer {
            background-color: #f9f9f9;
            border-radius: 0 0 10px 10px;
        }
        .modal-footer button {
            padding: 10px 20px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
<!-- Navbar Section-->
<?php include 'navbar.php'; ?> <!-- Include the navbar.php file -->

<!-- Main Content Section -->
<div class="container py-4">
    <h2 class="text-center mb-4"><b>Forum</b></h2>
    <?php if (isset($_SESSION['userId'])): ?>
        <div class="text-center"> <!-- Add a wrapper with text-center class for center alignment -->
            <button class="btn btn-primary add-question-btn" onclick="window.location.href='questionAdd.php'">Add Question</button>
        </div>
    <?php endif; ?>
    <?php foreach ($questions as $question): ?>
        <div class="card mb-5">
            <div class="card-header">
                <h6 class="mb-1" style="color:#007bff;">
                    <?php if (file_exists('pfp/' . $question['userId'] . '.jpg')): ?>
                        <img src="<?php echo 'pfp/' . $question['userId'] . '.jpg'; ?>" style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; margin-right: 10px;">
                    <?php else: ?>
                        <img src="pfp/avatar.jpg" style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; margin-right: 10px;">
                    <?php endif; ?>
                        <?php echo $question['username']; ?>
                </h6>
                <h4 class="mb-1"><?php echo $question['question']; ?></h4>
            </div>
            <div class="card-body">
                <?php foreach ($question['replies'] as $reply): ?>
                    <div class="mb-3">
                        <p style="color:#007bff;">
                        <?php if (file_exists('pfp/' . $reply['userId'] . '.jpg')): ?>
                            <img src="<?php echo 'pfp/' . $reply['userId'] . '.jpg'; ?>" alt="Profile Picture" style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; margin-right: 10px;">
                        <?php else: ?>
                            <img src="pfp/avatar.jpg" style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; margin-right: 10px;">
                        <?php endif; ?>
                            <?php echo $reply['username']; ?>
                        </p>
                        <small><?php echo $reply['reply']; ?></small>
                        <?php if (isset($_SESSION['userId']) && isset($_SESSION['2fa_verified']) && $_SESSION['2fa_verified'] && $reply['userId'] == $_SESSION['userId']): ?>
                            <!-- Edit and Delete buttons for replies -->
                            <div class="text-end"> <!-- Align buttons to the right -->
                                <button class="btn btn-primary" onclick="window.location.href='replyEdit.php?replyId=<?php echo $reply['replyId']; ?>'">Edit</button>
                                <button class="btn btn-danger" onclick="window.location.href='replyDelete.php?replyId=<?php echo $reply['replyId']; ?>'">Delete</button>
                            </div>
                        <?php endif; ?>
                        <hr>
                    </div>
                <?php endforeach; ?>
                <!-- Reply Box -->
                <?php if (isset($_SESSION['userId']) && isset($_SESSION['2fa_verified'])): ?>
                    <div class="reply-box">
                        <form action="replyAdd.php" method="post">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>"> <!-- Add CSRF token as hidden field -->
                            <input type="hidden" name="question_id" value="<?php echo $question['quesId']; ?>">
                            <textarea name="reply" placeholder="Write your reply here..." required></textarea>
                            <button type="submit">Post Reply</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
            <?php if (isset($_SESSION['userId']) && isset($_SESSION['2fa_verified']) && $_SESSION['2fa_verified'] && $question['userId'] == $_SESSION['userId']): ?>
                <!-- Edit and Delete buttons for questions -->
                <div class="card-footer">
                    <button class="btn btn-primary" onclick="window.location.href='questionEdit.php?quesId=<?php echo $question['quesId']; ?>'">Edit</button>
                    <button class="btn btn-danger" onclick="window.location.href='questionDelete.php?quesId=<?php echo $question['quesId']; ?>'">Delete</button>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

<!-- Import Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>