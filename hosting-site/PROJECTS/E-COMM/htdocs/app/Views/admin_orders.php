<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders | CREATRIX Admin</title>
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
        
        .page-header {
            background: var(--card-bg);
            padding: 25px 30px;
            border-radius: 20px;
            box-shadow: var(--shadow-sm);
            margin-bottom: 25px;
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
        
        .order-stats {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        
        .stat-card {
            background: var(--accent-light);
            padding: 10px 18px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-sm);
        }
        
        .stat-card i {
            font-size: 1.2rem;
            color: var(--accent);
        }
        
        .stat-card span {
            font-weight: 700;
            color: var(--primary);
            font-size: 1.1rem;
        }
        
        .stat-card .stat-label {
            font-size: 0.8rem;
            color: var(--text-light);
            font-weight: 400;
        }
        
        .filter-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 25px;
            background: var(--card-bg);
            padding: 20px;
            border-radius: 20px;
            box-shadow: var(--shadow-sm);
        }
        
        .filter-btn {
            padding: 8px 20px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.85rem;
            transition: all 0.3s;
            background: var(--accent-light);
            color: var(--text);
            border: 1px solid transparent;
        }
        
        .filter-btn:hover {
            background: var(--accent);
            color: var(--white);
            transform: translateY(-2px);
        }
        
        .filter-btn.active {
            background: var(--accent);
            color: var(--white);
            box-shadow: var(--shadow-sm);
        }
        
        .filter-btn.all {
            background: var(--primary);
            color: white;
        }
        
        .filter-btn.all:hover {
            background: var(--accent-hover);
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
            min-width: 800px;
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
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
        }
        
        .badge-pending { background: #FFF3E0; color: #B76E0E; }
        .badge-confirmed { background: #E3F2FD; color: #1565C0; }
        .badge-processing { background: #E3F2FD; color: #1565C0; }
        .badge-ready_for_pickup { background: #E8F5E9; color: #2E7D32; }
        .badge-to_ship { background: #E8F5E9; color: #2E7D32; }
        .badge-shipped { background: #E8F5E9; color: #2E7D32; }
        .badge-completed { background: var(--accent-light); color: var(--accent); }
        .badge-cancelled { background: #FFEBEE; color: #C62828; }
        
        .btn-action {
            padding: 6px 12px;
            border-radius: 8px;
            margin: 0 2px;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .btn-action:hover { 
            transform: translateY(-2px);
        }
        
        .btn-view { 
            background: var(--accent); 
            color: white;
        }
        
        .btn-view:hover {
            background: var(--accent-hover);
        }
        
        .pickup-schedule {
            font-size: 0.75rem;
            color: var(--text-light);
            line-height: 1.4;
        }
        
        .alert {
            padding: 16px 20px;
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
        
        .alert-danger {
            background: #FFEBEE;
            color: var(--danger);
            border: 1px solid #FFCDD2;
        }
        
        footer {
            margin-top: 30px;
            text-align: center;
            color: var(--text-light);
            font-size: 0.85rem;
            padding: 20px;
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
            
            .page-header {
                flex-direction: column;
                text-align: center;
            }
            
            .order-stats {
                justify-content: center;
            }
            
            .filter-buttons {
                justify-content: center;
                padding: 15px;
            }
            
            .filter-btn {
                padding: 6px 14px;
                font-size: 0.75rem;
            }
            
            .stat-card {
                padding: 8px 12px;
            }
            
            .stat-card i {
                font-size: 1rem;
            }
            
            .stat-card span {
                font-size: 1rem;
            }
            
            .table th, .table td {
                font-size: 0.75rem;
                padding: 8px;
            }
            
            .btn-action {
                padding: 4px 8px;
                font-size: 0.7rem;
            }
        }
        
        @media (max-width: 480px) {
            .stat-card {
                flex-direction: column;
                text-align: center;
                gap: 5px;
                padding: 10px;
            }
            
            .filter-btn {
                padding: 5px 12px;
                font-size: 0.7rem;
            }
            
            .page-header h1 {
                font-size: 1.4rem;
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
            <a class="nav-link" href="<?= base_url('/admin/inventory-report') ?>"><i class="fas fa-file-alt"></i> Inventory Report</a>
            <a class="nav-link active" href="<?= base_url('/admin/orders') ?>"><i class="fas fa-shopping-cart"></i> Orders</a>
            <hr style="border-color: rgba(255,255,255,0.2);">
            <a class="nav-link" href="<?= base_url('/logout') ?>"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </div>

    <div class="main-content">
        <div class="page-header">
            <h1>
                <i class="fas fa-shopping-cart"></i>
                Order Management
            </h1>
            <div class="order-stats">
                <div class="stat-card">
                    <i class="fas fa-chart-line"></i>
                    <div>
                        <span><?= count($orders ?? []) ?></span>
                        <div class="stat-label">Total Orders</div>
                    </div>
                </div>
                <?php 
                $pendingCount = 0;
                $completedCount = 0;
                $cancelledCount = 0;
                if (!empty($orders)) {
                    foreach ($orders as $order) {
                        if ($order['status'] == 'pending') $pendingCount++;
                        elseif ($order['status'] == 'completed') $completedCount++;
                        elseif ($order['status'] == 'cancelled') $cancelledCount++;
                    }
                }
                ?>
                <div class="stat-card">
                    <i class="fas fa-clock"></i>
                    <div>
                        <span><?= $pendingCount ?></span>
                        <div class="stat-label">Pending</div>
                    </div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-check-circle"></i>
                    <div>
                        <span><?= $completedCount ?></span>
                        <div class="stat-label">Completed</div>
                    </div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-times-circle"></i>
                    <div>
                        <span><?= $cancelledCount ?></span>
                        <div class="stat-label">Cancelled</div>
                    </div>
                </div>
            </div>
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

        <div class="filter-buttons">
            <a href="<?= base_url('/admin/orders') ?>" class="filter-btn all <?= !isset($status) ? 'active' : '' ?>">All Orders</a>
            <a href="<?= base_url('/admin/orders?status=pending') ?>" class="filter-btn pending <?= isset($status) && $status == 'pending' ? 'active' : '' ?>">Pending</a>
            <a href="<?= base_url('/admin/orders?status=confirmed') ?>" class="filter-btn confirmed <?= isset($status) && $status == 'confirmed' ? 'active' : '' ?>">Confirmed</a>
            <a href="<?= base_url('/admin/orders?status=processing') ?>" class="filter-btn processing <?= isset($status) && $status == 'processing' ? 'active' : '' ?>">Processing</a>
            <a href="<?= base_url('/admin/orders?status=ready_for_pickup') ?>" class="filter-btn ready_for_pickup <?= isset($status) && $status == 'ready_for_pickup' ? 'active' : '' ?>">Ready for Pickup</a>
            <a href="<?= base_url('/admin/orders?status=to_ship') ?>" class="filter-btn shipped <?= isset($status) && $status == 'to_ship' ? 'active' : '' ?>">To Ship</a>
            <a href="<?= base_url('/admin/orders?status=shipped') ?>" class="filter-btn shipped <?= isset($status) && $status == 'shipped' ? 'active' : '' ?>">Shipped</a>
            <a href="<?= base_url('/admin/orders?status=completed') ?>" class="filter-btn completed <?= isset($status) && $status == 'completed' ? 'active' : '' ?>">Completed</a>
            <a href="<?= base_url('/admin/orders?status=cancelled') ?>" class="filter-btn cancelled <?= isset($status) && $status == 'cancelled' ? 'active' : '' ?>">Cancelled</a>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Payment</th>
                        <th>Delivery</th>
                        <th>Pickup Schedule</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </thead>
                <tbody>
                    <?php if (!empty($orders)): ?>
                        <?php foreach ($orders as $order): ?>
                         <tr>
                            <td><strong>#<?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?></strong></td>
                            <td>User #<?= $order['user_id'] ?></td>
                            <td class="fw-bold text-success">₱<?= number_format($order['total_price'], 2) ?></td>
                            <td><?= ucfirst($order['payment_method'] ?? 'N/A') ?></td>
                            <td><?= ucfirst($order['delivery_method'] ?? 'N/A') ?></td>
                            <td>
                                <?php if ($order['delivery_method'] === 'pickup'): ?>
                                    <?php if (!empty($order['pickup_date'])): ?>
                                        <div class="pickup-schedule">
                                            <i class="fas fa-calendar-alt"></i> <?= date('M d, Y', strtotime($order['pickup_date'])) ?><br>
                                            <i class="fas fa-clock"></i> <?= esc($order['pickup_time_slot'] ?? '') ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted">N/A</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge badge-<?= $order['status'] ?>">
                                    <?= ucfirst(str_replace('_', ' ', $order['status'])) ?>
                                </span>
                            </td>
                            <td><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
                            <td>
                                <a href="<?= base_url('/admin/order-details/' . $order['id']) ?>" class="btn-action btn-view">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9">
                                <div class="empty-state">
                                    <i class="fas fa-shopping-cart"></i>
                                    <h4>No Orders Found</h4>
                                    <p>There are no orders to display at the moment.</p>
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