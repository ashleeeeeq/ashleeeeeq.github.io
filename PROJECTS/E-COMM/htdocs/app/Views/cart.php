<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart | CREATRIX</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

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
            --shadow-sm: 0 4px 12px rgba(31, 69, 41, 0.04);
            --shadow-md: 0 8px 24px rgba(31, 69, 41, 0.06);
            --shadow-hover: 0 16px 32px rgba(42, 92, 55, 0.1);
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg);
            color: var(--text);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

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

        /* Desktop Navigation - Keep original */
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
            padding: 2px 6px;
            font-size: 0.7rem;
            margin-left: 4px;
            font-weight: 600;
        }

        /* Hamburger Menu Button - Mobile only */
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

        /* Mobile Menu - Slides from right */
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
            max-width: 1400px;
            margin: 40px auto;
            padding: 0 24px;
            flex: 1;
        }

        .page-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .page-header h1 {
            font-size: 2rem;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 8px;
        }

        .page-header p {
            font-size: 0.95rem;
            color: var(--text-light);
        }

        .cart-container {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
        }

        .cart-items {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 24px;
            box-shadow: var(--shadow-sm);
        }

        .cart-header {
            display: grid;
            grid-template-columns: 40px 1fr 120px 120px 120px 50px;
            padding: 12px 0;
            border-bottom: 1px solid var(--accent-light);
            font-weight: 600;
            color: var(--primary);
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            gap: 15px;
        }

        .cart-item {
            display: grid;
            grid-template-columns: 40px 1fr 120px 120px 120px 50px;
            align-items: center;
            padding: 20px 0;
            border-bottom: 1px solid var(--accent-light);
            gap: 15px;
        }

        .cart-item:last-child {
            border-bottom: none;
        }

        .product-info {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .product-image {
            width: 80px;
            height: 80px;
            border-radius: 12px;
            overflow: hidden;
            background: var(--accent-light);
            flex-shrink: 0;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-details h4 {
            font-size: 1rem;
            font-weight: 600;
            margin: 0 0 4px 0;
            color: var(--text);
        }

        .item-variant {
            font-size: 0.75rem;
            color: var(--accent);
            font-weight: 500;
            margin-bottom: 4px;
        }

        .stock-info {
            font-size: 0.7rem;
            color: var(--text-light);
            margin-top: 4px;
        }

        .item-price, .item-subtotal {
            font-weight: 600;
            color: var(--primary);
            font-size: 1rem;
        }

        .item-price {
            text-align: right;
            padding-right: 20px;
        }

        .item-subtotal {
            text-align: right;
            padding-right: 15px;
        }

        .quantity-control {
            display: flex;
            align-items: center;
            gap: 8px;
            justify-content: center;
        }

        .quantity-btn {
            width: 32px;
            height: 32px;
            border: none;
            background: var(--accent-light);
            border-radius: 8px;
            cursor: pointer;
            transition: var(--transition);
            color: var(--accent);
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .quantity-btn:hover:not(:disabled) {
            background: var(--accent);
            color: white;
        }

        .quantity-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .quantity-input {
            width: 50px;
            text-align: center;
            padding: 6px;
            border: 1px solid var(--accent-light);
            border-radius: 8px;
            font-weight: 500;
            background: var(--white);
            font-size: 0.9rem;
        }

        .remove-item {
            color: #ef4444;
            cursor: pointer;
            transition: var(--transition);
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            background: #FEE2E2;
            font-size: 0.9rem;
            margin: 0 auto;
        }

        .remove-item:hover {
            background: #ef4444;
            color: white;
        }

        .cart-summary {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 28px;
            box-shadow: var(--shadow-sm);
            height: fit-content;
            position: sticky;
            top: 100px;
        }

        .summary-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--accent-light);
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 16px;
            padding: 8px 0;
            color: var(--text);
            font-size: 0.95rem;
        }

        .summary-total {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--primary);
            border-top: 2px solid var(--accent-light);
            padding-top: 16px;
            margin-top: 8px;
        }

        .btn-checkout {
            width: 100%;
            background: var(--accent);
            color: white;
            border: none;
            padding: 14px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: var(--transition);
            margin-top: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-checkout:hover {
            background: var(--accent-hover);
            transform: translateY(-2px);
        }

        .btn-continue {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 16px;
            color: var(--accent);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            transition: var(--transition);
        }

        .btn-continue:hover {
            color: var(--primary);
            gap: 12px;
        }

        .empty-cart {
            text-align: center;
            padding: 60px 40px;
            background: var(--card-bg);
            border-radius: 30px;
            box-shadow: var(--shadow-sm);
        }

        .empty-cart i {
            font-size: 4rem;
            color: var(--accent-light);
            margin-bottom: 16px;
        }

        .empty-cart h3 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 8px;
        }

        .empty-cart p {
            color: var(--text-light);
            margin-bottom: 24px;
        }

        .btn-shop {
            background: var(--accent);
            color: white;
            border: none;
            padding: 12px 32px;
            border-radius: 30px;
            font-weight: 500;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-shop:hover {
            background: var(--accent-hover);
            transform: translateY(-2px);
        }

        .toast-notification {
            position: fixed;
            top: 100px;
            right: 30px;
            background: var(--white);
            border-left: 4px solid var(--accent);
            border-radius: 12px;
            padding: 16px 24px;
            box-shadow: var(--shadow-hover);
            z-index: 1000;
            animation: slideIn 0.3s ease;
            max-width: 350px;
        }

        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        .toast-content {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .toast-icon {
            width: 36px;
            height: 36px;
            background: var(--accent-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--accent);
        }

        .toast-message {
            flex: 1;
        }

        .toast-message h4 {
            font-size: 1rem;
            font-weight: 600;
            margin: 0 0 4px 0;
        }

        .toast-message p {
            font-size: 0.85rem;
            margin: 0;
            color: var(--text-light);
        }

        .toast-close {
            background: none;
            border: none;
            color: var(--text-light);
            cursor: pointer;
        }

        footer {
            background: linear-gradient(135deg, var(--primary) 0%, #3D291D 100%);
            color: rgba(255,255,255,0.8);
            padding: 40px 5% 24px;
            margin-top: 60px;
            text-align: center;
            font-size: 0.9rem;
        }

        .alert {
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-success {
            background: var(--accent-light);
            color: var(--accent);
            border: 1px solid var(--accent);
        }

        .alert-error {
            background: #FEE2E2;
            color: #ef4444;
            border: 1px solid #FECACA;
        }

        @media (max-width: 1024px) {
            .cart-header, .cart-item {
                grid-template-columns: 40px 1fr 100px 100px 100px 50px;
                gap: 12px;
            }
            .item-price { padding-right: 10px; }
            .item-subtotal { padding-right: 8px; }
        }

        @media (max-width: 968px) {
            .cart-container { grid-template-columns: 1fr; }
            .cart-summary { position: static; }
        }

        @media (max-width: 768px) {
            nav { display: none; }
            .hamburger { display: flex; }
            header { justify-content: space-between; }
            .cart-header { display: none; }
            .cart-item {
                grid-template-columns: 1fr;
                gap: 12px;
                text-align: center;
            }
            .product-info { flex-direction: column; text-align: center; }
            .quantity-control { justify-content: center; }
            .remove-item { margin: 0 auto; }
            .item-price, .item-subtotal { text-align: center; padding-right: 0; }
            .cart-item div:first-child { display: flex; justify-content: center; }
        }

        @media (max-width: 480px) {
            .logo img { height: 30px; }
            .logo h2 { font-size: 1rem; }
            .hamburger { width: 40px; height: 40px; font-size: 1.3rem; }
            .mobile-menu { width: 85%; max-width: 280px; }
        }
    </style>
</head>
<body>

    <header>
        <div class="logo">
            <img src="<?= base_url('assets/creatrix_logo.png') ?>" alt="CREATRIX Logo">
            <h2>CREATRIX COIR</h2>
        </div>
        
        <!-- Desktop Navigation - Original, unchanged -->
        <nav>
            <a href="<?= base_url('/storefront') ?>"><i class="fas fa-store"></i> Store</a>
            <a href="<?= base_url('/products') ?>"><i class="fas fa-box"></i> Products</a>
            <a href="<?= base_url('/cart') ?>" class="active"><i class="fas fa-shopping-cart"></i> Cart 
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
            <?php if (session()->get('isLoggedIn')): ?>
                <a href="<?= base_url('/transactions') ?>"><i class="fas fa-history"></i> My Orders</a>
                <a href="<?= base_url('/profile') ?>"><i class="fas fa-user"></i> <?= esc(session()->get('fullname')) ?></a>
                <a href="<?= base_url('/logout') ?>"><i class="fas fa-sign-out-alt"></i> Logout</a>
            <?php else: ?>
                <a href="<?= base_url('/login') ?>"><i class="fas fa-sign-in-alt"></i> Login</a>
                <a href="<?= base_url('/register') ?>"><i class="fas fa-user-plus"></i> Register</a>
            <?php endif; ?>
        </nav>

        <!-- Hamburger Button - Mobile only -->
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
        <div class="page-header">
            <h1>Shopping Cart</h1>
            <p>Review your items before checkout</p>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($cart)): ?>
        <div class="cart-container">
            <div class="cart-items">
                <div class="cart-header">
                    <div><input type="checkbox" id="select-all"></div>
                    <div>Product</div>
                    <div>Price</div>
                    <div>Quantity</div>
                    <div>Subtotal</div>
                    <div></div>
                </div>

                <?php foreach ($cart as $item): ?>
                <div class="cart-item" id="item-<?= $item['id'] ?>">
                    <div>
                        <input type="checkbox" class="item-select" data-id="<?= $item['id'] ?>" data-price="<?= $item['price'] ?>" data-quantity="<?= $item['quantity'] ?>">
                    </div>
                    <div class="product-info">
                        <div class="product-image">
                            <img src="<?= base_url('uploads/products/' . ($item['image'] ?? 'default.jpg')) ?>" 
                                 alt="<?= esc($item['name'] ?? 'Product') ?>"
                                 onerror="this.src='https://via.placeholder.com/80x80/DCE7E0/2A5C37?text=Coir'">
                        </div>
                        <div class="product-details">
                            <h4><?= esc($item['product_name'] ?? $item['name']) ?></h4>
                            <?php if (isset($item['variation_value']) && $item['variation_value']): ?>
                                <div class="item-variant">
                                    <i class="fas fa-tag"></i> <?= esc($item['variation_value']) ?>
                                </div>
                            <?php endif; ?>
                            <div class="stock-info">Stock: <?= $item['stock'] ?? 0 ?> available</div>
                        </div>
                    </div>
                    <div class="item-price">₱<?= number_format($item['price'] ?? 0, 2) ?></div>
                    <div class="quantity-control">
                        <button class="quantity-btn" onclick="updateQuantity(<?= $item['id'] ?>, 'decrease')" <?= ($item['quantity'] ?? 1) <= 1 ? 'disabled' : '' ?>>
                            <i class="fas fa-minus"></i>
                        </button>
                        <input type="number" id="qty-<?= $item['id'] ?>" class="quantity-input" value="<?= $item['quantity'] ?? 1 ?>" min="1" max="<?= $item['stock'] ?? 99 ?>" readonly>
                        <button class="quantity-btn" onclick="updateQuantity(<?= $item['id'] ?>, 'increase')" <?= ($item['quantity'] ?? 1) >= ($item['stock'] ?? 99) ? 'disabled' : '' ?>>
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <div class="item-subtotal">₱<?= number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 1), 2) ?></div>
                    <div class="remove-item" onclick="removeItem(<?= $item['id'] ?>)">
                        <i class="fas fa-trash"></i>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="cart-summary">
                <h3 class="summary-title">Order Summary</h3>
                <div class="summary-row">
                    <span>Selected Items</span>
                    <span id="selected-total">₱0.00</span>
                </div>
                <div class="summary-row">
                    <span>Shipping</span>
                    <span id="shippingAmount">₱100.00</span>
                </div>
                <div class="summary-row summary-total">
                    <span>Total</span>
                    <span id="totalAmount">₱100.00</span>
                </div>

                <button id="checkout-selected" class="btn-checkout">
                    <i class="fas fa-lock"></i> Checkout Selected Items
                </button>
                <a href="<?= base_url('/products') ?>" class="btn-continue">
                    <i class="fas fa-arrow-left"></i> Continue Shopping
                </a>
            </div>
        </div>

        <?php else: ?>
        <div class="empty-cart">
            <i class="fas fa-shopping-cart"></i>
            <h3>Your Cart is Empty</h3>
            <p>Looks like you haven't added any products to your cart yet.</p>
            <a href="<?= base_url('/products') ?>" class="btn-shop">
                <i class="fas fa-store"></i> Start Shopping
            </a>
        </div>
        <?php endif; ?>
    </div>

    <div id="toast" class="toast-notification" style="display: none;">
        <div class="toast-content">
            <div class="toast-icon"><i class="fas fa-check"></i></div>
            <div class="toast-message">
                <h4 id="toast-title">Success!</h4>
                <p id="toast-message">Cart updated</p>
            </div>
            <button class="toast-close" onclick="hideToast()"><i class="fas fa-times"></i></button>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 CREATRIX. All rights reserved. | For educational purposes only.</p>
        <p>CREATRIX Group - Promoting Sustainable Living Through Coconut Coir Innovation</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Hamburger menu toggle - Mobile only
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

        const selectAll = document.getElementById('select-all');
        const itemSelects = document.querySelectorAll('.item-select');
        
        function updateSelectedTotal() {
            let total = 0;
            itemSelects.forEach(checkbox => {
                if (checkbox.checked) {
                    const price = parseFloat(checkbox.dataset.price);
                    const quantity = parseInt(checkbox.dataset.quantity);
                    total += price * quantity;
                }
            });
            document.getElementById('selected-total').textContent = '₱' + total.toFixed(2);
            const shipping = total >= 1000 ? 0 : 100;
            const grandTotal = total + shipping;
            document.getElementById('shippingAmount').textContent = shipping === 0 ? 'Free' : '₱100.00';
            document.getElementById('totalAmount').textContent = '₱' + grandTotal.toFixed(2);
        }

        if (selectAll) {
            selectAll.addEventListener('change', function() {
                itemSelects.forEach(checkbox => checkbox.checked = this.checked);
                updateSelectedTotal();
            });
        }

        itemSelects.forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedTotal);
        });

        updateSelectedTotal();

        document.getElementById('checkout-selected')?.addEventListener('click', function() {
            const selectedItems = [];
            itemSelects.forEach(checkbox => {
                if (checkbox.checked) selectedItems.push(checkbox.dataset.id);
            });
            if (selectedItems.length === 0) {
                alert('Please select at least one item to checkout');
                return;
            }
            fetch('<?= base_url('cart/save-selected') ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ selected: selectedItems })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) window.location.href = '<?= base_url('/checkout') ?>';
                else alert(data.message);
            })
            .catch(error => alert('Failed to proceed to checkout'));
        });

        function updateQuantity(cartId, action) {
            const qtyInput = document.getElementById('qty-' + cartId);
            if (!qtyInput) return;
            let currentQty = parseInt(qtyInput.value);
            const maxQty = parseInt(qtyInput.getAttribute('max'));
            let newQty = currentQty;
            if (action === 'increase' && currentQty < maxQty) newQty = currentQty + 1;
            else if (action === 'decrease' && currentQty > 1) newQty = currentQty - 1;
            else return;
            
            const buttons = document.querySelectorAll(`#item-${cartId} .quantity-btn`);
            buttons.forEach(btn => btn.style.opacity = '0.5');
            
            fetch('<?= base_url('cart/update') ?>/' + cartId, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
                body: 'quantity=' + newQty
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('cart-badge').textContent = data.cartCount;
                    showToast('Success', data.message);
                    setTimeout(() => location.reload(), 1000);
                } else showToast('Error', data.message, 'error');
            })
            .catch(error => showToast('Error', 'Failed to update quantity', 'error'))
            .finally(() => buttons.forEach(btn => btn.style.opacity = '1'));
        }

        function removeItem(cartId) {
            if (confirm('Remove this item from your cart?')) {
                fetch('<?= base_url('cart/remove') ?>/' + cartId, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('cart-badge').textContent = data.cartCount;
                        showToast('Success', data.message);
                        setTimeout(() => location.reload(), 1000);
                    } else showToast('Error', data.message, 'error');
                })
                .catch(error => showToast('Error', 'Failed to remove item', 'error'));
            }
        }

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
                toast.style.borderLeftColor = '#ef4444';
                toastIcon.className = 'fas fa-exclamation-triangle';
                toastIcon.style.color = '#ef4444';
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