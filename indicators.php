<?php
// indicators.php - Indicators Coming Soon Page
include 'header.php';
?>

<style>
    .indicators-hero {
        background: linear-gradient(135deg, rgba(0, 212, 255, 0.1), rgba(0, 100, 200, 0.1));
        padding: 8rem 5%;
        text-align: center;
        min-height: 70vh;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }

    .indicators-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: 
            radial-gradient(circle at 20% 40%, rgba(0, 212, 255, 0.08) 0%, transparent 40%),
            radial-gradient(circle at 80% 60%, rgba(0, 255, 136, 0.05) 0%, transparent 40%);
        animation: float 8s ease-in-out infinite;
        pointer-events: none;
    }

    .indicators-content {
        position: relative;
        z-index: 2;
        max-width: 800px;
        animation: fadeInUp 0.8s ease-out;
    }

    .coming-soon-badge {
        display: inline-block;
        padding: 0.6rem 1.5rem;
        background: rgba(0, 212, 255, 0.1);
        border: 1px solid var(--primary-color);
        border-radius: 50px;
        color: var(--primary-color);
        font-weight: 700;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 2px;
        margin-bottom: 2rem;
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.2);
    }

    .indicators-title {
        font-size: 4rem;
        font-weight: 900;
        margin-bottom: 1.5rem;
        line-height: 1.1;
        background: linear-gradient(135deg, #fff 30%, var(--primary-color));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .indicators-subtitle {
        font-size: 1.4rem;
        color: var(--text-muted);
        margin-bottom: 3rem;
        line-height: 1.6;
    }

    .working-box {
        background: linear-gradient(135deg, rgba(26, 31, 58, 0.95), rgba(10, 14, 39, 1));
        border: 2px solid rgba(0, 212, 255, 0.2);
        border-radius: 20px;
        padding: 3rem;
        margin-top: 2rem;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
        backdrop-filter: blur(10px);
    }

    .gear-icon {
        font-size: 3rem;
        color: var(--primary-color);
        margin-bottom: 1.5rem;
        animation: rotateGear 4s linear infinite;
        display: inline-block;
    }

    @keyframes rotateGear {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    @media (max-width: 768px) {
        .indicators-title { font-size: 2.5rem; }
        .indicators-subtitle { font-size: 1.1rem; }
        .indicators-hero { padding: 4rem 3%; }
    }
</style>

<section class="indicators-hero">
    <div class="indicators-content">
        <div class="coming-soon-badge">Coming Soon</div>
        
        <h1 class="indicators-title">Professional Trading Indicators</h1>
        
        <p class="indicators-subtitle">
            We are currently finalizing our proprietary technical indicators designed for institutional-level precision. 
            Soon, you'll be able to access the exact tools we use to identify high-probability setups.
        </p>

        <div class="working-box">
            <div class="gear-icon">⚙️</div>
            <h3 style="color: #fff; margin-bottom: 1rem; font-size: 1.8rem;">Currently Under Development</h3>
            <p style="color: var(--text-muted); line-height: 1.6;">
                Our engineering team is fine-tuning the algorithms to ensure 100% accuracy and performance. 
                Keep an eye on this space—something revolutionary is on its way.
            </p>
            <div style="margin-top: 2.5rem;">
                <a href="index.php" class="btn btn-primary" style="padding: 1rem 2.5rem; font-size: 1rem;">Back to Home</a>
                <a href="ea.php" class="btn btn-secondary" style="padding: 1rem 2.5rem; font-size: 1rem; margin-left: 1rem;">Explore EAs</a>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
