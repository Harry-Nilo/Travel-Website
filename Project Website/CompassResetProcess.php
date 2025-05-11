<?php
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $new_password_plain = trim($_POST["new_password"]);

    // Password complexity check
    if (!preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\W_]).{8,}$/', $new_password_plain)) {
        echo "Password must be at least 8 characters long and include uppercase, lowercase, number, and special character.";
        exit;
    }

    $new_password = password_hash($new_password_plain, PASSWORD_DEFAULT);

    // Check if user exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->close();

        // Update password and reset failed_attempts
        $stmt = $conn->prepare("UPDATE users SET password = ?, failed_attempts = 0, locked_until = NULL WHERE email = ?");
        $stmt->bind_param("ss", $new_password, $email);
        if ($stmt->execute()) {
            echo "Password reset successful. <a href='CompassLogin.php'>Login here</a>.";
        } else {
            echo "Error updating password.";
        }
    } else {
        echo "No account found with that email.";
    }

    $stmt->close();
    $conn->close();
}
?>
