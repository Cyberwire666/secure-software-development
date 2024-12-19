<?php
// add_note.php

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = $_POST['content'];
    $user_id = $userData['user_id'];

    // Insert new note
    $stmt = $db->prepare("INSERT INTO notes (user_id, content) VALUES (?, ?)");
    $stmt->bind_param("is", $user_id, $content);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        header("Location: notes.php");
        exit();
    } else {
        $error_message = "Failed to add note. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Note</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('../templates/header.php'); ?>

    <div class="container">
        <form method="POST" action="add_note.php">
            <label for="content">Note Content</label>
            <textarea name="content" id="content" rows="5" required></textarea>

            <button type="submit" class="btn btn-primary">Add Note</button>
        </form>
        <a href="notes.php" class="btn btn-secondary">Back to Notes</a>
    </div>
</body>
<?php include('../templates/footer.php'); ?>

</html>
