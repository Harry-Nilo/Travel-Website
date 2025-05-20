<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compass Login</title>
    <link rel="stylesheet" href="CompassLogin.css">
</head>
<body>
    <div class="container">
        <form action="CompassLoginProcess.php" method="POST">
            <h2>Login to Compass Travel</h2>
            <input type="text" name="username_email" placeholder="Username or Email" required>
            <div class="password-container">
                <input type="password" name="password" id="password" placeholder="Password" required>
                <button type="button" class="show-password" onclick="togglePassword()">Show</button>
            </div>
            <button type="submit">Login</button>
            <p>Forgot your password? <a href="CompassForgetPass.php">Reset Password</a></p>
            <p>Don't have an account? <a href="CompassRegister.php">Register here</a></p>
        </form>
    </div>
    <script src="CompassLogin.js"></script>
</body>
</html>
