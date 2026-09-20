<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Checkout | CREATRIX</title>
    
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

        /* Header */
        header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: var(--white);
            padding: 12px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--shadow-sm);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
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
        .nav-menu {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
        }

        .nav-menu a {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 30px;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.9rem;
            white-space: nowrap;
        }

        .nav-menu a:hover {
            background: rgba(255, 255, 255, 0.1);
            color: var(--white);
        }

        .nav-menu a.active {
            background: var(--white);
            color: var(--primary);
            font-weight: 500;
        }

        .cart-badge {
            background: var(--accent);
            color: white;
            border-radius: 50%;
            padding: 2px 8px;
            font-size: 0.7rem;
            margin-left: 5px;
            font-weight: 700;
            display: inline-block;
        }

        /* Hamburger Menu Button */
        .hamburger {
            display: none;
            background: none;
            border: none;
            color: var(--white);
            font-size: 1.5rem;
            cursor: pointer;
            transition: var(--transition);
            padding: 8px;
            z-index: 1001;
            width: 42px;
            height: 42px;
            border-radius: 50%;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
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
            z-index: 998;
            transition: all 0.3s;
        }

        .menu-overlay.active {
            display: block;
        }

        /* Mobile Menu - Slide from right */
        .mobile-menu {
            position: fixed;
            top: 0;
            right: -100%;
            width: 85%;
            max-width: 320px;
            height: 100vh;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            box-shadow: -5px 0 30px rgba(0, 0, 0, 0.3);
            z-index: 999;
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
            gap: 8px;
            margin-bottom: 25px;
        }

        .mobile-menu nav a {
            color: var(--white);
            text-decoration: none;
            padding: 14px 18px;
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
            font-size: 2.2rem;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 8px;
        }

        .page-header p {
            font-size: 1rem;
            color: var(--text-light);
        }

        .checkout-container {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
        }

        .checkout-form {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 30px;
            box-shadow: var(--shadow-sm);
        }

        .section-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--primary);
            margin: 25px 0 15px;
            padding-bottom: 8px;
            border-bottom: 1px solid var(--accent-light);
        }

        .section-title:first-of-type {
            margin-top: 0;
        }

        .info-card {
            background: var(--accent-light);
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 20px;
        }

        .info-row {
            display: flex;
            margin-bottom: 8px;
            font-size: 0.95rem;
        }

        .info-label {
            width: 100px;
            color: var(--text-light);
        }

        .info-value {
            color: var(--text);
            font-weight: 500;
        }

        .options-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin: 15px 0 20px;
        }

        .option-card {
            position: relative;
        }

        .option-card input {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .option-card label {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px 15px;
            background: var(--accent-light);
            border-radius: 16px;
            cursor: pointer;
            transition: var(--transition);
            border: 2px solid transparent;
            text-align: center;
        }

        .option-card input:checked + label {
            background: var(--white);
            border-color: var(--accent);
            box-shadow: var(--shadow-sm);
        }

        .option-card label i {
            font-size: 2rem;
            color: var(--accent);
            margin-bottom: 10px;
        }

        .option-card label span {
            font-weight: 500;
            margin-bottom: 4px;
        }

        .option-card label small {
            color: var(--text-light);
            font-size: 0.8rem;
        }

        .pickup-schedule {
            background: var(--accent-light);
            border-radius: 16px;
            padding: 20px;
            margin-top: 20px;
        }

        .pickup-schedule h3 {
            color: var(--primary);
            font-size: 1rem;
            margin-bottom: 15px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-weight: 500;
            margin-bottom: 8px;
            color: var(--text);
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid var(--accent-light);
            border-radius: 12px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
            transition: var(--transition);
            background: var(--white);
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(42, 92, 55, 0.1);
        }

        .order-summary {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 28px;
            box-shadow: var(--shadow-sm);
            height: fit-content;
        }

        .summary-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--accent-light);
        }

        .summary-items {
            margin-bottom: 20px;
            max-height: 400px;
            overflow-y: auto;
        }

        .summary-item {
            display: flex;
            gap: 12px;
            margin-bottom: 16px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--accent-light);
        }

        .item-image {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            overflow: hidden;
            background: var(--accent-light);
            flex-shrink: 0;
        }

        .item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .item-details {
            flex: 1;
        }

        .item-details h4 {
            font-size: 0.95rem;
            font-weight: 600;
            margin-bottom: 4px;
            color: var(--text);
        }

        .item-details .item-variant {
            font-size: 0.75rem;
            color: var(--accent);
            font-weight: 500;
            margin-bottom: 4px;
        }

        .item-details .item-quantity {
            font-size: 0.75rem;
            color: var(--text-light);
        }

        .item-price {
            font-weight: 600;
            color: var(--primary);
            font-size: 0.95rem;
            white-space: nowrap;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            padding: 8px 0;
            color: var(--text);
            font-size: 0.95rem;
        }

        .summary-total {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--primary);
            border-top: 2px solid var(--accent-light);
            padding-top: 16px;
            margin-top: 8px;
        }

        .btn-place-order {
            width: 100%;
            background: var(--accent);
            color: white;
            border: none;
            padding: 14px;
            border-radius: 10px;
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

        .btn-place-order:hover {
            background: var(--accent-hover);
            transform: translateY(-2px);
        }

        .btn-back {
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

        .btn-back:hover {
            color: var(--primary);
            gap: 12px;
        }

        /* Modal Styles */
        .payment-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            z-index: 2000;
            align-items: center;
            justify-content: center;
        }

        .payment-modal.active {
            display: flex;
        }

        .modal-content {
            background: var(--card-bg);
            border-radius: 24px;
            max-width: 450px;
            width: 90%;
            padding: 30px;
            animation: modalSlideIn 0.3s ease;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--accent-light);
        }

        .modal-header h3 {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--primary);
            margin: 0;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--text-light);
            transition: var(--transition);
        }

        .modal-close:hover {
            color: var(--danger);
        }

        .payment-icon {
            text-align: center;
            font-size: 3rem;
            color: var(--accent);
            margin-bottom: 20px;
        }

        .payment-instructions {
            background: var(--accent-light);
            padding: 15px;
            border-radius: 12px;
            margin: 15px 0;
            font-size: 0.85rem;
        }

        .payment-instructions p {
            margin: 5px 0;
        }

        .btn-confirm-payment {
            width: 100%;
            background: var(--accent);
            color: white;
            border: none;
            padding: 14px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            margin-top: 15px;
        }

        .btn-confirm-payment:hover {
            background: var(--accent-hover);
        }

        .alert {
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.95rem;
        }

        .alert-error {
            background: #FEE2E2;
            color: #ef4444;
            border: 1px solid #fecaca;
        }

        .alert-success {
            background: var(--accent-light);
            color: var(--accent);
            border: 1px solid var(--accent);
        }

        footer {
            background: linear-gradient(135deg, var(--primary) 0%, #1e2a1a 100%);
            color: rgba(255,255,255,0.85);
            padding: 40px 5% 24px;
            margin-top: 60px;
            text-align: center;
            font-size: 0.9rem;
        }

        @media (max-width: 992px) {
            .checkout-container {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .nav-menu {
                display: none;
            }
            
            .hamburger {
                display: flex;
            }
            
            header {
                justify-content: space-between;
            }
            
            .logo img {
                height: 32px;
            }
            
            .logo h2 {
                font-size: 1.1rem;
            }
            
            .container {
                padding: 0 16px;
            }
            
            .page-header h1 {
                font-size: 1.6rem;
            }
            
            .checkout-form {
                padding: 20px;
            }
            
            .options-grid {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 480px) {
            .logo h2 {
                font-size: 0.95rem;
            }
            
            .logo img {
                height: 28px;
            }
            
            .hamburger {
                font-size: 1.3rem;
                width: 38px;
                height: 38px;
            }
            
            .modal-content {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <img src="<?= base_url('assets/creatrix_logo.png') ?>" alt="CREATRIX Logo" onerror="this.src='https://via.placeholder.com/40x40/FFFFFF/2A5C37?text=C'">
            <h2>CREATRIX COIR</h2>
        </div>
        
        <!-- Desktop Navigation -->
        <div class="nav-menu">
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
            <?php if (session()->get('isLoggedIn')): ?>
                <a href="<?= base_url('/transactions') ?>"><i class="fas fa-history"></i> My Orders</a>
                <a href="<?= base_url('/profile') ?>"><i class="fas fa-user"></i> <?= esc(session()->get('fullname')) ?></a>
                <a href="<?= base_url('/logout') ?>"><i class="fas fa-sign-out-alt"></i> Logout</a>
            <?php else: ?>
                <a href="<?= base_url('/login') ?>"><i class="fas fa-sign-in-alt"></i> Login</a>
                <a href="<?= base_url('/register') ?>"><i class="fas fa-user-plus"></i> Register</a>
            <?php endif; ?>
        </div>

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
            <a href="<?= base_url('/cart') ?>" class="mobile-nav-link"><i class="fas fa-shopping-cart"></i> Cart <span class="cart-badge" id="mobileCartBadge">0</span></a>
            <?php if (session()->get('isLoggedIn')): ?>
                <a href="<?= base_url('/transactions') ?>" class="mobile-nav-link"><i class="fas fa-history"></i> My Orders</a>
                <a href="<?= base_url('/profile') ?>" class="mobile-nav-link"><i class="fas fa-user"></i> Profile</a>
                <a href="<?= base_url('/logout') ?>" class="mobile-nav-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
            <?php else: ?>
                <a href="<?= base_url('/login') ?>" class="mobile-nav-link"><i class="fas fa-sign-in-alt"></i> Login</a>
                <a href="<?= base_url('/register') ?>" class="mobile-nav-link"><i class="fas fa-user-plus"></i> Register</a>
            <?php endif; ?>
        </nav>
    </div>

    <div class="container">
        <div class="page-header">
            <h1>Checkout</h1>
            <p>Complete your purchase</p>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <div class="checkout-container">
            <div class="checkout-form">
                <h2 class="section-title">Contact Information</h2>
                <div class="info-card">
                    <div class="info-row">
                        <span class="info-label">Name:</span>
                        <span class="info-value"><?= esc($user['fullname'] ?? '') ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Email:</span>
                        <span class="info-value"><?= esc($user['email'] ?? '') ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Mobile:</span>
                        <span class="info-value"><?= esc($user['mobile'] ?? '') ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Address:</span>
                        <span class="info-value"><?= esc($user['address'] ?? '') ?></span>
                    </div>
                </div>

                <form id="checkoutForm" action="<?= base_url('/checkout/process') ?>" method="post">
                    <?= csrf_field() ?>
                    
                    <h2 class="section-title">Delivery Method</h2>
                    <div class="options-grid">
                        <div class="option-card">
                            <input type="radio" name="delivery_method" id="delivery" value="delivery" checked>
                            <label for="delivery">
                                <i class="fas fa-truck"></i>
                                <span>Door Delivery</span>
                                <small>₱100.00</small>
                            </label>
                        </div>
                        <div class="option-card">
                            <input type="radio" name="delivery_method" id="pickup" value="pickup">
                            <label for="pickup">
                                <i class="fas fa-store"></i>
                                <span>Store Pickup</span>
                                <small>Free</small>
                            </label>
                        </div>
                    </div>

                    <div id="pickup-schedule" class="pickup-schedule" style="display: none;">
                        <h3><i class="fas fa-calendar-alt"></i> Pickup Schedule</h3>
                        <div class="form-group">
                            <label for="pickup_date">Pickup Date</label>
                            <input type="date" name="pickup_date" id="pickup_date" min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                        </div>
                        <div class="form-group">
                            <label for="pickup_time_slot">Pickup Time Slot</label>
                            <select name="pickup_time_slot" id="pickup_time_slot">
                                <option value="">Select Time Slot</option>
                                <option value="9:00 AM - 11:00 AM">9:00 AM - 11:00 AM</option>
                                <option value="11:00 AM - 1:00 PM">11:00 AM - 1:00 PM</option>
                                <option value="1:00 PM - 3:00 PM">1:00 PM - 3:00 PM</option>
                                <option value="3:00 PM - 5:00 PM">3:00 PM - 5:00 PM</option>
                                <option value="5:00 PM - 7:00 PM">5:00 PM - 7:00 PM</option>
                            </select>
                        </div>
                        <small><i class="fas fa-info-circle"></i> Please arrive during your selected time slot for pickup.</small>
                    </div>

                    <h2 class="section-title">Payment Method</h2>
                    <div class="options-grid">
                        <div class="option-card">
                            <input type="radio" name="payment_method" id="cash" value="cash" checked>
                            <label for="cash">
                                <i class="fas fa-money-bill-wave"></i>
                                <span>Cash on Delivery</span>
                                <small>Pay when you receive</small>
                            </label>
                        </div>
                        <div class="option-card">
                            <input type="radio" name="payment_method" id="gcash" value="gcash">
                            <label for="gcash">
                                <i class="fas fa-mobile-alt"></i>
                                <span>GCash</span>
                                <small>Pay via GCash</small>
                            </label>
                        </div>
                        <div class="option-card">
                            <input type="radio" name="payment_method" id="card" value="card">
                            <label for="card">
                                <i class="fas fa-credit-card"></i>
                                <span>Credit Card</span>
                                <small>Pay with card</small>
                            </label>
                        </div>
                    </div>
                </form>
            </div>

            <div class="order-summary">
                <h3 class="summary-title">Order Summary</h3>
                
                <div class="summary-items">
                    <?php foreach ($cart as $item): ?>
                    <div class="summary-item">
                        <div class="item-image">
                            <img src="<?= base_url('uploads/products/' . ($item['product']['image'] ?? 'default.jpg')) ?>" 
                                alt="<?= esc($item['product']['product_name']) ?>"
                                onerror="this.src='https://via.placeholder.com/60x60/DCE7E0/2A5C37?text=Coir'">
                        </div>
                        <div class="item-details">
                            <h4><?= esc($item['product']['product_name']) ?></h4>
                            <?php if (isset($item['variation_value']) && $item['variation_value']): ?>
                                <div class="item-variant">
                                    <i class="fas fa-tag"></i> <?= esc($item['variation_value']) ?>
                                </div>
                            <?php endif; ?>
                            <div class="item-quantity">Qty: <?= $item['quantity'] ?></div>
                        </div>
                        <div class="item-price">
                            ₱<?= number_format($item['price'] * $item['quantity'], 2) ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>₱<?= number_format($total, 2) ?></span>
                </div>
                <div class="summary-row" id="shippingRow">
                    <span>Shipping</span>
                    <span id="shippingAmount">₱100.00</span>
                </div>
                <div class="summary-row summary-total">
                    <span>Total</span>
                    <span id="totalAmount">₱<?= number_format($total + 100, 2) ?></span>
                </div>

                <button type="button" id="placeOrderBtn" class="btn-place-order">
                    <i class="fas fa-check-circle"></i> Place Order
                </button>

                <a href="<?= base_url('/cart') ?>" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Back to Cart
                </a>
            </div>
        </div>
    </div>

    <!-- GCash Payment Modal -->
    <div id="gcashModal" class="payment-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-mobile-alt"></i> GCash Payment</h3>
                <button class="modal-close" onclick="closeModal('gcashModal')">&times;</button>
            </div>
            <div class="payment-icon">
                <i class="fas fa-qrcode"></i>
            </div>
            <div class="payment-instructions">
                <p><strong>GCash Payment Instructions:</strong></p>
                <p>1. Open your GCash app</p>
                <p>2. Go to "Pay QR" and scan the QR code below</p>
                <p>3. Enter the total amount: <strong>₱<?= number_format($total + (isset($_POST['delivery_method']) && $_POST['delivery_method'] === 'delivery' ? 100 : 0), 2) ?></strong></p>
                <p>4. Enter your reference number</p>
            </div>
            <div class="form-group">
                <label for="gcash_reference">GCash Reference Number *</label>
                <input type="text" id="gcash_reference" placeholder="Enter GCash reference number" required>
            </div>
            <button class="btn-confirm-payment" onclick="confirmPayment('gcash')">
                <i class="fas fa-check"></i> Confirm Payment
            </button>
        </div>
    </div>

    <!-- Credit Card Payment Modal -->
    <div id="cardModal" class="payment-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-credit-card"></i> Credit Card Payment</h3>
                <button class="modal-close" onclick="closeModal('cardModal')">&times;</button>
            </div>
            <div class="payment-icon">
                <i class="fas fa-credit-card"></i>
            </div>
            <div class="form-group">
                <label for="card_number">Card Number *</label>
                <input type="text" id="card_number" placeholder="1234 5678 9012 3456" maxlength="19">
            </div>
            <div class="form-group">
                <label for="card_name">Cardholder Name *</label>
                <input type="text" id="card_name" placeholder="Name on card">
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label for="expiry_date">Expiry Date *</label>
                    <input type="text" id="expiry_date" placeholder="MM/YY">
                </div>
                <div class="form-group">
                    <label for="cvv">CVV *</label>
                    <input type="password" id="cvv" placeholder="123" maxlength="4">
                </div>
            </div>
            <div class="payment-instructions">
                <p><strong>Total Amount:</strong> ₱<?= number_format($total + (isset($_POST['delivery_method']) && $_POST['delivery_method'] === 'delivery' ? 100 : 0), 2) ?></p>
                <p><i class="fas fa-lock"></i> Secure payment processing</p>
            </div>
            <button class="btn-confirm-payment" onclick="confirmPayment('card')">
                <i class="fas fa-check"></i> Pay Now
            </button>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 CREATRIX. All rights reserved. | For educational purposes only.</p>
        <p>CREATRIX Group - Promoting Sustainable Living Through Coconut Coir Innovation</p>
    </footer>

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

        document.querySelectorAll('.mobile-nav-link').forEach(link => {
            link.addEventListener('click', () => {
                closeMenu();
            });
        });

        window.addEventListener('resize', function() {
            if (window.innerWidth > 768 && mobileMenu.classList.contains('active')) {
                closeMenu();
            }
        });

        // Delivery method toggle
        const deliveryRadios = document.querySelectorAll('input[name="delivery_method"]');
        const pickupSchedule = document.getElementById('pickup-schedule');
        const pickupDateInput = document.getElementById('pickup_date');
        const pickupTimeSlotInput = document.getElementById('pickup_time_slot');
        
        function togglePickupSchedule() {
            const selectedDelivery = document.querySelector('input[name="delivery_method"]:checked').value;
            if (selectedDelivery === 'pickup') {
                pickupSchedule.style.display = 'block';
                const tomorrow = new Date();
                tomorrow.setDate(tomorrow.getDate() + 1);
                pickupDateInput.value = tomorrow.toISOString().split('T')[0];
            } else {
                pickupSchedule.style.display = 'none';
                pickupDateInput.value = '';
                pickupTimeSlotInput.value = '';
            }
        }
        
        deliveryRadios.forEach(radio => {
            radio.addEventListener('change', togglePickupSchedule);
        });
        
        togglePickupSchedule();
        
        // Update shipping amount based on delivery method
        document.querySelectorAll('input[name="delivery_method"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const subtotal = <?= $total ?>;
                const shipping = this.value === 'delivery' ? 100 : 0;
                document.getElementById('shippingAmount').textContent = shipping === 100 ? '₱100.00' : 'Free';
                document.getElementById('totalAmount').textContent = '₱' + (subtotal + shipping).toFixed(2);
            });
        });

        // Modal functions
        function openModal(modalId) {
            document.getElementById(modalId).classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
            document.body.style.overflow = '';
        }

        function confirmPayment(type) {
            if (type === 'gcash') {
                const reference = document.getElementById('gcash_reference').value;
                if (!reference) {
                    alert('Please enter your GCash reference number');
                    return;
                }
                // Add reference to form and submit
                const form = document.getElementById('checkoutForm');
                const hiddenRef = document.createElement('input');
                hiddenRef.type = 'hidden';
                hiddenRef.name = 'payment_reference';
                hiddenRef.value = reference;
                form.appendChild(hiddenRef);
                closeModal('gcashModal');
                form.submit();
            } else if (type === 'card') {
                const cardNumber = document.getElementById('card_number').value;
                const cardName = document.getElementById('card_name').value;
                const expiry = document.getElementById('expiry_date').value;
                const cvv = document.getElementById('cvv').value;
                
                if (!cardNumber || !cardName || !expiry || !cvv) {
                    alert('Please fill in all credit card details');
                    return;
                }
                const form = document.getElementById('checkoutForm');
                const hiddenCard = document.createElement('input');
                hiddenCard.type = 'hidden';
                hiddenCard.name = 'card_details';
                hiddenCard.value = JSON.stringify({ last4: cardNumber.slice(-4), cardholder: cardName });
                form.appendChild(hiddenCard);
                closeModal('cardModal');
                form.submit();
            }
        }

        // Place order button with payment validation
        document.getElementById('placeOrderBtn').addEventListener('click', function() {
            const deliveryMethod = document.querySelector('input[name="delivery_method"]:checked');
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
            
            if (!deliveryMethod || !paymentMethod) {
                alert('Please select both delivery and payment methods');
                return;
            }
            
            if (deliveryMethod.value === 'pickup') {
                const pickupDate = document.getElementById('pickup_date').value;
                const pickupTimeSlot = document.getElementById('pickup_time_slot').value;
                
                if (!pickupDate) {
                    alert('Please select a pickup date');
                    return;
                }
                
                if (!pickupTimeSlot) {
                    alert('Please select a pickup time slot');
                    return;
                }
            }
            
            // Show appropriate payment modal
            if (paymentMethod.value === 'gcash') {
                openModal('gcashModal');
            } else if (paymentMethod.value === 'card') {
                openModal('cardModal');
            } else {
                // Cash on delivery - submit directly
                document.getElementById('checkoutForm').submit();
            }
        });

        // Close modals on overlay click
        window.onclick = function(event) {
            if (event.target.classList.contains('payment-modal')) {
                event.target.classList.remove('active');
                document.body.style.overflow = '';
            }
        }
    </script>
</body>
</html>