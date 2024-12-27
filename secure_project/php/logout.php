<?php
// logout.php
require_once '../helpers/log_helper.php'; // Include logging helper for tracking user actions

// Start a session to access session data (such as user ID and login status)
session_start();

// Log logout event if the user is logged in (i.e., user_id exists in the session)
if (isset($_SESSION['user_id'])) {
    // Log the event when a user logs out, including the user ID
    log_message("INFO", "User with ID " . $_SESSION['user_id'] . " logged out.");
}

// Unset all session variables (clears session data like user_id, token, etc.)
session_unset();

// Destroy the session (completely destroys the session)
session_destroy();

// Redirect the user to the login page after logging out
header("Location: login.php");  // Redirect to the login page after logout
exit();  // Ensure no further code is executed
?>
