<?php
// cart.php
include 'header.php';
?>

<!-- ===== CART HEADER ===== -->
<section class="cart-header-section">
    <div class="cart-header-animated-bg">
        <div class="cart-bg-glow cart-glow-1"></div>
        <div class="cart-bg-glow cart-glow-2"></div>
    </div>
    <div class="cart-header-content">
        <h1 class="cart-main-title" data-i18n="yourCart">Enrollment Summary</h1>
        <p class="cart-subtitle" data-i18n="reviewCart">Review your selected programs before completing enrollment</p>
    </div>
</section>

<!-- ===== MOBILE NAVIGATION MENU ===== -->


<!-- ===== MAIN CART CONTAINER ===== -->
<div class="main-cart-wrapper">
    <div class="cart-layout">
        <!-- Cart Items Section -->
        <div class="cart-items-section">
            <div class="cart-items-header">
                <h2 class="cart-items-title">
                    <span class="title-animation"><span data-i18n="selectedPrograms">Selected Programs</span></span>
                </h2>
            </div>
            <div class="cart-items">
                <!-- Cart items loaded dynamically by JavaScript -->
                <div class="loading-cart">
                    <div class="cart-spinner"></div>
                    <p data-i18n="loadingCart">Loading your cart...</p>
                </div>
            </div>
        </div>

        <!-- Cart Summary Section -->
        <div class="cart-sidebar">
            <div class="cart-summary">
                <!-- Cart summary loaded dynamically by JavaScript -->
                <div class="summary-skeleton">
                    <div class="skeleton-line"></div>
                    <div class="skeleton-line"></div>
                    <div class="skeleton-line"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Hide hamburger menu on cart page */
    .hamburger {
        display: none !important;
    }

    /* Cart Page Styles */
    .cart-header-icon {
        font-size: 4rem;
        margin-bottom: 1.5rem;
        animation: bounce 2s infinite;
    }

    .cart-header-animated-bg {
        position: absolute;
        inset: 0;
        pointer-events: none;
        overflow: hidden;
    }

    .cart-bg-glow {
        position: absolute;
        border-radius: 50%;
        filter: blur(80px);
        opacity: 0.2;
        animation: cartGlowPulse 6s ease-in-out infinite;
    }

    .cart-glow-1 {
        width: 300px;
        height: 300px;
        background: var(--primary-color);
        top: -50px;
        left: 10%;
    }

    .cart-glow-2 {
        width: 250px;
        height: 250px;
        background: var(--secondary-color);
        bottom: -50px;
        right: 15%;
        animation-delay: 3s;
    }

    @keyframes cartGlowPulse {

        0%,
        100% {
            opacity: 0.15;
            transform: scale(1);
        }

        50% {
            opacity: 0.3;
            transform: scale(1.05);
        }
    }

    .cart-items-header {
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
        border-bottom: 2px solid rgba(0, 212, 255, 0.2);
    }

    .cart-items-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #fff;
        margin: 0;
    }

    .title-animation {
        display: inline-block;
        animation: slideInDown 0.6s ease-out;
    }

    .loading-cart {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 4rem 2rem;
        gap: 1.5rem;
    }

    .cart-spinner {
        width: 60px;
        height: 60px;
        border: 4px solid rgba(0, 212, 255, 0.2);
        border-top-color: var(--primary-color);
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    .loading-cart p {
        color: var(--text-muted);
        font-size: 1.1rem;
        animation: pulse 2s infinite ease-in-out;
    }

    .summary-skeleton {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .skeleton-line {
        height: 16px;
        background: linear-gradient(90deg, rgba(255, 255, 255, 0.05), rgba(0, 212, 255, 0.1), rgba(255, 255, 255, 0.05));
        background-size: 200% 100%;
        border-radius: 8px;
        animation: shimmer 2s infinite;
    }

    .skeleton-line:nth-child(1) {
        width: 100%;
    }

    .skeleton-line:nth-child(2) {
        width: 85%;
    }

    .skeleton-line:nth-child(3) {
        width: 90%;
    }

    @keyframes shimmer {

        0%,
        100% {
            background-position: 200% 0;
        }

        50% {
            background-position: -200% 0;
        }
    }

    @keyframes slideInDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    @keyframes bounce {

        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-10px);
        }
    }

    @keyframes pulse {

        0%,
        100% {
            opacity: 0.6;
        }

        50% {
            opacity: 1;
        }
    }

    /* Cart Items Table */
    .cart-items-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1.5rem;
    }

    .cart-items-table thead {
        border-bottom: 2px solid var(--primary-color);
    }

    .cart-items-table th {
        padding: 1rem;
        text-align: left;
        color: var(--primary-color);
        font-weight: 600;
        background: rgba(0, 212, 255, 0.05);
    }

    .cart-items-table td {
        padding: 1.5rem 1rem;
        border-bottom: 1px solid rgba(0, 212, 255, 0.1);
        color: var(--text-main);
    }

    .cart-item-name {
        font-weight: 500;
        font-size: 1.05rem;
    }

    .cart-item-price {
        color: var(--primary-color);
        font-weight: 600;
    }

    .remove-item-btn {
        padding: 0.5rem 1rem;
        background: #dc2626;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }

    .remove-item-btn:hover {
        background: #b91c1c;
        transform: translateY(-2px);
    }

    .empty-cart-message {
        text-align: center;
        padding: 3rem;
        color: var(--text-muted);
    }

    .empty-cart-message h3 {
        font-size: 1.5rem;
        margin-bottom: 1rem;
        color: var(--text-main);
    }

    .empty-cart-message p {
        font-size: 1.1rem;
        margin-bottom: 2rem;
    }

    .continue-shopping-btn {
        padding: 0.8rem 2rem;
        background: white;
        color: var(--dark-bg);
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
    }

    .continue-shopping-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(0, 212, 255, 0.4);
    }

    /* Cart Summary */
    .cart-summary {
        background: linear-gradient(135deg, rgba(0, 212, 255, 0.1), rgba(0, 184, 148, 0.05));
        border: 2px solid var(--primary-color);
        border-radius: 12px;
        padding: 2rem;
        position: sticky;
        top: 20px;
    }

    .summary-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 0;
        border-bottom: 1px solid rgba(0, 212, 255, 0.1);
        color: var(--text-main);
    }

    .summary-item:last-child {
        border-bottom: none;
        padding-top: 1.5rem;
        border-top: 2px solid var(--primary-color);
    }

    .summary-label {
        font-size: 1rem;
        font-weight: 500;
    }

    .summary-value {
        font-size: 1.1rem;
        color: var(--primary-color);
        font-weight: 600;
    }

    .summary-total {
        font-size: 1.5rem !important;
        color: #00ff88 !important;
    }

    .checkout-btn {
        width: 100%;
        padding: 1.2rem;
        background: white;
        border: none;
        border-radius: 10px;
        color: black;
        font-weight: bold;
        font-size: 1.1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-top: 2rem;
    }

    .checkout-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 40px rgba(0, 212, 255, 0.5);
    }

    .checkout-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
    }

    /* Mobile Navigation Menu */
    .cart-mobile-nav {
        display: none;
        background: linear-gradient(135deg, rgba(10, 14, 39, 0.95), rgba(26, 45, 95, 0.95));
        border-top: 2px solid rgba(0, 212, 255, 0.3);
        border-bottom: 2px solid rgba(0, 212, 255, 0.3);
        padding: 0.5rem 0;
        margin: 1rem 0;
    }

    .mobile-nav-link {
        display: block;
        padding: 1rem 1.5rem;
        color: #00d4ff;
        text-decoration: none;
        border-bottom: 1px solid rgba(0, 212, 255, 0.1);
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .mobile-nav-link:last-child {
        border-bottom: none;
    }

    .mobile-nav-link:hover {
        background: rgba(0, 212, 255, 0.1);
        padding-left: 2rem;
        color: #00ff88;
    }

    @media (max-width: 768px) {
        .cart-mobile-nav {
            display: block;
        }

        .cart-layout {
            flex-direction: column;
        }

        .cart-sidebar {
            width: 100%;
        }

        .cart-summary {
            position: static;
            margin-top: 2rem;
        }

        .cart-items-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
        }

        .cart-items-table thead {
            display: none;
        }

        .cart-items-table tbody tr {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
            padding: 1.2rem;
            border: none;
            border-bottom: none;
            background: linear-gradient(135deg, rgba(0, 212, 255, 0.08), rgba(0, 100, 150, 0.05));
            margin-bottom: 1rem;
            border-radius: 12px;
            border-left: 4px solid #00d4ff;
        }

        .cart-item-name {
            grid-column: 1;
            font-weight: 700;
            font-size: 1.1rem;
            color: #00d4ff;
            margin: 0;
            padding: 0;
        }

        .cart-items-table td {
            padding: 0;
            border: none;
            border-bottom: none;
        }

        .cart-items-table td:nth-child(2) {
            text-align: left;
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        .cart-items-table td:nth-child(3) {
            hidden: true;
        }

        .cart-items-table td:nth-child(4) {
            display: block;
            grid-column: 1;
            margin-top: 0.5rem;
        }

        .cart-items-table td:nth-child(5) {
            display: block;
            grid-column: 1;
            margin-top: 1rem;
        }

        .cart-item-price {
            grid-column: 1;
            color: #00ff88;
            font-weight: 700;
            font-size: 1.3rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
        }

        .remove-item-btn {
            padding: 0.7rem 1.2rem;
            background: #dc2626;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .remove-item-btn:active {
            background: #b91c1c;
            transform: scale(0.95);
        }
    }

    @media (max-width: 480px) {
        .cart-items-table tbody tr {
            padding: 1rem;
            gap: 0.8rem;
            margin-bottom: 0.8rem;
        }

        .cart-item-name {
            font-size: 1rem;
        }

        .cart-item-price {
            font-size: 1.1rem;
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }

        .remove-item-btn {
            padding: 0.6rem 1rem;
            font-size: 0.85rem;
            width: 100%;
        }
    }

    @media (max-width: 400px) {
        .cart-items-table tbody tr {
            padding: 0.8rem;
            gap: 0.6rem;
            margin-bottom: 0.6rem;
        }

        .cart-item-name {
            font-size: 0.95rem;
        }

        .cart-item-price {
            font-size: 1rem;
        }

        .remove-item-btn {
            padding: 0.5rem 0.8rem;
            font-size: 0.8rem;
            width: 100%;
        }
    }
</style>

<script>
    // Cart Management System
    const cart = {
        items: [],

        load: function() {
            try {
                const saved = localStorage.getItem('cart');
                this.items = saved ? JSON.parse(saved) : [];
                console.log('Cart loaded:', this.items);
            } catch (e) {
                console.error('Error loading cart:', e);
                this.items = [];
            }
        },

        save: function() {
            try {
                localStorage.setItem('cart', JSON.stringify(this.items));
                console.log('Cart saved:', this.items);
            } catch (e) {
                console.error('Error saving cart:', e);
            }
        },

        add: function(item) {
            this.items.push(item);
            this.save();
            console.log('Item added to cart:', item);
        },

        remove: function(itemId) {
            this.items = this.items.filter(item => item.id !== itemId);
            this.save();
            console.log('Item removed from cart:', itemId);
        },

        clear: function() {
            this.items = [];
            this.save();
            console.log('Cart cleared');
        },

        getTotal: function() {
            return this.items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        },

        getItemCount: function() {
            return this.items.length;
        }
    };

    // Display cart on cart.php page
    async function displayCartItems() {
        console.log('=== DISPLAY CART ITEMS ===');
        cart.load();

        const cartItemsDiv = document.querySelector('.cart-items');
        const cartSummaryDiv = document.querySelector('.cart-summary');

        // EMPTY CART
        if (cart.items.length === 0) {
            cartItemsDiv.innerHTML = `
            <div class="empty-cart-message">
                <h3>Your Cart is Empty</h3>
                <p>Explore our educational programs and begin building your trading expertise.</p>
                <a href="courses.php" class="continue-shopping-btn">Explore Programs</a>
            </div>
        `;
            cartSummaryDiv.innerHTML = `
            <h3>Enrollment Summary</h3>
            <p>$0.00</p>
        `;
            return;
        }

        // ===== TABLE =====
        let tableHtml = `
        <table class="cart-items-table">
            <thead>
                <tr>
                    <th>Program Name</th>
                    <th style="text-align:center;">Qty</th>
                    <th style="text-align:right;">Subtotal</th>
                    <th style="text-align:center;">Action</th>
                </tr>
            </thead>
            <tbody>
    `;

        cart.items.forEach(item => {
            const subtotal = item.price * item.quantity;

            tableHtml += `
            <tr>
                <td>
                    ${item.type === 'ea' ? ' EA - ' : ' '} ${item.title}
                </td>
                <td style="text-align:center;">${item.quantity}</td>
                <td style="text-align:right;">$${subtotal.toFixed(2)}</td>
                <td style="text-align:center;">
                    <button class="remove-item-btn" onclick="removeFromCart('${item.id}')">Remove</button>
                </td>
            </tr>
        `;
        });

        tableHtml += `</tbody></table>`;
        cartItemsDiv.innerHTML = tableHtml;

        // ===== CALCULATIONS =====
        const subtotal = cart.getTotal();
        const tax = subtotal * 0.01;
        const total = subtotal + tax;

        // ===== CHECK CONTENT =====
        let hasCourse = cart.items.some(item => item.type === 'course');
        let hasEA = cart.items.some(item => item.type === 'ea' || item.title === 'Risk Calculator'); 
        let hasRobotSR = cart.items.some(item => item.id === 'robot_sr' || item.title === 'S&R Precision EA');
        let hasRobotIB = cart.items.some(item => item.id === 'robot_ib' || item.title === 'Instant Breakout EA');

        let extraContent = '';

        // ===== COURSE UI =====
        if (hasCourse) {
            extraContent += `
        <div style="margin-top:2rem;">
            <p style="color:#00ff88;font-weight:bold;">Course Modules:</p>

            <div style="display:flex;flex-direction:column;gap:1.5rem;">
                <div style="padding:1rem;background:rgba(0,255,136,0.05);border-left:4px solid #00ff88;">
                    ✔ Basics Module
                </div>
                <div style="padding:1rem;background:rgba(0,212,255,0.05);border-left:4px solid #00d4ff;">
                    ✔ Advanced Module
                </div>
                <div style="padding:1rem;background:rgba(255,193,7,0.05);border-left:4px solid #ffc107;">
                    ✔ Risk Management
                </div>
            </div>
        </div>
        `;
        }

        // ===== EA / RISK CALCULATOR UI =====
        if (hasEA) {
            extraContent += `
        <div style="margin-top:2rem;">
            <p style="color:#00ff88;font-weight:bold;">Risk Calculator Includes:</p>

            <div style="display:flex;flex-direction:column;gap:1rem;">
                <div style="padding:1rem;background:rgba(0,255,136,0.05);border-left:4px solid #00ff88;">
                    ✔ Auto Lot Size Calculator
                </div>
                <div style="padding:1rem;background:rgba(0,212,255,0.05);border-left:4px solid #00d4ff;">
                    ✔ Risk % Control
                </div>
                <div style="padding:1rem;background:rgba(255,193,7,0.05);border-left:4px solid #ffc107;">
                    ✔ Works in MT5
                </div>
            </div>
        </div>
        `;
        }

        // ===== S&R PRECISION EA UI =====
        if (hasRobotSR) {
            extraContent += `
        <div style="margin-top:2rem;">
            <p style="color:#00ff88;font-weight:bold;">S&R Precision EA Includes:</p>

            <div style="display:flex;flex-direction:column;gap:1rem;">
                <div style="padding:1rem;background:rgba(0,255,136,0.05);border-left:4px solid #00ff88;">
                    ✔ Stop Guessing Breakouts
                </div>
                <div style="padding:1rem;background:rgba(0,212,255,0.05);border-left:4px solid #00d4ff;">
                    ✔ Real-time S&R Detection
                </div>
               \
                <div style="padding:1rem;background:rgba(0,255,136,0.05);border-left:4px solid #00ff88;">
                    ✔ MT5 Support
                </div>
            </div>
        </div>
        `;
        }

        // ===== INSTANT BREAKOUT EA UI =====
        if (hasRobotIB) {
            extraContent += `
        <div style="margin-top:2rem;">
            <p style="color:#00ff88;font-weight:bold;">Instant Breakout EA Includes:</p>

            <div style="display:flex;flex-direction:column;gap:1rem;">
                <div style="padding:1rem;background:rgba(0,255,136,0.05);border-left:4px solid #00ff88;">
                    ✔ Precise Trade Execution
                </div>
                <div style="padding:1rem;background:rgba(0,212,255,0.05);border-left:4px solid #00d4ff;">
                    ✔ Visual Level Adjustment
                </div>
               
                <div style="padding:1rem;background:rgba(0,255,136,0.05);border-left:4px solid #00ff88;">
                    ✔ MT5 Support
                </div>
            </div>
        </div>
        `;
        }

        // ===== FINAL SUMMARY =====
        cartSummaryDiv.innerHTML = `
        <h3 style="text-align:center;">Enrollment Summary</h3>

        ${extraContent}

        <div style="margin-top:2rem;">
            <div class="summary-item">
                <span class="summary-label">Subtotal</span>
                <span class="summary-value">$${subtotal.toFixed(2)}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Processing Fee</span>
                <span class="summary-value">$${tax.toFixed(2)}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label" style="font-size:1.2rem;font-weight:700;">Total</span>
                <span class="summary-value summary-total">$${total.toFixed(2)}</span>
            </div>
        </div>

        <button class="checkout-btn" onclick="proceedToCheckout()">Proceed to Checkout</button>
    `;
    }
    // Remove item from cart
    function removeFromCart(itemId) {
        if (confirm('Are you sure you want to remove this item from cart?')) {
            cart.remove(itemId);
            displayCartItems();
        }
    }

    // Proceed to checkout
    function proceedToCheckout() {

        if (cart.items.length === 0) {
            alert('Your cart is empty!');
            return;
        }

        const currentEmail = sessionStorage.getItem('currentEmail');

        if (!currentEmail) {
            alert('❌ Please login first!');
            window.location.href = 'login.php';
            return;
        }

        // ✅ SAVE CART TEMPORARILY
        localStorage.setItem('checkout_cart', JSON.stringify(cart.items));

        // ✅ GO TO CHECKOUT PAGE
        window.location.href = 'checkout.php';
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        console.log('=== CART PAGE LOADED ===');
        console.log('DOM Ready - Calling displayCartItems()');
        console.log('localStorage keys:', Object.keys(localStorage));
        console.log('localStorage cart value:', localStorage.getItem('cart'));
        displayCartItems();
    });
</script>

<?php include 'footer.php'; ?>