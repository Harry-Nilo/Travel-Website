<?php
session_start();
$conn = new mysqli("localhost", "root", "", "compass_site");

if (!isset($_SESSION['user_id'])) {
    header("Location: CompassLogin.php");
    exit();
}

$userId = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT posts.*, users.username 
    FROM posts 
    JOIN users ON posts.user_id = users.id
    ORDER BY (posts.user_id = ?) DESC, posts.created_at DESC
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Travelogs</title>
<link rel="stylesheet" href="Travelog.css" />
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet" />
</head>
<body>

<div class="navbar">
    <div class="nav-container">
      <a href="#" class="logo"><img src="images/compass_logo.gif" alt="Logo"></a>
      <ul class="nav-links">
        <li><a href="CompassHome.html">Home</a></li>
        <li><a href="TripPlanner.php">TripPlanner</a></li>
        <li><a href="Destination.php">Destinations</a></li>
        <li><a href="Travelog.php">Travel Logs</a></li>
        <li><a href="CompassAccount.php">Account</a></li>
        <li><a href="CompassLogin.php">Log Out</a></li>
      </ul>
    </div>
  </div>

<div class="Travelog-container">
  <h1>Travelogs</h1>

  <?php if (isset($_GET['error']) && $_GET['error'] === 'unauthorized_delete'): ?>
  <p style="color:red; text-align:center;">❌ You can only delete your own posts.</p>
  <?php endif; ?>


  <div class="carousel-row">
    <div class="carousel">
      <button class="carousel-button prev">&#10094;</button>
      <div class="carousel-track">
        <?php while($row = $result->fetch_assoc()): ?>
          <div class="carousel-slide">
            <strong><?php echo htmlspecialchars($row['username']); ?> posted:</strong>
            <img 
              src="uploads/<?php echo htmlspecialchars($row['image']); ?>" 
              alt="Travel photo" 
              class="modal-trigger" 
              data-modal="<?php echo htmlspecialchars($row['experience_modal']); ?>"
            />
            <p><?php echo nl2br(htmlspecialchars($row['experience'])); ?></p>

            <?php if ($row['user_id'] == $userId): ?>
              <form method="POST" action="delete.php" onsubmit="return confirm('Delete this post?');" style="display:inline;">
                <input type="hidden" name="post_id" value="<?php echo $row['id']; ?>">
                <button type="submit" class="delete-btn">Delete</button>
              </form>
            <?php endif; ?>
          </div>
        <?php endwhile; ?>
      </div>
      <button class="carousel-button next">&#10095;</button>
    </div>

    <div class="travelog-options">
      <div class="travelog-box">
        <p><strong>New Member:</strong></p>
        <p>Keep track of your wildest adventures online! Start your personal Travelog.</p>
        <a href="#" class="go-btn" id="newMemberBtn">GO &gt;&gt;</a>

      </div>

      <div class="travelog-box">
        <p><strong>Current Member:</strong></p>
        <p>Click below to begin your journey.</p>
        <a href="upload.php" class="go-btn2">GO &gt;&gt;</a>
      </div>
    </div>
  </div>
</div>

<div id="experienceModal" class="modal">
  <div class="modal-content">
    <span class="modal-close">&times;</span>
    <h2 id="modalTitle" style="margin-top: 0;">Travel Experience:</h2>
  <div id="modalText"></div>

  </div>
</div>

<div class="photo-grid">
  <div class="photo-item">
    <img src="images/kayaking.jpg" alt="Photo 1">
    <div class="caption"><h3>Conquering the rapids on the Rutan Islands</h3></br>Definitely our craziest journey ever! A beautiful collage of nature. 
      Rapids reaching nearly 50 mph, more than a dozen waterfalls (various sizes), and some killer 
      rocks gave us the biggest rush. Nothing beats the feeling of complete loss of control! 
      The Rutang Islands also has a lighter, more relaxing side -- check out the local villages</div>
  </div>
  <div class="photo-item">
    <img src="images/climbing4.jpg" alt="Photo 2">
    <div class="caption"><h3>Scaling the mountains in Manurai.</h3></br>Some of the steepest cliffs around! My buddy and I began our 3 day 
    scale above the majestic raging waters of Nanna.</div>
  </div>
  <div class="photo-item">
    <img src="images/cycling2.jpg" alt="Photo 3">
    <div class="caption"><h3>Cycling the Irma coastline</h3></br>Beautiful scenery combined with steep inclines and fast roads allowed for some great cycling. Don;t forget the helmet!!</div>
  </div>
</div>

<footer class="footer">
    <p>Contact Us:</p>
    <p>travel.compass@gmail.com</p>
    <p>+639123456789</p>
</footer>

<script>

const track = document.querySelector('.carousel-track');
const slides = Array.from(track.children);
const nextButton = document.querySelector('.carousel-button.next');
const prevButton = document.querySelector('.carousel-button.prev');
let slideWidth = slides[0]?.getBoundingClientRect().width + 30 || 330;
let currentIndex = 0;

function moveToSlide(index) {
    if (index < 0) index = slides.length - 1;
    if (index >= slides.length) index = 0;
    currentIndex = index;
    track.style.transform = 'translateX(' + (-slideWidth * index) + 'px)';
}

nextButton.addEventListener('click', () => {
    moveToSlide(currentIndex + 1);
});

prevButton.addEventListener('click', () => {
    moveToSlide(currentIndex - 1);
});



window.addEventListener('resize', () => {
    slideWidth = slides[0]?.getBoundingClientRect().width + 30 || 330;
    moveToSlide(currentIndex);
});

const modal = document.getElementById('experienceModal');
const modalText = document.getElementById('modalText');
const closeBtn = modal.querySelector('.modal-close');
const modalTriggers = document.querySelectorAll('.modal-trigger');

modalTriggers.forEach(img => {
    img.addEventListener('click', () => {
        modalText.textContent = img.dataset.modal;
        modal.style.display = 'flex';
    });
});

closeBtn.addEventListener('click', () => {
    modal.style.display = 'none';
});

window.addEventListener('click', (e) => {
    if (e.target === modal) {
        modal.style.display = 'none';
    }
});

document.getElementById('newMemberBtn').addEventListener('click', function(event) {
  event.preventDefault(); 
  alert('You are already logged in.');
});


</script>

</body>
</html>
