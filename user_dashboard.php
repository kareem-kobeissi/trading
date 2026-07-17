<?php
// user_dashboard.php
include 'header.php';
?>
<style>
    .dashboard-layout {
        display: grid;
        grid-template-columns: 280px 1fr;
        gap: 2.5rem;
        min-height: calc(100vh - 200px);
        animation: fadeInUp 0.6s ease-out;
    }

    .dashboard-sidebar {
        background: linear-gradient(145deg, rgba(26, 31, 58, 0.8), rgba(10, 14, 39, 0.9));
        border-radius: 20px;
        padding: 2.5rem;
        border: 1px solid rgba(0, 212, 255, 0.15);
        height: fit-content;
        position: sticky;
        top: 130px;
        box-shadow:
            0 15px 50px rgba(0, 0, 0, 0.4),
            0 0 0 1px rgba(255, 255, 255, 0.05) inset;
        animation: slideInLeft 0.6s ease-out;
    }

    .dashboard-sidebar h3 {
        color: var(--primary-color);
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid rgba(0, 212, 255, 0.2);
        animation: textGlow 3s infinite ease-in-out;
    }

    .sidebar-menu {
        list-style: none;
    }

    .sidebar-menu li {
        margin-bottom: 0.8rem;
        animation: fadeInUp 0.5s ease-out backwards;
    }

    .sidebar-menu li:nth-child(1) {
        animation-delay: 0.1s;
    }

    .sidebar-menu li:nth-child(2) {
        animation-delay: 0.2s;
    }

    .sidebar-menu li:nth-child(3) {
        animation-delay: 0.3s;
    }

    .sidebar-menu a {
        color: var(--text-light);
        text-decoration: none;
        padding: 1rem 1.2rem;
        border-radius: 12px;
        display: block;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }

    .sidebar-menu a::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 0;
        background: linear-gradient(90deg, rgba(0, 212, 255, 0.2), transparent);
        transition: width 0.4s ease;
        border-radius: 12px;
    }

    .sidebar-menu a:hover::before {
        width: 100%;
    }

    .sidebar-menu a:hover {
        color: var(--primary-color);
        transform: translateX(8px);
    }

    .sidebar-menu a.active {
        background: linear-gradient(135deg, rgba(0, 212, 255, 0.2), rgba(0, 100, 200, 0.2));
        color: var(--primary-color);
        border-left: 3px solid var(--primary-color);
        box-shadow: 0 4px 15px rgba(0, 212, 255, 0.1);
    }

    .logout-btn {
        width: 100%;
        padding: 1rem;
        background: linear-gradient(135deg, var(--danger-color), #dc2626);
        color: white;
        border: none;
        border-radius: 12px;
        cursor: pointer;
        margin-top: 2rem;
        font-weight: 700;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .logout-btn::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        transform: translate(-50%, -50%);
        transition: width 0.6s ease, height 0.6s ease;
    }

    .logout-btn:hover::before {
        width: 300px;
        height: 300px;
    }

    .logout-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(239, 68, 68, 0.4);
    }

    .dashboard-content {
        background: linear-gradient(145deg, rgba(26, 31, 58, 0.6), rgba(10, 14, 39, 0.7));
        border-radius: 24px;
        padding: 3rem;
        border: 1px solid rgba(0, 212, 255, 0.15);
        box-shadow:
            0 20px 50px rgba(0, 0, 0, 0.3),
            0 0 0 1px rgba(255, 255, 255, 0.03) inset;
        animation: slideInRight 0.6s ease-out;
        position: relative;
        overflow: hidden;
    }

    .dashboard-content::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, transparent, var(--primary-color), #00b894, transparent);
        animation: shimmer 4s infinite linear;
        background-size: 200% 100%;
    }

    .dashboard-title {
        font-size: 2.2rem;
        background: linear-gradient(135deg, var(--primary-color), #00b894, #00a8cc);
        background-size: 200% 200%;
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid rgba(0, 212, 255, 0.2);
        animation: gradientShift 4s ease infinite;
    }

    .orders-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 2rem;
        animation: fadeInUp 0.6s ease-out;
    }

    .orders-table thead {
        background: linear-gradient(135deg, rgba(0, 212, 255, 0.1), rgba(0, 100, 200, 0.1));
    }

    .orders-table th {
        padding: 1.2rem 1rem;
        text-align: left;
        color: var(--primary-color);
        font-weight: 700;
        border-bottom: 2px solid rgba(0, 212, 255, 0.2);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.9rem;
    }

    .orders-table td {
        padding: 1.2rem 1rem;
        border-bottom: 1px solid rgba(0, 212, 255, 0.1);
        transition: background 0.3s ease;
    }

    .orders-table tr {
        transition: all 0.3s ease;
    }

    .orders-table tr:hover {
        background: linear-gradient(135deg, rgba(0, 212, 255, 0.08), transparent);
        transform: scale(1.01);
    }

    .status-badge {
        display: inline-block;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .status-pending {
        background: rgba(245, 157, 0, 0.2);
        color: #f59d00;
    }

    .status-unlocked {
        background: rgba(16, 185, 129, 0.2);
        color: #10b981;
    }

    .status-cancelled {
        background: rgba(220, 38, 38, 0.2);
        color: #dc2626;
    }

    .courses-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 2rem;
        margin-top: 2rem;
    }

    .course-access-card {
        background: linear-gradient(135deg, rgba(0, 212, 255, 0.1), transparent);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 12px;
        padding: 2rem;
        text-align: center;
        transition: var(--transition);
    }

    .course-access-card:hover {
        transform: translateY(-5px);
        border-color: var(--primary-color);
        box-shadow: 0 10px 30px rgba(0, 212, 255, 0.2);
    }

    .course-access-card h4 {
        color: var(--primary-color);
        margin-bottom: 1rem;
    }

    .access-badge {
        display: inline-block;
        padding: 0.6rem 1.2rem;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 600;
        margin-top: 1rem;
    }

    .access-granted {
        background: rgba(16, 185, 129, 0.2);
        color: #10b981;
    }

    .access-waiting {
        background: rgba(245, 157, 0, 0.2);
        color: #f59d00;
    }

    .empty-state {
        text-align: center;
        padding: 3rem;
        color: var(--text-muted);
    }

    @media (max-width: 900px) {
        .dashboard-layout {
            grid-template-columns: 1fr;
        }

        .dashboard-sidebar {
            position: static;
        }
    }
</style>

<!-- ===== USER DASHBOARD ===== -->
<section class="section">
    <div class="dashboard-layout">
        <!-- Sidebar -->
        <aside class="dashboard-sidebar">
            <h3 data-i18n="menu">📊 Menu</h3>
            <ul class="sidebar-menu">
                <li><a class="menu-link active" onclick="showSection('orders')" data-i18n="myOrders">📋 My Orders</a></li>
<li><a class="menu-link" onclick="showSection('profile')" data-i18n="profile">👤 Profile</a></li>
<li><a class="menu-link" onclick="showSection('password')">Change Password</a></li>            </ul>
            <button class="logout-btn" onclick="userLogout()" data-i18n="logout">🚪 Logout</button>
        </aside>

        <!-- Content -->
        <main class="dashboard-content">
            <!-- Orders Section -->
            <div id="ordersSection" class="section-content" style="display: block;">
                <h2 class="dashboard-title" data-i18n="myOrders">📋 My Orders</h2>
                <div id="ordersContainer">
                    <div class="empty-state" data-i18n="loadingOrders">Loading orders...</div>
                </div>
            </div>

            <!-- Courses Section -->
            <div id="coursesSection" class="section-content" style="display: none;">
                <h2 class="dashboard-title" data-i18n="myCourses">📚 My Courses</h2>
                <div id="coursesContainer">
                    <div class="empty-state" data-i18n="loadingCourses">Loading courses...</div>
                </div>
            </div>
<!-- Change Password Section -->
<div id="passwordSection" class="section-content" style="display: none;">
    <h2 class="dashboard-title">Change Password</h2>
    <div style="max-width:500px;">
        <div style="background:linear-gradient(135deg,rgba(0,212,255,0.1),transparent);border:1px solid rgba(0,212,255,0.2);border-radius:12px;padding:2rem;">
            
            <div class="form-group" style="margin-bottom:1.5rem;">
                <label style="color:var(--text-muted);display:block;margin-bottom:0.5rem;">Current Password</label>
                <div style="position:relative;">
                    <input type="password" id="currentPassword" placeholder="Enter current password" style="width:100%;padding:1rem;padding-right:3rem;background:rgba(0,212,255,0.05);border:2px solid rgba(0,212,255,0.2);border-radius:10px;color:var(--text-main);font-size:1rem;box-sizing:border-box;">
                    <span onclick="togglePwd('currentPassword','eyeCurrent')" id="eyeCurrent" style="position:absolute;right:1rem;top:50%;transform:translateY(-50%);cursor:pointer;color:var(--text-muted);font-size:1.2rem;">👁</span>
                </div>
            </div>

            <div class="form-group" style="margin-bottom:1.5rem;">
                <label style="color:var(--text-muted);display:block;margin-bottom:0.5rem;">New Password</label>
                <div style="position:relative;">
                    <input type="password" id="newPassword" placeholder="Enter new password" style="width:100%;padding:1rem;padding-right:3rem;background:rgba(0,212,255,0.05);border:2px solid rgba(0,212,255,0.2);border-radius:10px;color:var(--text-main);font-size:1rem;box-sizing:border-box;">
                    <span onclick="togglePwd('newPassword','eyeNew')" id="eyeNew" style="position:absolute;right:1rem;top:50%;transform:translateY(-50%);cursor:pointer;color:var(--text-muted);font-size:1.2rem;">👁</span>
                </div>
            </div>

            <div class="form-group" style="margin-bottom:2rem;">
                <label style="color:var(--text-muted);display:block;margin-bottom:0.5rem;">Confirm New Password</label>
                <div style="position:relative;">
                    <input type="password" id="confirmPassword" placeholder="Confirm new password" style="width:100%;padding:1rem;padding-right:3rem;background:rgba(0,212,255,0.05);border:2px solid rgba(0,212,255,0.2);border-radius:10px;color:var(--text-main);font-size:1rem;box-sizing:border-box;">
                    <span onclick="togglePwd('confirmPassword','eyeConfirm')" id="eyeConfirm" style="position:absolute;right:1rem;top:50%;transform:translateY(-50%);cursor:pointer;color:var(--text-muted);font-size:1.2rem;">👁</span>
                </div>
            </div>

            <div id="passwordMessage" style="display:none;padding:1rem;border-radius:8px;margin-bottom:1.5rem;text-align:center;"></div>

            <button onclick="submitChangePassword()" id="changePasswordBtn" style="width:100%;padding:1.2rem;background:white;color:#000;border:none;border-radius:10px;font-weight:bold;font-size:1.1rem;cursor:pointer;transition:all 0.3s ease;">Update Password</button>
        </div>
    </div>
</div>
            <!-- Profile Section -->
            <div id="profileSection" class="section-content" style="display: none;">
                <h2 class="dashboard-title" data-i18n="myProfile">👤 My Profile</h2>
                <div id="profileContainer">
                    <div class="empty-state" data-i18n="loadingProfile">Loading profile...</div>
                </div>
            </div>
        </main>
    </div>
</section>

<!-- 
    REPLACE the entire <script>...</script> block in user_dashboard.php with this
-->
<script>
    // Check if user is logged in
    function checkUserAuth() {
        if (sessionStorage.getItem('userLogged') !== 'true') {
            window.location.href = 'login.php';
        }
    }

    // ===== LOAD ORDERS FROM DATABASE =====
    async function loadOrders() {
        const currentEmail = sessionStorage.getItem('currentEmail');
        const ordersContainer = document.getElementById('ordersContainer');

        if (!currentEmail) {
            ordersContainer.innerHTML = '<div class="empty-state">Please log in again.</div>';
            return;
        }

        ordersContainer.innerHTML = '<div class="empty-state">Loading orders...</div>';

        try {
            const formData = new FormData();
            formData.append('email', currentEmail);

            const response = await fetch('get_user_orders.php', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();

            if (!data.success) {
                ordersContainer.innerHTML = '<div class="empty-state">Error loading orders: ' + data.message + '</div>';
                return;
            }

            const orders = data.orders;

            if (orders.length === 0) {
                ordersContainer.innerHTML = `<div class="empty-state">${t('noOrders')} <a href="courses.php" style="color: var(--primary-color); text-decoration: underline;">Start Ordering</a></div>`;
                return;
            }

            let html = `
                <div class="orders-table-container">
                    <table class="orders-table">
                        <thead>
                            <tr>
                                <th>${t('orderID')}</th>
                                <th>Products</th>
                                <th>${t('date')}</th>
                                <th>${t('total')}</th>
                                <th>${t('status')}</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            orders.forEach(order => {
                let statusClass, statusText;
                if (order.status === 'cancelled') {
                    statusClass = 'status-cancelled';
                    statusText = t('cancelled');
                } else if (order.status === 'unlocked') {
                    statusClass = 'status-unlocked';
                    statusText = t('unlocked');
                } else {
                    statusClass = 'status-pending';
                    statusText = t('pending');
                }

              const createdDisplay = order.createdTime
                    ? new Date(order.createdTime).toLocaleString('en-US', { timeZone: 'Asia/Beirut', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit', hour12: false })
                    : order.createdAt;

                html += `<tr>
                    <td><strong>#${order.id.slice(-6)}</strong></td>
<td>
    ${order.product_type === 'ea' ? 'TTR Risk Calculator' : 
      order.product_type === 'robot' ? 'TTR Robot' : 
      order.product_type === 'robot_sr' ? 'S&R Precision EA' : 
      order.product_type === 'robot_ib' ? 'Instant Breakout EA' : 
      'Trading Mastery Course'}
</td>                    <td>${createdDisplay}</td>
                    <td>$${parseFloat(order.total).toFixed(2)}</td>
                    <td><span class="status-badge ${statusClass}">${statusText}</span></td>
                </tr>`;
            });

            html += '</tbody></table></div>';
            ordersContainer.innerHTML = html;

        } catch (err) {
            console.error('Error loading orders:', err);
            ordersContainer.innerHTML = '<div class="empty-state">Failed to load orders. Please refresh.</div>';
        }
    }

    // ===== LOAD COURSES FROM DATABASE =====
    async function loadCourses() {
        const currentEmail = sessionStorage.getItem('currentEmail');
        const coursesContainer = document.getElementById('coursesContainer');

        if (!currentEmail) {
            coursesContainer.innerHTML = '<div class="empty-state">Please log in again.</div>';
            return;
        }

        coursesContainer.innerHTML = '<div class="empty-state">Loading courses...</div>';

        try {
            const formData = new FormData();
            formData.append('email', currentEmail);

            const response = await fetch('get_user_orders.php', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();

            if (!data.success) {
                coursesContainer.innerHTML = '<div class="empty-state">Error loading courses.</div>';
                return;
            }

            // Get only unlocked orders
            const unlockedOrders = data.orders.filter(o => o.status === 'unlocked');

            if (unlockedOrders.length === 0) {
                coursesContainer.innerHTML = '<div class="empty-state">No unlocked courses yet. Your courses will appear here once admin approves your purchase.</div>';
                return;
            }

            // Collect all courses from unlocked orders
            const unlockedCourses = unlockedOrders.flatMap(o => o.courses || []);

            let html = '<div class="courses-grid">';
            unlockedCourses.forEach(course => {
                html += `
                    <div class="course-access-card">
                        <h4>📚 ${course.title}</h4>
                        <p style="color: var(--text-muted); font-size: 0.9rem;">$${parseFloat(course.price || 0).toFixed(2)}</p>
                        <span class="access-badge access-granted">✓ Access Granted</span>
                    </div>
                `;
            });
            html += '</div>';
            coursesContainer.innerHTML = html;

        } catch (err) {
            console.error('Error loading courses:', err);
            coursesContainer.innerHTML = '<div class="empty-state">Failed to load courses. Please refresh.</div>';
        }
    }

    // ===== LOAD PROFILE =====
    function loadProfile() {
        const username = sessionStorage.getItem('currentUsername');
        const email = sessionStorage.getItem('currentEmail');
        const profileContainer = document.getElementById('profileContainer');

        profileContainer.innerHTML = `
            <div style="background: linear-gradient(135deg, rgba(0, 212, 255, 0.1), transparent); border-radius: 12px; padding: 2rem; border: 1px solid rgba(0, 212, 255, 0.2); max-width: 500px;">
                <div style="margin-bottom: 1.5rem;">
                    <p style="color: var(--text-muted); margin-bottom: 0.5rem;">Username</p>
                    <p style="color: var(--primary-color); font-size: 1.2rem; font-weight: 600;">${username || 'Not set'}</p>
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <p style="color: var(--text-muted); margin-bottom: 0.5rem;">Email</p>
                    <p style="color: var(--primary-color); font-size: 1.2rem; font-weight: 600;">${email || 'Not set'}</p>
                </div>
            </div>
        `;
    }

    // ===== SHOW SECTION =====
    function showSection(section) {
        document.querySelectorAll('.section-content').forEach(el => el.style.display = 'none');
        document.querySelectorAll('.menu-link').forEach(el => el.classList.remove('active'));
        document.getElementById(section + 'Section').style.display = 'block';
        event.target.classList.add('active');

       if (section === 'orders') loadOrders();
        else if (section === 'courses') loadCourses();
        else if (section === 'profile') loadProfile();
        else if (section === 'password') {
            document.getElementById('currentPassword').value = '';
            document.getElementById('newPassword').value = '';
            document.getElementById('confirmPassword').value = '';
            document.getElementById('passwordMessage').style.display = 'none';
        }
    }

    // ===== USER LOGOUT =====
    function userLogout() {
        if (confirm('Are you sure you want to logout?')) {
            sessionStorage.removeItem('userLogged');
            sessionStorage.removeItem('currentUsername');
            sessionStorage.removeItem('currentEmail');
            window.location.href = 'login.php';
        }
    }

    // ===== INITIALIZE =====
    checkUserAuth();
    loadOrders();

    // Auto-refresh every 30 seconds to catch admin approvals
    setInterval(loadOrders, 30000);

    // Refresh when tab becomes visible
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) loadOrders();
    });
    function togglePwd(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.textContent = '👁';
        } else {
            input.type = 'password';
            icon.textContent = '👁';
        }
    }

    async function submitChangePassword() {
        const currentPassword = document.getElementById('currentPassword').value.trim();
        const newPassword = document.getElementById('newPassword').value.trim();
        const confirmPassword = document.getElementById('confirmPassword').value.trim();
        const msgDiv = document.getElementById('passwordMessage');
        const btn = document.getElementById('changePasswordBtn');

        const showMsg = (msg, success) => {
            msgDiv.style.display = 'block';
            msgDiv.style.background = success ? 'rgba(0,255,136,0.2)' : 'rgba(220,38,38,0.2)';
            msgDiv.style.color = success ? '#00ff88' : '#dc2626';
            msgDiv.textContent = msg;
        };

        if (!currentPassword || !newPassword || !confirmPassword) {
            showMsg('❌ Please fill in all fields', false); return;
        }
        if (newPassword.length < 6) {
            showMsg('❌ New password must be at least 6 characters', false); return;
        }
        if (newPassword !== confirmPassword) {
            showMsg('❌ New passwords do not match', false); return;
        }

        btn.disabled = true;
        btn.textContent = '⏳ Updating...';

        try {
            const formData = new FormData();
            formData.append('email', sessionStorage.getItem('currentEmail'));
            formData.append('current_password', currentPassword);
            formData.append('new_password', newPassword);

            const response = await fetch('change_password.php', { method: 'POST', body: formData });
            const result = await response.json();

           if (result.success) {
                showMsg('✅ Password updated successfully!', true);
                document.getElementById('currentPassword').value = '';
                document.getElementById('newPassword').value = '';
                document.getElementById('confirmPassword').value = '';
                // Remove force change flag — banner will disappear
                sessionStorage.removeItem('forcePasswordChange');
                btn.textContent = '✓ Updated!';
                setTimeout(() => { btn.disabled = false; btn.textContent = 'Update Password'; }, 3000);
            } else {
                showMsg('❌ ' + result.message, false);
                btn.disabled = false;
                btn.textContent = 'Update Password';
            }
        } catch (err) {
            showMsg('❌ Request failed. Please try again.', false);
            btn.disabled = false;
            btn.textContent = 'Update Password';
        }
    }
</script>

<?php include 'footer.php'; ?>
