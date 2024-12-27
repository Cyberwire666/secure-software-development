<?php
// Start the session to manage user data between pages
session_start();

// Include the database connection script and log helper for logging errors and events
require_once 'db.php'; // Contains $db for database interactions
require_once '../helpers/log_helper.php'; // Utility for logging events and errors

// Main block for handling the POST request (user registration form submission)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data and trim whitespace
    $username = trim($_POST['username']); // Get and clean the 'username' input
    $email = trim($_POST['email']);       // Get and clean the 'email' input
    $password = trim($_POST['password']); // Get and clean the 'password' input

    // Check if any field is empty
    if (empty($username) || empty($email) || empty($password)) {
        $error_message = "All fields are required."; // Error for missing fields
        log_message('ERROR', 'Registration failed: missing fields.'); // Log the issue
    } else {
        // Query to check if the username or email already exists
        $stmt = $db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email); // Bind the username and email to the query
        $stmt->execute();                          // Execute the query
        $stmt->store_result();                     // Store the result for future use

        // Check if a user with the same username or email exists
        if ($stmt->num_rows > 0) {
            $error_message = "Username or email is already taken."; // Notify the user
            log_message('ERROR', "Registration failed: {$username} or {$email} already exists."); // Log the duplicate entry
        } else {
            // Hash the password for secure storage
            $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hash the password with bcrypt

            // Prepare the query to insert the new user's data
            $stmt = $db->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $hashed_password); // Bind the values to the query

            // Execute the insertion query and check success
            if ($stmt->execute()) {
                $_SESSION['user_id'] = $stmt->insert_id; // Store the new user's ID in the session
                log_message('INFO', "New user registered: {$username} with email {$email}"); // Log the successful registration
                header("Location: login.php"); // Redirect to the login page
                exit(); // Exit to stop further script execution
            } else {
                log_message('ERROR', "Database error during registration for username: {$username}"); // Log a database error
            }
        }
        $stmt->close(); // Close the statement
    }
}
?>

<!-- HTML for the registration form page -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"> <!-- Character encoding -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge"> <!-- IE compatibility -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Responsive design -->
    <title>Register - Notes App</title> <!-- Page title -->
    <link rel="stylesheet" href="../css/style.css"> <!-- External CSS -->
</head>
<body>

<!-- Include a common header template -->
<?php include('../templates/header.php'); ?>

<div class="container">
    <!-- Registration Form -->
    <form method="POST" action="register.php"> <!-- Form submission via POST to this file -->
        <h2>Register an account</h2> <!-- Heading -->

        <!-- Display error messages -->
        <?php if (isset($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div> <!-- Display error if available -->
        <?php endif; ?>

        <!-- Username input -->
        <label for="username">Username</label>
        <input type="text" name="username" id="username" required> <!-- Input with validation -->

        <!-- Email input -->
        <label for="email">Email</label>
        <input type="email" name="email" id="email" required> <!-- Input with email-specific validation -->

        <!-- Password input -->
        <label for="password">Password</label>
        <input type="password" name="password" id="password" required> <!-- Password field -->

        <!-- Submit button -->
        <button type="submit" class="btn btn-primary">Register</button>
    </form>

    <!-- Link to login page for existing users -->
    <p>Already have an account? <a href="login.php">Login here</a></p>
</div>

<!-- Include a common footer template -->
<?php include('../templates/footer.php'); ?>

</body>
</html>
