<?php
// admin_dashboard.php
require_once __DIR__ . '/config.php';
if (empty($_SESSION['is_admin']) && empty($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit;
}
include 'header.php';
?>
<style>
    .admin-layout {
        display: grid;
        grid-template-columns: 280px 1fr;
        gap: 2.5rem;
        min-height: calc(100vh - 180px);
        margin-bottom: 4rem;
        animation: fadeInUp 0.6s ease-out;
    }

    .admin-sidebar {
        background: linear-gradient(145deg, rgba(26, 31, 58, 0.8), rgba(10, 14, 39, 0.9));
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border-radius: 20px;
        padding: 2.5rem 1.5rem;
        border: 1px solid rgba(0, 212, 255, 0.15);
        height: fit-content;
        position: sticky;
        top: 130px;
        box-shadow:
            0 15px 50px rgba(0, 0, 0, 0.4),
            0 0 0 1px rgba(255, 255, 255, 0.05) inset;
        animation: slideInLeft 0.6s ease-out;
    }

    .admin-sidebar h3 {
        color: var(--primary-color);
        margin-bottom: 2rem;
        padding-left: 1rem;
        font-size: 1.2rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: 800;
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
        color: var(--text-muted);
        text-decoration: none;
        padding: 1rem 1.2rem;
        border-radius: 12px;
        display: flex;
        align-items: center;
        gap: 12px;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        font-weight: 600;
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
        background: linear-gradient(135deg, rgba(0, 212, 255, 0.15), rgba(0, 100, 200, 0.15));
        color: var(--primary-color);
        border: 1px solid rgba(0, 212, 255, 0.2);
        box-shadow: 0 4px 20px rgba(0, 212, 255, 0.15);
    }

    .admin-content {
        background: linear-gradient(145deg, rgba(26, 31, 58, 0.6), rgba(10, 14, 39, 0.7));
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border-radius: 24px;
        padding: 3rem;
        border: 1px solid rgba(255, 255, 255, 0.05);
        box-shadow:
            0 20px 50px rgba(0, 0, 0, 0.3),
            0 0 0 1px rgba(255, 255, 255, 0.03) inset;
        animation: slideInRight 0.6s ease-out;
        position: relative;
        overflow: hidden;
    }

    .admin-content::before {
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

    .admin-title {
        font-size: 2.5rem;
        font-weight: 800;
        background: linear-gradient(135deg, var(--primary-color), #00b894, #00a8cc);
        background-size: 200% 200%;
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 2.5rem;
        letter-spacing: -1px;
        animation: gradientShift 4s ease infinite;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 2rem;
        margin-bottom: 3rem;
    }

    .stat-card {
        background: linear-gradient(145deg, rgba(26, 31, 58, 0.8), rgba(10, 14, 39, 0.9));
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 20px;
        padding: 2rem;
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        animation: fadeInUp 0.6s ease-out backwards;
    }

    .stat-card:nth-child(1) {
        animation-delay: 0.1s;
    }

    .stat-card:nth-child(2) {
        animation-delay: 0.2s;
    }

    .stat-card:nth-child(3) {
        animation-delay: 0.3s;
    }

    .stat-card:hover {
        transform: translateY(-8px) scale(1.02);
        border-color: rgba(0, 212, 255, 0.4);
        box-shadow:
            0 20px 50px rgba(0, 0, 0, 0.5),
            0 0 30px rgba(0, 212, 255, 0.15);
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: radial-gradient(circle at top right, rgba(0, 212, 255, 0.1), transparent);
        pointer-events: none;
        transition: opacity 0.4s ease;
    }

    .stat-card:hover::before {
        opacity: 1.5;
    }

    .stat-card::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--primary-color), #00b894);
        transform: scaleX(0);
        transition: transform 0.4s ease;
    }

    .stat-card:hover::after {
        transform: scaleX(1);
    }

    .stat-label {
        color: var(--text-muted);
        font-size: 0.95rem;
        font-weight: 600;
        margin-bottom: 1rem;
        display: block;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-value {
        font-size: 3rem;
        font-weight: 800;
        display: block;
        transition: transform 0.3s ease;
    }

    .stat-card:hover .stat-value {
        transform: scale(1.05);
    }

    .orders-table-container {
        background: linear-gradient(145deg, rgba(10, 14, 39, 0.5), rgba(26, 31, 58, 0.3));
        border-radius: 20px;
        border: 1px solid rgba(0, 212, 255, 0.1);
        overflow-x: auto;
        overflow-y: visible;
        animation: fadeInUp 0.6s ease-out;
        width: 100%;
        display: block !important;
        visibility: visible !important;
        padding: 1rem;
    }

    .orders-table {
        width: 100%;
        border-collapse: collapse;
        display: table;
        visibility: visible;
        min-width: 1200px;
    }

    .orders-table th {
        padding: 0.8rem 1rem;
        text-align: left;
        color: var(--primary-color);
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 1px;
        background: rgba(26, 31, 58, 0.8);
        border-bottom: 2px solid rgba(0, 212, 255, 0.3);
        white-space: nowrap;
        font-weight: 700;
    }

    .orders-table th:nth-child(1) {
        width: 120px;
    }

    .orders-table th:nth-child(2) {
        width: 130px;
    }

    .orders-table th:nth-child(3) {
        width: 130px;
    }

    .orders-table th:nth-child(4) {
        width: 160px;
    }

    .orders-table th:nth-child(5) {
        width: 200px;
    }

    .orders-table th:nth-child(6) {
        width: 160px;
    }

    .orders-table th:nth-child(7) {
        width: 100px;
    }

    .orders-table th:nth-child(8) {
        width: 110px;
    }

    .orders-table th:nth-child(9) {
        width: 100px;
    }

    .orders-table td {
        padding: 0.8rem 1rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.07);
        font-weight: 500;
        font-size: 0.85rem;
        color: var(--text-main);
        vertical-align: top;
    }

    .orders-table td:nth-child(1) {
        width: 120px;
    }

    .orders-table td:nth-child(2) {
        width: 130px;
    }

    .orders-table td:nth-child(3) {
        width: 130px;
    }

    .orders-table td:nth-child(4) {
        width: 160px;
    }

    .orders-table td:nth-child(5) {
        width: 200px;
        white-space: normal;
    }

    .orders-table td:nth-child(6) {
        width: 160px;
        white-space: normal;
    }

    .orders-table td:nth-child(7) {
        width: 100px;
    }

    .orders-table td:nth-child(8) {
        width: 110px;
    }

    .orders-table td:nth-child(9) {
        width: 100px;
    }

    .orders-table tr:hover {
        background: rgba(0, 212, 255, 0.03);
    }

    .unlock-btn {
        background: linear-gradient(135deg, var(--primary-color), #00a8cc);
        color: #000;
        padding: 0.5rem 1rem;
        border: none;
        border-radius: 8px;
        font-weight: 700;
        cursor: pointer;
        transition: var(--transition);
        font-size: 0.8rem;
    }

    .unlock-btn:hover:not(:disabled) {
        transform: scale(1.05);
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.4);
    }

    .unlock-btn:disabled {
        background: rgba(255, 255, 255, 0.1);
        color: var(--text-muted);
        cursor: not-allowed;
        opacity: 0.6;
    }

    /* Status Badge Styles */
    .status-badge {
        display: inline-block;
        padding: 0.4rem 0.7rem;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.75rem;
        white-space: nowrap;
    }

    .status-pending {
        background: rgba(245, 157, 0, 0.2);
        color: #f59d00;
        border: 1px solid #f59d00;
    }

    .status-unlocked {
        background: rgba(0, 255, 136, 0.2);
        color: #00b894;
        border: 1px solid #00b894;
    }

    .status-cancelled {
        background: rgba(220, 38, 38, 0.2);
        color: #dc2626;
        border: 1px solid #dc2626;
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: var(--text-muted);
        background: linear-gradient(135deg, rgba(0, 212, 255, 0.05), rgba(0, 100, 200, 0.05));
        border-radius: 16px;
        border: 2px dashed rgba(0, 212, 255, 0.2);
        font-size: 1.1rem;
        animation: fadeInUp 0.5s ease-out;
    }

    #ordersContainer {
        width: 100%;
        display: block !important;
        visibility: visible !important;
    }

    .delete-btn {
        background-color: #dc2626 !important;
        color: white !important;
        padding: 0.5rem 1rem !important;
        border: none !important;
        border-radius: 8px !important;
        cursor: pointer !important;
        font-weight: 600 !important;
        font-size: 0.85rem !important;
        transition: all 0.3s ease !important;
    }

    .delete-btn:hover {
        background-color: #b91c1c !important;
        transform: scale(1.05) !important;
        box-shadow: 0 5px 15px rgba(220, 38, 38, 0.3) !important;
    }

    .section-content {
        display: none;
    }

    .section-content {
        animation: fadeInUp 0.5s ease-out;
    }

    .section-content[style*="display: block"] {
        display: block !important;
    }

    @media (max-width: 992px) {
        .admin-layout {
            grid-template-columns: 1fr;
        }
    }
</style>

<style>
    /* Refined admin dashboard presentation */
    .admin-layout {
        width: min(1500px, 100%);
        margin: 0 auto 3rem;
        grid-template-columns: 250px minmax(0, 1fr);
        gap: 1.25rem;
        align-items: start;
    }
    .admin-sidebar {
        top: 100px;
        padding: 1.25rem;
        border-radius: 18px;
        background: linear-gradient(160deg, rgba(18,28,54,.94), rgba(7,12,30,.96));
        border: 1px solid rgba(0,212,255,.18);
        box-shadow: 0 18px 45px rgba(0,0,0,.26);
        overflow: hidden;
    }
    .admin-sidebar::before {
        content: '';
        display: block;
        width: 48px;
        height: 4px;
        margin: 0 0 1rem;
        border-radius: 999px;
        background: linear-gradient(90deg, var(--primary-color), #00b894);
        box-shadow: 0 0 18px rgba(0,212,255,.35);
    }
    .admin-sidebar h3 { margin: 0 0 1.2rem; padding: 0; font-size: .85rem; letter-spacing: .13em; }
    .sidebar-menu { display: grid; gap: .45rem; }
    .sidebar-menu li { margin: 0; }
    .sidebar-menu a {
        min-height: 48px;
        padding: .75rem .85rem;
        border-radius: 11px;
        font-size: .88rem;
        border: 1px solid transparent;
    }
    .sidebar-menu a:hover { transform: translateX(4px); background: rgba(0,212,255,.07); }
    .sidebar-menu a.active {
        background: linear-gradient(135deg, rgba(0,212,255,.17), rgba(0,184,148,.08));
        border-color: rgba(0,212,255,.32);
        box-shadow: inset 3px 0 0 var(--primary-color), 0 8px 22px rgba(0,212,255,.08);
    }
    .admin-content {
        min-width: 0;
        padding: clamp(1.25rem, 2.4vw, 2.25rem);
        border-radius: 20px;
        background: linear-gradient(150deg, rgba(18,28,54,.75), rgba(7,12,30,.82));
        border: 1px solid rgba(0,212,255,.12);
        box-shadow: 0 20px 55px rgba(0,0,0,.24);
    }
    .admin-title {
        width: fit-content;
        margin: 0 0 1.5rem;
        font-size: clamp(1.55rem, 3vw, 2.15rem);
        letter-spacing: -.025em;
    }
    .admin-title::after {
        content: '';
        display: block;
        width: 55%;
        height: 3px;
        margin-top: .55rem;
        border-radius: 999px;
        background: linear-gradient(90deg, var(--primary-color), transparent);
    }
    .stats-grid {
        grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }
    .stat-card {
        min-height: 145px;
        padding: 1.25rem;
        border-radius: 16px;
        background: linear-gradient(145deg, rgba(255,255,255,.055), rgba(0,212,255,.025));
        border: 1px solid rgba(255,255,255,.075);
        box-shadow: 0 10px 28px rgba(0,0,0,.16);
    }
    .stat-card:hover { transform: translateY(-4px); }
    .stat-label { margin-bottom: .7rem; font-size: .72rem; letter-spacing: .09em; line-height: 1.45; }
    .stat-value, .stat-card .stat-value[style] { font-size: clamp(2rem, 4vw, 2.5rem) !important; line-height: 1.1; }
    .orders-table-container {
        padding: .7rem;
        border-radius: 16px;
        border: 1px solid rgba(0,212,255,.14);
        background: rgba(5,10,25,.42);
        box-shadow: inset 0 1px 0 rgba(255,255,255,.035);
        scrollbar-color: var(--primary-color) rgba(255,255,255,.04);
        scrollbar-width: thin;
    }
    .orders-table { border-collapse: separate; border-spacing: 0; }
    .orders-table th {
        position: sticky;
        top: 0;
        z-index: 2;
        padding: .85rem .8rem;
        background: #111a34;
        font-size: .7rem;
        letter-spacing: .08em;
    }
    .orders-table th:first-child { border-radius: 10px 0 0 10px; }
    .orders-table th:last-child { border-radius: 0 10px 10px 0; }
    .orders-table td { padding: .85rem .8rem; font-size: .79rem; line-height: 1.45; }
    .orders-table tbody tr:nth-child(even) { background: rgba(255,255,255,.018); }
    .orders-table tbody tr:hover { background: rgba(0,212,255,.065); }

    /* Show every order field without horizontal scrolling on laptop. */
    .orders-table-container { overflow-x: visible; }
    .orders-table {
        width: 100%;
        min-width: 0;
        table-layout: fixed;
    }
    .orders-table th,
    .orders-table td {
        width: auto !important;
        white-space: normal !important;
        overflow-wrap: anywhere;
        word-break: normal;
    }
    .orders-table th { font-size: .78rem; padding: .85rem .55rem; }
    .orders-table td { font-size: .88rem; padding: .9rem .55rem; }
    .orders-table th:nth-child(1), .orders-table td:nth-child(1) { width: 8% !important; }
    .orders-table th:nth-child(2), .orders-table td:nth-child(2) { width: 10% !important; }
    .orders-table th:nth-child(3), .orders-table td:nth-child(3) { width: 9% !important; }
    .orders-table th:nth-child(4), .orders-table td:nth-child(4) { width: 15% !important; }
    .orders-table th:nth-child(5), .orders-table td:nth-child(5) { width: 13% !important; }
    .orders-table th:nth-child(6), .orders-table td:nth-child(6) { width: 13% !important; }
    .orders-table th:nth-child(7), .orders-table td:nth-child(7) { width: 8% !important; }
    .orders-table th:nth-child(8), .orders-table td:nth-child(8) { width: 8% !important; }
    .orders-table th:nth-child(9), .orders-table td:nth-child(9) { width: 16% !important; }
    .orders-table td:nth-child(9) button { width: 100%; display: block; margin: .3rem 0 !important; }

    @media (min-width: 761px) {
        .orders-table-container { padding: 0; border: 0; background: transparent; }
        .orders-table,
        .orders-table tbody { display: block; width: 100%; }
        .orders-table thead { display: none; }
        .orders-table tbody { display: grid; gap: 1rem; }
        .orders-table tr {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0;
            padding: .75rem;
            border: 1px solid rgba(0,212,255,.16);
            border-radius: 15px;
            background: linear-gradient(145deg, rgba(255,255,255,.045), rgba(0,212,255,.025));
            box-shadow: 0 12px 30px rgba(0,0,0,.16);
        }
        .orders-table td,
        .orders-table td:nth-child(n) {
            display: flex;
            flex-direction: column;
            gap: .4rem;
            width: auto !important;
            min-width: 0;
            padding: .85rem 1rem;
            border-right: 1px solid rgba(255,255,255,.06);
            border-bottom: 1px solid rgba(255,255,255,.06);
            font-size: .94rem;
            line-height: 1.5;
        }
        .orders-table td:nth-child(3n) { border-right: 0; }
        .orders-table td:nth-child(7),
        .orders-table td:nth-child(8) { border-bottom: 0; }
        .orders-table td::before {
            color: var(--primary-color);
            font-size: .68rem;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
        }
        .orders-table td:nth-child(1)::before { content: 'Ref ID'; }
        .orders-table td:nth-child(2)::before { content: 'Full Name'; }
        .orders-table td:nth-child(3)::before { content: 'Phone'; }
        .orders-table td:nth-child(4)::before { content: 'Email'; }
        .orders-table td:nth-child(5)::before { content: 'Products'; }
        .orders-table td:nth-child(6)::before { content: 'Created Date / Time'; }
        .orders-table td:nth-child(7)::before { content: 'Total'; }
        .orders-table td:nth-child(8)::before { content: 'Status'; }
        .orders-table td:nth-child(9)::before { content: 'Actions'; grid-column: 1 / -1; }
        .orders-table td:nth-child(9) {
            grid-column: 1 / -1;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: .6rem;
            border: 0;
            border-top: 1px solid rgba(0,212,255,.1);
            margin-top: .2rem;
            padding-top: 1rem;
        }
        .orders-table td:nth-child(9) button {
            width: 100%;
            min-height: 40px;
            margin: 0 !important;
            font-size: .8rem !important;
        }
    }
    .status-badge { padding: .38rem .65rem; border-radius: 999px; font-size: .68rem; }
    .unlock-btn, .delete-btn {
        min-height: 34px;
        padding: .45rem .7rem !important;
        border-radius: 8px !important;
        font-size: .72rem !important;
        margin: .15rem !important;
    }

    .orders-table button[onclick*="'revert_item'"] {
        background: linear-gradient(135deg, #f59e0b, #d97706) !important;
        color: #1b1202 !important;
        border: 1px solid rgba(255,193,7,.75) !important;
        box-shadow: 0 5px 14px rgba(245,158,11,.2) !important;
    }

    .orders-table button[onclick*="'cancel_item'"] {
        background: rgba(239,68,68,.1) !important;
        color: #ff8585 !important;
        border: 1px solid rgba(239,68,68,.48) !important;
        box-shadow: none !important;
    }

    .orders-table button[onclick*="'delete_item'"] {
        background: linear-gradient(135deg, #991b1b, #5f1010) !important;
        color: #fff !important;
        border: 1px solid rgba(255,100,100,.35) !important;
        box-shadow: 0 5px 14px rgba(127,29,29,.25) !important;
    }

    .orders-table button[onclick*="'revert_item'"],
    .orders-table button[onclick*="'cancel_item'"],
    .orders-table button[onclick*="'delete_item'"] {
        min-width: 72px;
        font-weight: 750 !important;
        letter-spacing: .01em;
        transition: transform .2s ease, box-shadow .2s ease, filter .2s ease !important;
    }

    .orders-table button[onclick*="'revert_item'"]:hover,
    .orders-table button[onclick*="'cancel_item'"]:hover,
    .orders-table button[onclick*="'delete_item'"]:hover {
        transform: translateY(-2px) !important;
        filter: brightness(1.12);
    }
    .empty-state { padding: 3rem 1.25rem; border-radius: 14px; }
    #settingsSection > div {
        background: linear-gradient(145deg, rgba(255,255,255,.045), rgba(0,212,255,.025)) !important;
        border-color: rgba(0,212,255,.17) !important;
        box-shadow: 0 12px 35px rgba(0,0,0,.14);
    }
    @media (max-width: 992px) {
        .admin-layout { grid-template-columns: 1fr; gap: 1rem; }
        .admin-sidebar { position: static; padding: .9rem; }
        .admin-sidebar::before { display: none; }
        .admin-sidebar h3 { margin-bottom: .75rem; text-align: center; }
        .sidebar-menu {
            display: flex;
            gap: .55rem;
            overflow-x: auto;
            padding-bottom: .25rem;
            scroll-snap-type: x proximity;
        }
        .sidebar-menu li { flex: 0 0 auto; scroll-snap-align: start; }
        .sidebar-menu a { white-space: nowrap; min-height: 42px; padding: .65rem .8rem; }
        .sidebar-menu a.active { box-shadow: inset 0 -3px 0 var(--primary-color); }
    }
    @media (max-width: 600px) {
        .section:has(.admin-layout) { padding: 1rem .65rem; }
        .admin-content { padding: 1rem .7rem; border-radius: 15px; }
        .admin-title { font-size: 1.45rem; margin-bottom: 1.15rem; }
        .stats-grid { grid-template-columns: 1fr 1fr; gap: .65rem; }
        .stat-card { min-height: 120px; padding: .9rem; }
        .stat-label { font-size: .63rem; }
        .stat-value, .stat-card .stat-value[style] { font-size: 1.75rem !important; }
        .orders-table-container { padding: .4rem; border-radius: 12px; }
        .orders-table td { padding: .7rem .65rem; }
        #settingsSection > div { padding: 1.25rem !important; }
    }

    @media (max-width: 760px) {
        .orders-table-container { padding: 0; border: 0; background: transparent; overflow: visible; }
        .orders-table, .orders-table tbody { display: block; width: 100%; }
        .orders-table thead { display: none; }
        .orders-table tr {
            display: block;
            width: 100%;
            margin-bottom: 1rem;
            padding: .6rem;
            border: 1px solid rgba(0,212,255,.16);
            border-radius: 14px;
            background: linear-gradient(145deg, rgba(255,255,255,.045), rgba(0,212,255,.025));
            box-shadow: 0 10px 25px rgba(0,0,0,.16);
        }
        .orders-table td,
        .orders-table td:nth-child(n) {
            display: grid;
            grid-template-columns: 112px minmax(0, 1fr);
            gap: .75rem;
            align-items: start;
            width: 100% !important;
            padding: .65rem .55rem;
            border-bottom: 1px solid rgba(255,255,255,.065);
            font-size: .92rem;
            text-align: left;
        }
        .orders-table td:last-child { border-bottom: 0; }
        .orders-table td::before {
            color: var(--primary-color);
            font-size: .7rem;
            font-weight: 800;
            letter-spacing: .06em;
            text-transform: uppercase;
        }
        .orders-table td:nth-child(1)::before { content: 'Ref ID'; }
        .orders-table td:nth-child(2)::before { content: 'Full Name'; }
        .orders-table td:nth-child(3)::before { content: 'Phone'; }
        .orders-table td:nth-child(4)::before { content: 'Email'; }
        .orders-table td:nth-child(5)::before { content: 'Products'; }
        .orders-table td:nth-child(6)::before { content: 'Created'; }
        .orders-table td:nth-child(7)::before { content: 'Total'; }
        .orders-table td:nth-child(8)::before { content: 'Status'; }
        .orders-table td:nth-child(9)::before { content: 'Action'; }
        .orders-table td:nth-child(9) button { width: 100%; margin: .25rem 0 !important; font-size: .8rem !important; }
    }
    @media (max-width: 390px) {
        .stats-grid { grid-template-columns: 1fr; }
    }
</style>

<!-- ===== ADMIN DASHBOARD ===== -->
<section class="section">
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <h3 data-i18n="adminCenter">📊 Admin Center</h3>
            <ul class="sidebar-menu">
                <li><a class="menu-link active" onclick="showSection('orders')" data-i18n="customerOrders">📋 Customer Orders</a></li>
                <li><a class="menu-link" onclick="showSection('stats')" data-i18n="marketInsights">📈 Market Insights</a></li>
                <li><a class="menu-link" onclick="showSection('settings')" data-i18n="portalSettings">⚙️ Portal Settings</a></li>
                <li><a class="menu-link" href="admin_history.php" data-i18n="transactionHistory">📊 Transaction History</a></li>
            </ul>
        </aside>

        <!-- Content -->
        <main class="admin-content">
            <!-- Orders Section -->
            <div id="ordersSection" class="section-content" style="display: block;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                    <h2 class="admin-title" data-i18n="ordersManagement">Orders Management</h2>

                </div>

                <div id="ordersContainer">
                    <div class="empty-state" data-i18n="processingData">Processing historical data...</div>
                </div>
            </div>

            <!-- Stats Section -->
            <div id="statsSection" class="section-content" style="display: none;">
                <h2 class="admin-title" data-i18n="businessAnalytics">Business Analytics</h2>

                <!-- Market Insights Section -->
                <div style="margin-bottom: 3rem;">
                    <h3 style="color: var(--primary-color); font-size: 1.3rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                        📈 Market Insights
                    </h3>
                    <div class="stats-grid">
                        <div class="stat-card">
                            <span class="stat-label">Total Approved Transactions</span>
                            <h3 id="approvedTransactions" class="stat-value" style="color: #00ff88; font-size: 2.5rem;">0</h3>
                        </div>
                        <div class="stat-card">
                            <span class="stat-label">Revenue Generated</span>
                            <h3 id="approvedRevenue" class="stat-value" style="color: #00ff88; font-size: 2.5rem;">$0</h3>
                        </div>
                    </div>
                </div>

                <!-- Overall Stats -->
                <div>
                    <h3 style="color: var(--primary-color); font-size: 1.3rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                        📊 Overall Statistics
                    </h3>
                    <div class="stats-grid">
                        <div class="stat-card">
                            <span class="stat-label" data-i18n="totalTransactions">Total Transactions</span>
                            <h3 id="totalOrders" class="stat-value" style="color: var(--primary-color);">0</h3>
                        </div>
                        <div class="stat-card">
                            <span class="stat-label" data-i18n="pendingApproval">Pending Approval</span>
                            <h3 id="pendingOrders" class="stat-value" style="color: #f59d00;">0</h3>
                        </div>
                        <div class="stat-card">
                            <span class="stat-label">Cancelled Orders</span>
                            <h3 id="cancelledOrders" class="stat-value" style="color: #dc2626;">0</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings Section -->
            <div id="settingsSection" class="section-content" style="display: none;">
                <h2 class="admin-title" data-i18n="securityAccess">Security & Access</h2>
                <div style="background: rgba(255, 255, 255, 0.03); border-radius: 16px; padding: 2.5rem; border: 1px solid rgba(0, 212, 255, 0.1);">
                    
                    <div style="padding: 1.5rem; border-radius: 10px; background: rgba(0, 212, 255, 0.05); margin-bottom: 2rem;">
                        <code style="color: var(--primary-color);">ID: admin_root_001</code>
                    </div>
                    <button class="btn btn-primary glow-btn" onclick="adminLogout()" data-i18n="terminateSession">Terminate Session</button>
                </div>
            </div>
        </main>
    </div>
</section>

<div id="automationModal" class="automation-modal" hidden>
    <div class="automation-modal-card" role="dialog" aria-modal="true" aria-labelledby="automationModalTitle">
        <button type="button" class="automation-close" onclick="closeAutomationPanel()" aria-label="Close">&times;</button>
        <h2 id="automationModalTitle">Customer Conversation Review</h2>
        <div id="automationMessages" class="automation-messages"></div>
    </div>
</div>

<style>
    .automation-modal { position: fixed; inset: 0; z-index: 10000; padding: 4vh 1rem; background: rgba(3,8,24,.82); overflow-y: auto; }
    .automation-modal[hidden] { display: none; }
    .automation-modal-card { position: relative; width: min(860px, 100%); margin: auto; padding: 1.5rem; border: 1px solid rgba(0,212,255,.28); border-radius: 18px; background: #0d1730; box-shadow: 0 24px 80px rgba(0,0,0,.55); }
    .automation-modal-card h2 { margin: 0 2.5rem 1rem 0; color: var(--primary-color); }
    .automation-close { position: absolute; right: 1rem; top: .8rem; border: 0; background: transparent; color: #fff; font-size: 2rem; cursor: pointer; }
    .automation-messages { display: grid; gap: .9rem; }
    .automation-message { padding: 1rem; border: 1px solid rgba(255,255,255,.1); border-radius: 12px; background: rgba(255,255,255,.035); }
    .automation-message.incoming { border-color: rgba(245,157,0,.35); }
    .automation-message-meta { margin-bottom: .65rem; color: #8da2c8; font-size: .8rem; }
    .automation-message h4 { margin: .75rem 0 .35rem; color: #78e8ff; }
    .automation-message pre { margin: 0; color: #eef4ff; white-space: pre-wrap; word-break: break-word; font: inherit; line-height: 1.55; }
    .automation-proof-links { display: flex; flex-wrap: wrap; gap: .5rem; margin-top: .75rem; }
    .automation-proof-links a { padding: .45rem .7rem; border-radius: 8px; background: rgba(0,212,255,.12); color: #78e8ff; text-decoration: none; }
    @media (max-width: 600px) { .automation-review-summary { grid-template-columns: 1fr; } .automation-modal-card { padding: 1rem; } }
</style>

<script>
    function checkAdminAuth() {
        // No auth check - admin access is unrestricted
    }
    // ===== LOAD ORDERS FROM DATABASE =====
    async function loadOrders() {
        const ordersContainer = document.getElementById('ordersContainer');

        try {
            const response = await fetch('get_orders.php?refresh=' + Date.now(), {
                cache: 'no-store'
            });
            const data = await response.json();

            if (!data.success) {
                ordersContainer.innerHTML = '<div class="empty-state">Error loading orders: ' + data.message + '</div>';
                return;
            }

            const orders = data.orders;

            if (orders.length === 0) {
                ordersContainer.innerHTML = '<div class="empty-state" data-i18n="noTransactionHistory">No transaction history found.</div>';
                applyLanguage();
                return;
            }

            let html = `
                <div class="orders-table-container">
                    <table class="orders-table">
                        <thead>
                            <tr>
                                <th data-i18n="refID">Ref ID</th>
                                <th data-i18n="fullName">Full Name</th>
                                <th data-i18n="phoneNumber">Phone</th>
                                <th data-i18n="email">Email</th>
                                <th>Products</th>
                                <th data-i18n="createdDate">Created Date/Time</th>
                                <th data-i18n="total">Total</th>
                                <th data-i18n="status">Status</th>
                                <th data-i18n="action">Action</th>
                            </tr>
                        </thead>
                        <tbody>`;

            orders.forEach(order => {
                let statusClass, statusText;
                if (order.status === 'cancelled') {
                    statusClass = 'status-cancelled';
                    statusText = t('cancelled');
                } else if (order.status === 'unlocked') {
                    statusClass = 'status-unlocked';
                    statusText = t('active');
                } else {
                    statusClass = 'status-pending';
                    statusText = t('pending');
                }

                let actionBtn;
                const dbId = order.db_id || '';
                const orderId = order.id || '';
                const itemId = order.item_id || '';

                if (order.status === 'cancelled') {
                    actionBtn = `<button class="unlock-btn" onclick="updateOrder('restore_item','${orderId}',${dbId},${itemId})" style="background-color:#4ecdc4;cursor:pointer;">✓ ${t('restore')}</button>`;
                } else if (order.status === 'unlocked') {
                    actionBtn = `<button class="unlock-btn" onclick="updateOrder('revert_item','${orderId}',${dbId},${itemId})" style="background-color:#f59d00;cursor:pointer;">↩️ ${t('revert')}</button>`;
                } else {
                    actionBtn = `<button class="unlock-btn" onclick="updateOrder('approve_item','${orderId}',${dbId},${itemId})" style="background-color:#00b894;cursor:pointer;font-weight:bold;padding:0.45rem 0.85rem;font-size:0.85rem;box-shadow:0 0 10px rgba(0,184,148,0.4);">✓ Approve</button>
                                 <button class="unlock-btn" onclick="updateOrder('cancel_item','${orderId}',${dbId},${itemId})" style="background-color:#ff6b6b;cursor:pointer;margin-left:0.3rem;">✕ Reject</button>`;
                }

                const createdDisplay = order.createdTime ? new Date(order.createdTime).toLocaleString('en-US', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                }) : (order.createdAt || '');
                html += `<tr data-timestamp="${order.createdTime || ''}">
                    <td><strong>#${orderId.slice(-6)}</strong></td>
                    <td>${order.name}</td>
                    <td>${order.phone}</td>
                    <td>${order.email}</td>
                    <td>
                        ${order.product_type === 'ea' ? 'TTR Risk Calculator' :
                          order.product_type === 'robot' ? 'TTR Robot' :
                          order.product_type === 'robot_sr' ? 'S&R Precision EA' :
                          order.product_type === 'robot_ib' ? 'Instant Breakout EA' :
                          order.product_type === 'indicator' ? 'The Holly Grail Indicator' :
                          'Trading Mastery Course'}
                    </td>
                    <td>${createdDisplay}</td>
                    <td title="Includes 1% processing fee">$${parseFloat(order.total).toFixed(2)} USD</td>
                    <td><span class="status-badge ${statusClass}">${statusText}</span></td>
                    <td>
                        ${actionBtn}
                        <button class="unlock-btn" onclick="updateOrder('delete_item','${orderId}',${dbId},${itemId})" style="margin-left:0.3rem;background:#e63946;cursor:pointer;" title="Permanently Delete Order">🗑️ Delete</button>
                        <button class="unlock-btn" onclick="downloadCustomerExcel('${encodeURIComponent(order.email)}')" style="margin-left:0.3rem;background:#087f5b;">Excel</button>
                        <button class="unlock-btn" onclick="openCustomerHistory('${encodeURIComponent(order.email)}')" style="margin-left:0.3rem;background:#364fc7;">History</button>
                        <button class="unlock-btn" onclick="openAutomationPanel(${dbId})" style="margin-left:0.3rem;background:#0b7285;">Conversation</button>
                    </td>
                </tr>`;
            });

            html += '</tbody></table></div>';
            ordersContainer.innerHTML = html;
            applyLanguage();
            updateStats(orders);

        } catch (err) {
            console.error('Error loading orders:', err);
            ordersContainer.innerHTML = '<div class="empty-state">Failed to load orders. Check console for details.</div>';
        }
    }

    function downloadCustomerExcel(encodedEmail) {
        window.location.href = 'export_customer_history.php?email=' + encodedEmail;
    }

    function openCustomerHistory(encodedEmail) {
        window.location.href = 'admin_history.php?email=' + encodedEmail;
    }

    // ===== UPDATE ORDER STATUS =====
    function getOrderDetails(orderId) {
        const rows = document.querySelectorAll('.orders-table tbody tr');
        for (const row of rows) {
            const cells = row.querySelectorAll('td');
            if (cells.length > 0 && cells[0].textContent.trim().includes(orderId.slice(-6))) {
                return {
                    name: cells[1]?.textContent.trim() || '-',
                    phone: cells[2]?.textContent.trim() || '-',
                    email: cells[3]?.textContent.trim() || '-',
                    products: cells[4]?.textContent.trim() || '-',
                    timestamp: row.getAttribute('data-timestamp') || '',
                    total: cells[6]?.textContent.trim() || '-'
                };
            }
        }
        return {
            name: '-',
            phone: '-',
            email: '-',
            products: '-',
            timestamp: '',
            total: '-'
        };
    }

    async function updateOrder(action, orderId, dbId, itemId) {
        const confirmMessages = {
            approve_item: 'Are you sure you want to approve this item?',
            cancel_item: 'Are you sure you want to cancel this item?',
            restore_item: 'Restore this item to pending?',
            revert_item: 'Revert this item back to pending?',
            delete_item: '⚠️ PERMANENTLY DELETE this item? This cannot be undone!'
        };

        if (!confirm(confirmMessages[action] || 'Are you sure?')) return;
        if (action === 'delete_item' && !confirm('Are you absolutely sure?')) return;

        // Grab details BEFORE DB action so row still exists in table
        const orderDetails = getOrderDetails(orderId);

        try {
            const response = await fetch('update_order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    action: action,
                    order_ref: orderId,
                    db_id: dbId,
                    item_id: itemId
                })
            });
            const result = await response.json();

            if (result.success) {
                loadOrders();
            } else {
                alert('Error: ' + result.message);
            }
        } catch (err) {
            alert('Request failed: ' + err.message);
        }
    }

    function escapeAutomationHtml(value) {
        return String(value ?? '').replace(/[&<>'"]/g, char => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;'
        })[char]);
    }

    function attachmentLinks(raw) {
        if (!raw) return '';
        let links = [];
        try { links = JSON.parse(raw); } catch (_) { links = [raw]; }
        links = Array.isArray(links) ? links : [];
        const safeLinks = links.filter(link => /^uploads\/automation\/\d+\/[a-f0-9]+\.(?:jpg|png|webp|pdf)$/i.test(link));
        if (!safeLinks.length) return '';
        return `<div class="automation-proof-links">${safeLinks.map((link, index) =>
            `<a href="download_automation_attachment.php?path=${encodeURIComponent(link)}" target="_blank" rel="noopener">View proof ${index + 1}</a>`
        ).join('')}</div>`;
    }

    async function openAutomationPanel(orderId) {
        const modal = document.getElementById('automationModal');
        const messages = document.getElementById('automationMessages');
        modal.hidden = false;
        messages.innerHTML = '<div>Loading conversation...</div>';
        try {
            const response = await fetch(`get_order_automation.php?order_id=${encodeURIComponent(orderId)}`, { cache: 'no-store' });
            const data = await response.json();
            if (!response.ok || !data.success) throw new Error(data.message || 'Unable to load conversation');
            messages.innerHTML = data.messages.length ? data.messages.map(message => `
                <article class="automation-message ${escapeAutomationHtml(message.direction)}">
                    <div class="automation-message-meta">${escapeAutomationHtml(message.direction)} via ${escapeAutomationHtml(message.channel)} · ${escapeAutomationHtml(message.created_at)}</div>
                    <h4>Original message</h4><pre>${escapeAutomationHtml(message.original_message)}</pre>
                    ${message.translated_message ? `<h4>English translation</h4><pre>${escapeAutomationHtml(message.translated_message)}</pre>` : ''}
                    ${message.ai_summary ? `<h4>AI summary</h4><pre>${escapeAutomationHtml(message.ai_summary)}</pre>` : ''}
                    ${attachmentLinks(message.attachment_url)}
                </article>`).join('') : '<div class="empty-state">No messages recorded yet.</div>';
        } catch (error) {
            messages.innerHTML = `<div>${escapeAutomationHtml(error.message)}</div>`;
        }
    }

    function closeAutomationPanel() {
        document.getElementById('automationModal').hidden = true;
    }

    function sendApprovalEmail(orderId, d) {
        const now = new Date();
        const actionTime = now.toLocaleString('en-US', {
            timeZone: 'Asia/Beirut',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
        const body = `
            <h2 style="color:#00b894;">✅ Order Approved</h2>
            <table style="border-collapse:collapse; width:100%; font-family:Arial,sans-serif;">
                <tr><td style="padding:8px; color:#666;">Order ID</td><td style="padding:8px; font-weight:bold;">${orderId}</td></tr>
                <tr style="background:#f9f9f9;"><td style="padding:8px; color:#666;">Customer Name</td><td style="padding:8px;">${d.name}</td></tr>
                <tr><td style="padding:8px; color:#666;">Email</td><td style="padding:8px;">${d.email}</td></tr>
                <tr style="background:#f9f9f9;"><td style="padding:8px; color:#666;">Phone</td><td style="padding:8px;">${d.phone}</td></tr>
                <tr><td style="padding:8px; color:#666;">Product</td><td style="padding:8px; font-weight:bold;">${d.products}</td></tr>
                <tr style="background:#f9f9f9;"><td style="padding:8px; color:#666;">Total</td><td style="padding:8px; font-weight:bold;">${d.total}</td></tr>
<tr><td style="padding:8px; color:#666;">Approved At</td><td style="padding:8px; color:#00b894; font-weight:bold;">${actionTime}</td></tr>
            </table>        `;

        // Send to customer
        const f1 = new FormData();
        f1.append('to_email', d.email);
        f1.append('subject', `[APPROVED] ${orderId}`);
        f1.append('body', body);
        fetch('send_email.php', {
            method: 'POST',
            body: f1
        }).catch(e => console.error(e));

        // Send to support
        const f2 = new FormData();
        f2.append('to_email', 'support@thetradingroutine.com');
        f2.append('subject', `[APPROVED] ${orderId}`);
        f2.append('body', body);
        fetch('send_email.php', {
            method: 'POST',
            body: f2
        }).catch(e => console.error(e));
    }

    function sendCancellationEmail(orderId, d) {
        const now = new Date();
        const actionTime = now.toLocaleString('en-US', {
            timeZone: 'Asia/Beirut',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });

        const body = `
            <h2 style="color:#dc2626;">❌ Order Cancelled</h2>
            <table style="border-collapse:collapse; width:100%; font-family:Arial,sans-serif;">
                <tr><td style="padding:8px; color:#666;">Order ID</td><td style="padding:8px; font-weight:bold;">${orderId}</td></tr>
                <tr style="background:#f9f9f9;"><td style="padding:8px; color:#666;">Customer Name</td><td style="padding:8px;">${d.name}</td></tr>
                <tr><td style="padding:8px; color:#666;">Email</td><td style="padding:8px;">${d.email}</td></tr>
                <tr style="background:#f9f9f9;"><td style="padding:8px; color:#666;">Phone</td><td style="padding:8px;">${d.phone}</td></tr>
                <tr><td style="padding:8px; color:#666;">Product</td><td style="padding:8px; font-weight:bold;">${d.products}</td></tr>
                <tr style="background:#f9f9f9;"><td style="padding:8px; color:#666;">Total</td><td style="padding:8px; font-weight:bold;">${d.total}</td></tr>
<tr><td style="padding:8px; color:#666;">Cancelled At</td><td style="padding:8px; color:#dc2626; font-weight:bold;">${actionTime}</td></tr>
            </table>        `;

        // Send to customer
        const f1 = new FormData();
        f1.append('to_email', d.email);
        f1.append('subject', `[CANCELLED] ${orderId}`);
        f1.append('body', body);
        fetch('send_email.php', {
            method: 'POST',
            body: f1
        }).catch(e => console.error(e));

        // Send to support
        const f2 = new FormData();
        f2.append('to_email', 'support@thetradingroutine.com');
        f2.append('subject', `[CANCELLED] ${orderId}`);
        f2.append('body', body);
        fetch('send_email.php', {
            method: 'POST',
            body: f2
        }).catch(e => console.error(e));
    }

    // ===== UPDATE STATS =====
    function updateStats(orders) {
        const total = orders.length;
        const pending = orders.filter(o => o.status === 'pending').length;
        const approved = orders.filter(o => o.status === 'unlocked').length;
        const cancelled = orders.filter(o => o.status === 'cancelled').length;
        const approvedRevenue = orders.filter(o => o.status === 'unlocked').reduce((sum, o) => sum + parseFloat(o.total), 0);

        document.getElementById('approvedTransactions').textContent = approved;
        document.getElementById('approvedRevenue').textContent = '$' + approvedRevenue.toLocaleString(undefined, {
            minimumFractionDigits: 2
        });
        document.getElementById('totalOrders').textContent = total;
        document.getElementById('pendingOrders').textContent = pending;
        document.getElementById('cancelledOrders').textContent = cancelled;
    }

    // ===== SHOW SECTION =====
    function showSection(section) {
        document.querySelectorAll('.section-content').forEach(el => el.style.display = 'none');
        document.querySelectorAll('.menu-link').forEach(el => el.classList.remove('active'));
        document.getElementById(section + 'Section').style.display = 'block';
        event.target.classList.add('active');
        if (section === 'orders' || section === 'stats') loadOrders();
    }

    // ===== ADMIN LOGOUT =====
    async function adminLogout() {
        if (confirm('Are you sure you want to logout?')) {
            await fetch('auth_admin_logout.php', { method: 'POST' }).catch(() => {});
            localStorage.removeItem('adminLogged');
            localStorage.removeItem('adminEmail');
            window.location.href = 'login.php';
        }
    }

    // ===== INITIALIZE =====
    checkAdminAuth();
    loadOrders();

    // Keep Customer Orders and Market Insights synchronized with the database.
    setInterval(loadOrders, 8000);

    // Refresh when tab becomes visible
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) loadOrders();
    });
    window.addEventListener('focus', loadOrders);
</script>

<?php include 'footer.php'; ?>
