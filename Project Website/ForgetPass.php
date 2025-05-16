<?php
require 'Database.php';
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

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

            // Check if the email exists in the database
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Generate a secure code and expiry time
                $resetCode = strtoupper(substr(bin2hex(random_bytes(4)), 0, 8)); // 8-char code
                date_default_timezone_set('Asia/Manila');
                $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

                error_log("Expiry being set: " . $expiry);

                // Update the user's reset code and expiry in the database
                $stmt = $pdo->prepare("UPDATE users SET reset_token = :token, reset_token_expiry = :expiry WHERE email = :email");
                $stmt->execute([
                    'token' => $resetCode,
                    'expiry' => $expiry,
                    'email' => $email
                ]);

                // Email subject and body (just the code)
                $subject = "Your Password Reset Code";
                $message = "
                <html>
                <head>
                    <title>Password Reset Code</title>
                </head>
                <body>
                    <p>Hello,</p>
                    <p>Your password reset code is:</p>
                    <h2 style='color:#007BFF;'>$resetCode</h2>
                    <p>This code will expire in 1 hour.</p>
                    <p>If you did not request this, please ignore this email.</p>
                </body>
                </html>
                ";

                // Send the email using PHPMailer
                $mail = new PHPMailer(true);

                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'travl.compass@gmail.com';
                    $mail->Password = 'wqko jxoc vqjz xedm';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    $mail->setFrom('travl.compass@gmail.com', 'Your Website');
                    $mail->addAddress($email);
                    $mail->isHTML(true);
                    $mail->Subject = $subject;
                    $mail->Body = $message;

                    $mail->send();
                    $success = "A password reset code has been sent to your email.";
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f4;
        }
        .forgot-container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 300px;
        }
        .forgot-container h1 {
            margin-bottom: 20px;
            font-size: 24px;
            text-align: center;
        }
        .forgot-container input {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .forgot-container button {
            width: 100%;
            padding: 10px;
            background: #007BFF;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .forgot-container button:hover {
            background: #0056b3;
        }
        .error {
            color: red;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .success {
            color: green;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .reset-link {
            display: block;
            text-align: center;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="forgot-container">
        <h1>Forgot Password</h1>
        <?php if (!empty($errors)): ?>
            <div class="error">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="success">
                <p><?php echo htmlspecialchars($success); ?></p>
                <a class="reset-link" href="ResetPass.php">Enter Reset Code</a>
            </div>
        <?php endif; ?>
        <form method="POST" action="">
            <input type="email" name="email" placeholder="Enter your email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
            <button type="submit">Send Reset Code</button>
        </form>
    </div>
</body>
</html>