<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "Unauthorized access. Please log in.";
    exit;
}

$host = "localhost";
$user = "root";
$password = "";
$dbname = "compass_site";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

$city = $_POST['city'] ?? '';
$country = $_POST['country'] ?? '';
$activities = isset($_POST['activity']) ? implode(', ', $_POST['activity']) : '';
$info_types = isset($_POST['info']) ? implode(', ', $_POST['info']) : '';

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





