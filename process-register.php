<?php
require_once 'config.php';

// Check if form was submitted (similar to checking if POST request in Express.js)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Get form data (like req.body in Express.js)
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    // Basic validation
    if (empty($username) || empty($password)) {
        header("Location: daftar.php?error=empty");
        exit();
    }
    
    // Check if username already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    
    if ($stmt->rowCount() > 0) {
        header("Location: daftar.php?error=exists");
        exit();
    }
    
    // Hash password (like bcrypt in JavaScript)
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new user
    $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'user')");
    
    if ($stmt->execute([$username, $hashed_password])) {
        // Registration successful, redirect to login
        header("Location: masuk.php?success=registered");
        exit();
    } else {
        header("Location: daftar.php?error=failed");
        exit();
    }
}
?>
