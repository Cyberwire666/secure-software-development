<?php
// delete_note.php

session_start();
require_once 'jwt.php'; // Include JWT functions

// Redirect if not logged in
if (!isset($_SESSION['token'])) {
    header("Location: login.php");
    exit();
}

// Validate the JWT token
$userData = validateJWT($_SESSION['token']);
if (!$userData) {
    header("Location: login.php");
    exit();
}

require_once 'db.php';

// Check if the note_id is set
if (isset($_GET['note_id'])) {
    $note_id = (int) $_GET['note_id'];
    $user_id = $userData['user_id'];

    // Delete the note only if it belongs to the logged-in user
    $stmt = $db->prepare("DELETE FROM notes WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $note_id, $user_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        header("Location: notes.php");
        exit();
    } else {
        echo "Error: Note could not be deleted or does not exist.";
    }
} else {
    echo "Error: Note ID is required.";
}
?>
