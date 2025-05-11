<?php
require 'Database.php';

$token = $_GET['token'] ?? ''; 
$new_password = trim($_POST['new_password'] ?? '');
$confirm_password = trim($_POST['confirm_password'] ?? '');
$errors = [];
$success = "";

if (empty($token)) {
    $errors[] = "Invalid or missing token.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($errors)) {
    if (!$new_password || !$confirm_password) {
        $errors[] = "Both password fields are required.";
    } elseif ($new_password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    } elseif (strlen($new_password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    } else {
        try {
            $pdo = getDatabaseConnection();

            $stmt = $pdo->prepare("SELECT * FROM users WHERE reset_token = :token AND reset_token_expiry > NOW()");
            $stmt->execute(['token' => $token]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

                $stmt = $pdo->prepare("UPDATE users SET password_hash = :password_hash, reset_token = NULL, reset_token_expiry = NULL WHERE id = :id");
                $stmt->execute([
                    'password_hash' => $hashed_password,
                    'id' => $user['id']
                ]);

                $success = "Your password has been reset successfully. You can now <a href='LogIn.php'>log in</a>.";
            } else {
                $errors[] = "Invalid or expired token.";
            }
        } catch (PDOException $e) {
            error_log("Reset Password error: " . $e->getMessage());
            $errors[] = "An error occurred. Please try again later.";
        }
    }
}
?>