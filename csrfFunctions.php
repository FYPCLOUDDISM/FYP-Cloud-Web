<?php
function generateCsrfToken() {
    $token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $token;
    return $token;
}

function validateCsrfToken($token) {
    if (!isset($_SESSION['csrf_token'])) {
        return false; // CSRF token not set in session
    }

    return hash_equals($_SESSION['csrf_token'], $token); // Compare CSRF tokens using hash_equals for timing attack resistance
}
?>
