<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>All Products | CREATRIX</title>
    
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
            padding: 12px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--shadow-md);
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
        .desktop-nav {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
        }

        .desktop-nav a {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 30px;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
            white-space: nowrap;
        }

        .desktop-nav a:hover {
            background: rgba(255, 255, 255, 0.15);
            color: var(--white);
        }

        .desktop-nav a.active {
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
            z-index: 999;
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

        .mobile-menu .mobile-btns {
            display: flex;
            flex-direction: column;
            gap: 12px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
        }

        .mobile-menu .mobile-btns a {
            text-align: center;
            padding: 12px 20px;
            border-radius: 30px;
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
            font-size: 2.2rem;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 8px;
        }

        .page-header p {
            font-size: 1rem;
            color: var(--text-light);
        }

        .filter-section {
            background: var(--card-bg);
            padding: 24px;
            border-radius: 20px;
            box-shadow: var(--shadow-sm);
            margin-bottom: 40px;
        }

        .filter-section form {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            align-items: flex-end;
        }

        .filter-group {
            flex: 1;
            min-width: 160px;
        }

        .filter-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
            color: var(--text);
            font-size: 0.85rem;
        }

        .filter-group label i {
            color: var(--accent);
            margin-right: 6px;
        }

        .filter-group select, 
        .filter-group input {
            width: 100%;
            padding: 10px 14px;
            border: 2px solid var(--accent-light);
            border-radius: 12px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.9rem;
            transition: var(--transition);
            background: var(--white);
        }

        .filter-group select:focus, 
        .filter-group input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(42, 92, 55, 0.1);
        }

        .btn-filter {
            background: var(--accent);
            color: white;
            border: none;
            padding: 10px 28px;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 500;
            transition: var(--transition);
            height: 44px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-filter:hover {
            background: var(--accent-hover);
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
            gap: 24px;
            margin-bottom: 48px;
        }

        .product-card {
            background: var(--card-bg);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-hover);
        }

        .product-image {
            height: auto;
            aspect-ratio: 4 / 3;
            background: var(--accent-light);
            overflow: hidden;
            cursor: pointer;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s;
        }

        .product-card:hover .product-image img {
            transform: scale(1.05);
        }

        .product-info {
            padding: 20px 16px 16px;
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .product-category {
            color: var(--accent);
            font-size: 0.7rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 6px;
        }

        .product-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin: 0 0 8px 0;
            color: var(--text);
            line-height: 1.4;
            cursor: pointer;
        }

        .product-price {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 12px;
        }

        .stock-indicator {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.8rem;
            margin-bottom: 12px;
        }

        .stock-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
        }

        .stock-dot.in { background: var(--accent); }
        .stock-dot.low { background: #f59e0b; }
        .stock-dot.out { background: #ef4444; }

        .product-actions {
            display: flex;
            gap: 12px;
        }

        .btn-add-cart,
        .btn-buy-now {
            flex: 1;
            background: var(--accent);
            color: white;
            border: none;
            padding: 10px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
        }

        .btn-buy-now {
            background: var(--primary-light);
        }

        .btn-buy-now:hover {
            background: var(--primary);
        }

        .btn-add-cart:hover {
            background: var(--accent-hover);
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 40px;
        }

        .pagination a, 
        .pagination span {
            padding: 8px 14px;
            border: none;
            border-radius: 30px;
            color: var(--text);
            text-decoration: none;
            transition: var(--transition);
            background: var(--card-bg);
            box-shadow: var(--shadow-sm);
            font-weight: 500;
        }

        .pagination a:hover {
            background: var(--accent-light);
            color: var(--accent);
        }

        .pagination .active {
            background: var(--accent);
            color: white;
        }

        .empty-section {
            text-align: center;
            padding: 60px 20px;
            background: var(--card-bg);
            border-radius: 30px;
            color: var(--text-light);
            margin-bottom: 40px;
        }

        .empty-section i {
            font-size: 3.5rem;
            color: var(--accent-light);
            margin-bottom: 16px;
        }

        .empty-section h3 {
            font-size: 1.5rem;
            font-weight: 500;
            color: var(--text);
        }

        .btn-reset {
            background: var(--accent);
            color: white;
            border: none;
            padding: 10px 28px;
            border-radius: 30px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-reset:hover {
            background: var(--accent-hover);
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

        .toast-close {
            background: none;
            border: none;
            color: var(--text-light);
            cursor: pointer;
        }

        footer {
            background: linear-gradient(135deg, var(--primary) 0%, #1e2a1a 100%);
            color: rgba(255,255,255,0.85);
            padding: 40px 5% 24px;
            margin-top: 60px;
            text-align: center;
            font-size: 0.9rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .desktop-nav {
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
            
            .product-grid {
                grid-template-columns: 1fr;
            }
            
            .filter-section form {
                flex-direction: column;
            }
            
            .filter-group {
                width: 100%;
            }
            
            .btn-filter {
                width: 100%;
                justify-content: center;
            }
            
            .page-header h1 {
                font-size: 1.6rem;
            }
            
            .container {
                padding: 0 16px;
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
                width: 38px;
                height: 38px;
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
            <img src="<?= base_url('assets/creatrix_logo.png') ?>" alt="CREATRIX Logo" onerror="this.src='https://via.placeholder.com/40x40/FFFFFF/2A5C37?text=C'">
            <h2>CREATRIX COIR</h2>
        </div>
        
        <!-- Desktop Navigation -->
        <div class="desktop-nav">
            <a href="<?= base_url('/storefront') ?>"><i class="fas fa-store"></i> Store</a>
            <a href="<?= base_url('/products') ?>" class="active"><i class="fas fa-box"></i> Products</a>
            <a href="<?= base_url('/cart') ?>">
                <i class="fas fa-shopping-cart"></i> Cart 
                <span class="cart-badge" id="cart-badge">0</span>
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
            <h1>All Products</h1>
            <p><i class="fas fa-box"></i> <?= isset($products) ? count($products) : 0 ?> eco-friendly products</p>
        </div>

        <div class="filter-section">
            <form action="<?= base_url('/products') ?>" method="get">
                <div class="filter-group">
                    <label><i class="fas fa-tag"></i> Category</label>
                    <select name="category">
                        <option value="">All Categories</option>
                        <option value="Gardening" <?= isset($category) && $category == 'Gardening' ? 'selected' : '' ?>>Gardening</option>
                        <option value="Home & Living" <?= isset($category) && $category == 'Home & Living' ? 'selected' : '' ?>>Home & Living</option>
                        <option value="Agriculture" <?= isset($category) && $category == 'Agriculture' ? 'selected' : '' ?>>Agriculture</option>
                        <option value="Packaging" <?= isset($category) && $category == 'Packaging' ? 'selected' : '' ?>>Packaging</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label><i class="fas fa-sort"></i> Sort By</label>
                    <select name="sort">
                        <option value="newest" <?= isset($sort) && $sort == 'newest' ? 'selected' : '' ?>>Newest First</option>
                        <option value="price_low" <?= isset($sort) && $sort == 'price_low' ? 'selected' : '' ?>>Price: Low to High</option>
                        <option value="price_high" <?= isset($sort) && $sort == 'price_high' ? 'selected' : '' ?>>Price: High to Low</option>
                        <option value="name" <?= isset($sort) && $sort == 'name' ? 'selected' : '' ?>>Name A-Z</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label><i class="fas fa-search"></i> Search</label>
                    <input type="text" name="search" placeholder="Search products..." value="<?= isset($search) ? $search : '' ?>">
                </div>
                <button type="submit" class="btn-filter">
                    <i class="fas fa-filter"></i> Apply
                </button>
            </form>
        </div>

        <?php if (!empty($products)): ?>
        <div class="product-grid">
            <?php foreach ($products as $product): ?>
            <div class="product-card">
                <a href="<?= base_url('/product-details/' . $product['id']) ?>" class="product-image-link">
                    <div class="product-image">
                        <img src="<?= base_url('uploads/products/' . ($product['image'] ?? 'default.jpg')) ?>" 
                             alt="<?= esc($product['product_name']) ?>"
                             onerror="this.src='https://via.placeholder.com/300x220/DCE7E0/2A5C37?text=Coir'">
                    </div>
                </a>
                <div class="product-info">
                    <a href="<?= base_url('/product-details/' . $product['id']) ?>" style="text-decoration: none;">
                        <div class="product-category"><?= esc($product['category']) ?></div>
                        <h3 class="product-title"><?= esc($product['product_name']) ?></h3>
                    </a>
                    <div class="product-price">₱<?= number_format($product['price'], 2) ?></div>
                    
                    <div class="stock-indicator">
                        <?php if (isset($product['stock']) && $product['stock'] > 0): ?>
                            <?php if ($product['stock'] < 10): ?>
                                <span class="stock-dot low"></span>
                                <span>Only <?= $product['stock'] ?> left</span>
                            <?php else: ?>
                                <span class="stock-dot in"></span>
                                <span>In Stock</span>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="stock-dot out"></span>
                            <span>Out of Stock</span>
                        <?php endif; ?>
                    </div>

                    <?php if (isset($product['stock']) && $product['stock'] > 0): ?>
                        <div class="product-actions">
                            <button class="btn-add-cart add-to-cart-btn" data-product-id="<?= $product['id'] ?>">
                                <i class="fas fa-cart-plus"></i> Add to Cart
                            </button>
                            <button class="btn-buy-now buy-now-btn" data-product-id="<?= $product['id'] ?>">
                                <i class="fas fa-bolt"></i> Buy Now
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="product-actions">
                            <button class="btn-add-cart" disabled style="opacity: 0.5; cursor: not-allowed;">
                                <i class="fas fa-cart-plus"></i> Out of Stock
                            </button>
                            <button class="btn-buy-now" disabled style="opacity: 0.5; cursor: not-allowed;">
                                <i class="fas fa-bolt"></i> Out of Stock
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if (isset($pager) && $pager->getPageCount() > 1): ?>
        <div class="pagination">
            <?= $pager->links() ?>
        </div>
        <?php endif; ?>

        <?php else: ?>
        <div class="empty-section">
            <i class="fas fa-box-open"></i>
            <h3>No Products Found</h3>
            <p>Try adjusting your filters or search criteria.</p>
            <a href="<?= base_url('/products') ?>" class="btn-reset">
                <i class="fas fa-redo"></i> Reset Filters
            </a>
        </div>
        <?php endif; ?>
    </div>

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
        <p>&copy; 2026 CREATRIX. All rights reserved. | For educational purposes only.</p>
        <p>CREATRIX Group - Promoting Sustainable Living Through Coconut Coir Innovation</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Hamburger menu toggle - Slide from right
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

        hamburger.addEventListener('click', (e) => {
            e.stopPropagation();
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

        // Update cart badge function
        function updateCartBadge(count) {
            const badge = document.getElementById('cart-badge');
            if (badge) badge.textContent = count;
        }

        // Add to cart functionality
        document.querySelectorAll('.add-to-cart-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const productId = this.dataset.productId;
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
                this.disabled = true;
                
                fetch('<?= base_url('cart/add') ?>/' + productId, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: 'quantity=1'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateCartBadge(data.cartCount);
                        showToast('Added to Cart!', data.message);
                    } else {
                        showToast('Error', data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Error', 'Failed to add to cart', 'error');
                })
                .finally(() => {
                    this.innerHTML = originalText;
                    this.disabled = false;
                });
            });
        });

        // Buy now functionality
        document.querySelectorAll('.buy-now-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const productId = this.dataset.productId;
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                this.disabled = true;
                
                const body = new URLSearchParams({ quantity: 1 });
                
                fetch('<?= base_url('cart/buy-now') ?>/' + productId, {
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
                        updateCartBadge(data.cartCount);
                        window.location.href = '<?= base_url('/checkout') ?>';
                    } else {
                        showToast('Error', data.message, 'error');
                        this.innerHTML = originalText;
                        this.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Error', 'Failed to process', 'error');
                    this.innerHTML = originalText;
                    this.disabled = false;
                });
            });
        });
        
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

        // Initialize cart badge from PHP
        <?php if (session()->get('isLoggedIn')): ?>
            <?php 
            $cartModel = new \App\Models\CartModel();
            $cartItems = $cartModel->where('user_id', session()->get('id'))->findAll();
            $cartCount = array_sum(array_column($cartItems, 'quantity'));
            ?>
            document.getElementById('cart-badge').textContent = <?= $cartCount ?>;
        <?php endif; ?>
    </script>
</body>
</html>