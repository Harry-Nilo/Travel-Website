<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "Unauthorized access. Please log in.";
    exit;
}

// Database credentials
$host = "localhost";
$user = "root";
$password = "";
$dbname = "compass_site";

// Connect to MySQL
$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user ID from session
$user_id = $_SESSION['user_id'];

// Get POST data
$city = $_POST['city'] ?? '';
$country = $_POST['country'] ?? '';
$activities = isset($_POST['activity']) ? implode(', ', $_POST['activity']) : '';
$info_types = isset($_POST['info']) ? implode(', ', $_POST['info']) : '';

// Insert into database
$stmt = $conn->prepare("INSERT INTO plans (city, country, activities, info_types, user_id) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssssi", $city, $country, $activities, $info_types, $user_id);

if ($stmt->execute()) {
    echo "Trip plan saved successfully!";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>






