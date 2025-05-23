<?php
require 'config.php';
session_start();

$username_email = $_POST["username_email"];
$password = $_POST["password"];

$stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
$stmt->bind_param("ss", $username_email, $username_email);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    if (!is_null($user["locked_until"]) && strtotime($user["locked_until"]) > time()) {
        $remaining = ceil((strtotime($user["locked_until"]) - time()) / 60);
        header("Location: CompassLogin.php?error=" . urlencode("Account is locked. Try again in $remaining minute(s)."));
        exit;
    }

    if (password_verify($password, $user["password"])) {
        $stmt = $conn->prepare("UPDATE users SET failed_attempts = 0, locked_until = NULL WHERE id = ?");
        $stmt->bind_param("i", $user["id"]);
        $stmt->execute();

        $_SESSION["user"] = $user["username"];
        $_SESSION["user_id"] = $user["id"];
        header("Location: CompassHome.html");
        exit;
    } else {
        $failed_attempts = $user["failed_attempts"] + 1;

        if ($failed_attempts >= 3) {
            $lock_time = date("Y-m-d H:i:s", time() + (5 * 60));
            $stmt = $conn->prepare("UPDATE users SET failed_attempts = ?, locked_until = ? WHERE id = ?");
            $stmt->bind_param("isi", $failed_attempts, $lock_time, $user["id"]);
        } else {
            $stmt = $conn->prepare("UPDATE users SET failed_attempts = ? WHERE id = ?");
            $stmt->bind_param("ii", $failed_attempts, $user["id"]);
        }

        $stmt->execute();
        header("Location: CompassLogin.php?error=" . urlencode("Invalid credentials. Attempt $failed_attempts of 3."));
        exit;
    }
} else {
    header("Location: CompassLogin.php?error=" . urlencode("User not found."));
    exit;
}
?>

