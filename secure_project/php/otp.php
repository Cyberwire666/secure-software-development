<?php
// otp.php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = trim($_POST['otp']);
    
    // Check if the entered OTP matches the one stored in session
    if ($otp == $_SESSION['otp']) {
        // OTP is correct, issue JWT token and redirect to notes
        $token = createJWT(['user_id' => $_SESSION['user_id']]);
        $_SESSION['token'] = $token;
        header("Location: notes.php");
        exit();
    } else {
        // OTP is incorrect
        $error_message = "Invalid OTP. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enter OTP - Notes App</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('../templates/header.php'); ?>
<div class="container">
    <form method="POST" action="otp.php">
        <h2>Enter the OTP</h2>
        <?php if (isset($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <label for="otp">One-Time Password (OTP)</label>
        <input type="text" name="otp" id="otp" required>
        <button type="submit" class="btn btn-primary">Verify OTP</button>
    </form>
</div>
<?php include('../templates/footer.php'); ?>
</body>
</html>
