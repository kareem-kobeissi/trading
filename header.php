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
    <title>THE TRΛDING ROUTINE</title>
    <link rel="icon" type="image/png" href="hsenn.jpeg">
    <link rel="stylesheet" href="styles.css?v=<?php echo file_exists(__DIR__ . '/styles.css') ? filemtime(__DIR__ . '/styles.css') : time(); ?>">
    <?php if ($isLoggedIn): ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@29.1.0/dist/css/intlTelInput.css">
    <?php endif; ?>
    <!-- Font Awesome for Hamburger Icon and Social -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Translations -->
    <script src="translations.js"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
</head>

<body>
    <script>
        // The site uses inline states and page navigation instead of browser popups.
        window.alert = function() {};
        window.confirm = function() { return true; };
        window.prompt = function() { return null; };
    </script>
    <header class="main-header">
        <nav class="navbar">
            <div class="nav-brand">
                <a href="index.php" class="logo">
                    <img src="hsenn.jpeg" alt="THE TRΛDING ROUTINE Logo" class="logo-img">
                    <span class="logo-text">THE TRΛDING ROUTINE</span>
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
                            <li class="mobile-only"><a href="admin_dashboard.php"><i class="fas fa-chart-line" aria-hidden="true"></i> <span data-i18n="adminPanel">Admin Panel</span></a></li>
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
                            <a href="admin_dashboard.php" class="btn btn-secondary glass-btn"><i class="fas fa-chart-line" aria-hidden="true"></i> <span data-i18n="adminPanel">Admin Panel</span></a>
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
                    localStorage.removeItem('adminLogged');
                    localStorage.removeItem('adminEmail');
                    window.location.href = 'index.php';
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

    <style media="not all">
        /* Refined header presentation — content and behavior remain unchanged. */
        .main-header {
            min-height: 82px;
            padding: 0;
            background: linear-gradient(180deg, rgba(7, 12, 30, .94), rgba(7, 12, 30, .86));
            border-bottom: 1px solid rgba(0, 212, 255, .16);
            box-shadow: 0 10px 35px rgba(0, 0, 0, .2);
        }
        .main-header::after {
            content: '';
            position: absolute;
            left: 8%; right: 8%; bottom: -1px; height: 1px;
            background: linear-gradient(90deg, transparent, rgba(0,212,255,.7), rgba(0,255,136,.45), transparent);
        }
        .navbar { max-width: 1600px; min-height: 82px; padding: .55rem 1.5%; gap: clamp(.55rem, 1vw, 1.1rem); }
        .nav-brand .logo { gap: .65rem; font-size: clamp(1.15rem, 1.35vw, 1.4rem); letter-spacing: .035em; }
        .logo-img {
            width: 66px; height: 66px;
            aspect-ratio: 1 / 1;
            border-radius: 50% !important;
            object-fit: cover;
            border: none;
            box-shadow: none;
            filter: none;
        }
        .nav-brand .logo:hover .logo-img { filter: none; box-shadow: none; }
        .logo-text {
            background: var(--brand-title-gradient, linear-gradient(135deg, #ffffff 30%, #00d4ff));
            background-clip: text; -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            white-space: nowrap;
        }
        .nav-links {
            flex: 0 1 auto;
            width: fit-content;
            justify-content: center; gap: .1rem; margin: 0; padding: .25rem;
            border: 1px solid rgba(255,255,255,.055); border-radius: 15px;
            background: rgba(255,255,255,.025);
        }
        .nav-links a {
            display: block; padding: .66rem .55rem; border-radius: 10px;
            font-size: clamp(1.25rem, 1.18vw, 1.38rem); white-space: nowrap;
        }
        .nav-links a::after { display: none; }
        .nav-links a:hover { background: rgba(0,212,255,.09); transform: translateY(-1px); }
        .nav-links a.active {
            background: linear-gradient(135deg, rgba(0,212,255,.17), rgba(0,184,148,.09));
            box-shadow: inset 0 0 0 1px rgba(0,212,255,.25), 0 5px 18px rgba(0,212,255,.08);
        }
        .auth-buttons { gap: .5rem; flex-wrap: nowrap; }
        .auth-buttons .btn {
            padding: .66rem .75rem;
            font-size: clamp(1.25rem, 1.18vw, 1.38rem);
            border-radius: 10px;
            white-space: nowrap;
        }
        .hamburger { border-radius: 13px; padding: 10px 12px; }

        @media (max-width: 992px) {
            body { padding-top: 76px; }
            .main-header, .navbar { min-height: 76px; }
            .navbar { padding: .55rem 1rem; flex-wrap: nowrap; justify-content: space-between; }
            .nav-brand .logo { font-size: clamp(.82rem, 3.5vw, 1.05rem); }
            .logo-img { width: 58px; height: 58px; }
            .hamburger { margin-left: auto; }
            .nav-links {
                top: 0; width: min(86%, 370px); padding: 82px 1rem 1.25rem; gap: .35rem;
                border-radius: 22px 0 0 22px; border: 0; border-left: 1px solid rgba(0,212,255,.3);
                background: linear-gradient(180deg, rgba(7,12,30,.995), rgba(5,20,40,.99));
            }
            .nav-links li { width: 100%; max-width: none; margin: 0; }
            .nav-links li a { padding: .84rem 1rem !important; font-size: 1.3rem !important; border-radius: 11px; }
            .nav-links li a:hover { transform: translateX(4px); }
        }
        @media (max-width: 420px) {
            .navbar { padding-inline: .7rem; }
            .logo-img { width: 56px; height: 56px; }
            .nav-brand .logo { gap: .45rem; font-size: 1rem; letter-spacing: .015em; }
            .hamburger { padding: 8px 10px; }
            .hamburger .bar { width: 24px; }
            .nav-links { width: 92%; }
        }

        /* Premium floating navigation treatment */
        .main-header {
            min-height: 100px;
            padding: 10px 14px;
            background: linear-gradient(180deg, rgba(3, 7, 18, .82), rgba(3, 7, 18, .35));
            border: 0;
            box-shadow: none;
        }

        .main-header::before {
            content: '';
            position: absolute;
            inset: 0;
            pointer-events: none;
            background: radial-gradient(circle at 50% -80%, rgba(0, 212, 255, .18), transparent 58%);
        }

        .main-header::after { display: none; }

        .navbar {
            position: relative;
            max-width: 1520px;
            min-height: 80px;
            padding: .55rem .8rem;
            gap: clamp(.55rem, .9vw, 1rem);
            border: 1px solid rgba(0, 212, 255, .18);
            border-radius: 22px;
            background: linear-gradient(135deg, rgba(14, 24, 48, .93), rgba(6, 12, 28, .96));
            box-shadow: 0 18px 45px rgba(0, 0, 0, .34), inset 0 1px 0 rgba(255, 255, 255, .055);
        }

        .navbar::before {
            content: '';
            position: absolute;
            left: 12%;
            right: 12%;
            top: -1px;
            height: 2px;
            border-radius: 0 0 999px 999px;
            background: linear-gradient(90deg, transparent, var(--primary-color), #00b894, transparent);
            opacity: .8;
        }

        .nav-brand {
            padding-right: .8rem;
            border-right: 1px solid rgba(255, 255, 255, .08);
        }

        .nav-brand .logo { font-size: clamp(1.35rem, 1.48vw, 1.58rem); }
        .logo-img { width: 62px; height: 62px; transition: transform .3s ease; }
        .nav-brand .logo:hover { transform: none; }
        .nav-brand .logo:hover .logo-img { transform: rotate(-3deg) scale(1.04); }

        .nav-links {
            flex: 1 1 auto;
            width: auto;
            gap: .3rem;
            padding: .25rem;
            border: 0;
            background: transparent;
        }

        .nav-links li {
            display: flex;
            align-items: center;
            gap: .3rem;
        }

        .nav-links li.mobile-only { display: none; }

        .nav-links li:not(:last-child)::after {
            content: '';
            width: 1px;
            height: 20px;
            flex: 0 0 1px;
            background: linear-gradient(180deg, transparent, rgba(188, 239, 255, .28), transparent);
        }

        .nav-links a {
            padding: .68rem .48rem;
            font-size: clamp(1.28rem, 1.2vw, 1.42rem);
            font-weight: 650;
            color: rgba(238, 248, 255, .82);
        }

        .nav-links a:hover {
            color: #fff;
            background: rgba(0, 212, 255, .075);
            box-shadow: inset 0 0 0 1px rgba(0, 212, 255, .11);
        }

        .nav-links a.active {
            color: #07111f !important;
            text-shadow: none;
            background: linear-gradient(135deg, #32dcff, #15c7a0);
            box-shadow: 0 8px 22px rgba(0, 212, 255, .2), inset 0 1px 0 rgba(255, 255, 255, .35);
        }

        .auth-buttons { padding-left: .2rem; }
        .auth-buttons .btn {
            min-height: 44px;
            padding: .68rem .7rem;
            font-size: clamp(1.28rem, 1.2vw, 1.42rem);
            border-radius: 12px;
            font-weight: 750;
        }

        .auth-buttons .btn-secondary {
            color: #dff8ff;
            background: rgba(0, 212, 255, .055);
            border: 1px solid rgba(0, 212, 255, .3);
        }

        .auth-buttons .btn-primary {
            color: #06111b;
            background: linear-gradient(135deg, #29d8ff, #18caa4);
            border: 0;
            box-shadow: 0 8px 24px rgba(0, 212, 255, .2);
        }

        @media (max-width: 992px) {
            body { padding-top: 88px; }
            .main-header { min-height: 88px; padding: 6px; }
            .navbar { min-height: 76px; padding: .45rem .7rem; border-radius: 17px; }
            .navbar::before { left: 22%; right: 22%; }
            .nav-brand { padding-right: 0; border-right: 0; }
            .logo-img { width: 56px; height: 56px; }
            .nav-links {
                width: min(88%, 390px);
                padding: 92px 1rem 1.25rem;
                border-radius: 24px 0 0 24px;
                border-left: 1px solid rgba(0, 212, 255, .28);
                background: linear-gradient(180deg, rgba(8, 16, 35, .995), rgba(4, 10, 24, .995));
                box-shadow: -18px 0 55px rgba(0, 0, 0, .5);
            }
            .nav-links li a { font-size: 1.38rem !important; }
            .nav-links li a.active { color: #06111b !important; }
            .nav-links li { display: block; }
            .nav-links li.mobile-only { display: block; }
            .nav-links li:not(:last-child)::after { display: none; }
            .hamburger {
                border: 1px solid rgba(0, 212, 255, .32);
                background: rgba(0, 212, 255, .07);
                box-shadow: inset 0 1px 0 rgba(255, 255, 255, .05);
            }
        }

        @media (max-width: 420px) {
            body { padding-top: 82px; }
            .main-header { min-height: 82px; padding: 5px; }
            .navbar { min-height: 72px; padding-inline: .55rem; border-radius: 15px; }
            .logo-img { width: 52px; height: 52px; }
            .nav-brand .logo { font-size: 1.12rem; }
        }
    </style>
