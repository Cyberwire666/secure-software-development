<?php
// register.php

// Start the session to track user data
session_start();

// Include database connection file
require_once 'db.php';

// If form is submitted via POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize the posted user input
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Simple form validation to ensure fields are not empty
    if (empty($username) || empty($email) || empty($password)) {
        $error_message = "All fields are required.";
    } else {
        // Prepare the SQL query to check if the username already exists in the database
        $check_user = $db->prepare("SELECT id FROM users WHERE username = ?");
        $check_email = $db->prepare("SELECT id FROM users WHERE email = ?");
        
        if ($check_user === false || $check_email === false) {
            $error_message = "Error preparing the query: " . $db->error; // Handle statement preparation failure
        } else {
            // Bind the parameters for both queries
            $check_user->bind_param("s", $username);
            $check_email->bind_param("s", $email);

            // Execute first query (username check)
            if (!$check_user->execute()) {
                $error_message = "Error executing the username check query: " . $check_user->error;
            } else {
                // Store the result for username check
                $check_user->store_result();
                
                // Check if the username already exists
                if ($check_user->num_rows > 0) {
                    $error_message = "Username already taken. Please choose another.";
                } else {
                    // Execute second query (email check)
                    if (!$check_email->execute()) {
                        $error_message = "Error executing the email check query: " . $check_email->error;
                    } else {
                        // Store the result for email check
                        $check_email->store_result();
                        
                        // Check if the email is already taken
                        if ($check_email->num_rows > 0) {
                            $error_message = "Email already in use. Please choose another.";
                        } else {
                            // Hash the password before storing it in the database for better security
                            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                            // SQL Injection Mitigation 
                            // Prepare the SQL query to insert a new user into the database
                            $stmt = $db->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
                            
                            if ($stmt === false) {
                                $error_message = "Error preparing the insert query: " . $db->error;
                            } else {
                                // Bind the parameters for the insertion query
                                $stmt->bind_param("sss", $username, $email, $hashed_password);
                                if (!$stmt->execute()) {
                                    $error_message = "Error executing the insert query: " . $stmt->error; // Handle execution failure
                                } else {
                                    // Store the user ID in the session after successful registration
                                    $_SESSION['user_id'] = $stmt->insert_id;
                                    // Redirect to the index page after successful registration
                                    header("Location: ../index.php");
                                    exit(); // Stop further script execution
                                }
                            }
                            
                            // Close the statement for inserting new user
                            $stmt->close();
                        }
                    }
                }

                // Close the statement after executing the username check
                $check_email->close();
                $check_user->close();
            }
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
