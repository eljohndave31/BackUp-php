<?php

$servername = getenv('DB_HOST');
$username   = getenv('DB_USERNAME');  // ✅ Matches Coolify
$password   = getenv('DB_PASSWORD');  // ✅ Matches Coolify
$dbname     = getenv('DB_DATABASE');

try {
    $conn = new PDO("mysql:host=$servername;port=3306;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
