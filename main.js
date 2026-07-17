// ===== UTILITY FUNCTIONS =====
function updateAuthButtons() {
    const isUserLogged = sessionStorage.getItem('userLogged') === 'true';
const isAdminLogged = localStorage.getItem('adminLogged') === 'true';
    const authLink = document.getElementById('authLink');
    const signupLink = document.getElementById('signupLink');

    if (isAdminLogged) {
        // Admin is logged in
        if (authLink) {
            authLink.textContent = '📊 ' + t('adminPanel');
            authLink.href = 'admin_dashboard.php';
        }
        if (signupLink) {
            signupLink.style.display = 'none';
        }
    } else if (isUserLogged) {
        // User is logged in - show Profile button and hide Sign Up
        if (authLink) {
            const username = sessionStorage.getItem('currentUsername') || 'User';
            authLink.textContent = '👤 ' + t('profile');
            authLink.href = 'user_dashboard.php';
        }
        if (signupLink) {
            signupLink.style.display = 'none';
        }
    } else {
        // User is NOT logged in - show Login and Sign Up buttons
        if (authLink) {
            authLink.textContent = t('login');
            authLink.href = 'login.php';
        }
        if (signupLink) {
            signupLink.style.display = 'inline-block';
        }
    }
}

// Call on page load
document.addEventListener('DOMContentLoaded', () => {
    updateAuthButtons();
    setupMobileMenu();
    setupScrollAnimations();
    setupParallaxEffects();
    addLoadingAnimations();
});

// ===== SCROLL REVEAL ANIMATIONS =====
function setupScrollAnimations() {
    // Add animate-on-scroll class to elements
    const animateElements = document.querySelectorAll(
        '.course-card, .feature-card, .stat-card, .form-container, ' +
        '.cart-items, .cart-summary, .checkout-form-section, .checkout-summary-section, ' +
        '.about-content, .about-cta-section, .dashboard-content'
    );

    animateElements.forEach((el, index) => {
        el.classList.add('scroll-reveal');
        el.style.transitionDelay = `${index * 0.05}s`;
    });

    // Intersection Observer for scroll animations
    const observerOptions = {
        root: null,
        rootMargin: '0px 0px -50px 0px',
        threshold: 0.1
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('revealed');
                // Don't unobserve to allow re-animation on scroll
            }
        });
    }, observerOptions);

    document.querySelectorAll('.scroll-reveal').forEach(el => {
        observer.observe(el);
    });

    // Add reveal styles dynamically
    if (!document.getElementById('scroll-reveal-styles')) {
        const style = document.createElement('style');
        style.id = 'scroll-reveal-styles';
        style.textContent = `
            .scroll-reveal {
                opacity: 0;
                transform: translateY(30px);
                transition: opacity 0.6s cubic-bezier(0.4, 0, 0.2, 1), 
                            transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
            }
            .scroll-reveal.revealed {
                opacity: 1;
                transform: translateY(0);
            }
        `;
        document.head.appendChild(style);
    }
}

// ===== PARALLAX EFFECTS =====
function setupParallaxEffects() {
    const hero = document.querySelector('.hero');
    if (!hero) return;

    window.addEventListener('scroll', () => {
        const scrolled = window.pageYOffset;
        const heroContent = document.querySelector('.hero-content');
        if (heroContent && scrolled < window.innerHeight) {
            heroContent.style.transform = `translateY(${scrolled * 0.3}px)`;
            heroContent.style.opacity = 1 - (scrolled / window.innerHeight) * 0.8;
        }
    });
}

// ===== LOADING ANIMATIONS =====
function addLoadingAnimations() {
    // Add stagger animation to navigation links
    const navLinks = document.querySelectorAll('.nav-links a');
    navLinks.forEach((link, index) => {
        link.style.animation = `fadeInDown 0.5s ease-out ${0.1 + index * 0.1}s backwards`;
    });

    // Add hover sound effect simulation (visual feedback)
    document.querySelectorAll('.btn, .course-card, .feature-card').forEach(el => {
        el.addEventListener('mouseenter', function () {
            this.style.transition = 'all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1)';
        });
        el.addEventListener('mouseleave', function () {
            this.style.transition = 'all 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
        });
    });

    // Add ripple effect to buttons
    document.querySelectorAll('.btn').forEach(btn => {
        btn.addEventListener('click', function (e) {
            const ripple = document.createElement('span');
            ripple.classList.add('btn-ripple');
            const rect = this.getBoundingClientRect();
            ripple.style.left = `${e.clientX - rect.left}px`;
            ripple.style.top = `${e.clientY - rect.top}px`;
            this.appendChild(ripple);
            setTimeout(() => ripple.remove(), 600);
        });
    });

    // Add ripple styles
    if (!document.getElementById('ripple-styles')) {
        const style = document.createElement('style');
        style.id = 'ripple-styles';
        style.textContent = `
            .btn-ripple {
                position: absolute;
                width: 10px;
                height: 10px;
                background: rgba(255, 255, 255, 0.4);
                border-radius: 50%;
                transform: translate(-50%, -50%) scale(0);
                animation: ripple 0.6s ease-out;
                pointer-events: none;
            }
            @keyframes ripple {
                to {
                    transform: translate(-50%, -50%) scale(30);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    }
}

// ===== MOBILE MENU TOGGLE =====
function setupMobileMenu() {
    const hamburger = document.getElementById('hamburger-btn');
    const navLinks = document.querySelector('.nav-links');

    hamburger.addEventListener('click', function () {
        hamburger.classList.toggle('active');
        navLinks.classList.toggle('nav-active');
        document.body.classList.toggle('menu-open');
    });

    // Also close menu when a link is clicked
    navLinks.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
            hamburger.classList.remove('active');
            navLinks.classList.remove('nav-active');
            document.body.classList.remove('menu-open');
        });
    });
}


// ===== CART MANAGEMENT =====
const cart = {
    items: JSON.parse(localStorage.getItem('cart')) || [],

    load() {
        try {
            const saved = localStorage.getItem('cart');
            this.items = saved ? JSON.parse(saved) : [];
            console.log('Cart loaded:', this.items);
        } catch (e) {
            console.error('Error loading cart:', e);
            this.items = [];
        }
    },

    add(courseId, courseTitle, price, description = '', level = '', duration = '', instructor = '', image = '') {
        const item = this.items.find(i => i.id === courseId);
        if (item) {
            // Course already in cart - keep quantity as 1 (no duplicate items)
            this.showNotification(`${courseTitle} is already in your cart!`);
        } else {
            this.items.push({
                id: courseId,
                title: courseTitle,
                price: price,
                description: description,
                level: level,
                duration: duration,
                instructor: instructor,
                image: image,
                quantity: 1
            });
            this.save();
            this.showNotification(`${courseTitle} added to cart!`);
        }
    },

    remove(courseId) {
        console.log(`Removing course ${courseId} (type: ${typeof courseId})`);
        console.log(`Current cart items:`, this.items.map(i => ({ id: i.id, idType: typeof i.id })));
        const initialCount = this.items.length;
        this.items = this.items.filter(i => {
            const match = i.id === courseId || parseInt(i.id) === parseInt(courseId);
            console.log(`Checking item ${i.id} against ${courseId}: match=${match}`);
            return !match;
        });
        console.log(`Items after filter:`, this.items);
        console.log(`Removed ${initialCount - this.items.length} item(s). Remaining: ${this.items.length}`);
        this.save();
        console.log('Cart saved to localStorage');
    },

    clear() {
        this.items = [];
        this.save();
    },

    save() {
        localStorage.setItem('cart', JSON.stringify(this.items));
    },

    getTotal() {
        return this.items.reduce((total, item) => total + (item.price * item.quantity), 0);
    },

    getItemCount() {
        return this.items.length;
    },

    showNotification(message) {
        const notification = document.createElement('div');
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 80px;
            right: 20px;
            background: linear-gradient(135deg, #00d4ff, #00a8cc);
            color: #000;
            padding: 1rem 1.5rem;
            border-radius: 50px;
            z-index: 9999;
            animation: slideInRight 0.3s ease-out;
            font-weight: 600;
        `;
        document.body.appendChild(notification);
        setTimeout(() => notification.remove(), 3000);
    }
};

// ===== FORM VALIDATION =====
function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function validatePassword(password) {
    return password.length >= 6;
}

// ===== LOGOUT FUNCTION =====
function logout() {
    if (confirm('Are you sure you want to logout?')) {
        sessionStorage.removeItem('userLogged');
        sessionStorage.removeItem('currentUsername');
        sessionStorage.removeItem('currentEmail');
        window.location.href = 'logout.php';
    }
}

// User logout function (same as logout)
function userLogout() {
    logout();
}

// ===== SIGNUP FORM HANDLER =====
document.addEventListener('DOMContentLoaded', function () {
    const signupForm = document.getElementById('signupForm');
    if (signupForm) {
        // Track which step we're on and what email we sent the code to
        let isCodeStep = false;
        let submittedEmail = null;
        let submittedUsername = null;
        let submittedPassword = null;

        signupForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const username = document.getElementById('username').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const verificationCode = document.getElementById('verificationCode').value.trim();
            const codeInputGroup = document.getElementById('codeInputGroup');

            // Step 2: Code verification and account creation
            if (isCodeStep) {
                console.log('===== STEP 2: VERIFYING CODE AND CREATING ACCOUNT =====');

                // Code verification and account creation
                if (!verificationCode || verificationCode.length !== 6) {
                    alert('Please enter a valid 6-digit code');
                    return;
                }

                // Verify code is all digits
                if (!/^\d{6}$/.test(verificationCode)) {
                    alert('Code must contain exactly 6 digits');
                    return;
                }

                // Verify email hasn't changed
                if (email !== submittedEmail) {
                    alert('Email address has changed. Please start over.');
                    isCodeStep = false;
                    signupForm.reset();
                    codeInputGroup.style.display = 'none';
                    return;
                }

                try {
                    // Build request body with stored values
                    const bodyData = `action=verify_and_create&username=${encodeURIComponent(submittedUsername)}&email=${encodeURIComponent(submittedEmail)}&password=${encodeURIComponent(submittedPassword)}&code=${encodeURIComponent(verificationCode)}`;

                    console.log('===== SUBMITTING VERIFICATION =====');
                    console.log('Username:', submittedUsername, 'Length:', submittedUsername.length);
                    console.log('Email:', submittedEmail, 'Length:', submittedEmail.length);
                    console.log('Password length:', submittedPassword.length);
                    console.log('Code:', verificationCode, 'Length:', verificationCode.length);
                    console.log('Full body data:', bodyData);

                    const response = await fetch('auth_signup.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: bodyData
                    });

                    console.log('Response status:', response.status);
                    console.log('Response ok:', response.ok);

                    // Get response text first to see if it's HTML or JSON
                    const responseText = await response.text();
                    console.log('Response text:', responseText);

                    if (!response.ok) {
                        throw new Error('Server error: ' + response.status + ' - ' + responseText);
                    }

                    let result;
                    try {
                        result = JSON.parse(responseText);
                    } catch (e) {
                        throw new Error('Invalid response format: ' + responseText);
                    }

                    if (result.success) {
                        alert('Account created successfully! Please login.');
                        isCodeStep = false;
                        submittedEmail = null;
                        submittedUsername = null;
                        submittedPassword = null;
                        signupForm.reset();
                        codeInputGroup.style.display = 'none';
                        window.location.href = 'login.php';
                    } else {
                        alert('Error: ' + (result.message || 'Unknown error'));
                    }
                } catch (error) {
                    console.error('Signup error:', error);
                    alert('Error creating account: ' + error.message);
                }
            } else {
                // Step 1: Validate form and send verification code to email
                console.log('===== STEP 1: SENDING VERIFICATION CODE =====');

                // Form validation
                if (!username || !email || !password || !confirmPassword) {
                    alert('Please fill all required fields');
                    return;
                }

                if (!validateEmail(email)) {
                    alert('Please enter a valid email');
                    return;
                }

                if (!validatePassword(password)) {
                    alert('Password must be at least 6 characters');
                    return;
                }

                if (password !== confirmPassword) {
                    alert('Passwords do not match');
                    return;
                }

                console.log('All validations passed. Sending code to:', email);

                try {
                    const response = await fetch('auth_signup.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `action=send_code&email=${encodeURIComponent(email)}`
                    });

                    console.log('Send code response status:', response.status);

                    if (!response.ok) {
                        throw new Error('Server error: ' + response.status);
                    }

                    const result = await response.json();
                    console.log('Send code result:', result);

                    if (result.success) {
                        // Store credentials for step 2
                        submittedEmail = email;
                        submittedUsername = username;
                        submittedPassword = password;

                        // Move to step 2
                        isCodeStep = true;

                        // Show code input field
                        codeInputGroup.style.display = 'block';
                        codeInputGroup.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        document.getElementById('verificationCode').focus();

                        // Start the 15-minute timer (900 seconds)
                        startCodeTimer(900);

                        alert('Verification code has been sent to ' + email + '.\n\nPlease check your email inbox (and spam folder) and enter the 6-digit code below.');
                    } else {
                        alert('Error: ' + (result.message || 'Failed to send code'));
                    }
                } catch (error) {
                    console.error('Send code error:', error);
                    alert('Error sending verification code: ' + error.message);
                }
            }
        });
    }

    // ===== LOGIN FORM HANDLER =====
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        // Load saved email if "Remember me" was checked
        const rememberMeCheckbox = document.getElementById('rememberMe');
        const savedEmail = localStorage.getItem('savedEmail');
        if (savedEmail) {
            document.getElementById('email').value = savedEmail;
            if (rememberMeCheckbox) {
                rememberMeCheckbox.checked = true;
            }
        }

        loginForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const rememberMe = rememberMeCheckbox ? rememberMeCheckbox.checked : false;

            if (!email || !password) {
                alert('Please fill all fields');
                return;
            }

            // Check for admin login
            if (email === 'Admin@thetradingroutine.com' && password === '123456') {
              // REPLACE WITH:
const adminTabId = 'tab_' + Date.now();
localStorage.setItem('adminLogged', 'true');
localStorage.setItem('adminEmail', email);
localStorage.setItem('adminTabId', adminTabId);
sessionStorage.setItem('myAdminTabId', adminTabId);
                if (rememberMe) {
                    localStorage.setItem('savedEmail', email);
                } else {
                    localStorage.removeItem('savedEmail');
                }
                alert('Admin login successful! Redirecting to dashboard...');
                window.location.href = 'admin_dashboard.php';
                return;
            }

            try {
                const response = await fetch('auth_login.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}`
                });

                const result = await response.json();

                // Log debug info from server
                if (result.debug) {
                    console.log('Server Debug Info:', result.debug);
                }

                if (!response.ok) {
                    console.error('Server Error:', result);
                    throw new Error(result.message || 'Server error: ' + response.status);
                }

                if (result.success) {
                    sessionStorage.setItem('userLogged', 'true');
                    sessionStorage.setItem('currentUsername', result.username || '');
                    sessionStorage.setItem('currentEmail', result.email || email);

                    // Save email if "Remember me" is checked
                    if (rememberMe) {
                        localStorage.setItem('savedEmail', email);
                    } else {
                        localStorage.removeItem('savedEmail');
                    }

                    // Check if there's a redirect parameter in the URL
                    const urlParams = new URLSearchParams(window.location.search);
                    const redirectTo = urlParams.get('redirect') || 'index.php';

                    console.log('Login successful! Redirecting to:', redirectTo);
                    window.location.href = redirectTo;
                } else {
                    console.error('Login failed:', result.message);
                    alert('Login failed: ' + (result.message || 'Unknown error'));
                }
            } catch (error) {
                console.error('Login error:', error);
                console.error('Error details:', error.message);
                alert('🔴 Login Error:\n\n' + error.message + '\n\nCheck browser console (F12) for details.');
            }
        });
    }

    // ===== LOAD COURSES =====
    const coursesGrid = document.querySelector('.courses-grid');
    if (coursesGrid) {
        loadCourses();
    }

    // ===== LOAD CART =====
    const cartItems = document.querySelector('.cart-items');
    if (cartItems) {
        displayCart();
        setupCartDelegation();
    }

    // ===== CHECKOUT HANDLER =====
    const checkoutForm = document.getElementById('checkoutForm');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            if (cart.items.length === 0) {
                alert('Your cart is empty');
                return;
            }

            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const address = document.getElementById('address').value.trim();
            const cardNumber = document.getElementById('cardNumber').value.trim();

            if (!name || !email || !address || !cardNumber) {
                alert('Please fill all fields');
                return;
            }

            try {
                const response = await fetch('checkout.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `name=${encodeURIComponent(name)}&email=${encodeURIComponent(email)}&address=${encodeURIComponent(address)}&cardNumber=${encodeURIComponent(cardNumber)}&courses=${encodeURIComponent(JSON.stringify(cart.items))}&total=${cart.getTotal()}`
                });

                const result = await response.json();
                if (result.success) {
                    alert('Order placed successfully!');
                    cart.clear();
                    window.location.href = 'index.php';
                } else {
                    alert('Error processing order: ' + result.message);
                }
            } catch (error) {
                alert('An error occurred. Please try again.');
                console.error(error);
            }
        });
    }
});

// ===== LOAD COURSES FROM SERVER =====
async function loadCourses() {
    try {
        console.log('Loading courses...');
        const response = await fetch('get_courses.php');
        const courses = await response.json();
        console.log('Courses loaded:', courses);

        const coursesGrid = document.querySelector('.courses-grid');
        if (!coursesGrid) {
            console.error('Courses grid not found in DOM');
            return;
        }

        if (!courses || courses.length === 0) {
            coursesGrid.innerHTML = `
                <div style="grid-column: 1/-1; text-align: center; padding: 3rem; color: var(--text-muted);">
                    <p style="font-size: 1.2rem;" data-i18n="noCourses">No courses found. Please import database.sql to phpMyAdmin</p>
                    <a href="debug.html" style="color: var(--primary-color); text-decoration: none; margin-top: 1rem; display: inline-block;" data-i18n="loading">Check Database →</a>
                </div>
            `;
            applyLanguage();
            return;
        }

        coursesGrid.innerHTML = '';

        courses.forEach((course, index) => {
            const card = document.createElement('div');
            card.className = 'course-card';
            card.style.animationDelay = `${index * 0.1}s`;

            // Get image for course
            const imageUrl = getCourseImageUrl(course.id);
            const emoji = getCourseEmoji(course.id);

            card.innerHTML = `
                <div class="course-image" style="position: relative; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));">
                    <img src="${imageUrl}" style="width: 100%; height: 100%; object-fit: cover; opacity: 0.7;" onerror="this.style.opacity='0'" alt="${course.title}">
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 3rem;">${emoji}</div>
                </div>
                <div class="course-content">
                    <h3 class="course-title">${course.title}</h3>
                    <p class="course-description">${course.description.substring(0, 100)}...</p>
                    <div class="course-meta">
                        <span>⏱️ ${course.duration}</span>
                        <span>📊 ${course.level}</span>
                    </div>
                    <div class="course-footer">
                        <div class="course-price">$${parseFloat(course.price).toFixed(2)}</div>
                        <button class="btn btn-primary" data-i18n="enrollNow" onclick="event.stopPropagation(); cart.add(${course.id}, '${course.title}', ${course.price}, '${course.description.replace(/'/g, "\\'")}', '${course.level}', '${course.duration}', '${course.instructor}', '${imageUrl}')">
                            Enroll Now
                        </button>
                    </div>
                </div>
            `;

            // No click navigation - only add to cart button available
            coursesGrid.appendChild(card);
        });
    } catch (error) {
        console.error('Error loading courses:', error);
        const coursesGrid = document.querySelector('.courses-grid');
        if (coursesGrid) {
            coursesGrid.innerHTML = `
                <div style="grid-column: 1/-1; text-align: center; padding: 3rem; color: var(--danger-color);">
                    <p style="font-size: 1.2rem;">⚠️ Error loading courses</p>
                    <p style="color: var(--text-muted); margin-top: 0.5rem;">${error.message}</p>
                    <a href="debug.html" style="color: var(--primary-color); text-decoration: none; margin-top: 1rem; display: inline-block;">Check Database →</a>
                </div>
            `;
        }
    }
}

// ===== GET COURSE EMOJI =====
function getCourseEmoji(courseId) {
    const emojis = {
        1: '₿',  // Bitcoin
        2: '📈', // Stock chart
        3: '💱', // Exchange
        4: '📊', // Chart
        5: '🛡️', // Shield
        6: '⚡'  // Lightning
    };
    return emojis[courseId] || '📚';
}

// ===== GET COURSE IMAGE URL =====
function getCourseImageUrl(courseId) {
    const imageIds = {
        1: '1642514623647-a622933c4d32', // Trading charts
        2: '1552664730-d307ca884978', // Stock market
        3: '1454165804606-c3d57bc86b40', // Financial data
        4: '1509042239860-f550ce710b93', // Trading desk
        5: '1551876019-5ee127ceac32', // Business meeting
        6: '1516321318423-f06f85e504b3'  // Financial graphs
    };
    const imageId = imageIds[courseId] || imageIds[1];
    return `https://images.unsplash.com/photo-${imageId}?w=500&q=50`;
}

// ===== DISPLAY CART =====
function displayCart() {
    const cartItemsContainer = document.querySelector('.cart-items');
    const cartSummary = document.querySelector('.cart-summary');

    if (!cartItemsContainer) return;

    if (cart.items.length === 0) {
        cartItemsContainer.innerHTML = '<div class="empty-cart-message"><p data-i18n="emptyCart">🛒 Your cart is empty</p><p><a href="courses.php" class="btn btn-primary" data-i18n="continueShopping">Continue Shopping</a></p></div>';
        if (cartSummary) {
            cartSummary.style.display = 'none';
        }
        applyLanguage();
        resetCartDelegation();
        return;
    }

    cartItemsContainer.innerHTML = '';

    cart.items.forEach((item, index) => {
        const itemElement = document.createElement('div');
        itemElement.className = 'cart-item-detailed';
        itemElement.style.animationDelay = `${index * 0.1}s`;
        itemElement.setAttribute('data-course-id', item.id);

        const imageUrl = item.image || getCourseImageUrl(item.id);

        itemElement.innerHTML = `
            <div class="cart-item-image">
                <img src="${imageUrl}" alt="${item.title}" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22140%22 height=%22140%22%3E%3Crect width=%22140%22 height=%22140%22 fill=%22%23164e63%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 font-size=%2240%22 fill=%22%2300d4ff%22 text-anchor=%22middle%22 dy=%22.3em%22%3E📚%3C/text%3E%3C/svg%3E'">
            </div>
            <div class="cart-item-content">
                <h3 class="cart-item-title">${item.title}</h3>
                <p class="cart-item-instructor">👨‍🏫 <span data-i18n="instructor">Instructor</span>: ${item.instructor || 'Not specified'}</p>
                <p class="cart-item-description">${item.description ? item.description.substring(0, 80) + '...' : 'No description'}</p>
                <div class="cart-item-details">
                    <span class="detail-badge">⏱️ ${item.duration || 'N/A'}</span>
                    <span class="detail-badge">📊 ${item.level || 'N/A'}</span>
                </div>
            </div>
            <div class="cart-item-actions">
                <div class="item-price"><span data-i18n="price">Price</span>: <strong>$${parseFloat(item.price).toFixed(2)}</strong></div>
                <button type="button" class="remove-btn" data-course-id="${item.id}" data-i18n="remove">🗑️ Remove</button>
            </div>
        `;
        cartItemsContainer.appendChild(itemElement);
    });

    if (cartSummary) {
        const total = cart.getTotal();
        cartSummary.innerHTML = `
            <h3 class="summary-title" data-i18n="orderSummary">Order Summary</h3>
            <div class="summary-row">
                <span data-i18n="subtotal">Subtotal:</span>
                <span>$${total.toFixed(2)}</span>
            </div>
            <div class="summary-row summary-total">
                <span data-i18n="total">Total:</span>
                <span>$${total.toFixed(2)}</span>
            </div>
            <button class="btn btn-primary checkout-btn" data-i18n="proceedCheckout" onclick="checkoutProtection()">
                Proceed to Checkout
            </button>
            <button class="btn btn-secondary" style="width: 100%; margin-top: 1rem;" data-i18n="continueShopping" onclick="window.location.href='courses.php'">
                ← Continue Shopping
            </button>
        `;
        applyLanguage();
    }

    // Reset delegation to allow new listeners to be set up
    resetCartDelegation();
    setupCartDelegation();
}

// ===== SETUP CART EVENT DELEGATION =====
let cartDelegationSetup = false;

function setupCartDelegation() {
    const cartItemsContainer = document.querySelector('.cart-items');
    if (!cartItemsContainer) return;

    // Only set up listeners once per container
    if (cartItemsContainer._delegationSetup) {
        return;
    }

    console.log('Setting up cart event delegation');

    // Add click event listener with event delegation
    const handleRemove = function (e) {
        // Handle remove button clicks
        if (e.target.classList.contains('remove-btn') || e.target.closest('.remove-btn')) {
            e.preventDefault();
            e.stopPropagation();

            const removeBtn = e.target.closest('.remove-btn') || e.target;
            const courseId = parseInt(removeBtn.getAttribute('data-course-id'));

            console.log(`Remove button clicked for course ${courseId}`);

            if (confirm('Are you sure you want to remove this course from your cart?')) {
                console.log(`Confirmed: Removing course ${courseId}`);
                try {
                    cart.remove(courseId);
                    console.log(`Course ${courseId} removed from cart`);
                    console.log('Current cart items:', cart.items);

                    // Refresh the display
                    displayCart();
                    console.log('Cart display updated');
                } catch (error) {
                    console.error('Error removing course:', error);
                }
            }
        }
    };

    // Store function references to allow removal
    cartItemsContainer._removeHandler = handleRemove;

    cartItemsContainer.addEventListener('click', handleRemove);

    cartItemsContainer._delegationSetup = true;
    console.log('Cart event delegation setup complete');
}

// ===== RESET CART DELEGATION =====
function resetCartDelegation() {
    const cartItemsContainer = document.querySelector('.cart-items');
    if (!cartItemsContainer) return;

    // Remove old event listeners
    if (cartItemsContainer._removeHandler) {
        cartItemsContainer.removeEventListener('click', cartItemsContainer._removeHandler);
    }

    cartItemsContainer._delegationSetup = false;
}

// ===== SMOOTH SCROLL =====
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({ behavior: 'smooth' });
        }
    });
});

// ===== UPDATE ONLY CART SUMMARY =====
function updateCartSummary() {
    const cartSummary = document.querySelector('.cart-summary');
    if (cartSummary) {
        const total = cart.getTotal();

        // Find existing elements or refresh if needed
        const totalRows = cartSummary.querySelectorAll('.summary-row span:last-child');
        if (totalRows.length >= 2) {
            totalRows[0].textContent = `$${total.toFixed(2)}`;
            totalRows[1].textContent = `$${total.toFixed(2)}`;
        } else {
            displayCart(); // Fallback to full render if structure is different
        }
    }
}

// ===== CHECKOUT PROTECTION =====
function checkoutProtection() {
    const isUserLogged = sessionStorage.getItem('userLogged') === 'true';

    if (!isUserLogged) {
        alert('⚠️ You must sign up and log in to proceed with checkout.\n\nPlease create an account or log in first.');
        window.location.href = 'login.php';
    } else {
        window.location.href = 'checkout.php';
    }
}

// ===== EMAIL VERIFICATION CODE SYSTEM =====
let verificationCodeTimer = null;

async function sendVerificationCode() {
    const emailInput = document.getElementById('email');
    const email = emailInput?.value.trim();

    if (!email) {
        alert('Please enter your email address');
        return;
    }

    if (!validateEmail(email)) {
        alert('Please enter a valid email address');
        return;
    }

    // Show loading state
    const sendBtn = document.querySelector('.btn-verify-email');
    const originalText = sendBtn?.textContent;
    if (sendBtn) {
        sendBtn.disabled = true;
        sendBtn.textContent = '⏳ Sending...';
    }

    try {
        const response = await fetch('api/send-verification-code.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ email: email })
        });

        const result = await response.json();

        if (result.success) {
            // Show code input group
            const codeInputGroup = document.getElementById('codeInputGroup');
            if (codeInputGroup) {
                codeInputGroup.style.display = 'block';
                codeInputGroup.scrollIntoView({ behavior: 'smooth' });
            }

            // Start countdown timer
            startCodeTimer(300); // 5 minutes

            // Show success message
            alert('✅ Verification code sent to your email! Check your inbox.');
        } else {
            alert('❌ Error: ' + (result.message || 'Could not send verification code'));
        }
    } catch (error) {
        console.error('Error sending verification code:', error);
        alert('⚠️ An error occurred. Please try again.');
    } finally {
        // Restore button state
        if (sendBtn) {
            sendBtn.disabled = false;
            sendBtn.textContent = originalText;
        }
    }
}

function startCodeTimer(seconds) {
    const timerElement = document.getElementById('codeTimer');
    if (!timerElement) return;

    let remaining = seconds;
    timerElement.style.display = 'block';

    if (verificationCodeTimer) {
        clearInterval(verificationCodeTimer);
    }

    const updateTimer = () => {
        const mins = Math.floor(remaining / 60);
        const secs = remaining % 60;
        timerElement.textContent = `Code expires in ${mins}:${secs.toString().padStart(2, '0')}`;

        if (remaining <= 0) {
            clearInterval(verificationCodeTimer);
            timerElement.textContent = '⏰ Code expired. Please request a new code.';
            const codeInputGroup = document.getElementById('codeInputGroup');
            if (codeInputGroup) {
                codeInputGroup.style.display = 'none';
            }
        }

        remaining--;
    };

    updateTimer();
    verificationCodeTimer = setInterval(updateTimer, 1000);
}

// ===== OAUTH FUNCTIONS =====
async function loginWithGoogle() {
    // This would redirect to Google OAuth
    // For now, show a placeholder message
    alert('⚠️ Google Login is being configured.\n\nPlease use traditional login for now.');
    console.log('Google OAuth flow initiated');

    // In production, you would:
    // window.location.href = 'auth/google-callback.php';
}

async function loginWithFacebook() {
    // This would redirect to Facebook OAuth
    alert('⚠️ Facebook Login is being configured.\n\nPlease use traditional login for now.');
    console.log('Facebook OAuth flow initiated');

    // In production, you would:
    // window.location.href = 'auth/facebook-callback.php';
}

async function loginWithApple() {
    // This would redirect to Apple Sign In
    alert('⚠️ Apple Login is being configured.\n\nPlease use traditional login for now.');
    console.log('Apple Sign In flow initiated');

    // In production, you would:
    // window.location.href = 'auth/apple-callback.php';
}

// ===== REMEMBER ME FUNCTIONALITY =====
function setupRememberMe() {
    const rememberCheckbox = document.getElementById('rememberMe');
    const emailInput = document.getElementById('email');

    // Load remembered email if exists
    const rememberedEmail = localStorage.getItem('rememberedEmail');
    if (rememberedEmail && emailInput) {
        emailInput.value = rememberedEmail;
        if (rememberCheckbox) {
            rememberCheckbox.checked = true;
        }
    }

    // Listen for login form submission
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        const originalSubmitHandler = loginForm.onsubmit;
        loginForm.addEventListener('submit', function () {
            // Save email if remember me is checked
            if (rememberCheckbox && rememberCheckbox.checked && emailInput) {
                localStorage.setItem('rememberedEmail', emailInput.value);
            } else {
                localStorage.removeItem('rememberedEmail');
            }
        });
    }
}

// Initialize remember me on page load
document.addEventListener('DOMContentLoaded', function () {
    setupRememberMe();
});

// ===== DATABASE CONNECTION TEST =====
async function testDatabaseConnection() {
    const messageDiv = document.getElementById('testResultMessage');
    const testBtn = document.querySelector('.btn-test-connection');

    if (!messageDiv || !testBtn) {
        console.error('Test elements not found');
        alert('❌ Error: Test elements not found on page');
        return;
    }

    // Show loading state
    testBtn.disabled = true;
    testBtn.textContent = '⏳ Testing...';
    messageDiv.innerHTML = '<p style="color: #fff;">Testing database connection...</p>';
    messageDiv.style.display = 'block';

    try {
        console.log('Fetching test_db_connection.php...');
        const response = await fetch('test_db_connection.php');
        console.log('Response status:', response.status);

        const text = await response.text();
        console.log('Raw response:', text);

        // Try to parse JSON
        let data;
        try {
            data = JSON.parse(text);
        } catch (parseError) {
            console.error('JSON parse error:', parseError);
            messageDiv.innerHTML = `
                <div style="background: rgba(255, 107, 107, 0.2); border: 2px solid #ff6b6b; padding: 1rem; border-radius: 8px;">
                    <h4 style="color: #ff6b6b; margin-top: 0;">❌ Response Parse Error</h4>
                    <div style="color: #ffb3b3; font-family: monospace; font-size: 0.85rem; word-break: break-word;">
                        <strong>Raw Response:</strong><br>${text.substring(0, 500)}
                    </div>
                    <p style="color: #999; font-size: 0.9rem; margin-top: 1rem;">
                        The server returned invalid JSON. Check browser console (F12) for details.
                    </p>
                </div>
            `;
            return;
        }

        // Build HTML response
        let html = '';

        if (data.status === 'success') {
            let tablesHtml = '';
            if (data.details.all_tables && data.details.all_tables.length > 0) {
                tablesHtml = '<p>📊 Tables found:</p><ul style="list-style: none; padding-left: 0;">';
                data.details.all_tables.forEach(table => {
                    tablesHtml += `<li style="margin: 0.3rem 0;">✅ ${table}</li>`;
                });
                tablesHtml += '</ul>';
            } else {
                tablesHtml = '<p style="color: #ffb347;">⚠️ No tables found - Database is empty</p>';
            }

            html = `
                <div style="background: rgba(0, 255, 136, 0.2); border: 2px solid #00ff88; padding: 1rem; border-radius: 8px;">
                    <h4 style="color: #00ff88; margin-top: 0; margin-bottom: 0.5rem;">✅ ${data.message}</h4>
                    <ul style="list-style: none; padding: 0; margin: 0.5rem 0 0 0; color: #ccc; font-size: 0.9rem;">
                        <li>🏠 Host: <strong>${data.details.host}</strong></li>
                        <li>👤 User: <strong>${data.details.user}</strong></li>
                        <li>📦 Database: <strong>${data.details.database}</strong></li>
                        <li>📊 MySQL Version: <strong>${data.details.mysql_version || 'Unknown'}</strong></li>
                        <li>📋 Users Table: <strong style="color: ${data.details.users_table.includes('✅') ? '#00ff88' : '#ffb347'}">${data.details.users_table}</strong></li>
                    </ul>
                    ${tablesHtml}
                </div>
            `;
        } else {
            html = `
                <div style="background: rgba(255, 107, 107, 0.2); border: 2px solid #ff6b6b; padding: 1rem; border-radius: 8px;">
                    <h4 style="color: #ff6b6b; margin-top: 0; margin-bottom: 0.5rem;">❌ ${data.message}</h4>
                    <div style="color: #ffb3b3; font-family: monospace; font-size: 0.85rem; word-break: break-word;">
                        <strong>Error:</strong> ${data.details.error || 'Unknown error'}
                    </div>
                    <p style="color: #999; font-size: 0.9rem; margin-top: 1rem;">
                        <strong>Troubleshooting Steps:</strong><br>
                        1. Check your DB_HOST in config.php (might need mysql.YOUR-DOMAIN.com for GoDaddy)<br>
                        2. Verify username and password are correct<br>
                        3. Ensure database exists on your GoDaddy account<br>
                        4. Check that credentials match exactly (spaces, special chars)<br>
                        5. Contact GoDaddy support if issues persist
                    </p>
                </div>
            `;
        }

        messageDiv.innerHTML = html;

    } catch (error) {
        console.error('Fetch error:', error);
        messageDiv.innerHTML = `
            <div style="background: rgba(255, 107, 107, 0.2); border: 2px solid #ff6b6b; padding: 1rem; border-radius: 8px;">
                <h4 style="color: #ff6b6b; margin-top: 0;">❌ Connection Test Error</h4>
                <div style="color: #ffb3b3; font-family: monospace; font-size: 0.85rem; word-break: break-word;">
                    ${error.message}
                </div>
                <p style="color: #999; font-size: 0.9rem; margin-top: 1rem;">
                    <strong>Check:</strong><br>
                    1. Open browser console (F12) to see full error<br>
                    2. Make sure test_db_connection.php file exists<br>
                    3. File path should be relative to login page
                </p>
            </div>
        `;
    } finally {
        // Restore button state
        testBtn.disabled = false;
        testBtn.textContent = '🔧 Test Database Connection';
    }
}
