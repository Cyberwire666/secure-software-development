<?php
// login.php

// Start the session to keep track of user login status
session_start();

// Include necessary files
require_once 'db.php'; // Database connection
require_once 'jwt.php'; // Include functions for generating JWT tokens

// OTP related variables
$otp_expiration = 300; // OTP expiration time in seconds (5 minutes)

// Check if OTP has already been generated and its expiry
if (isset($_SESSION['otp_time']) && time() - $_SESSION['otp_time'] > $otp_expiration) {
    // OTP expired, reset
    unset($_SESSION['otp'], $_SESSION['otp_time']);
}

// Check if the form was submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve input data from the POST request and sanitize it
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        
        // Validate and limit the input length to avoid buffer overflow issues
        if (strlen($username) > 255) {
            $error_message = "Username is too long.";
        } elseif (strlen($password) > 255) {
            $error_message = "Password is too long.";
        } elseif (empty($username) || empty($password)) {
            $error_message = "Username and password cannot be empty.";
        } else {
            // Prepare the SQL query to check if the user exists in the database
            $stmt = $db->prepare("SELECT id, password FROM users WHERE username = ?");
            
            if ($stmt === false) {
                $error_message = "Error preparing the query: " . $db->error; // Handle statement preparation failure
            } else {
                // Bind the parameter and execute the query
                $stmt->bind_param("s", $username);
                if (!$stmt->execute()) {
                    $error_message = "Error executing the query: " . $stmt->error; // Handle execution failure
                } else {
                    $stmt->store_result(); // Store the result to check if a matching user exists
                    
                    if ($stmt->num_rows > 0) {
                        $stmt->bind_result($user_id, $db_password); // Retrieve user ID and password from database
                        $stmt->fetch(); // Fetch the data

                        // Verify if the provided password matches the stored password
                        if (password_verify($password, $db_password)) {
                            // Generate OTP and store it in session
                            $_SESSION['otp'] = rand(100000, 999999);
                            $_SESSION['otp_time'] = time();

                            // You could also send the OTP via email or SMS here

                            // Redirect to OTP verification page
                            header("Location: verify_otp.php");
                            exit(); // Stop further script execution
                        } else {
                            $error_message = "Invalid username or password.";
                        }
                    } else {
                        $error_message = "User not found.";
                    }
                }

                // Close the prepared statement after use
                $stmt->close();
            }
        }
    } elseif (isset($_POST['otp'])) {  // OTP verification form submission
        // Validate OTP input
        if ($_POST['otp'] == $_SESSION['otp'] && time() - $_SESSION['otp_time'] <= $otp_expiration) {
            // OTP is correct and not expired, generate JWT
            $token = createJWT(['user_id' => $user_id, 'username' => $username]);
            $_SESSION['token'] = $token;

            // Redirect to the notes page after successful login
            header("Location: notes.php");
            exit();
        } else {
            $error_message = "Invalid or expired OTP.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Notes App</title>
    <link rel="stylesheet" href="../css/style.css"> <!-- Link to CSS file -->
</head>
<body>

<?php include('../templates/header.php'); // Include header ?>

<div class="container">
    <!-- Login form -->
    <form method="POST" action="login.php">
        <h2>Login to your account</h2>
        
        <!-- Show error message if there is one -->
        <?php if (isset($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <label for="username">Username</label>
        <input type="text" name="username" id="username" required> <!-- Username input field -->

        <label for="password">Password</label>
        <input type="password" name="password" id="password" required> <!-- Password input field -->

        <button type="submit" class="btn btn-primary">Login</button> <!-- Submit button -->
    </form>
    
    <!-- Link to the register page if the user doesn't have an account -->
    <p>Don't have an account? <a href="register.php">Register here</a></p>
</div>

<?php include('../templates/footer.php'); // Include footer ?>

</body>
</html>

