<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Featured Destinations with Search</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="Destination.css" />
</head>
<body style="background-image: url('images/WebsiteBG.jpeg'); background-size: cover;">

  <nav class="navbar">
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
  </nav>

  <div class="main-content">
    <h1>Featured Destinations</h1>
    <div class="details-container">
      <h3>Compass works hard to bring you the best possible trips for your rugged lifestyle. Here you'll find our latest travel packages suited for the adventurous spirit.</h3>
    </div>
  </div>

  <div class="search-filter-container">
    <div class="filter-dropdown">
      <select onchange="filterByCategory(this.value)">
        <option value="all">All</option>
        <option value="surf">Surf</option>
        <option value="bike">Bike</option>
        <option value="climb">Climb</option>
      </select>
    </div>
    <div class="search-bar">
      <input type="text" id="searchInput" placeholder="Search destinations..." autocomplete="off" />
      <div id="dropdownResults" class="dropdown-results"></div>
    </div>
  </div>

  <div class="grid-container">
    <div class="box hover-box" data-title="California Surfing Safari" data-description="Explore the beaches of California." data-category="surf">
      <img src="images/surfing3.jpg" alt="California Surfing Safari">
      <div class="image-details">
        <h3>California Surfing Safari</h3>
      </div>
      <div class="hover-detail">
        <p><h3>Be ready to go on a moment's notice. We will call you when the surf is pumping and fly you out for five mornings of hurricane 
          inspired summertime southern swells</h3></p> <h2>$960</br>includes lodging, food and airfare.</h2>
          <button><a href="Travelog.php">More Details</a></button>
      </div>
    </div>

    <div class="box hover-box" data-title="Bike New Zealand" data-description="Cycle through beautiful New Zealand." data-category="bike">
      <img src="images/biking22.jpg" alt="Bike New Zealand">
      <div class="image-details">
        <h3>Bike New Zealand</h3>
      </div>
      <div class="hover-detail">
        <p><h3>Beautiful scenery combined with steep inclines and fast roads allowed for some great cycling. Don't forget the helmet!!</h3></p> 
        <h2>$1490</br>includes lodging, food and airfare.</h2>
        <button><a href="Travelog.php">More Details</a></button>
      </div>
    </div>

    <div class="box hover-box" data-title="Devil's Tower Rock Climb" data-description="Climb the famous Devil's Tower." data-category="climb">
      <img src="images/climbing.jpg" alt="Devil's Tower Rock Climb">
      <div class="image-details">
        <h3>Devil's Tower Rock Climb</h3>
      </div>
      <div class="hover-detail">
        <p><h3>In this three day trip you'll scale the majestic cliffs of beautiful Devil's Tower Wyoming.</h3></p>
        <h2>$1490</br>includes lodging, food and airfare.</h2>
        <button><a href="Travelog.php">More Details</a></button>
      </div>
    </div>
  </div>

<footer class="footer">
  <p>Contact Us:</p>
  <p>travel.compass@gmail.com</p>
  <p>+639123456789</p>
</footer>


  <script>
    const searchInput = document.getElementById('searchInput');
    const dropdownResults = document.getElementById('dropdownResults');
    const boxes = document.querySelectorAll('.box.hover-box');

    searchInput.addEventListener('input', function () {
      const query = this.value.trim().toLowerCase();
      dropdownResults.innerHTML = '';

      if (query === '') {
        dropdownResults.style.display = 'none';
        boxes.forEach(box => box.style.display = 'block');
        return;
      }

      let found = false;

      boxes.forEach(box => {
        const title = box.dataset.title;
        const description = box.dataset.description;
        const imgSrc = box.querySelector('img').src;

        if (title.toLowerCase().includes(query) || description.toLowerCase().includes(query)) {
          const item = document.createElement('div');
          item.className = 'dropdown-item';

          item.innerHTML = `
          <img src="${imgSrc}" alt="${title}">
          <div class="item-details">
            <h4>${title}</h4>
          </div>
         `;

          item.addEventListener('click', () => {
            boxes.forEach(b => {
              b.style.display = (b.dataset.title === title) ? 'block' : 'none';
            });
            dropdownResults.style.display = 'none';
            searchInput.value = title;
          });

          dropdownResults.appendChild(item);
          found = true;
        }
      });

      dropdownResults.style.display = found ? 'block' : 'none';
    });

    document.addEventListener('click', (e) => {
      if (!e.target.closest('.search-bar')) {
        dropdownResults.style.display = 'none';
      }
    });

    function filterByCategory(category) {
      boxes.forEach(box => {
        const boxCategory = box.dataset.category;
        if (category === 'all' || boxCategory === category) {
          box.style.display = 'block';
        } else {
          box.style.display = 'none';
        }
      });
      searchInput.value = '';
      dropdownResults.style.display = 'none';
    }


  </script>
</body>
</html>
