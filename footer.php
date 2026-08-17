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
                        <img src="hsenn.jpeg" alt="THE TRΛDING ROUTINE Logo" class="footer-logo-img">
                        <div class="logo-glow-ring"></div>
                    </div>
                    <span class="footer-brand-text">THE TRΛDING ROUTINE</span>
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
                <p class="copyright-text">&copy; 2026 THE TRΛDING ROUTINE. All rights reserved.</p>
                <p class="footer-legal-name">THE TRADING ROUTINE is operated by HUSSEIN SHEIKH ALI.</p>
                <nav class="footer-legal-links" aria-label="Legal links">
                    <a href="privacy-policy.php">Privacy Policy</a>
                    <span aria-hidden="true">&bull;</span>
                    <a href="data-deletion.php">Data Deletion</a>
                </nav>
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

    /* Compact footer height on laptop and phone */
    .simple-footer {
        margin-top: 45px;
        padding-bottom: 12px;
    }

    .footer-wave {
        height: 38px;
        margin-bottom: 18px;
    }

    .footer-main-row {
        gap: 32px;
        margin-bottom: 18px;
    }

    .footer-brand-minimal .footer-logo {
        margin-bottom: 10px;
        gap: 10px;
    }

    .footer-logo-img {
        width: 52px;
        height: 52px;
        aspect-ratio: 1 / 1;
        border-radius: 50% !important;
        object-fit: cover;
        border: none;
        filter: none;
        box-shadow: none;
    }

    .footer-brand-minimal .footer-logo:hover .footer-logo-img {
        filter: none;
        box-shadow: none;
    }

    .logo-glow-ring {
        display: none;
    }

    .footer-tagline {
        margin-bottom: 10px;
        line-height: 1.45;
    }

    .footer-links-title,
    .footer-social-title {
        margin-bottom: 12px;
    }

    .footer-links-title {
        display: block;
        margin: 0 0 28px !important;
        padding-bottom: 8px;
        line-height: 1.3;
    }

    .footer-links-title + .footer-nav-simple,
    .footer-links-column .footer-nav-simple {
        margin-top: 8px;
    }

    .footer-nav-simple {
        display: grid;
        grid-template-columns: repeat(2, minmax(85px, 1fr));
        gap: 2px 14px;
    }

    .footer-nav-simple a {
        padding: 4px 0;
        font-size: 0.88rem;
    }

    .footer-social-simple { gap: 9px; }
    .social-icon-wrapper { width: 39px; height: 39px; border-radius: 10px; }

    .download-section {
        margin-top: 0.9rem;
        padding-top: 0.9rem;
    }

    .download-link { width: 40px; height: 40px; }
    .download-logo { width: 23px; height: 23px; }

    .trading-hours-badge {
        margin-top: 13px;
        padding: 8px 14px;
        font-size: 0.78rem;
    }

    .footer-bottom-divider { margin: 16px 0 8px; }
    .footer-copy-row { padding: 10px 0; justify-content: center; text-align: center; }
    .footer-left { flex: 0 1 auto; width: 100%; text-align: center; }
    .copyright-text { margin: 0; }
    .footer-legal-name {
        margin: 0.25rem 0 0;
        color: rgba(255, 255, 255, 0.72);
        font-size: 0.78rem;
        line-height: 1.4;
        text-align: center;
    }
    .footer-legal-links {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 0.55rem;
        margin-top: 0.35rem;
        font-size: 0.78rem;
    }
    .footer-legal-links a { color: rgba(255, 255, 255, 0.78); text-decoration: none; }
    .footer-legal-links a:hover { color: #5ee5ff; text-decoration: underline; }

    @media (max-width: 768px) {
        .simple-footer { margin-top: 32px; padding: 0 1rem 10px; }
        .footer-wave { height: 24px; margin-bottom: 10px; }
        .footer-main-row { gap: 18px; margin-bottom: 10px; }
        .footer-brand-text { font-size: 1rem; }
        .footer-tagline { max-width: 440px; margin: 0 auto 5px; font-size: 0.82rem; line-height: 1.4; }
        .footer-links-title, .footer-social-title { font-size: 0.95rem; margin-bottom: 10px; }
        .footer-links-title { margin-bottom: 24px !important; }
        .footer-nav-simple {
            display: grid !important;
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            gap: 8px !important;
            max-width: 340px;
            margin: 0 auto !important;
        }
        .footer-nav-simple a {
            width: auto !important;
            min-height: 36px;
            padding: 7px 10px;
            justify-content: flex-start !important;
            gap: 7px;
            font-size: 0.82rem;
            border-radius: 9px;
            background: rgba(255,255,255,.035);
            border: 1px solid rgba(255,255,255,.06);
        }
        .footer-nav-simple a .nav-dot { width: 6px; height: 6px; flex-shrink: 0; }
        .download-section { margin-top: .7rem; padding-top: .7rem; }
        .trading-hours-badge { margin-top: 10px; }
        .footer-bottom-divider { margin: 12px 0 5px; }
        .footer-copy-row { padding: 6px 0; gap: 8px; font-size: .78rem; }
    }

    @media (max-width: 380px) {
        .footer-nav-simple { grid-template-columns: 1fr !important; max-width: 240px; }
        .footer-nav-simple a { justify-content: center !important; }
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

<?php if (isset($_SESSION['user_id'])): ?>
<div class="phone-verification-modal" id="phoneVerificationModal" hidden>
    <div class="phone-verification-card" role="dialog" aria-modal="true" aria-labelledby="phoneVerificationTitle">
        <button type="button" class="phone-verification-close" id="phoneVerificationClose" aria-label="Close">&times;</button>
        <div class="phone-verification-icon"><i class="fab fa-whatsapp" aria-hidden="true"></i></div>
        <h2 id="phoneVerificationTitle">Verify your WhatsApp number</h2>
        <p>We need a verified WhatsApp number before creating your pending order.</p>
        <div id="phoneVerificationPhoneStep">
            <label for="phoneVerificationPhone">WhatsApp number</label>
            <input type="tel" id="phoneVerificationPhone" autocomplete="tel" placeholder="Your phone number">
            <button type="button" class="phone-verification-primary" id="phoneVerificationSend">Send verification code</button>
        </div>
        <div id="phoneVerificationCodeStep" hidden>
            <label for="phoneVerificationCode">Six-digit code</label>
            <input type="text" inputmode="numeric" maxlength="6" id="phoneVerificationCode" autocomplete="one-time-code" placeholder="000000">
            <button type="button" class="phone-verification-primary" id="phoneVerificationVerify">Verify and continue</button>
            <button type="button" class="phone-verification-secondary" id="phoneVerificationBack">Change number</button>
        </div>
        <p class="phone-verification-message" id="phoneVerificationMessage" aria-live="polite"></p>
    </div>
</div>
<style>
.phone-verification-modal[hidden]{display:none}.phone-verification-modal{position:fixed;inset:0;z-index:100000;display:grid;place-items:center;padding:1rem;background:rgba(3,8,24,.82);backdrop-filter:blur(8px)}.phone-verification-card{position:relative;width:min(440px,100%);padding:2rem;border:1px solid rgba(47,216,255,.35);border-radius:22px;background:linear-gradient(145deg,#0d1a38,#09142c);box-shadow:0 24px 70px rgba(0,0,0,.5);color:#eaf6ff}.phone-verification-card h2{margin:.55rem 0;text-align:center;color:#fff}.phone-verification-card>p{text-align:center;color:#9fb1ca;line-height:1.55}.phone-verification-icon{margin:auto;width:58px;height:58px;display:grid;place-items:center;border-radius:18px;background:rgba(37,211,102,.14);color:#46e68a;font-size:2rem}.phone-verification-close{position:absolute;right:.8rem;top:.65rem;border:0;background:transparent;color:#aac0d8;font-size:1.8rem;cursor:pointer}.phone-verification-card label{display:block;margin:1rem 0 .45rem;color:#cfe9f6;font-weight:700}.phone-verification-card input{width:100%;box-sizing:border-box;padding:.9rem 1rem;border:1px solid #28496a;border-radius:12px;background:#071126;color:#fff;font-size:1rem}.phone-verification-card .iti{width:100%}.phone-verification-primary,.phone-verification-secondary{width:100%;margin-top:1rem;padding:.9rem 1rem;border-radius:12px;font-weight:800;cursor:pointer}.phone-verification-primary{border:0;background:linear-gradient(135deg,#20d5f5,#39df96);color:#061326}.phone-verification-secondary{border:1px solid #315575;background:transparent;color:#b9d4e8}.phone-verification-message{min-height:1.4em;margin:.8rem 0 0!important;font-size:.9rem}
</style>
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@29.1.0/dist/js/intlTelInput.min.js"></script>
<script src="phone_verification.js?v=<?php echo file_exists(__DIR__ . '/phone_verification.js') ? filemtime(__DIR__ . '/phone_verification.js') : time(); ?>"></script>
<?php endif; ?>
<script src="main.js?v=<?php echo file_exists(__DIR__ . '/main.js') ? filemtime(__DIR__ . '/main.js') : time(); ?>"></script>
</body>

</html>
