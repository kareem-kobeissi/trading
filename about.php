<?php
// about.php
include 'header.php';
?>

<!-- ===== ABOUT HEADER SECTION ===== -->
<section class="about-header-section">
    <div class="about-header-content">
        <h1 class="about-main-title" data-i18n="aboutTrading">About TheTradingRoutine</h1>
        <p class="about-subtitle" data-i18n="buildingDisciplined">Building disciplined traders through structure, strategy, and real market experience.</p>
    </div>
</section>

<!-- ===== MEET THE FOUNDER SECTION ===== -->
<div class="about-container">
    <div class="about-content">
        <div class="about-image">
            <img src="3.jpeg" alt="Hussein Sheikh Ali - Founder" class="about-image-img" loading="lazy">
        </div>
        <div class="about-text">
            <h2 data-i18n="meetFounder">Meet the Founder</h2>
            <p data-i18n="founderBio"><strong>Hussein Sheikh Ali</strong> is a trader with over 4 years of experience in global financial markets, specializing in gold, indices, and price-action based market structure analysis.</p>
            <p data-i18n="frameworkDescription">Through years of studying institutional behavior and market psychology, he developed The Trading Routine framework — a structured approach designed to help traders eliminate randomness and trade with discipline.</p>

        </div>
    </div>
</div>

<!-- ===== OUR PHILOSOPHY SECTION ===== -->
<section class="about-philosophy-section">
    <div class="philosophy-container">
        <h2 data-i18n="philosophy">Our Philosophy</h2>
        <p class="philosophy-intro" data-i18n="philosophyIntroText">At The Trading Routine, we believe:</p>
        <div class="philosophy-grid">
            <div class="philosophy-card">
                <p data-i18n="tradingSkill">Trading is a skill, not gambling</p>
            </div>
            <div class="philosophy-card">
                <p data-i18n="disciplineBeat">Discipline beats intelligence</p>
            </div>
            <div class="philosophy-card">
                <p data-i18n="consistency">Consistency comes from rules and routines</p>
            </div>
            <div class="philosophy-card">
                <p data-i18n="riskManagement">Risk management is the foundation of survival</p>
            </div>
        </div>
    </div>
</section>

<!-- ===== OUR MISSION SECTION (CENTERED) ===== -->
<section class="about-mission-section">
    <div class="mission-container">
        <h2 data-i18n="mission">Our Mission</h2>
        <p><span class="brand-name">The Trading Routine</span> <span data-i18n="missionText">was created to help traders move from randomness to structured, professional decision-making.</span></p>
        <p>Our goal is simple: teach traders how to read markets, control risk, and build long-term consistency using institutional-grade concepts.</p>
    </div>
</section>

<!-- ===== STATS SECTION ===== -->


<!-- ===== INFO SECTION ===== -->
<section class="about-info-section">
    <div class="info-header">
        <h2 data-i18n="whyChooseUs">Why Choose Us</h2>
        <p data-i18n="everythingNeeded">Everything you need for trading success</p>
    </div>
    <div class="info-container">
        <a href="courses.php" class="info-card info-card-link">
            <div class="info-card-top">
                <h3 data-i18n="infoCard1">Elite Education</h3>
            </div>
            <p data-i18n="infoCard1Desc">The information provided is for educational purposes only and does not represent trading or investment recommendations.</p>
            <div class="info-card-footer">
                <span class="info-link" data-i18n="viewCourses">View Courses →</span>
            </div>
        </a>
        <a href="contact.php" class="info-card info-card-link">
            <div class="info-card-top">
                <h3 data-i18n="infoCard2">Advanced Support</h3>
            </div>
            <p data-i18n="infoCard2Desc">Dedicated support via Email or Whatsapp.</p>
            <div class="info-card-footer">
                <span class="info-link" data-i18n="contactUs">Contact Us →</span>
            </div>
        </a>
        <a href="broker.php" class="info-card info-card-link">
            <div class="info-card-top">
                <h3 data-i18n="infoCard3">Broker on Your Side</h3>
            </div>
            <p data-i18n="infoCard3Desc">If you don't have a broker yet, we can guide you on how to choose a reliable one.</p>
            <div class="info-card-footer">
                <span class="info-link" data-i18n="viewBrokers">View Brokers →</span>
            </div>
        </a>
    </div>
</section>

<!-- ===== ABOUT CTA SECTION ===== -->
<section class="about-cta-section">
    <div class="about-cta-content">
        <h2 class="about-cta-title" data-i18n="marketToSkill">Turn Market Knowledge Into Real Skill</h2>
        <p class="about-cta-subtitle" data-i18n="learnProfessional">Learn professional trading strategies, risk management, and market structure used by experienced traders.</p>
        <a href="signup.php" class="cta-button" id="joinTradeAcademyBtn">
            <span data-i18n="becomeProTrader">Become a Pro Trader</span>
        </a>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var joinBtn = document.getElementById('joinTradeAcademyBtn');
                if (joinBtn) {
                    joinBtn.addEventListener('click', function(e) {
                        if (sessionStorage.getItem('userLogged') === 'true') {
                            e.preventDefault();
                            window.location.href = 'courses.php';
                        }
                    });
                }
            });
        </script>
    </div>
</section>

<style>
    .about-header-icon {
        font-size: 4rem;
        margin-bottom: 1.5rem;
        animation: bounce 2s infinite;
    }

    /* ===== FOUNDER SECTION STYLES ===== */
    .about-text {
        background: linear-gradient(135deg, rgba(0, 212, 255, 0.05), rgba(0, 184, 148, 0.05));
        padding: 3rem;
        border-radius: 20px;
        border: 2px solid rgba(0, 212, 255, 0.2);
    }

    .about-text h2 {
        font-size: 2.2rem;
        margin-bottom: 1.5rem;
        background: linear-gradient(135deg, var(--primary-color), #00b894);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .about-text p {
        font-size: 1.1rem;
        line-height: 1.8;
        margin-bottom: 1.5rem;
        color: var(--text-light);
    }

    .founder-badges {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1.5rem;
        margin-top: 2.5rem;
        padding-top: 2.5rem;
        border-top: 1px solid rgba(0, 212, 255, 0.2);
    }

    .badge {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.8rem;
        padding: 1.5rem;
        background: linear-gradient(145deg, rgba(26, 31, 58, 0.8), rgba(10, 14, 39, 0.9));
        border-radius: 15px;
        border: 1px solid rgba(0, 212, 255, 0.15);
        transition: all 0.3s ease;
    }

    .badge:hover {
        transform: translateY(-5px);
        border-color: var(--primary-color);
        box-shadow: 0 10px 30px rgba(0, 212, 255, 0.15);
    }

    .badge-icon {
        font-size: 2rem;
    }

    .badge-text {
        font-size: 0.95rem;
        font-weight: 600;
        color: var(--text-light);
        text-align: center;
    }

    .about-stats-section {
        padding: 4rem 5%;
        max-width: 1200px;
        margin: 0 auto;
    }

    .about-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 2rem;
    }

    .about-stat-card {
        background: linear-gradient(145deg, rgba(26, 31, 58, 0.8), rgba(10, 14, 39, 0.9));
        border-radius: 20px;
        padding: 2.5rem 2rem;
        text-align: center;
        border: 1px solid rgba(0, 212, 255, 0.15);
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        animation: fadeInUp 0.6s ease-out backwards;
    }

    .about-stat-card:nth-child(1) {
        animation-delay: 0.1s;
    }

    .about-stat-card:nth-child(2) {
        animation-delay: 0.2s;
    }

    .about-stat-card:nth-child(3) {
        animation-delay: 0.3s;
    }

    .about-stat-card:nth-child(4) {
        animation-delay: 0.4s;
    }

    .about-stat-card:hover {
        transform: translateY(-10px) scale(1.02);
        border-color: var(--primary-color);
        box-shadow: 0 20px 50px rgba(0, 212, 255, 0.2);
    }

    .about-stat-number {
        display: block;
        font-size: 3rem;
        font-weight: 800;
        background: linear-gradient(135deg, var(--primary-color), #00b894);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 0.5rem;
    }

    .about-stat-label {
        color: var(--text-muted);
        font-size: 1rem;
        font-weight: 600;
    }

    .cta-button {
        display: inline-flex;
        align-items: center;
        gap: 0.8rem;
    }

    .cta-arrow {
        transition: transform 0.3s ease;
    }

    .cta-button:hover .cta-arrow {
        transform: translateX(5px);
    }

    /* ===== PHILOSOPHY SECTION STYLES ===== */
    .about-philosophy-section {
        padding: 4rem 5%;
        max-width: 1200px;
        margin: 0 auto;
        background: linear-gradient(180deg, rgba(26, 31, 58, 0.3), rgba(0, 0, 0, 0.1));
        border-radius: 20px;
        margin-bottom: 3rem;
    }

    .philosophy-container h2 {
        text-align: center;
        font-size: 2.5rem;
        margin-bottom: 1rem;
        background: linear-gradient(135deg, var(--primary-color), #00b894);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .philosophy-intro {
        text-align: center;
        color: var(--text-muted);
        font-size: 1.1rem;
        margin-bottom: 3rem;
    }

    .philosophy-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 2rem;
        margin-top: 2rem;
    }

    .philosophy-card {
        background: linear-gradient(145deg, rgba(26, 31, 58, 0.8), rgba(10, 14, 39, 0.9));
        border-radius: 15px;
        padding: 2.5rem 1.5rem;
        text-align: center;
        border: 1px solid rgba(0, 212, 255, 0.1);
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .philosophy-card:hover {
        transform: translateY(-8px);
        border-color: var(--primary-color);
        box-shadow: 0 15px 40px rgba(0, 212, 255, 0.15);
    }

    .philosophy-icon {
        font-size: 2.5rem;
        margin-bottom: 1rem;
    }

    .philosophy-card p {
        font-size: 1.05rem;
        line-height: 1.6;
        color: var(--text-light);
        font-weight: 500;
    }

    /* ===== MISSION SECTION STYLES ===== */
    .about-mission-section {
        padding: 5rem 5%;
        max-width: 900px;
        margin: 3rem auto;
        text-align: center;
        background: linear-gradient(135deg, rgba(0, 212, 255, 0.05), rgba(0, 184, 148, 0.05));
        border-radius: 25px;
        border: 2px solid rgba(0, 212, 255, 0.2);
    }

    .mission-container h2 {
        font-size: 2.8rem;
        margin-bottom: 2rem;
        background: linear-gradient(135deg, var(--primary-color), #00b894);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .mission-container p {
        font-size: 1.15rem;
        line-height: 1.8;
        color: var(--text-light);
        margin-bottom: 1.5rem;
        max-width: 700px;
        margin-left: auto;
        margin-right: auto;
    }

    .mission-container p:last-child {
        margin-bottom: 0;
    }

    /* ===== INFO SECTION STYLES ===== */
    .about-info-section {
        padding: 5rem 5%;
        max-width: 1400px;
        margin: 4rem auto;
        position: relative;
    }

    .info-header {
        text-align: center;
        margin-bottom: 4rem;
    }

    .info-header h2 {
        font-size: 2.8rem;
        margin-bottom: 0.8rem;
        background: linear-gradient(135deg, var(--primary-color), #00b894);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .info-header p {
        font-size: 1.1rem;
        color: var(--text-muted);
    }

    .info-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2.5rem;
    }

    .info-card {
        background: linear-gradient(145deg, rgba(26, 31, 58, 0.95), rgba(10, 14, 39, 0.95));
        border-radius: 20px;
        padding: 3rem 2.5rem;
        border: 2px solid rgba(0, 212, 255, 0.1);
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        position: relative;
        overflow: hidden;
    }

    .info-card-link {
        text-decoration: none !important;
        color: inherit !important;
        cursor: pointer;
    }

    .info-card-link h3 {
        color: var(--text-light) !important;
    }

    .info-card-link p {
        color: var(--text-muted) !important;
    }

    .info-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(0, 212, 255, 0.1), transparent);
        transition: left 0.5s ease;
        z-index: 0;
    }

    .info-card:hover::before {
        left: 100%;
    }

    .info-card:hover {
        transform: translateY(-15px);
        border-color: var(--primary-color);
        box-shadow: 0 30px 60px rgba(0, 212, 255, 0.2);
    }

    .info-card-top {
        display: flex;
        align-items: center;
        gap: 1.2rem;
        margin-bottom: 1.5rem;
        position: relative;
        z-index: 2;
    }

    .info-icon {
        font-size: 3rem;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 70px;
        height: 70px;
        background: linear-gradient(135deg, rgba(0, 212, 255, 0.1), rgba(0, 184, 148, 0.1));
        border-radius: 15px;
        border: 2px solid rgba(0, 212, 255, 0.2);
    }

    .info-card h3 {
        font-size: 1.6rem;
        color: var(--text-light);
        margin: 0;
        font-weight: 700;
    }

    .info-card p {
        font-size: 1.05rem;
        line-height: 1.7;
        color: var(--text-muted);
        margin-bottom: 2rem;
        flex-grow: 1;
        position: relative;
        z-index: 2;
    }

    .info-card-footer {
        position: relative;
        z-index: 2;
    }

    .info-link {
        color: var(--primary-color);
        font-size: 0.95rem;
        font-weight: 600;
        transition: color 0.3s ease;
    }

    .info-link:hover {
        color: #00b894;
    }
</style>

<?php include 'footer.php'; ?>