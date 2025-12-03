<?php
// Database configuration
// This connects to your MySQL database
// Now supports environment variables for Docker deployment!

// Get database credentials from environment variables (for production)
// Falls back to localhost/XAMPP values for local development
$host = getenv('DB_HOST') ?: 'localhost';
$dbname = getenv('DB_NAME') ?: 'todolist_db';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';

try {
    // Create PDO connection (think of this like creating a database client in JavaScript)
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Set error mode to exceptions (similar to try-catch in JS)
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Set default fetch mode to associative array
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    // In production, log the error instead of exposing it
    error_log("Database connection failed: " . $e->getMessage());
    die("Unable to connect to database. Please contact administrator.");
}

// Start session (like cookies in JavaScript for storing login state)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
