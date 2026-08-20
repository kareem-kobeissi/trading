<?php
// courses.php
include 'header.php';
require_once 'config.php';
require_once 'commerce_config.php';

// Get course information
$course_price = coursePriceUsd();
$course_id = 'basics'; // Default course section

// Check if user is logged in and has unlocked access to course
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$user_name = isset($_SESSION['username']) ? $_SESSION['username'] : null;

$has_access = false;
$course_access_status = 'none';
if ($user_id) {
    $accStmt = $conn->prepare("SELECT oi.item_status, o.status FROM orders o JOIN order_items oi ON o.id = oi.order_id WHERE o.user_id = ? AND oi.product_type = 'course' ORDER BY FIELD(oi.item_status, 'unlocked', 'pending', 'cancelled') LIMIT 1");
    if ($accStmt) {
        $accStmt->bind_param('i', $user_id);
        $accStmt->execute();
        $accRes = $accStmt->get_result();
        if ($accRes && $accRes->num_rows > 0) {
            $accRow = $accRes->fetch_assoc();
            $course_access_status = $accRow['item_status'] ?: $accRow['status'];
            if ($course_access_status === 'unlocked') {
                $has_access = true;
            }
        }
        $accStmt->close();
    }
}

// Access control: Check localStorage orders from JavaScript
// This is handled on client-side since we're using localStorage/JSON
// Server-side will always return false, JavaScript will check actual status
// For now, we'll rely on client-side checking for the locked overlay

// Video data organized by section
$videoSections = [
    'basics' => [
        'title' => 'Basics',
        'videos' => [
            'Introduction.mp4',
            'Chart Types.mp4',
 'Engulfing Patterns.mp4',
            'Market Structure.mp4',



            'RSI & SMA.mp4',

            
        ]
    ],
    'advanced' => [
        'title' => 'Advanced',
        'videos' => [
            'S&R and Trendline.mp4',
             'S&R Breaks.mp4',
             'RBS & SBR and Supply & Demand .mp4',
             'Fake - Breakout.mp4',
              '(BOS) & (CHOCH).mp4',
             'Patterns.mp4',
           
            
            
            'Fibonacci Retracement & Extension.mp4',
            'Order Blocks.mp4',
            'Fair Value Gap.mp4',
            'Liquidity Sweeps.mp4',
            
            
            
          
            'The Holy Grail.mp4',
            
          
            
            
            

    
            
        ]
    ],
    'risk' => [
        'title' => 'Risk Management',
        'videos' => [
            'Risk Management.mp4'
        ]
    ]
];

$foundationLessonCount = count($videoSections['basics']['videos']);
$advancedLessonCount = count($videoSections['advanced']['videos']);
$riskLessonCount = count($videoSections['risk']['videos']);
$totalLessonCount = $foundationLessonCount + $advancedLessonCount + $riskLessonCount;
?>

<!-- ===== COURSES HEADER SECTION ===== -->
<section class="about-header-section courses-header">
    <div class="premium-page-hero">
        <div class="about-header-content premium-hero-copy">
            <span class="premium-hero-kicker">Learn • Practice • Master</span>
            <h1 class="about-main-title">Trading Mastery Course</h1>
            <p class="about-subtitle">From beginner foundations to advanced market mastery</p>
        </div>
        <div class="premium-hero-visual courses-visual-wrapper tilt-visual" aria-hidden="true" id="coursesTiltVisual">
            <div class="tilt-glow"></div>
            <div class="action-emitter">
                <img src="course-journey.webp" alt="" class="premium-hero-image courses-hero-image" decoding="async" fetchpriority="high" id="coursesHeroImg">

            </div>
            <!-- Floating learning chips -->
            <div class="belief-chip chip-a courses-chip-a" style="top:8%;right:-5%;--cdur:6s;--cdelay:0s"><span class="icon-3d"><i class="fas fa-coins"></i></span> Forex</div>
            <div class="belief-chip chip-b courses-chip-b" style="top:28%;left:-8%;--cdur:6.5s;--cdelay:1.5s"><span class="icon-3d"><i class="fas fa-chart-bar"></i></span> Stocks</div>
            <div class="belief-chip chip-c courses-chip-c" style="bottom:26%;right:-6%;--cdur:5.5s;--cdelay:3s"><span class="icon-3d"><i class="fas fa-shield-halved"></i></span> Risk Ratio</div>
            <div class="belief-chip chip-d courses-chip-d" style="bottom:10%;left:-5%;--cdur:7s;--cdelay:0.8s"><span class="icon-3d"><i class="fas fa-chess-knight"></i></span> Strategies</div>
            <!-- Live badge -->
            <div class="img-badge" style="bottom:-14px;left:50%;transform:translateX(-50%);--bpdelay:.4s">
                <span class="badge-dot"></span> Lifetime Access
            </div>
        </div>
    </div>
</section>

<!-- ===== VIDEO COURSES SECTION ===== -->
<section class="section">
    <div class="video-courses-container">

        <!-- Course Price Card -->
        <div class="course-info-card">
            <div class="course-header">
                <h2>Complete Trading Mastery Program</h2>
                <div class="course-price" id="courseBadgeStatus">
                    <?php
                    if ($course_access_status === 'unlocked') {
                        echo '<span style="color: #00ff88;"><i class="fas fa-check-circle"></i> APPROVED</span>';
                    } elseif ($course_access_status === 'pending') {
                        echo '<span style="color: #f59d00;"><i class="fas fa-clock"></i> PENDING</span>';
                    } else {
                        echo '<span class="course-offer"><strong>$' . number_format($course_price, 0) . '</strong><span class="course-offer-or">or</span><span class="course-offer-free">FREE with Broker Registration</span></span>';
                    }
                    ?>
                </div>
            </div>

            <div class="course-details">
                <p><strong><?php echo $totalLessonCount; ?> structured lessons</strong> covering market foundations, advanced execution, and professional risk control</p>
                <ul class="course-modules">
                    <li>Foundation Module — <?php echo $foundationLessonCount; ?> Lessons</li>
                    <li>Advanced Strategy Module — <?php echo $advancedLessonCount; ?> Lessons</li>
                    <li>Risk Management Module — <?php echo $riskLessonCount; ?> Masterclass</li>
                </ul>
            </div>

            <div id="courseActionArea">
                <?php if (!$user_id): ?>
                    <a href="login.php?redirect=courses.php" class="btn-buy-course">Login to Enroll</a>
                <?php elseif ($course_access_status === 'unlocked'): ?>
                    <div class="access-granted" style="color:#00ff88;font-weight:bold;padding:.8rem 1.2rem;background:rgba(0,255,136,0.1);border:1px solid rgba(0,255,136,0.3);border-radius:10px;display:inline-block;">✓ Access Approved — Course Unlocked</div>
                <?php elseif ($course_access_status === 'pending'): ?>
                    <button type="button" class="btn-buy-course" disabled style="opacity:.7;cursor:not-allowed;background:#333;color:#f59d00;border:1px solid #f59d00;"><i class="fas fa-clock" aria-hidden="true"></i> Pending Admin Approval</button>
                <?php else: ?>
                    <button type="button" class="btn-buy-course" onclick="requestCourseAccess()">Enroll — $<?php echo number_format($course_price, 0); ?> or Free with Broker</button>
                <?php endif; ?>
            </div>
        </div>

        <!-- Section Selector -->
        <div class="section-selector">
            <label class="radio-label">
                <input type="radio" name="course-section" value="basics" checked>
                <span class="radio-button"><span class="module-icon">01</span><span><strong>Basics</strong><small>Build your foundation</small></span></span>
            </label>
            <label class="radio-label">
                <input type="radio" name="course-section" value="advanced">
                <span class="radio-button"><span class="module-icon">02</span><span><strong>Advanced</strong><small>Master execution</small></span></span>
            </label>
            <label class="radio-label">
                <input type="radio" name="course-section" value="risk">
                <span class="radio-button"><span class="module-icon">03</span><span><strong>Risk Management</strong><small>Protect your capital</small></span></span>
            </label>

            <!-- Download PDF Button -->
            <div class="pdf-download-wrapper">
                <button class="pdf-download-btn" id="pdfDownloadBtn" onclick="togglePdfMenu()">
                    <span class="workbook-icon"><i class="fas fa-file-pdf"></i></span>
                    <span><strong>Course Workbook</strong><small>PDF learning resources</small></span>
                    <i class="fas fa-chevron-down workbook-chevron"></i>
                </button>
                <div id="pdfLockOverlay" class="pdf-lock-overlay">
                    <div class="pdf-lock-content">
                        <p class="pdf-lock-text">Login to access course resources</p>
                    </div>
                </div>
                <div id="pdfMenu" class="pdf-menu" style="display: none;">
                    <a href="javascript:void(0)" onclick="handlePdfDownload('pdf/The%20Trading%20Routine%20-%20Presentation.pdf'); return false;" class="pdf-item">📄 The Trading Routine - Presentation</a>
                    <a href="javascript:void(0)" onclick="handlePdfDownload('pdf/The%20Trading%20Routine%20Course%20-%20Part%201%20in%20ENGLISH.pdf'); return false;" class="pdf-item">📖 Course - Part 1</a>
                    <a href="javascript:void(0)" onclick="handlePdfDownload('pdf/The%20trading%20routine%20course%20-%20part%202%20in%20ENGLISH.pdf'); return false;" class="pdf-item">📖 Course - Part 2</a>
                    <a href="javascript:void(0)" onclick="handlePdfDownload('pdf/the%20trading%20routine%20course%20-%20part%202.1%20ENGLISH.pdf'); return false;" class="pdf-item">📖 Course - Part 2.1</a>
                    <a href="javascript:void(0)" onclick="handlePdfDownload('pdf/The%20trading%20routine%20course%20-%20part%203%20in%20ENGLISH.pdf'); return false;" class="pdf-item">📖 Course - Part 3</a>
                </div>
            </div>
        </div>

        <!-- Video Player and Playlist Container -->
        <div class="video-main-container" id="videoMainContainer">
            <!-- Video Player Section -->
            <div class="video-player-section">
                <div id="videoPlayer" class="video-player">
                    <video id="mainVideo" controls controlsList="nodownload" preload="none">
                        <p>Your browser does not support HTML5 video. Please use Chrome, Firefox, Safari, or Edge.</p>
                    </video>
                    <?php if (!$has_access): ?>
                        <div class="video-locked-overlay">
                            <div class="lock-content">
                                <div class="lock-icon"><i class="fas fa-lock"></i></div>
                                <div class="unlock-title">Enroll to Unlock</div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div id="videoTitle" class="video-title">chart types</div>
            </div>


            <!-- Video Playlist -->
            <div class="video-playlist-wrapper">
                <div class="playlist-heading">
                    <div>
                        <span class="playlist-eyebrow">COURSE CONTENT</span>
                        <h3 id="sectionTitle" class="playlist-title">Basics Videos</h3>
                    </div>
                    <span id="lessonCount" class="lesson-count"></span>
                </div>
                <div id="videoPlaylist" class="video-playlist">
                    <!-- Videos will be loaded by JavaScript -->
                </div>
            </div>
        </div>

        <!-- Exclusive Private Training Section -->
        <div class="private-training-section">
            <div class="private-training-box">
                <div class="private-training-title">Exclusive Private Training</div>
                <div class="private-training-subtitle">Need a more personalized learning experience?</div>
                <div class="private-training-description">
                    In addition to our online courses, private one-to-one trading lessons are available for students who prefer direct guidance.
                    <br><br>
                    Private sessions can be arranged based on your location and learning needs, with personalized support designed to accelerate your understanding of the markets.
                    <br><br>
                    Available by appointment for registered members.
                </div>
                <a href="https://wa.me/96171493997?text=Hello%2C%20I%20would%20like%20to%20request%20a%20private%20trading%20session.%20Please%20send%20me%20the%20available%20dates%2C%20details%2C%20and%20price."
                    target="_blank" rel="noopener noreferrer" class="private-session-btn">Request Private Session</a>
            </div>
        </div>
    </div>
</section>

<style>
    /* Course Info Card */
    .course-info-card {
        background: linear-gradient(135deg, rgba(0, 212, 255, 0.1), rgba(0, 184, 148, 0.05));
        border: 2px solid var(--primary-color);
        padding: 2rem;
        border-radius: 15px;
        margin-bottom: 3rem;
        box-shadow: 0 8px 32px rgba(0, 212, 255, 0.15);
    }

    .course-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1.5rem;
        border-bottom: 2px solid rgba(0, 212, 255, 0.2);
    }

    .course-header h2 {
        font-size: 1.8rem;
        color: var(--text-light);
        margin: 0;
    }

    .course-price {
        font-size: 1rem;
        font-weight: bold;
        color: #00ff88;
        text-shadow: 0 0 10px rgba(0, 255, 136, 0.5);
        max-width: min(100%, 360px);
        white-space: normal;
    }

    .course-offer {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        flex-wrap: wrap;
        gap: 0.35rem 0.55rem;
        line-height: 1.25;
        text-align: right;
    }

    .course-offer strong {
        color: #00ff88;
        font-size: clamp(1.55rem, 3vw, 2.15rem);
    }

    .course-offer-or {
        color: var(--text-muted);
        font-size: 0.82rem;
        text-transform: lowercase;
    }

    .course-offer-free {
        flex-basis: 100%;
        color: #55dcf5;
        font-size: clamp(0.82rem, 1.7vw, 1rem);
        letter-spacing: 0.02em;
    }

    .course-details p {
        color: var(--text-muted);
        font-size: 1.1rem;
        margin-bottom: 1rem;
    }

    .course-modules {
        list-style: none;
        padding: 0;
        margin: 1.5rem 0;
    }

    .course-modules li {
        color: var(--text-muted);
        padding: 0.8rem 0;
        padding-left: 1.5rem;
        font-size: 1rem;
    }

    .btn-buy-course {
        width: 100%;
        padding: 1rem;
        background: white;
        border: none;
        border-radius: 10px;
        color: var(--dark-bg);
        font-weight: bold;
        font-size: 1.1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: block;
        text-align: center;
        margin-top: 1.5rem;
    }

    .btn-buy-course:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(0, 212, 255, 0.4);
    }

    .access-granted {
        padding: 1rem;
        background: rgba(0, 255, 136, 0.1);
        border: 2px solid #00ff88;
        border-radius: 10px;
        color: #00ff88;
        text-align: center;
        font-weight: bold;
        font-size: 1.1rem;
        margin-top: 1.5rem;
    }

    /* Video Locked Overlay */
    .video-locked-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(0, 0, 0, 0.9), rgba(0, 20, 40, 0.9));
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        z-index: 10;
        backdrop-filter: blur(8px);
        overflow: hidden;
        direction: ltr;
    }

    .lock-content {
        text-align: center;
        color: #fff;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: clamp(0.3rem, 1.5vw, 1.5rem);
        padding: clamp(0.5rem, 2vw, 2rem);
        background: linear-gradient(135deg, rgba(0, 0, 0, 0.4), rgba(0, 212, 255, 0.1));
        border-radius: clamp(8px, 2vw, 20px);
        border: 2px solid rgba(0, 212, 255, 0.3);
        backdrop-filter: blur(10px);
        max-width: 90%;
        width: 90%;
        box-sizing: border-box;
        margin: 0 auto;
        align-self: center;
    }

    .lock-icon {
        font-size: clamp(1.8rem, 7vw, 5rem);
        margin-bottom: 0.5rem;
        display: block;
        animation: pulse 2s infinite;
        color: #888888;
        text-shadow: 0 0 10px rgba(136, 136, 136, 0.5);
        filter: drop-shadow(0 0 5px rgba(136, 136, 136, 0.4));
    }

    .lock-content h3 {
        font-size: 1.8rem;
        margin-bottom: 0.5rem;
        color: var(--primary-color);
    }

    .unlock-title {
        font-size: clamp(0.9rem, 4vw, 2.8rem);
        font-weight: 900;
        color: #ffffff;
        margin: 0;
        padding: clamp(0.4rem, 1.2vw, 1.5rem) clamp(0.6rem, 1.8vw, 2rem);
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.8), 0 0 30px rgba(0, 212, 255, 0.6);
        letter-spacing: 0.5px;
        line-height: 1.3;
        background: rgba(0, 212, 255, 0.1);
        border-radius: clamp(6px, 1.5vw, 12px);
        border: 1px solid rgba(0, 212, 255, 0.3);
        animation: fadeInScale 0.6s ease-out;
        word-wrap: break-word;
        word-break: break-word;
        white-space: normal;
        width: 100%;
        box-sizing: border-box;
    }

    @keyframes fadeInScale {
        from {
            opacity: 0;
            transform: scale(0.8);
        }

        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .unlock-subtitle {
        font-size: 0.75rem;
        color: #00d4ff;
        line-height: 1.4;
        margin-bottom: 1.5rem;
        margin-right: 1rem;
        max-width: 300px;
        font-weight: 500;
        text-align: right;
    }

    .unlock-price {
        display: inline-block;
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--dark-bg);
        background: linear-gradient(135deg, #00ff88, #00d4ff);
        padding: 0.8rem 2rem;
        border-radius: 25px;
        margin-top: 1rem;
        box-shadow: 0 0 30px rgba(0, 255, 136, 0.5), 0 0 50px rgba(0, 212, 255, 0.3);
        letter-spacing: 0.3px;
    }

    .lock-content p {
        font-size: 1rem;
        color: #ccc;
        margin-bottom: 1.5rem;
    }

    .btn-unlock {
        padding: 1rem 2rem;
        background: white;
        border: none;
        border-radius: 10px;
        color: var(--dark-bg);
        font-weight: bold;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-unlock:hover {
        transform: scale(1.05);
        box-shadow: 0 10px 30px rgba(0, 212, 255, 0.5);
    }

    @keyframes pulse {

        0%,
        100% {
            transform: scale(1);
            filter: drop-shadow(0 0 15px rgba(0, 212, 255, 0.6));
        }

        50% {
            transform: scale(1.15);
            filter: drop-shadow(0 0 25px rgba(0, 212, 255, 0.8));
        }
    }

    .courses-header-icon {
        font-size: 4rem;
        margin-bottom: 1.5rem;
        animation: bounce 2s infinite;
    }

    .courses-header {
        position: relative;
        overflow: hidden;
    }

    .courses-header::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(0, 212, 255, 0.05) 0%, transparent 50%);
        animation: float 10s ease-in-out infinite;
        pointer-events: none;
    }

    .video-courses-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem;
        overflow-x: hidden;
    }

    /* Private Training Section */
    .private-training-section {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem;
    }

    .private-training-box {
        background: linear-gradient(135deg, rgba(0, 212, 255, 0.1), rgba(0, 184, 148, 0.05));
        border: 2px solid var(--primary-color);
        border-radius: 15px;
        padding: 2.5rem;
        box-shadow: 0 8px 32px rgba(0, 212, 255, 0.15);
    }

    .private-training-title {
        font-size: 2rem;
        font-weight: 800;
        background: linear-gradient(135deg, var(--primary-color), var(--accent-green), #00a8cc);
        background-size: 200% 200%;
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 1rem;
        animation: gradientShift 4s ease infinite;
    }

    .private-training-subtitle {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--text-light);
        margin-bottom: 1.5rem;
        font-style: italic;
    }

    .private-training-description {
        font-size: 1rem;
        color: var(--text-muted);
        line-height: 1.8;
        margin-bottom: 1.8rem;
    }

    .private-session-btn {
        display: inline-block;
        padding: 1rem 2.5rem;
        background: white;
        color: var(--dark-bg);
        border: none;
        border-radius: 10px;
        font-weight: 700;
        font-size: 1.05rem;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(255, 255, 255, 0.2);
    }

    .private-session-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(0, 212, 255, 0.4);
        background: #f0f0f0;
    }

    .private-session-btn:active {
        transform: translateY(0);
    }

    /* Main Container for Video and Playlist */
    .video-main-container {
        display: grid;
        grid-template-columns: 1fr 350px;
        gap: 2rem;
        margin-top: 2rem;
        max-width: 100%;
    }

    /* Video Player Section */
    .video-player-section {
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    /* Section Selector - Radio Buttons */
    .section-selector {
        display: flex;
        gap: 2rem;
        margin-bottom: 3rem;
        flex-wrap: wrap;
        justify-content: center;
    }

    .radio-label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
    }

    .radio-label input[type="radio"] {
        display: none;
    }

    .radio-button {
        padding: 0.75rem 1.5rem;
        border: 2px solid var(--primary-color);
        border-radius: 25px;
        background: transparent;
        color: var(--primary-color);
        font-weight: bold;
        transition: all 0.3s ease;
        cursor: pointer;
        font-size: 1rem;
    }

    .radio-label input[type="radio"]:checked+.radio-button {
        background: var(--primary-color);
        color: var(--bg-dark);
        box-shadow: 0 0 15px rgba(0, 212, 255, 0.5);
    }

    .radio-label input[type="radio"]:hover+.radio-button {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 212, 255, 0.3);
    }

    /* PDF Download Button */
    .pdf-download-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .pdf-download-btn {
        padding: 0.75rem 1.5rem;
        border: 2px solid var(--primary-color);
        border-radius: 25px;
        background: transparent;
        color: var(--primary-color);
        font-weight: bold;
        cursor: pointer;
        font-size: 1rem;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.7rem;
        white-space: nowrap;
    }

    .pdf-download-btn:hover {
        background: var(--primary-color);
        color: var(--bg-dark);
        box-shadow: 0 0 15px rgba(0, 212, 255, 0.5);
        transform: translateY(-2px);
    }

    .pdf-download-btn i {
        font-size: 1.2rem;
    }

    /* PDF Lock Overlay */
    .pdf-download-wrapper {
        position: relative;
    }

    .pdf-lock-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.7);
        border-radius: 25px;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 20;
        backdrop-filter: blur(2px);
    }

    .pdf-lock-content {
        text-align: center;
        color: #fff;
    }

    .pdf-lock-icon {
        font-size: 2.5rem;
        color: #888888;
        display: block;
        margin-bottom: 0.5rem;
        text-shadow: 0 0 10px rgba(136, 136, 136, 0.5);
    }

    .pdf-lock-text {
        font-size: 0.9rem;
        font-weight: 600;
        color: #ffffff;
        margin: 0;
        padding: 0;
        text-shadow: 0 0 5px rgba(0, 0, 0, 0.5);
    }

    /* PDF Menu Dropdown */
    .pdf-menu {
        position: absolute;
        top: 100%;
        left: 0;
        background: linear-gradient(135deg, rgba(26, 31, 58, 0.95), rgba(10, 14, 39, 0.95));
        border: 2px solid var(--primary-color);
        border-radius: 15px;
        min-width: 320px;
        box-shadow: 0 15px 50px rgba(0, 212, 255, 0.3), 0 0 30px rgba(0, 255, 136, 0.1);
        z-index: 1000;
        margin-top: 12px;
        animation: slideDown 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        backdrop-filter: blur(10px);
        overflow: hidden;
    }

    .pdf-item {
        display: flex;
        align-items: center;
        gap: 1.2rem;
        padding: 1.2rem 1.8rem;
        color: var(--text-light);
        text-decoration: none;
        border-bottom: 1px solid rgba(0, 212, 255, 0.15);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }

    .pdf-item::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 0;
        background: linear-gradient(90deg, transparent, rgba(0, 212, 255, 0.2), transparent);
        transition: width 0.4s ease;
    }

    .pdf-item:hover::before {
        width: 100%;
    }

    .pdf-item:last-child {
        border-bottom: none;
    }

    .pdf-item:hover {
        background: rgba(0, 212, 255, 0.08);
        color: var(--primary-color);
        padding-left: 2.2rem;
        box-shadow: inset 4px 0 0 var(--primary-color);
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-15px) scale(0.95);
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    /* Responsive PDF Section */
    @media (max-width: 768px) {
        .section-selector {
            gap: 0.8rem;
            justify-content: flex-start;
            flex-wrap: nowrap;
            overflow-x: auto;
            padding-bottom: 0.5rem;
        }

        .section-selector::-webkit-scrollbar {
            height: 4px;
        }

        .section-selector::-webkit-scrollbar-track {
            background: rgba(0, 212, 255, 0.1);
        }

        .section-selector::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 4px;
        }

        .pdf-download-wrapper {
            width: 100%;
            position: relative;
        }

        .pdf-download-btn {
            width: 100%;
            justify-content: center;
            padding: 0.8rem 1.2rem;
            font-size: 0.95rem;
        }

        .pdf-menu {
            position: fixed;
            top: auto;
            left: 50%;
            right: auto;
            bottom: 0;
            transform: translateX(-50%);
            min-width: calc(100% - 2rem);
            max-width: 100%;
            border-radius: 20px 20px 0 0;
            max-height: 75vh;
            overflow-y: auto;
            animation: slideUpMobile 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        @keyframes slideUpMobile {
            from {
                opacity: 0;
                transform: translateX(-50%) translateY(100%);
            }

            to {
                opacity: 1;
                transform: translateX(-50%) translateY(0);
            }
        }

        .pdf-item {
            padding: 1.3rem 1.6rem;
            gap: 1rem;
        }

        .pdf-item:hover {
            padding-left: 1.8rem;
        }
    }

    @media (max-width: 480px) {
        .section-selector {
            gap: 0.6rem;
            margin-bottom: 2rem;
        }

        .radio-button {
            padding: 0.55rem 1rem;
            font-size: 0.85rem;
            white-space: nowrap;
        }

        .pdf-download-btn {
            padding: 0.7rem 1rem;
            font-size: 0.9rem;
            gap: 0.5rem;
        }

        .pdf-download-btn i {
            font-size: 1rem;
        }

        .pdf-menu {
            min-width: calc(100% - 1rem);
            margin: 0 0.5rem;
        }

        .pdf-item {
            padding: 1rem 1.3rem;
            gap: 0.8rem;
            font-size: 0.95rem;
        }

        .video-main-container {
            gap: 0.8rem;
        }

        .video-playlist-wrapper {
            padding: 1rem;
            max-height: 250px;
        }

        .playlist-title {
            font-size: 0.95rem;
            margin-bottom: 0.6rem;
        }

        .video-item {
            padding: 0.5rem;
            font-size: 0.8rem;
        }
    }

    @media (max-width: 400px) {
        .section-selector {
            gap: 0.5rem;
        }

        .radio-button {
            padding: 0.5rem 0.9rem;
            font-size: 0.8rem;
        }

        .pdf-download-btn {
            padding: 0.6rem 0.9rem;
            font-size: 0.85rem;
        }

        .pdf-item {
            padding: 0.9rem 1rem;
            font-size: 0.9rem;
        }

        .video-player {
            aspect-ratio: 16 / 9;
        }

        .playlist-title {
            font-size: 0.9rem;
        }
    }

    /* Video Player */
    .video-player {
        position: relative;
        width: 100%;
        background: #000;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 8px 32px rgba(0, 212, 255, 0.2);
        aspect-ratio: 16 / 9;
    }

    .video-player video {
        width: 100%;
        height: 100%;
        display: block;
        object-fit: contain;
        background: #000;
    }

    .video-title {
        font-size: 1.3rem;
        font-weight: bold;
        color: white;
        margin-top: 1rem;
        padding: 0 1rem;
    }

    /* Video Playlist */
    .video-playlist-wrapper {
        background: var(--card-bg);
        border-radius: 10px;
        padding: 1.5rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        height: fit-content;
        max-height: 600px;
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .playlist-title {
        font-size: 1.2rem;
        color: var(--primary-color);
        margin-bottom: 1rem;
        margin-top: 0;
        border-bottom: 2px solid var(--primary-color);
        padding-bottom: 1rem;
    }

    .video-playlist {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        overflow-y: auto;
        overflow-x: hidden;
        flex: 1;
        min-width: 0;
    }

    .video-playlist::-webkit-scrollbar {
        width: 6px;
    }

    .video-playlist::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.1);
        border-radius: 10px;
    }

    .video-playlist::-webkit-scrollbar-thumb {
        background: var(--primary-color);
        border-radius: 10px;
    }

    .video-playlist::-webkit-scrollbar-thumb:hover {
        background: rgba(0, 212, 255, 0.8);
    }

    .video-item {
        display: flex;
        align-items: center;
        gap: 0.8rem;
        padding: 0.8rem;
        background: var(--bg-dark);
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s ease;
        border-left: 3px solid transparent;
        font-size: 0.95rem;
    }

    .video-item:hover {
        background: rgba(0, 212, 255, 0.1);
        border-left-color: var(--primary-color);
        transform: translateX(3px);
    }

    .video-item.active {
        background: rgba(0, 212, 255, 0.15);
        border-left-color: var(--primary-color);
        font-weight: 600;
    }

    .video-item-icon {
        font-size: 1.2rem;
        flex-shrink: 0;
    }

    .video-item-title {
        flex: 1;
        color: var(--text-main);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        font-size: 0.9rem;
    }

    @media (max-width: 1024px) {
        .video-main-container {
            grid-template-columns: 1fr;
        }

        .video-playlist-wrapper {
            max-height: 400px;
        }

        .course-info-card {
            padding: 1.5rem;
        }

        .course-header {
            flex-direction: column;
            gap: 1.5rem;
            align-items: flex-start;
        }

        .course-price {
            font-size: 2rem;
        }

        .course-offer {
            justify-content: flex-start;
            text-align: left;
        }
    }

    @media (max-width: 768px) {
        .section-selector {
            gap: 0.8rem;
            justify-content: flex-start;
            flex-wrap: nowrap;
            overflow-x: auto;
            padding-bottom: 0.5rem;
            margin-bottom: 2rem;
        }

        .section-selector::-webkit-scrollbar {
            height: 4px;
        }

        .section-selector::-webkit-scrollbar-track {
            background: rgba(0, 212, 255, 0.1);
            border-radius: 10px;
        }

        .section-selector::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 4px;
        }

        .radio-button {
            padding: 0.6rem 1.2rem;
            font-size: 0.9rem;
            white-space: nowrap;
        }

        .pdf-download-wrapper {
            width: 100%;
            position: relative;
        }

        .pdf-download-btn {
            width: 100%;
            justify-content: center;
            padding: 0.8rem 1.2rem;
            font-size: 0.95rem;
        }

        .pdf-menu {
            position: fixed;
            top: auto;
            left: 50%;
            right: auto;
            bottom: 0;
            transform: translateX(-50%);
            min-width: calc(100% - 2rem);
            max-width: 100%;
            border-radius: 20px 20px 0 0;
            max-height: 75vh;
            overflow-y: auto;
            animation: slideUpMobile 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            margin: 0;
        }

        @keyframes slideUpMobile {
            from {
                opacity: 0;
                transform: translateX(-50%) translateY(100%);
            }

            to {
                opacity: 1;
                transform: translateX(-50%) translateY(0);
            }
        }

        .pdf-item {
            padding: 1.3rem 1.6rem;
            gap: 1rem;
        }

        .pdf-item:hover {
            padding-left: 1.8rem;
        }

        .video-courses-container {
            padding: 1rem;
        }

        .course-info-card {
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .course-header {
            flex-direction: column;
            gap: 1rem;
            align-items: flex-start;
            padding-bottom: 1rem;
        }

        .course-header h2 {
            font-size: 1.5rem;
        }

        .course-price {
            font-size: 2rem;
        }

        .course-modules li {
            font-size: 0.95rem;
            padding: 0.6rem 0;
        }

        .btn-buy-course {
            padding: 0.9rem;
            font-size: 1rem;
        }

        .video-main-container {
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .video-playlist-wrapper {
            padding: 1rem;
            max-height: 300px;
        }

        .playlist-title {
            font-size: 1rem;
            margin-bottom: 0.8rem;
        }

        .video-item {
            padding: 0.6rem;
            font-size: 0.85rem;
        }

        .video-title {
            font-size: 1.1rem;
            margin-top: 0.8rem;
        }

        .private-training-section {
            padding: 1rem;
        }

        .private-training-box {
            padding: 1.5rem;
        }

        .private-training-title {
            font-size: 1.5rem;
        }

        .private-training-subtitle {
            font-size: 1rem;
        }

        .private-training-description {
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
        }

        .private-session-btn {
            padding: 0.8rem 1.8rem;
            font-size: 0.95rem;
            width: 100%;
            text-align: center;
        }
    }

    @media (max-width: 480px) {
        .section-selector {
            gap: 0.6rem;
            margin-bottom: 2rem;
        }

        .radio-button {
            padding: 0.55rem 1rem;
            font-size: 0.85rem;
        }

        .pdf-download-btn {
            padding: 0.7rem 1rem;
            font-size: 0.9rem;
            gap: 0.5rem;
        }

        .pdf-download-btn i {
            font-size: 1rem;
        }

        .pdf-menu {
            min-width: calc(100% - 1rem);
            margin: 0 0.5rem;
        }

        .pdf-item {
            padding: 1rem 1.3rem;
            gap: 0.8rem;
            font-size: 0.95rem;
        }

        .course-info-card {
            padding: 1.2rem;
            margin-bottom: 1.5rem;
        }

        .course-header {
            flex-direction: column;
            gap: 1rem;
        }

        .course-header h2 {
            font-size: 1.3rem;
        }

        .course-price {
            font-size: 1.8rem;
        }

        .course-modules {
            margin: 1rem 0;
        }

        .course-modules li {
            font-size: 0.9rem;
            padding: 0.5rem 0;
        }

        .video-main-container {
            gap: 0.8rem;
            margin-top: 1.2rem;
        }

        .video-player {
            aspect-ratio: 16 / 9;
        }

        .video-title {
            font-size: 1rem;
            margin-top: 0.6rem;
        }

        .video-playlist-wrapper {
            padding: 0.9rem;
            max-height: 250px;
        }

        .playlist-title {
            font-size: 0.95rem;
            margin-bottom: 0.6rem;
            padding-bottom: 0.8rem;
        }

        .video-item {
            padding: 0.5rem;
            gap: 0.5rem;
        }

        .video-item-icon {
            font-size: 1rem;
        }

        .video-item-title {
            font-size: 0.8rem;
        }

        .private-training-section {
            padding: 0.8rem;
        }

        .private-training-box {
            padding: 1.2rem;
        }

        .private-training-title {
            font-size: 1.3rem;
            margin-bottom: 0.8rem;
        }

        .private-training-subtitle {
            font-size: 0.95rem;
            margin-bottom: 1rem;
        }

        .private-training-description {
            font-size: 0.85rem;
            margin-bottom: 1.2rem;
        }

        .private-session-btn {
            padding: 0.7rem 1.5rem;
            font-size: 0.9rem;
            width: 100%;
        }

        .btn-buy-course {
            padding: 0.8rem;
            font-size: 0.95rem;
        }
    }

    @media (max-width: 400px) {
        .section-selector {
            gap: 0.5rem;
        }

        .radio-button {
            padding: 0.5rem 0.9rem;
            font-size: 0.8rem;
        }

        .pdf-download-btn {
            padding: 0.6rem 0.9rem;
            font-size: 0.85rem;
        }

        .course-header h2 {
            font-size: 1.2rem;
        }

        .course-price {
            font-size: 1.6rem;
        }

        .pdf-item {
            padding: 0.9rem 1rem;
            font-size: 0.9rem;
            gap: 0.6rem;
        }

        .video-player {
            aspect-ratio: 16 / 9;
            border-radius: 8px;
        }

        .playlist-title {
            font-size: 0.9rem;
        }

        .private-training-title {
            font-size: 1.2rem;
        }

        .private-training-box {
            padding: 1rem;
        }
    }
</style>

<style>
    /* Refined course navigation and lesson experience */
    .section-selector {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr)) minmax(240px, 1.15fr);
        gap: 1rem;
        align-items: stretch;
        margin: 0 0 2rem;
        padding: 0;
        overflow: visible;
    }

    .radio-label {
        display: block;
        min-width: 0;
    }

    .radio-button,
    .pdf-download-btn {
        width: 100%;
        min-height: 76px;
        padding: 0.9rem 1rem;
        border: 1px solid rgba(0, 212, 255, 0.22);
        border-radius: 16px;
        background: linear-gradient(145deg, rgba(255, 255, 255, .055), rgba(0, 212, 255, .025));
        color: var(--text-light);
        display: flex;
        align-items: center;
        gap: .8rem;
        text-align: left;
        box-shadow: 0 8px 25px rgba(0, 0, 0, .16);
        transition: transform .25s ease, border-color .25s ease, box-shadow .25s ease, background .25s ease;
    }

    .radio-button>span:last-child,
    .pdf-download-btn>span:nth-child(2) {
        display: grid;
        gap: .2rem;
        min-width: 0;
    }

    .radio-button strong,
    .pdf-download-btn strong {
        font-size: .96rem;
        line-height: 1.2;
        color: var(--text-light);
    }

    .radio-button small,
    .pdf-download-btn small {
        color: var(--text-muted);
        font-size: .72rem;
        font-weight: 500;
        white-space: nowrap;
    }

    .module-icon,
    .workbook-icon {
        width: 42px;
        height: 42px;
        border-radius: 13px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 42px;
        background: rgba(0, 212, 255, .1);
        color: var(--primary-color);
        border: 1px solid rgba(0, 212, 255, .25);
        font-size: .78rem;
        font-weight: 800;
    }

    .workbook-icon {
        background: rgba(231, 76, 60, .12);
        color: #ff6b6b;
        border-color: rgba(231, 76, 60, .3);
        font-size: 1.15rem;
    }

    .workbook-chevron {
        margin-left: auto;
        color: var(--text-muted);
        font-size: .8rem !important;
    }

    .radio-label input[type="radio"]:checked+.radio-button {
        color: var(--text-light);
        background: linear-gradient(145deg, rgba(0, 212, 255, .18), rgba(0, 184, 148, .08));
        border-color: var(--primary-color);
        box-shadow: 0 10px 30px rgba(0, 212, 255, .18), inset 0 0 0 1px rgba(0, 212, 255, .08);
        transform: translateY(-2px);
    }

    .radio-label input[type="radio"]:checked+.radio-button .module-icon {
        background: var(--primary-color);
        color: #07111f;
    }

    .radio-label input[type="radio"]:hover+.radio-button,
    .pdf-download-btn:hover {
        transform: translateY(-3px);
        border-color: rgba(0, 212, 255, .65);
        color: var(--text-light);
    }

    .pdf-download-wrapper {
        width: 100%;
        align-items: stretch;
    }

    .pdf-lock-overlay {
        border-radius: 16px;
    }

    .pdf-menu {
        left: auto;
        right: 0;
        width: min(380px, 90vw);
        min-width: 0;
    }

    .video-main-container {
        grid-template-columns: minmax(0, 1.65fr) minmax(300px, .75fr);
        gap: 1.25rem;
        padding: 1rem;
        border: 1px solid rgba(0, 212, 255, .16);
        border-radius: 22px;
        background: linear-gradient(145deg, rgba(255, 255, 255, .035), rgba(0, 0, 0, .08));
        box-shadow: 0 18px 55px rgba(0, 0, 0, .2);
    }

    .video-player {
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 12px 35px rgba(0, 0, 0, .35);
        background: #000;
    }

    .video-player video {
        display: block;
        width: 100%;
        aspect-ratio: 16/9;
        object-fit: contain;
        background: #000;
    }

    .video-locked-overlay .lock-content {
        width: auto;
        min-width: 310px;
        max-width: 420px;
        padding: 1.4rem 1.8rem;
        gap: .75rem;
        border-width: 1px;
        border-radius: 12px;
    }

    .video-locked-overlay .lock-icon {
        font-size: 2.8rem;
        margin-bottom: 0;
    }

    .video-locked-overlay .unlock-title {
        width: auto;
        padding: .7rem 1.1rem;
        font-size: 1.5rem;
        border-radius: 8px;
        white-space: nowrap;
    }

    /* Professional locked-lesson card */
    .video-locked-overlay {
        background:
            radial-gradient(circle at 50% 42%, rgba(0, 212, 255, .12), transparent 38%),
            rgba(2, 7, 18, .88);
        backdrop-filter: blur(5px);
    }

    .video-locked-overlay .lock-content {
        position: relative;
        width: min(390px, 72%);
        min-width: 0;
        max-width: 390px;
        padding: 1.6rem 1.8rem 1.45rem;
        gap: .9rem;
        border: 1px solid rgba(0, 212, 255, .28);
        border-radius: 18px;
        background: linear-gradient(145deg, rgba(19, 31, 57, .94), rgba(7, 13, 30, .96));
        box-shadow: 0 22px 55px rgba(0, 0, 0, .42), inset 0 1px 0 rgba(255, 255, 255, .06);
        overflow: hidden;
    }

    .video-locked-overlay .lock-content::before {
        content: '';
        position: absolute;
        top: 0;
        left: 18%;
        right: 18%;
        height: 3px;
        border-radius: 0 0 999px 999px;
        background: linear-gradient(90deg, transparent, var(--primary-color), #00b894, transparent);
    }

    .video-locked-overlay .lock-icon {
        width: 64px;
        height: 64px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin: 0;
        border-radius: 18px;
        font-size: 1.65rem;
        color: var(--primary-color);
        background: linear-gradient(145deg, rgba(0, 212, 255, .15), rgba(0, 184, 148, .06));
        border: 1px solid rgba(0, 212, 255, .28);
        text-shadow: none;
        filter: none;
        animation: none;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, .08);
    }

    .video-locked-overlay .unlock-title {
        width: auto;
        padding: 0;
        border: 0;
        border-radius: 0;
        background: none;
        color: #f3fbff;
        font-size: clamp(1.15rem, 2vw, 1.45rem);
        font-weight: 800;
        letter-spacing: .01em;
        line-height: 1.25;
        text-shadow: none;
        animation: none;
    }

    .video-title {
        margin-top: .9rem;
        padding: .1rem .35rem;
        font-size: 1.15rem;
        font-weight: 750;
        color: var(--text-light);
        text-transform: capitalize;
    }

    .video-title::before {
        content: 'NOW PLAYING';
        display: block;
        margin-bottom: .3rem;
        color: var(--primary-color);
        font-size: .65rem;
        letter-spacing: .14em;
        font-weight: 800;
    }

    .video-playlist-wrapper {
        max-height: 590px;
        padding: 1rem;
        border-radius: 16px;
        border: 1px solid rgba(0, 212, 255, .14);
        background: rgba(5, 12, 28, .55);
        box-shadow: none;
    }

    .playlist-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: .75rem;
        padding: .25rem .2rem .85rem;
        border-bottom: 1px solid rgba(0, 212, 255, .15);
        margin-bottom: .75rem;
    }

    .playlist-eyebrow {
        display: block;
        color: var(--text-muted);
        font-size: .62rem;
        letter-spacing: .13em;
        font-weight: 800;
        margin-bottom: .25rem;
    }

    .playlist-title {
        margin: 0;
        padding: 0;
        border: 0;
        font-size: 1.05rem;
        color: var(--text-light);
    }

    .lesson-count {
        padding: .35rem .55rem;
        border-radius: 999px;
        color: var(--primary-color);
        background: rgba(0, 212, 255, .1);
        font-size: .72rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .video-playlist {
        gap: .55rem;
        padding-right: .25rem;
    }

    .video-item {
        min-height: 58px;
        padding: .65rem .7rem;
        gap: .7rem;
        border: 1px solid transparent;
        border-left: 1px solid transparent;
        border-radius: 12px;
        background: rgba(255, 255, 255, .035);
    }

    .video-item:hover {
        transform: translateY(-1px);
        border-color: rgba(0, 212, 255, .28);
        background: rgba(0, 212, 255, .08);
    }

    .video-item.active {
        border-color: rgba(0, 212, 255, .5);
        background: linear-gradient(90deg, rgba(0, 212, 255, .15), rgba(0, 184, 148, .06));
    }

    .video-item-icon {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(0, 212, 255, .09);
        color: var(--primary-color);
        font-size: .72rem;
        font-weight: 800;
        flex: 0 0 34px;
    }

    .video-item.active .video-item-icon {
        background: var(--primary-color);
        color: #06101c;
    }

    .video-item-title {
        white-space: normal;
        line-height: 1.3;
        font-size: .82rem;
    }

    .private-session-btn {
        display: block;
        width: fit-content;
        margin: 1.5rem auto 0;
        text-align: center;
    }

    @media (max-width: 1100px) {
        .section-selector {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .video-main-container {
            grid-template-columns: 1fr;
        }

        .video-playlist-wrapper {
            max-height: 430px;
        }
    }

    @media (max-width: 640px) {
        .video-courses-container {
            padding: .75rem;
        }

        .section-selector {
            grid-template-columns: 1fr;
            gap: .65rem;
            margin-bottom: 1.2rem;
            overflow: visible;
        }

        .radio-button,
        .pdf-download-btn {
            min-height: 64px;
            padding: .7rem .8rem;
            border-radius: 14px;
        }

        .module-icon,
        .workbook-icon {
            width: 38px;
            height: 38px;
            flex-basis: 38px;
            border-radius: 11px;
        }

        .radio-button strong,
        .pdf-download-btn strong {
            font-size: .9rem;
        }

        .radio-button small,
        .pdf-download-btn small {
            font-size: .68rem;
        }

        .pdf-lock-overlay {
            border-radius: 14px;
        }

        .pdf-menu {
            position: fixed;
            left: 50%;
            right: auto;
            width: calc(100% - 1rem);
            min-width: 0;
        }

        .video-main-container {
            margin-top: 1rem;
            padding: .55rem;
            gap: .8rem;
            border-radius: 16px;
        }

        .video-player {
            border-radius: 12px;
        }

        .video-locked-overlay .lock-content {
            min-width: 240px;
            max-width: 320px;
            padding: 1rem 1.25rem;
        }

        .video-locked-overlay .lock-icon { font-size: 2rem; }
        .video-locked-overlay .unlock-title { font-size: 1.2rem; padding: .55rem .85rem; }

        .video-locked-overlay .lock-content {
            width: min(280px, 78%);
            min-width: 0;
            max-width: 280px;
            padding: 1.1rem 1rem 1rem;
            gap: .65rem;
            border-radius: 14px;
        }

        .video-locked-overlay .lock-icon {
            width: 48px;
            height: 48px;
            font-size: 1.25rem;
            border-radius: 13px;
        }

        .video-locked-overlay .unlock-title {
            padding: 0;
            font-size: 1rem;
        }

        .video-title {
            font-size: 1rem;
            padding: 0 .2rem;
            margin-top: .7rem;
        }

        .video-playlist-wrapper {
            padding: .7rem;
            max-height: none;
            border-radius: 13px;
        }

        .video-playlist {
            max-height: 360px;
        }

        .video-item {
            min-height: 54px;
            padding: .6rem;
        }

        .video-item-title {
            font-size: .8rem;
        }

        .playlist-title {
            font-size: .95rem;
        }

        .lesson-count {
            font-size: .68rem;
        }
    }
</style>

<script>
    // Course price and ID for cart operations
    const coursePrice = <?php echo json_encode((float) $course_price); ?>;
    const courseOfferMarkup = `<span class="course-offer"><strong>$${coursePrice.toFixed(0)}</strong><span class="course-offer-or">or</span><span class="course-offer-free">FREE with Broker Registration</span></span>`;
    const courseActionLabel = `Enroll — $${coursePrice.toFixed(0)} or Free with Broker`;
    const courseId = '<?php echo $course_id; ?>';
    const userId = '<?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null'; ?>';
    const isLoggedIn = Boolean(userId && userId !== 'null');

    // Toggle PDF Menu
    function togglePdfMenu() {
        // Check if user has access before allowing dropdown
        if (!hasAccess) {
            return;
        }

        const pdfMenu = document.getElementById('pdfMenu');
        if (pdfMenu.style.display === 'none' || pdfMenu.style.display === '') {
            pdfMenu.style.display = 'block';
        } else {
            pdfMenu.style.display = 'none';
        }
    }

    // Handle PDF Download - Check Access First
    function handlePdfDownload(pdfPath) {
        if (!hasAccess) {
            return;
        }

        // Create a temporary download link
        const link = document.createElement('a');
        link.href = pdfPath;
        link.download = pdfPath.split('/').pop(); // Get filename from path
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    // Update PDF lock state based on access
    function updatePdfLockState() {
        const pdfLockOverlay = document.getElementById('pdfLockOverlay');
        const pdfDownloadBtn = document.getElementById('pdfDownloadBtn');
        const pdfMenu = document.getElementById('pdfMenu');

        if (!pdfLockOverlay || !pdfDownloadBtn) return;

        if (hasAccess) {
            // User has access - hide lock overlay
            pdfLockOverlay.style.display = 'none';
            pdfDownloadBtn.style.pointerEvents = 'auto';
            pdfDownloadBtn.style.opacity = '1';
        } else {
            // User doesn't have access - show lock overlay and close menu
            pdfLockOverlay.style.display = 'flex';
            pdfDownloadBtn.style.pointerEvents = 'none';
            pdfDownloadBtn.style.opacity = '0.6';

            // Also close the PDF menu if it's open
            if (pdfMenu) {
                pdfMenu.style.display = 'none';
            }
        }
    }

    // Close PDF menu when clicking outside
    document.addEventListener('click', function(event) {
        const pdfWrapper = document.querySelector('.pdf-download-wrapper');
        const pdfMenu = document.getElementById('pdfMenu');
        if (pdfWrapper && !pdfWrapper.contains(event.target)) {
            pdfMenu.style.display = 'none';
        }
    });

    // Add course to cart FUNCTION - Must be defined early before HTML buttons call it
    async function addCourseToCart() {
        console.log('=== ADD TO CART CLICKED ===');
        console.log('userId:', userId);

        if (!userId || userId === 'null') {
            window.location.href = 'login.php';
            return;
        }

        // CHECK IF USER ALREADY HAS AN ORDER
        const userHasExistingOrder = await checkUserHasOrder();
        console.log('User has existing order:', userHasExistingOrder);

        if (userHasExistingOrder) {
            const cartButton = document.querySelector('.btn-buy-course');
            if (cartButton) {
                cartButton.disabled = true;
                cartButton.textContent = 'Order Already Submitted';
                cartButton.style.opacity = '0.65';
                cartButton.style.cursor = 'not-allowed';
            }
            return;
        }

        console.log('Creating new cart entry...');
        // Load current cart from localStorage
        globalCart.load();
        console.log('Current cart items before add:', globalCart.items);

        // CHECK IF ITEM ALREADY IN CART - prevent duplicates
        const itemAlreadyInCart = globalCart.items.some(item => item.courseId === 'complete-mastery');
        if (itemAlreadyInCart) {
            console.log('❌ Course already in cart - preventing duplicate');
            setCourseAlreadyInCartState();
            return;
        }

        // Create ONE course item that includes ALL three sections
        const courseItem = {
            id: 'complete-mastery-' + Date.now(),
            title: 'Trading Mastery Courses',
            price: coursePrice,
            quantity: 1,
            type: 'course',
            courseId: 'complete-mastery',
            sections: ['basics', 'advanced', 'risk'] // Include all sections
        };

        console.log('Adding Trading Mastery Courses to cart:', courseItem);

        // Add to cart
        globalCart.add(courseItem);
        console.log('Cart items after add:', globalCart.items);
        console.log('Verifying localStorage:', localStorage.getItem('cart'));

        const total = globalCart.getTotal();

        setCourseAlreadyInCartState();
    }

    function setCourseAlreadyInCartState() {
        const cartButton = document.querySelector('.btn-buy-course');
        if (!cartButton || cartButton.tagName !== 'BUTTON') return;
        cartButton.disabled = true;
        cartButton.textContent = 'Already in Cart';
        cartButton.style.opacity = '0.72';
        cartButton.style.cursor = 'not-allowed';
        cartButton.onclick = null;
    }

    // Function to show visual confirmation when adding to cart
    function showAddToCartModal(total) {
        const modal = document.createElement('div');
        modal.id = 'addToCartModal';
        modal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 99999;
            backdrop-filter: blur(5px);
        `;

        modal.innerHTML = `
            <div style="
                background: linear-gradient(135deg, rgba(10, 14, 39, 0.95), rgba(26, 31, 58, 0.95));
                border: 2px solid #00d4ff;
                border-radius: 20px;
                padding: 3rem;
                text-align: center;
                max-width: 500px;
                box-shadow: 0 0 50px rgba(0, 212, 255, 0.4), 0 0 100px rgba(0, 255, 136, 0.2);
                animation: popIn 0.5s ease-out;
            ">
                <div style="font-size: 4rem; margin-bottom: 1rem; animation: bounce 0.6s infinite;">✅</div>
                <h2 style="color: #00ff88; font-size: 1.8rem; margin: 1rem 0; text-shadow: 0 0 20px rgba(0, 255, 136, 0.5);">
                    Successfully Added to Cart!
                </h2>
                <p style="color: #00d4ff; font-size: 1.2rem; margin: 1.5rem 0; font-weight: bold;">
                    Trading Mastery Courses
                </p>
                <div style="background: rgba(0, 212, 255, 0.1); border-left: 4px solid #00ff88; padding: 1.5rem; margin: 1.5rem 0; border-radius: 8px; text-align: left;">
                    <p style="color: var(--text-muted); margin: 0.5rem 0; font-size: 0.95rem;">
                        ✓ 8 Basics Videos
                    </p>
                    <p style="color: var(--text-muted); margin: 0.5rem 0; font-size: 0.95rem;">
                        ✓ 15 Advanced Videos
                    </p>
                    <p style="color: var(--text-muted); margin: 0.5rem 0; font-size: 0.95rem;">
                        ✓ 1 Risk Management Video
                    </p>
                </div>
                <div style="background: rgba(0, 255, 136, 0.1); border: 2px solid #00ff88; padding: 1rem; border-radius: 10px; margin: 1.5rem 0;">
                    <p style="color: var(--text-muted); font-size: 0.9rem; margin: 0.5rem 0;">Total Amount</p>
                    <p style="color: #00ff88; font-size: 2.5rem; font-weight: bold; margin: 0.5rem 0;">$${total.toFixed(2)}</p>
                </div>
                <p style="color: var(--text-muted); font-size: 1rem; margin-top: 1.5rem;">
                    Redirecting to cart in <span id="countdown" style="color: #00d4ff; font-weight: bold;">3</span> seconds...
                </p>
            </div>
            <style>
                @keyframes popIn {
                    0% {
                        transform: scale(0.7);
                        opacity: 0;
                    }
                    100% {
                        transform: scale(1);
                        opacity: 1;
                    }
                }
                @keyframes bounce {
                    0%, 100% {
                        transform: scale(1);
                    }
                    50% {
                        transform: scale(1.2);
                    }
                }
            </style>
        `;

        document.body.appendChild(modal);

        // Countdown timer
        let countdown = 6;
        const countdownEl = document.getElementById('countdown');
        const countdownInterval = setInterval(() => {
            countdown--;
            if (countdownEl) {
                countdownEl.textContent = countdown;
            }
            if (countdown <= 0) {
                clearInterval(countdownInterval);
            }
        }, 1000);
    }

    // Global cart object
    const globalCart = {
        items: [],
        save: function() {
            try {
                localStorage.setItem('cart', JSON.stringify(this.items));
                console.log('✓ Cart saved to localStorage:', this.items);
            } catch (e) {
                console.error('Error saving cart:', e);
            }
        },
        load: function() {
            try {
                this.items = JSON.parse(localStorage.getItem('cart') || '[]');
                console.log('✓ Cart loaded from localStorage:', this.items);
            } catch (e) {
                console.error('Error loading cart:', e);
                this.items = [];
            }
        },
        add: function(item) {
            this.items.push(item);
            this.save();
            console.log('✓ Item added to cart:', item);
        },
        clear: function() {
            this.items = [];
            this.save();
            console.log('Cart cleared');
        },
        getTotal: function() {
            return this.items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        }
    };

    // TEMPORARY DEBUG FUNCTION - Clear old orders to test
    window.clearOldOrders = function() {
        console.log('🧹 Clearing all orders from localStorage...');
        localStorage.removeItem('admin_orders');
        localStorage.removeItem('cart');

        // Get current username and remove specific orders
        const sessionUsername = sessionStorage.getItem('currentUsername');
        if (sessionUsername) {
            localStorage.removeItem('user_orders_' + sessionUsername);
        }

        console.log('✅ Old orders cleared! Reload the page to see changes');
        location.reload();
    };

    // Access control - Check localStorage for course purchases
    let hasAccess = false;
    const userName = '<?php echo $user_name ?? ''; ?>';
    const userEmail = '<?php echo isset($_SESSION['email']) ? $_SESSION['email'] : ''; ?>';
    const currentUsername = sessionStorage.getItem('currentUsername');
    const currentEmail = sessionStorage.getItem('currentEmail');

    // Check if user has unlocked access to current course from DB
    async function checkCourseAccess(courseId) {
        if (!userId || userId === 'null') return false;
        try {
            const res = await fetch('check_access.php?product=course', { cache: 'no-store' });
            const data = await res.json();
            return data.status === 'unlocked';
        } catch(e) {
            return false;
        }
    }
    async function checkUserHasOrder() {
        if (!userId || userId === 'null') return null;
        try {
            const res = await fetch('check_access.php?product=course', { cache: 'no-store' });
            const data = await res.json();
            return data.status;
        } catch(e) {
            return null;
        }
    }

    async function requestCourseAccess() {
        if (!userId || userId === 'null') {
            window.location.href = 'login.php?redirect=courses.php';
            return;
        }
        if (window.TTRPhoneVerification && !(await window.TTRPhoneVerification.ensureVerifiedPhone())) {
            return;
        }
        const actionArea = document.getElementById('courseActionArea');
        if (actionArea) {
            actionArea.innerHTML = '<button type="button" class="btn-buy-course" disabled style="opacity:.6;cursor:not-allowed;background:#444;"><i class="fas fa-spinner fa-spin" aria-hidden="true"></i> Submitting Request...</button>';
        }
        try {
            const res = await fetch('record_free_access.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'product=course',
                cache: 'no-store'
            });
            const data = await res.json();
            updateCartButtonState();
            updateAccessStatus();
        } catch(e) {
            console.error('Request failed:', e);
            if (actionArea) {
                actionArea.innerHTML = `<button type="button" class="btn-buy-course" onclick="requestCourseAccess()">${courseActionLabel}</button>`;
            }
        }
    }

    // Update access status when section changes
    async function updateAccessStatus() {
        hasAccess = await checkCourseAccess(currentSection);
        const overlay = document.querySelector('.video-locked-overlay');
        if (overlay) {
            if (isLoggedIn && hasAccess) {
                overlay.style.display = 'none';
                overlay.style.visibility = 'hidden';
                const video = document.getElementById('mainVideo');
                if (video) {
                    video.removeAttribute('disabled');
                    video.controls = true;
                    if (!video.getAttribute('src')) loadVideo(currentVideoIndex);
                }
            } else {
                overlay.style.display = 'flex';
                overlay.style.visibility = 'visible';
                const video = document.getElementById('mainVideo');
                if (video && video.getAttribute('src')) {
                    video.pause();
                    video.removeAttribute('src');
                    video.load();
                }
            }
        }
        updatePdfLockState();
    }

    // Access control check - prevent playback if not purchased
    const videoElement = document.getElementById('mainVideo');
    let playbackProtectionEnabled = false;

    function preventUnauthorizedPlayback() {
        if (!videoElement || playbackProtectionEnabled) {
            console.log('Video protection: Already enabled or no video element');
            return; // Prevent duplicate listeners
        }

        playbackProtectionEnabled = true;

        // This listener checks hasAccess at play time (not at setup time)
        // so it will respect updates to hasAccess variable
        videoElement.addEventListener('play', function(e) {
            console.log('Play attempted. hasAccess:', hasAccess, 'userId:', userId);

            if (!userId) {
                // Not logged in
                e.preventDefault();
                this.pause();
            } else if (!hasAccess) {
                // Logged in but not purchased
                e.preventDefault();
                this.pause();
            }
        });

        videoElement.addEventListener('seeking', function(e) {
            if (!hasAccess) {
                e.preventDefault();
            }
        });

        console.log('✓ Playback protection enabled');
    }

    async function updateCartButtonState() {
        const actionArea = document.getElementById('courseActionArea');
        const badgeStatus = document.getElementById('courseBadgeStatus');
        if (!actionArea) return;

        if (!isLoggedIn) {
            actionArea.innerHTML = '<a href="login.php?redirect=courses.php" class="btn-buy-course">Login to Enroll</a>';
            if (badgeStatus) badgeStatus.innerHTML = courseOfferMarkup;
            return;
        }

        const orderStatus = await checkUserHasOrder();

        if (orderStatus === 'unlocked') {
            actionArea.innerHTML = '<div class="access-granted" style="color:#00ff88;font-weight:bold;padding:.8rem 1.2rem;background:rgba(0,255,136,0.1);border:1px solid rgba(0,255,136,0.3);border-radius:10px;display:inline-block;">✓ Access Approved — Course Unlocked</div>';
            if (badgeStatus) badgeStatus.innerHTML = '<span style="color: #00ff88;"><i class="fas fa-check-circle"></i> APPROVED</span>';
        } else if (orderStatus === 'pending') {
            actionArea.innerHTML = '<button type="button" class="btn-buy-course" disabled style="opacity:.7;cursor:not-allowed;background:#333;color:#f59d00;border:1px solid #f59d00;"><i class="fas fa-clock" aria-hidden="true"></i> Pending Admin Approval</button>';
            if (badgeStatus) badgeStatus.innerHTML = '<span style="color: #f59d00;"><i class="fas fa-clock"></i> PENDING</span>';
        } else {
            actionArea.innerHTML = `<button type="button" class="btn-buy-course" onclick="requestCourseAccess()">${courseActionLabel}</button>`;
            if (badgeStatus) badgeStatus.innerHTML = courseOfferMarkup;
        }
    }

    // Video data from PHP
    const videoData = <?php echo json_encode($videoSections); ?>;
    let currentSection = 'basics';
    let currentVideoIndex = 0;

    // Radio button event listeners
    document.querySelectorAll('input[name="course-section"]').forEach(radio => {
        radio.addEventListener('change', function() {
            currentSection = this.value;
            currentVideoIndex = 0;
            updateAccessStatus(); // Check access when section changes
            updateVideoSection();
        });
    });

    function updateVideoSection() {
        const section = videoData[currentSection];

        // Update section title
        document.getElementById('sectionTitle').textContent = section.title + ' Videos';
        const lessonCount = document.getElementById('lessonCount');
        if (lessonCount) lessonCount.textContent = section.videos.length + (section.videos.length === 1 ? ' lesson' : ' lessons');

        // Load first video of the section
        if (section.videos.length > 0) {
            loadVideo(0);
        }

        // Update playlist
        updatePlaylist();
    }

    function updatePlaylist() {
        const section = videoData[currentSection];
        const playlistDiv = document.getElementById('videoPlaylist');
        playlistDiv.innerHTML = '';

        section.videos.forEach((video, index) => {
            const videoItem = document.createElement('div');
            videoItem.className = 'video-item' + (index === currentVideoIndex ? ' active' : '');
            videoItem.setAttribute('role', 'button');
            videoItem.setAttribute('tabindex', '0');
            videoItem.setAttribute('aria-label', `Play lesson ${index + 1}: ${video.replace('.mp4', '')}`);
            videoItem.innerHTML = `
                <span class="video-item-icon">${String(index + 1).padStart(2, '0')}</span>
                <span class="video-item-title">${video.replace('.mp4', '')}</span>
            `;
            videoItem.addEventListener('click', () => loadVideo(index));
            videoItem.addEventListener('keydown', event => {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    loadVideo(index);
                }
            });
            playlistDiv.appendChild(videoItem);
        });
    }

    function loadVideo(index) {
        const section = videoData[currentSection];
        if (index >= 0 && index < section.videos.length) {
            currentVideoIndex = index;
            const videoFile = section.videos[index];

            // Determine video path based on section
            let videoFolder = 'videos/basics/';
            if (currentSection === 'advanced') {
                videoFolder = 'videos/advanced/';
            } else if (currentSection === 'risk') {
                videoFolder = 'videos/Risk%20management/'; // URL encoded space
            }

            const videoPath = videoFolder + videoFile;

            const videoElement = document.getElementById('mainVideo');
            document.getElementById('videoTitle').textContent = videoFile.replace('.mp4', '');

            // Keep large lesson files out of the initial page request. Locked users
            // can browse the syllabus without downloading any video data.
            if (!hasAccess) {
                if (videoElement.getAttribute('src')) {
                    videoElement.pause();
                    videoElement.removeAttribute('src');
                    videoElement.load();
                }
                updatePlaylist();
                return;
            }

            videoElement.src = videoPath;

            // Add error handling
            videoElement.onerror = function() {
                console.error('Failed to load video:', videoPath);
                document.getElementById('videoTitle').textContent = videoFile.replace('.mp4', '') + ' (Error loading)';
            };

            videoElement.onloadstart = function() {
                console.log('Loading video:', videoPath);
            };

            videoElement.oncanplay = function() {
                console.log('Video ready to play:', videoPath);
                document.getElementById('videoTitle').textContent = videoFile.replace('.mp4', '');
            };

            // Load metadata
            videoElement.load();

            updatePlaylist();
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', async function() {
        // Get logged in user info
        const currentUsername = sessionStorage.getItem('currentUsername');
        const currentEmail = sessionStorage.getItem('currentEmail');
        // Check initial access status FROM DATABASE (requires admin approval)
        hasAccess = await checkCourseAccess(currentSection);
        console.log('DOMContentLoaded - Initial hasAccess from DB:', hasAccess);

        updateCartButtonState();
        updatePdfLockState();

        const overlay = document.querySelector('.video-locked-overlay');
        if (overlay) {
            if (isLoggedIn && hasAccess) {
                overlay.style.display = 'none';
                overlay.style.visibility = 'hidden';
                if (videoElement) videoElement.controls = true;
            } else {
                overlay.style.display = 'flex';
                overlay.style.visibility = 'visible';
                if (videoElement) videoElement.controls = false;
            }
        }

        // Prevent unauthorized playback
        preventUnauthorizedPlayback();

        // Load initial video section
        updateVideoSection();

        // Log access status for debugging
        console.log('User ID:', userId);
        console.log('User Name:', userName);
        console.log('Current Section:', currentSection);
        console.log('Has access to ' + currentSection + ':', hasAccess);
    });

    async function syncCourseApprovalState() {
        if (document.hidden) return;
        await updateAccessStatus();
        await updateCartButtonState();
        updatePdfLockState();
    }

    // Keep pending/approved state synchronized with admin actions.
    setInterval(syncCourseApprovalState, 5000);

    // Listen for sessionStorage changes (logout/login from other tabs or login action)
    window.addEventListener('storage', function(e) {
        if (e.key === 'userLogged' || e.key === 'currentUsername' || e.key === 'currentEmail') {
            console.log('Auth status changed! Reloading access check...');
            // User logged in or out - refresh access status
            syncCourseApprovalState();
        }
    });

    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) syncCourseApprovalState();
    });
    window.addEventListener('focus', syncCourseApprovalState);

    // Listen for logout event from same tab
    window.addEventListener('userLogout', function() {
        console.log('User logged out - locking all course content');
        hasAccess = false;
        console.log('hasAccess set to:', hasAccess);

        // Close and lock the PDF section
        const pdfMenu = document.getElementById('pdfMenu');
        const pdfLockOverlay = document.getElementById('pdfLockOverlay');
        const pdfDownloadBtn = document.getElementById('pdfDownloadBtn');

        if (pdfMenu) pdfMenu.style.display = 'none';
        if (pdfLockOverlay) pdfLockOverlay.style.display = 'flex';
        if (pdfDownloadBtn) {
            pdfDownloadBtn.style.pointerEvents = 'none';
            pdfDownloadBtn.style.opacity = '0.6';
        }

        console.log('PDF section locked');
        updateAccessStatus();
        updateCartButtonState();
    });

    // Also listen for when this same tab logs in/out
    document.addEventListener('beforeunload', function() {
        // Before page reload, save current state if needed
    });
</script>

<script>
(function() {
    var wrap = document.getElementById('coursesTiltVisual');
    var img  = document.getElementById('coursesHeroImg');
    if (!wrap || !img) return;
    var raf;
    wrap.addEventListener('mousemove', function(e) {
        cancelAnimationFrame(raf);
        raf = requestAnimationFrame(function() {
            var r = wrap.getBoundingClientRect();
            var x = (e.clientX - r.left) / r.width  - 0.5;
            var y = (e.clientY - r.top)  / r.height - 0.5;
            img.style.transform = 'perspective(700px) rotateX(' + (y * -12) + 'deg) rotateY(' + (x * 12) + 'deg) scale(1.04)';
        });
    });
    wrap.addEventListener('mouseleave', function() {
        img.style.transform = '';
    });
})();
</script>

<?php include 'footer.php'; ?>
