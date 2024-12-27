<?php
// Start a session to access session variables
session_start();

// Include the required JWT validation function and database connection
require_once 'jwt.php';  // JWT function to validate the token
require_once 'db.php';   // Database connection for fetching notes

// Check if the user has a valid token in the session
if (!isset($_SESSION['token']) || !($userData = validateJWT($_SESSION['token']))) {
    header("Location: login.php");  // If no valid token is present, redirect to the login page
    exit();  // Stop the script execution after redirect
}

// Extract the user ID from the decoded JWT data
$user_id = $userData['user_id'];  // Store the user ID from the JWT

// Prepare an SQL query to fetch notes associated with the user
$stmt = $db->prepare("SELECT * FROM notes WHERE user_id = ?");  // Query to fetch notes for the logged-in user
$stmt->bind_param("i", $user_id);  // Bind the user ID to the SQL query parameter
$stmt->execute();  // Execute the query
$result = $stmt->get_result();  // Get the result of the query
?>

<!-- Notes Management HTML -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Notes</title>
    <link rel="stylesheet" href="../css/style.css">  <!-- Link to the styles for this page -->
</head>
<body>

<?php include('../templates/header.php'); ?> <!-- Include the header for navigation and title -->

<div class="container">
    <h2>Manage Your Notes</h2>  <!-- Page title -->

    <!-- Displaying all the notes in an unordered list -->
    <ul>
        <?php while ($note = $result->fetch_assoc()): ?>  <!-- Loop through the results to fetch each note -->
            <li>
                <?php echo htmlspecialchars($note['content']); ?> <!-- Display the content of the note safely -->
                
                <!-- Edit and delete links for each note -->
                <a href="update_note.php?note_id=<?php echo $note['id']; ?>">Edit</a>  <!-- Link to edit the note -->
                <a href="delete_note.php?note_id=<?php echo $note['id']; ?>" 
                   onclick="return confirm('Are you sure you want to delete this note?');">Delete</a> <!-- Delete the note with confirmation -->
            </li>
        <?php endwhile; ?>  <!-- End loop -->
    </ul>

    <!-- Link to the page where users can add a new note -->
    <a href="add_note.php" class="btn btn-primary">Add Note</a>  <!-- Button to add a new note -->
</div>

</body>
<?php include('../templates/footer.php'); ?> <!-- Include the footer -->

</html>
