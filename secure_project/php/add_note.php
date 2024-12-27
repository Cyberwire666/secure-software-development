<?php
// Start a session to access session variables like the token
session_start();

// Include the JWT functions for token validation
require_once 'jwt.php';

// Check if the token exists in the session, or if it's empty
if (!isset($_SESSION['token']) || empty($_SESSION['token'])) {
    // If no token is found, redirect the user to the login page
    header("Location: login.php");  // Redirecting the user to login.php
    exit();  // Stop script execution if no token is available
}

// Validate the JWT token to check if the user is authorized
$userData = validateJWT($_SESSION['token']);  // Decode and validate the token

// If the validation fails (userData is false), handle it here
if (!$userData) {
    // Redirect to login if the validation fails
    header("Location: login.php");  // Redirect to login page for re-authentication
    exit();  // Stop further execution if validation failed
}

// Include the database connection
require_once 'db.php';  // Database connection for interacting with notes

// Check if the form is submitted via POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Trim any leading/trailing spaces from the content
    $content = trim($_POST['content']);  // User's note content

    // Retrieve the user ID from the decoded JWT token data
    $user_id = $userData['user_id'];

    // Ensure the user_id is valid and not empty
    if (empty($user_id)) {
        // If the user_id is empty, redirect them to login again
        header("Location: login.php");  // Redirect if user_id is missing
        exit();  // Stop if user_id is not present (shouldn't happen, but just in case)
    }

    // Check if content is empty (i.e., if the user didn't provide any text for the note)
    if (empty($content)) {
        // If content is empty, set an error message
        $error_message = "Content cannot be empty."; // Error handling message
    } else {
        // Proceed to insert the note if content is valid
        // Prepare the SQL statement for inserting a new note
        $stmt = $db->prepare("INSERT INTO notes (user_id, content) VALUES (?, ?)");

        // Check if SQL preparation failed
        if ($stmt === false) {
            // Error in SQL preparation, output the error message
            $error_message = "SQL Error: " . $db->error;  // SQL error message
        } else {
            // Bind the user_id and note content to the query parameters
            $stmt->bind_param("is", $user_id, $content);

            // Attempt to execute the statement
            if (!$stmt->execute()) {
                // If the execution fails, display the error message
                $error_message = "Failed to add note. Error: " . $stmt->error;  // Error during execution
            } else {
                // If successful, redirect to the notes page to see the added note
                header("Location: notes.php");  // Redirecting to the notes page after success
                exit();  // Stop further code execution after redirect
            }

            // Close the statement after execution
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Note</title>  <!-- Title for the page -->
    <link rel="stylesheet" href="../css/style.css"> <!-- Link to the stylesheets -->
</head>
<body>

<?php include('../templates/header.php'); ?> <!-- Include the header -->

    <div class="container">
        <!-- Form to input new note content -->
        <form method="POST" action="add_note.php">
            <label for="content">Note Content</label>
            <textarea name="content" id="content" rows="5" required></textarea>  <!-- Text area for note content -->
            <button type="submit" class="btn btn-primary">Add Note</button>  <!-- Submit button to add the note -->
        </form>

        <!-- Link to go back to the notes page -->
        <a href="notes.php" class="btn btn-secondary">Back to Notes</a>  <!-- Back button to notes.php -->

        <!-- If there is an error message, show it -->
        <?php if (isset($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>  <!-- Display error message if set -->
        <?php endif; ?>
    </div>

</body>

<?php include('../templates/footer.php'); ?>  <!-- Include footer for the page -->

</html>
