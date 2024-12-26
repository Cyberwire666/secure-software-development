<?php
session_start();
require_once 'jwt.php';
require_once '../helpers/log_helper.php';

// Check if OTP exists and if it is expired
if (!isset($_SESSION['otp']) || time() - $_SESSION['otp_time'] > 300) {
    log_message("ERROR", "OTP expired.");
    header("Location: login.php");
    exit();
}

// Handle OTP verification and JWT creation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['otp'] == $_SESSION['otp']) {
        // OTP is correct, generate the JWT token
        $token = createJWT($_SESSION['user_id'], $_SESSION['username']);
        $_SESSION['token'] = $token;

        // Clear OTP session variables after successful verification
        unset($_SESSION['otp'], $_SESSION['otp_time']);
        log_message("INFO", "OTP verified. JWT created.");

        // Redirect to notes page
        header("Location: notes.php");
        exit();
    } else {
        // Incorrect OTP
        $error_message = "Invalid OTP.";
        log_message("ERROR", "Invalid OTP entered for user ID: {$_SESSION['user_id']}");
    }
}
?>
<!-- OTP Verification Form HTML -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP - Notes App</title>
    <link rel="stylesheet" href="../css/style.css"> <!-- Link to CSS file -->
</head>
<body>

<?php include('../templates/header.php'); // Include header ?>

<div class="container">
    <h2>Enter the OTP sent to your device</h2>

    <!-- Show error message if there is one -->
    <?php if (isset($error_message)): ?>
        <div class="error-message"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <!-- Display the OTP for testing or development purposes -->
    <div class="debug-message">
        <strong>Debug OTP:</strong> <?php echo htmlspecialchars($_SESSION['otp']); ?>
    </div>

    <!-- OTP input form -->
    <form method="POST" action="verify_otp.php">
        <label for="otp">OTP</label>
        <input type="text" name="otp" id="otp" required> <!-- OTP input field -->

        <button type="submit" class="btn btn-primary">Verify OTP</button> <!-- Submit button -->
    </form>
</div>

<?php include('../templates/footer.php'); // Include footer ?>

</body>
</html>
