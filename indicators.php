<?php
// indicators.php - TradingView indicator page
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

    .indicator-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.55rem;
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

    .indicator-product-card {
        background: linear-gradient(135deg, rgba(26, 31, 58, 0.95), rgba(10, 14, 39, 1));
        border: 2px solid rgba(0, 212, 255, 0.2);
        border-radius: 20px;
        padding: 3rem;
        margin-top: 2rem;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
        backdrop-filter: blur(10px);
    }

    .indicator-preview-image {
        display: block;
        width: 100%;
        aspect-ratio: 16 / 9;
        margin: 0 auto 2rem;
        object-fit: cover;
        border: 1px solid rgba(0, 212, 255, 0.25);
        border-radius: 16px;
        box-shadow: 0 18px 45px rgba(0, 0, 0, 0.42), 0 0 24px rgba(0, 212, 255, 0.08);
    }

    .indicator-actions {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 1rem;
        margin-top: 2.25rem;
    }

    .indicator-actions .btn {
        min-width: 220px;
        padding: 1rem 1.5rem;
        border-radius: 13px;
    }

    .indicator-whatsapp-btn {
        color: #fff !important;
        background: linear-gradient(135deg, #25d366, #128c4a) !important;
        box-shadow: 0 10px 28px rgba(37, 211, 102, 0.25) !important;
    }

    .indicator-whatsapp-btn:hover {
        box-shadow: 0 15px 36px rgba(37, 211, 102, 0.38) !important;
    }

    @media (max-width: 768px) {
        .indicators-title { font-size: 2.5rem; }
        .indicators-subtitle { font-size: 1.1rem; }
        .indicators-hero { padding: 4rem 3%; }
        .indicator-product-card { padding: 2rem 1rem; }
        .indicator-actions { display: grid; }
        .indicator-actions .btn { width: 100%; min-width: 0; }
    }
</style>

<section class="indicators-hero">
    <div class="indicators-content">
        <div class="indicator-badge"><i class="fas fa-chart-line"></i> TradingView Indicator</div>
        
        <h1 class="indicators-title">The Holly Grail</h1>
        


        <div class="indicator-product-card">
            <img src="holly-grail-preview.jpeg" alt="The Holly Grail indicator displayed on a TradingView gold chart" class="indicator-preview-image" loading="lazy">
            <h2 style="margin-bottom: 1rem;">Discover The Holly Grail Indicator</h2>
            <p style="color: var(--text-muted); line-height: 1.6;">
                Read the complete description and understand how the indicator works directly on TradingView. No payment or admin approval is required.
            </p>
            <div class="indicator-actions">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="record_free_access.php?product=indicator" target="_blank" rel="noopener noreferrer" class="btn btn-primary">
                        <i class="fas fa-external-link-alt"></i> Open Free Indicator
                    </a>
                <?php else: ?>
                    <a href="login.php?redirect=indicators.php" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
