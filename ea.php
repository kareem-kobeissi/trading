<?php
require 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');

include 'header.php';
?>

<section class="ea-page-hero">
    <div class="premium-page-hero ea-premium-hero">
        <div class="premium-hero-copy">
            <span class="premium-hero-kicker">Automate • Control • Execute</span>
            <h1>Expert Advisors</h1>
            <p class="premium-hero-subtitle">Professional trading tools built for disciplined execution and precise risk control.</p>
        </div>
        <div class="premium-hero-visual ea-visual-wrapper tilt-visual" aria-hidden="true" id="eaTiltVisual">
            <div class="tilt-glow"></div>
            <img src="ea-workstation.webp" alt="" class="premium-hero-image premium-hero-image-wide"
                decoding="async" fetchpriority="high" id="eaHeroImg">



            <!-- Floating tech chips -->
            <div class="belief-chip chip-a ea-chip-a" style="top:8%;right:-5%;--cdur:6s;--cdelay:0s"><span class="icon-3d"><i class="fas fa-robot"></i></span> Automation</div>
            <div class="belief-chip chip-b ea-chip-b" style="top:28%;left:-8%;--cdur:6.5s;--cdelay:1.5s"><span class="icon-3d"><i class="fas fa-bolt"></i></span> Low Latency</div>
            <div class="belief-chip chip-c ea-chip-c" style="bottom:26%;right:-6%;--cdur:5.5s;--cdelay:3s"><span class="icon-3d"><i class="fas fa-shield-halved"></i></span> Smart Risk</div>
            <div class="belief-chip chip-d ea-chip-d" style="bottom:10%;left:-5%;--cdur:7s;--cdelay:0.8s"><span class="icon-3d"><i class="fas fa-gears"></i></span> Algorithmic</div>
            <!-- Live status badge -->
            <div class="img-badge" style="bottom:-14px;left:50%;transform:translateX(-50%);--bpdelay:.4s">
                <span class="badge-dot"></span> EA's Online
            </div>
        </div>
    </div>
</section>

<main class="section ea-tools-section">
    <h2>Risk Calculator</h2>

    <article class="site-card ea-access-card">
        <h3 class="ea-manager-title">TTR Trading Manager</h3>

        <?php
        $ea_status = 'none';
        if (isset($_SESSION['user_id'])) {
            $uid = (int)$_SESSION['user_id'];
            $eaStmt = $conn->prepare("SELECT oi.item_status, o.status FROM orders o JOIN order_items oi ON o.id = oi.order_id WHERE o.user_id = ? AND oi.product_type = 'ea' ORDER BY FIELD(oi.item_status, 'unlocked', 'pending', 'cancelled') LIMIT 1");
            if ($eaStmt) {
                $eaStmt->bind_param('i', $uid);
                $eaStmt->execute();
                $eaRes = $eaStmt->get_result();
                if ($eaRes && $eaRes->num_rows > 0) {
                    $eaRow = $eaRes->fetch_assoc();
                    $ea_status = $eaRow['item_status'] ?: $eaRow['status'];
                }
                $eaStmt->close();
            }
        }
        ?>

        <div id="eaActionContainer">
            <?php if (!isset($_SESSION['user_id'])): ?>
                <p class="ea-access-description">
                    Calculate position size from your account, entry, and stop loss. Plan risk consistently before every trade.
                </p>
                <a href="login.php?redirect=ea.php" class="download-btn">Login to Request Access</a>
            <?php elseif ($ea_status === 'unlocked'): ?>
                <p class="ea-access-status" style="color: #00ff88; font-weight: bold;">✓ Access Approved by Admin</p>
                <a href="download_ea.php" class="download-btn" style="background: linear-gradient(135deg, #00b894, #00d4ff);">Download Risk Calculator</a>
            <?php elseif ($ea_status === 'pending'): ?>
                <p class="ea-access-status" style="color: #f59d00; font-weight: bold;"><i class="fas fa-clock"></i> Request Submitted — Pending Admin Approval</p>
                <button type="button" class="download-btn" disabled style="opacity:.6;cursor:not-allowed;background:#444;">⏳ Awaiting Admin Approval</button>
            <?php else: ?>
                <p class="ea-access-description">
                    Calculate position size from your account, entry, and stop loss. Request access from the admin to unlock download.
                </p>
                <button type="button" class="download-btn" onclick="submitEaRequest(this)" style="cursor:pointer;border:none;">Request EA Access</button>
            <?php endif; ?>
        </div>

        <script>
            function checkEaApprovalSync() {
                fetch('check_access.php?product=ea', {
                        cache: 'no-store'
                    })
                    .then(r => r.json())
                    .then(data => {
                        const container = document.getElementById('eaActionContainer');
                        if (!container) return;
                        if (data.status === 'unlocked') {
                            container.innerHTML = `
                            <p class="ea-access-status" style="color: #00ff88; font-weight: bold;">✓ Access Approved by Admin</p>
                            <a href="download_ea.php" class="download-btn" style="background: linear-gradient(135deg, #00b894, #00d4ff);">Download Risk Calculator</a>
                        `;
                        } else if (data.status === 'pending') {
                            container.innerHTML = `
                            <p class="ea-access-status" style="color: #f59d00; font-weight: bold;"><i class="fas fa-clock"></i> Request Submitted — Pending Admin Approval</p>
                            <button type="button" class="download-btn" disabled style="opacity:.6;cursor:not-allowed;background:#444;">⏳ Awaiting Admin Approval</button>
                        `;
                        }
                    })
                    .catch(e => console.error(e));
            }
            setInterval(checkEaApprovalSync, 5000);

            async function submitEaRequest(btn) {
                if (window.TTRPhoneVerification && !(await window.TTRPhoneVerification.ensureVerifiedPhone())) {
                    return;
                }
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
                btn.style.opacity = '0.7';

                fetch('record_free_access.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: 'product=ea',
                        cache: 'no-store'
                    })
                    .then(r => r.json())
                    .then(data => {
                        checkEaApprovalSync();
                    })
                    .catch(err => {
                        console.error(err);
                        btn.disabled = false;
                        btn.innerHTML = 'Request EA Access';
                    });
            }
        </script>
    </article>

    <section class="robot-coming-soon" aria-labelledby="robotComingSoonTitle">
        <div class="robot-coming-content">
            <span class="robot-coming-badge"><i class="fas fa-code" aria-hidden="true"></i> In Development</span>
            <div class="robot-coming-icon"><i class="fas fa-robot" aria-hidden="true"></i></div>
            <p class="robot-coming-eyebrow">The Next Generation</p>
            <h2 id="robotComingSoonTitle">Trading Robot Coming Soon</h2>
            <p class="robot-coming-description">
                A new trading robot is being built for disciplined execution, intelligent risk control, and a cleaner professional workflow.
            </p>
            <div class="robot-coming-features">
                <span><i class="fas fa-bolt" aria-hidden="true"></i> Precise Execution</span>
                <span><i class="fas fa-shield-alt" aria-hidden="true"></i> Smart Risk Control</span>
                <span><i class="fas fa-chart-line" aria-hidden="true"></i> Built for Traders</span>
            </div>
            <button type="button" class="robot-coming-button" disabled>
                <i class="fas fa-clock" aria-hidden="true"></i> Coming Soon
            </button>
        </div>
    </section>
</main>

<style>
    .ea-tools-section {
        text-align: center;
        padding: 4rem 5%;
    }

    .ea-tools-section>h2 {
        margin-bottom: 2rem;
        color: var(--primary-color);
    }

    .ea-manager-title {
        margin: 0 0 1rem;
        color: #eefaff;
        background: var(--brand-title-gradient, linear-gradient(135deg, #fff 30%, #00d4ff));
        background-clip: text;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .download-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 48px;
        padding: .9rem 1.6rem;
        border: 0;
        border-radius: 12px;
        background: linear-gradient(135deg, #00d4ff, #00b894);
        color: #04101d;
        font-size: 1rem;
        font-weight: 800;
        text-decoration: none;
        transition: transform .25s ease, box-shadow .25s ease;
    }

    .download-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 24px rgba(0, 212, 255, .22);
    }

    .robot-coming-soon {
        position: relative;
        width: min(880px, 100%);
        margin: 4rem auto 1rem;
        padding: 1px;
        overflow: hidden;
        border-radius: 24px;
        background: linear-gradient(135deg, rgba(0, 212, 255, .65), rgba(0, 184, 148, .24), rgba(255, 255, 255, .08));
    }

    .robot-coming-content {
        position: relative;
        padding: clamp(2rem, 5vw, 3.4rem);
        border-radius: 23px;
        background: linear-gradient(145deg, rgba(17, 26, 54, .98), rgba(6, 12, 31, .98));
    }

    .robot-coming-badge {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        padding: .5rem .85rem;
        border: 1px solid rgba(0, 212, 255, .3);
        border-radius: 999px;
        background: rgba(0, 212, 255, .08);
        color: #70e9ff;
        font-size: .75rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .robot-coming-icon {
        display: grid;
        place-items: center;
        width: 76px;
        height: 76px;
        margin: 1.5rem auto;
        border: 1px solid rgba(0, 212, 255, .28);
        border-radius: 20px;
        background: linear-gradient(145deg, rgba(0, 212, 255, .15), rgba(0, 184, 148, .08));
        color: #00d4ff;
        font-size: 2rem;
    }

    .robot-coming-eyebrow {
        margin-bottom: .7rem;
        color: #6f8aa5;
        font-size: .72rem;
        font-weight: 800;
        letter-spacing: .16em;
        text-transform: uppercase;
    }

    .robot-coming-description {
        max-width: 650px;
        margin: 1rem auto 1.5rem;
        color: rgba(225, 225, 227, .88);
        line-height: 1.65;
    }

    .robot-coming-features {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: .75rem;
        margin-bottom: 1.6rem;
    }

    .robot-coming-features span {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        padding: .65rem .85rem;
        border: 1px solid rgba(0, 212, 255, .18);
        border-radius: 10px;
        background: rgba(0, 212, 255, .05);
        color: #d8f7ff;
        font-size: .85rem;
        font-weight: 700;
    }

    .robot-coming-features i {
        color: #00d4ff;
    }

    .robot-coming-button {
        min-height: 44px;
        padding: .75rem 1.2rem;
        border: 1px solid rgba(255, 255, 255, .12);
        border-radius: 10px;
        background: rgba(255, 255, 255, .05);
        color: #7d8da6;
        font-weight: 800;
        cursor: not-allowed;
    }

    @media (max-width: 640px) {
        .ea-tools-section {
            padding: 3rem 1rem;
        }

        .download-btn {
            width: 100%;
        }

        .robot-coming-soon {
            margin-top: 2.75rem;
        }

        .robot-coming-features {
            display: grid;
        }
    }
</style>

<script>
    (function() {
        var wrap = document.getElementById('eaTiltVisual');
        var img = document.getElementById('eaHeroImg');
        if (!wrap || !img) return;
        var raf;
        wrap.addEventListener('mousemove', function(e) {
            cancelAnimationFrame(raf);
            raf = requestAnimationFrame(function() {
                var r = wrap.getBoundingClientRect();
                var x = (e.clientX - r.left) / r.width - 0.5;
                var y = (e.clientY - r.top) / r.height - 0.5;
                img.style.transform = 'perspective(700px) rotateX(' + (y * -10) + 'deg) rotateY(' + (x * 10) + 'deg) scale(1.03)';
            });
        });
        wrap.addEventListener('mouseleave', function() {
            img.style.transform = '';
        });
    })();
</script>

<?php include 'footer.php'; ?>
