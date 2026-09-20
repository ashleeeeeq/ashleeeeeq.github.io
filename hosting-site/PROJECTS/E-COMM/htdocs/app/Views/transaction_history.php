<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders | CREATRIX</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('assets/creatrix_logo.png') ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('assets/creatrix_logo.png') ?>">

    <style>
        :root {
            --bg: #F5F7FA;
            --card-bg: #FFFFFF;
            --primary: #1F4529;
            --primary-light: #477A53;
            --accent: #2A5C37;
            --accent-light: #DCE7E0;
            --accent-hover: #16381F;
            --text: #2C3B31;
            --text-light: #647368;
            --white: #FFFFFF;
            --gray-light: #F5F5F5;
            --gray-border: #E8E8E8;
            --shadow-sm: 0 2px 4px rgba(0,0,0,0.04);
            --shadow-md: 0 4px 12px rgba(0,0,0,0.08);
            --shadow-hover: 0 8px 24px rgba(0,0,0,0.12);
            --transition: all 0.3s ease;
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
            z-index: 1002;
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
            z-index: 1003;
            align-items: center;
            justify-content: center;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            position: relative;
        }

        .hamburger:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: scale(1.05);
        }

        /* Overlay for mobile menu - starts below header */
        .menu-overlay {
            display: none;
            position: fixed;
            top: 70px;
            left: 0;
            width: 100%;
            height: calc(100% - 70px);
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            transition: all 0.3s;
        }

        .menu-overlay.active {
            display: block;
        }

        /* Mobile Menu - starts below header */
        .mobile-menu {
            position: fixed;
            top: 70px;
            right: -100%;
            width: 85%;
            max-width: 320px;
            height: calc(100% - 70px);
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            box-shadow: -5px 0 30px rgba(0, 0, 0, 0.2);
            z-index: 1001;
            transition: right 0.3s ease;
            padding: 20px;
            overflow-y: auto;
        }

        .mobile-menu.active {
            right: 0;
        }

        .mobile-menu nav {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 25px;
        }

        .mobile-menu nav a {
            color: var(--white);
            text-decoration: none;
            padding: 12px 16px;
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
            padding: 12px 16px;
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

        /* Page Header */
        .page-header {
            margin-bottom: 30px;
        }

        .page-header h1 {
            font-size: 1.8rem;
            font-weight: 600;
            color: var(--primary);
            margin: 0;
        }

        /* Order Status Tabs */
        .order-tabs {
            background: var(--white);
            border-radius: 12px;
            padding: 8px;
            margin-bottom: 24px;
            box-shadow: var(--shadow-sm);
            display: flex;
            gap: 4px;
            flex-wrap: wrap;
        }

        .tab-btn {
            flex: 1;
            padding: 12px 16px;
            border: none;
            background: transparent;
            font-weight: 500;
            font-size: 0.9rem;
            border-radius: 8px;
            cursor: pointer;
            transition: var(--transition);
            color: var(--text-light);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .tab-btn i {
            font-size: 1rem;
        }

        .tab-btn:hover {
            background: var(--gray-light);
            color: var(--text);
        }

        .tab-btn.active {
            background: var(--accent);
            color: white;
        }

        /* Order Cards */
        .order-card {
            background: var(--white);
            border-radius: 12px;
            margin-bottom: 16px;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            overflow: hidden;
        }

        .order-card:hover {
            box-shadow: var(--shadow-md);
        }

        /* Order Header */
        .order-header {
            background: var(--gray-light);
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            border-bottom: 1px solid var(--gray-border);
        }

        .store-info {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .store-info i {
            color: var(--accent);
            font-size: 1rem;
        }

        .store-name {
            font-weight: 500;
            font-size: 0.9rem;
        }

        .order-status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .order-status i {
            font-size: 0.75rem;
        }

        .status-pending { background: #fff3cd; color: #856404; }
        .status-confirmed { background: #cce5ff; color: #004085; }
        .status-processing { background: #cce5ff; color: #004085; }
        .status-ready_for_pickup { background: #d4edda; color: #155724; }
        .status-to_ship { background: #cce5ff; color: #004085; }
        .status-shipped { background: #d4edda; color: #155724; }
        .status-completed { background: var(--accent-light); color: var(--accent); }
        .status-cancelled { background: #f8d7da; color: #721c24; }

        /* Order Body */
        .order-body {
            padding: 20px;
        }

        .order-item {
            display: flex;
            gap: 16px;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--gray-border);
        }

        .order-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .item-image {
            width: 80px;
            height: 80px;
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

        .item-name {
            font-weight: 500;
            font-size: 1rem;
            margin-bottom: 4px;
            color: var(--text);
        }

        .item-variation {
            font-size: 0.75rem;
            color: var(--text-light);
            margin-bottom: 8px;
        }

        .item-price {
            display: flex;
            gap: 12px;
            align-items: baseline;
            flex-wrap: wrap;
        }

        .price {
            font-weight: 600;
            color: var(--primary);
            font-size: 0.95rem;
        }

        .quantity {
            color: var(--text-light);
            font-size: 0.85rem;
        }

        /* Order Footer */
        .order-footer {
            padding: 16px 20px;
            background: var(--gray-light);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            border-top: 1px solid var(--gray-border);
        }

        .order-total {
            display: flex;
            align-items: baseline;
            gap: 8px;
            flex-wrap: wrap;
        }

        .total-label {
            font-size: 0.85rem;
            color: var(--text-light);
        }

        .total-amount {
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--primary);
        }

        .order-actions {
            display: flex;
            gap: 12px;
        }

        .btn-action {
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: 500;
            font-size: 0.85rem;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 1px solid transparent;
        }

        .btn-primary {
            background: var(--accent);
            color: white;
        }

        .btn-primary:hover {
            background: var(--accent-hover);
            transform: translateY(-1px);
        }

        .btn-outline {
            background: transparent;
            border-color: var(--accent-light);
            color: var(--text);
        }

        .btn-outline:hover {
            border-color: var(--accent);
            color: var(--accent);
        }

        .btn-danger {
            background: #f8d7da;
            color: #721c24;
        }

        .btn-danger:hover {
            background: #f5c6cb;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: var(--white);
            border-radius: 12px;
        }

        .empty-state i {
            font-size: 4rem;
            color: var(--accent-light);
            margin-bottom: 16px;
        }

        .empty-state h3 {
            font-size: 1.3rem;
            font-weight: 500;
            color: var(--text);
            margin-bottom: 8px;
        }

        .empty-state p {
            color: var(--text-light);
            margin-bottom: 24px;
        }

        .btn-shop {
            background: var(--accent);
            color: white;
            border: none;
            padding: 10px 32px;
            border-radius: 30px;
            font-weight: 500;
            font-size: 0.9rem;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-shop:hover {
            background: var(--accent-hover);
            transform: translateY(-1px);
        }

        footer {
            background: linear-gradient(135deg, var(--primary) 0%, #3D291D 100%);
            color: rgba(255,255,255,0.8);
            padding: 40px 5% 24px;
            margin-top: 60px;
            text-align: center;
            font-size: 0.9rem;
        }

        footer p {
            margin: 6px 0;
        }

        /* Responsive Design */
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
            
            .menu-overlay,
            .mobile-menu {
                top: 65px;
                height: calc(100% - 65px);
            }
            
            .order-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .order-footer {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .order-actions {
                width: 100%;
            }
            
            .btn-action {
                flex: 1;
                justify-content: center;
            }
            
            .order-tabs {
                overflow-x: auto;
                flex-wrap: nowrap;
                -webkit-overflow-scrolling: touch;
            }
            
            .tab-btn {
                white-space: nowrap;
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
            
            .menu-overlay,
            .mobile-menu {
                top: 60px;
                height: calc(100% - 60px);
            }
            
            .mobile-menu nav a {
                padding: 10px 14px;
                font-size: 0.95rem;
            }
            
            .mobile-menu .mobile-btns a {
                padding: 10px 14px;
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
            <a href="<?= base_url('/transactions') ?>" class="active"><i class="fas fa-history"></i> My Orders</a>
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
        <div class="page-header">
            <h1>My Orders</h1>
        </div>

        <!-- Status Tabs -->
        <div class="order-tabs">
            <button class="tab-btn active" data-status="all">
                <i class="fas fa-list"></i> All
            </button>
            <button class="tab-btn" data-status="pending">
                <i class="fas fa-clock"></i> Pending
            </button>
            <button class="tab-btn" data-status="confirmed">
                <i class="fas fa-check-circle"></i> Confirmed
            </button>
            <button class="tab-btn" data-status="processing">
                <i class="fas fa-box"></i> Processing
            </button>
            <button class="tab-btn" data-status="to_ship">
                <i class="fas fa-truck"></i> To Ship
            </button>
            <button class="tab-btn" data-status="shipped">
                <i class="fas fa-shipping-fast"></i> To Receive
            </button>
            <button class="tab-btn" data-status="ready_for_pickup">
                <i class="fas fa-store"></i> To Pickup
            </button>
            <button class="tab-btn" data-status="completed">
                <i class="fas fa-check-double"></i> Completed
            </button>
            <button class="tab-btn" data-status="cancelled">
                <i class="fas fa-times-circle"></i> Cancelled
            </button>
        </div>

        <!-- Orders List -->
        <div id="orders-container">
            <?php if (!empty($transactions)): ?>
                <?php foreach ($transactions as $order): ?>
                    <div class="order-card" data-status="<?= $order['status'] ?>">
                        <!-- Order Header -->
                        <div class="order-header">
                            <div class="store-info">
                                <i class="fas fa-store"></i>
                                <span class="store-name">CREATRIX Official Store</span>
                            </div>
                            <div class="order-status status-<?= $order['status'] ?>">
                                <i class="fas <?= $order['status'] == 'pending' ? 'fa-clock' : ($order['status'] == 'shipped' ? 'fa-truck' : ($order['status'] == 'completed' ? 'fa-check-circle' : 'fa-info-circle')) ?>"></i>
                                <?= ucfirst(str_replace('_', ' ', $order['status'])) ?>
                            </div>
                        </div>

                        <!-- Order Body - Items -->
                        <div class="order-body">
                            <?php 
                            $orderItemsModel = new \App\Models\OrderItemsModel();
                            $items = $orderItemsModel->getOrderItems($order['id']);
                            $displayItems = array_slice($items, 0, 2);
                            $remainingItems = count($items) - 2;
                            ?>
                            
                            <?php foreach ($displayItems as $item): ?>
                            <div class="order-item">
                                <div class="item-image">
                                    <img src="<?= base_url('uploads/products/' . ($item['image'] ?? 'default.jpg')) ?>" 
                                         alt="<?= esc($item['product_name']) ?>"
                                         onerror="this.src='https://via.placeholder.com/80x80/DCE7E0/2A5C37?text=Coir'">
                                </div>
                                <div class="item-details">
                                    <div class="item-name"><?= esc($item['product_name']) ?></div>
                                    <?php if (!empty($item['variation_name'])): ?>
                                        <div class="item-variation"><?= esc($item['variation_name']) ?></div>
                                    <?php endif; ?>
                                    <div class="item-price">
                                        <span class="price">₱<?= number_format($item['price'], 2) ?></span>
                                        <span class="quantity">x<?= $item['quantity'] ?></span>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            
                            <?php if ($remainingItems > 0): ?>
                                <div class="text-muted small" style="margin-top: 8px;">
                                    and <?= $remainingItems ?> more item(s)
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Order Footer -->
                        <div class="order-footer">
                            <div class="order-total">
                                <span class="total-label">Total:</span>
                                <span class="total-amount">₱<?= number_format($order['total_price'], 2) ?></span>
                            </div>
                            <div class="order-actions">
                                <a href="<?= base_url('/order-details/' . $order['id']) ?>" class="btn-action btn-outline">
                                    <i class="fas fa-eye"></i> View Details
                                </a>
                                <?php if ($order['status'] === 'shipped'): ?>
                                    <form action="<?= base_url('/order/receive/' . $order['id']) ?>" method="post" style="display: inline;">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn-action btn-primary" onclick="return confirm('Confirm receipt of this order?')">
                                            <i class="fas fa-check-circle"></i> Order Received
                                        </button>
                                    </form>
                                <?php endif; ?>
                                <?php if ($order['status'] === 'ready_for_pickup'): ?>
                                    <form action="<?= base_url('/order/pickup/' . $order['id']) ?>" method="post" style="display: inline;">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn-action btn-primary" onclick="return confirm('Confirm pickup of this order?')">
                                            <i class="fas fa-check-double"></i> Confirm Pickup
                                        </button>
                                    </form>
                                <?php endif; ?>
                                <?php if ($order['status'] === 'pending'): ?>
                                    <form action="<?= base_url('/order/cancel/' . $order['id']) ?>" method="post" style="display: inline;">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn-action btn-danger" onclick="return confirm('Cancel this order?')">
                                            <i class="fas fa-times-circle"></i> Cancel
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-shopping-bag"></i>
                    <h3>No Orders Yet</h3>
                    <p>You haven't placed any orders yet. Start shopping to see your orders here!</p>
                    <a href="<?= base_url('/products') ?>" class="btn-shop">
                        <i class="fas fa-store"></i> Start Shopping
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 CREATRIX. All rights reserved. | For educational purposes only, and no copyright infringement is intended.</p>
        <p style="margin-top: 8px; font-size: 0.8rem;">CREATRIX Group - Promoting Sustainable Living Through Coconut Coir Innovation</p>
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

        // Tab Filtering
        const tabBtns = document.querySelectorAll('.tab-btn');
        const orderCards = document.querySelectorAll('.order-card');
        
        function filterOrders(status) {
            let visibleCount = 0;
            
            orderCards.forEach(card => {
                const cardStatus = card.dataset.status;
                
                if (status === 'all' || cardStatus === status) {
                    card.style.display = 'block';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });
            
            const ordersContainer = document.getElementById('orders-container');
            const existingEmpty = ordersContainer.querySelector('.empty-state:not(.original-empty)');
            
            if (visibleCount === 0 && orderCards.length > 0) {
                if (!existingEmpty) {
                    const emptyDiv = document.createElement('div');
                    emptyDiv.className = 'empty-state';
                    emptyDiv.innerHTML = `
                        <i class="fas fa-box-open"></i>
                        <h3>No Orders Found</h3>
                        <p>You don't have any orders in this category.</p>
                        <a href="<?= base_url('/products') ?>" class="btn-shop">
                            <i class="fas fa-store"></i> Start Shopping
                        </a>
                    `;
                    ordersContainer.appendChild(emptyDiv);
                }
            } else if (existingEmpty && visibleCount > 0) {
                existingEmpty.remove();
            }
        }
        
        tabBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                tabBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                
                const status = btn.dataset.status;
                filterOrders(status);
            });
        });
    </script>
</body>
</html>