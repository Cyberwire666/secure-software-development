<?php
// register.php

session_start();

// Include the database connection file
require_once 'db.php';

// If form is submitted (POST request)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate user input
    $username = mysqli_real_escape_string($db, $_POST['username']);
    $password = $_POST['password'];

    // Check if the username already exists in the database
    $check_user = $db->prepare("SELECT id FROM users WHERE username = ?");
    $check_user->bind_param("s", $username);
    $check_user->execute();
    $check_user->store_result();
    
    if ($check_user->num_rows > 0) {
        // If username exists, show an error message
        $error_message = "Username already taken. Please choose another.";
    } else {
        // Hash the password before storing it
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert the new user into the users table
        $stmt = $db->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $hashed_password);
        
        if ($stmt->execute()) {
            // If registration is successful, log the user in and redirect
            $_SESSION['user_id'] = $stmt->insert_id;
            header("Location: ../index.php");
            exit();
        } else {
            // If the insertion fails, show a generic error message
            $error_message = "Something went wrong. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Notes App</title>
    <link rel="stylesheet" href="../css/style.css"> <!-- Linking to external CSS -->
</head>

<body>

<?php include('../templates/header.php'); ?>

<div class="container">
    <form method="POST" action="register.php">
        <h2>Register an account</h2>

        <!-- Display error message if there is any -->
        <?php if (isset($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <label for="username">Username</label>
        <input type="text" name="username" id="username" required>

        <label for="password">Password</label>
        <input type="password" name="password" id="password" required>

        <button type="submit" class="btn btn-primary">Register</button>
    </form>

    <p>Already have an account? <a href="login.php">Login here</a></p>
</div>


<?php include('../templates/footer.php'); ?>

</body>
</html>
