<?php
// Security Headers for XSS Prevention
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data:;");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");

// Secure Session Configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.gc_maxlifetime', 1800); // 30 minutes

// Initialize Session
session_start();

// Session Security Functions
function validate_session_integrity() {
    if (!isset($_SESSION['ip_address']) || !isset($_SESSION['user_agent'])) {
        return false;
    }
    
    if ($_SESSION['ip_address'] !== $_SERVER['REMOTE_ADDR']) {
        return false;
    }
    
    if ($_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
        return false;
    }
    
    return true;
}

function check_session_timeout() {
    $session_timeout = 1800; // 30 minutes
    if (isset($_SESSION['login_time'])) {
        $elapsed_time = time() - $_SESSION['login_time'];
        if ($elapsed_time > $session_timeout) {
            session_destroy();
            return false;
        }
    }
    
    // Update last activity
    $_SESSION['last_activity'] = time();
    return true;
}

function require_login() {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['authenticated'])) {
        header('Location: login.admin');
        exit();
    }
    
    if (!validate_session_integrity()) {
        session_destroy();
        header('Location: login.admin?security=1');
        exit();
    }
    
    if (!check_session_timeout()) {
        header('Location: login.admin?timeout=1');
        exit();
    }
}

// Input Sanitization Functions
function sanitize_input($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function safe_output($output) {
    return htmlspecialchars($output, ENT_QUOTES, 'UTF-8');
}

// CSRF Protection Functions
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validate_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Shopping Cart Functions (Cookie-based)
function get_shopping_cart() {
    if (isset($_COOKIE['shopping_cart'])) {
        $cart = json_decode($_COOKIE['shopping_cart'], true);
        return is_array($cart) ? $cart : [];
    }
    return [];
}

function update_cart_cookie($cart_items) {
    $cart_json = json_encode($cart_items);
    $expiry_time = time() + (86400 * 30); // 30 days
    setcookie('shopping_cart', $cart_json, $expiry_time, '/', '', true, true);
}

function add_to_cart($product_id, $product_name, $price, $quantity = 1) {
    $cart = get_shopping_cart();
    
    if (isset($cart[$product_id])) {
        $cart[$product_id]['quantity'] += $quantity;
    } else {
        $cart[$product_id] = [
            'name' => sanitize_input($product_name),
            'price' => floatval($price),
            'quantity' => intval($quantity)
        ];
    }
    
    update_cart_cookie($cart);
    return count($cart);
}

function remove_from_cart($product_id) {
    $cart = get_shopping_cart();
    if (isset($cart[$product_id])) {
        unset($cart[$product_id]);
        update_cart_cookie($cart);
    }
    return count($cart);
}

function get_cart_total() {
    $cart = get_shopping_cart();
    $total = 0;
    foreach ($cart as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    return $total;
}

// User Preference Functions
function get_user_preference($preference, $default = null) {
    return $_COOKIE[$preference] ?? $default;
}

function set_user_preference($preference, $value) {
    $expiry_time = time() + (86400 * 365); // 1 year
    setcookie($preference, sanitize_input($value), $expiry_time, '/', '', true, true);
}

// Process Login/Logout Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'login':
                if (isset($_POST['email']) && isset($_POST['password']) && isset($_POST['csrf_token'])) {
                    if (validate_csrf_token($_POST['csrf_token'])) {
                        $email = sanitize_input($_POST['email']);
                        $password = sanitize_input($_POST['password']);
                        
                        // Simple validation (in production, use proper database authentication)
                        if (filter_var($email, FILTER_VALIDATE_EMAIL) && strlen($password) >= 6) {
                            // Regenerate session ID to prevent session fixation
                            session_regenerate_id(true);
                            
                            $_SESSION['user_id'] = uniqid();
                            $_SESSION['email'] = $email;
                            $_SESSION['authenticated'] = true;
                            $_SESSION['login_time'] = time();
                            $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
                            $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
                            $_SESSION['last_activity'] = time();
                            
                            // Set remember me cookie if checked
                            if (isset($_POST['remember']) && $_POST['remember'] === 'on') {
                                $remember_token = bin2hex(random_bytes(32));
                                setcookie('remember_token', $remember_token, time() + (86400 * 30), '/', '', true, true);
                                $_SESSION['remember_token'] = $remember_token;
                            }
                            
                            $login_success = true;
                        } else {
                            $login_error = 'Invalid email or password. Please try again.';
                        }
                    }
                }
                break;
                
            case 'signup':
                if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['csrf_token'])) {
                    if (validate_csrf_token($_POST['csrf_token'])) {
                        $name = sanitize_input($_POST['name']);
                        $email = sanitize_input($_POST['email']);
                        $password = sanitize_input($_POST['password']);
                        $confirm = sanitize_input($_POST['confirm']);
                        
                        // Validation
                        if (strlen($name) >= 2 && filter_var($email, FILTER_VALIDATE_EMAIL) && 
                            strlen($password) >= 6 && $password === $confirm) {
                            
                            // Regenerate session ID
                            session_regenerate_id(true);
                            
                            $_SESSION['user_id'] = uniqid();
                            $_SESSION['name'] = $name;
                            $_SESSION['email'] = $email;
                            $_SESSION['authenticated'] = true;
                            $_SESSION['login_time'] = time();
                            $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
                            $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
                            $_SESSION['last_activity'] = time();
                            
                            $signup_success = true;
                        } else {
                            $signup_error = 'Please check your input. Name must be at least 2 characters, email must be valid, and password must be at least 6 characters with matching confirmation.';
                        }
                    }
                }
                break;
                
            case 'logout':
                if (isset($_POST['csrf_token']) && validate_csrf_token($_POST['csrf_token'])) {
                    session_destroy();
                    setcookie('remember_token', '', time() - 3600, '/');
                    header('Location: ' . $_SERVER['PHP_SELF']);
                    exit();
                }
                break;
                
            case 'add_to_cart':
                if (isset($_POST['product_id']) && isset($_POST['product_name']) && isset($_POST['price']) && isset($_POST['csrf_token'])) {
                    if (validate_csrf_token($_POST['csrf_token'])) {
                        $product_id = sanitize_input($_POST['product_id']);
                        $product_name = sanitize_input($_POST['product_name']);
                        $price = floatval($_POST['price']);
                        
                        $cart_count = add_to_cart($product_id, $product_name, $price);
                        $cart_success = true;
                    }
                }
                break;
                
            case 'remove_from_cart':
                if (isset($_POST['product_id']) && isset($_POST['csrf_token'])) {
                    if (validate_csrf_token($_POST['csrf_token'])) {
                        $product_id = sanitize_input($_POST['product_id']);
                        $cart_count = remove_from_cart($product_id);
                        header('Location: ' . $_SERVER['PHP_SELF']);
                        exit();
                    }
                }
                break;
                
            case 'contact':
                if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['subject']) && isset($_POST['message']) && isset($_POST['csrf_token'])) {
                    if (validate_csrf_token($_POST['csrf_token'])) {
                        $name = sanitize_input($_POST['name']);
                        $email = sanitize_input($_POST['email']);
                        $subject = sanitize_input($_POST['subject']);
                        $message = sanitize_input($_POST['message']);
                        
                        // In production, send email or save to database
                        $contact_success = true;
                    }
                }
                break;
                
            case 'newsletter':
                if (isset($_POST['email']) && isset($_POST['csrf_token'])) {
                    if (validate_csrf_token($_POST['csrf_token'])) {
                        $email = sanitize_input($_POST['email']);
                        
                        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            // In production, save to newsletter database
                            $newsletter_success = true;
                        } else {
                            $newsletter_error = 'Please enter a valid email address.';
                        }
                    }
                }
                break;
        }
    }
}

// Check for remember me token
if (!isset($_SESSION['authenticated']) && isset($_COOKIE['remember_token'])) {
    // In production, validate remember token against database
    // For demo purposes, we'll skip this
}

// Set the page title dynamically
$page_title = 'ShopHub - Modern E-Commerce';

// Generate CSRF token for forms
$csrf_token = generate_csrf_token();

// Get user preferences
$theme = get_user_preference('theme', 'light');
$language = get_user_preference('language', 'en');
$currency = get_user_preference('currency', 'USD');

// Get shopping cart data
$cart = get_shopping_cart();
$cart_count = array_sum(array_column($cart, 'quantity'));
$cart_total = get_cart_total();

// Check if user is logged in
$is_logged_in = isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true;
$user_name = $_SESSION['name'] ?? $_SESSION['email'] ?? 'Guest';

?>

<link rel="stylesheet" href="includes/main.css">
<link rel="stylesheet" href="includes/footer.css">

<?php include('includes/header.html'); ?>

<!-- Navigation Bar -->
<nav class="navbar">
    <div class="nav-container">
        <div class="nav-logo">
            <h2>ShopHub</h2>
        </div>
        <ul class="nav-menu">
            <li><a href="#home">Home</a></li>
            <li><a href="#shop">Shop</a></li>
            <li><a href="#categories">Categories</a></li>
            <li><a href="#deals">Deals</a></li>
            <li><a href="#contact">Contact</a></li>
        </ul>
        <div class="nav-actions">
            <button class="btn btn-search" onclick="toggleSearch()">🔍</button>
            <button class="btn btn-cart" onclick="toggleCart()">🛒 Cart (<?php echo $cart_count; ?>)</button>
            <?php if ($is_logged_in): ?>
                <div class="user-menu">
                    <span class="welcome-user">Welcome, <?php echo safe_output($user_name); ?>!</span>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="logout">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        <button type="submit" class="btn btn-logout">Logout</button>
                    </form>
                </div>
            <?php else: ?>
                <button class="btn btn-login" onclick="showLoginModal()">Login</button>
                <button class="btn btn-signup" onclick="showSignupModal()">Sign Up</button>
            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- Search Bar (Hidden by default) -->
<div id="searchBar" class="search-bar" style="display: none;">
    <div class="search-container">
        <input type="text" placeholder="Search products..." class="search-input">
        <button onclick="toggleSearch()" class="close-search">×</button>
    </div>
</div>

<main>
    <!-- Hero Section -->
    <section id="home" class="hero">
        <div class="hero-content">
            <h1>Welcome to ShopHub</h1>
            <p>Your one-stop destination for quality products at amazing prices</p>
            <div class="hero-buttons">
                <button class="btn btn-primary btn-large" onclick="scrollToSection('shop')">Shop Now</button>
                <button class="btn btn-secondary btn-large" onclick="showSignupModal()">Get 10% Off</button>
            </div>
        </div>
        <div class="hero-image">
            <div class="product-showcase">
                <div class="product-card featured">
                    <div class="product-badge">Hot Deal</div>
                    <div class="product-img">📱</div>
                    <h3>Smartphones</h3>
                    <p class="price">From $299</p>
                </div>
                <div class="product-card">
                    <div class="product-img">💻</div>
                    <h3>Laptops</h3>
                    <p class="price">From $599</p>
                </div>
                <div class="product-card">
                    <div class="product-img">🎧</div>
                    <h3>Headphones</h3>
                    <p class="price">From $49</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Categories -->
    <section id="categories" class="categories">
        <h2>Shop by Category</h2>
        <div class="category-grid">
            <div class="category-card" onclick="browseCategory('electronics')">
                <div class="category-icon">📱</div>
                <h3>Electronics</h3>
                <p>2,456 Products</p>
            </div>
            <div class="category-card" onclick="browseCategory('fashion')">
                <div class="category-icon">👕</div>
                <h3>Fashion</h3>
                <p>3,892 Products</p>
            </div>
            <div class="category-card" onclick="browseCategory('home')">
                <div class="category-icon">🏠</div>
                <h3>Home & Garden</h3>
                <p>1,234 Products</p>
            </div>
            <div class="category-card" onclick="browseCategory('sports')">
                <div class="category-icon">⚽</div>
                <h3>Sports</h3>
                <p>987 Products</p>
            </div>
            <div class="category-card" onclick="browseCategory('books')">
                <div class="category-icon">📚</div>
                <h3>Books</h3>
                <p>5,678 Products</p>
            </div>
            <div class="category-card" onclick="browseCategory('toys')">
                <div class="category-icon">🎮</div>
                <h3>Toys & Games</h3>
                <p>1,456 Products</p>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section id="shop" class="featured-products">
        <h2>Featured Products</h2>
        <div class="product-grid">
            <div class="product-item">
                <div class="product-image">📱</div>
                <div class="product-info">
                    <h3>iPhone 14 Pro</h3>
                    <div class="rating">⭐⭐⭐⭐⭐ (4.8)</div>
                    <p class="description">Latest iPhone with advanced camera system</p>
                    <div class="price-row">
                        <span class="price">$999</span>
                        <span class="old-price">$1,099</span>
                    </div>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="add_to_cart">
                        <input type="hidden" name="product_id" value="iphone14pro">
                        <input type="hidden" name="product_name" value="iPhone 14 Pro">
                        <input type="hidden" name="price" value="999">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        <button type="submit" class="btn btn-add-cart">Add to Cart</button>
                    </form>
                </div>
            </div>
            <div class="product-item">
                <div class="product-image">💻</div>
                <div class="product-info">
                    <h3>MacBook Air M2</h3>
                    <div class="rating">⭐⭐⭐⭐⭐ (4.9)</div>
                    <p class="description">Powerful laptop with M2 chip</p>
                    <div class="price-row">
                        <span class="price">$1,199</span>
                        <span class="old-price">$1,399</span>
                    </div>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="add_to_cart">
                        <input type="hidden" name="product_id" value="macbook_air_m2">
                        <input type="hidden" name="product_name" value="MacBook Air M2">
                        <input type="hidden" name="price" value="1199">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        <button type="submit" class="btn btn-add-cart">Add to Cart</button>
                    </form>
                </div>
            </div>
            <div class="product-item">
                <div class="product-image">🎧</div>
                <div class="product-info">
                    <h3>AirPods Pro</h3>
                    <div class="rating">⭐⭐⭐⭐ (4.6)</div>
                    <p class="description">Wireless earbuds with noise cancellation</p>
                    <div class="price-row">
                        <span class="price">$249</span>
                        <span class="old-price">$299</span>
                    </div>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="add_to_cart">
                        <input type="hidden" name="product_id" value="airpods_pro">
                        <input type="hidden" name="product_name" value="AirPods Pro">
                        <input type="hidden" name="price" value="249">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        <button type="submit" class="btn btn-add-cart">Add to Cart</button>
                    </form>
                </div>
            </div>
            <div class="product-item">
                <div class="product-image">⌚</div>
                <div class="product-info">
                    <h3>Apple Watch Series 8</h3>
                    <div class="rating">⭐⭐⭐⭐⭐ (4.7)</div>
                    <p class="description">Advanced health and fitness features</p>
                    <div class="price-row">
                        <span class="price">$399</span>
                        <span class="old-price">$449</span>
                    </div>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="add_to_cart">
                        <input type="hidden" name="product_id" value="apple_watch_8">
                        <input type="hidden" name="product_name" value="Apple Watch Series 8">
                        <input type="hidden" name="price" value="399">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        <button type="submit" class="btn btn-add-cart">Add to Cart</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Special Deals -->
    <section id="deals" class="deals">
        <h2>🔥 Hot Deals - Limited Time Offers</h2>
        <div class="deals-grid">
            <div class="deal-card">
                <div class="deal-badge">50% OFF</div>
                <div class="deal-image">📱</div>
                <h3>Flash Sale</h3>
                <p>Selected smartphones</p>
                <div class="countdown" id="countdown1">Ends in: 02:34:56</div>
                <button class="btn btn-deal" onclick="viewDeal('flash-sale')">Shop Now</button>
            </div>
            <div class="deal-card">
                <div class="deal-badge">30% OFF</div>
                <div class="deal-image">💻</div>
                <h3>Laptop Deals</h3>
                <p>Top brands included</p>
                <div class="countdown" id="countdown2">Ends in: 05:12:34</div>
                <button class="btn btn-deal" onclick="viewDeal('laptop-deals')">Shop Now</button>
            </div>
            <div class="deal-card">
                <div class="deal-badge">Buy 2 Get 1</div>
                <div class="deal-image">🎧</div>
                <h3>Audio Special</h3>
                <p>Headphones & speakers</p>
                <div class="countdown" id="countdown3">Ends in: 01:45:23</div>
                <button class="btn btn-deal" onclick="viewDeal('audio-special')">Shop Now</button>
            </div>
        </div>
    </section>

    <!-- Newsletter Signup -->
    <section class="newsletter">
        <div class="newsletter-content">
            <h2>Get Exclusive Offers</h2>
            <p>Subscribe to our newsletter and be the first to know about new products and special deals</p>
            <?php if (isset($newsletter_success) && $newsletter_success): ?>
                <div class="success-message">
                    <p>✅ Thank you for subscribing! Check your email for a 10% discount code.</p>
                </div>
            <?php else: ?>
                <?php if (isset($newsletter_error)): ?>
                    <div class="error-message">
                        <p>❌ <?php echo safe_output($newsletter_error); ?></p>
                    </div>
                <?php endif; ?>
                <form method="POST" class="newsletter-form">
                    <input type="hidden" name="action" value="newsletter">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <input type="email" name="email" placeholder="Enter your email" required>
                    <button type="submit" class="btn btn-primary">Subscribe</button>
                </form>
            <?php endif; ?>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact">
        <h2>Contact Us</h2>
        <div class="contact-container">
            <div class="contact-info">
                <h3>Get in Touch</h3>
                <p><strong>Customer Service:</strong> support@shophub.com</p>
                <p><strong>Phone:</strong> 1-800-SHOP-HUB</p>
                <p><strong>Hours:</strong> Mon-Fri 9AM-8PM, Sat-Sun 10AM-6PM</p>
                <div class="social-links">
                    <a href="#" class="social-link">📘 Facebook</a>
                    <a href="#" class="social-link">🐦 Twitter</a>
                    <a href="#" class="social-link">📷 Instagram</a>
                    <a href="#" class="social-link">💼 LinkedIn</a>
                </div>
            </div>
            <div class="contact-form">
                <h3>Send us a Message</h3>
                <?php if (isset($contact_success) && $contact_success): ?>
                    <div class="success-message">
                        <p>✅ Message sent successfully! We'll get back to you soon.</p>
                    </div>
                <?php else: ?>
                    <form method="POST">
                        <input type="hidden" name="action" value="contact">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        <div class="form-group">
                            <label for="contact-name">Name:</label>
                            <input type="text" id="contact-name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="contact-email">Email:</label>
                            <input type="email" id="contact-email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="contact-subject">Subject:</label>
                            <select id="contact-subject" name="subject" required>
                                <option value="">Select a topic</option>
                                <option value="order">Order Status</option>
                                <option value="return">Returns & Refunds</option>
                                <option value="product">Product Information</option>
                                <option value="technical">Technical Support</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="contact-message">Message:</label>
                            <textarea id="contact-message" name="message" rows="4" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Send Message</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>

<!-- Shopping Cart Sidebar -->
<div id="cartSidebar" class="cart-sidebar">
    <div class="cart-header">
        <h3>Shopping Cart</h3>
        <button onclick="toggleCart()" class="close-cart">×</button>
    </div>
    <div class="cart-items" id="cartItems">
        <?php if (empty($cart)): ?>
            <p class="empty-cart">Your cart is empty</p>
        <?php else: ?>
            <?php foreach ($cart as $product_id => $item): ?>
                <div class="cart-item">
                    <span><?php echo safe_output($item['name']); ?></span>
                    <span>$<?php echo number_format($item['price'], 2); ?> x <?php echo $item['quantity']; ?></span>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="remove_from_cart">
                        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        <button type="submit" class="remove-item" onclick="return confirm('Remove this item from cart?')">×</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <div class="cart-footer">
        <div class="cart-total">
            <strong>Total: $<?php echo number_format($cart_total, 2); ?></strong>
        </div>
        <button class="btn btn-primary" onclick="checkout()">Checkout</button>
        <button class="btn btn-secondary" onclick="toggleCart()">Continue Shopping</button>
    </div>
</div>

<!-- Login Modal -->
<div id="loginModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Login</h2>
            <button onclick="closeModal('loginModal')" class="close-modal">×</button>
        </div>
        <?php if (isset($login_success) && $login_success): ?>
            <div class="success-message">
                <p>✅ Login successful! Welcome back.</p>
                <script>setTimeout(() => { window.location.reload(); }, 1500);</script>
            </div>
        <?php else: ?>
            <?php if (isset($login_error)): ?>
                <div class="error-message">
                    <p>❌ <?php echo safe_output($login_error); ?></p>
                </div>
            <?php endif; ?>
            <form method="POST">
                <input type="hidden" name="action" value="login">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <div class="form-group">
                    <label for="login-email">Email:</label>
                    <input type="email" id="login-email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="login-password">Password:</label>
                    <input type="password" id="login-password" name="password" required>
                </div>
                <div class="form-options">
                    <label>
                        <input type="checkbox" name="remember"> Remember me
                    </label>
                    <a href="#" class="forgot-password">Forgot password?</a>
                </div>
                <button type="submit" class="btn btn-primary">Login</button>
                <p class="switch-form">
                    Don't have an account? <a href="#" onclick="switchToSignup()">Sign up</a>
                </p>
            </form>
        <?php endif; ?>
    </div>
</div>

<!-- Signup Modal -->
<div id="signupModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Sign Up</h2>
            <button onclick="closeModal('signupModal')" class="close-modal">×</button>
        </div>
        <?php if (isset($signup_success) && $signup_success): ?>
            <div class="success-message">
                <p>✅ Account created successfully! Welcome to ShopHub.</p>
                <script>setTimeout(() => { window.location.reload(); }, 1500);</script>
            </div>
        <?php else: ?>
            <?php if (isset($signup_error)): ?>
                <div class="error-message">
                    <p>❌ <?php echo safe_output($signup_error); ?></p>
                </div>
            <?php endif; ?>
            <form method="POST">
                <input type="hidden" name="action" value="signup">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <div class="form-group">
                    <label for="signup-name">Full Name:</label>
                    <input type="text" id="signup-name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="signup-email">Email:</label>
                    <input type="email" id="signup-email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="signup-password">Password:</label>
                    <input type="password" id="signup-password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="signup-confirm">Confirm Password:</label>
                    <input type="password" id="signup-confirm" name="confirm" required>
                </div>
                <div class="form-options">
                    <label>
                        <input type="checkbox" name="newsletter" checked> Subscribe to newsletter
                    </label>
                </div>
                <button type="submit" class="btn btn-primary">Sign Up</button>
                <p class="switch-form">
                    Already have an account? <a href="#" onclick="switchToLogin()">Login</a>
                </p>
            </form>
        <?php endif; ?>
    </div>
</div>

<!-- JavaScript for E-commerce Functionality -->
<script>
// Shopping Cart
let cart = [];
let cartCount = 0;

function addToCart(product, price) {
    cart.push({ name: product, price: price, quantity: 1 });
    cartCount++;
    updateCartDisplay();
    showNotification(`${product} added to cart!`);
}

function updateCartDisplay() {
    const cartButton = document.querySelector('.btn-cart');
    cartButton.textContent = `🛒 Cart (${cartCount})`;
    
    const cartItems = document.getElementById('cartItems');
    const cartTotal = document.getElementById('cartTotal');
    
    if (cart.length === 0) {
        cartItems.innerHTML = '<p class="empty-cart">Your cart is empty</p>';
        cartTotal.textContent = '0.00';
    } else {
        let total = 0;
        cartItems.innerHTML = cart.map((item, index) => {
            total += item.price;
            return `
                <div class="cart-item">
                    <span>${item.name}</span>
                    <span>$${item.price}</span>
                    <button onclick="removeFromCart(${index})" class="remove-item">×</button>
                </div>
            `;
        }).join('');
        cartTotal.textContent = total.toFixed(2);
    }
}

function removeFromCart(index) {
    cart.splice(index, 1);
    cartCount--;
    updateCartDisplay();
}

function toggleCart() {
    const cartSidebar = document.getElementById('cartSidebar');
    cartSidebar.style.display = cartSidebar.style.display === 'block' ? 'none' : 'block';
}

function checkout() {
    if (cart.length === 0) {
        showNotification('Your cart is empty!');
        return;
    }
    showNotification('Proceeding to checkout...');
    // Redirect to checkout page
}

// Search functionality
function toggleSearch() {
    const searchBar = document.getElementById('searchBar');
    searchBar.style.display = searchBar.style.display === 'block' ? 'none' : 'block';
    if (searchBar.style.display === 'block') {
        document.querySelector('.search-input').focus();
    }
}

// Modal functions
function showLoginModal() {
    document.getElementById('loginModal').style.display = 'block';
}

function showSignupModal() {
    document.getElementById('signupModal').style.display = 'block';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

function switchToSignup() {
    closeModal('loginModal');
    showSignupModal();
}

function switchToLogin() {
    closeModal('signupModal');
    showLoginModal();
}

// Form handlers
function handleLogin(event) {
    event.preventDefault();
    showNotification('Login successful! Welcome back.');
    closeModal('loginModal');
}

function handleSignup(event) {
    event.preventDefault();
    showNotification('Account created successfully! Welcome to ShopHub.');
    closeModal('signupModal');
}

function submitContact(event) {
    event.preventDefault();
    showNotification('Message sent successfully! We\'ll get back to you soon.');
    event.target.reset();
}

function subscribeNewsletter(event) {
    event.preventDefault();
    showNotification('Thank you for subscribing! Check your email for a 10% discount code.');
    event.target.reset();
}

// Navigation and utility functions
function scrollToSection(sectionId) {
    document.getElementById(sectionId).scrollIntoView({ behavior: 'smooth' });
}

function browseCategory(category) {
    showNotification(`Browsing ${category} products...`);
}

function viewDeal(dealId) {
    showNotification(`Loading ${dealId} deals...`);
}

function showNotification(message) {
    const notification = document.createElement('div');
    notification.className = 'notification';
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Close modals when clicking outside
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
}

// Countdown timers for deals
function updateCountdowns() {
    const countdowns = document.querySelectorAll('.countdown');
    countdowns.forEach((countdown, index) => {
        const time = countdown.textContent.match(/(\d+):(\d+):(\d+)/);
        if (time) {
            let hours = parseInt(time[1]);
            let minutes = parseInt(time[2]);
            let seconds = parseInt(time[3]);
            
            seconds--;
            if (seconds < 0) {
                seconds = 59;
                minutes--;
                if (minutes < 0) {
                    minutes = 59;
                    hours--;
                    if (hours < 0) {
                        hours = 23;
                    }
                }
            }
            
            countdown.textContent = `Ends in: ${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
        }
    });
}

setInterval(updateCountdowns, 1000);
</script>


<?php include('includes/footer.html'); ?>
