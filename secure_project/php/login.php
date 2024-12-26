<?php
session_start();
require_once 'db.php';
require_once 'jwt.php';
require_once '../helpers/log_helper.php'; // Including the log helper

$otp_expiration = 300;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['username'], $_POST['password'])) {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        if (empty($username) || empty($password)) {
            $error_message = "Username and password are required.";
            log_message("ERROR", "Login failed: Empty fields provided.");
        } else {
            $stmt = $db->prepare("SELECT id, password FROM users WHERE username = ?");
            if ($stmt) {
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows === 1) {
                    $stmt->bind_result($user_id, $db_password);
                    $stmt->fetch();

                    if (password_verify($password, $db_password)) {
                        // OTP generation
                        $_SESSION['otp'] = rand(100000, 999999);
                        $_SESSION['otp_time'] = time();
                        $_SESSION['user_id'] = $user_id;
                        $_SESSION['username'] = $username;

                        log_message("INFO", "Login successful: User '{$username}' logged in. OTP generated.");
                        header("Location: verify_otp.php");
                        exit;
                    } else {
                        $error_message = "Invalid password.";
                        log_message("ERROR", "Login failed: Invalid password for username '{$username}'.");
                    }
                } else {
                    $error_message = "Invalid username or password.";
                    log_message("ERROR", "Login failed: Invalid username '{$username}'.");
                }
                $stmt->close();
            } else {
                $error_message = "Database query error.";
                log_message("ERROR", "Login failed: Database query error during login for username '{$username}'.");
            }
        }
    }
}
?>
<!-- Login Form HTML -->
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
