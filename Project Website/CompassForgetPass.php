<?php
session_start();  

require 'Database.php';
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$email = trim($_POST['email'] ?? '');
$errors = [];


$success = $_SESSION['success'] ?? '';
unset($_SESSION['success']);

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
                
                $resetCode = strtoupper(substr(bin2hex(random_bytes(4)), 0, 8)); 
                date_default_timezone_set('Asia/Manila');
                $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

                
                $stmt = $pdo->prepare("UPDATE users SET reset_token = :token, reset_token_expiry = :expiry WHERE email = :email");
                $stmt->execute([
                    'token' => $resetCode,
                    'expiry' => $expiry,
                    'email' => $email
                ]);

               
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

                
                $mail = new PHPMailer(true);

                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'travl.compass@gmail.com';
                    $mail->Password = 'wqko jxoc vqjz xedm';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    $mail->setFrom('noreply@CompassTravel.com', 'Compass Travel Reset Code');
                    $mail->addAddress($email);
                    $mail->isHTML(true);
                    $mail->Subject = $subject;
                    $mail->Body = $message;

                    $mail->send();

                    
                    $_SESSION['success'] = "A password reset code has been sent to your email.";
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit();

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
    <meta charset="UTF-8" />
    <title>Forgot Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
body {
    font-family: 'Arial', sans-serif;
    background: url('images/WebsiteBG.jpeg') no-repeat center center fixed;
    background-size: cover;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.forgot-container {
    background: rgba(0, 0, 0, 0.5); 
    padding: 30px;
    border-radius: 10px;
    border: 2px solid #333; 
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 400px;
    animation: fadeIn 0.6s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

h1 {
    text-align: center;
    margin-bottom: 25px;
    color: #F5C45E;
}

input {
    width: 93%;
    padding: 12px;
    margin-bottom: 20px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 16px;
}

button {
    width: 100%;
    padding: 12px;
    background-color: #F5C45E;
    border: none;
    border-radius: 8px;
    color: white;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.3s;
}

button:hover {
    background-color: rgb(245, 161, 5);
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

.reset-link {
    display: block;
    text-align: center;
    margin-top: 15px;
    font-weight: bold;
    color: #F5C45E;
}

.reset-link:hover {
    text-decoration: underline;
}

    </style>
</head>
<body>
<div class="forgot-container">
    <h1>Forgot Password</h1>

    <?php if (!empty($errors)): ?>
        <div class="error">
            <?php foreach ($errors as $error): ?>
                <p><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="success">
            <p><?= htmlspecialchars($success) ?></p>
            <a class="reset-link" href="CompassResetPass.php">Enter Reset Code</a>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <input type="email" name="email" placeholder="Enter your email" value="<?= htmlspecialchars($email) ?>" required />
        <button type="submit">Send Reset Code</button>
    </form>
</div>
</body>
</html>
