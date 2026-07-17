<?php
// checkout.php
include 'header.php';
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@29.1.0/dist/css/intlTelInput.css">

<!-- ===== CHECKOUT HEADER ===== -->
<section class="checkout-header-section">
    <div class="checkout-header-content">
        <h1 class="checkout-title" id="checkoutPageTitle">Checkout</h1>
        <p class="checkout-subtitle" id="checkoutPageSubtitle">Complete your registration</p>
    </div>
</section>

<!-- ===== CHECKOUT SECTION ===== -->
<style>
    .checkout-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 70vh;
        padding: 2rem;
    }

    .checkout-layout {
        width: 100%;
        display: flex;
        justify-content: center;
    }

    .checkout-form-section {
        width: 100%;
        max-width: 650px;
    }

    .form-card {
        width: 100%;
        margin: 0 auto;
    }

    #whatsappPaymentInfo, #usdtPaymentInfo { display: none !important; }
    .payment-method-option:has(input[value="usdt"]) { opacity: 0.8; }
    .payment-help {
        margin: 1rem 0 1.5rem;
        padding: 1rem;
        border: 1px solid rgba(0, 212, 255, 0.25);
        border-radius: 10px;
        background: rgba(0, 212, 255, 0.06);
        color: var(--text-muted);
        line-height: 1.6;
    }

    .iti { width: 100%; }
    .iti input { width: 100%; }
    .iti__country-container { color: #222; }
    .payment-label { display: inline-flex; align-items: center; gap: 0.65rem; }
    .payment-logo {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-weight: 800;
    }
    .whish-logo { background: #e31b23; color: #fff; font-size: 0.65rem; letter-spacing: -0.5px; }
    .usdt-logo { background: #26a17b; color: #fff; font-size: 1.35rem; }
</style>

<div class="checkout-container">
    <div class="checkout-layout">
        <!-- Checkout Form -->
        <div class="checkout-form-section">
            <div class="form-card">
                <h3 class="form-section-title" id="formTitle">Your Information</h3>
                <form id="checkoutForm">
                    <div id="checkoutOrderSummary" style="margin-bottom: 1.5rem; padding: 1rem; background: rgba(0, 212, 255, 0.06); border: 1px solid rgba(0, 212, 255, 0.2); border-radius: 10px;">
                        <h4 style="margin: 0 0 0.75rem; color: var(--text-main);">Your order</h4>
                        <div id="checkoutSummaryItems" style="display: grid; gap: 0.5rem;"></div>
                        <div style="display: flex; justify-content: space-between; margin-top: 0.75rem; color: var(--text-muted); font-size: 0.9rem;">
                            <span>Service fee (1%)</span><span id="checkoutServiceFee">$0.00</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-top: 0.9rem; padding-top: 0.9rem; border-top: 1px solid rgba(0, 212, 255, 0.2); font-weight: bold;">
                            <span>Total</span><span id="checkoutSummaryTotal" style="color: var(--primary-color);">$0.00</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="name" data-i18n="fullName">Full Name</label>
                        <input type="text" id="name" name="name" placeholder="Enter your full name" required>
                    </div>

                    <div class="form-group">
                        <label for="phone"><span data-i18n="phoneNumber">Phone Number</span></label>
                        <input type="tel" id="phone" name="phone" placeholder="Your phone number"
                            required>
                        <small style="display:block; margin-top:0.5rem; color:var(--text-muted);">Select your country, then type only your phone number.</small>
                    </div>

                    <!-- Payment Method Section (hidden for free courses, shown for EA) -->
                    <div id="paymentMethodSection" style="display: none;">
                        <h3 class="form-section-title" style="margin-top: 2rem;" data-i18n="paymentMethod">Payment
                            Method</h3>

                        <div class="payment-methods">
                            <label class="payment-method-option">
                                <input type="radio" name="paymentMethod" value="whatsapp" class="payment-radio" checked>
                                <span class="payment-label"><span class="payment-logo whish-logo">whish</span> Whish <small>(Recommended)</small></span>
                            </label>
                            <label class="payment-method-option">
                                <input type="radio" name="paymentMethod" value="usdt" class="payment-radio">
                                <span class="payment-label"><span class="payment-logo usdt-logo">₮</span> USDT <small>(Alternative)</small></span>
                            </label>
                        </div>

                        <div class="payment-help">
                            Place your order first. Next, you will see the exact amount, payment details, and one
                            WhatsApp button for sending your payment screenshot.
                        </div>

                        <!-- WhatsApp Payment Section -->
                        <div id="whatsappPaymentInfo" class="payment-info-box" style="display: none;">
                            <h4 class="payment-info-title">Whish Payment</h4>
                            <div class="payment-info-content">
                                <img src="wts.jpeg" alt="Whish Payment"
                                    style="width: 100%; max-width: 300px; border-radius: 10px; margin-bottom: 1rem;">
                                <p><strong>Send Whish via WhatsApp Number:</strong></p>
                                <div class="transfer-number-box" style="font-size: 1.1rem; letter-spacing: 2px;">+961 71
                                    493 997</div>
                                <div
                                    style="margin-top: 1.5rem; padding: 1.5rem; background: linear-gradient(135deg, rgba(0, 212, 255, 0.1) 0%, rgba(255, 193, 7, 0.1) 100%); border: 2px solid rgba(0, 212, 255, 0.3); border-radius: 10px;">
                                    <p
                                        style="margin: 0 0 1rem 0; font-size: 1rem; color: var(--primary-color); font-weight: bold;">
                                        IMPORTANT INSTRUCTIONS</p>
                                    <div style="display: flex; flex-direction: column; gap: 0.8rem;">
                                        <div style="display: flex; align-items: flex-start; gap: 0.8rem;">
                                            <span
                                                style="background: var(--primary-color); color: white; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; flex-shrink: 0;">1</span>
                                            <p style="margin: 0; color: var(--text-main); font-size: 0.9rem;">Send
                                                <strong>The Amount Via Whish</strong> to the Whatsapp number above</p>
                                        </div>
                                        <div style="display: flex; align-items: flex-start; gap: 0.8rem;">
                                            <span
                                                style="background: var(--primary-color); color: white; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; flex-shrink: 0;">2</span>
                                            <p style="margin: 0; color: var(--text-main); font-size: 0.9rem;">Take a
                                                screenshot of your payment receipt</p>
                                        </div>
                                        <div style="display: flex; align-items: flex-start; gap: 0.8rem;">
                                            <span
                                                style="background: var(--primary-color); color: white; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; flex-shrink: 0;">3</span>
                                            <p style="margin: 0; color: var(--text-main); font-size: 0.9rem;">Send the
                                                screenshot to WhatsApp: <strong>+961 71 493 997</strong></p>
                                        </div>
                                        <div style="display: flex; align-items: flex-start; gap: 0.8rem;">
                                            <span
                                                style="background: var(--primary-color); color: white; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; flex-shrink: 0;">4</span>
                                            <p style="margin: 0; color: var(--text-main); font-size: 0.9rem;">Include
                                                your name and email in the message</p>
                                        </div>
                                        <div style="display: flex; align-items: flex-start; gap: 0.8rem;">
                                            <span
                                                style="background: var(--primary-color); color: white; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; flex-shrink: 0;">5</span>
                                            <p style="margin: 0; color: var(--text-main); font-size: 0.9rem;">Our team
                                                will verify and <strong style="color: #00ff00;">unlock your
                                                    purchase</strong> within minutes</p>
                                        </div>
                                    </div>
                                </div>
                                <a href="https://wa.me/96171493997?text=I%20would%20like%20to%20send%20payment%20for%20my%20courses"
                                    target="_blank" class="whatsapp-btn"
                                    style="display: inline-block; margin-top: 1rem;">
                                    Open WhatsApp
                                </a>
                            </div>
                        </div>

                        <!-- USDT Payment Section -->
                        <div id="usdtPaymentInfo" class="payment-info-box" style="display: none;">
                            <h4 class="payment-info-title">USDT (TRC20 Network) Payment</h4>
                            <div class="payment-info-content">
                                <p><strong>Send USDT on TRC20 Network:</strong></p>
                                <img src="bin.jpeg" alt="USDT Payment"
                                    style="width: 100%; max-width: 300px; border-radius: 10px; margin: 1rem 0;">
                                <div class="transfer-number-box"
                                    style="font-size: 0.85rem; word-break: break-all; font-family: monospace;">
                                    TTdq4aEeJ57Vmsb11HthW9QXWRLCgEN9wx</div>
                                <div
                                    style="margin-top: 1.5rem; padding: 1.5rem; background: linear-gradient(135deg, rgba(255, 193, 7, 0.1) 0%, rgba(0, 212, 255, 0.1) 100%); border: 2px solid rgba(255, 193, 7, 0.3); border-radius: 10px;">
                                    <p style="margin: 0 0 1rem 0; font-size: 1rem; color: #ffc107; font-weight: bold;">
                                        IMPORTANT INSTRUCTIONS</p>
                                    <div style="display: flex; flex-direction: column; gap: 0.8rem;">
                                        <div style="display: flex; align-items: flex-start; gap: 0.8rem;">
                                            <span
                                                style="background: #ffc107; color: #333; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; flex-shrink: 0;">1</span>
                                            <p style="margin: 0; color: var(--text-main); font-size: 0.9rem;">Send
                                                <strong>The Amount</strong> to the wallet address above</p>
                                        </div>
                                        <div style="display: flex; align-items: flex-start; gap: 0.8rem;">
                                            <span
                                                style="background: #ffc107; color: #333; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; flex-shrink: 0;">2</span>
                                            <p style="margin: 0; color: var(--text-main); font-size: 0.9rem;">Make sure
                                                you use <strong>TRC20 Network</strong> (not Ethereum, Polygon, etc)</p>
                                        </div>
                                        <div style="display: flex; align-items: flex-start; gap: 0.8rem;">
                                            <span
                                                style="background: #ffc107; color: #333; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; flex-shrink: 0;">3</span>
                                            <p style="margin: 0; color: var(--text-main); font-size: 0.9rem;">Copy and
                                                screenshot the transaction receipt</p>
                                        </div>
                                        <div style="display: flex; align-items: flex-start; gap: 0.8rem;">
                                            <span
                                                style="background: #ffc107; color: #333; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; flex-shrink: 0;">4</span>
                                            <p style="margin: 0; color: var(--text-main); font-size: 0.9rem;">Send the
                                                screenshot to WhatsApp: <strong>+961 71 493 997</strong></p>
                                        </div>
                                        <div style="display: flex; align-items: flex-start; gap: 0.8rem;">
                                            <span
                                                style="background: #ffc107; color: #333; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; flex-shrink: 0;">5</span>
                                            <p style="margin: 0; color: var(--text-main); font-size: 0.9rem;">Include
                                                your name and email in the message</p>
                                        </div>
                                    </div>
                                </div>
                                <a href="https://wa.me/96171493997?text=I%20have%20sent%20USDT%20on%20TRC20%20network%20-%20please%20confirm%20receipt"
                                    target="_blank" class="whatsapp-btn"
                                    style="display: inline-block; margin-top: 1rem;">
                                    Send Transaction via WhatsApp
                                </a>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary checkout-submit-btn">
                        Complete Registration
                    </button>
                </form>
            </div>
        </div>

        <!-- Order Summary -->

    </div>
</div>

<!-- Payment Receipt Modal -->
<div id="paymentReceiptModal"
    style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #000000; z-index: 10000; justify-content: center; align-items: center; padding: 2rem;">
    <div
        style="background: var(--bg-main); border-radius: 15px; padding: 2.5rem; width: 100%; max-width: 550px; max-height: 95vh; overflow-y: auto; box-shadow: 0 20px 60px rgba(0, 212, 255, 0.4); position: relative; margin: auto;">
        <!-- Receipt Header -->
        <div
            style="text-align: center; margin-bottom: 2rem; padding-bottom: 1.5rem; border-bottom: 2px solid rgba(0, 212, 255, 0.2);">
            <h2 style="margin: 0; color: var(--primary-color); font-size: 1.8rem;">Order Received</h2>
            <p style="margin: 0.5rem 0 0 0; color: var(--text-muted); font-size: 0.9rem;">Awaiting payment confirmation</p>
        </div>

        <!-- Receipt Content -->
        <div style="margin-bottom: 2rem;">
            <!-- Order ID -->
            <div
                style="margin-bottom: 1.5rem; padding: 1rem; background: rgba(0, 212, 255, 0.05); border-left: 3px solid var(--primary-color); border-radius: 5px;">
                <p style="margin: 0; color: var(--text-muted); font-size: 0.85rem;">ORDER ID</p>
                <p id="receiptOrderId"
                    style="margin: 0.5rem 0 0 0; color: var(--primary-color); font-weight: bold; font-size: 1.1rem; font-family: monospace;">
                    ORD-...</p>
            </div>

            <!-- Customer Info -->
            <div style="margin-bottom: 1.5rem;">
                <h4 style="margin: 0 0 1rem 0; color: var(--text-main); font-size: 0.95rem;">Customer Information</h4>
                <div style="display: grid; gap: 0.8rem;">
                    <div>
                        <p style="margin: 0; color: var(--text-muted); font-size: 0.85rem;">Full Name</p>
                        <p id="receiptName" style="margin: 0.3rem 0 0 0; color: var(--text-main); font-weight: 500;">-
                        </p>
                    </div>
                    <div>
                        <p style="margin: 0; color: var(--text-muted); font-size: 0.85rem;">Email</p>
                        <p id="receiptEmail" style="margin: 0.3rem 0 0 0; color: var(--text-main); font-weight: 500;">-
                        </p>
                    </div>
                    <div>
                        <p style="margin: 0; color: var(--text-muted); font-size: 0.85rem;">Phone Number</p>
                        <p id="receiptPhone" style="margin: 0.3rem 0 0 0; color: var(--text-main); font-weight: 500;">-
                        </p>
                    </div>
                </div>
            </div>

            <!-- Order Details -->
            <div style="margin-bottom: 1.5rem;">
                <h4 style="margin: 0 0 1rem 0; color: var(--text-main); font-size: 0.95rem;">Order Details</h4>
                <div style="display: grid; gap: 0.8rem;">
                    <div style="display: flex; justify-content: space-between;">
                        <p style="margin: 0; color: var(--text-muted);">Products</p>
                        <p id="receiptProductName" style="margin: 0; color: var(--text-main); font-weight: 500;">-</p>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <p style="margin: 0; color: var(--text-muted);">Amount</p>
                        <p id="receiptAmount" style="margin: 0; color: #00ff00; font-weight: bold; font-size: 1.1rem;">-
                        </p>
                    </div>
                </div>
            </div>

            <!-- Payment Method -->
            <div style="margin-bottom: 1.5rem; padding: 1rem; background: rgba(0, 212, 255, 0.05); border-radius: 8px;">
                <p style="margin: 0; color: var(--text-muted); font-size: 0.85rem;">PAYMENT METHOD</p>
                <p id="receiptPaymentMethod"
                    style="margin: 0.5rem 0 0 0; color: var(--primary-color); font-weight: bold;">-</p>
                <p style="margin: 0.5rem 0 0 0; color: var(--text-muted); font-size: 0.85rem;">Payment ID: <span
                        id="receiptPaymentId" style="color: var(--text-main); font-family: monospace;">-</span></p>
            </div>

            <!-- Date/Time -->
            <div id="manualPaymentSteps" style="margin-bottom: 1.5rem; padding: 1.25rem; border: 1px solid rgba(37, 211, 102, 0.35); border-radius: 10px; background: rgba(37, 211, 102, 0.06);">
                <h4 style="margin: 0 0 1rem; color: var(--text-main);">Complete your payment</h4>
                <p style="margin: 0 0 0.75rem; color: var(--text-muted);">1. Send the exact amount shown above.</p>
                <div id="whishPaymentDetails" style="margin-bottom: 0.75rem;">
                    <p style="margin: 0 0 0.4rem; color: var(--text-muted);">2. Send via Whish to:</p>
                    <button type="button" onclick="copyPaymentValue('+96171493997', this)" style="width: 100%; padding: 0.8rem; border: 1px solid var(--primary-color); border-radius: 8px; background: transparent; color: var(--primary-color); cursor: pointer; font-weight: bold;">+961 71 493 997 — Copy number</button>
                </div>
                <div id="usdtPaymentDetails" style="display: none; margin-bottom: 0.75rem;">
                    <p style="margin: 0 0 0.4rem; color: var(--text-muted);">2. Send USDT using TRC20 only:</p>
                    <img src="bin.jpeg" alt="USDT TRC20 payment QR code" style="display: block; width: 100%; max-width: 240px; margin: 0 auto 1rem; border-radius: 10px; background: #fff;">
                    <button type="button" onclick="copyPaymentValue('TTdq4aEeJ57Vmsb11HthW9QXWRLCgEN9wx', this)" style="width: 100%; padding: 0.8rem; border: 1px solid #ffc107; border-radius: 8px; background: transparent; color: #ffc107; cursor: pointer; font-weight: bold;">Copy TRC20 wallet address</button>
                </div>
                <p style="margin: 0; color: var(--text-muted);">3. Take a screenshot and send it using the WhatsApp button below.</p>
            </div>

            <div
                style="display: flex; justify-content: space-between; padding: 1rem; background: rgba(255, 193, 7, 0.05); border-radius: 8px;">
                <div>
                    <p style="margin: 0; color: var(--text-muted); font-size: 0.85rem;">Date</p>
                    <p id="receiptDate" style="margin: 0.3rem 0 0 0; color: var(--text-main); font-weight: 500;">-</p>
                </div>
                <div>
                    <p style="margin: 0; color: var(--text-muted); font-size: 0.85rem;">Time</p>
                    <p id="receiptTime" style="margin: 0.3rem 0 0 0; color: var(--text-main); font-weight: 500;">-</p>
                </div>
            </div>

            <!-- Auto-Close Countdown Timer -->
            <div id="countdownTimer"
                style="display: none; margin-top: 2rem; padding: 1.5rem; background: rgba(231, 76, 60, 0.1); border: 2px solid #E74C3C; border-radius: 10px; text-align: center;">
                <p style="margin: 0 0 0.5rem 0; color: var(--text-muted); font-size: 0.9rem;">Modal closes in</p>
                <p id="countdownDisplay"
                    style="margin: 0; color: #E74C3C; font-weight: bold; font-size: 2.5rem; text-shadow: 0 0 10px rgba(231, 76, 60, 0.5);">
                    20</p>
                <p style="margin: 0.5rem 0 0 0; color: var(--text-muted); font-size: 0.85rem;">seconds</p>
            </div>
        </div>

        <!-- Action Buttons -->
        <div style="display: grid; grid-template-columns: 1fr; gap: 1rem;">
            <button id="whatsappSendBtn" onclick="sendWhatsAppReceipt();"
                style="background: #25D366; color: white; padding: 1rem; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; text-align: center; font-size: 0.9rem; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(37, 211, 102, 0.3);">
                Send Payment Proof on WhatsApp
            </button>
        </div>

        <!-- Footer Note -->
        <p style="margin: 1.5rem 0 0 0; text-align: center; color: var(--text-muted); font-size: 0.85rem;">
            Access is normally activated within 5–15 minutes after payment is verified.
        </p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@29.1.0/dist/js/intlTelInput.min.js"></script>
<script>
    let checkoutPhoneInput = null;

    const checkoutCart = {
        items: [],
        load: function () {
            try {
                const saved = localStorage.getItem('cart');
                this.items = saved ? JSON.parse(saved) : [];
            } catch (e) {
                this.items = [];
            }
        },
        getTotal: function () {
            return this.items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        },
        getItemCount: function () {
            return this.items.length;
        }
    };

    document.addEventListener('DOMContentLoaded', function () {
        console.log('=== CHECKOUT PAGE LOADED ===');

        const isUserLogged = sessionStorage.getItem('userLogged');
        const currentUsername = sessionStorage.getItem('currentUsername');
        const currentEmail = sessionStorage.getItem('currentEmail');

        const phoneElement = document.getElementById('phone');
        if (phoneElement && window.intlTelInput) {
            checkoutPhoneInput = window.intlTelInput(phoneElement, {
                initialCountry: 'lb',
                separateDialCode: true,
                countrySearch: true,
                countryOrder: ['lb', 'ae', 'sa', 'qa', 'kw', 'us', 'gb']
            });
        }

        if (!isUserLogged || !currentUsername || !currentEmail) {
            alert('⚠️ You need to be logged in!\n\nRedirecting to Login page...');
            setTimeout(function () {
                window.location.href = 'login.php?redirect=index.php';
            }, 500);
            return;
        }

        checkoutCart.load();

        if (checkoutCart.items.length === 0) {
            alert('Your cart is empty!');
            setTimeout(function () {
                window.location.href = 'courses.php';
            }, 500);
            return;
        }

        const summaryItems = document.getElementById('checkoutSummaryItems');
        const summaryTotal = document.getElementById('checkoutSummaryTotal');
        const summaryServiceFee = document.getElementById('checkoutServiceFee');
        if (summaryItems) {
            summaryItems.innerHTML = checkoutCart.items.map(item =>
                `<div style="display:flex; justify-content:space-between; gap:1rem;"><span>${item.title || 'Course'}</span><span>$${(item.price * item.quantity).toFixed(2)}</span></div>`
            ).join('');
        }
        const cartSubtotal = checkoutCart.getTotal();
        const serviceFee = cartSubtotal * 0.01;
        if (summaryServiceFee) summaryServiceFee.textContent = '$' + serviceFee.toFixed(2);
        if (summaryTotal) summaryTotal.textContent = '$' + (cartSubtotal + serviceFee).toFixed(2);

        // Detect if cart has items that require full payment
        const hasPaidProduct = checkoutCart.items.some(item => item.type === 'robot' || item.type === 'robot_sr' || item.type === 'robot_ib' || item.type === 'ea' || item.type === 'course');
        const isFreeOnly = !hasPaidProduct; // Free checkout (only if no paid products)

        // Update page titles and form based on cart type
        const pageTitle = document.getElementById('checkoutPageTitle');
        const pageSubtitle = document.getElementById('checkoutPageSubtitle');
        const formTitle = document.getElementById('formTitle');
        const paymentSection = document.getElementById('paymentMethodSection');
        const submitBtn = document.querySelector('.checkout-submit-btn');

        if (isFreeOnly) {
            // Course only — free enrollment
            if (pageTitle) pageTitle.textContent = 'Free Enrollment';
            if (pageSubtitle) pageSubtitle.textContent = 'Enter your details to enroll in the free course';
            if (formTitle) formTitle.textContent = 'Your Information';
            if (submitBtn) submitBtn.textContent = 'Complete Enrollment';
            if (paymentSection) paymentSection.style.display = 'none';
        } else {
            // EA purchase — show payment methods
            if (pageTitle) pageTitle.textContent = 'Secure Checkout';
            if (pageSubtitle) pageSubtitle.textContent = 'Place your order, then send your payment proof';
            if (formTitle) formTitle.textContent = 'Billing Information';
            if (submitBtn) submitBtn.textContent = 'Place Order';
            if (paymentSection) paymentSection.style.display = 'block';

            // Handle payment method radio button changes
            const paymentRadios = document.querySelectorAll('input[name="paymentMethod"]');
            paymentRadios.forEach(radio => {
                radio.addEventListener('change', function () {
                    const whatsappInfo = document.getElementById('whatsappPaymentInfo');
                    const usdtInfo = document.getElementById('usdtPaymentInfo');

                    if (whatsappInfo) whatsappInfo.style.display = 'none';
                    if (usdtInfo) usdtInfo.style.display = 'none';

                    if (this.value === 'whatsapp') {
                        if (whatsappInfo) whatsappInfo.style.display = 'block';
                        if (submitBtn) submitBtn.textContent = 'Place Order';
                    } else if (this.value === 'usdt') {
                        if (usdtInfo) usdtInfo.style.display = 'block';
                        if (submitBtn) submitBtn.textContent = 'Place Order';
                    }
                });
            });

            const defaultPayment = document.querySelector('input[name="paymentMethod"]:checked');
            if (defaultPayment) defaultPayment.dispatchEvent(new Event('change'));
        }

        // Handle form submission
        const checkoutForm = document.getElementById('checkoutForm');
        if (checkoutForm) {
            checkoutForm.addEventListener('submit', async function (e) {
                e.preventDefault();

                const name = document.getElementById('name').value.trim();
                const localPhone = document.getElementById('phone').value.trim();
                const phoneDigits = localPhone.replace(/\D/g, '').replace(/^0+/, '');
                const selectedCountry = checkoutPhoneInput ? checkoutPhoneInput.getSelectedCountry() : null;
                const phone = selectedCountry?.dialCode ? '+' + selectedCountry.dialCode + phoneDigits : localPhone;

                let paymentMethod;
                if (isFreeOnly) {
                    paymentMethod = 'free';
                } else {
                    paymentMethod = document.querySelector('input[name="paymentMethod"]:checked')?.value;
                }

                if (!name || phoneDigits.length < 6) {
                    alert('❌ Please fill in all required fields!');
                    return;
                }

                if (!isFreeOnly && !paymentMethod) {
                    alert('❌ Please select a payment method!');
                    return;
                }

                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.textContent = '⏳ Processing...';
                }

                const now = new Date();
                const dateStr = now.getFullYear().toString() +
                    String(now.getMonth() + 1).padStart(2, '0') +
                    String(now.getDate()).padStart(2, '0') +
                    String(now.getHours()).padStart(2, '0') +
                    String(now.getMinutes()).padStart(2, '0') +
                    String(now.getSeconds()).padStart(2, '0');
                const order_ref = 'ORD-' + currentEmail + '-' + dateStr;
                const total = isFreeOnly ? '0.00' : (checkoutCart.getTotal() * 1.01).toFixed(2);

                let productName = "Unknown Product";
                if (checkoutCart.items.length > 0) {
                    const names = checkoutCart.items.map(item => {
                        if (item.type === 'course') return 'Trading Mastery Course';
                        if (item.type === 'ea') return 'TTR Risk Calculator EA';
                        if (item.type === 'robot') return 'TTR Robot';
                        if (item.type === 'robot_sr') return 'S&R Precision EA';
                        if (item.type === 'robot_ib') return 'Instant Breakout EA';
                        return item.title || 'Unknown Product';
                    });
                    productName = names.join(' + ');
                }

                // ===== SAVE TO DATABASE =====
                try {
                    const response = await fetch('save_order.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            order_ref: order_ref,
                            name: name,
                            phone: phone,
                            email: currentEmail,
                            payment_method: paymentMethod,
                            total: total,
                            items: checkoutCart.items
                        })
                    });
                    const result = await response.json();
                    if (!result.success) {
                        console.error('DB save failed:', result.message);
                        alert('We could not place your order. Your cart is safe—please try again.');
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.textContent = isFreeOnly ? 'Complete Enrollment' : 'Place Order';
                        }
                        return;
                    } else {
                        console.log('✓ Order saved to database:', result.order_ref);
                    }
                } catch (err) {
                    console.error('DB save error:', err);
                    alert('We could not place your order. Your cart is safe—please try again.');
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.textContent = isFreeOnly ? 'Complete Enrollment' : 'Place Order';
                    }
                    return;
                }

                // ===== SAVE TO localStorage (backup) =====
                const order = {
                    id: order_ref,
                    items: checkoutCart.items,
                    total: total,
                    name: name,
                    phone: phone,
                    email: currentEmail,
                    paymentMethod: paymentMethod,
                    status: 'pending',
                    createdAt: new Date().toLocaleDateString('en-US', {
                        year: 'numeric', month: 'long', day: 'numeric'
                    }),
                    createdTime: new Date().getTime()
                };

                const userOrders = JSON.parse(localStorage.getItem('user_orders_' + currentUsername) || '[]');
                userOrders.push(order);
                localStorage.setItem('user_orders_' + currentUsername, JSON.stringify(userOrders));

                const allOrders = JSON.parse(localStorage.getItem('admin_orders') || '[]');
                allOrders.push(order);
                localStorage.setItem('admin_orders', JSON.stringify(allOrders));

                console.log('✓ Order saved');

                // Clear cart
                localStorage.removeItem('cart');
                checkoutCart.items = [];

                if (submitBtn) {
                    submitBtn.disabled = false;
                }

                // Show result based on type
                // Show receipt modal for all orders (Free and EA)
                document.getElementById('receiptOrderId').textContent = order_ref;
                document.getElementById('receiptName').textContent = name;
                document.getElementById('receiptEmail').textContent = currentEmail;
                document.getElementById('receiptPhone').textContent = phone;
                document.getElementById('receiptProductName').textContent = productName;
                document.getElementById('receiptAmount').textContent = isFreeOnly ? '0.00' : '$' + parseFloat(total).toFixed(2) + ' USD';
                document.getElementById('receiptPaymentId').textContent = isFreeOnly ? 'N/A' : order_ref.substring(0, 20) + '...';

                let paymentMethodText = 'Registration';
                if (!isFreeOnly) {
                    paymentMethodText = paymentMethod === 'whatsapp' ? '💬 Whish Payment' : '₿ USDT (TRC20) Payment';
                }
                document.getElementById('receiptPaymentMethod').textContent = paymentMethodText;

                const manualSteps = document.getElementById('manualPaymentSteps');
                const whatsappButton = document.getElementById('whatsappSendBtn');
                const whishDetails = document.getElementById('whishPaymentDetails');
                const usdtDetails = document.getElementById('usdtPaymentDetails');
                if (manualSteps) manualSteps.style.display = isFreeOnly ? 'none' : 'block';
                if (whatsappButton) whatsappButton.style.display = isFreeOnly ? 'none' : 'block';
                if (!isFreeOnly && whishDetails && usdtDetails) {
                    whishDetails.style.display = paymentMethod === 'whatsapp' ? 'block' : 'none';
                    usdtDetails.style.display = paymentMethod === 'usdt' ? 'block' : 'none';
                }

                document.getElementById('receiptDate').textContent = now.toLocaleDateString('en-US', {
                    year: 'numeric', month: 'long', day: 'numeric'
                });
                document.getElementById('receiptTime').textContent = now.toLocaleTimeString('en-US', {
                    hour: '2-digit', minute: '2-digit'
                });

                const receiptMessage = `ORDER RECEIPT\n\nOrder ID: ${order_ref}\nProduct: ${productName}\nCustomer: ${name}\nEmail: ${currentEmail}\nPhone: ${phone}\nAmount: ${isFreeOnly ? 'FREE' : '$' + parseFloat(total).toFixed(2)}\nPayment: ${paymentMethodText}\nStatus: pending`;
                document.getElementById('whatsappSendBtn').href = 'https://wa.me/96171493997?text=' + encodeURIComponent(receiptMessage);

                document.body.style.overflow = 'hidden';
                const footer = document.querySelector('footer');
                if (footer) footer.style.display = 'none';

                const modal = document.getElementById('paymentReceiptModal');
                modal.style.display = 'flex';
                modal.style.flexDirection = 'column';
                window.scrollTo(0, 0);
            });
        }

        window.downloadReceiptPDF = function () {
            const receiptContent = document.querySelector('[style*="max-width: 550px"]');
            if (!receiptContent) { alert('Receipt not found'); return; }
            const orderId = document.getElementById('receiptOrderId')?.textContent || 'receipt';
            const clonedContent = receiptContent.cloneNode(true);
            clonedContent.style.backgroundColor = 'white';
            clonedContent.style.color = '#000';
            const script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js';
            script.onload = function () {
                html2pdf().set({
                    margin: 10,
                    filename: `Receipt_${orderId}.pdf`,
                    html2canvas: { scale: 2 },
                    jsPDF: { orientation: 'portrait', unit: 'mm', format: 'a4' }
                }).from(clonedContent).save();
            };
            document.head.appendChild(script);
        };

        window.sendEmailReceipt = function () {
            const receiptOrderId = document.getElementById('receiptOrderId')?.textContent || '';
            const receiptName = document.getElementById('receiptName')?.textContent || '';
            const receiptEmail = document.getElementById('receiptEmail')?.textContent || '';
            const receiptPhone = document.getElementById('receiptPhone')?.textContent || '';
            const receiptPayment = document.getElementById('receiptPaymentMethod')?.textContent || '';
            const receiptDate = document.getElementById('receiptDate')?.textContent || '';
            const receiptTime = document.getElementById('receiptTime')?.textContent || '';
            const receiptProductName = document.getElementById('receiptProductName')?.textContent || '';

            const formData = new FormData();
            formData.append('to_email', 'kbkareem31@gmail.com');
            formData.append('subject', `[NEW ORDER] ${receiptOrderId}`);
            formData.append('body', `<h2>New Order</h2><ul><li>Order: ${receiptOrderId}</li><li>Name: ${receiptName}</li><li>Product: ${receiptProductName}</li><li>Email: ${receiptEmail}</li><li>Phone: ${receiptPhone}</li><li>Payment: ${receiptPayment}</li><li>Date: ${receiptDate} ${receiptTime}</li></ul>`);

            fetch('send_email.php', { method: 'POST', body: formData })
                .then(r => r.json())
                .then(result => {
                    alert(result.success ? '✓ Receipt sent!' : 'Error: ' + result.message);
                    startCountdownTimer();
                })
                .catch(() => { alert('Error sending email'); startCountdownTimer(); });
        };

        window.sendWhatsAppReceipt = function () {
            const receiptOrderId = document.getElementById('receiptOrderId')?.textContent || '';
            const receiptName = document.getElementById('receiptName')?.textContent || '';
            const receiptEmail = document.getElementById('receiptEmail')?.textContent || '';
            const receiptPhone = document.getElementById('receiptPhone')?.textContent || '';
            const receiptProduct = document.getElementById('receiptProductName')?.textContent || '';
            const receiptAmount = document.getElementById('receiptAmount')?.textContent || '';
            const receiptMethod = document.getElementById('receiptPaymentMethod')?.textContent || '';
            const message = `Hello, I have completed my payment.\n\nOrder: ${receiptOrderId}\nProduct: ${receiptProduct}\nAmount: ${receiptAmount}\nMethod: ${receiptMethod}\nName: ${receiptName}\nEmail: ${receiptEmail}\nPhone: ${receiptPhone}\n\nI will attach my payment screenshot here.`;
            window.open(`https://wa.me/96171493997?text=${encodeURIComponent(message)}`, '_blank');
        };

        window.copyPaymentValue = function (value, button) {
            navigator.clipboard.writeText(value).then(function () {
                const original = button.textContent;
                button.textContent = 'Copied ✓';
                setTimeout(function () { button.textContent = original; }, 1800);
            });
        };

        function startCountdownTimer() {
            const timerDisplay = document.getElementById('countdownDisplay');
            const timerContainer = document.getElementById('countdownTimer');
            if (!timerContainer) return;
            timerContainer.style.display = 'block';
            let secondsRemaining = 20;
            timerDisplay.textContent = secondsRemaining;
            const countdownInterval = setInterval(() => {
                secondsRemaining--;
                timerDisplay.textContent = secondsRemaining;
                if (secondsRemaining <= 5) timerDisplay.style.color = '#FF6B6B';
                if (secondsRemaining <= 0) { clearInterval(countdownInterval); closeReceiptModal(); }
            }, 1000);
        }

        window.closeReceiptModal = function () {
            const modal = document.getElementById('paymentReceiptModal');
            if (modal) {
                modal.style.display = 'none';
                const footer = document.querySelector('footer');
                if (footer) footer.style.display = '';
                window.location.href = 'courses.php';
            }
        };
    });
</script>


<?php include 'footer.php'; ?>
