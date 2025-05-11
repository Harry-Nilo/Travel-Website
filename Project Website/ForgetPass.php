<?php
require 'Database.php';
require 'path/to/PHPMailer/src/Exception.php';
require 'path/to/PHPMailer/src/PHPMailer.php';
require 'path/to/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$email = trim($_POST['email'] ?? '');
$errors = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$email) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    } else {
        try {
            $pdo = getDatabaseConnection();

            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $token = bin2hex(random_bytes(32));
                $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

                $stmt = $pdo->prepare("UPDATE users SET reset_token = :token, reset_token_expiry = :expiry WHERE email = :email");
                $stmt->execute([
                    'token' => $token,
                    'expiry' => $expiry,
                    'email' => $email
                ]);

                $resetLink = "https://yourdomain.com/ResetPassword.php?token=$token";
                $subject = "Password Reset Request";
                $message = "
                <html>
                <head>
                    <title>Password Reset Request</title>
                </head>
                <body>
                    <p>Hello,</p>
                    <p>Click the link below to reset your password:</p>
                    <p><a href='$resetLink'>$resetLink</a></p>
                    <p>If you did not request this, please ignore this email.</p>
                </body>
                </html>
                ";

                $mail = new PHPMailer(true);

                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com'; // Replace with your SMTP server
                    $mail->SMTPAuth = true;
                    $mail->Username = 'compss.site@gmail.com'; // SMTP username
                    $mail->Password = 'iked qugw ymie vjsm'; // SMTP password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    $mail->setFrom('no-reply@yourdomain.com', 'Your Website');
                    $mail->addAddress($email);
                    $mail->isHTML(true);
                    $mail->Subject = $subject;
                    $mail->Body = $message;

                    $mail->send();
                    $success = "A password reset link has been sent to your email.";
                } catch (Exception $e) {
                    error_log("PHPMailer error: " . $mail->ErrorInfo);
                    $errors[] = "Failed to send the email. Please try again later.";
                }
            } else {
                $errors[] = "No account found with that email.";
            }
        } catch (PDOException $e) {
            error_log("Forgot Password error: " . $e->getMessage());
            $errors[] = "An error occurred. Please try again later.";
        }
    }
}
?>