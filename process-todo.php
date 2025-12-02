<?php
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: masuk.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    $user_id = $_SESSION['user_id'];
    
    // Add new todo
    if ($action === 'add') {
        $title = trim($_POST['title']);
        
        if (!empty($title)) {
            $stmt = $pdo->prepare("INSERT INTO todos (user_id, title, status, priority) VALUES (?, ?, 'Belum', 'mendesak')");
            $stmt->execute([$user_id, $title]);
        }
    }
    
    // Toggle completion
    elseif ($action === 'toggle') {
        $todo_id = $_POST['todo_id'];
        
        // First check if todo belongs to this user
        $stmt = $pdo->prepare("SELECT is_completed, status FROM todos WHERE id = ? AND user_id = ?");
        $stmt->execute([$todo_id, $user_id]);
        $todo = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($todo) {
            $new_completed = !$todo['is_completed'];
            
            // If checking (completing), set status to Selesai
            // If unchecking (not completed), set status to Belum
            $new_status = $new_completed ? 'Selesai' : 'Belum';
            
            $stmt = $pdo->prepare("UPDATE todos SET is_completed = ?, status = ? WHERE id = ? AND user_id = ?");
            $stmt->execute([$new_completed, $new_status, $todo_id, $user_id]);
        }
    }
    
    // Delete todo
    elseif ($action === 'delete') {
        $todo_id = $_POST['todo_id'];
        
        $stmt = $pdo->prepare("DELETE FROM todos WHERE id = ? AND user_id = ?");
        $stmt->execute([$todo_id, $user_id]);
    }
    
    // Update todo status (for drag & drop)
    elseif ($action === 'update_status') {
        $todo_id = $_POST['todo_id'];
        $status = $_POST['status'];
        
        // Validate status
        $valid_statuses = ['Belum', 'Proses', 'Selesai'];
        if (in_array($status, $valid_statuses)) {
            // If status is Done, mark as completed. Otherwise, mark as not completed
            $is_completed = ($status === 'Selesai') ? 1 : 0;
            
            $stmt = $pdo->prepare("UPDATE todos SET status = ?, is_completed = ? WHERE id = ? AND user_id = ?");
            $stmt->execute([$status, $is_completed, $todo_id, $user_id]);
        }
        
        // For AJAX requests, don't redirect
        exit();
    }
    
    // Update todo priority (for table view dropdown)
    elseif ($action === 'update_priority') {
        $todo_id = $_POST['todo_id'];
        $priority = $_POST['priority'];
        
        // Validate priority
        $valid_priorities = ['mendesak', 'penting', 'biasa'];
        if (in_array($priority, $valid_priorities)) {
            $stmt = $pdo->prepare("UPDATE todos SET priority = ? WHERE id = ? AND user_id = ?");
            $stmt->execute([$priority, $todo_id, $user_id]);
        }
    }
    
    // Redirect back to dashboard
    $view_param = isset($_POST['view']) ? "?view=" . $_POST['view'] : (isset($_GET['view']) ? "?view=" . $_GET['view'] : "");
    header("Location: dashboard.php" . $view_param);
    exit();
}
?>
