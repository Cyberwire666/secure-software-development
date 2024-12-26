<?php
// register.php

// Start the session to track user data
session_start();

// Include necessary files
require_once 'db.php';
require_once '../helpers/log_helper.php'; // Include log helper for logging

// If form is submitted via POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize the posted user input
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    try {
        // Validate and limit the input length to avoid buffer overflow issues
        if (strlen($username) > 255) {
            throw new Exception("Username is too long.");
        }
        if (strlen($email) > 255) {
            throw new Exception("Email is too long.");
        }
        if (strlen($password) > 255) {
            throw new Exception("Password is too long.");
        }
        if (empty($username) || empty($email) || empty($password)) {
            throw new Exception("All fields are required.");
        }

        // Check if username or email already exists
        $check_user = $db->prepare("SELECT id FROM users WHERE username = ?");
        $check_email = $db->prepare("SELECT id FROM users WHERE email = ?");

        if ($check_user === false || $check_email === false) {
            throw new Exception("Error preparing the query: " . $db->error);
        }

        // Bind and execute username check
        $check_user->bind_param("s", $username);
        if (!$check_user->execute()) {
            throw new Exception("Error executing the username check query: " . $check_user->error);
        }
        $check_user->store_result();

        if ($check_user->num_rows > 0) {
            throw new Exception("Username already taken. Please choose another.");
        }

        // Bind and execute email check
        $check_email->bind_param("s", $email);
        if (!$check_email->execute()) {
            throw new Exception("Error executing the email check query: " . $check_email->error);
        }
        $check_email->store_result();

        if ($check_email->num_rows > 0) {
            throw new Exception("Email already in use. Please choose another.");
        }

        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert the new user into the database
        $stmt = $db->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        if ($stmt === false) {
            throw new Exception("Error preparing the insert query: " . $db->error);
        }
        $stmt->bind_param("sss", $username, $email, $hashed_password);
        if (!$stmt->execute()) {
            throw new Exception("Error executing the insert query: " . $stmt->error);
        }

        // Log success and redirect
        write_log("New user registered successfully: Username = {$username}, ID = {$stmt->insert_id}");
        $_SESSION['user_id'] = $stmt->insert_id;
        header("Location: ../index.php");
        exit();

    } catch (Exception $e) {
        // Log the error
        write_log($e->getMessage());

        // Display the error message
        $error_message = $e->getMessage();
    } finally {
        // Close statements if they exist
        if (isset($check_user)) $check_user->close();
        if (isset($check_email)) $check_email->close();
        if (isset($stmt)) $stmt->close();
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

<?php include('../templates/header.php'); // Include header ?>

<div class="container">
    <!-- Registration form -->
    <form method="POST" action="register.php">
        <h2>Register an account</h2>

        <!-- Display error message if there is one -->
        <?php if (isset($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <label for="username">Username</label>
        <input type="text" name="username" id="username" required> <!-- Username input field -->

        <label for="email">Email</label>
        <input type="email" name="email" id="email" required> <!-- Email input field -->

        <label for="password">Password</label>
        <input type="password" name="password" id="password" required> <!-- Password input field -->

        <button type="submit" class="btn btn-primary">Register</button> <!-- Submit button -->
    </form>

    <!-- Link to the login page if the user already has an account -->
    <p>Already have an account? <a href="login.php">Login here</a></p>
</div>

<?php include('../templates/footer.php'); // Include footer ?>

</body>
</html>
