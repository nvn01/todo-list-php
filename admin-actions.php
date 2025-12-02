<?php
require_once 'config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id'])) {
    header("Location: masuk.php");
    exit();
}

$stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || $user['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

// Handle admin actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    
    if ($action === 'delete_user') {
        $user_id = $_POST['user_id'];
        
        // Delete user (todos will be deleted automatically due to foreign key CASCADE)
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'user'");
        $stmt->execute([$user_id]);
        
        header("Location: admin.php?success=user_deleted");
        exit();
    }
}

// Redirect back if accessed directly
header("Location: admin.php");
exit();
?>
