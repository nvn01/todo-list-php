<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TodoList - Fokus, Produktif, dan Terorganisir</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body data-theme="dark">
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo">
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
            </div>
            
            <div class="nav-links">
                <a href="#dokumentasi" class="nav-link">Dokumentasi</a>
                <a href="#beranda" class="nav-link">Beranda</a>
                <button class="btn-masuk" onclick="window.location.href='masuk.php'">Masuk</button>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <h1 class="hero-title">Fokus, produktif, dan terorganisir</h1>
        <p class="hero-subtitle">Aplikasi todo list untukmu yang ngga mau ribet</p>
        
        <button class="btn-primary" onclick="window.location.href='daftar.php'">Mulai sekarang!</button>
        
        <!-- App Screenshots Carousel -->
        <div class="carousel-container">
            <div class="carousel-wrapper">
                <div class="carousel-slides">
                    <!-- Screenshots will be loaded here -->
                    <div class="carousel-slide active">
                        <img src="screenshots/screenshot1.png" alt="TodoList Screenshot 1" onerror="this.src='screenshot-placeholder.png'">
                    </div>
                    <div class="carousel-slide">
                        <img src="screenshots/screenshot2.png" alt="TodoList Screenshot 2" onerror="this.src='screenshot-placeholder.png'">
                    </div>
                </div>
            </div>
        </div>
    </section>

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
        
        // Carousel Functionality
        let currentSlideIndex = 0;
        let autoSlideInterval;
        
        function showSlide(index) {
            const slides = document.querySelectorAll('.carousel-slide');
            
            if (index >= slides.length) currentSlideIndex = 0;
            if (index < 0) currentSlideIndex = slides.length - 1;
            
            slides.forEach(slide => {
                slide.classList.remove('active');
            });
            
            slides[currentSlideIndex].classList.add('active');
        }
        
        function autoSlide() {
            currentSlideIndex++;
            showSlide(currentSlideIndex);
        }
        
        function startAutoSlide() {
            autoSlideInterval = setInterval(autoSlide, 4000); // Change slide every 4 seconds
        }
        
        // Start auto-sliding when page loads
        document.addEventListener('DOMContentLoaded', function() {
            startAutoSlide();
        });
    </script>
</body>
</html>
