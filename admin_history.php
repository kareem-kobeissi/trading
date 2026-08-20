<?php
// admin_history.php - Admin Transaction History Page
include 'header.php';
?>

<!-- ===== ADMIN HISTORY HEADER ===== -->
<section class="admin-history-header">
    <div class="admin-history-content">
        <h1 class="admin-history-title" data-i18n="transactionHistory">📊 Transaction History</h1>
        <p class="admin-history-subtitle" data-i18n="viewAllUserTransactions">View complete transaction records with all user activities and timestamps</p>
    </div>
</section>

<!-- ===== ADMIN HISTORY SECTION ===== -->
<div class="admin-history-container">
    <div class="history-card">
        <h2 class="history-card-title">📋 All User Transactions</h2>

        <!-- Search and Filter Section -->
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
            <button onclick="downloadExcel()" style="padding:0.8rem 1.5rem;background:#00b894;color:white;border:none;border-radius:8px;font-weight:700;cursor:pointer;font-size:0.95rem;">Download Excel</button>
        </div>
<div style="display:flex;justify-content:flex-end;margin-bottom:1rem;">
</div>
<div class="history-filters">            <input type="text" id="searchInput" placeholder="Search by name, email, or order ID..." class="search-input">
            <select id="statusFilter" class="status-filter">
                <option value="">All Actions</option>
                <option value="approve">Approved</option>
                <option value="cancel">Cancelled</option>
                <option value="delete">Deleted</option>
                <option value="revert">Reverted</option>
                <option value="restore">Restored</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>

        <!-- History Table -->
        <div id="historyTableContainer" style="overflow-x: auto;">
            <table class="history-table">
                <thead>
                    <tr>
                        <th>Ref</th>
                        <th>Order ID</th>
                        <th>Customer Name</th>
                        <th>Email</th>
                        <th>Product</th>
                        <th>Action</th>
                        <th>Performed At (Lebanon)</th>
                    </tr>
                </thead>
                <tbody id="historyTableBody">
                    <tr>
                        <td colspan="11" style="text-align: center; padding: 2rem; color: var(--text-muted);">Loading transaction history...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .admin-history-header {
        background: linear-gradient(135deg, rgba(0, 212, 255, 0.1), rgba(0, 100, 200, 0.1));
        padding: 4rem 5%;
        text-align: center;
        min-height: 40vh;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }

    .admin-history-content {
        position: relative;
        z-index: 2;
        max-width: 800px;
        animation: fadeInUp 0.8s ease-out;
    }

    .admin-history-title {
        font-size: 3rem;
        font-weight: 900;
        margin-bottom: 1rem;
        color: #fff;
        background: linear-gradient(135deg, var(--primary-color), #00b894);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .admin-history-subtitle {
        font-size: 1.2rem;
        color: var(--text-muted);
        margin-bottom: 1rem;
        line-height: 1.6;
    }

    .admin-history-container {
        max-width: 1600px;
        margin: 3rem auto;
        padding: 2rem;
    }

    .history-card {
        background: linear-gradient(135deg, rgba(26, 31, 58, 0.9), rgba(10, 14, 39, 1));
        border: 2px solid rgba(0, 212, 255, 0.15);
        border-radius: 15px;
        padding: 2rem;
        box-shadow: 0 8px 32px rgba(0, 212, 255, 0.15);
    }

    .history-card-title {
        font-size: 1.8rem;
        color: var(--text-light);
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid rgba(0, 212, 255, 0.2);
    }

    .history-filters {
        display: flex;
        gap: 1rem;
        margin-bottom: 2rem;
        flex-wrap: wrap;
    }

    .search-input,
    .status-filter {
        padding: 0.8rem 1rem;
        background: rgba(0, 212, 255, 0.05);
        border: 2px solid rgba(0, 212, 255, 0.2);
        border-radius: 8px;
        color: var(--text-main);
        font-size: 0.95rem;
        transition: all 0.3s ease;
        flex: 1;
        min-width: 200px;
    }

    .search-input:focus,
    .status-filter:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 15px rgba(0, 212, 255, 0.3);
    }

    .status-filter {
        flex: 0 1 auto;
        min-width: 150px;
    }

    .history-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.9rem;
    }

    .history-table thead {
        background: rgba(0, 212, 255, 0.1);
        border-bottom: 2px solid var(--primary-color);
    }

    .history-table th {
        padding: 1rem;
        text-align: left;
        color: var(--primary-color);
        font-weight: 600;
        white-space: nowrap;
    }

    .history-table td {
        padding: 1rem;
        border-bottom: 1px solid rgba(0, 212, 255, 0.1);
        color: var(--text-main);
    }

    .history-table tbody tr:hover {
        background: rgba(0, 212, 255, 0.05);
        transition: all 0.3s ease;
    }

    .status-badge {
        display: inline-block;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
    }

    .status-badge.pending {
        background: rgba(255, 193, 7, 0.2);
        color: #ffc107;
        border: 1px solid #ffc107;
    }

    .status-badge.unlocked {
        background: rgba(0, 255, 136, 0.2);
        color: #00ff88;
        border: 1px solid #00ff88;
    }

    .action-btn {
        padding: 0.5rem 1rem;
        background: var(--primary-color);
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 0.85rem;
        transition: all 0.3s ease;
    }

    .action-btn:hover {
        background: #0099ff;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 212, 255, 0.4);
    }

    @media (max-width: 1200px) {
        .admin-history-container {
            padding: 1rem;
        }

        .history-table {
            font-size: 0.8rem;
        }

        .history-table th,
        .history-table td {
            padding: 0.7rem;
        }
    }

    @media (max-width: 768px) {
        .admin-history-title {
            font-size: 1.8rem;
        }

        .admin-history-subtitle {
            font-size: 1rem;
        }

        .history-filters {
            flex-direction: column;
        }

        .search-input,
        .status-filter {
            width: 100%;
            min-width: auto;
        }

        .history-table {
            font-size: 0.75rem;
        }

        .history-table th,
        .history-table td {
            padding: 0.5rem;
        }
    }
</style>
<script>
    let allLogsCache = [];

    async function loadTransactionHistory() {
        const tableBody = document.getElementById('historyTableBody');
        tableBody.innerHTML = `<tr><td colspan="6" style="text-align:center;padding:2rem;color:var(--text-muted);">Loading...</td></tr>`;

        try {
            const response = await fetch('get_admin_logs.php');
            const data = await response.json();

            if (!data.success) {
                tableBody.innerHTML = `<tr><td colspan="6" style="text-align:center;color:red;">Error: ${data.message}</td></tr>`;
                return;
            }

            allLogsCache = data.logs;
            const requestedEmail = new URLSearchParams(window.location.search).get('email');
            if (requestedEmail) {
                document.getElementById('searchInput').value = requestedEmail;
                filterTransactions();
            } else {
                displayTransactions(allLogsCache);
            }

        } catch (err) {
            tableBody.innerHTML = `<tr><td colspan="6" style="text-align:center;color:red;">Failed to load logs.</td></tr>`;
        }
    }

    function displayTransactions(logs) {
        const tableBody = document.getElementById('historyTableBody');

        if (logs.length === 0) {
            tableBody.innerHTML = `<tr><td colspan="6" style="text-align:center;padding:2rem;color:var(--text-muted);">No logs found</td></tr>`;
            return;
        }

        let html = '';
        logs.forEach(log => {
            const performedAt = log.performed_at ?
                new Date(log.performed_at).toLocaleString('en-US', {
                    timeZone: 'Asia/Beirut',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: false
                }) :
                '-';

            let actionBadge;
            if (log.action === 'approve') {
                actionBadge = `<span style="background:rgba(0,255,136,0.2);color:#00ff88;border:1px solid #00ff88;padding:0.4rem 0.8rem;border-radius:20px;font-weight:600;font-size:0.85rem;">✓ Approved</span>`;
            } else if (log.action === 'cancel') {
                actionBadge = `<span style="background:rgba(220,38,38,0.2);color:#dc2626;border:1px solid #dc2626;padding:0.4rem 0.8rem;border-radius:20px;font-weight:600;font-size:0.85rem;">❌ Cancelled</span>`;
            } else if (log.action === 'delete') {
                actionBadge = `<span style="background:rgba(153,27,27,0.2);color:#991b1b;border:1px solid #991b1b;padding:0.4rem 0.8rem;border-radius:20px;font-weight:600;font-size:0.85rem;">🗑 Deleted</span>`;
            } else if (log.action === 'revert') {
                actionBadge = `<span style="background:rgba(245,157,0,0.2);color:#f59d00;border:1px solid #f59d00;padding:0.4rem 0.8rem;border-radius:20px;font-weight:600;font-size:0.85rem;">↩️ Reverted</span>`;
            } else if (log.action === 'restore') {
                actionBadge = `<span style="background:rgba(78,205,196,0.2);color:#4ecdc4;border:1px solid #4ecdc4;padding:0.4rem 0.8rem;border-radius:20px;font-weight:600;font-size:0.85rem;">✓ Restored</span>`;
            } else {
                actionBadge = `<span style="padding:0.4rem 0.8rem;border-radius:20px;font-size:0.85rem;">${log.action}</span>`;
            }

            html += `<tr>
                <td><strong>#${log.order_ref ? log.order_ref.slice(-6) : log.id}</strong></td>
                <td>${log.order_ref || '-'}</td>
                <td>${log.customer_name || '-'}</td>
                <td>${log.customer_email || '-'}</td>
                <td>
                    ${log.product_type === 'ea' ? 'TTR Risk Calculator' : 
                      log.product_type === 'robot' ? 'TTR Robot' : 
                      log.product_type === 'robot_sr' ? 'S&R Precision EA' : 
                      log.product_type === 'robot_ib' ? 'Instant Breakout EA' : 
                      (log.product_type === 'course' ? 'Trading Mastery Course' : '-')}
                </td>
                <td>${actionBadge}</td>
                <td>${performedAt}</td>
            </tr>`;
        });

        tableBody.innerHTML = html;
    }

    function filterTransactions() {
        const searchValue = document.getElementById('searchInput').value.toLowerCase();
        const statusValue = document.getElementById('statusFilter').value;

        const filtered = allLogsCache.filter(log => {
            const matchesSearch = !searchValue ||
                log.customer_name?.toLowerCase().includes(searchValue) ||
                log.customer_email?.toLowerCase().includes(searchValue) ||
                log.order_ref?.toLowerCase().includes(searchValue);
            const matchesStatus = !statusValue || log.action === statusValue;
            return matchesSearch && matchesStatus;
        });

        displayTransactions(filtered);
    }

    function downloadExcel() {
        if (allLogsCache.length === 0) {
            alert('No logs to export');
            return;
        }

        let csv = 'Ref,Order ID,Customer Name,Email,Product,Action,Performed At\n';
        allLogsCache.forEach(log => {
            const performedAt = log.performed_at ?
                new Date(log.performed_at).toLocaleString('en-US', {
                    timeZone: 'Asia/Beirut',
                    hour12: false
                }) :
                '-';
            const pName = log.product_type === 'ea' ? 'TTR Risk Calculator' : 
                          log.product_type === 'robot' ? 'TTR Robot' : 
                          log.product_type === 'robot_sr' ? 'S&R Precision EA' : 
                          log.product_type === 'robot_ib' ? 'Instant Breakout EA' : 
                          (log.product_type === 'course' ? 'Trading Mastery Course' : '-');
            csv += `"#${log.order_ref ? log.order_ref.slice(-6) : log.id}","${log.order_ref}","${log.customer_name}","${log.customer_email}","${pName}","${log.action}","${performedAt}"\n`;
        });

        const blob = new Blob([csv], {
            type: 'text/csv'
        });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `AdminLogs_${new Date().toLocaleDateString()}.csv`;
        a.click();
        URL.revokeObjectURL(url);
    }

    document.getElementById('searchInput').addEventListener('input', filterTransactions);
    document.getElementById('statusFilter').addEventListener('change', filterTransactions);
    document.addEventListener('DOMContentLoaded', loadTransactionHistory);
    setInterval(loadTransactionHistory, 10000);
</script>

<?php include 'footer.php'; ?>
