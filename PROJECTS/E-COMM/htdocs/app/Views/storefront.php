<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Storefront | CREATRIX</title>
    
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
            --shadow-md: 0 10px 20px rgba(31, 69, 41, 0.08);
            --shadow-hover: 0 15px 30px rgba(42, 92, 55, 0.15);
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            --danger: #E53E3E;
            --warning: #F59E0B;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg);
            color: var(--text);
            margin: 0;
            padding: 0;
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
            height: 42px;
            filter: brightness(0) invert(1);
            transition: transform 0.3s;
        }

        .logo:hover img {
            transform: scale(1.05);
        }

        .logo h2 {
            font-weight: 700;
            font-size: 1.4rem;
            margin: 0;
            letter-spacing: -0.5px;
        }

        /* Desktop Navigation */
        .nav-menu {
            display: flex;
            gap: 8px;
            align-items: center;
            margin: 0 20px;
        }

        .nav-menu a {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            font-weight: 500;
            padding: 8px 18px;
            border-radius: 30px;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
            white-space: nowrap;
        }

        .nav-menu a:hover {
            background: rgba(255, 255, 255, 0.15);
            color: var(--white);
            transform: translateY(-2px);
        }

        .nav-menu a.active {
            background: var(--white);
            color: var(--primary);
            font-weight: 600;
            box-shadow: var(--shadow-sm);
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

        .container {
            max-width: 1400px;
            margin: 40px auto;
            padding: 0 30px;
        }

        .hero-banner {
            background: linear-gradient(rgba(31, 69, 41, 0.75), rgba(42, 92, 55, 0.75)), url('https://images.unsplash.com/photo-1591857177580-dc82b9ac4e1e?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 40px;
            border-radius: 28px;
            margin-bottom: 50px;
            text-align: center;
            box-shadow: var(--shadow-md);
        }

        .hero-banner h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }

        .hero-banner p {
            font-size: 1.2rem;
            max-width: 600px;
            margin: 0 auto;
            opacity: 0.9;
            font-weight: 300;
        }

        .section-title {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--primary);
            margin: 50px 0 30px;
            text-align: center;
            position: relative;
        }

        .section-title::after {
            content: '';
            display: block;
            width: 60px;
            height: 4px;
            background: var(--accent);
            margin: 15px auto;
            border-radius: 2px;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            margin-bottom: 60px;
        }

        .product-card {
            background: var(--card-bg);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            position: relative;
            border: 1px solid rgba(0,0,0,0.03);
            display: flex;
            flex-direction: column;
            cursor: pointer;
        }

        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-hover);
            border-color: var(--accent-light);
        }

        .product-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            padding: 6px 16px;
            border-radius: 25px;
            font-size: 0.75rem;
            font-weight: 600;
            z-index: 2;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .badge-new {
            background: var(--accent);
            color: white;
        }

        .badge-best-seller {
            background: var(--primary-light);
            color: white;
        }

        .badge-trending {
            background: #E53E3E;
            color: white;
        }

        .product-image {
            height: auto;
            aspect-ratio: 4 / 3;
            background: var(--accent-light);
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            max-width: 100%;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            transition: transform 0.6s;
        }

        .product-card:hover .product-image img {
            transform: scale(1.05);
        }

        .product-info {
            padding: 25px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .product-category {
            color: var(--accent);
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 8px;
        }

        .product-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 12px;
            color: var(--text);
            line-height: 1.4;
        }

        .product-price {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 20px;
            margin-top: auto;
        }

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
            padding: 12px;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 600;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 0.9rem;
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

        .category-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 25px;
            margin: 40px 0 60px;
        }

        .category-card {
            background: var(--card-bg);
            padding: 35px 20px;
            text-align: center;
            border-radius: 20px;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            text-decoration: none;
            color: var(--text);
            border: 2px solid transparent;
            display: block;
        }

        .category-card:hover {
            transform: translateY(-8px);
            border-color: var(--accent);
            box-shadow: var(--shadow-md);
        }

        .category-card i {
            font-size: 2.5rem;
            color: var(--primary-light);
            margin-bottom: 15px;
            transition: var(--transition);
        }

        .category-card:hover i {
            color: var(--accent);
            transform: scale(1.1);
        }

        .category-card h3 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .empty-section {
            text-align: center;
            padding: 60px 20px;
            background: var(--white);
            border-radius: 20px;
            color: var(--text-light);
            margin-bottom: 60px;
            border: 2px dashed #E2E8F0;
        }

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
            background: linear-gradient(135deg, var(--primary) 0%, #1e2a1a 100%);
            color: rgba(255,255,255,0.85);
            padding: 50px 5% 30px;
            margin-top: 70px;
            text-align: center;
            font-size: 0.9rem;
        }

        footer p {
            margin: 8px 0;
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .nav-menu a {
                padding: 8px 14px;
                font-size: 0.85rem;
            }
        }

        @media (max-width: 992px) {
            .product-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 25px;
            }
            .category-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .hero-banner h1 {
                font-size: 2.8rem;
            }
            
            .nav-menu {
                gap: 5px;
                margin: 0 10px;
            }
            
            .nav-menu a {
                padding: 8px 12px;
                font-size: 0.8rem;
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
                padding: 12px 5%;
            }
            
            .logo img {
                height: 32px;
            }
            
            .logo h2 {
                font-size: 1.1rem;
            }
            
            .product-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .category-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .hero-banner {
                padding: 60px 20px;
            }
            
            .hero-banner h1 {
                font-size: 2rem;
            }
            
            .hero-banner p {
                font-size: 1rem;
            }
            
            .section-title {
                font-size: 1.6rem;
            }
            
            .container {
                padding: 0 20px;
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
            
            .hero-banner h1 {
                font-size: 1.5rem;
            }
            
            .product-title {
                font-size: 1rem;
            }
            
            .product-price {
                font-size: 1.2rem;
            }
            
            .btn-add-cart, .btn-buy-now {
                padding: 10px;
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <img src="<?= base_url('assets/creatrix_logo.png') ?>" alt="CREATRIX Logo" onerror="this.src='https://via.placeholder.com/42x42/FFFFFF/2A5C37?text=C'">
            <h2>CREATRIX COIR</h2>
        </div>
        
        <!-- Desktop Navigation -->
        <div class="nav-menu">
            <a href="<?= base_url('/storefront') ?>" class="active"><i class="fas fa-store"></i> Store</a>
            <a href="<?= base_url('/products') ?>"><i class="fas fa-box"></i> Products</a>
            <a href="<?= base_url('/cart') ?>">
                <i class="fas fa-shopping-cart"></i> Cart 
                <span class="cart-badge" id="cart-badge">0</span>
            </a>
            <?php if (session()->get('isLoggedIn')): ?>
                <a href="<?= base_url('/transactions') ?>"><i class="fas fa-history"></i> My Orders</a>
                <a href="<?= base_url('/profile') ?>"><i class="fas fa-user"></i> <?= esc(session()->get('fullname')) ?></a>
                <a href="<?= base_url('/logout') ?>"><i class="fas fa-sign-out-alt"></i> Logout</a>
            <?php endif; ?>
        </div>

        <!-- Hamburger Button -->
        <button class="hamburger" id="hamburger">
            <i class="fas fa-bars"></i>
        </button>
    </header>

    <!-- Overlay -->
    <div class="menu-overlay" id="menuOverlay"></div>

    <!-- Mobile Menu - Slide from right -->
    <div class="mobile-menu" id="mobileMenu">
        <nav>
            <a href="<?= base_url('/storefront') ?>" class="mobile-nav-link"><i class="fas fa-store"></i> Store</a>
            <a href="<?= base_url('/products') ?>" class="mobile-nav-link"><i class="fas fa-box"></i> Products</a>
            <a href="<?= base_url('/cart') ?>" class="mobile-nav-link"><i class="fas fa-shopping-cart"></i> Cart <span class="cart-badge" id="mobileCartBadge">0</span></a>
            <?php if (session()->get('isLoggedIn')): ?>
                <a href="<?= base_url('/transactions') ?>" class="mobile-nav-link"><i class="fas fa-history"></i> My Orders</a>
                <a href="<?= base_url('/profile') ?>" class="mobile-nav-link"><i class="fas fa-user"></i> Profile</a>
                <a href="<?= base_url('/logout') ?>" class="mobile-nav-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
            <?php endif; ?>
        </nav>
    </div>

    <div class="container">
        <div class="hero-banner">
            <h1>Eco-Friendly Coconut Coir Products</h1>
            <p>Sustainable solutions for gardening, agriculture, and home living</p>
        </div>

        <h2 class="section-title">Shop by Category</h2>
        <div class="category-grid">
            <a href="<?= base_url('/products/category/gardening') ?>" class="category-card">
                <i class="fas fa-seedling"></i>
                <h3>Gardening</h3>
            </a>
            <a href="<?= base_url('/products/category/home-living') ?>" class="category-card">
                <i class="fas fa-home"></i>
                <h3>Home & Living</h3>
            </a>
            <a href="<?= base_url('/products/category/agriculture') ?>" class="category-card">
                <i class="fas fa-tractor"></i>
                <h3>Agriculture</h3>
            </a>
            <a href="<?= base_url('/products/category/packaging') ?>" class="category-card">
                <i class="fas fa-box"></i>
                <h3>Packaging</h3>
            </a>
        </div>

        <?php if (!empty($newProducts)): ?>
        <h2 class="section-title">New Arrivals</h2>
        <div class="product-grid">
            <?php foreach (array_slice($newProducts, 0, 3) as $product): ?>
            <div class="product-card" data-url="<?= base_url('/product-details/' . $product['id']) ?>">
                <span class="product-badge badge-new">New</span>
                <div class="product-image">
                    <img src="<?= base_url('uploads/products/' . ($product['image'] ?? 'default.jpg')) ?>" 
                         alt="<?= esc($product['product_name']) ?>"
                         onerror="this.src='https://via.placeholder.com/400x300/DCE7E0/2A5C37?text=Coir'">
                </div>
                <div class="product-info">
                    <div class="product-category"><?= esc($product['category']) ?></div>
                    <h3 class="product-title"><?= esc($product['product_name']) ?></h3>
                    <div class="product-price">₱<?= number_format($product['price'], 2) ?></div>
                    <div class="product-actions">
                        <button class="btn-add-cart add-to-cart-btn" data-product-id="<?= $product['id'] ?>">
                            <i class="fas fa-cart-plus"></i> Add to Cart
                        </button>
                        <button class="btn-buy-now buy-now-btn" data-product-id="<?= $product['id'] ?>">
                            <i class="fas fa-shopping-bag"></i> Buy Now
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-section">
            <i class="fas fa-box-open" style="font-size: 3.5rem; color: var(--text-light); margin-bottom: 15px;"></i>
            <h3 style="color: var(--text);">No New Products Yet</h3>
            <p>Check back soon for new arrivals</p>
        </div>
        <?php endif; ?>

        <?php if (!empty($bestSellers)): ?>
        <h2 class="section-title">Best Sellers</h2>
        <div class="product-grid">
            <?php foreach (array_slice($bestSellers, 0, 3) as $product): ?>
            <div class="product-card" data-url="<?= base_url('/product-details/' . $product['id']) ?>">
                <span class="product-badge badge-best-seller">Best Seller</span>
                <div class="product-image">
                    <img src="<?= base_url('uploads/products/' . ($product['image'] ?? 'default.jpg')) ?>" 
                         alt="<?= esc($product['product_name']) ?>"
                         onerror="this.src='https://via.placeholder.com/400x300/DCE7E0/2A5C37?text=Coir'">
                </div>
                <div class="product-info">
                    <div class="product-category"><?= esc($product['category']) ?></div>
                    <h3 class="product-title"><?= esc($product['product_name']) ?></h3>
                    <div class="product-price">₱<?= number_format($product['price'], 2) ?></div>
                    <div class="product-actions">
                        <button class="btn-add-cart add-to-cart-btn" data-product-id="<?= $product['id'] ?>">
                            <i class="fas fa-cart-plus"></i> Add to Cart
                        </button>
                        <button class="btn-buy-now buy-now-btn" data-product-id="<?= $product['id'] ?>">
                            <i class="fas fa-shopping-bag"></i> Buy Now
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-section">
            <i class="fas fa-star" style="font-size: 3.5rem; color: var(--text-light); margin-bottom: 15px;"></i>
            <h3 style="color: var(--text);">No Best Sellers Yet</h3>
            <p>Products will appear here when they become popular</p>
        </div>
        <?php endif; ?>

        <?php if (!empty($trending)): ?>
        <h2 class="section-title">Trending Now</h2>
        <div class="product-grid">
            <?php foreach (array_slice($trending, 0, 3) as $product): ?>
            <div class="product-card" data-url="<?= base_url('/product-details/' . $product['id']) ?>">
                <span class="product-badge badge-trending">Trending</span>
                <div class="product-image">
                    <img src="<?= base_url('uploads/products/' . ($product['image'] ?? 'default.jpg')) ?>" 
                         alt="<?= esc($product['product_name']) ?>"
                         onerror="this.src='https://via.placeholder.com/400x300/DCE7E0/2A5C37?text=Coir'">
                </div>
                <div class="product-info">
                    <div class="product-category"><?= esc($product['category']) ?></div>
                    <h3 class="product-title"><?= esc($product['product_name']) ?></h3>
                    <div class="product-price">₱<?= number_format($product['price'], 2) ?></div>
                    <div class="product-actions">
                        <button class="btn-add-cart add-to-cart-btn" data-product-id="<?= $product['id'] ?>">
                            <i class="fas fa-cart-plus"></i> Add to Cart
                        </button>
                        <button class="btn-buy-now buy-now-btn" data-product-id="<?= $product['id'] ?>">
                            <i class="fas fa-shopping-bag"></i> Buy Now
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-section">
            <i class="fas fa-chart-line" style="font-size: 3.5rem; color: var(--text-light); margin-bottom: 15px;"></i>
            <h3 style="color: var(--text);">No Trending Products</h3>
            <p>Check back later for trending items</p>
        </div>
        <?php endif; ?>
    </div>

    <div id="toast" class="toast-notification" style="display: none;">
        <div class="toast-content">
            <div class="toast-icon"><i class="fas fa-check"></i></div>
            <div class="toast-message">
                <h4 id="toast-title">Success</h4>
                <p id="toast-message">Product added to cart</p>
            </div>
            <button class="toast-close" onclick="hideToast()"><i class="fas fa-times"></i></button>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 CREATRIX. All rights reserved. | For educational purposes only, and no copyright infringement is intended.</p>
        <p style="margin-top: 10px; font-size: 0.8rem;">CREATRIX Group - Promoting Sustainable Living Through Coconut Coir Innovation</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Hamburger menu toggle - slide from right
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

        // Update cart badge function
        function updateCartBadge(count) {
            const badges = document.querySelectorAll('#cart-badge, #mobileCartBadge');
            badges.forEach(badge => {
                if (badge) badge.textContent = count;
            });
        }

        // Make entire card clickable to product details
        document.querySelectorAll('.product-card').forEach(card => {
            const url = card.dataset.url;
            if (url) {
                card.addEventListener('click', function(e) {
                    if (e.target.closest('.add-to-cart-btn') || e.target.closest('.buy-now-btn')) {
                        return;
                    }
                    window.location.href = url;
                });
            }
        });

        // Add to cart
        document.querySelectorAll('.add-to-cart-btn').forEach(button => {
            button.addEventListener('click', function(e) {
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
                        showToast('Added to Cart', data.message);
                        const cartBadge = document.getElementById('cart-badge');
                        if (cartBadge) {
                            cartBadge.style.transform = 'scale(1.3)';
                            setTimeout(() => {
                                cartBadge.style.transform = 'scale(1)';
                            }, 200);
                        }
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

        // Buy now
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