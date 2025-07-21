<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set content type to JSON
header('Content-Type: application/json');

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    // If logged in, return success with user details (optional)
    echo json_encode([
        "loggedIn" => true,
        "user_id" => $_SESSION['user_id']
    ]);
} else {
    // If not logged in, return failure
    echo json_encode([
        "loggedIn" => false
    ]);
}
?>
