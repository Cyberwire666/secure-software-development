<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notes Application</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="container">
        <header>
            <h1>Welcome to the Notes App</h1>
        </header>
        <p>
            This app helps you manage your personal notes securely.
            You can add, update, and delete your notes anytime.
        </p>
        <div class="button-group">
            <a href="php/register.php" class="btn btn-primary">Register</a>
            <a href="php/login.php" class="btn btn-secondary">Login</a>
        </div>
    </div>
    <?php include __DIR__ . '/templates/footer.php'; ?>
</body>
</html>
