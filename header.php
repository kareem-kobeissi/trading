<?php
// header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Support both normal login and Google/social login
$isLoggedIn = isset($_SESSION['user_id']);
$username = $isLoggedIn ? $_SESSION['username'] : '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light only">
    <title>TheTradingRoutine - Master Trading</title>
    <link rel="icon" type="image/png" href="1.png">
    <link rel="stylesheet" href="styles.css">
    <!-- Font Awesome for Hamburger Icon and Social -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Translations -->
    <script src="translations.js"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* Page Loading Animation */
        .page-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #0a0e27 0%, #1a2d5f 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            transition: opacity 0.5s ease, visibility 0.5s ease;
        }

        .page-loader.hidden {
            opacity: 0;
            visibility: hidden;
        }

        .loader-content {
            text-align: center;
        }

        .loader-spinner {
            width: 60px;
            height: 60px;
            border: 4px solid rgba(0, 212, 255, 0.2);
            border-top-color: #00d4ff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 1.5rem;
        }

        .loader-text {
            color: #00d4ff;
            font-size: 1.2rem;
            font-weight: 600;
            animation: pulse 1.5s infinite ease-in-out;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>
    <!-- Page Loader -->
    <div class="page-loader" id="pageLoader">
        <div class="loader-content">
            <div class="loader-spinner"></div>
            <div class="loader-text">Loading...</div>
        </div>
    </div>
    <script>
        window.addEventListener('load', function() {
            setTimeout(function() {
                document.getElementById('pageLoader').classList.add('hidden');
            }, 300);
        });
    </script>

    <header class="main-header">
        <nav class="navbar">
            <div class="nav-brand">
                <a href="index.php" class="logo">
                    <img src="hsenn.jpeg" alt="TheTradingRoutine Logo" class="logo-img">
                    <span class="logo-text">The<span class="blue-text">Tra</span><span class="green-text">ding</span>Routine</span>
                </a>
            </div>

            <!-- Special Hamburger Menu Button -->
            <button class="hamburger" id="hamburger-btn" aria-label="Menu">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </button>

            <ul class="nav-links">
                <li><a href="index.php" data-i18n="home">Home</a></li>
                <li><a href="courses.php" data-i18n="courses">Courses</a></li>
                <li><a href="broker.php" data-i18n="broker">Broker</a></li>
                <li><a href="ea.php">EAs</a></li>
                <li><a href="indicators.php">Indicators</a></li>
                <li><a href="cart.php" data-i18n="cart">Cart</a></li>
                <li><a href="contact.php" data-i18n="contact">Contact</a></li>
                
                <li><a href="about.php" data-i18n="about">About</a></li>

                <?php if ($isLoggedIn): ?>
                    <li class="mobile-only"><a href="user_dashboard.php" data-i18n="profile">👤 Profile</a></li>
                    <li class="mobile-only"><a href="logout.php" onclick="logout()" data-i18n="logout">🚪 Logout</a></li>
                <?php
else: ?>
                    <li class="mobile-only"><a href="login.php" data-i18n="login">Login</a></li>
                    <li class="mobile-only"><a href="signup.php" data-i18n="signup">Sign Up</a></li>
                <?php
endif; ?>
            </ul>

            <script>
const userLogged = !!sessionStorage.getItem('currentEmail');
                function updateMobileMenu() {
                    const adminLogged = localStorage.getItem('adminLogged') === 'true';
                    const navLinks = document.querySelector('.nav-links');

                    if (!navLinks) return;

                    // Remove existing mobile-only items
                    const mobileItems = navLinks.querySelectorAll('.mobile-only');
                    mobileItems.forEach(item => item.remove());

                    // Add appropriate items based on login status
                    if (adminLogged) {
                        navLinks.insertAdjacentHTML('beforeend', `
                            <li class="mobile-only"><a href="admin_dashboard.php" data-i18n="adminPanel">📊 Admin Panel</a></li>
                        `);
                    } else if (userLogged) {
                        navLinks.insertAdjacentHTML('beforeend', `
                            <li class="mobile-only"><a href="user_dashboard.php" data-i18n="profile">👤 Profile</a></li>
                            <li class="mobile-only"><a href="#" onclick="userLogout(); return false;" data-i18n="logout">🚪 Logout</a></li>
                        `);
                    } else {
                        navLinks.insertAdjacentHTML('beforeend', `
                            <li class="mobile-only"><a href="login.php" data-i18n="login">Login</a></li>
                            <li class="mobile-only"><a href="signup.php" data-i18n="signup">Sign Up</a></li>
                        `);
                    }
                    applyLanguage();
                }

                // Update mobile menu on page load
                document.addEventListener('DOMContentLoaded', function() {
                    updateMobileMenu();
                });
            </script>

            <div class="auth-buttons" id="authButtonsContainer">
                <?php if ($isLoggedIn): ?>
                    <a href="user_dashboard.php" class="btn btn-secondary glass-btn" data-i18n="profile">👤 Profile</a>
<a href="logout.php" class="btn btn-primary glow-btn">Logout</a>                <?php
else: ?>
                    <a href="login.php" class="btn btn-secondary glass-btn" data-i18n="login">Login</a>
                    <a href="signup.php" class="btn btn-primary glow-btn" data-i18n="signup">Sign Up</a>
                <?php
endif; ?>
            </div>

            <script>
                // Function to update header based on login status
                function updateHeaderAuth() {
                const adminLogged = localStorage.getItem('adminLogged') === 'true';
                    const authButtonsContainer = document.getElementById('authButtonsContainer');

                    console.log('Checking auth status - Admin:', adminLogged, 'User:', userLogged);

                    if (!authButtonsContainer) return;

                    if (adminLogged) {
                        console.log('Displaying Admin Panel button');
                        authButtonsContainer.innerHTML = `
                            <a href="admin_dashboard.php" class="btn btn-secondary glass-btn" data-i18n="adminPanel">📊 Admin Panel</a>
                        `;
                    } else if (userLogged) {
                        console.log('Displaying User Profile button');
                        authButtonsContainer.innerHTML = `
                            <a href="user_dashboard.php" class="btn btn-secondary glass-btn" data-i18n="profile">👤 Profile</a>
                            <a href="#" onclick="userLogout(); return false;" class="btn btn-primary glow-btn" data-i18n="logout">Logout</a>
                        `;
                    } else {
                        console.log('Displaying Login/Sign Up buttons');
                        authButtonsContainer.innerHTML = `
                            <a href="login.php" class="btn btn-secondary glass-btn" data-i18n="login">Login</a>
                            <a href="signup.php" class="btn btn-primary glow-btn" data-i18n="signup">Sign Up</a>
                        `;
                    }
                    applyLanguage();
                }

                // Update header immediately and as early as possible
                if (document.currentScript) {
                    // This script is running inline, update immediately
                    const authButtonsContainer = document.getElementById('authButtonsContainer');
                    if (authButtonsContainer) {
                        updateHeaderAuth();
                    }
                }

                // Also update on DOMContentLoaded as fallback
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', function() {
                        setTimeout(function() {
                            updateHeaderAuth();
                        }, 0);
                    });
                } else {
                    // If DOM is already ready
                    setTimeout(function() {
                        updateHeaderAuth();
                    }, 0);
                }

                // Listen for storage changes from other tabs
                window.addEventListener('storage', function(e) {
                    if (e.key === 'adminLogged' || e.key === 'currentEmail') {
                        console.log('Storage event detected, updating header');
                        updateHeaderAuth();
                        updateMobileMenu();
                    }
                });

                // Admin logout function
                function adminLogout() {
    if (confirm('Are you sure you want to logout?')) {
        localStorage.removeItem('adminLogged');
        localStorage.removeItem('adminEmail');
                        window.location.href = 'index.php';
                    }
                }

                // Set active navigation link based on current page
                function setActiveNavLink() {
                    // Get current page filename
                    const currentPage = window.location.pathname.split('/').pop() || 'index.php';

                    // Remove active class from all nav links
                    const navLinks = document.querySelectorAll('.nav-links a');
                    navLinks.forEach(link => {
                        link.classList.remove('active');
                    });

                    // Add active class to current page link
                    navLinks.forEach(link => {
                        const href = link.getAttribute('href');
                        if (href === currentPage || (currentPage === '' && href === 'index.php')) {
                            link.classList.add('active');
                        }
                    });
                }

                // Call setActiveNavLink when DOM is ready
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', setActiveNavLink);
                } else {
                    setActiveNavLink();
                }
            </script>
        </nav>
    </header>
