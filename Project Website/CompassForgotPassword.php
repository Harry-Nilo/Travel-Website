<!DOCTYPE html>
<html lang="en">
<head>
    <title>Reset Password</title>
    <link rel="stylesheet" href="CompassLogin1.css">
</head>
<body>
    <div class="container">
        <form action="CompassResetProcess.php" method="POST">
            <h2>Reset Your Password</h2>
            <input type="email" name="email" placeholder="Enter your email" required>
            <div class="password-container">
    <input type="password" name="new_password" id="new_password" placeholder="Enter new password" required
        pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\W_]).{8,}"
        title="Password must be at least 8 characters long and include uppercase, lowercase, number, and special character.">
    <button type="button" class="show-password" onclick="togglePassword()">Show</button>
</div>
            <button type="submit">Reset Password</button>
            <p><a href="CompassLogin.php">Back to Login</a></p>
        </form>
    </div>
    <script src="CompassForgotPassword.js"></script>
</body>
</html>
