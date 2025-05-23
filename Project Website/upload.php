<?php
session_start();
$conn = new mysqli("localhost", "root", "", "compass_site");

if (!isset($_SESSION["user_id"])) {
    die("You must be logged in to upload.");
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $experience = $_POST['experience'];
    $experience_modal = $_POST['experience_modal'];
    $user_id = $_SESSION["user_id"];

    $imageName = basename($_FILES['photo']['name']);
    $imageTmp = $_FILES['photo']['tmp_name'];
    $uploadDir = "uploads/";
    $uploadPath = $uploadDir . $imageName;

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    if (move_uploaded_file($imageTmp, $uploadPath)) {
        $stmt = $conn->prepare("INSERT INTO posts (image, experience, experience_modal, user_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $imageName, $experience, $experience_modal, $user_id);

        if ($stmt->execute()) {
            header("Location: travelog.php");
            exit;
        } else {
            $message = "❌ Database error: " . $stmt->error;
        }
    } else {
        $message = "❌ Failed to upload image.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Upload New Post</title>
<link rel="stylesheet" href="Travelog.css" />
</head>
<body>

<nav class="navbar">
  <div class="nav-container">
    <a href="travelog.php" class="logo"><img src="./images/compass_logo.gif" alt="Logo" /></a>
    <ul class="nav-links">
      <li><a href="CompassHome.php">Home</a></li>
      <li><a href="TripPlanner.php">TripPlanner</a></li>
      <li><a href="Destination.php">Destinations</a></li>
      <li><a href="Travelog.php">Travel Logs</a></li>
      <li><a href="CompassAccount.php">Account</a></li>
      <li><a href="logout.php">Log Out</a></li>
    </ul>
  </div>
</nav>

<div class="Travelog-container">
  <h1>Share Your Journey</h1>
  <?php if ($message): ?>
    <p style="color:red; text-align:center;"><?php echo htmlspecialchars($message); ?></p>
  <?php endif; ?>
  <form action="upload.php" method="POST" enctype="multipart/form-data" style="max-width:500px; margin: 0 auto;">
    <label for="photo" style="display:block; margin-bottom: 10px; font-weight:bold;">Select photo:</label>
    <input type="file" name="photo" id="photo" required style="margin-bottom: 20px; width: 100%;" />

    <label for="experience" style="display:block; margin-bottom: 10px; font-weight:bold;">Write a caption:</label>
    <textarea name="experience" id="experience" rows="3" required style="width: 100%; margin-bottom: 20px;"></textarea>

    <label for="experience_modal" style="display:block; margin-bottom: 10px; font-weight:bold;">Share your travel experiences:</label>
    <textarea name="experience_modal" id="experience_modal" rows="3" required style="width: 100%; margin-bottom: 20px;"></textarea>

    <button type="submit" class="upload-btn">Upload</button>
  </form>
</div>

</body>
</html>
