<?php
session_start();
require 'Database.php';

if (!isset($_SESSION['user'])) {
    echo "unauthorized";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['trip_id'])) {
    $tripId = intval($_POST['trip_id']);
    $username = $_SESSION['user'];

    try {
        $pdo = getDatabaseConnection();

        // Get user ID from session username
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            echo "user_not_found";
            exit();
        }

        $userId = $user['id'];

        // Ensure user owns the trip before deletion
        $stmt = $pdo->prepare("DELETE FROM plans WHERE id = ? AND user_id = ?");
        $stmt->execute([$tripId, $userId]);

        if ($stmt->rowCount() > 0) {
            echo "success";
        } else {
            echo "not_found";
        }

    } catch (PDOException $e) {
        echo "error";
    }
} else {
    echo "invalid_request";
}
