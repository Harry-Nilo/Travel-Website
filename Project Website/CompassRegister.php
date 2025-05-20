<!DOCTYPE html>
<html lang="en">
<head>
    <title>Compass Register</title>
    <link rel="stylesheet" href="CompassRegister.css">
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <form action="CompassRegisterProcess.php" method="POST" onsubmit="return validatePasswords()">
                <h2>Register for Compass Travel</h2>
                <input type="text" name="username" placeholder="Username" required>
                <input type="email" name="email" placeholder="Email" required>

                <div class="password-container">
                    <input type="password" name="password" id="password" placeholder="Password" required
                        pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\W_]).{8,}"
                        title="Password must be at least 8 characters long and include uppercase, lowercase, number, and special character.">
                    <button type="button" class="show-password" onclick="togglePassword('password')">Show</button>
                </div>

                <div class="password-container">
                    <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
                    <button type="button" class="show-password" onclick="togglePassword('confirm_password')">Show</button>
                </div>

                <button type="submit">Register</button>
            </form>
            <p>Already have an account? <a href="CompassLogin.php">Login here</a></p>
        </div>
    </div>

    <script>
        function validatePasswords() {
            const password = document.getElementById('password').value;
            const confirm = document.getElementById('confirm_password').value;
            if (password !== confirm) {
                alert("Passwords do not match.");
                return false;
            }
            return true;
        }

        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            field.type = field.type === "password" ? "text" : "password";
        }
    </script>
</body>
</html>
