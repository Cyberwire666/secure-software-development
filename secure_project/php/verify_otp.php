<?php
// Start the session to manage session data between pages
session_start();

// Include necessary files for JWT and logging functionalities
require_once 'jwt.php'; // Handle JWT token generation and verification
require_once '../helpers/log_helper.php'; // Utility for logging messages (errors and info)

// Check if OTP is set in the session and if it has expired (5-minute limit)
if (!isset($_SESSION['otp']) || time() - $_SESSION['otp_time'] > 300) {
    log_message("ERROR", "OTP expired."); // Log error if OTP is missing or expired
    header("Location: login.php"); // Redirect to the login page
    exit(); // Stop further execution if OTP is expired or not found
}

// Handle OTP verification process when the form is submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the submitted OTP matches the session stored OTP
    if ($_POST['otp'] == $_SESSION['otp']) {
        // OTP is correct, generate a JWT token using user ID and username
        $token = createJWT($_SESSION['user_id'], $_SESSION['username']);
        $_SESSION['token'] = $token; // Store the generated JWT token in session for future use

        // Clear OTP-related session variables after successful OTP verification
        unset($_SESSION['otp'], $_SESSION['otp_time']); // Unset OTP session data to prevent reuse
        log_message("INFO", "OTP verified. JWT created."); // Log the successful OTP verification and JWT creation

        // Redirect the user to the notes page where they can access their notes
        header("Location: notes.php");
        exit(); // Exit to ensure no further code is executed after the redirect
    } else {
        // Incorrect OTP entered, show an error message to the user
        $error_message = "Invalid OTP."; // Error message indicating invalid OTP
        log_message("ERROR", "Invalid OTP entered for user ID: {$_SESSION['user_id']}"); // Log the invalid OTP attempt
    }
}
?>
<!-- OTP Verification Form HTML -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"> <!-- Set the character encoding to UTF-8 for proper character rendering -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Make page responsive to different devices -->
    <title>Verify OTP - Notes App</title> <!-- Set the title of the page -->
    <link rel="stylesheet" href="../css/style.css"> <!-- Link to the external CSS stylesheet for styling -->
</head>
<body>

<!-- Include header template (same header for all pages) -->
<?php include('../templates/header.php'); ?>

<div class="container">
    <!-- Display the heading for OTP input form -->
    <h2>Enter the OTP sent to your device</h2>

    <!-- Display any error message if it's set (e.g., incorrect OTP entered) -->
    <?php if (isset($error_message)): ?>
        <div class="error-message"><?php echo $error_message; ?></div> <!-- Display error message if available -->
    <?php endif; ?>

    <!-- Debug message: Display the OTP (useful for testing or development purposes) -->
    <div class="debug-message">
        <strong>Debug OTP:</strong> <?php echo htmlspecialchars($_SESSION['otp']); ?>
    </div>

    <!-- OTP input form, submitted to the same page (verify_otp.php) -->
    <form method="POST" action="verify_otp.php"> <!-- Form sends OTP input via POST method -->
        <label for="otp">OTP</label>
        <input type="text" name="otp" id="otp" required> <!-- Input field for OTP (required) -->

        <button type="submit" class="btn btn-primary">Verify OTP</button> <!-- Submit button to verify OTP -->
    </form>
</div>

<!-- Include footer template (same footer for all pages) -->
<?php include('../templates/footer.php'); ?>

</body>
</html>
