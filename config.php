<?php
// Database configuration
// This connects to your MySQL database

$host = 'localhost';        // Database host (usually localhost for XAMPP)
$dbname = 'todolist_db';    // Database name
$username = 'root';         // MySQL username (default is 'root' in XAMPP)
$password = '';             // MySQL password (default is empty in XAMPP)

try {
    // Create PDO connection (think of this like creating a database client in JavaScript)
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    
    // Set error mode to exceptions (similar to try-catch in JS)
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Start session (like cookies in JavaScript for storing login state)
session_start();
?>
