<?php
session_start();
require 'Database.php';

if (!isset($_SESSION['user'])) {
    header("Location: CompassLogin.php");
    exit();
}

$pdo = getDatabaseConnection();
$username = $_SESSION['user'];

try {
    $stmt = $pdo->prepare("SELECT username, email FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "User not found.";
        exit();
    }
} catch (PDOException $e) {
    echo "Error fetching user: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="CompassAccount.css" />
  <title>Compass Travel - Account</title>
</head>
<body>

<div class="navbar">
  <div class="nav-container">
    <a href="#" class="logo"><img src="images/compass_logo.gif" alt="Logo" /></a>
    <ul class="nav-links">
      <li><a href="CompassHome.html">Home</a></li>
      <li><a href="TripPlanner.php">TripPlanner</a></li>
      <li><a href="Destination.html">Destinations</a></li>
      <li><a href="Travelog.html">Travel Logs</a></li>
      <li><a href="CompassAccount.php">Account</a></li>
      <li><a href="CompassLogin.php">Log Out</a></li>
    </ul>
  </div>
</div>

<div class="account-info">
  <?php if ($user): ?>
    <h2>Account Details</h2>
    <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
    <p><a href="CompassForgetPass.php" class="change-password-link">Change password</a></p>
  <?php else: ?>
    <p class="error"><strong>Error:</strong> <?php echo htmlspecialchars($error); ?></p>
  <?php endif; ?>
</div>

<div class="user-trips">
  <h2>Your Planned Trips</h2>
  <?php
  try {
      $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
      $stmt->execute([$username]);
      $userRow = $stmt->fetch(PDO::FETCH_ASSOC);
      $userId = $userRow['id'];

      // ✅ Query updated to use plans table inside user_auth (no more trip_planner_db.)
      $stmt = $pdo->prepare("SELECT id, country, city, activities, info_types, submitted_at FROM plans WHERE user_id = ? ORDER BY submitted_at DESC");
      $stmt->execute([$userId]);
      $trips = $stmt->fetchAll(PDO::FETCH_ASSOC);

      if ($trips):
          foreach ($trips as $trip):
  ?>
      <div id="trip-<?php echo $trip['id']; ?>" class="trip-entry">
        <p><strong>Country:</strong> <?php echo htmlspecialchars($trip['country']); ?></p>
        <p><strong>City:</strong> <?php echo htmlspecialchars($trip['city']); ?></p>
        <p><strong>Activities:</strong> <?php echo nl2br(htmlspecialchars($trip['activities'])); ?></p>
        <p><strong>Info Types:</strong> <?php echo nl2br(htmlspecialchars($trip['info_types'])); ?></p>
        <p><small><em>Submitted at: <?php echo $trip['submitted_at']; ?></em></small></p>
        <button onclick="deleteTrip(<?php echo $trip['id']; ?>)" class="delete-trip-button">Remove</button>
      </div>
  <?php
          endforeach;
      else:
          echo "<p>No trips submitted yet.</p>";
      endif;
  } catch (PDOException $e) {
      echo "<p class='error'>Error retrieving trips: " . htmlspecialchars($e->getMessage()) . "</p>";
  }
  ?>
</div>

<script>
function deleteTrip(tripId) {
  if (!confirm("Are you sure you want to delete this trip?")) return;

  fetch('delete_trip.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'trip_id=' + encodeURIComponent(tripId)
  })
  .then(response => response.text())
  .then(result => {
    if (result.trim() === 'success') {
      const tripDiv = document.getElementById('trip-' + tripId);
      if (tripDiv) tripDiv.remove();
      alert('Trip deleted successfully.');
    } else {
      alert('Failed to delete trip. Please try again.');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('An error occurred while deleting the trip.');
  });
}
</script>

</body>
</html>
