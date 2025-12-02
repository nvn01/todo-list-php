<?php
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: masuk.php");
    exit();
}

// Check if user is admin
$stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || $user['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

// Get statistics
$stats = [];

// Total users
$stmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE role = 'user'");
$stats['total_users'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Total todos
$stmt = $pdo->query("SELECT COUNT(*) as total FROM todos");
$stats['total_todos'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Completed todos
$stmt = $pdo->query("SELECT COUNT(*) as total FROM todos WHERE is_completed = 1");
$stats['completed_todos'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Active users (users with at least one todo)
$stmt = $pdo->query("SELECT COUNT(DISTINCT user_id) as total FROM todos");
$stats['active_users'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Get all users with their todo counts
$stmt = $pdo->query("
    SELECT 
        u.id,
        u.username,
        u.created_at,
        COUNT(t.id) as todo_count,
        SUM(CASE WHEN t.is_completed = 1 THEN 1 ELSE 0 END) as completed_count
    FROM users u
    LEFT JOIN todos t ON u.id = t.user_id
    WHERE u.role = 'user'
    GROUP BY u.id
    ORDER BY u.created_at DESC
");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get recent todos
$stmt = $pdo->query("
    SELECT 
        t.*,
        u.username
    FROM todos t
    JOIN users u ON t.user_id = u.id
    ORDER BY t.created_at DESC
    LIMIT 10
");
$recent_todos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - TodoList</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .admin-dashboard {
            min-height: 100vh;
            padding: 2rem;
        }
        
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }
        
        .admin-title {
            font-size: 2rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .admin-badge {
            background: linear-gradient(135deg, #7C3AED, #6D28D9);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 600;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: var(--bg-tertiary);
            border: 1px solid var(--border-color);
            border-radius: 1rem;
            padding: 1.5rem;
            transition: all 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 30px var(--shadow);
        }
        
        .stat-label {
            font-size: 0.875rem;
            color: var(--text-secondary);
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        
        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--accent-primary);
        }
        
        .stat-icon {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        
        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            margin-top: 2rem;
        }
        
        .table-container {
            background: var(--bg-tertiary);
            border: 1px solid var(--border-color);
            border-radius: 1rem;
            overflow: hidden;
            margin-bottom: 2rem;
        }
        
        .admin-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .admin-table th {
            background: var(--bg-secondary);
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            border-bottom: 1px solid var(--border-color);
        }
        
        .admin-table td {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
        }
        
        .admin-table tr:hover {
            background: var(--bg-hover);
        }
        
        .delete-btn {
            background: rgba(239, 68, 68, 0.2);
            color: var(--red);
            border: 1px solid rgba(239, 68, 68, 0.3);
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.2s;
        }
        
        .delete-btn:hover {
            background: var(--red);
            color: white;
        }
        
        .back-btn {
            background: var(--bg-tertiary);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
        }
        
        .back-btn:hover {
            background: var(--bg-hover);
        }
        
        .badge {
            padding: 0.25rem 0.75rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        .badge-success {
            background: rgba(34, 197, 94, 0.2);
            color: var(--green);
            border: 1px solid rgba(34, 197, 94, 0.3);
        }
        
        .badge-warning {
            background: rgba(234, 179, 8, 0.2);
            color: var(--yellow);
            border: 1px solid rgba(234, 179, 8, 0.3);
        }
        
        .badge-danger {
            background: rgba(239, 68, 68, 0.2);
            color: var(--red);
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        
        .user-actions {
            display: flex;
            gap: 0.5rem;
        }
    </style>
</head>
<body data-theme="dark">
    <div class="admin-dashboard">
        <div class="admin-header">
            <div>
                <div class="admin-title">
                    <span>🛡️ Admin Dashboard</span>
                    <span class="admin-badge">ADMIN</span>
                </div>
            </div>
            <div style="display: flex; gap: 1rem; align-items: center;">
                <span style="color: var(--text-secondary);">Hai, <?= htmlspecialchars($_SESSION['username']) ?>!</span>
                <a href="dashboard.php" class="back-btn">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                        <path d="M16 10H4M4 10L10 16M4 10L10 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Kembali ke Dashboard
                </a>
                <a href="logout.php"><button class="logout-btn" style="background: var(--red); color: white; padding: 0.5rem 1rem; border: none; border-radius: 0.5rem; cursor: pointer;">Keluar</button></a>
            </div>
        </div>
        
        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">👥</div>
                <div class="stat-label">Total Users</div>
                <div class="stat-value"><?= $stats['total_users'] ?></div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">📝</div>
                <div class="stat-label">Total Todos</div>
                <div class="stat-value"><?= $stats['total_todos'] ?></div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">✅</div>
                <div class="stat-label">Completed Todos</div>
                <div class="stat-value"><?= $stats['completed_todos'] ?></div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">⚡</div>
                <div class="stat-label">Active Users</div>
                <div class="stat-value"><?= $stats['active_users'] ?></div>
            </div>
        </div>
        
        <!-- User Management -->
        <h2 class="section-title">User Management</h2>
        <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Joined</th>
                        <th>Total Todos</th>
                        <th>Completed</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td><strong><?= htmlspecialchars($user['username']) ?></strong></td>
                            <td><?= date('d M Y', strtotime($user['created_at'])) ?></td>
                            <td><?= $user['todo_count'] ?></td>
                            <td><?= $user['completed_count'] ?></td>
                            <td>
                                <form method="POST" action="admin-actions.php" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus user <?= htmlspecialchars($user['username']) ?> dan semua todos-nya?')">
                                    <input type="hidden" name="action" value="delete_user">
                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                    <button type="submit" class="delete-btn">🗑 Hapus</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                                Belum ada user terdaftar
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Recent Todos -->
        <h2 class="section-title">Recent Todos</h2>
        <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_todos as $todo): ?>
                        <tr>
                            <td><?= $todo['id'] ?></td>
                            <td><strong><?= htmlspecialchars($todo['username']) ?></strong></td>
                            <td><?= htmlspecialchars($todo['title']) ?></td>
                            <td>
                                <span class="badge badge-<?= $todo['status'] === 'Selesai' ? 'success' : ($todo['status'] === 'Proses' ? 'warning' : 'danger') ?>">
                                    <?= $todo['status'] ?>
                                </span>
                            </td>
                            <td>
                                <?php 
                                $priority_emoji = [
                                    'mendesak' => '🔴',
                                    'penting' => '🟠',
                                    'biasa' => '🔵'
                                ];
                                echo $priority_emoji[$todo['priority']] . ' ' . ucfirst($todo['priority']);
                                ?>
                            </td>
                            <td><?= date('d M Y H:i', strtotime($todo['created_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    
                    <?php if (empty($recent_todos)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                                Belum ada todos
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Floating Theme Toggle -->
    <button class="theme-toggle" onclick="toggleTheme()" aria-label="Toggle theme">
        <svg class="sun-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="5"/>
            <line x1="12" y1="1" x2="12" y2="3"/>
            <line x1="12" y1="21" x2="12" y2="23"/>
            <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/>
            <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
            <line x1="1" y1="12" x2="3" y2="12"/>
            <line x1="21" y1="12" x2="23" y2="12"/>
            <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/>
            <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
        </svg>
        <svg class="moon-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
        </svg>
    </button>
    
    <script>
        // Theme Toggle Functionality
        function toggleTheme() {
            const body = document.body;
            const currentTheme = body.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            body.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
        }
        
        // Load saved theme on page load
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme') || 'dark';
            document.body.setAttribute('data-theme', savedTheme);
        });
    </script>
</body>
</html>
