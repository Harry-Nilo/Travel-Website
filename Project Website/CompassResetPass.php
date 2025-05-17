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
                $stmt = $pdo->prepare("UPDATE users SET password = :password, reset_token = NULL, reset_token_expiry = NULL WHERE id = :id");
                $stmt->execute([
                'password' => $hashed_password,
                'id' => $user['id']
            ]);

                $success = "Your password has been reset successfully. You can now <a href='CompassLogin.php'>log in</a>.";
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
    <title>Reset Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
       body {
    font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: url('images/CompassBG.jpeg') no-repeat center center fixed;
    background-size: cover;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.reset-container {
     background-color: rgba(214, 240, 244, 0.7);
    padding: 30px;
    border-radius: 10px;
    border: 2px solid #333; /* Dark border */
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
    width: 100%;
    max-width: 400px;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

h1 {
    text-align: center;
    margin-bottom: 20px;
    color: #333;
}

input {
    width: 93%;
    padding: 12px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 16px;
}

button {
    width: 100%;
    padding: 12px;
    background-color: #007BFF;
    border: none;
    border-radius: 6px;
    color: #fff;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.3s;
}

button:hover {
    background-color: #0056b3;
}

.error, .success {
    font-size: 14px;
    margin-bottom: 15px;
    padding: 10px;
    border-radius: 6px;
}

.error {
    background-color: #f8d7da;
    color: #721c24;
}

.success {
    background-color: #d4edda;
    color: #155724;
}
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
