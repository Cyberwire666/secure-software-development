<?php
// Start a session to access session variables like the token
session_start();

// Include the JWT functions for token validation
require_once 'jwt.php'; // JWT validation functions for security

// Check if the user is logged in by verifying the token in the session
if (!isset($_SESSION['token'])) {
    // If the token is not present, redirect the user to the login page
    header("Location: login.php");  // Redirecting to login page
    exit();  // Stop further execution if the user is not logged in
}

// Validate the JWT token to ensure the user is authorized
$userData = validateJWT($_SESSION['token']);  // Decodes and checks token for validity

// If the token is invalid, redirect to the login page
if (!$userData) {
    header("Location: login.php");  // Redirect to login if validation failed
    exit();  // Stop further execution if JWT validation fails
}

// Include the database connection for interacting with the notes table
require_once 'db.php';  // Database connection

// Check if a note ID has been passed in the URL query parameters
if (isset($_GET['note_id'])) {
    // Get the note_id from the URL and convert it to an integer
    $note_id = (int) $_GET['note_id'];

    // Get the user ID from the validated JWT token
    $user_id = $userData['user_id'];

    // Prepare a SQL query to fetch the note's content from the database for the logged-in user
    $stmt = $db->prepare("SELECT content FROM notes WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $note_id, $user_id);  // Binding the note_id and user_id as parameters
    $stmt->execute();  // Execute the query
    $stmt->bind_result($content);  // Bind the result to the $content variable
    $stmt->fetch();  // Fetch the result (note's content)
    $stmt->close();  // Close the prepared statement

    // If the form is submitted (POST request)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get the new content from the form
        $new_content = $_POST['content'];

        // Prepare an UPDATE SQL query to change the note's content in the database
        $stmt = $db->prepare("UPDATE notes SET content = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("sii", $new_content, $note_id, $user_id);  // Binding the new content, note_id, and user_id
        $stmt->execute();  // Execute the query to update the content

        // If the update is successful (affected rows are greater than 0), redirect to the notes page
        if ($stmt->affected_rows > 0) {
            header("Location: notes.php");  // Redirect to notes.php after a successful update
            exit();  // Stop further execution after redirecting
        } else {
            // If no rows were affected (update failed), set an error message
            $error_message = "Error: Unable to update the note.";
        }
    }
} else {
    // If note_id is not provided in the URL, show an error message
    echo "Error: Note ID is required.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Note</title>  <!-- Title of the page -->
    <link rel="stylesheet" href="../css/style.css">  <!-- Link to the stylesheets -->
</head>
<body>

<?php include('../templates/header.php'); ?>  <!-- Include the page header -->

    <div class="container">
        <!-- Display any error message if set -->
        <?php if (isset($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>  <!-- Display error message here -->
        <?php endif; ?>

        <!-- Form to update the note -->
        <form method="POST" action="update_note.php?note_id=<?php echo $note_id; ?>">
            <!-- Label and text area for updating note content -->
            <label for="content">Note Content</label>
            <textarea name="content" id="content" rows="5" required><?php echo htmlspecialchars($content); ?></textarea>  <!-- Pre-filled with the current content -->

            <button type="submit" class="btn btn-primary">Update Note</button>  <!-- Submit button for updating the note -->
        </form>

        <!-- Link to go back to the notes page -->
        <a href="notes.php" class="btn btn-secondary">Back to Notes</a>  <!-- Button to go back to notes list -->

    </div>

</body>

<?php include('../templates/footer.php'); ?>  <!-- Include the page footer -->

</html>
