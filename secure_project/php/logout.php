<?php
// logout.php
require_once '../helpers/log_helper.php'; // Include logging helper
session_start();

// Log logout event with the user ID
if (isset($_SESSION['user_id'])) {
    log_message("INFO", "User with ID " . $_SESSION['user_id'] . " logged out.");
}

// Unset and destroy session
session_unset();
session_destroy();

// Redirect to login page after logout
header("Location: login.php");
exit();
?>
