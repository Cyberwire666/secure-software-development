<?php
session_start();
require_once 'db.php';
require_once '../helpers/log_helper.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($email) || empty($password)) {
        $error_message = "All fields are required.";
    } else {
        $stmt = $db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error_message = "Username or email is already taken.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $hashed_password);

            if ($stmt->execute()) {
                $_SESSION['user_id'] = $stmt->insert_id;
                header("Location: login.php");
                exit();
            }
        }
        $stmt->close();
    }
}
?>
<!-- Registration Form HTML -->

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
