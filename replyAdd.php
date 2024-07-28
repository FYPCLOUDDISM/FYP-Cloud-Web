<?php
session_start();
include "dbFunctions.php";

// Validate the request method and user session
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['userId'])) {
    // Retrieve and sanitize data from the form
    $questionId = intval($_POST['question_id']); // Convert to integer for security
    $reply = htmlspecialchars($_POST['reply']); // Sanitize input to prevent XSS attacks
    $userId = $_SESSION['userId'];

    // Prepare the SQL statement to insert the reply into the database
    $insertReplyQuery = "INSERT INTO replies (quesId, userId, reply) VALUES (?, ?, ?)";
    
    // Prepare and execute the statement using parameterized queries to prevent SQL injection
    $stmt = mysqli_prepare($link, $insertReplyQuery);
    mysqli_stmt_bind_param($stmt, "iis", $questionId, $userId, $reply);
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        // Redirect back to the forum page after successful reply
        header("Location: forum.php");
        exit();
    } else {
        // Handle the case where the reply insertion fails
        echo "Error: " . mysqli_error($link);
    }
} else {
    // Redirect to the login page if the user is not logged in
    header("Location: login.php");
    exit();
}
?>