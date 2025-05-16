<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require 'Database.php';

$email = trim($_POST['email'] ?? '');
$resetCode = trim($_POST['reset_code'] ?? '');
$new_password = trim($_POST['new_password'] ?? '');
$confirm_password = trim($_POST['confirm_password'] ?? '');
$errors = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$email || !$resetCode || !$new_password || !$confirm_password) {
        $errors[] = "All fields are required.";
    } elseif ($new_password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    } elseif (strlen($new_password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    } else {
        try {
            $pdo = getDatabaseConnection();
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email AND reset_token = :token AND reset_token_expiry > NOW()");
            $stmt->execute([
                'email' => $email,
                'token' => $resetCode
            ]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password_plain = :password_plain, reset_token = NULL, reset_token_expiry = NULL WHERE id = :id");
                $stmt->execute([
                    'password_plain' => $hashed_password,
                    'id' => $user['id']
                ]);
                $success = "Your password has been reset successfully. You can now <a href='LogIn.php'>log in</a>.";
            } else {
                $errors[] = "Invalid or expired reset code.";
            }
        } catch (PDOException $e) {
            error_log("Reset code error: " . $e->getMessage());
            $errors[] = "An error occurred. Please try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Enter Reset Code</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .reset-container { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); width: 320px; }
        .reset-container h1 { text-align: center; }
        .reset-container input { width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 4px; }
        .reset-container button { width: 100%; padding: 10px; background: #007BFF; color: #fff; border: none; border-radius: 4px; cursor: pointer; }
        .reset-container button:hover { background: #0056b3; }
        .error { color: red; font-size: 14px; margin-bottom: 10px; }
        .success { color: green; font-size: 14px; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="reset-container">
        <h1>Reset Password</h1>
        <?php if (!empty($errors)): ?>
            <div class="error">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="success">
                <p><?php echo $success; ?></p>
            </div>
        <?php else: ?>
        <form method="POST" action="">
            <input type="email" name="email" placeholder="Your email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
            <input type="text" name="reset_code" placeholder="Reset code" value="<?php echo htmlspecialchars($resetCode ?? ''); ?>" required>
            <input type="password" name="new_password" placeholder="New password" required>
            <input type="password" name="confirm_password" placeholder="Confirm new password" required>
            <button type="submit">Reset Password</button>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>