<?php
include 'header.php';
?>

<style>
    .debug-container {
        max-width: 1200px;
        margin: 30px auto;
        padding: 30px;
        background: rgba(26, 31, 58, 0.6);
        border-radius: 15px;
        border: 1px solid rgba(0, 212, 255, 0.2);
    }

    .debug-section {
        margin-bottom: 30px;
        padding: 20px;
        background: rgba(10, 14, 39, 0.8);
        border-radius: 10px;
        border-left: 4px solid var(--primary-color);
    }

    .debug-section h3 {
        color: var(--primary-color);
        margin-top: 0;
    }

    .storage-item {
        background: rgba(0, 0, 0, 0.3);
        padding: 15px;
        margin: 10px 0;
        border-radius: 8px;
        font-family: monospace;
        word-break: break-all;
        color: #00b894;
    }

    .storage-key {
        color: #00d4ff;
        font-weight: bold;
        margin-bottom: 8px;
    }

    .btn-debug {
        background: #ff3e3e;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        cursor: pointer;
        margin: 10px 5px 10px 0;
        font-weight: 600;
    }

    .btn-debug:hover {
        background: #ff5555;
    }

    .btn-clear {
        background: #ff6b6b;
    }

    .btn-clear:hover {
        background: #ff8888;
    }
</style>

<div class="debug-container">
    <h1 style="color: var(--primary-color); margin-top: 0;">🔧 Storage Debug Panel</h1>

    <div class="debug-section">
        <h3>Session Storage (Tab-Specific)</h3>
        <p style="color: var(--text-muted); font-size: 0.9rem;">These are stored per-tab and cleared when you close the browser tab.</p>
        <div id="sessionStorageDebug"></div>
    </div>

    <div class="debug-section">
        <h3>Local Storage (Shared Across All Tabs)</h3>
        <p style="color: var(--text-muted); font-size: 0.9rem;">These are shared across all tabs and persist until cleared.</p>
        <div id="localStorageDebug"></div>
    </div>

    <div class="debug-section">
        <h3>Controls</h3>
        <button class="btn-debug" onclick="refreshDebug()">🔄 Refresh Debug Info</button>
        <button class="btn-debug btn-clear" onclick="clearAllStorage()">🗑️ Clear All Storage</button>
        <button class="btn-debug" style="background: #4ecdc4;" onclick="location.reload()">↻ Reload Page</button>
    </div>
</div>

<script>
    function formatJson(obj) {
        return JSON.stringify(obj, null, 2);
    }

    function refreshDebug() {
        // Session Storage
        const sessionDebug = document.getElementById('sessionStorageDebug');
        let sessionHtml = '';
        if (sessionStorage.length === 0) {
            sessionHtml = '<div class="storage-item"><span style="color: #888;">Empty</span></div>';
        } else {
            for (let i = 0; i < sessionStorage.length; i++) {
                const key = sessionStorage.key(i);
                const value = sessionStorage.getItem(key);
                sessionHtml += `<div class="storage-item">
                    <div class="storage-key">🔑 ${key}</div>
                    <div>${value}</div>
                </div>`;
            }
        }
        sessionDebug.innerHTML = sessionHtml;

        // Local Storage
        const localDebug = document.getElementById('localStorageDebug');
        let localHtml = '';
        if (localStorage.length === 0) {
            localHtml = '<div class="storage-item"><span style="color: #888;">Empty</span></div>';
        } else {
            for (let i = 0; i < localStorage.length; i++) {
                const key = localStorage.key(i);
                const value = localStorage.getItem(key);
                let displayValue = value;

                // Try to parse JSON for better display
                try {
                    const parsed = JSON.parse(value);
                    if (Array.isArray(parsed)) {
                        displayValue = `Array with ${parsed.length} items<br><pre>${formatJson(parsed)}</pre>`;
                    } else if (typeof parsed === 'object') {
                        displayValue = `<pre>${formatJson(parsed)}</pre>`;
                    }
                } catch (e) {
                    // Not JSON, display as is
                }

                localHtml += `<div class="storage-item">
                    <div class="storage-key">🔑 ${key}</div>
                    <div>${displayValue}</div>
                </div>`;
            }
        }
        localDebug.innerHTML = localHtml;

        console.log('Debug refresh complete');
        console.log('Session Storage:', sessionStorage);
        console.log('Local Storage:', localStorage);
    }

    function clearAllStorage() {
        if (confirm('Are you sure you want to clear ALL storage? This will delete all orders and cart items!')) {
            sessionStorage.clear();
            localStorage.clear();
            alert('All storage cleared!');
            refreshDebug();
        }
    }

    // Initial load
    refreshDebug();

    // Auto-refresh every 2 seconds
    setInterval(refreshDebug, 2000);
</script>

<?php include 'footer.php'; ?>
