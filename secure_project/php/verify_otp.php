<?php
// verify_otp.php

// Start session to access session data
session_start();

require_once 'jwt.php';

// Ensure OTP exists in session
if (!isset($_SESSION['otp']) || !isset($_SESSION['otp_time'])) {
    // Redirect to login if OTP isn't set
    header("Location: login.php");
    exit();
}

// Check if OTP is expired
$otp_expiration = 300; // 5 minutes in seconds
if (time() - $_SESSION['otp_time'] > $otp_expiration) {
    unset($_SESSION['otp'], $_SESSION['otp_time']); // Clear OTP session
    $error_message = "OTP expired. Please log in again.";
    header("Location: login.php?error=" . urlencode($error_message));
    exit();
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve OTP from user input
    $user_otp = trim($_POST['otp']);

    // Validate OTP
    if ($user_otp == $_SESSION['otp']) {
        // OTP is correct, allow login
        $token = createJWT(['user_id' => $_SESSION['user_id'], 'username' => $_SESSION['username']]);
        $_SESSION['token'] = $token;

        // Clear OTP session variables after successful verification
        unset($_SESSION['otp'], $_SESSION['otp_time']);

        header("Location: notes.php"); // Redirect to notes page
        exit();
    } else {
        $error_message = "Invalid OTP. Please try again.";
    }
}
?>

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
