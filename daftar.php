<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - TodoList</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            position: relative;
            overflow: hidden;
        }
        
        .auth-card {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 1.5rem;
            padding: 3rem;
            max-width: 450px;
            width: 100%;
            position: relative;
            z-index: 2;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
        }
        
        .auth-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .auth-header h1 {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }
        
        .auth-header p {
            color: var(--text-secondary);
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            font-size: 0.875rem;
            color: var(--text-secondary);
        }
        
        .form-input {
            width: 100%;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-color);
            border-radius: 0.75rem;
            padding: 0.875rem 1rem;
            color: var(--text-primary);
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-input:focus {
            outline: none;
            border-color: var(--accent-primary);
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
        }
        
        [data-theme="light"] .form-input {
            background: var(--bg-tertiary);
        }
        
        [data-theme="light"] .form-input:focus {
            background: white;
        }
        
        .form-input::placeholder {
            color: var(--text-tertiary);
        }
        
        .btn-submit {
            width: 100%;
            background: var(--accent-primary);
            border: none;
            color: white;
            padding: 1rem;
            border-radius: 0.875rem;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(124, 58, 237, 0.3);
            margin-top: 1rem;
        }
        
        .btn-submit:hover {
            background: var(--accent-hover);
            transform: translateY(-2px);
            box-shadow: 0 15px 40px rgba(124, 58, 237, 0.5);
        }
        
        .auth-footer {
            text-align: center;
            margin-top: 1.5rem;
            color: var(--text-secondary);
        }
        
        .auth-footer a {
            color: var(--accent-primary);
            text-decoration: none;
            font-weight: 600;
        }
        
        .auth-footer a:hover {
            text-decoration: underline;
        }
        
        .back-link {
            color: var(--text-secondary);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 2rem;
            transition: color 0.3s ease;
        }
        
        .back-link:hover {
            color: var(--text-primary);
        }
        
        .error-message {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            border: 1px solid rgba(239, 68, 68, 0.3);
            text-align: center;
        }
    </style>
</head>
<body data-theme="dark">
    <div class="auth-container">
        <div class="auth-card">
            <a href="index.php" class="back-link">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                    <path d="M16 10H4M4 10L10 16M4 10L10 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Kembali
            </a>
            
            <div class="auth-header">
                <h1>Mulai produktivitasmu!</h1>
                <p>Buat akun dan kelola tugasmu dengan mudah</p>
            </div>
            
            <?php if(isset($_GET['error'])): ?>
                <div class="error-message">
                    <?php 
                    if($_GET['error'] == 'exists') echo 'Username sudah digunakan!';
                    elseif($_GET['error'] == 'empty') echo 'Semua field harus diisi!';
                    else echo 'Terjadi kesalahan!';
                    ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="process-register.php">
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-input" placeholder="username" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-input" placeholder="Minimal 8 karakter" required>
                </div>
                
                <button type="submit" class="btn-submit">Daftar Sekarang</button>
            </form>
            
            <div class="auth-footer">
                Sudah punya akun? <a href="masuk.php">Masuk di sini</a>
            </div>
        </div>
        
        <!-- Background decorations -->
        <div class="bg-decoration decoration-1"></div>
        <div class="bg-decoration decoration-2"></div>
    </div>
    
    <script>
        // Load theme from localStorage (inherited from index page)
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme') || 'dark';
            document.body.setAttribute('data-theme', savedTheme);
        });
    </script>
</body>
</html>
