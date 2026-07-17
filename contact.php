<?php
// contact.php
include 'header.php';
?>

<!-- ===== CONTACT HEADER SECTION ===== -->
<section class="contact-header">
    <div class="contact-header-content">
        <h1 class="contact-title" data-i18n="contactOurTeam">Contact Our Team</h1>
        <p class="contact-subtitle" data-i18n="contactTeamDesc">If you have any questions about our trading courses, broker services, or educational resources, our team is ready to assist you.</p>
    </div>
</section>

<!-- ===== CONTACT SECTION ===== -->
<section class="contact-section">
    <div class="contact-container">
        <!-- Contact Info -->
        <div class="contact-info">
            <h2 data-i18n="hereToHelp">We're Here to Help</h2>
            <div class="info-items">
                <div class="info-item">
                    <div class="info-item-content">
                        <h3 data-i18n="emailSupport">Email Support</h3>
                        <p data-i18n="emailSupportDesc">For inquiries regarding courses, trading education, or partnerships, reach out to our support team.</p>
                        <p><a href="mailto:support@thetradingroutine.com">support@thetradingroutine.com</a></p>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-item-content">
                        <h3 data-i18n="whatsappSupport">WhatsApp Support</h3>
                        <p data-i18n="whatsappSupportDesc">Connect with our team instantly for assistance or additional information.</p>
                        <p><a href="https://wa.me/71493997" target="_blank" data-i18n="startConversation">Start a Conversation</a></p>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-item-content">
                        <h3 data-i18n="responseTime">Response Time</h3>
                        <p data-i18n="responseTimeDesc">Our team strives to respond to all inquiries within 24 hours.</p>
                    </div>
                </div>
            </div>

            <div class="contact-features">
                <h3 data-i18n="whyContactTeam">Why Contact Our Team?</h3>
                <ul>
                    <li class="feature-title" data-i18n="feature1">Course Information & Guidance</li>
                    <li data-i18n="feature1Desc">Get assistance choosing the right course and understanding our trading education programs.</li>
                    <li class="feature-title" data-i18n="feature2">Trading Strategy Questions</li>
                    <li data-i18n="feature2Desc">Ask questions related to trading concepts, strategies, and market analysis.</li>
                    <li class="feature-title" data-i18n="feature3">Payments & Billing Support</li>
                    <li data-i18n="feature3Desc">Receive help with course payments, billing inquiries, or account access.</li>
                    <li class="feature-title" data-i18n="feature4">Partnership Opportunities</li>
                    <li data-i18n="feature4Desc">Discuss collaboration opportunities, broker partnerships, and business inquiries.</li>
                    <li class="feature-title" data-i18n="feature5">Technical Support</li>
                    <li data-i18n="feature5Desc">Get assistance with platform access, account setup, or technical issues.</li>
                </ul>
            </div>
        </div>

    </div>
</section>

<style>
    /* ===== CONTACT HEADER ===== */
    .contact-header {
        position: relative;
        padding: 6rem 5%;
        text-align: center;
        background: linear-gradient(135deg, rgba(0, 212, 255, 0.1), rgba(0, 184, 148, 0.05));
        border-bottom: 2px solid rgba(0, 212, 255, 0.2);
        margin-bottom: 4rem;
    }

    .contact-header-content {
        max-width: 800px;
        margin: 0 auto;
    }

    .contact-header-icon {
        font-size: 4rem;
        margin-bottom: 1.5rem;
        display: inline-block;
        animation: bounce 2s infinite;
    }

    .contact-title {
        font-size: 3rem;
        margin-bottom: 1rem;
        background: linear-gradient(135deg, var(--primary-color), #00b894);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        font-weight: 800;
    }

    .contact-subtitle {
        font-size: 1.2rem;
        color: var(--text-muted);
        line-height: 1.6;
    }

    /* ===== CONTACT SECTION ===== */
    .contact-section {
        padding: 4rem 5%;
        max-width: 1200px;
        margin: 0 auto;
    }

    .contact-container {
        display: grid;
        grid-template-columns: 1fr;
        gap: 4rem;
        align-items: start;
    }

    .contact-info h2 {
        font-size: 2rem;
        margin-bottom: 2rem;
        color: var(--text-light);
    }

    .info-items {
        display: flex;
        flex-direction: column;
        gap: 2rem;
        margin-bottom: 3rem;
    }

    .info-item {
        display: flex;
        gap: 1.5rem;
        padding: 1.5rem;
        background: linear-gradient(145deg, rgba(26, 31, 58, 0.8), rgba(10, 14, 39, 0.9));
        border-radius: 15px;
        border: 1px solid rgba(0, 212, 255, 0.15);
        transition: all 0.3s ease;
    }

    .info-item:hover {
        transform: translateX(10px);
        border-color: var(--primary-color);
        box-shadow: 0 10px 30px rgba(0, 212, 255, 0.15);
    }

    .info-item-icon {
        font-size: 2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, rgba(0, 212, 255, 0.1), rgba(0, 184, 148, 0.1));
        border-radius: 12px;
        flex-shrink: 0;
    }

    .info-item-content h3 {
        font-size: 1.1rem;
        margin-bottom: 0.5rem;
        color: var(--primary-color);
    }

    .info-item-content p {
        color: var(--text-muted);
    }

    .info-item-content a {
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 600;
        transition: color 0.3s ease;
    }

    .info-item-content a:hover {
        color: #00b894;
    }

    .contact-features {
        background: linear-gradient(135deg, rgba(0, 212, 255, 0.05), rgba(0, 184, 148, 0.05));
        padding: 2rem;
        border-radius: 15px;
        border: 1px solid rgba(0, 212, 255, 0.2);
    }

    .contact-features h3 {
        font-size: 1.3rem;
        margin-bottom: 1.5rem;
        color: var(--text-light);
    }

    .contact-features ul {
        list-style: none;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .contact-features li {
        color: var(--text-light);
        font-size: 1rem;
        padding: 0;
        margin: 0;
    }

    .contact-features .feature-title {
        font-weight: bold;
        color: #00d4ff;
        margin-bottom: 0.3rem;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 768px) {
        .contact-container {
            grid-template-columns: 1fr;
            gap: 2rem;
        }

        .contact-title {
            font-size: 2rem;
        }

        .contact-header {
            padding: 3rem 5%;
        }

        .contact-form-wrapper {
            padding: 2rem;
        }
    }
</style>

<?php include 'footer.php'; ?>