<?php
date_default_timezone_set('Singapore');
$db_host = "fyprds.cjmygavhp6fg.us-east-1.rds.amazonaws.com";
$db_user = "admin";
$db_pass = "cloudfyp";
$db_name = "cloud_fyp";
$link = mysqli_connect($db_host, $db_user, $db_pass, $db_name) or die(mysqli_connect_error());

// Create a secure database connection
$link = mysqli_init();
mysqli_ssl_set($link, NULL, NULL, NULL, NULL, NULL);
mysqli_real_connect($link, $db_host, $db_user, $db_pass, $db_name, 3306, NULL, MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT) or die(mysqli_connect_error());

// Function to execute SQL queries with parameters
function executeSQL($query, $params = array()) {
    global $link;
    $stmt = mysqli_prepare($link, $query);
    if ($stmt === false) {
        error_log("SQL Error: " . mysqli_error($link)); // Log SQL errors
        return false;
    }
    if (!empty($params)) {
        $types = '';
        $bindParams = array();
        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= 'i';
            } elseif (is_float($param)) {
                $types .= 'd';
            } elseif (is_string($param)) {
                $types .= 's';
            } else {
                $types .= 'b';
            }
            $bindParams[] = &$param;
        }
        array_unshift($bindParams, $stmt, $types);
        call_user_func_array('mysqli_stmt_bind_param', $bindParams);
    }
    $success = mysqli_stmt_execute($stmt);
    if (!$success) {
        error_log("SQL Error: " . mysqli_stmt_error($stmt)); // Log SQL errors
        return false;
    }
    return mysqli_stmt_get_result($stmt);
}
?>
