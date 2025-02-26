<?php
// Database Configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "personal_data";

// Create connection using MySQLi
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection and display user-friendly message if it fails
if ($conn->connect_error) {
    die("<h3 style='color:red; text-align:center;'>Database Connection Failed: " . $conn->connect_error . "</h3>");
}

// Set character encoding to avoid issues with special characters
$conn->set_charset("utf8");
?>
