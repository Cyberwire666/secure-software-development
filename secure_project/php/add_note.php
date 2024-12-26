<?php
session_start();
require_once 'jwt.php'; // Include JWT functions

// Check if the token is set
if (!isset($_SESSION['token']) || empty($_SESSION['token'])) {
    // You can redirect the user to the login page, or display nothing
    header("Location: login.php");  // Redirect to login or a generic error page
    exit();  // Exit if the token is not set
}

// Validate the JWT token
$userData = validateJWT($_SESSION['token']);

// Check for invalid JWT
if (!$userData) {
    // You can redirect the user to the login page here as well
    header("Location: login.php");  // Redirect to login or a generic error page
    exit();  // Stop further execution if validation failed
}

require_once 'db.php'; // Database connection

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = trim($_POST['content']);  // Sanitize input
    $user_id = $userData['user_id'];  // Make sure we are getting the user ID

    // Ensure user_id is not empty before proceeding
    if (empty($user_id)) {
        // You can handle the error here silently or redirect if necessary
        header("Location: login.php");  // Redirect if user_id is missing
        exit();  // Stop if user_id is empty
    }

    // Handle empty content
    if (empty($content)) {
        $error_message = "Content cannot be empty."; // You could choose to silently handle this or log it
    } else {
        // Insert new note
        $stmt = $db->prepare("INSERT INTO notes (user_id, content) VALUES (?, ?)");
        if ($stmt === false) {
            $error_message = "SQL Error: " . $db->error; // Log or silently handle the error
        } else {
            $stmt->bind_param("is", $user_id, $content);

            if (!$stmt->execute()) {
                $error_message = "Failed to add note. Error: " . $stmt->error; // Log or silently handle the error
            } else {
                header("Location: notes.php");  // Redirect after success
                exit();
            }

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

        <!-- Error message section (if set, display the error message) -->
        <?php if (isset($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>
    </div>
</body>
<?php include('../templates/footer.php'); ?>

</html>
