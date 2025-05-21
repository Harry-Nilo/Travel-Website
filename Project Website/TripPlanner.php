<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="TripPlanner.css">
  <title>Compass Travel - Trip Planner</title>
</head>
<body>

  <!-- Replaced Header with Destinations-style Navbar -->
  <div class="navbar">
    <div class="nav-container">
      <a href="#" class="logo"><img src="images/compass_logo.gif" alt="Logo"></a>
      <ul class="nav-links">
        <li><a href="CompassHome.html">Home</a></li>
        <li><a href="TripPlanner.php">TripPlanner</a></li>
        <li><a href="Destination.html">Destinations</a></li>
        <li><a href="Travelog.html">Travel Logs</a></li>
        <li><a href="CompassAccount.php">Account</a></li>
        <li><a href="CompassLogin.php">Log Out</a></li>
      </ul>
      </ul>
    </div>
  </div>

  <section class="introduction">
    <h1>Adventure Planner</h1>
    <p>
      Either click a region on the map below or type it into the fields:
    </p>
    <div id="chartdiv" style="width: 100%; height: 500px; border-radius: 8px;"></div>
  </section>

  <!-- amCharts v5 CDN -->
  <script src="https://cdn.amcharts.com/lib/5/index.js"></script>
  <script src="https://cdn.amcharts.com/lib/5/map.js"></script>
  <script src="https://cdn.amcharts.com/lib/5/geodata/worldLow.js"></script>
  <script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>

  <script>
    am5.ready(function () {
      var root = am5.Root.new("chartdiv");
      root.setThemes([am5themes_Animated.new(root)]);

      var chart = root.container.children.push(
        am5map.MapChart.new(root, {
          projection: am5map.geoMercator(),
          panX: "translateX",
          panY: "translateY",
          wheelY: "zoom"
        })
      );

      var polygonSeries = chart.series.push(
        am5map.MapPolygonSeries.new(root, {
          geoJSON: am5geodata_worldLow,
          exclude: ["AQ"]
        })
      );

      polygonSeries.mapPolygons.template.setAll({
        tooltipText: "{name}",
        interactive: true
      });

      polygonSeries.mapPolygons.template.states.create("hover", {
        fill: am5.color(0xff0000)
      });

      polygonSeries.mapPolygons.template.events.on("click", function (ev) {
        var countryName = ev.target.dataItem.dataContext.name;
        alert("You selected: " + countryName);
        document.getElementById("country").value = countryName;
      });
    });
  </script>

  <!-- Start of Form -->
  <form id="tripForm">
    <section class="destination-section">
      <div class="destination-row">
        <img src="images/No1.gif" alt="Number 1" class="number-image">
        <div class="destination-content">
          <h2>Destination</h2>
          <p>Either select a region on the map above or type it into the fields below:</p>

          <div class="input-section">
            <label for="city">City or closest major city:</label>
            <input type="text" id="city" name="city" required>

            <label for="country">Country or Region:</label>
            <input type="text" id="country" name="country" required>
          </div>
        </div>
      </div>
    </section>

    <section class="activity-section">
      <div class="destination-row">
        <img src="images/No2.gif" alt="Number 2" class="number-image">
        <div class="destination-content">
          <h2>Activity</h2>
          <p>Tell us what kind of things you will be doing there:</p>

          <div class="checkbox-section">
            <label><input type="checkbox" name="activity[]" value="Hiking"> Hiking</label>
            <label><input type="checkbox" name="activity[]" value="Kayaking"> Kayaking</label>
            <label><input type="checkbox" name="activity[]" value="Fishing"> Fishing</label>
            <label><input type="checkbox" name="activity[]" value="Mountain Hiking"> Mountain Hiking</label>
            <label><input type="checkbox" name="activity[]" value="Skiing"> Skiing</label>
            <label><input type="checkbox" name="activity[]" value="Surfing"> Surfing</label>
          </div>
        </div>
      </div>
    </section>

    <section class="information-section">
      <div class="destination-row">
        <img src="images/No3.gif" alt="Number 3" class="number-image">
        <div class="destination-content">
          <h2>Information</h2>
          <p>What kind of information do you want about this trip:</p>

          <div class="checkbox-section">
            <label><input type="checkbox" name="info[]" value="Transportation"> Transportation</label>
            <label><input type="checkbox" name="info[]" value="Weather"> Weather</label>
            <label><input type="checkbox" name="info[]" value="Political Info"> Political Info</label>
            <label><input type="checkbox" name="info[]" value="Health"> Health</label>
            <label><input type="checkbox" name="info[]" value="Gear"> Gear</label>
            <label><input type="checkbox" name="info[]" value="Specific Activity"> Specific Activity</label>
          </div>
        </div>
      </div>
    </section>

    <section class="submit-section">
      <div class="destination-row">
        <img src="images/No4.gif" alt="Number 4" class="number-image">
        <div class="destination-content">
          <h2>Submit >></h2>
          <div class="submit-button-container">
            <button type="submit" class="submit-button">Submit</button>
          </div>
        </div>
      </div>
    </section>
  </form>
  <!-- End of Form -->

  <footer class="footer">
    @Compass Travel
  </footer>

  <!-- JavaScript for AJAX submission -->
  <script>
    document.getElementById('tripForm').addEventListener('submit', function (e) {
      e.preventDefault(); // Stop default form submission

      const formData = new FormData(this);

      fetch('save_trip.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.text())
      .then(result => {
        alert(result.trim());
        document.getElementById('tripForm').reset();
      })
      .catch(error => {
        alert('Error saving trip: ' + error);
      });
    });
  </script>
</body>
</html>

