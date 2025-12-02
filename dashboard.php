<?php
require_once 'config.php';

// Check if user is logged in (like middleware in Express.js)
if (!isset($_SESSION['user_id'])) {
    header("Location: masuk.php");
    exit();
}

// Get user's todos
$stmt = $pdo->prepare("SELECT * FROM todos WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$todos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Default view is 'simple'
$view = isset($_GET['view']) ? $_GET['view'] : 'simple';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - TodoList</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* CSS Variables for Theming */
        :root {
            --bg-primary: #000000;
            --bg-secondary: rgba(255,255,255,0.03);
            --bg-tertiary: rgba(255,255,255,0.05);
            --bg-hover: rgba(255,255,255,0.08);
            --text-primary: #FFFFFF;
            --text-secondary: #A0A0A0;
            --border-color: rgba(255,255,255,0.1);
            --accent-primary: #7C3AED;
            --accent-hover: #6D28D9;
            --red: #ef4444;
            --orange: #f97316;
            --blue: #3b82f6;
            --yellow: #eab308;
            --green: #22c55e;
            --shadow: rgba(0,0,0,0.2);
        }
        
        [data-theme="light"] {
            --bg-primary: #FFFFFF;
            --bg-secondary: #F3F4F6;
            --bg-tertiary: #E5E7EB;
            --bg-hover: #D1D5DB;
            --text-primary: #1F2937;
            --text-secondary: #6B7280;
            --border-color: #E5E7EB;
            --accent-primary: #7C3AED;
            --accent-hover: #6D28D9;
            --red: #dc2626;
            --orange: #ea580c;
            --blue: #2563eb;
            --yellow: #ca8a04;
            --green: #16a34a;
            --shadow: rgba(0,0,0,0.1);
        }
        
        body {
            background: var(--bg-primary);
            color: var(--text-primary);
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .dashboard {
            min-height: 100vh;
            padding: 2rem;
            padding-bottom: 5rem; /* Space for fixed footer */
        }
        
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }
        
        .dashboard-title {
            font-size: 2rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .view-toggle {
            display: flex;
            gap: 0.5rem;
            background: var(--bg-secondary);
            padding: 0.25rem;
            border-radius: 0.5rem;
        }
        
        .view-btn {
            padding: 0.5rem 1rem;
            background: transparent;
            border: none;
            color: var(--text-secondary);
            cursor: pointer;
            border-radius: 0.375rem;
            transition: all 0.2s;
        }
        
        .view-btn.active {
            background: var(--accent-primary);
            color: white;
        }
        
        .theme-toggle {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            background: var(--accent-primary);
            border: none;
            color: white;
            padding: 1rem;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            width: 56px;
            height: 56px;
            box-shadow: 0 4px 12px rgba(124, 58, 237, 0.3);
            z-index: 1000;
        }
        
        .theme-toggle:hover {
            background: var(--accent-hover);
            transform: scale(1.1) rotate(15deg);
            box-shadow: 0 6px 20px rgba(124, 58, 237, 0.4);
        }
        
        .theme-toggle svg {
            position: absolute;
            transition: opacity 0.3s, transform 0.3s;
        }
        
        .sun-icon {
            opacity: 0;
            transform: rotate(180deg) scale(0);
        }
        
        [data-theme="light"] .sun-icon {
            opacity: 1;
            transform: rotate(0deg) scale(1);
        }
        
        .moon-icon {
            opacity: 1;
            transform: rotate(0deg) scale(1);
        }
        
        [data-theme="light"] .moon-icon {
            opacity: 0;
            transform: rotate(-180deg) scale(0);
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .username {
            color: var(--text-secondary);
        }
        
        .logout-btn {
            background: var(--red);
            color: white;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 0.375rem;
            cursor: pointer;
            transition: background 0.2s;
        }
        
        .logout-btn:hover {
            background: #cc0000;
        }
        
        /* Add Todo Section */
        .add-todo {
            background: var(--bg-tertiary);
            padding: 1.5rem;
            border-radius: 0.75rem;
            margin-bottom: 2rem;
            border: 1px solid var(--border-color);
        }
        
        .add-todo-form {
            display: flex;
            gap: 0.75rem;
        }
        
        .todo-input {
            flex: 1;
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            font-size: 1rem;
        }
        
        .todo-input:focus {
            outline: none;
            border-color: var(--accent-primary);
        }
        
        .add-btn {
            background: var(--accent-primary);
            color: white;
            padding: 0.75rem 2rem;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.2s;
        }
        
        .add-btn:hover {
            background: var(--accent-hover);
        }
        
        /* Simple View (List) */
        .simple-view {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .todo-list-item {
            background: var(--bg-tertiary);
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            padding: 1rem 1.5rem;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: all 0.2s;
        }
        
        .todo-list-item:hover {
            background: var(--bg-hover);
        }
        
        .todo-list-item.completed {
            opacity: 0.6;
        }
        
        .todo-checkbox-simple {
            width: 20px;
            height: 20px;
            cursor: pointer;
            flex-shrink: 0;
        }
        
        .todo-text {
            flex: 1;
            font-size: 1rem;
            transition: all 0.2s;
        }
        
        .todo-text.completed {
            text-decoration: line-through;
            color: var(--text-secondary);
        }
        
        .trash-btn {
            background: transparent;
            border: none;
            color: var(--text-secondary);
            cursor: pointer;
            padding: 0.5rem;
            font-size: 1.5rem;
            transition: color 0.2s;
            flex-shrink: 0;
        }
        
        .trash-btn:hover {
            color: var(--red);
        }
        
        /* Board View (Card) */
        .board-view {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
        }
        
        .board-column {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 0.75rem;
            padding: 1rem;
            min-height: 400px;
        }
        
        .column-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid var(--border-color);
        }
        
        .column-title {
            font-weight: 600;
            font-size: 1.125rem;
        }
        
        .column-count {
            background: var(--bg-tertiary);
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
        }
        
        .todo-card {
            background: var(--bg-tertiary);
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 0.75rem;
            transition: all 0.2s;
        }
        
        .todo-card:hover {
            background: var(--bg-hover);
            transform: translateY(-2px);
            box-shadow: 0 4px 6px var(--shadow);
        }
        
        .todo-card.dragging {
            opacity: 0.5;
            cursor: move;
        }
        
        .board-column.drag-over {
            background: rgba(124, 58, 237, 0.1);
            border: 2px dashed var(--accent-primary);
        }
        
        .todo-card-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 0.5rem;
        }
        
        .todo-card-title {
            flex: 1;
            word-break: break-word;
        }
        
        .todo-card-title.completed {
            text-decoration: line-through;
            opacity: 0.6;
        }
        
        .todo-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .action-btn {
            background: transparent;
            border: none;
            color: var(--text-secondary);
            cursor: pointer;
            padding: 0.25rem;
            transition: color 0.2s;
        }
        
        .action-btn:hover {
            color: var(--text-primary);
        }
        
        .delete-btn:hover {
            color: var(--red);
        }
        
        .priority-badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .priority-mendesak {
            background: rgba(239, 68, 68, 0.2);
            color: var(--red);
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        
        .priority-penting {
            background: rgba(249, 115, 22, 0.2);
            color: var(--orange);
            border: 1px solid rgba(249, 115, 22, 0.3);
        }
        
        .priority-biasa {
            background: rgba(59, 130, 246, 0.2);
            color: var(--blue);
            border: 1px solid rgba(59, 130, 246, 0.3);
        }
        
        .priority-select {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            padding: 0.375rem 0.75rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            font-family: 'Inter', sans-serif;
        }
        
        .priority-select:hover {
            background: var(--bg-hover);
            border-color: var(--accent-primary);
        }
        
        .priority-select:focus {
            outline: none;
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 2px rgba(124, 58, 237, 0.2);
        }
        
        .priority-select.mendesak {
            background: rgba(239, 68, 68, 0.2);
            color: var(--red);
            border-color: rgba(239, 68, 68, 0.3);
        }
        
        .priority-select.penting {
            background: rgba(249, 115, 22, 0.2);
            color: var(--orange);
            border-color: rgba(249, 115, 22, 0.3);
        }
        
        .priority-select.biasa {
            background: rgba(59, 130, 246, 0.2);
            color: var(--blue);
            border-color: rgba(59, 130, 246, 0.3);
        }
        
        [data-theme="light"] .priority-select option {
            background: white;
            color: #1F2937;
        }
        
        [data-theme="dark"] .priority-select option {
            background: #1a1a1a;
            color: white;
        }
        
        /* Table View */
        .table-view {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 0.75rem;
            overflow: hidden;
        }
        
        .todo-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .todo-table th {
            background: var(--bg-tertiary);
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            border-bottom: 1px solid var(--border-color);
        }
        
        .todo-table td {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
        }
        
        .todo-table tr:hover {
            background: var(--bg-hover);
        }
        
        .status-badge {
            display: inline-block;
            padding: 0.375rem 0.75rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        .status-belum {
            background: rgba(239, 68, 68, 0.2);
            color: var(--red);
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        
        .status-proses {
            background: rgba(234, 179, 8, 0.2);
            color: var(--yellow);
            border: 1px solid rgba(234, 179, 8, 0.3);
        }
        
        .status-selesai {
            background: rgba(34, 197, 94, 0.2);
            color: var(--green);
            border: 1px solid rgba(34, 197, 94, 0.3);
        }
        
        .checkbox-cell {
            width: 40px;
        }
        
        .todo-checkbox {
            margin-bottom: 1rem;
            border: 1px solid var(--border-color);
        }
        
        .success-message {
            background: rgba(34, 197, 94, 0.2);
            color: var(--green);
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            border: 1px solid rgba(34, 197, 94, 0.3);
        }
    </style>
</head>
<body data-theme="dark">
    <div class="dashboard">
        <div class="dashboard-header">
            <div class="dashboard-title">
                <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="32" height="32" rx="8" fill="url(#gradient1)"/>
                    <path d="M9 16L14 21L23 11" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <defs>
                        <linearGradient id="gradient1" x1="0" y1="0" x2="32" y2="32">
                            <stop offset="0%" style="stop-color:#8B5CF6"/>
                            <stop offset="100%" style="stop-color:#6366F1"/>
                        </linearGradient>
                    </defs>
                </svg>
                <span>TodoList</span>
            </div>
            
            <div class="view-toggle">
                <button class="view-btn <?= $view === 'simple' ? 'active' : '' ?>" onclick="window.location.href='?view=simple'">Sederhana</button>
                <button class="view-btn <?= $view === 'board' ? 'active' : '' ?>" onclick="window.location.href='?view=board'">Papan</button>
                <button class="view-btn <?= $view === 'table' ? 'active' : '' ?>" onclick="window.location.href='?view=table'">Tabel</button>
            </div>
            
            <div class="user-info">
                <span class="username">Hai, <?= htmlspecialchars($_SESSION['username']) ?>!</span>
                <a href="logout.php"><button class="logout-btn">Keluar</button></a>
            </div>
        </div>
        
        <!-- Add Todo Form -->
        <div class="add-todo">
            <form method="POST" action="process-todo.php" class="add-todo-form">
                <input type="text" name="title" class="todo-input" placeholder="Tambah tugas baru..." required>
                <button type="submit" name="action" value="add" class="add-btn">Tambah</button>
            </form>
        </div>
        
        <?php if ($view === 'simple'): ?>
            <!-- Simple View (List) -->
            <div class="simple-view">
                <?php foreach ($todos as $todo): ?>
                    <div class="todo-list-item <?= $todo['is_completed'] ? 'completed' : '' ?>">
                        <form method="POST" action="process-todo.php" style="display: inline;">
                            <input type="hidden" name="todo_id" value="<?= $todo['id'] ?>">
                            <input type="hidden" name="view" value="simple">
                            <input type="hidden" name="action" value="toggle">
                            <input type="checkbox" class="todo-checkbox-simple" <?= $todo['is_completed'] ? 'checked' : '' ?> onchange="this.form.submit()">
                        </form>
                        
                        <span class="todo-text <?= $todo['is_completed'] ? 'completed' : '' ?>">
                            <?= htmlspecialchars($todo['title']) ?>
                        </span>
                        
                        <form method="POST" action="process-todo.php" style="display: inline;" onsubmit="return confirm('Hapus tugas ini?')">
                            <input type="hidden" name="todo_id" value="<?= $todo['id'] ?>">
                            <input type="hidden" name="view" value="simple">
                            <button type="submit" name="action" value="delete" class="trash-btn">🗑</button>
                        </form>
                    </div>
                <?php endforeach; ?>
                
                <?php if (empty($todos)): ?>
                    <div style="text-align: center; color: #A0A0A0; padding: 3rem;">
                        Belum ada tugas. Tambahkan tugas pertamamu!
                    </div>
                <?php endif; ?>
            </div>
        
        <?php elseif ($view === 'board'): ?>
            <!-- Board View (Card Layout with Drag & Drop) -->
            <div class="board-view">
                <!-- Belum Column -->
                <div class="board-column" data-status="Belum" ondrop="drop(event)" ondragover="allowDrop(event)" ondragleave="dragLeave(event)">
                    <div class="column-header">
                        <span class="column-title" style="color: #ef4444;">Belum</span>
                        <span class="column-count"><?= count(array_filter($todos, fn($t) => $t['status'] === 'Belum')) ?></span>
                    </div>
                    <?php foreach ($todos as $todo): ?>
                        <?php if ($todo['status'] === 'Belum'): ?>
                            <div class="todo-card" draggable="true" ondragstart="drag(event)" data-id="<?= $todo['id'] ?>">
                                <div class="todo-card-title <?= $todo['is_completed'] ? 'completed' : '' ?>">
                                    <?= htmlspecialchars($todo['title']) ?>
                                </div>
                                <span class="priority-badge priority-<?= $todo['priority'] ?>"><?= $todo['priority'] ?></span>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                
                <!-- Proses Column -->
                <div class="board-column" data-status="Proses" ondrop="drop(event)" ondragover="allowDrop(event)" ondragleave="dragLeave(event)">
                    <div class="column-header">
                        <span class="column-title" style="color: #eab308;">Proses</span>
                        <span class="column-count"><?= count(array_filter($todos, fn($t) => $t['status'] === 'Proses')) ?></span>
                    </div>
                    <?php foreach ($todos as $todo): ?>
                        <?php if ($todo['status'] === 'Proses'): ?>
                            <div class="todo-card" draggable="true" ondragstart="drag(event)" data-id="<?= $todo['id'] ?>">
                                <div class="todo-card-title <?= $todo['is_completed'] ? 'completed' : '' ?>">
                                    <?= htmlspecialchars($todo['title']) ?>
                                </div>
                                <span class="priority-badge priority-<?= $todo['priority'] ?>"><?= $todo['priority'] ?></span>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                
                <!-- Selesai Column -->
                <div class="board-column" data-status="Selesai" ondrop="drop(event)" ondragover="allowDrop(event)" ondragleave="dragLeave(event)">
                    <div class="column-header">
                        <span class="column-title" style="color: #22c55e;">Selesai ✅</span>
                        <span class="column-count"><?= count(array_filter($todos, fn($t) => $t['status'] === 'Selesai')) ?></span>
                    </div>
                    <?php foreach ($todos as $todo): ?>
                        <?php if ($todo['status'] === 'Selesai'): ?>
                            <div class="todo-card" draggable="true" ondragstart="drag(event)" data-id="<?= $todo['id'] ?>">
                                <div class="todo-card-title <?= $todo['is_completed'] ? 'completed' : '' ?>">
                                    <?= htmlspecialchars($todo['title']) ?>
                                </div>
                                <span class="priority-badge priority-<?= $todo['priority'] ?>"><?= $todo['priority'] ?></span>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php else: ?>
            <!-- Table View -->
            <div class="table-view">
                <table class="todo-table">
                    <thead>
                        <tr>
                            <th class="checkbox-cell">Selesai</th>
                            <th>Prioritas</th>
                            <th>Nama Tugas</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($todos as $todo): ?>
                            <tr>
                                <td class="checkbox-cell">
                                    <form method="POST" action="process-todo.php" style="display: inline;">
                                        <input type="hidden" name="todo_id" value="<?= $todo['id'] ?>">
                                        <input type="hidden" name="view" value="table">
                                        <input type="hidden" name="action" value="toggle">
                                        <input type="checkbox" class="todo-checkbox" <?= $todo['is_completed'] ? 'checked' : '' ?> onchange="this.form.submit()">
                                    </form>
                                </td>
                                <td>
                                    <form method="POST" action="process-todo.php" style="display: inline;">
                                        <input type="hidden" name="todo_id" value="<?= $todo['id'] ?>">
                                        <input type="hidden" name="view" value="table">
                                        <input type="hidden" name="action" value="update_priority">
                                        <select name="priority" class="priority-select <?= $todo['priority'] ?>" onchange="this.form.submit()">
                                            <option value="mendesak" <?= $todo['priority'] === 'mendesak' ? 'selected' : '' ?>>🔴 Mendesak</option>
                                            <option value="penting" <?= $todo['priority'] === 'penting' ? 'selected' : '' ?>>🟠 Penting</option>
                                            <option value="biasa" <?= $todo['priority'] === 'biasa' ? 'selected' : '' ?>>🔵 Biasa</option>
                                        </select>
                                    </form>
                                </td>
                                <td class="<?= $todo['is_completed'] ? 'todo-card-title completed' : '' ?>">
                                    <?= htmlspecialchars($todo['title']) ?>
                                </td>
                                <td>
                                    <span class="status-badge status-<?= strtolower($todo['status']) ?>">
                                        <?= $todo['status'] ?>
                                    </span>
                                </td>
                                <td>
                                    <form method="POST" action="process-todo.php" style="display: inline;" onsubmit="return confirm('Hapus tugas ini?')">
                                        <input type="hidden" name="todo_id" value="<?= $todo['id'] ?>">
                                        <button type="submit" name="action" value="delete" class="action-btn delete-btn">🗑 Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Floating Theme Toggle Button -->
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
    
    <!-- Footer -->
    <div class="footer">
        Made with PHP❤️ by <strong>Novandra Anugrah</strong>
    </div>
    
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
        
        // Drag and Drop for Board View
        function allowDrop(event) {
            event.preventDefault();
            event.currentTarget.classList.add('drag-over');
        }
        
        function dragLeave(event) {
            event.currentTarget.classList.remove('drag-over');
        }
        
        function drag(event) {
            event.dataTransfer.setData("todoId", event.target.dataset.id);
            event.target.classList.add('dragging');
        }
        
        function drop(event) {
            event.preventDefault();
            event.currentTarget.classList.remove('drag-over');
            
            const todoId = event.dataTransfer.getData("todoId");
            const newStatus = event.currentTarget.dataset.status;
            
            // Remove dragging class
            document.querySelectorAll('.dragging').forEach(el => el.classList.remove('dragging'));
            
            // Update status via AJAX
            const formData = new FormData();
            formData.append('action', 'update_status');
            formData.append('todo_id', todoId);
            formData.append('status', newStatus);
            
            fetch('process-todo.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                // Reload page to show updated status
                window.location.reload();
            })
            .catch(error => console.error('Error:', error));
        }
    </script>
</body>
</html>
