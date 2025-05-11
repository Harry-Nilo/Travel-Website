<?php
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password_plain = trim($_POST["password"]);
    $confirm_password = trim($_POST["confirm_password"]);

    if ($password_plain !== $confirm_password) {
        echo "Passwords do not match.";
        exit;
    }

    if (!preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\W_]).{8,}$/', $password_plain)) {
        echo "Password must be at least 8 characters long and include uppercase, lowercase, number, and special character.";
        exit;
    }

    $password = password_hash($password_plain, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "Error: Username or Email already exists.";
        exit;
    }
    $stmt->close();

    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $password);

    if ($stmt->execute()) {
        header("Location: CompassLogin.php?success=registered");
    } else {
        echo "Error: Could not register user.";
    }
    $stmt->close();
    $conn->close();
}
?>
