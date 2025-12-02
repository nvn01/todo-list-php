<?php
require_once 'config.php';

// Delete existing admin user
$stmt = $pdo->prepare("DELETE FROM users WHERE username = 'admin'");
$stmt->execute();

// Create admin with proper password hash
$username = 'admin';
$password = 'TomatoPotato01!';
$role = 'admin';

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
$stmt->execute([$username, $hashed_password, $role]);

echo "✅ Admin user created successfully!<br><br>";
echo "Username: <strong>admin</strong><br>";
echo "Password: <strong>TomatoPotato01!</strong><br><br>";
echo "You can now <a href='masuk.php'>login here</a><br><br>";
echo "<hr>";
echo "Generated hash: " . $hashed_password;
?>
