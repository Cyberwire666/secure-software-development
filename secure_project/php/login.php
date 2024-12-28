<?php
// Start the session to store and manage user-related data between pages
session_start();

// Include necessary files: database connection, JWT handling, and logging helper
require_once 'db.php'; // This includes the database connection `$db`
require_once 'jwt.php'; // JWT functionality for token handling
require_once '../helpers/log_helper.php'; // Utility for logging events and errors

// Define OTP expiration time (5 minutes)
$otp_expiration = 300; // OTP expires in 300 seconds (5 minutes)

// Main block for handling POST requests (user login form submission)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if username and password are provided via POST
    if (isset($_POST['username'], $_POST['password'])) {
        // Clean and store form inputs for username and password
        $username = trim($_POST['username']); // Trim whitespace from username
        $password = trim($_POST['password']); // Trim whitespace from password

        if (strlen($username) > 255) {
            $error_message = "Username is too long.";
            log_message("error", $error_message);
        } elseif (strlen($password) > 255) {
            $error_message = "Password is too long.";
            log_message("error", $error_message);
        } elseif (empty($username) || empty($password)) {
            $error_message = "Username and password cannot be empty.";
            log_message("error", $error_message);
        } else {
            // Prepare the SQL query to retrieve user data from the database by username
            $stmt = $db->prepare("SELECT id, password FROM users WHERE username = ?");
            if ($stmt) {
                $stmt->bind_param("s", $username); // Bind the 'username' value to the query
                $stmt->execute(); // Execute the SQL query
                $stmt->store_result(); // Store the result for later use

                // Check if exactly one user is found
                if ($stmt->num_rows === 1) {
                    $stmt->bind_result($user_id, $db_password); // Bind the result values to variables
                    $stmt->fetch(); // Fetch the data (user ID and password)

                    // Verify if the provided password matches the stored hashed password
                    if (password_verify($password, $db_password)) {
                        // OTP generation for the user
                        $_SESSION['otp'] = rand(100000, 999999); // Generate a random 6-digit OTP
                        $_SESSION['otp_time'] = time(); // Store the time when OTP was generated
                        $_SESSION['user_id'] = $user_id; // Store user ID in the session
                        $_SESSION['username'] = $username; // Store username in the session

                        log_message("INFO", "Login successful: User '{$username}' logged in. OTP generated."); // Log the successful login
                        header("Location: verify_otp.php"); // Redirect to the OTP verification page
                        exit; // Exit to prevent further script execution
                    } else {
                        $error_message = "Invalid password."; // Error message for incorrect password
                        log_message("ERROR", "Login failed: Invalid password for username '{$username}'."); // Log the password mismatch
                    }
                } else {
                    $error_message = "Invalid username or password."; // Error message for incorrect username
                    log_message("ERROR", "Login failed: Invalid username '{$username}'."); // Log the invalid username attempt
                }
                $stmt->close(); // Close the statement to free resources
            } else {
                $error_message = "Database query error."; // Error message for query failure
                log_message("ERROR", "Login failed: Database query error during login for username '{$username}'."); // Log the database error
            }
        }
    }
}
?>

<!-- HTML structure for the login page -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"> <!-- Set the character encoding to UTF-8 -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Ensure responsiveness for all devices -->
    <title>Login - Notes App</title> <!-- Set the title of the page -->
    <link rel="stylesheet" href="../css/style.css"> <!-- Link to the external CSS stylesheet -->
</head>
<body>

<!-- Include the header template (common across all pages) -->
<?php include('../templates/header.php'); ?>

<div class="container">
    <!-- Login form where users input their credentials -->
    <form method="POST" action="login.php"> <!-- Form submits POST data to this PHP script -->
        <h2>Login to your account</h2> <!-- Form heading -->

        <!-- Show error message if there is an error (e.g. empty fields, invalid username or password) -->
        <?php if (isset($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div> <!-- Display error message if set -->
        <?php endif; ?>

        <!-- Input field for the username -->
        <label for="username">Username</label>
        <input type="text" name="username" id="username" required> <!-- Require username input field -->

        <!-- Input field for the password -->
        <label for="password">Password</label>
        <input type="password" name="password" id="password" required> <!-- Require password input field -->

        <!-- Submit button to submit the login form -->
        <button type="submit" class="btn btn-primary">Login</button> <!-- Submit button with styling -->
    </form>

    <!-- Link to the registration page for new users -->
    <p>Don't have an account? <a href="register.php">Register here</a></p>
</div>

<!-- Include the footer template (common across all pages) -->
<?php include('../templates/footer.php'); ?>

</body>
</html>
