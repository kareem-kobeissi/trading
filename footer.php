<?php
// footer.php
?>
<footer class="simple-footer">
    <!-- Animated Background Elements -->
    <div class="footer-bg-elements">
        <div class="footer-glow footer-glow-1"></div>
        <div class="footer-glow footer-glow-2"></div>
        <div class="footer-particles">
            <span></span><span></span><span></span><span></span><span></span>
        </div>
    </div>

    <div class="footer-container">
        <!-- Top Wave Decoration -->
        <div class="footer-wave">
            <svg viewBox="0 0 1200 120" preserveAspectRatio="none">
                <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z"></path>
            </svg>
        </div>

        <div class="footer-main-row">
            <div class="footer-brand-minimal">
                <a href="index.php" class="footer-logo">
                    <div class="footer-logo-wrapper">
                        <img src="hsenn.jpeg" alt="TheTradingRoutine Logo" class="footer-logo-img">
                        <div class="logo-glow-ring"></div>
                    </div>
                    <span class="footer-brand-text">The<span class="blue-text">Tra</span><span class="green-text">ding</span>Routine</span>
                </a>
                <p class="footer-tagline" data-i18n="footerTagline">
                    Built for traders who want structure, discipline, and real market understanding. </p>

                <!-- Newsletter Mini Section -->

            </div>

            <div class="footer-links-section">
                <div class="footer-links-column">
                    <h4 class="footer-links-title" data-i18n="quickLinks">
                        Quick Links
                    </h4>
                    <div class="footer-nav-simple">
                        <a href="index.php" data-i18n="footerHome"><span class="nav-dot"></span><span data-i18n="footerHome">Home</span></a>
                        <a href="courses.php" data-i18n="footerCourses"><span class="nav-dot"></span><span data-i18n="footerCourses">Courses</span></a>
                        <a href="broker.php">Broker</span></a>
                        <a href="ea.php">EAs</span></a>
                        <a href="cart.php" data-i18n="footerCart"><span class="nav-dot"></span><span data-i18n="footerCart">Cart</span></a>
                        <a href="contact.php">Contact</span></a>
                        <a href="about.php" data-i18n="footerAbout"><span class="nav-dot"></span><span data-i18n="footerAbout">About</span></a>
                    </div>
                </div>


            </div>

            <div class="footer-social-section">
                <h4 class="footer-social-title" data-i18n="connectWithUs">Connect With Us</h4>
                <div class="footer-social-simple">

                    <a href="https://wa.me/96171493997" target="_blank" class="social-icon-wrapper whatsapp" data-tooltip="WhatsApp: +961 71 493 997">
                        <i class="fab fa-whatsapp"></i>
                        <span class="social-glow"></span>
                    </a>
                    <a href="https://www.tiktok.com/@thetradingroutine" target="_blank" class="social-icon-wrapper" data-tooltip="TikTok">
                        <i class="fab fa-tiktok"></i>
                        <span class="social-glow"></span>
                    </a>
                    <a href="https://youtube.com/@husseinsheikhalittr?si=l_lYRKU1Sh5-TvcP" target="_blank" class="social-icon-wrapper youtube" data-tooltip="YouTube">
                        <i class="fab fa-youtube"></i>
                        <span class="social-glow"></span>
                    </a>
                    <a href="mailto:support@thetradingroutine.com" class="social-icon-wrapper email" data-tooltip="Email Support">
                        <i class="fas fa-envelope"></i>
                        <span class="social-glow"></span>
                    </a>
                </div>

                <!-- Download Now Section -->
                <div class="download-section">
                    <h4 class="footer-social-title">Download Now</h4>
                    <div class="download-links">
                        <a href="https://www.metatrader5.com/en/download" target="_blank" class="download-link metatrader" data-tooltip="MetaTrader 5 - Download Trading Platform">
                            <img src="mp5.PNG" alt="MetaTrader 5" class="download-logo">
                        </a>
                        <a href="https://www.tradingview.com/pricing/?share_your_love=husseinsheikhali7" target="_blank" class="download-link tradingview" data-tooltip="TradingView - Professional Charts">
                            <img src="tr.PNG" alt="TradingView" class="download-logo">
                        </a>
                    </div>
                </div>

                <!-- Trading Hours Badge -->
                <div class="trading-hours-badge">
                    <span class="pulse-dot"></span>
                    <span data-i18n="support247">24/7 Support Available</span>
                </div>
            </div>
        </div>

        <div class="footer-bottom-divider">
            <span class="divider-diamond"></span>
        </div>

        <div class="footer-copy-row">
            <div class="footer-left">
                <p class="copyright-text">&copy; 2026 TheTradingRoutine. All rights reserved.</p>
            </div>

        </div>

        <!-- Developer Credit - Centered -->

    </div>

    <!-- Back to Top Button -->
    <button class="back-to-top" id="backToTop" title="Back to top">
        <i class="fas fa-chevron-up"></i>
    </button>
</footer>

<style>
    /* Ensure nav dots are visible in footer links */
    .footer-nav-simple a .nav-dot {
        display: inline-block;
        width: 8px;
        height: 8px;
        background: var(--primary-color);
        border-radius: 50%;
        margin-right: 8px;
        transition: all 0.3s ease;
    }

    .footer-nav-simple a:hover .nav-dot {
        background: var(--accent-green);
        box-shadow: 0 0 8px var(--primary-color);
    }

    /* Mobile - Center Quick Links */
    @media (max-width: 768px) {
        .footer-links-section {
            text-align: center !important;
        }

        .footer-links-column {
            text-align: center !important;
            width: 100% !important;
            margin: 0 auto !important;
        }

        .footer-links-title {
            text-align: center !important;
            margin: 0 auto !important;
            justify-content: center !important;
        }

        .footer-nav-simple {
            text-align: center !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 0.5rem !important;
            width: 100% !important;
        }

        .footer-nav-simple a {
            text-align: center !important;
            width: 100% !important;
            justify-content: center !important;
        }
    }

    /* Download Section Styles */
    .download-section {
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid rgba(0, 212, 255, 0.2);
        text-align: center;
    }

    .download-links {
        display: flex;
        gap: 0.8rem;
        align-items: center;
        justify-content: center;
    }

    /* Laptop/Desktop - Left aligned */
    @media (min-width: 992px) {
        .download-section {
            text-align: left;
        }

        .download-links {
            justify-content: flex-start !important;
        }
    }

    .download-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 48px;
        height: 48px;
        padding: 0;
        background: rgba(0, 212, 255, 0.1);
        border: 2px solid rgba(0, 212, 255, 0.2);
        border-radius: 10px;
        text-decoration: none;
        color: var(--primary-color);
        transition: all 0.3s ease;
        position: relative;
    }

    .download-link:hover {
        background: rgba(0, 212, 255, 0.2);
        border-color: var(--primary-color);
        box-shadow: 0 0 15px rgba(0, 212, 255, 0.3);
        transform: translateY(-3px);
    }

    .download-logo {
        width: 28px;
        height: 28px;
        object-fit: contain;
        filter: brightness(1.2);
    }

    .download-link:hover .download-logo {
        filter: brightness(1.5) drop-shadow(0 0 6px var(--primary-color));
    }

    /* Developer Credit Section */
    .footer-developer-section {
        text-align: center;
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid rgba(0, 212, 255, 0.1);
    }

    .developer-credit {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        text-decoration: none;
        color: var(--text-muted);
        font-size: 0.75rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .developer-credit:hover {
        color: var(--primary-color);
    }

    .heart-icon {
        font-size: 0.8rem;
    }

    .credit-text {
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }

    .developer-name {
        color: var(--primary-color);
        font-weight: 600;
    }

    .instagram-indicator {
        font-size: 0.65rem;
        color: var(--text-muted);
        opacity: 0.7;
    }
</style>

<script>
    // Ensure translations are applied to footer elements
    function applyFooterTranslations() {
        if (typeof applyLanguage === 'function') {
            applyLanguage();
        } else {
            // If translations not ready, try again after a short delay
            setTimeout(applyFooterTranslations, 100);
        }
    }

    // Apply immediately and on DOM ready
    applyFooterTranslations();
    document.addEventListener('DOMContentLoaded', applyFooterTranslations);

    // Back to Top Button
    const backToTopBtn = document.getElementById('backToTop');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 300) {
            backToTopBtn.classList.add('visible');
        } else {
            backToTopBtn.classList.remove('visible');
        }
    });
    backToTopBtn.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });

    // Newsletter form animation
    const newsletterInput = document.querySelector('.newsletter-input');
    if (newsletterInput) {
        newsletterInput.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        newsletterInput.addEventListener('blur', function() {
            this.parentElement.classList.remove('focused');
        });
    }
</script>

<script src="main.js"></script>
</body>

</html>