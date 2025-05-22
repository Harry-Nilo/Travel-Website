<?php
header('Content-Type: application/json');
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password_plain = trim($_POST["password"]);
    $confirm_password = trim($_POST["confirm_password"]);

    // Password match check
    if ($password_plain !== $confirm_password) {
        echo json_encode(['status' => 'error', 'message' => 'Passwords do not match.']);
        exit;
    }

    // Validate password complexity
    if (!preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\W_]).{8,}$/', $password_plain)) {
        echo json_encode(['status' => 'error', 'message' => 'Password must be at least 8 characters long and include uppercase, lowercase, number, and special character.']);
        exit;
    }

    $password = password_hash($password_plain, PASSWORD_DEFAULT);

    // Check for duplicate username or email
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Username or Email already exists.']);
        exit;
    }
    $stmt->close();

    // Insert new user into the database
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $password);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Registered successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error: Could not register user.']);
    }
    $stmt->close();
    $conn->close();
}
?>
