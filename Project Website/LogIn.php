<?php 
require 'Database.php';
session_start();

$username_or_email = trim($_POST['username_or_email'] ?? '');
$password = trim($_POST['password'] ?? '');
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$username_or_email || !$password) {
        $errors[] = "Both fields are required.";
    } else {
        try {
            $pdo = getDatabaseConnection(); 

            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username OR email = :email");
            $stmt->execute(['username' => $username_or_email, 'email' => $username_or_email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                if ($user['lock_until'] && strtotime($user['lock_until']) > time()) {
                    $errors[] = "Your account is locked. Try again later.";
                } elseif (password_verify($password, $user['password'])) {
                    $stmt = $pdo->prepare("UPDATE users SET failed_attempts = 0, lock_until = NULL WHERE id = :id");
                    $stmt->execute(['id' => $user['id']]);

                    session_regenerate_id(true); 
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    header("Location: dashboard.php"); 
                    exit;
                } else {
                    $failed_attempts = $user['failed_attempts'] + 1;
                    $lock_until = null;

                    if ($failed_attempts >= 3) {
                        $lock_until = date('Y-m-d H:i:s', strtotime('+15 minutes')); 
                        $errors[] = "Your account is locked due to multiple failed login attempts. Try again later.";
                    } else {
                        $errors[] = "Invalid username/email or password.";
                    }

                    $stmt = $pdo->prepare("UPDATE users SET failed_attempts = :failed_attempts, lock_until = :lock_until WHERE id = :id");
                    $stmt->execute([
                        'failed_attempts' => $failed_attempts,
                        'lock_until' => $lock_until,
                        'id' => $user['id']
                    ]);
                }
            } else {
                $errors[] = "Invalid username/email or password.";
            }
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            $errors[] = "An error occurred. Please try again later.";
        }
    }
}
?>