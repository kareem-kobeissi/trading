<?php
// index.php
include 'header.php';
?>
<!-- Force Password Change Banner -->




</script>
<!-- ===== HERO SECTION ===== -->
<section class="hero">
    <div class="hero-content">
        <h1 data-i18n="tradeWithPrecision">Trade With Institutional Precision.</h1>
        <p class="subtitle" data-i18n="tradeWithPrecisionDesc">Master professional Stock and Forex strategies used by
            the top 1%. Join a community built on rules, not hype.</p>
        <div class="hero-buttons">
            <a href="courses.php" class="btn btn-primary" data-i18n="viewStrategy">View The Strategy</a>
            <a href="about.php" class="btn btn-secondary" data-i18n="joinCommunity">Join The Community</a>
        </div>
    </div>
</section>

<!-- ===== BROKER PARTNERSHIP SECTION ===== -->
<section class="broker-section"
    style="width: 100%; display: flex; justify-content: center; align-items: center; padding: 5rem 2%; background: linear-gradient(135deg, rgba(0, 212, 255, 0.1), rgba(0, 100, 200, 0.08));">
    <div class="broker-container" style="width: 100%; max-width: 1200px; margin: 0 auto; padding: 0 2%;">
        <div class="broker-content"
            style="background: linear-gradient(135deg, rgba(26, 31, 58, 0.9), rgba(10, 14, 39, 1)); border-radius: 20px; padding: 4rem 3rem; border: 2px solid rgba(0, 212, 255, 0.2); text-align: center; box-shadow: 0 15px 50px rgba(0, 0, 0, 0.3); width: 100%;">
            <h2 class="broker-title" style="font-size: 2.5rem; font-weight: 900; margin-bottom: 1rem; color: #00d4ff;"
                data-i18n="executeMarkets">Execute Like a Professional.</h2>
            <p class="broker-subtitle"
                style="font-size: 1.2rem; color: rgba(225, 225, 227, 0.95); margin-bottom: 2rem; line-height: 1.6;"
                data-i18n="executeMarketsDesc">Connect with our preferred broker for institutional spreads and
                lightning-fast execution.</p>
            <a href="broker.php" class="btn btn-primary" style="margin-top: 2rem; display: inline-block;"
                data-i18n="registerNow">Register Now</a>
        </div>
    </div>
</section>

<!-- ===== EXPERT ADVISORS PROMO SECTION ===== -->
<section class="ea-promo-section" style="width: 100%; display: flex; justify-content: center; align-items: center; padding: 2rem 2% 8rem; background: transparent;">
    <div class="ea-promo-container" style="width: 100%; max-width: 1200px; margin: 0 auto; padding: 0 2%;">
        <div class="ea-promo-content" style="background: linear-gradient(135deg, rgba(26, 31, 58, 0.95), rgba(10, 14, 39, 1)); border-radius: 20px; padding: 5rem 3rem; border: 2px solid rgba(0, 212, 255, 0.3); text-align: center; box-shadow: 0 20px 60px rgba(0, 212, 255, 0.15); width: 100%; backdrop-filter: blur(15px); position: relative; overflow: hidden;">
            <!-- Decorative Glow -->
            <div style="position: absolute; top: -50%; left: -50%; width: 200%; height: 200%; background: radial-gradient(circle at center, rgba(0, 212, 255, 0.05) 0%, transparent 70%); pointer-events: none;"></div>
            
            <h2 style="font-size: clamp(1.6rem, 3vw, 2rem); font-weight: 900; margin-bottom: 1.5rem; color: #fff; text-transform: uppercase; letter-spacing: 2px; background: linear-gradient(135deg, #00d4ff, #00b894); -webkit-background-clip: text; -webkit-text-fill-color: transparent; position: relative; z-index: 1;">Expert Advisors</h2>
            <p style="font-size: 1rem; color: rgba(225, 225, 227, 0.95); max-width: 850px; margin: 0 auto 2rem; line-height: 1.7; position: relative; z-index: 1;">
                Unlock the power of automated trading with Expert Advisors designed for traders who value precision, discipline, and performance. 
                Our EAs help remove emotional decision-making and bring more structure to your execution, allowing you to trade with greater consistency and confidence. 
                Explore advanced tools built to support your strategy, improve efficiency, and give you a smarter way to approach the markets.
            </p>
            <div style="position: relative; z-index: 1;">
                <a href="ea.php" class="btn btn-primary">
                    Discover Our EAs
                </a>
            </div>
        </div>
    </div>
</section>

<!-- ===== FEATURED COURSES ===== -->


<!-- ===== FEATURES SECTION ===== -->
<section class="section">
    <h2 class="section-title" data-i18n="whyChooseUs">Why Choose Us?</h2>
    <div class="features-grid">
        <div class="feature-card" style="animation-delay: 0s;">
            <div class="feature-image">
                <img src="111.png" alt="Expert Instructors" class="feature-img">
            </div>
            <div class="feature-content">
                <h3 class="feature-title" data-i18n="expertInstructors">Expert Instructors</h3>
                <p class="feature-description" data-i18n="expertInstructorsDescHome">Institutional Mentorship. Learn
                    from veterans with decades of experience in global equity and currency markets.</p>
            </div>
        </div>
        <div class="feature-card" style="animation-delay: 0.1s;">
            <div class="feature-image">
                <img src="222.png" alt="Proven Strategies" class="feature-img">
            </div>
            <div class="feature-content">
                <h3 class="feature-title" data-i18n="provenStrategies">Proven Strategies</h3>
                <p class="feature-description" data-i18n="provenStrategiesDescHome">Rules-Based Frameworks. Stop
                    guessing. We teach repeatable, data-backed routines designed for long-term consistency.</p>
            </div>
        </div>
        <div class="feature-card" style="animation-delay: 0.2s;">
            <div class="feature-image">
                <img src="333.png" alt="Lifetime Access" class="feature-img">
            </div>
            <div class="feature-content">
                <h3 class="feature-title" data-i18n="lifetimeAccess">Lifetime Access</h3>
                <p class="feature-description" data-i18n="lifetimeAccessDescHome">Your Career Headquarters. Get
                    permanent access to all curriculum updates, live market reviews, and our private trading floor.</p>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
