<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($product['product_name']) ?> | CREATRIX</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('assets/creatrix_logo.png') ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('assets/creatrix_logo.png') ?>">
    
    <style>
        :root {
            --bg: #F0F4F1;
            --card-bg: #FFFFFF;
            --primary: #1F4529;
            --primary-light: #477A53;
            --accent: #2A5C37;
            --accent-light: #DCE7E0;
            --accent-hover: #16381F;
            --text: #2C3B31;
            --text-light: #647368;
            --white: #FFFFFF;
            --shadow-sm: 0 4px 6px rgba(31, 69, 41, 0.04);
            --shadow-md: 0 10px 20px rgba(31, 69, 41, 0.08);
            --shadow-hover: 0 15px 30px rgba(42, 92, 55, 0.15);
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg);
            color: var(--text);
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        /* HEADER & NAVIGATION */
        header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: var(--white);
            padding: 15px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            box-shadow: var(--shadow-md);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo img {
            height: 40px;
            filter: brightness(0) invert(1);
        }

        .logo h2 {
            font-weight: 600;
            font-size: 1.5rem;
            margin: 0;
        }

        /* Desktop Navigation */
        nav {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        nav a {
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            font-weight: 500;
            padding: 8px 18px;
            border-radius: 25px;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.95rem;
        }

        nav a:hover {
            background: rgba(255, 255, 255, 0.15);
            color: var(--white);
        }

        nav a.active {
            background: var(--white);
            color: var(--primary);
            font-weight: 600;
            box-shadow: var(--shadow-sm);
        }

        .cart-badge {
            background: var(--accent);
            color: white;
            border-radius: 50%;
            padding: 2px 8px;
            font-size: 0.75rem;
            margin-left: 2px;
            font-weight: 700;
        }

        /* Hamburger Menu Button */
        .hamburger {
            display: none;
            background: none;
            border: none;
            color: var(--white);
            font-size: 1.8rem;
            cursor: pointer;
            transition: var(--transition);
            padding: 8px;
            z-index: 1001;
            align-items: center;
            justify-content: center;
            width: 45px;
            height: 45px;
            border-radius: 50%;
        }

        .hamburger:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: scale(1.05);
        }

        /* Overlay for mobile menu */
        .menu-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            transition: all 0.3s;
        }

        .menu-overlay.active {
            display: block;
        }

        /* Mobile Menu */
        .mobile-menu {
            position: fixed;
            top: 0;
            right: -100%;
            width: 85%;
            max-width: 320px;
            height: 100vh;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            box-shadow: -5px 0 30px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            transition: right 0.3s ease;
            padding: 80px 20px 30px;
            overflow-y: auto;
        }

        .mobile-menu.active {
            right: 0;
        }

        .mobile-menu nav {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-bottom: 30px;
        }

        .mobile-menu nav a {
            color: var(--white);
            text-decoration: none;
            padding: 14px 20px;
            border-radius: 12px;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1rem;
            font-weight: 500;
        }

        .mobile-menu nav a:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateX(5px);
        }

        .mobile-menu .mobile-btns {
            display: flex;
            flex-direction: column;
            gap: 12px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
        }

        .mobile-menu .mobile-btns a {
            text-align: center;
            padding: 14px 20px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .mobile-menu .mobile-login {
            background: transparent;
            border: 2px solid var(--white);
            color: var(--white);
        }

        .mobile-menu .mobile-login:hover {
            background: var(--white);
            color: var(--primary);
        }

        .mobile-menu .mobile-register {
            background: var(--accent);
            color: var(--white);
            border: 2px solid var(--accent);
        }

        .mobile-menu .mobile-register:hover {
            background: var(--accent-hover);
        }

        .container {
            max-width: 95%;
            margin: 40px auto;
            padding: 0 20px;
        }

        /* PRODUCT DETAILS */
        .product-details {
            background: var(--card-bg);
            border-radius: 30px;
            padding: 40px;
            box-shadow: var(--shadow-md);
            margin-bottom: 50px;
        }

        .product-image-large {
            width: 100%;
            height: auto;
            aspect-ratio: 4 / 3;
            background: var(--accent-light);
            border-radius: 20px;
            overflow: hidden;
            border: 3px solid var(--accent);
            box-shadow: var(--shadow-md);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .product-image-large img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            transition: transform 0.6s;
        }

        .product-image-large:hover img {
            transform: scale(1.05);
        }

        .product-category-badge {
            display: inline-block;
            background: var(--accent-light);
            color: var(--accent);
            padding: 8px 16px;
            border-radius: 25px;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .product-title-large {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 15px;
        }

        .product-price-large {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--accent);
            margin-bottom: 20px;
        }

        .product-stock {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 25px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .stock-success {
            background: #d4edda;
            color: #155724;
        }

        .stock-warning {
            background: #fff3cd;
            color: #856404;
        }

        .stock-danger {
            background: #f8d7da;
            color: #721c24;
        }

        .product-description {
            font-size: 1.1rem;
            line-height: 1.8;
            color: var(--text);
            margin-bottom: 30px;
            padding: 20px 0;
            border-top: 1px solid #E2E8F0;
            border-bottom: 1px solid #E2E8F0;
        }

        .quantity-selector {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .quantity-label {
            font-weight: 600;
            color: var(--text);
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            border: 2px solid var(--accent-light);
            border-radius: 12px;
            overflow: hidden;
        }

        .quantity-btn {
            width: 45px;
            height: 45px;
            background: var(--white);
            border: none;
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--accent);
            cursor: pointer;
            transition: var(--transition);
        }

        .quantity-btn:hover {
            background: var(--accent-light);
        }

        .quantity-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .quantity-input {
            width: 70px;
            height: 45px;
            border: none;
            border-left: 2px solid var(--accent-light);
            border-right: 2px solid var(--accent-light);
            text-align: center;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .variation-selector {
            margin-bottom: 20px;
        }
        
        .variation-selector label {
            font-weight: 600;
            display: block;
            margin-bottom: 8px;
        }
        
        .variation-options {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 8px;
        }
        
        .variation-option {
            padding: 8px 16px;
            border: 2px solid var(--accent-light);
            background: var(--white);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .variation-option:hover {
            border-color: var(--accent);
        }
        
        .variation-option.selected {
            background: var(--accent);
            color: var(--white);
            border-color: var(--accent);
        }

        .product-actions-large {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        
        .btn-add-cart-large,
        .btn-buy-now {
            flex: 1;
            background: var(--accent);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 15px;
            font-weight: 600;
            font-size: 1.1rem;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .btn-buy-now {
            background: var(--primary-light);
        }
        
        .btn-buy-now:hover {
            background: var(--primary);
        }

        /* RELATED PRODUCTS */
        .related-section {
            margin-top: 60px;
        }

        .section-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary);
            margin: 0 0 30px 0;
            text-align: center;
        }

        .related-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 25px;
        }

        .related-card {
            background: var(--card-bg);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            text-decoration: none;
            color: var(--text);
        }

        .related-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }

        .related-image {
            height: 180px;
            background: var(--accent-light);
            overflow: hidden;
        }

        .related-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s;
        }

        .related-card:hover .related-image img {
            transform: scale(1.1);
        }

        .related-info {
            padding: 20px;
        }

        .related-title {
            font-size: 1rem;
            font-weight: 600;
            margin: 0 0 8px 0;
            color: var(--text);
        }

        .related-price {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--accent);
            margin: 0;
        }

        /* TOAST NOTIFICATION */
        .toast-notification {
            position: fixed;
            top: 100px;
            right: 30px;
            background: var(--white);
            border-left: 4px solid var(--accent);
            border-radius: 10px;
            padding: 15px 25px;
            box-shadow: var(--shadow-hover);
            z-index: 1000;
            animation: slideIn 0.3s ease;
            max-width: 350px;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .toast-content {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .toast-icon {
            width: 40px;
            height: 40px;
            background: var(--accent-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--accent);
            font-size: 1.2rem;
        }

        .toast-message {
            flex: 1;
        }

        .toast-message h4 {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text);
            margin: 0 0 5px 0;
        }

        .toast-message p {
            font-size: 0.9rem;
            color: var(--text-light);
            margin: 0;
        }

        .toast-close {
            background: none;
            border: none;
            color: var(--text-light);
            cursor: pointer;
            font-size: 1.1rem;
        }

        footer {
            background: linear-gradient(135deg, var(--primary) 0%, #3D291D 100%);
            color: rgba(255,255,255,0.8);
            padding: 50px 5% 30px;
            margin-top: 70px;
            text-align: center;
            font-size: 0.95rem;
        }

        @media (max-width: 992px) {
            .related-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            nav {
                display: none;
            }
            
            .hamburger {
                display: flex;
            }
            
            header {
                justify-content: space-between;
            }
            
            .product-details {
                padding: 20px;
            }
            
            .product-title-large {
                font-size: 1.8rem;
            }
            
            .product-price-large {
                font-size: 1.8rem;
            }
            
            .product-actions-large {
                flex-direction: column;
            }
            
            .related-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            .logo img {
                height: 30px;
            }
            
            .logo h2 {
                font-size: 1rem;
            }
            
            .hamburger {
                width: 40px;
                height: 40px;
                font-size: 1.3rem;
            }
            
            .mobile-menu {
                width: 85%;
                max-width: 280px;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <img src="<?= base_url('assets/creatrix_logo.png') ?>" alt="CREATRIX Logo">
            <h2>CREATRIX COIR</h2>
        </div>
        
        <!-- Desktop Navigation -->
        <nav>
            <a href="<?= base_url('/storefront') ?>"><i class="fas fa-store"></i> Store</a>
            <a href="<?= base_url('/products') ?>"><i class="fas fa-box"></i> Products</a>
            <a href="<?= base_url('/cart') ?>">
                <i class="fas fa-shopping-cart"></i> Cart 
                <span class="cart-badge" id="cart-badge">
                    <?php 
                    if (session()->get('isLoggedIn')) {
                        $cartModel = new \App\Models\CartModel();
                        $cartItems = $cartModel->where('user_id', session()->get('id'))->findAll();
                        echo array_sum(array_column($cartItems, 'quantity'));
                    } else {
                        echo 0;
                    }
                    ?>
                </span>
            </a>
            <a href="<?= base_url('/transactions') ?>"><i class="fas fa-history"></i> My Orders</a>
            <?php if (session()->get('isLoggedIn')): ?>
                <a href="<?= base_url('/profile') ?>"><i class="fas fa-user"></i> <?= esc(session()->get('fullname')) ?></a>
                <a href="<?= base_url('/logout') ?>"><i class="fas fa-sign-out-alt"></i> Logout</a>
            <?php else: ?>
                <a href="<?= base_url('/login') ?>"><i class="fas fa-sign-in-alt"></i> Login</a>
                <a href="<?= base_url('/register') ?>"><i class="fas fa-user-plus"></i> Register</a>
            <?php endif; ?>
        </nav>

        <!-- Hamburger Button -->
        <button class="hamburger" id="hamburger">
            <i class="fas fa-bars"></i>
        </button>
    </header>

    <!-- Overlay -->
    <div class="menu-overlay" id="menuOverlay"></div>

    <!-- Mobile Menu -->
    <div class="mobile-menu" id="mobileMenu">
        <nav>
            <a href="<?= base_url('/storefront') ?>" class="mobile-nav-link"><i class="fas fa-store"></i> Store</a>
            <a href="<?= base_url('/products') ?>" class="mobile-nav-link"><i class="fas fa-box"></i> Products</a>
            <a href="<?= base_url('/cart') ?>" class="mobile-nav-link"><i class="fas fa-shopping-cart"></i> Cart</a>
            <a href="<?= base_url('/transactions') ?>" class="mobile-nav-link"><i class="fas fa-history"></i> My Orders</a>
            <a href="<?= base_url('/profile') ?>" class="mobile-nav-link"><i class="fas fa-user"></i> Profile</a>
        </nav>
        <div class="mobile-btns">
            <a href="<?= base_url('/login') ?>" class="mobile-login"><i class="fas fa-sign-in-alt"></i> Login</a>
            <a href="<?= base_url('/register') ?>" class="mobile-register"><i class="fas fa-user-plus"></i> Register</a>
        </div>
    </div>

    <div class="container">
        <div class="product-details">
            <div class="row">
                <div class="col-md-6 mb-4 mb-md-0">
                    <div class="product-image-large">
                        <img src="<?= base_url('uploads/products/' . ($product['image'] ?? 'default.jpg')) ?>" 
                             alt="<?= esc($product['product_name']) ?>"
                             onerror="this.src='https://via.placeholder.com/600x400/E8F0E9/436F4D?text=Coir+Product'">
                    </div>
                </div>

                <div class="col-md-6">
                    <span class="product-category-badge">
                        <i class="fas fa-tag me-2"></i><?= esc($product['category']) ?>
                    </span>
                    <h1 class="product-title-large"><?= esc($product['product_name']) ?></h1>
                    <div class="product-price-large" id="product-price">₱<?= number_format($product['price'], 2) ?></div>

                    <?php 
                    $stockClass = '';
                    $stockText = '';
                    if ($product['stock'] <= 0) {
                        $stockClass = 'stock-danger';
                        $stockText = 'Out of Stock';
                    } elseif ($product['stock'] < 10) {
                        $stockClass = 'stock-warning';
                        $stockText = 'Low Stock - Only ' . $product['stock'] . ' left';
                    } else {
                        $stockClass = 'stock-success';
                        $stockText = 'In Stock (' . $product['stock'] . ' available)';
                    }
                    ?>
                    <div class="product-stock <?= $stockClass ?>" id="product-stock">
                        <i class="fas <?= $product['stock'] <= 0 ? 'fa-times-circle' : ($product['stock'] < 10 ? 'fa-exclamation-circle' : 'fa-check-circle') ?> me-2"></i>
                        <?= $stockText ?>
                    </div>

                    <div class="product-description">
                        <?= nl2br(esc($product['description'])) ?>
                    </div>

                    <!-- Variation Choices -->
                    <?php if (!empty($variations)): ?>
                    <div class="variation-selector">
                        <label><?= esc($variations[0]['variation_name']) ?>:</label>
                        <div class="variation-options">
                            <?php foreach ($variations as $var): ?>
                                <button type="button" 
                                        class="variation-option" 
                                        data-id="<?= $var['id'] ?>" 
                                        data-price="<?= $var['price'] ?>" 
                                        data-stock="<?= $var['stock'] ?>"
                                        data-value="<?= esc($var['variation_value']) ?>">
                                    <?= esc($var['variation_value']) ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="quantity-selector">
                        <span class="quantity-label">Quantity:</span>
                        <div class="quantity-controls">
                            <button type="button" class="quantity-btn" id="decrease-qty" <?= $product['stock'] <= 0 ? 'disabled' : '' ?>>
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" class="quantity-input" id="quantity" value="1" min="1" max="<?= $product['stock'] ?>" readonly>
                            <button type="button" class="quantity-btn" id="increase-qty" <?= $product['stock'] <= 0 ? 'disabled' : '' ?>>
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        <span class="text-muted" id="max-stock-label">Max: <?= $product['stock'] ?></span>
                    </div>

                    <div class="product-actions-large">
                        <button class="btn-add-cart-large" id="add-to-cart-btn" data-product-id="<?= $product['id'] ?>">
                            <i class="fas fa-shopping-cart"></i> Add to Cart
                        </button>
                        <button class="btn-buy-now" id="buy-now-btn" data-product-id="<?= $product['id'] ?>">
                            <i class="fas fa-shopping-bag"></i> Buy Now
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        <?php if (!empty($relatedProducts)): ?>
        <div class="related-section">
            <h2 class="section-title">Related Products</h2>
            <div class="related-grid">
                <?php foreach ($relatedProducts as $related): ?>
                <a href="<?= base_url('/product-details/' . $related['id']) ?>" class="related-card">
                    <div class="related-image">
                        <img src="<?= base_url('uploads/products/' . ($related['image'] ?? 'default.jpg')) ?>" 
                             alt="<?= esc($related['product_name']) ?>"
                             onerror="this.src='https://via.placeholder.com/300x200/E8F0E9/436F4D?text=Coir'">
                    </div>
                    <div class="related-info">
                        <h3 class="related-title"><?= esc($related['product_name']) ?></h3>
                        <p class="related-price">₱<?= number_format($related['price'], 2) ?></p>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="toast-notification" style="display: none;">
        <div class="toast-content">
            <div class="toast-icon"><i class="fas fa-check"></i></div>
            <div class="toast-message">
                <h4 id="toast-title">Success!</h4>
                <p id="toast-message">Product added to cart</p>
            </div>
            <button class="toast-close" onclick="hideToast()"><i class="fas fa-times"></i></button>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 CREATRIX. For educational purposes only, and no copyright infringement is intended.</p>
        <p>CREATRIX Group - Promoting Sustainable Living Through Coconut Coir Innovation</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Hamburger menu toggle
        const hamburger = document.getElementById('hamburger');
        const mobileMenu = document.getElementById('mobileMenu');
        const menuOverlay = document.getElementById('menuOverlay');

        function openMenu() {
            mobileMenu.classList.add('active');
            menuOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
            const icon = hamburger.querySelector('i');
            icon.classList.remove('fa-bars');
            icon.classList.add('fa-times');
        }

        function closeMenu() {
            mobileMenu.classList.remove('active');
            menuOverlay.classList.remove('active');
            document.body.style.overflow = '';
            const icon = hamburger.querySelector('i');
            icon.classList.remove('fa-times');
            icon.classList.add('fa-bars');
        }

        hamburger.addEventListener('click', () => {
            if (mobileMenu.classList.contains('active')) {
                closeMenu();
            } else {
                openMenu();
            }
        });

        menuOverlay.addEventListener('click', closeMenu);

        // Close mobile menu when clicking on a link
        document.querySelectorAll('.mobile-nav-link').forEach(link => {
            link.addEventListener('click', () => {
                closeMenu();
            });
        });

        // Close menu on window resize (if screen becomes desktop)
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768 && mobileMenu.classList.contains('active')) {
                closeMenu();
            }
        });

        // DOM elements
        const quantityInput = document.getElementById('quantity');
        const decreaseBtn = document.getElementById('decrease-qty');
        const increaseBtn = document.getElementById('increase-qty');
        const addToCartBtn = document.getElementById('add-to-cart-btn');
        const buyNowBtn = document.getElementById('buy-now-btn');
        const priceElement = document.getElementById('product-price');
        const stockElement = document.getElementById('product-stock');
        const maxStockLabel = document.getElementById('max-stock-label');

        // Variation buttons
        const variationButtons = document.querySelectorAll('.variation-option');
        let selectedVariationId = null;
        let currentStock = <?= $product['stock'] ?>;
        let currentPrice = <?= $product['price'] ?>;

        // Update UI based on selected variation
        function updateVariationFromButton(button) {
            variationButtons.forEach(btn => btn.classList.remove('selected'));
            button.classList.add('selected');
            selectedVariationId = button.dataset.id;

            const price = parseFloat(button.dataset.price);
            const stock = parseInt(button.dataset.stock);
            currentPrice = price;
            currentStock = stock;

            priceElement.innerHTML = '₱' + price.toFixed(2);
            maxStockLabel.innerHTML = 'Max: ' + stock;
            quantityInput.max = stock;
            if (parseInt(quantityInput.value) > stock) quantityInput.value = stock;

            if (stock <= 0) {
                stockElement.innerHTML = '<i class="fas fa-times-circle"></i> Out of Stock';
                stockElement.className = 'product-stock stock-danger';
                addToCartBtn.disabled = true;
                buyNowBtn.disabled = true;
            } else if (stock < 10) {
                stockElement.innerHTML = '<i class="fas fa-exclamation-circle"></i> Low Stock - Only ' + stock + ' left';
                stockElement.className = 'product-stock stock-warning';
                addToCartBtn.disabled = false;
                buyNowBtn.disabled = false;
            } else {
                stockElement.innerHTML = '<i class="fas fa-check-circle"></i> In Stock (' + stock + ' available)';
                stockElement.className = 'product-stock stock-success';
                addToCartBtn.disabled = false;
                buyNowBtn.disabled = false;
            }
        }

        if (variationButtons.length > 0) {
            variationButtons.forEach(btn => {
                btn.addEventListener('click', () => updateVariationFromButton(btn));
            });
            updateVariationFromButton(variationButtons[0]);
        }

        // Quantity buttons
        decreaseBtn.addEventListener('click', function() {
            let val = parseInt(quantityInput.value);
            if (val > 1) quantityInput.value = val - 1;
        });
        increaseBtn.addEventListener('click', function() {
            let val = parseInt(quantityInput.value);
            if (val < currentStock) quantityInput.value = val + 1;
        });

        // Add to Cart
        function addToCart(productId, quantity) {
            const variationId = selectedVariationId || null;
            const url = '<?= base_url('cart/add') ?>/' + productId;
            const body = new URLSearchParams({
                quantity: quantity,
                variation_id: variationId || ''
            });

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: body.toString()
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('cart-badge').textContent = data.cartCount;
                    showToast('Added to Cart!', data.message);
                } else {
                    showToast('Error', data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Error', 'Failed to add to cart', 'error');
            });
        }

        // Buy Now
        function buyNow(productId, quantity) {
            const variationId = selectedVariationId || null;
            const url = '<?= base_url('cart/buy-now') ?>/' + productId;
            const body = new URLSearchParams({
                quantity: quantity,
                variation_id: variationId || ''
            });

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: body.toString()
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('cart-badge').textContent = data.cartCount;
                    window.location.href = '<?= base_url('/checkout') ?>';
                } else {
                    showToast('Error', data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Error', 'Failed to process', 'error');
            });
        }

        // Add to Cart button click
        addToCartBtn.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const quantity = quantityInput.value;
            
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
            this.disabled = true;
            
            addToCart(productId, quantity);
            
            setTimeout(() => {
                this.innerHTML = originalText;
                this.disabled = false;
            }, 1000);
        });

        // Buy Now button click
        buyNowBtn.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const quantity = quantityInput.value;
            
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            this.disabled = true;
            
            buyNow(productId, quantity);
        });

        // Toast functions
        function showToast(title, message, type = 'success') {
            const toast = document.getElementById('toast');
            const toastTitle = document.getElementById('toast-title');
            const toastMessage = document.getElementById('toast-message');
            const toastIcon = document.querySelector('.toast-icon i');
            if (type === 'success') {
                toast.style.borderLeftColor = 'var(--accent)';
                toastIcon.className = 'fas fa-check';
                toastIcon.style.color = 'var(--accent)';
            } else {
                toast.style.borderLeftColor = '#dc3545';
                toastIcon.className = 'fas fa-exclamation-triangle';
                toastIcon.style.color = '#dc3545';
            }
            toastTitle.textContent = title;
            toastMessage.textContent = message;
            toast.style.display = 'block';
            setTimeout(hideToast, 3000);
        }
        
        function hideToast() {
            document.getElementById('toast').style.display = 'none';
        }
    </script>
</body>
</html>