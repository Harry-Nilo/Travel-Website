<?php
session_start();

$conn = new mysqli("localhost", "root", "", "compass_site");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION["user_id"])) {
    die("You must be logged in to delete posts.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = intval($_POST['post_id']);
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("SELECT image FROM posts WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $post_id, $user_id);
    $stmt->execute();
    $stmt->bind_result($image);
    if ($stmt->fetch()) {
        $stmt->close();

        $imagePath = "uploads/" . $image;
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }

        $delStmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
        $delStmt->bind_param("i", $post_id);
        $delStmt->execute();
        $delStmt->close();

        header("Location: travelog.php");
        exit();
    } else {
        header("Location: travelog.php?error=unauthorized_delete");
        exit();
    }
}
?>
