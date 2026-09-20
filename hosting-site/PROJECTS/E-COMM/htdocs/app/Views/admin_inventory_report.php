<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Inventory Report | CREATRIX Admin</title>
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
            --success: #2A5C37;
            --info: #3498DB;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg);
            margin: 0;
            overflow-x: hidden;
        }
        
        /* ===== SIDEBAR WITH HAMBURGER (SAME PATTERN AS DASHBOARD) ===== */
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
        
        /* Sidebar closed state for mobile */
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
            align-items: center;
            justify-content: center;
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
        
        .page-header {
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
        
        .page-header h1 {
            font-size: 1.8rem;
            color: var(--primary);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .page-header h1 i {
            color: var(--accent);
            font-size: 1.6rem;
        }
        
        .export-btn {
            background: var(--accent);
            color: var(--white);
            padding: 10px 20px;
            border-radius: 10px;
            text-decoration: none;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
            font-size: 0.9rem;
            border: none;
            cursor: pointer;
        }
        
        .export-btn:hover {
            background: var(--accent-hover);
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: var(--card-bg);
            padding: 25px;
            border-radius: 20px;
            box-shadow: var(--shadow-sm);
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            background: var(--accent-light);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        
        .stat-icon i {
            font-size: 1.8rem;
            color: var(--accent);
        }
        
        .stat-content {
            flex: 1;
            min-width: 0;
        }
        
        .stat-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 4px;
            line-height: 1.2;
            word-break: break-word;
            overflow-wrap: break-word;
        }
        
        .stat-number.total-value {
            font-size: 1.1rem;
            letter-spacing: -0.3px;
            word-break: break-all;
        }
        
        .stat-label {
            color: var(--text-light);
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .stat-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            margin-top: 5px;
        }
        
        .stat-badge.warning {
            background: var(--warning);
            color: white;
        }
        
        .stat-badge.danger {
            background: var(--danger);
            color: white;
        }
        
        .table-responsive {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 20px;
            box-shadow: var(--shadow-sm);
            overflow-x: auto;
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
        
        .stock-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .stock-badge.high {
            background: var(--accent-light);
            color: var(--accent);
        }
        
        .stock-badge.medium {
            background: #FFF3E0;
            color: var(--warning);
        }
        
        .stock-badge.low {
            background: #FFEBEE;
            color: var(--danger);
        }
        
        .stock-value {
            font-weight: 600;
            color: var(--primary);
        }
        
        .category-badge {
            background: var(--accent-light);
            color: var(--accent);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }
        
        .empty-state i {
            font-size: 4rem;
            color: var(--accent-light);
            margin-bottom: 16px;
        }
        
        .empty-state h4 {
            color: var(--text);
            font-size: 1.2rem;
            margin-bottom: 8px;
        }
        
        .empty-state p {
            color: var(--text-light);
            font-size: 0.9rem;
        }
        
        footer {
            margin-top: 30px;
            text-align: center;
            color: var(--text-light);
            font-size: 0.85rem;
            padding: 20px;
        }
        
        /* RESPONSIVE DESIGN (SAME AS DASHBOARD) */
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
            
            .page-header {
                flex-direction: column;
                text-align: center;
            }
            
            .page-header h1 {
                font-size: 1.5rem;
                justify-content: center;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .stat-card {
                flex-direction: column;
                text-align: center;
            }
            
            .stat-icon {
                margin: 0 auto;
            }
            
            .export-btn {
                width: 100%;
                justify-content: center;
            }
        }
        
        @media (max-width: 480px) {
            .stat-number {
                font-size: 1.2rem;
            }
            
            .stat-number.total-value {
                font-size: 0.95rem;
            }
            
            .table th, .table td {
                padding: 10px 8px;
                font-size: 0.8rem;
            }
            
            .stock-badge, .category-badge {
                padding: 3px 8px;
                font-size: 0.7rem;
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
            <a class="nav-link" href="<?= base_url('/admin/storefront') ?>"><i class="fas fa-store"></i> Storefront</a>
            <a class="nav-link" href="<?= base_url('/admin/inventory') ?>"><i class="fas fa-box"></i> Inventory</a>
            <a class="nav-link" href="<?= base_url('/admin/reports') ?>"><i class="fas fa-chart-line"></i> Sales Reports</a>
            <a class="nav-link active" href="<?= base_url('/admin/inventory-report') ?>"><i class="fas fa-file-alt"></i> Inventory Report</a>
            <a class="nav-link" href="<?= base_url('/admin/orders') ?>"><i class="fas fa-shopping-cart"></i> Orders</a>
            <hr style="border-color: rgba(255,255,255,0.2);">
            <a class="nav-link" href="<?= base_url('/logout') ?>"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </div>

    <div class="main-content">
        <div class="page-header">
            <h1>
                <i class="fas fa-chart-line"></i>
                Inventory Report
            </h1>
            <button class="export-btn" onclick="window.print()">
                <i class="fas fa-print"></i> Export / Print Report
            </button>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-boxes"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number"><?= number_format($totalProducts ?? 0) ?></div>
                    <div class="stat-label">Total Products</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-cubes"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number"><?= number_format($totalItems ?? 0) ?></div>
                    <div class="stat-label">Total Items in Stock</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-chart-simple"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number total-value">₱<?= number_format($totalValue ?? 0, 2) ?></div>
                    <div class="stat-label">Total Stock Value</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number"><?= $lowStockCount ?? 0 ?></div>
                    <div class="stat-label">Low Stock Items</div>
                    <?php if (($lowStockCount ?? 0) > 0): ?>
                        <div class="stat-badge warning">Requires Attention</div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number"><?= $outOfStockCount ?? 0 ?></div>
                    <div class="stat-label">Out of Stock</div>
                    <?php if (($outOfStockCount ?? 0) > 0): ?>
                        <div class="stat-badge danger">Critical</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Products Table -->
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Stock Value</th>
                    </thead>
                <tbody>
                    <?php if (!empty($products)): ?>
                        <?php foreach ($products as $product): ?>
                        <tr>
                            <td><strong>#<?= $product['id'] ?></strong></td>
                            <td><?= esc($product['product_name']) ?></td>
                            <td><span class="category-badge"><?= esc($product['category']) ?></span></td>
                            <td class="fw-bold">₱<?= number_format($product['price'], 2) ?></td>
                            <td>
                                <?php if ($product['stock'] <= 0): ?>
                                    <span class="stock-badge low">
                                        <i class="fas fa-times-circle"></i> 0
                                    </span>
                                <?php elseif ($product['stock'] < 10): ?>
                                    <span class="stock-badge medium">
                                        <i class="fas fa-exclamation-circle"></i> <?= $product['stock'] ?>
                                    </span>
                                <?php else: ?>
                                    <span class="stock-badge high">
                                        <i class="fas fa-check-circle"></i> <?= $product['stock'] ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="stock-value">₱<?= number_format($product['stock_value'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <i class="fas fa-box-open"></i>
                                    <h4>No Products Found</h4>
                                    <p>There are no products in the inventory to display.</p>
                                    <a href="<?= base_url('/admin/add-product') ?>" class="export-btn" style="display: inline-flex; margin-top: 15px;">
                                        <i class="fas fa-plus"></i> Add New Product
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <footer>
            <p>&copy; 2026 CREATRIX. For educational purposes only, and no copyright infringement is intended.</p>
            <p>CREATRIX Group - Promoting Sustainable Living Through Coconut Coir Innovation</p>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Mobile menu toggle logic (identical to working dashboard code)
        const menuToggle = document.getElementById('menuToggle');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        
        function toggleMenu() {
            sidebar.classList.toggle('open');
            overlay.classList.toggle('active');
            
            // Change icon
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
            if (icon) {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        }
        
        if (menuToggle) {
            menuToggle.addEventListener('click', toggleMenu);
        }
        if (overlay) {
            overlay.addEventListener('click', closeMenu);
        }
        
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
        
        // Initialize sidebar state based on screen width
        function initSidebarState() {
            if (window.innerWidth <= 768) {
                sidebar.classList.add('closed');
                sidebar.classList.remove('open');
                overlay.classList.remove('active');
                const icon = menuToggle.querySelector('i');
                if (icon) {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            } else {
                sidebar.classList.remove('closed', 'open');
                overlay.classList.remove('active');
            }
        }
        
        initSidebarState();
        
        // Watch for orientation/resize to keep consistent
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                sidebar.classList.remove('open', 'closed');
                overlay.classList.remove('active');
                const icon = menuToggle.querySelector('i');
                if (icon) {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            } else {
                // Ensure closed state when mobile but not open by accident
                if (!sidebar.classList.contains('open')) {
                    sidebar.classList.add('closed');
                }
            }
        });
    </script>
</body>
</html>