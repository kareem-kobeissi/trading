<?php
require 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Prevent browser from caching this page
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');

$status = 'none';

// Check EA order status
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    $stmt = $conn->prepare("
        SELECT oi.item_status AS status
        FROM order_items oi
        JOIN orders o ON o.id = oi.order_id
        WHERE o.user_id = ?
        AND oi.course_id = 2
        ORDER BY oi.id DESC
        LIMIT 1
    ");

    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $status = $row['status']; // pending / unlocked
    }

    // Check Robot order status
    $stmtRobot = $conn->prepare("
        SELECT oi.item_status AS status
        FROM order_items oi
        JOIN orders o ON o.id = oi.order_id
        WHERE o.user_id = ?
        AND oi.course_id = 3
        ORDER BY oi.id DESC
        LIMIT 1
    ");

    $stmtRobot->bind_param("i", $userId);
    $stmtRobot->execute();
    $resultRobot = $stmtRobot->get_result();

    if ($rowRobot = $resultRobot->fetch_assoc()) {
        $robotStatus = $rowRobot['status']; // pending / unlocked
    } else {
        $robotStatus = 'none';
    }

    // Check Robot IB order status
    $stmtRobotIB = $conn->prepare("
        SELECT oi.item_status AS status
        FROM order_items oi
        JOIN orders o ON o.id = oi.order_id
        WHERE o.user_id = ?
        AND oi.course_id = 4
        ORDER BY oi.id DESC
        LIMIT 1
    ");

    $stmtRobotIB->bind_param("i", $userId);
    $stmtRobotIB->execute();
    $resultRobotIB = $stmtRobotIB->get_result();

    if ($rowRobotIB = $resultRobotIB->fetch_assoc()) {
        $robotIBStatus = $rowRobotIB['status']; // pending / unlocked
    } else {
        $robotIBStatus = 'none';
    }
} else {
    $robotStatus = 'none';
    $robotIBStatus = 'none';
}
?>

<?php include 'header.php'; ?>

<section class="section" style="text-align:center; padding:4rem 5%;">

    <h1 style="
        font-size:3rem;
        background: linear-gradient(135deg, var(--primary-color), #00b894);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom:1rem;
    ">
        Expert Advisors
    </h1>

    <p style="color: var(--text-muted); margin-bottom:2rem; font-size: 1.2rem;">
        Professional Trading Tools
    </p>

    <h2 style="color: var(--primary-color); margin-bottom: 2rem;">Risk Calculator</h2>

    <?php if (!isset($_SESSION['user_id'])): ?>

        <!-- 🔐 NOT LOGGED IN — Show buy button, redirect to login on click -->
        <div style="max-width:600px; margin:0 auto; padding:2rem; border-radius:20px; background: linear-gradient(145deg, rgba(26,31,58,0.8), rgba(10,14,39,0.9)); border:1px solid rgba(0,212,255,0.2);">
            <h4 style="color: #00b894; margin-top: 0; margin-bottom: 1rem;">TTR Trading Manager</h4>
            <p style="text-align: left; margin-bottom: 1.5rem; color: var(--text-light); line-height: 1.6;">
                Stop risking too much on a single trade. Our Risk Calculator is designed to help you manage position size with precision, control losses, and stay consistent. In just seconds, you can know exactly how much to risk based on your account, entry, and stop loss—so every trade is planned, calculated, and professional.
            </p>
            <h3 style="font-size: 2rem; margin-bottom: 1.5rem; color: white;">$100</h3>

            <a href="login.php?redirect=ea.php" class="download-btn">
                Buy Now
            </a>
        </div>

    <?php elseif ($status === 'unlocked'): ?>

        <!-- ✅ ACCESS GRANTED -->
        <div style="max-width:600px; margin:0 auto; padding:2rem; border-radius:20px; background: linear-gradient(145deg, rgba(26,31,58,0.8), rgba(10,14,39,0.9)); border:1px solid rgba(0,212,255,0.2);">
            <h4 style="color: #00b894; margin-top: 0; margin-bottom: 1rem;">TTR Trading Manager</h4>
            
            <h3 style="color:#00ff88; margin-bottom: 1.5rem;">✔ Access Granted</h3>

         
            <a href="download_ea.php" class="download-btn">
                Download Risk Calculator
            </a>
        </div>

    <?php elseif ($status === 'pending'): ?>

        <!-- ⏳ PENDING -->
        <div style="max-width:600px; margin:0 auto; padding:2rem; border-radius:20px; background: linear-gradient(145deg, rgba(26,31,58,0.8), rgba(10,14,39,0.9)); border:1px solid #ffc107;">
            <h4 style="color: #00b894; margin-top: 0; margin-bottom: 1rem;">TTR Trading Manager</h4>

            <h3 style="color:#ffc107; margin-bottom: 1rem;">⏳ Pending Approval</h3>
            <p style="color: var(--text-light);">Manager will approve your order soon.</p>
        </div>

    <?php else: ?>

        <!-- 🛒 NOT PURCHASED -->
        <div style="max-width:600px; margin:0 auto; padding:2rem; border-radius:20px; background: linear-gradient(145deg, rgba(26,31,58,0.8), rgba(10,14,39,0.9)); border:1px solid rgba(0,212,255,0.2);">
            <h4 style="color: #00b894; margin-top: 0; margin-bottom: 1rem;">TTR Trading Manager</h4>
            <p style="text-align: left; margin-bottom: 1.5rem; color: var(--text-light); line-height: 1.6;">
                Stop risking too much on a single trade. Our Risk Calculator is designed to help you manage position size with precision, control losses, and stay consistent. In just seconds, you can know exactly how much to risk based on your account, entry, and stop loss—so every trade is planned, calculated, and professional.
            </p>
            <h3 style="font-size: 2rem; margin-bottom: 1.5rem; color: white;">$100</h3>

            <button onclick="addEAToCart()" class="download-btn">
                Buy Now
            </button>
        </div>

    <?php endif; ?>

    <h1 style="
        font-size:3rem;
        background: linear-gradient(135deg, var(--primary-color), #00b894);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-top:4rem;
        margin-bottom:1rem;
    ">
        Robot
    </h1>

    <div style="margin-bottom: 4rem;">
        <?php if (!isset($_SESSION['user_id'])): ?>

            <!-- 🔐 NOT LOGGED IN — Show buy button, redirect to login on click -->
            <div style="max-width:600px; margin:0 auto; padding:2rem; border-radius:20px; background: linear-gradient(145deg, rgba(26,31,58,0.8), rgba(10,14,39,0.9)); border:1px solid rgba(0,212,255,0.2);">
                <h2 style="color: var(--primary-color); margin-bottom: 1rem;">S&R Precision EA</h2>
                <p style="text-align: left; margin-bottom: 1.5rem; color: var(--text-light); line-height: 1.6;">
                    Stop guessing breakouts and missing key market moves.<br><br>
                    Our Instant Breakout EA detects real-time support and resistance reactions instantly.<br>
                    It enters trades automatically the moment price breaks or touches key levels.<br>
                    With smart risk management and precise SL/TP, every trade is structured and controlled.<br>
                    Trade faster, cleaner, and more professionally—without emotional decisions.
                </p>
                <h3 style="font-size: 2rem; margin-bottom: 1.5rem; color: white;">$300</h3>
                <a href="login.php?redirect=ea.php" class="download-btn">Buy Now</a>
            </div>

        <?php elseif ($robotStatus === 'unlocked'): ?>

            <!-- ✅ ACCESS GRANTED -->
            <div style="max-width:600px; margin:0 auto; padding:2rem; border-radius:20px; background: linear-gradient(145deg, rgba(26,31,58,0.8), rgba(10,14,39,0.9)); border:1px solid rgba(0,212,255,0.2);">
                <h2 style="color: var(--primary-color); margin-bottom: 1rem;">S&R Precision EA</h2>
                <h3 style="color:#00ff88; margin-bottom: 1.5rem;">✔ Access Granted</h3>
                <a href="download_robot.php" class="download-btn">Download S&R Precision EA</a>
            </div>

        <?php elseif ($robotStatus === 'pending'): ?>

            <!-- ⏳ PENDING -->
            <div style="max-width:600px; margin:0 auto; padding:2rem; border-radius:20px; background: linear-gradient(145deg, rgba(26,31,58,0.8), rgba(10,14,39,0.9)); border:1px solid #ffc107;">
                <h2 style="color: var(--primary-color); margin-bottom: 1rem;">S&R Precision EA</h2>
                <h3 style="color:#ffc107; margin-bottom: 1rem;">⏳ Pending Approval</h3>
                <p style="color: var(--text-light);">Manager will approve your order soon.</p>
            </div>

        <?php else: ?>

            <!-- 🛒 NOT PURCHASED -->
            <div style="max-width:600px; margin:0 auto; padding:2rem; border-radius:20px; background: linear-gradient(145deg, rgba(26,31,58,0.8), rgba(10,14,39,0.9)); border:1px solid rgba(0,212,255,0.2);">
                <h2 style="color: var(--primary-color); margin-bottom: 1rem;">S&R Precision EA</h2>
                <p style="text-align: left; margin-bottom: 1.5rem; color: var(--text-light); line-height: 1.6;">
                    Stop guessing breakouts and missing key market moves.<br><br>
                    Our S&R Precision EA detects real-time support and resistance reactions instantly.<br>
                    It enters trades automatically the moment price breaks or touches key levels.<br>
                    With smart risk management and precise SL/TP, every trade is structured and controlled.<br>
                    Trade faster, cleaner, and more professionally—without emotional decisions.
                </p>
                <h3 style="font-size: 2rem; margin-bottom: 1.5rem; color: white;">$300</h3>
                <button onclick="addSRToCart()" class="download-btn">Buy Now</button>
            </div>

        <?php endif; ?>
    </div>

    <div style="margin-bottom: 4rem;">
        <?php if (!isset($_SESSION['user_id'])): ?>

            <!-- 🔐 NOT LOGGED IN -->
            <div style="max-width:600px; margin:0 auto; padding:2rem; border-radius:20px; background: linear-gradient(145deg, rgba(26,31,58,0.8), rgba(10,14,39,0.9)); border:1px solid rgba(0,212,255,0.2);">
                <h2 style="color: var(--primary-color); margin-bottom: 1rem;">Instant Breakout EA</h2>
                <p style="text-align: left; margin-bottom: 1.5rem; color: var(--text-light); line-height: 1.6;">
                    Instant Breakout EA is an advanced trading risk calculator designed for precise trade execution.<br><br>
                    It calculates lot size, stop loss, and take profit based on % or fixed dollar risk.<br>
                    Traders can visually set and adjust trade levels directly on the chart.<br>
                    It ensures strict risk control and consistent position sizing in real time.<br>
                    Built for traders who want accuracy, discipline, and professional execution.
                </p>
                <h3 style="font-size: 2rem; margin-bottom: 1.5rem; color: white;">$300</h3>
                <a href="login.php?redirect=ea.php" class="download-btn">Buy Now</a>
            </div>

        <?php elseif ($robotIBStatus === 'unlocked'): ?>

            <!-- ✅ ACCESS GRANTED -->
            <div style="max-width:600px; margin:0 auto; padding:2rem; border-radius:20px; background: linear-gradient(145deg, rgba(26,31,58,0.8), rgba(10,14,39,0.9)); border:1px solid rgba(0,212,255,0.2);">
                <h2 style="color: var(--primary-color); margin-bottom: 1rem;">Instant Breakout EA</h2>
                <h3 style="color:#00ff88; margin-bottom: 1.5rem;">✔ Access Granted</h3>
                <a href="download_instant_breakout_ea.php" class="download-btn">Download Instant Breakout EA</a>
            </div>

        <?php elseif ($robotIBStatus === 'pending'): ?>

            <!-- ⏳ PENDING -->
            <div style="max-width:600px; margin:0 auto; padding:2rem; border-radius:20px; background: linear-gradient(145deg, rgba(26,31,58,0.8), rgba(10,14,39,0.9)); border:1px solid #ffc107;">
                <h2 style="color: var(--primary-color); margin-bottom: 1rem;">Instant Breakout EA</h2>
                <h3 style="color:#ffc107; margin-bottom: 1rem;">⏳ Pending Approval</h3>
                <p style="color: var(--text-light);">Manager will approve your order soon.</p>
            </div>

        <?php else: ?>

            <!-- 🛒 NOT PURCHASED -->
            <div style="max-width:600px; margin:0 auto; padding:2rem; border-radius:20px; background: linear-gradient(145deg, rgba(26,31,58,0.8), rgba(10,14,39,0.9)); border:1px solid rgba(0,212,255,0.2);">
                <h2 style="color: var(--primary-color); margin-bottom: 1rem;">Instant Breakout EA</h2>
                <p style="text-align: left; margin-bottom: 1.5rem; color: var(--text-light); line-height: 1.6;">
                    Instant Breakout EA is an advanced trading risk calculator designed for precise trade execution.<br><br>
                    It calculates lot size, stop loss, and take profit based on % or fixed dollar risk.<br>
                    Traders can visually set and adjust trade levels directly on the chart.<br>
                    It ensures strict risk control and consistent position sizing in real time.<br>
                    Built for traders who want accuracy, discipline, and professional execution.
                </p>
                <h3 style="font-size: 2rem; margin-bottom: 1.5rem; color: white;">$300</h3>
                <button onclick="addIBToCart()" class="download-btn">Buy Now</button>
            </div>

        <?php endif; ?>
    </div>

</section>

<style>
    .download-btn {
        display: inline-block;
        padding: 1.2rem 2.5rem;
        font-size: 1.1rem;
        font-weight: 700;
        color: #000;
        background: linear-gradient(135deg, #00d4ff, #00b894);
        border-radius: 12px;
        text-decoration: none;
        transition: 0.3s;
        border: none;
        cursor: pointer;
    }

    .download-btn:hover {
        transform: scale(1.05);
        box-shadow: 0 10px 30px rgba(0, 212, 255, 0.4);
    }
</style>

<script>
    function addEAToCart() {

        const userEmail = sessionStorage.getItem('currentEmail');

        if (!userEmail) {
            alert('❌ Please login first!');
            window.location.href = 'login.php';
            return;
        }

        let cart = JSON.parse(localStorage.getItem('cart') || '[]');

        const exists = cart.some(item => item.type === 'ea');
        if (exists) {
            alert('⚠️ EA already in cart!');
            return;
        }

        cart.push({
            id: 'ea_1',
            title: 'TTR Risk Calculator',
            price: 100,
            quantity: 1,
            type: 'ea'
        });

        localStorage.setItem('cart', JSON.stringify(cart));

        alert('✅ Expert Advisor added to cart!');
        window.location.href = 'cart.php';
    }

    function addSRToCart() {
        const userEmail = sessionStorage.getItem('currentEmail');
        if (!userEmail) {
            alert('❌ Please login first!');
            window.location.href = 'login.php';
            return;
        }

        let cart = JSON.parse(localStorage.getItem('cart') || '[]');
        const exists = cart.some(item => item.type === 'robot_sr');
        if (exists) {
            alert('⚠️ S&R Precision EA already in cart!');
            return;
        }

        cart.push({
            id: 'robot_sr_1',
            title: 'S&R Precision EA',
            price: 300,
            quantity: 1,
            type: 'robot_sr'
        });

        localStorage.setItem('cart', JSON.stringify(cart));
        alert('✅ S&R Precision EA added to cart!');
        window.location.href = 'cart.php';
    }

    function addIBToCart() {
        const userEmail = sessionStorage.getItem('currentEmail');
        if (!userEmail) {
            alert('❌ Please login first!');
            window.location.href = 'login.php';
            return;
        }

        let cart = JSON.parse(localStorage.getItem('cart') || '[]');
        const exists = cart.some(item => item.type === 'robot_ib');
        if (exists) {
            alert('⚠️ Instant Breakout EA already in cart!');
            return;
        }

        cart.push({
            id: 'robot_ib_1',
            title: 'Instant Breakout EA',
            price: 300,
            quantity: 1,
            type: 'robot_ib'
        });

        localStorage.setItem('cart', JSON.stringify(cart));
        alert('✅ Instant Breakout EA added to cart!');
        window.location.href = 'cart.php';
    }
</script>

<?php include 'footer.php'; ?>
