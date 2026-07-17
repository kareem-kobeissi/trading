<?php
// broker.php - Trading Broker Partnership Page
include 'header.php';
?>

<style>
    /* ===== BROKER PAGE STYLING ===== */
    .broker-page-hero {
        background: linear-gradient(135deg, rgba(0, 212, 255, 0.1), rgba(0, 100, 200, 0.1));
        padding: 6rem 5%;
        text-align: center;
        min-height: 60vh;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }

    .broker-page-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background:
            radial-gradient(circle at 20% 50%, rgba(0, 212, 255, 0.05) 0%, transparent 50%),
            radial-gradient(circle at 80% 80%, rgba(0, 100, 200, 0.05) 0%, transparent 50%);
        pointer-events: none;
    }

    .broker-hero-content {
        position: relative;
        z-index: 2;
        max-width: 800px;
        animation: fadeInUp 0.8s ease-out;
    }

    .broker-hero-title {
        font-size: 3.5rem;
        font-weight: 900;
        margin-bottom: 1.5rem;
        color: #fff;
        line-height: 1.2;
        background: linear-gradient(135deg, var(--primary-color), #00b894);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .broker-hero-subtitle {
        font-size: 1.3rem;
        color: var(--text-muted);
        margin-bottom: 2rem;
        line-height: 1.6;
    }

    /* ===== BROKER INFO SECTION ===== */
    .broker-info-section {
        padding: 6rem 5%;
        background: linear-gradient(180deg, rgba(26, 31, 58, 0.5), rgba(10, 14, 39, 0.5));
    }

    .broker-info-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 2.5rem;
        max-width: 1200px;
        margin: 0 auto;
        align-items: stretch;
    }

    .broker-info-card {
        background: linear-gradient(135deg, rgba(26, 31, 58, 0.9), rgba(10, 14, 39, 1));
        border-radius: 16px;
        padding: 3rem 2.5rem;
        border: 1px solid rgba(0, 212, 255, 0.15);
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        animation: fadeInUp 0.7s ease-out backwards;
        display: flex;
        flex-direction: column;
    }

    .broker-info-card:nth-child(1) {
        animation-delay: 0.1s;
    }

    .broker-info-card:nth-child(2) {
        animation-delay: 0.2s;
    }

    .broker-info-card:nth-child(3) {
        animation-delay: 0.3s;
    }

    .broker-info-card:hover {
        transform: translateY(-8px);
        border-color: var(--primary-color);
        box-shadow:
            0 20px 60px rgba(0, 212, 255, 0.15),
            0 0 40px rgba(0, 212, 255, 0.1),
            inset 0 1px 0 rgba(255, 255, 255, 0.08);
    }

    .broker-info-icon {
        font-size: 2rem;
        margin-bottom: 0.8rem;
        height: 2rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .broker-info-title {
        font-size: 1.4rem;
        color: #fff;
        margin-bottom: 1rem;
        font-weight: 700;
        min-height: auto;
        display: flex;
        align-items: center;
    }

    .broker-info-text {
        color: var(--text-muted);
        line-height: 1.6;
        font-size: 1rem;
        flex: 1;
        margin-top: auto;
    }

    /* ===== BROKER FEATURES ===== */
    .broker-features-section {
        padding: 6rem 5%;
        background: linear-gradient(180deg, rgba(10, 14, 39, 0.5), rgba(5, 7, 25, 0.5));
    }

    .broker-features-title {
        text-align: center;
        font-size: 2.8rem;
        font-weight: 900;
        margin-bottom: 1rem;
        color: #fff;
    }

    .broker-features-subtitle {
        text-align: center;
        font-size: 1.2rem;
        color: var(--text-muted);
        margin-bottom: 4rem;
        max-width: 700px;
        margin-left: auto;
        margin-right: auto;
        line-height: 1.6;
    }

    .broker-features-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
        max-width: 1400px;
        margin: 0 auto;
        align-items: stretch;
    }

    .broker-feature-item {
        background: linear-gradient(135deg, rgba(26, 31, 58, 0.8), rgba(10, 14, 39, 0.9));
        border: 1px solid rgba(0, 212, 255, 0.15);
        border-radius: 16px;
        padding: 1.5rem 1.2rem;
        text-align: center;
        animation: fadeInUp 0.7s ease-out backwards;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
    }

    .broker-feature-item:hover {
        border-color: var(--primary-color);
        box-shadow: 0 15px 50px rgba(0, 212, 255, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.05);
        transform: translateY(-8px);
    }

    .broker-feature-item:nth-child(1) {
        animation-delay: 0s;
    }

    .broker-feature-item:nth-child(2) {
        animation-delay: 0.1s;
    }

    .broker-feature-item:nth-child(3) {
        animation-delay: 0.2s;
    }

    .broker-feature-item:nth-child(4) {
        animation-delay: 0.3s;
    }

    .broker-feature-item:nth-child(5) {
        animation-delay: 0.4s;
    }

    .broker-feature-item:nth-child(6) {
        animation-delay: 0.5s;
    }

    .broker-feature-emoji {
        font-size: 2rem;
        margin-bottom: 0.8rem;
        display: inline-block;
    }

    .broker-feature-name {
        font-size: 1rem;
        color: #fff;
        font-weight: 800;
        margin-bottom: 0.6rem;
    }

    .broker-feature-description {
        font-size: 0.8rem;
        color: var(--text-muted);
        line-height: 1.5;
        flex: 1;
    }

    /* ===== CTA SECTION ===== */
    .broker-cta-section {
        padding: 8rem 5%;
        background: linear-gradient(135deg, rgba(0, 212, 255, 0.1), rgba(0, 100, 200, 0.1));
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .broker-cta-section::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, rgba(0, 212, 255, 0.1), transparent);
        border-radius: 50%;
        animation: pulse 4s ease-in-out infinite;
    }

    .broker-cta-content {
        position: relative;
        z-index: 2;
        max-width: 600px;
        margin: 0 auto;
    }

    .broker-cta-title {
        font-size: 2.8rem;
        font-weight: 900;
        margin-bottom: 1.5rem;
        color: #fff;
    }

    .broker-cta-text {
        font-size: 1.1rem;
        color: var(--text-muted);
        margin-bottom: 2.5rem;
        line-height: 1.8;
    }

    .broker-register-btn {
        display: inline-block;
        padding: 1.3rem 3.5rem;
        background: linear-gradient(135deg, var(--primary-color), #0099ff);
        color: #fff;
        border: none;
        border-radius: 12px;
        font-size: 1.1rem;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 10px 40px rgba(0, 212, 255, 0.3);
        letter-spacing: 0.5px;
    }

    .broker-register-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 20px 60px rgba(0, 212, 255, 0.5);
    }

    .broker-register-btn:active {
        transform: translateY(-1px);
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 768px) {
        .broker-page-hero {
            padding: 4rem 3%;
            min-height: 50vh;
        }

        .broker-hero-title {
            font-size: 2.2rem;
        }

        .broker-hero-subtitle {
            font-size: 1.1rem;
        }

        .broker-info-section,
        .broker-features-section,
        .broker-cta-section {
            padding: 4rem 3%;
        }

        .broker-info-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }

        .broker-features-title {
            font-size: 2rem;
        }

        .broker-features-subtitle {
            font-size: 1.1rem;
            margin-bottom: 3rem;
        }

        .broker-features-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }

        .broker-cta-title {
            font-size: 1.8rem;
        }

        .broker-feature-item {
            padding: 2rem 1.5rem;
        }

        .broker-feature-emoji {
            font-size: 2.2rem;
        }

        .broker-feature-name {
            font-size: 1.1rem;
        }

        .broker-feature-description {
            font-size: 0.9rem;
        }
    }

    @media (max-width: 480px) {
        .broker-page-hero {
            padding: 3rem 2%;
            min-height: 40vh;
        }

        .broker-hero-title {
            font-size: 1.8rem;
        }

        .broker-hero-subtitle {
            font-size: 1rem;
        }

        .broker-info-section,
        .broker-features-section,
        .broker-cta-section {
            padding: 3rem 2%;
        }

        .broker-info-grid {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        .broker-features-title {
            font-size: 1.6rem;
            margin-bottom: 0.8rem;
        }

        .broker-features-subtitle {
            font-size: 1rem;
            margin-bottom: 2rem;
        }

        .broker-features-grid {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        .broker-cta-title {
            font-size: 1.5rem;
        }

        .broker-register-btn {
            padding: 1rem 2.5rem;
            font-size: 1rem;
        }

        .broker-feature-item {
            padding: 1.5rem 1rem;
        }

        .broker-feature-emoji {
            font-size: 2rem;
        }

        .broker-feature-name {
            font-size: 1rem;
            margin-bottom: 0.8rem;
        }

        .broker-feature-description {
            font-size: 0.85rem;
        }
    }
</style>

<!-- ===== BROKER PAGE HERO ===== -->
<section class="broker-page-hero">
    <div class="broker-hero-content">
        <h1 class="broker-hero-title" data-i18n="accessMarkets">Access Global Financial Markets</h1>
        <p class="broker-hero-subtitle" data-i18n="openAccount2">Open a live trading account with our regulated broker
            partner and start trading forex, indices , commodities , metals , energies.</p>
    </div>
</section>

<!-- ===== BROKER INFO CARDS ===== -->
<section class="broker-info-section">
    <div class="broker-info-grid">
        <div class="broker-info-card">
            <h3 class="broker-info-title" data-i18n="security">Security Enviroment</h3>
            <p class="broker-info-text" data-i18n="securityDesc">Trade with confidence through a regulated trading
                infrastructure designed to protect client funds and ensure a secure trading experience.</p>
        </div>

        <div class="broker-info-card">
            <h3 class="broker-info-title" data-i18n="ultraFast">Ultra-Fast Trade Execution</h3>
            <p class="broker-info-text" data-i18n="ultraFastDesc">Execute trades with institutional-grade speed and
                minimal latency, powered by advanced trading technology.</p>
        </div>

        <div class="broker-info-card">
            <h3 class="broker-info-title" data-i18n="industryLeading">Industry-Leading Spreads</h3>
            <p class="broker-info-text" data-i18n="industryLeadingDesc">Access competitive spreads across major markets,
                helping you costs and maximize trading efficiency.</p>
        </div>
    </div>
</section>

<!-- ===== BROKER FEATURES ===== -->
<section class="broker-features-section">
    <h2 class="broker-features-title" data-i18n="whyChoose">Why Traders Choose Our Broker Partner?</h2>
    <p class="broker-features-subtitle" data-i18n="accessPowerful">Access powerful trading technology, professional
        tools, and reliable support designed for modern traders.</p>
    <div class="broker-features-grid">
        <div class="broker-feature-item">
            <div class="broker-feature-name" data-i18n="multipleAssets">Multiple Asset Classes</div>
            <div class="broker-feature-description" data-i18n="multipleAssetsDesc">Trade forex, indices, stocks from a
                single platform.</div>
        </div>
        <div class="broker-feature-item">
            <div class="broker-feature-name" data-i18n="support247">24/7 Client Support</div>
            <div class="broker-feature-description" data-i18n="support247Desc">Access professional support anytime to
                ensure a smooth and uninterrupted trading experience.</div>
        </div>
        <div class="broker-feature-item">
            <div class="broker-feature-name" data-i18n="expertAdvisor">Expert Advisor (EA) Support</div>
            <div class="broker-feature-description" data-i18n="expertAdvisorDesc">Run automated trading strategies and
                Expert Advisors seamlessly on advanced trading platforms.</div>
        </div>
        <div class="broker-feature-item">
            <div class="broker-feature-name" data-i18n="instantFunding">Instant Funding & Withdrawals</div>
            <div class="broker-feature-description" data-i18n="instantFundingDesc">Easily deposit and withdraw funds
                through reliable payment solutions including Match2Pay, Wish Money, OMT, and secure trader payment
                portals.</div>
        </div>
    </div>
</section>

<!-- ===== BROKER CTA ===== -->
<section class="broker-cta-section">
    <div class="broker-cta-content">
        <h2 class="broker-cta-title" data-i18n="readyTrade">Ready to Trade?</h2>
        <p class="broker-cta-text" data-i18n="joinThousands">Join thousands of traders who trust our partner broker.
            Create your account today and get started with your trading journey.</p>
        <a href="https://portal.bbcorp.trade/auth/jwt/sign-up/partner/X2sUYi/prod/KAS663" target="_blank"
            class="broker-register-btn" data-i18n="registerNow">
            Register Now & Start Trading
        </a>
    </div>
</section>

<?php include 'footer.php'; ?>