<?php
// Start a session to access session variables like the token
session_start();

// Include JWT functions to handle the token validation
require_once 'jwt.php'; // JWT validation functions for user security

// Check if the user is logged in by verifying the token in the session
if (!isset($_SESSION['token'])) {
    // If the token is not present, redirect the user to the login page
    header("Location: login.php");  // Redirect to the login page
    exit();  // Stop further execution as user is not authenticated
}
// Validate the JWT token to ensure the user is authorized
$userData = validateJWT($_SESSION['token']);  // Decodes and checks token for validity

// If the token is invalid, redirect to the login page
if (!$userData) {
    header("Location: login.php");  // Redirect to the login page if JWT validation fails
    exit();  // Stop further execution if JWT validation failed
}

// Include the database connection for interacting with the notes table
require_once 'db.php';  // Including the database connection file

// Check if a note ID has been provided in the URL query parameters
if (isset($_GET['note_id'])) {
    // Get the note_id from the URL and cast it to an integer
    $note_id = (int) $_GET['note_id'];

    // Get the user ID from the validated JWT token
    $user_id = $userData['user_id'];

    // Prepare a SQL query to delete the note, but only if it belongs to the logged-in user
    $stmt = $db->prepare("DELETE FROM notes WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $note_id, $user_id);  // Binding the note_id and user_id as parameters
    $stmt->execute();  // Execute the delete query

    // If the deletion is successful, redirect to the notes.php page
    if ($stmt->affected_rows > 0) {
        header("Location: notes.php");  // Redirect to the notes list page after deletion
        exit();  // Stop further execution after redirect
    } else {
        // If no rows were deleted (possibly the note didn't exist), display an error message
        echo "Error: Note could not be deleted or does not exist.";
    }
} else {
    // If note_id is not provided, show an error message
    echo "Error: Note ID is required.";
}
?>
