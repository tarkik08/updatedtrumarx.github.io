<?php
// Database Configuration
// Update these values with your actual cPanel database credentials

$db_host = 'localhost';
$db_name = 'YOUR_DATABASE_NAME'; // Update this
$db_user = 'YOUR_DATABASE_USER'; // Update this
$db_pass = 'YOUR_DATABASE_PASSWORD'; // Update this

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    
    // Set PDO to throw exceptions on error
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Set default fetch mode to associative array
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    // If connection fails, log error and stop script
    // In production, you might not want to show the full error to the user
    error_log("Database Connection Failed: " . $e->getMessage());
    die(json_encode(['success' => false, 'message' => 'Database connection failed.']));
}
?>
