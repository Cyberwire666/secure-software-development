<?php
session_start();
require_once 'jwt.php';
require_once 'db.php';

if (!isset($_SESSION['token']) || !($userData = validateJWT($_SESSION['token']))) {
    header("Location: login.php");
    exit();
}

$user_id = $userData['user_id'];
$stmt = $db->prepare("SELECT * FROM notes WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!-- Notes Management HTML -->


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Notes</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('../templates/header.php'); ?>
    <div class="container">
        <h2>Manage Your Notes</h2>

        <!-- Display Notes -->
        <ul>
            <?php while ($note = $result->fetch_assoc()): ?>
                <li>
                    <?php echo htmlspecialchars($note['content']); ?>
                    <a href="update_note.php?note_id=<?php echo $note['id']; ?>">Edit</a>
                    <a href="delete_note.php?note_id=<?php echo $note['id']; ?>" onclick="return confirm('Are you sure you want to delete this note?');">Delete</a>
                </li>
            <?php endwhile; ?>
        </ul>

        <!-- Add Note Button -->
        <a href="add_note.php" class="btn btn-primary">Add Note</a>
    </div>
</body>
<?php include('../templates/footer.php'); ?>

</html>
