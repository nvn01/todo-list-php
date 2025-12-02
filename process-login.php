<?php
require_once 'config.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Get form data
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    // Basic validation
    if (empty($username) || empty($password)) {
        header("Location: masuk.php?error=empty");
        exit();
    }
    
    // Find user by username
    $stmt = $pdo->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Verify user exists and password is correct
    if ($user && password_verify($password, $user['password'])) {
        // Login successful - create session (like setting JWT or session cookie in JS)
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        
        // Redirect to dashboard
        header("Location: dashboard.php");
        exit();
    } else {
        // Login failed
        header("Location: masuk.php?error=invalid");
        exit();
    }
}
?>
