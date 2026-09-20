<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Storefront | CREATRIX Admin</title>
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
            --danger: #E53E3E;
            --warning: #F59E0B;
            --info: #3498DB;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg);
            margin: 0;
            overflow-x: hidden;
        }
        
        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 280px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: var(--white);
            padding: 20px;
            overflow-y: auto;
            z-index: 1000;
            transition: all 0.3s ease;
        }
        
        .sidebar.closed {
            left: -280px;
        }
        
        .sidebar-header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 2px solid rgba(255,255,255,0.2);
            margin-bottom: 20px;
        }
        
        .sidebar-header .logo-img {
            width: 80px;
            height: 80px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
        }
        
        .sidebar-header .logo-img img {
            width: 50px;
            height: 50px;
            object-fit: contain;
            filter: brightness(0) invert(1);
        }
        
        .sidebar-header h3 {
            font-weight: 700;
            font-size: 1.5rem;
            margin: 0;
        }
        
        .sidebar-header p {
            font-size: 0.8rem;
            opacity: 0.8;
            margin-top: 5px;
        }
        
        .nav-link {
            color: var(--white) !important;
            padding: 12px 15px;
            border-radius: 10px;
            margin-bottom: 5px;
            transition: all 0.3s;
            display: block;
            text-decoration: none;
        }
        
        .nav-link:hover,
        .nav-link.active {
            background: rgba(255,255,255,0.2);
            transform: translateX(5px);
        }
        
        .nav-link i {
            margin-right: 10px;
            width: 20px;
        }
        
        /* Mobile Menu Toggle Button */
        .menu-toggle {
            display: none;
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1001;
            background: var(--accent);
            color: white;
            border: none;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: var(--shadow-md);
        }
        
        .menu-toggle:hover {
            background: var(--accent-hover);
            transform: scale(1.05);
        }
        
        /* Overlay for mobile menu */
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 999;
            transition: all 0.3s;
        }
        
        .overlay.active {
            display: block;
        }
        
        .main-content {
            margin-left: 280px;
            padding: 30px;
            transition: all 0.3s;
        }
        
        .header {
            background: var(--card-bg);
            padding: 25px 30px;
            border-radius: 20px;
            box-shadow: var(--shadow-sm);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .header h1 {
            font-size: 1.8rem;
            color: var(--primary);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .header h1 i {
            color: var(--accent);
            font-size: 1.6rem;
        }
        
        .header p {
            color: var(--text-light);
            margin: 5px 0 0 0;
            font-size: 0.85rem;
        }
        
        .btn-primary {
            background: var(--accent);
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            white-space: nowrap;
        }
        
        .btn-primary:hover {
            background: var(--accent-hover);
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }
        
        .storefront-card {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 25px;
            box-shadow: var(--shadow-sm);
            margin-bottom: 30px;
        }
        
        .storefront-card h3 {
            color: var(--primary);
            margin-bottom: 8px;
            font-size: 1.2rem;
            font-weight: 600;
        }
        
        .storefront-card .subtitle {
            color: var(--text-light);
            font-size: 0.85rem;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--accent-light);
        }
        
        .table-responsive {
            overflow-x: auto;
            margin-bottom: 20px;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px;
        }
        
        .table th {
            background: var(--accent-light);
            color: var(--text);
            font-weight: 600;
            padding: 14px 12px;
            text-align: left;
            font-size: 0.85rem;
        }
        
        .table td {
            padding: 14px 12px;
            border-bottom: 1px solid var(--accent-light);
            vertical-align: middle;
        }
        
        .table tr:hover {
            background: var(--accent-light);
        }
        
        .badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
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
        
        .badge-none {
            background: var(--text-light);
            color: white;
        }
        
        .form-select {
            padding: 8px 12px;
            border: 2px solid var(--accent-light);
            border-radius: 8px;
            background: var(--white);
            font-family: 'Poppins', sans-serif;
            font-size: 0.85rem;
            transition: all 0.3s;
            width: 100%;
        }
        
        .form-select:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(42, 92, 55, 0.1);
        }
        
        .product-name {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .product-name strong {
            color: var(--text);
            font-weight: 600;
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .alert-success {
            background: var(--accent-light);
            color: var(--accent);
            border: 1px solid var(--accent);
        }
        
        .alert-danger {
            background: #FFEBEE;
            color: var(--danger);
            border: 1px solid #FFCDD2;
        }
        
        .stats-row {
            display: flex;
            gap: 20px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }
        
        .stat-card {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 15px 20px;
            flex: 1;
            min-width: 150px;
            box-shadow: var(--shadow-sm);
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .stat-icon {
            width: 45px;
            height: 45px;
            background: var(--accent-light);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--accent);
            font-size: 1.3rem;
        }
        
        .stat-info h4 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            margin: 0;
        }
        
        .stat-info p {
            font-size: 0.75rem;
            color: var(--text-light);
            margin: 0;
        }
        
        .action-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        
        footer {
            margin-top: 30px;
            text-align: center;
            color: var(--text-light);
            font-size: 0.85rem;
            padding: 20px;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .menu-toggle {
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            .sidebar {
                left: -280px;
            }
            
            .sidebar.open {
                left: 0;
            }
            
            .main-content {
                margin-left: 0;
                padding: 70px 20px 20px;
            }
            
            .stats-row {
                flex-direction: column;
            }
            
            .header {
                flex-direction: column;
                text-align: center;
            }
            
            .header h1 {
                justify-content: center;
            }
            
            .btn-primary {
                width: 100%;
                justify-content: center;
            }
            
            .action-buttons {
                justify-content: center;
            }
            
            .storefront-card h3 {
                text-align: center;
            }
            
            .storefront-card .subtitle {
                text-align: center;
            }
            
            .table td {
                font-size: 0.85rem;
            }
            
            .form-select {
                font-size: 0.8rem;
                padding: 6px 10px;
            }
        }
        
        @media (max-width: 480px) {
            .stat-card {
                flex-direction: column;
                text-align: center;
            }
            
            .stat-icon {
                margin: 0 auto;
            }
            
            .badge {
                font-size: 0.7rem;
                padding: 4px 8px;
            }
            
            .product-name strong {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <!-- Mobile Menu Toggle Button -->
    <button class="menu-toggle" id="menuToggle">
        <i class="fas fa-bars"></i>
    </button>
    
    <!-- Overlay -->
    <div class="overlay" id="overlay"></div>

    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="logo-img">
                <img src="<?= base_url('assets/creatrix_logo.png') ?>" alt="CREATRIX">
            </div>
            <h3>CREATRIX COIR</h3>
            <p>Admin Panel</p>
        </div>
        <nav class="nav flex-column">
            <a class="nav-link" href="<?= base_url('/admin/dashboard') ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a class="nav-link active" href="<?= base_url('/admin/storefront') ?>"><i class="fas fa-store"></i> Storefront</a>
            <a class="nav-link" href="<?= base_url('/admin/inventory') ?>"><i class="fas fa-box"></i> Inventory</a>
            <a class="nav-link" href="<?= base_url('/admin/reports') ?>"><i class="fas fa-chart-line"></i> Sales Reports</a>
            <a class="nav-link" href="<?= base_url('/admin/inventory-report') ?>"><i class="fas fa-file-alt"></i> Inventory Report</a>
            <a class="nav-link" href="<?= base_url('/admin/orders') ?>"><i class="fas fa-shopping-cart"></i> Orders</a>
            <hr style="border-color: rgba(255,255,255,0.2);">
            <a class="nav-link" href="<?= base_url('/logout') ?>"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </div>

    <div class="main-content">
        <div class="header">
            <div>
                <h1><i class="fas fa-store"></i> Manage Storefront</h1>
                <p>Control what products appear on your storefront homepage</p>
            </div>
            <a href="<?= base_url('/admin/inventory') ?>" class="btn-primary">
                <i class="fas fa-box"></i> Manage Products
            </a>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>
        
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <!-- Statistics Cards -->
        <div class="stats-row">
            <?php 
            $newCount = 0;
            $trendingCount = 0;
            $bestSellerCount = 0;
            foreach ($allProducts as $product) {
                if ($product['featured'] == 'new') $newCount++;
                elseif ($product['featured'] == 'trending') $trendingCount++;
                elseif ($product['featured'] == 'best_seller') $bestSellerCount++;
            }
            ?>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-star"></i></div>
                <div class="stat-info">
                    <h4><?= $newCount ?></h4>
                    <p>New Arrivals</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
                <div class="stat-info">
                    <h4><?= $trendingCount ?></h4>
                    <p>Trending</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-trophy"></i></div>
                <div class="stat-info">
                    <h4><?= $bestSellerCount ?></h4>
                    <p>Best Sellers</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-boxes"></i></div>
                <div class="stat-info">
                    <h4><?= count($allProducts) ?></h4>
                    <p>Total Products</p>
                </div>
            </div>
        </div>

        <div class="storefront-card">
            <h3><i class="fas fa-star"></i> Featured Products Selection</h3>
            <div class="subtitle">
                Choose which products appear in "New Arrivals", "Trending", and "Best Sellers" sections on the storefront.
                <br><small><i class="fas fa-info-circle"></i> Only products with featured status will appear on the homepage.</small>
            </div>
            
            <form action="<?= base_url('/admin/storefront/update') ?>" method="post">
                <?= csrf_field() ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                             <tr>
                                <th>Product</th>
                                <th>Category</th>
                                <th>Current Status</th>
                                <th>Set Featured</th>
                              </thead>
                        <tbody>
                            <?php foreach ($allProducts as $product): ?>
                            <tr>
                                <td>
                                    <div class="product-name">
                                        <strong><?= esc($product['product_name']) ?></strong>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge" style="background: var(--info); color: white;">
                                        <?= esc($product['category']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($product['featured'] == 'new'): ?>
                                        <span class="badge badge-new">New Arrival</span>
                                    <?php elseif ($product['featured'] == 'best_seller'): ?>
                                        <span class="badge badge-best-seller">Best Seller</span>
                                    <?php elseif ($product['featured'] == 'trending'): ?>
                                        <span class="badge badge-trending">Trending</span>
                                    <?php else: ?>
                                        <span class="badge badge-none">None</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <select name="featured[<?= $product['id'] ?>]" class="form-select">
                                        <option value="none" <?= $product['featured'] == 'none' ? 'selected' : '' ?>>None</option>
                                        <option value="new" <?= $product['featured'] == 'new' ? 'selected' : '' ?>> New Arrival</option>
                                        <option value="trending" <?= $product['featured'] == 'trending' ? 'selected' : '' ?>> Trending</option>
                                        <option value="best_seller" <?= $product['featured'] == 'best_seller' ? 'selected' : '' ?>> Best Seller</option>
                                    </select>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 15px; margin-top: 20px;">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i> Update Storefront
                    </button>
                </div>
            </form>
        </div>

        <footer>
            <p>&copy; 2026 CREATRIX. All rights reserved. | For educational purposes only, and no copyright infringement is intended.</p>
            <p>CREATRIX Group - Promoting Sustainable Living Through Coconut Coir Innovation</p>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Mobile menu toggle
        const menuToggle = document.getElementById('menuToggle');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        
        function toggleMenu() {
            sidebar.classList.toggle('open');
            overlay.classList.toggle('active');
            
            const icon = menuToggle.querySelector('i');
            if (sidebar.classList.contains('open')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        }
        
        function closeMenu() {
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
            const icon = menuToggle.querySelector('i');
            icon.classList.remove('fa-times');
            icon.classList.add('fa-bars');
        }
        
        menuToggle.addEventListener('click', toggleMenu);
        overlay.addEventListener('click', closeMenu);
        
        // Close menu on window resize if screen becomes desktop
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                closeMenu();
            }
        });
        
        // Close menu when clicking a nav link (mobile only)
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    closeMenu();
                }
            });
        });
    </script>
</body>
</html>