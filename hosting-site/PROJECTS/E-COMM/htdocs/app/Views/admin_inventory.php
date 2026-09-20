<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Inventory | CREATRIX Admin</title>
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
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg);
            margin: 0;
            overflow-x: hidden;
        }

        /* ===== SIDEBAR (EXACT MOBILE PATTERN FROM DASHBOARD) ===== */
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
        
        /* Header area */
        .header {
            background: var(--card-bg);
            padding: 20px 30px;
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
        
        .btn-add {
            background: var(--accent);
            color: var(--white);
            padding: 10px 24px;
            border-radius: 12px;
            text-decoration: none;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
            font-size: 0.9rem;
            white-space: nowrap;
        }
        
        .btn-add:hover {
            background: var(--accent-hover);
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }
        
        /* Filter card */
        .filter-section {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: var(--shadow-sm);
        }
        
        .filter-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--accent-light);
        }
        
        .filter-section .form-label {
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--text);
            margin-bottom: 6px;
        }
        
        .filter-section .form-control,
        .filter-section .form-select {
            border: 2px solid var(--accent-light);
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 0.9rem;
            transition: all 0.3s;
        }
        
        .filter-section .form-control:focus,
        .filter-section .form-select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(42, 92, 55, 0.1);
            outline: none;
        }
        
        .filter-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 30px;
            flex-wrap: wrap;
        }
        
        .btn-filter {
            padding: 10px 24px;
            border-radius: 10px;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            border: none;
            text-decoration: none;
        }
        
        .btn-filter-primary {
            background: var(--accent);
            color: white;
        }
        
        .btn-filter-primary:hover {
            background: var(--accent-hover);
            transform: translateY(-2px);
        }
        
        .btn-filter-secondary {
            background: var(--text-light);
            color: white;
        }
        
        .btn-filter-secondary:hover {
            background: #4a5a4f;
            transform: translateY(-2px);
        }
        
        /* Table responsive */
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
            min-width: 680px;
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
        
        .badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
        }
        
        .badge-success { background: var(--success); color: white; }
        .badge-warning { background: var(--warning); color: white; }
        .badge-danger { background: var(--danger); color: white; }
        .badge-info { background: #3498DB; color: white; }
        
        .product-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid var(--accent-light);
        }
        
        /* ACTION BUTTONS GROUP - FIXED FOR SMALL SCREENS */
        .action-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
        }
        
        .btn-action {
            padding: 6px 14px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            transition: all 0.3s;
            font-size: 0.75rem;
            font-weight: 500;
            white-space: nowrap;
        }
        
        .btn-action i {
            font-size: 0.8rem;
        }
        
        .btn-action:hover {
            transform: translateY(-2px);
        }
        
        .btn-edit {
            background: var(--primary);
            color: white;
        }
        
        .btn-edit:hover {
            background: var(--primary-light);
        }
        
        .btn-delete {
            background: var(--danger);
            color: white;
        }
        
        .btn-delete:hover {
            background: #c53030;
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
            background: #FEE2E2;
            color: var(--danger);
            border: 1px solid #FECACA;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 25px;
            flex-wrap: wrap;
        }
        
        .pagination a,
        .pagination span {
            padding: 8px 14px;
            border: 1px solid var(--accent-light);
            border-radius: 8px;
            color: var(--text);
            text-decoration: none;
            transition: all 0.3s;
            font-size: 0.9rem;
        }
        
        .pagination a:hover {
            background: var(--accent-light);
            transform: translateY(-2px);
        }
        
        .pagination .active {
            background: var(--accent);
            color: white;
            border-color: var(--accent);
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
            
            .header {
                flex-direction: column;
                text-align: center;
                padding: 20px;
            }
            
            .header h1 {
                font-size: 1.5rem;
                justify-content: center;
            }
            
            .btn-add {
                width: 100%;
                justify-content: center;
                white-space: normal;
                padding: 10px 16px;
            }
            
            .filter-actions {
                justify-content: center;
            }
            
            .filter-actions .btn-filter {
                flex: 1;
                justify-content: center;
            }
            
            /* Action buttons stack gracefully on mobile */
            .action-buttons {
                flex-direction: column;
                align-items: flex-start;
                gap: 6px;
            }
            
            .btn-action {
                width: 100%;
                justify-content: center;
                padding: 8px 12px;
            }
            
            .table td {
                font-size: 0.85rem;
            }
        }
        
        @media (max-width: 480px) {
            .stat-card {
                flex-direction: column;
                text-align: center;
            }
            
            .btn-action {
                font-size: 0.7rem;
                padding: 6px 10px;
            }
            
            .pagination a, .pagination span {
                padding: 6px 10px;
                font-size: 0.8rem;
            }
            
            .filter-section .row {
                row-gap: 12px;
            }
            
            .btn-add i, .btn-action i {
                margin-right: 2px;
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
            <a class="nav-link active" href="<?= base_url('/admin/inventory') ?>"><i class="fas fa-box"></i> Inventory</a>
            <a class="nav-link" href="<?= base_url('/admin/reports') ?>"><i class="fas fa-chart-line"></i> Sales Reports</a>
            <a class="nav-link" href="<?= base_url('/admin/inventory-report') ?>"><i class="fas fa-file-alt"></i> Inventory Report</a>
            <a class="nav-link" href="<?= base_url('/admin/orders') ?>"><i class="fas fa-shopping-cart"></i> Orders</a>
            <hr style="border-color: rgba(255,255,255,0.2);">
            <a class="nav-link" href="<?= base_url('/logout') ?>"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </div>

    <div class="main-content">
        <div class="header">
            <h1><i class="fas fa-box"></i> Inventory Management</h1>
            <a href="<?= base_url('/admin/add-product') ?>" class="btn-add"><i class="fas fa-plus"></i> Add New Product</a>
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

        <!-- Filter Form -->
        <div class="filter-section">
            <div class="filter-title">
                <i class="fas fa-filter"></i> Filter Products
            </div>
            <form method="get" action="<?= base_url('/admin/inventory') ?>">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label"><i class="fas fa-search"></i> Search</label>
                        <input type="text" name="search" class="form-control" placeholder="Product name..." value="<?= esc($search ?? '') ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label"><i class="fas fa-tag"></i> Category</label>
                        <select name="category" class="form-select">
                            <option value="">All Categories</option>
                            <option value="Gardening" <?= ($category ?? '') == 'Gardening' ? 'selected' : '' ?>>Gardening</option>
                            <option value="Home & Living" <?= ($category ?? '') == 'Home & Living' ? 'selected' : '' ?>>Home & Living</option>
                            <option value="Agriculture" <?= ($category ?? '') == 'Agriculture' ? 'selected' : '' ?>>Agriculture</option>
                            <option value="Packaging" <?= ($category ?? '') == 'Packaging' ? 'selected' : '' ?>>Packaging</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label"><i class="fas fa-chart-line"></i> Stock Status</label>
                        <select name="stock" class="form-select">
                            <option value="">All Stock</option>
                            <option value="in" <?= ($stock ?? '') == 'in' ? 'selected' : '' ?>>In Stock (>10)</option>
                            <option value="low" <?= ($stock ?? '') == 'low' ? 'selected' : '' ?>>Low Stock (1-10)</option>
                            <option value="out" <?= ($stock ?? '') == 'out' ? 'selected' : '' ?>>Out of Stock</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label"><i class="fas fa-sort"></i> Sort By</label>
                        <select name="sort" class="form-select">
                            <option value="newest" <?= ($sort ?? '') == 'newest' ? 'selected' : '' ?>>Newest First</option>
                            <option value="price_asc" <?= ($sort ?? '') == 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
                            <option value="price_desc" <?= ($sort ?? '') == 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
                            <option value="name_asc" <?= ($sort ?? '') == 'name_asc' ? 'selected' : '' ?>>Name A-Z</option>
                            <option value="stock_asc" <?= ($sort ?? '') == 'stock_asc' ? 'selected' : '' ?>>Stock: Low to High</option>
                        </select>
                    </div>
                </div>
                <div class="filter-actions">
                    <button type="submit" class="btn-filter btn-filter-primary">
                        <i class="fas fa-search"></i> Apply Filters
                    </button>
                    <a href="<?= base_url('/admin/inventory') ?>" class="btn-filter btn-filter-secondary">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Products Table -->
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Featured</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="fas fa-box-open" style="font-size: 3rem; color: var(--text-light);"></i>
                                <h5 class="mt-3">No products found</h5>
                                <?php if (!empty($search) || !empty($category) || !empty($stock)): ?>
                                    <p class="text-muted">Try adjusting your filters</p>
                                <?php else: ?>
                                    <a href="<?= base_url('/admin/add-product') ?>" class="btn-add mt-3" style="display: inline-flex;">Add New Product</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($products as $product): ?>
                        <tr>
                            <td>#<?= $product['id'] ?></td>
                            <td>
                                <?php if (!empty($product['image'])): ?>
                                    <img src="<?= base_url('uploads/products/' . $product['image']) ?>" class="product-image" alt="<?= esc($product['product_name']) ?>">
                                <?php else: ?>
                                    <img src="https://via.placeholder.com/50x50/1F4529/ffffff?text=No+Image" class="product-image" alt="No image">
                                <?php endif; ?>
                            </td>
                            <td><strong><?= esc($product['product_name']) ?></strong></td>
                            <td><span class="badge badge-info"><?= esc($product['category']) ?></span></td>
                            <td class="fw-bold text-success">₱<?= number_format($product['price'], 2) ?></td>
                            <td>
                                <?php if ($product['stock'] <= 0): ?>
                                    <span class="badge badge-danger">Out of Stock</span>
                                <?php elseif ($product['stock'] < 10): ?>
                                    <span class="badge badge-warning">Only <?= $product['stock'] ?> left</span>
                                <?php else: ?>
                                    <span class="badge badge-success"><?= $product['stock'] ?> in stock</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($product['featured'] == 'new'): ?>
                                    <span class="badge" style="background: var(--accent); color: white;">New</span>
                                <?php elseif ($product['featured'] == 'best_seller'): ?>
                                    <span class="badge" style="background: var(--primary-light); color: white;">Best Seller</span>
                                <?php elseif ($product['featured'] == 'trending'): ?>
                                    <span class="badge" style="background: #E53E3E; color: white;">Trending</span>
                                <?php else: ?>
                                    <span class="badge" style="background: var(--text-light); color: white;">None</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="<?= base_url('/admin/edit-product/' . $product['id']) ?>" class="btn-action btn-edit">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="<?= base_url('/admin/delete-product/' . $product['id']) ?>" class="btn-action btn-delete" onclick="return confirm('Delete this product? This action cannot be undone.')">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if (isset($pager) && $pager['last'] > 1): ?>
        <div class="pagination">
            <?php if ($pager['current'] > 1): ?>
                <a href="?page=<?= $pager['current']-1 ?>&<?= http_build_query(array_filter(['search' => $search, 'category' => $category, 'stock' => $stock, 'sort' => $sort])) ?>">
                    <i class="fas fa-chevron-left"></i> Previous
                </a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $pager['last']; $i++): ?>
                <?php if ($i == $pager['current']): ?>
                    <span class="active"><?= $i ?></span>
                <?php else: ?>
                    <a href="?page=<?= $i ?>&<?= http_build_query(array_filter(['search' => $search, 'category' => $category, 'stock' => $stock, 'sort' => $sort])) ?>"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>
            <?php if ($pager['current'] < $pager['last']): ?>
                <a href="?page=<?= $pager['current']+1 ?>&<?= http_build_query(array_filter(['search' => $search, 'category' => $category, 'stock' => $stock, 'sort' => $sort])) ?>">
                    Next <i class="fas fa-chevron-right"></i>
                </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

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
        
        // Extra: ensure sidebar starts closed on mobile if screen < 768px
        if (window.innerWidth <= 768) {
            sidebar.classList.remove('open');
            sidebar.classList.add('closed');
            // ensure proper class for closed state styling
            if (!sidebar.classList.contains('closed')) sidebar.classList.add('closed');
            overlay.classList.remove('active');
        } else {
            sidebar.classList.remove('closed');
            sidebar.classList.remove('open');
        }
        
        // watch for orientation/resize to keep consistent
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
                // ensure closed state when mobile but not open by accident
                if (!sidebar.classList.contains('open')) {
                    sidebar.classList.add('closed');
                } else {
                    sidebar.classList.remove('closed');
                }
            }
        });
    </script>
</body>
</html>