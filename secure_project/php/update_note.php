<?php
// update_note.php

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

if (isset($_GET['note_id'])) {
    $note_id = (int) $_GET['note_id'];
    $user_id = $userData['user_id'];

    // Fetch the note to ensure it belongs to the user
    $stmt = $db->prepare("SELECT content FROM notes WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $note_id, $user_id);
    $stmt->execute();
    $stmt->bind_result($content);
    $stmt->fetch();
    $stmt->close();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $new_content = $_POST['content'];

        // Update the note
        $stmt = $db->prepare("UPDATE notes SET content = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("sii", $new_content, $note_id, $user_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            header("Location: notes.php");
            exit();
        } else {
            $error_message = "Error: Unable to update the note.";
        }
    }
} else {
    echo "Error: Note ID is required.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Note</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('../templates/header.php'); ?>

    <div class="container">
        <?php if (isset($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form method="POST" action="update_note.php?note_id=<?php echo $note_id; ?>">
            <label for="content">Note Content</label>
            <textarea name="content" id="content" rows="5" required><?php echo htmlspecialchars($content); ?></textarea>

            <button type="submit" class="btn btn-primary">Update Note</button>
        </form>
        <a href="notes.php" class="btn btn-secondary">Back to Notes</a>
    </div>
</body>
<?php include('../templates/footer.php'); ?>

</html>
