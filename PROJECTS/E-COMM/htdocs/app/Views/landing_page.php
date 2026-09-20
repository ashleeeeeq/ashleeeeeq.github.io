<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>CREATRIX | Premium Coconut Coir Products</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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

        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg);
            color: var(--text);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Header */
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
            transition: transform 0.3s;
        }

        .logo:hover img {
            transform: scale(1.05);
        }

        .logo h2 {
            font-weight: 600;
            font-size: 1.5rem;
            margin: 0;
            white-space: nowrap;
        }

        /* Desktop Navigation */
        .nav-menu {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .nav-menu a {
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
            cursor: pointer;
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

        /* Header Buttons */
        .header-btns {
            display: flex;
            gap: 15px;
        }

        .header-btns a {
            padding: 8px 25px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
            display: inline-block;
            font-size: 0.95rem;
        }

        .btn-login { 
            background: transparent; 
            color: var(--white);
            border: 2px solid var(--white) !important;
        }

        .btn-login:hover { 
            background: var(--white); 
            color: var(--primary);
            transform: translateY(-2px);
        }

        .btn-register { 
            background: var(--accent); 
            color: var(--white);
            border: 2px solid var(--accent);
        }

        .btn-register:hover { 
            background: var(--accent-hover); 
            border-color: var(--accent-hover);
            transform: translateY(-2px);
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
            border-color: var(--accent-hover);
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(rgba(31, 69, 41, 0.85), rgba(42, 92, 55, 0.85)), url('https://images.unsplash.com/photo-1591857177580-dc82b9ac4e1e?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80') center/cover;
            color: var(--white);
            padding: 100px 5%;
            text-align: center;
        }

        .hero-content {
            max-width: 800px;
            margin: 0 auto;
        }

        .hero h1 {
            font-size: 3rem;
            margin-bottom: 20px;
            animation: fadeInUp 1s ease;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            font-weight: 700;
        }

        .hero p {
            font-size: 1.1rem;
            margin-bottom: 30px;
            animation: fadeInUp 1s ease 0.2s both;
            line-height: 1.8;
        }

        .hero-btns {
            animation: fadeInUp 1s ease 0.4s both;
        }

        .btn-primary {
            display: inline-block;
            padding: 14px 40px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.9rem;
            background: var(--accent);
            color: var(--white);
            border: 2px solid var(--accent);
        }

        .btn-primary:hover {
            background: var(--accent-hover);
            border-color: var(--accent-hover);
            transform: translateY(-3px);
            box-shadow: var(--shadow-hover);
        }

        /* Featured Products */
        .featured-section {
            padding: 80px 5%;
            background: var(--bg);
        }

        .section-title {
            text-align: center;
            margin-bottom: 50px;
        }

        .section-title h2 {
            font-size: 2.2rem;
            color: var(--primary);
            margin-bottom: 15px;
            position: relative;
            display: inline-block;
            font-weight: 700;
        }

        .section-title h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: var(--accent);
            border-radius: 2px;
        }

        .section-title p {
            color: var(--text-light);
            font-size: 1rem;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 30px;
            margin-top: 40px;
        }

        .product-card {
            background: var(--card-bg);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            position: relative;
            cursor: pointer;
        }

        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-hover);
        }

        .product-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: var(--accent);
            color: var(--white);
            padding: 5px 12px;
            border-radius: 25px;
            font-size: 0.7rem;
            font-weight: 600;
            z-index: 2;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .product-image {
            height: 220px;
            background: var(--accent-light);
            overflow: hidden;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s;
        }

        .product-card:hover .product-image img {
            transform: scale(1.08);
        }

        .product-info {
            padding: 20px;
        }

        .product-category {
            color: var(--accent);
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }

        .product-title {
            font-size: 1rem;
            font-weight: 600;
            margin: 8px 0;
            color: var(--text);
            line-height: 1.4;
        }

        .product-price {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 15px;
        }

        .product-actions {
            display: flex;
            gap: 10px;
        }

        .btn-add-cart {
            flex: 1;
            background: var(--accent);
            color: var(--white);
            border: none;
            padding: 10px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 0.85rem;
            text-decoration: none;
        }

        .btn-add-cart:hover {
            background: var(--accent-hover);
        }

        .btn-view {
            width: 42px;
            background: var(--accent-light);
            color: var(--accent);
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            text-decoration: none;
        }

        .btn-view:hover {
            background: var(--primary);
            color: var(--white);
        }

        /* Categories Section */
        .categories-section {
            padding: 80px 5%;
            background: var(--white);
        }

        .category-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 25px;
            margin-top: 40px;
        }

        .category-card {
            background: var(--card-bg);
            padding: 30px 20px;
            text-align: center;
            border-radius: 20px;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            text-decoration: none;
            color: var(--text);
            border: 2px solid transparent;
            display: block;
            cursor: pointer;
        }

        .category-card:hover {
            transform: translateY(-8px);
            border-color: var(--accent);
            box-shadow: var(--shadow-md);
        }

        .category-card i {
            font-size: 2.2rem;
            color: var(--primary-light);
            margin-bottom: 15px;
            transition: var(--transition);
        }

        .category-card:hover i {
            color: var(--accent);
            transform: scale(1.1);
        }

        .category-card h3 {
            font-size: 1rem;
            font-weight: 600;
            margin: 0;
        }

        /* Features Section */
        .features-section {
            padding: 60px 5%;
            background: var(--bg);
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 30px;
        }

        .feature-item {
            text-align: center;
            padding: 20px;
        }

        .feature-item i {
            font-size: 2.2rem;
            color: var(--accent);
            margin-bottom: 15px;
        }

        .feature-item h4 {
            font-size: 1.1rem;
            margin-bottom: 10px;
            color: var(--primary);
            font-weight: 600;
        }

        .feature-item p {
            color: var(--text-light);
            font-size: 0.85rem;
            line-height: 1.6;
        }

        /* About Section */
        .about-section {
            padding: 80px 5%;
            background: var(--white);
        }

        .about-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            align-items: center;
        }

        .about-text h2 {
            font-size: 2rem;
            color: var(--primary);
            margin-bottom: 20px;
            font-weight: 700;
        }

        .about-text p {
            font-size: 1rem;
            line-height: 1.8;
            color: var(--text);
            margin-bottom: 20px;
        }

        .about-image {
            height: 350px;
            background: var(--accent-light);
            border-radius: 25px;
            overflow: hidden;
            box-shadow: var(--shadow-md);
        }

        .about-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Footer */
        footer {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: var(--white);
            padding: 50px 5% 30px;
            margin-top: auto;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 40px;
            margin-bottom: 30px;
        }

        .footer-section h3 {
            font-size: 1.1rem;
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 10px;
            font-weight: 600;
        }

        .footer-section h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 2px;
            background: var(--white);
        }

        .footer-section p {
            line-height: 1.8;
            font-size: 0.85rem;
            opacity: 0.9;
        }

        .footer-section ul {
            list-style: none;
        }

        .footer-section ul li {
            margin-bottom: 10px;
        }

        .footer-section ul li a {
            color: var(--white);
            text-decoration: none;
            opacity: 0.9;
            transition: var(--transition);
            cursor: pointer;
            font-size: 0.85rem;
        }

        .footer-section ul li a:hover {
            opacity: 1;
            padding-left: 5px;
        }

        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 15px;
        }

        .social-links a {
            color: var(--white);
            font-size: 1.2rem;
            opacity: 0.9;
            transition: var(--transition);
            cursor: pointer;
        }

        .social-links a:hover {
            opacity: 1;
            transform: translateY(-3px);
        }

        .footer-bottom {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid rgba(255,255,255,0.2);
            font-size: 0.8rem;
            opacity: 0.9;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .product-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .category-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .nav-menu, .header-btns {
                display: none;
            }

            .hamburger {
                display: flex;
            }

            .hero h1 {
                font-size: 2rem;
            }

            .hero p {
                font-size: 1rem;
            }

            .product-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .category-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }

            .about-content {
                grid-template-columns: 1fr;
                gap: 30px;
            }

            .about-text h2 {
                font-size: 1.6rem;
            }

            .features-section {
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
            }

            .footer-content {
                grid-template-columns: 1fr;
                gap: 30px;
                text-align: center;
            }

            .footer-section h3::after {
                left: 50%;
                transform: translateX(-50%);
            }

            .social-links {
                justify-content: center;
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

            .hero h1 {
                font-size: 1.6rem;
            }

            .hero {
                padding: 80px 5%;
            }

            .section-title h2 {
                font-size: 1.6rem;
            }

            .category-grid {
                grid-template-columns: 1fr;
            }

            .features-section {
                grid-template-columns: 1fr;
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

        <div class="nav-menu">
            <a href="#products"><i class="fas fa-store"></i> Products</a>
            <a href="#about"><i class="fas fa-users"></i> About Us</a>
            <a href="#contact"><i class="fas fa-headset"></i> Contact</a>
        </div>

        <div class="header-btns">
            <a class="btn-login" href="<?= base_url('/login') ?>"><i class="fas fa-sign-in-alt"></i> Login</a>
            <a class="btn-register" href="<?= base_url('/register') ?>"><i class="fas fa-user-plus"></i> Register</a>
        </div>

        <button class="hamburger" id="hamburger">
            <i class="fas fa-bars"></i>
        </button>
    </header>

    <!-- Overlay -->
    <div class="menu-overlay" id="menuOverlay"></div>

    <!-- Mobile Menu -->
    <div class="mobile-menu" id="mobileMenu">
        <nav>
            <a href="#products" class="mobile-nav-link"><i class="fas fa-store"></i> Products</a>
            <a href="#about" class="mobile-nav-link"><i class="fas fa-users"></i> About Us</a>
            <a href="#contact" class="mobile-nav-link"><i class="fas fa-headset"></i> Contact</a>
        </nav>
        <div class="mobile-btns">
            <a href="<?= base_url('/login') ?>" class="mobile-login"><i class="fas fa-sign-in-alt"></i> Login</a>
            <a href="<?= base_url('/register') ?>" class="mobile-register"><i class="fas fa-user-plus"></i> Register</a>
        </div>
    </div>

    <main>
        <!-- Hero Section -->
        <section class="hero" id="home">
            <div class="hero-content">
                <h1>Welcome to CREATRIX COIR!</h1>
                <p>Discover our eco-friendly coconut coir products - sustainable solutions for agriculture, gardening, and beyond. Join us in promoting a greener future.</p>
                <div class="hero-btns">
                    <a href="<?= base_url('/login') ?>" class="btn-primary"><i class="fas fa-shopping-bag"></i> Shop Now</a>
                </div>
            </div>
        </section>

        <!-- Featured Products -->
        <section class="featured-section" id="products">
            <div class="section-title">
                <h2>Featured Products</h2>
                <p>Discover our most popular coconut coir products</p>
            </div>

            <div class="product-grid">
                <?php if (!empty($featuredProducts)): ?>
                    <?php foreach ($featuredProducts as $product): ?>
                    <div class="product-card" onclick="window.location.href='<?= base_url('/login') ?>'">
                        <?php if ($product['featured'] == 'new'): ?>
                            <span class="product-badge">New</span>
                        <?php elseif ($product['featured'] == 'best_seller'): ?>
                            <span class="product-badge" style="background: var(--primary-light);">Best Seller</span>
                        <?php elseif ($product['featured'] == 'trending'): ?>
                            <span class="product-badge" style="background: #E53E3E;">Trending</span>
                        <?php endif; ?>
                        
                        <div class="product-image">
                            <img src="<?= base_url('uploads/products/' . ($product['image'] ?? 'default.jpg')) ?>" 
                                alt="<?= esc($product['product_name']) ?>"
                                onerror="this.src='https://via.placeholder.com/400x300/DCE7E0/2A5C37?text=CREATRIX'">
                        </div>
                        <div class="product-info">
                            <div class="product-category"><?= esc($product['category']) ?></div>
                            <h3 class="product-title"><?= esc($product['product_name']) ?></h3>
                            <div class="product-price">₱<?= number_format($product['price'], 2) ?></div>
                            <div class="product-actions">
                                <button class="btn-add-cart" onclick="event.stopPropagation(); window.location.href='<?= base_url('/login') ?>'">
                                    <i class="fas fa-shopping-cart"></i> Add to Cart
                                </button>
                                <button class="btn-view" onclick="event.stopPropagation(); window.location.href='<?= base_url('/login') ?>'">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Fallback static products -->
                    <div class="product-card" onclick="window.location.href='<?= base_url('/login') ?>'">
                        <span class="product-badge">New</span>
                        <div class="product-image">
                            <img src="https://images.unsplash.com/photo-1591857177580-dc82b9ac4e1e?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" alt="Coconut Coir Pot">
                        </div>
                        <div class="product-info">
                            <div class="product-category">Gardening</div>
                            <h3 class="product-title">Biodegradable Coir Pots</h3>
                            <div class="product-price">₱120.00</div>
                            <div class="product-actions">
                                <button class="btn-add-cart" onclick="event.stopPropagation(); window.location.href='<?= base_url('/login') ?>'">Add to Cart</button>
                                <button class="btn-view" onclick="event.stopPropagation(); window.location.href='<?= base_url('/login') ?>'"><i class="fas fa-eye"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="product-card" onclick="window.location.href='<?= base_url('/login') ?>'">
                        <span class="product-badge" style="background: var(--primary-light);">Best Seller</span>
                        <div class="product-image">
                            <img src="https://images.unsplash.com/photo-1591857177580-dc82b9ac4e1e?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" alt="Coir Brush">
                        </div>
                        <div class="product-info">
                            <div class="product-category">Home & Living</div>
                            <h3 class="product-title">Natural Coir Brush</h3>
                            <div class="product-price">₱180.00</div>
                            <div class="product-actions">
                                <button class="btn-add-cart" onclick="event.stopPropagation(); window.location.href='<?= base_url('/login') ?>'">Add to Cart</button>
                                <button class="btn-view" onclick="event.stopPropagation(); window.location.href='<?= base_url('/login') ?>'"><i class="fas fa-eye"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="product-card" onclick="window.location.href='<?= base_url('/login') ?>'">
                        <span class="product-badge" style="background: #E53E3E;">Hot</span>
                        <div class="product-image">
                            <img src="https://images.unsplash.com/photo-1591857177580-dc82b9ac4e1e?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" alt="Coir Mat">
                        </div>
                        <div class="product-info">
                            <div class="product-category">Home Decor</div>
                            <h3 class="product-title">Eco-Friendly Doormat</h3>
                            <div class="product-price">₱350.00</div>
                            <div class="product-actions">
                                <button class="btn-add-cart" onclick="event.stopPropagation(); window.location.href='<?= base_url('/login') ?>'">Add to Cart</button>
                                <button class="btn-view" onclick="event.stopPropagation(); window.location.href='<?= base_url('/login') ?>'"><i class="fas fa-eye"></i></button>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Categories Section -->
        <section class="categories-section">
            <div class="section-title">
                <h2>Shop by Category</h2>
                <p>Explore our wide range of coconut coir products</p>
            </div>
            <div class="category-grid">
                <div class="category-card" onclick="window.location.href='<?= base_url('/login') ?>'">
                    <i class="fas fa-seedling"></i>
                    <h3>Gardening</h3>
                </div>
                <div class="category-card" onclick="window.location.href='<?= base_url('/login') ?>'">
                    <i class="fas fa-home"></i>
                    <h3>Home & Living</h3>
                </div>
                <div class="category-card" onclick="window.location.href='<?= base_url('/login') ?>'">
                    <i class="fas fa-tractor"></i>
                    <h3>Agriculture</h3>
                </div>
                <div class="category-card" onclick="window.location.href='<?= base_url('/login') ?>'">
                    <i class="fas fa-box"></i>
                    <h3>Packaging</h3>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="features-section">
            <div class="feature-item">
                <i class="fas fa-leaf"></i>
                <h4>100% Eco-Friendly</h4>
                <p>Made from natural coconut coir, sustainable and biodegradable</p>
            </div>
            <div class="feature-item">
                <i class="fas fa-truck"></i>
                <h4>Free Shipping</h4>
                <p>Free delivery for orders above ₱1,000 within Metro Manila</p>
            </div>
            <div class="feature-item">
                <i class="fas fa-shield-alt"></i>
                <h4>Quality Guaranteed</h4>
                <p>Highest quality standards for all our products</p>
            </div>
            <div class="feature-item">
                <i class="fas fa-headset"></i>
                <h4>24/7 Support</h4>
                <p>Our customer service team is always ready to assist</p>
            </div>
        </section>

        <!-- About Section -->
        <section class="about-section" id="about">
            <div class="about-content">
                <div class="about-text">
                    <h2>About CREATRIX</h2>
                    <p>We are dedicated to providing high-quality, sustainable coconut coir products while promoting eco-friendly practices and supporting local communities in the Philippines.</p>
                    <p>Our mission is to transform coconut husks into valuable, eco-friendly products that benefit both people and the planet. From gardening to home living, our products are designed with sustainability in mind.</p>
                </div>
                <div class="about-image">
                    <img src="https://images.unsplash.com/photo-1591857177580-dc82b9ac4e1e?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" alt="About CREATRIX">
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>About CREATRIX</h3>
                <p>We provide high-quality, sustainable coconut coir products while promoting eco-friendly practices and supporting local communities.</p>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                </div>
            </div>
            <div class="footer-section">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="#products">Products</a></li>
                    <li><a href="#about">About Us</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Contact Info</h3>
                <ul>
                    <li><i class="fas fa-map-marker-alt"></i> Manila, Philippines</li>
                    <li><i class="fas fa-phone"></i> +63 (2) 1234 5678</li>
                    <li><i class="fas fa-envelope"></i> info@creatrix.com</li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 CREATRIX. All rights reserved. | For educational purposes only, no copyright infringement intended.</p>
            <p>CREATRIX Group - Promoting Sustainable Living Through Coconut Coir Innovation</p>
        </div>
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
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const targetId = link.getAttribute('href');
                const target = document.querySelector(targetId);
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
                closeMenu();
            });
        });

        // Close menu on window resize (if screen becomes desktop)
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768 && mobileMenu.classList.contains('active')) {
                closeMenu();
            }
        });

        // Smooth scrolling for desktop nav links
        document.querySelectorAll('.nav-menu a, .hero-btns a').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                if (href && href.startsWith('#')) {
                    e.preventDefault();
                    const target = document.querySelector(href);
                    if (target) {
                        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }
            });
        });
    </script>
</body>
</html>