<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | CREATRIX</title>
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
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
            background: var(--accent-light);
            padding: 8px 20px;
            border-radius: 40px;
        }
        
        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--accent);
            background: var(--accent-light);
        }
        
        .user-name {
            font-weight: 500;
            color: var(--text);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
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
        }
        
        .stat-number {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 4px;
            line-height: 1.2;
        }
        
        .stat-label {
            color: var(--text-light);
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .recent-section {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 25px;
            box-shadow: var(--shadow-sm);
            margin-bottom: 30px;
        }
        
        .section-title {
            font-size: 1.2rem;
            color: var(--primary);
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid var(--accent-light);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .section-title i {
            color: var(--accent);
        }
        
        .table-responsive {
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
            padding: 12px;
            text-align: left;
            font-size: 0.85rem;
        }
        
        .table td {
            padding: 12px;
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
        
        .badge-success { background: var(--success); color: white; }
        .badge-warning { background: var(--warning); color: white; }
        .badge-danger { background: var(--danger); color: white; }
        .badge-info { background: var(--info); color: white; }
        
        .btn-action {
            padding: 6px 12px;
            border-radius: 8px;
            margin: 0 2px;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .btn-action:hover { transform: translateY(-2px); }
        .btn-edit { background: var(--primary); color: white; }
        .btn-edit:hover { background: var(--primary-light); }
        .btn-delete { background: var(--danger); color: white; }
        .btn-delete:hover { background: #c53030; }
        .btn-view { background: var(--accent); color: white; }
        .btn-view:hover { background: var(--accent-hover); }
        
        .search-box {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        
        .search-box form {
            flex: 1;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .search-box input {
            flex: 1;
            min-width: 200px;
            padding: 12px 15px;
            border: 2px solid var(--accent-light);
            border-radius: 10px;
            font-size: 0.9rem;
            transition: all 0.3s;
        }
        
        .search-box input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(42, 92, 55, 0.1);
        }
        
        .search-box button {
            padding: 10px 24px;
            background: var(--accent);
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
        }
        
        .search-box button:hover {
            background: var(--accent-hover);
            transform: translateY(-2px);
        }
        
        .btn-add {
            background: var(--accent);
            color: white;
            padding: 8px 20px;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
            font-size: 0.85rem;
            white-space: nowrap;
        }
        
        .btn-add:hover {
            background: var(--accent-hover);
            transform: translateY(-2px);
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        
        .pagination a, .pagination span {
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
        
        .alert {
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .alert-success { background: var(--accent-light); color: var(--accent); border: 1px solid var(--accent); }
        .alert-danger { background: #FFEBEE; color: var(--danger); border: 1px solid #FFCDD2; }
        
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
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .page-header {
                flex-direction: column;
                text-align: center;
            }
            
            .user-info {
                justify-content: center;
                width: 100%;
            }
            
            .search-box form {
                flex-direction: column;
                width: 100%;
            }
            
            .search-box input {
                width: 100%;
            }
            
            .search-box button {
                width: 100%;
                justify-content: center;
            }
            
            .btn-add {
                width: 100%;
                justify-content: center;
            }
            
            .btn-action {
                padding: 6px 10px;
                font-size: 0.7rem;
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
            
            .stat-icon {
                margin: 0 auto;
            }
            
            .btn-action {
                display: inline-flex;
                margin-bottom: 5px;
            }
            
            .table td {
                white-space: nowrap;
            }
            
            .pagination a, .pagination span {
                padding: 6px 10px;
                font-size: 0.8rem;
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
            <a class="nav-link active" href="<?= base_url('/admin/dashboard') ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a class="nav-link" href="<?= base_url('/admin/storefront') ?>"><i class="fas fa-store"></i> Storefront</a>
            <a class="nav-link" href="<?= base_url('/admin/inventory') ?>"><i class="fas fa-box"></i> Inventory</a>
            <a class="nav-link" href="<?= base_url('/admin/reports') ?>"><i class="fas fa-chart-line"></i> Sales Reports</a>
            <a class="nav-link" href="<?= base_url('/admin/inventory-report') ?>"><i class="fas fa-file-alt"></i> Inventory Report</a>
            <a class="nav-link" href="<?= base_url('/admin/orders') ?>"><i class="fas fa-shopping-cart"></i> Orders</a>
            <hr style="border-color: rgba(255,255,255,0.2);">
            <a class="nav-link" href="<?= base_url('/logout') ?>"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </div>

    <div class="main-content">
        <div class="page-header">
            <h1>
                <i class="fas fa-chart-line"></i>
                Dashboard
            </h1>
            <div class="user-info">
                <span class="user-name">Welcome, <?= session()->get('fullname') ?></span>
                <?php
                $profilePicture = session()->get('profile_picture');
                if ($profilePicture) {
                    $avatarUrl = base_url('uploads/profiles/' . $profilePicture);
                } else {
                    $avatarUrl = 'https://via.placeholder.com/45x45/2A5C37/ffffff?text=' . substr(session()->get('fullname'), 0, 1);
                }
                ?>
                <img src="<?= $avatarUrl ?>" alt="Profile" class="user-avatar" 
                     onerror="this.src='https://via.placeholder.com/45x45/2A5C37/ffffff?text=U'">
            </div>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-content">
                    <div class="stat-number"><?= number_format($totalCustomers ?? 0) ?></div>
                    <div class="stat-label">Total Customers</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-box"></i></div>
                <div class="stat-content">
                    <div class="stat-number"><?= number_format($totalProducts ?? 0) ?></div>
                    <div class="stat-label">Total Products</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-shopping-cart"></i></div>
                <div class="stat-content">
                    <div class="stat-number"><?= number_format($totalOrders ?? 0) ?></div>
                    <div class="stat-label">Total Orders</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-chart-simple"></i></div>
                <div class="stat-content">
                    <div class="stat-number">₱<?= number_format($todaySales ?? 0, 2) ?></div>
                    <div class="stat-label">Today's Sales</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-calendar"></i></div>
                <div class="stat-content">
                    <div class="stat-number">₱<?= number_format($monthlySales ?? 0, 2) ?></div>
                    <div class="stat-label">Monthly Sales</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-clock"></i></div>
                <div class="stat-content">
                    <div class="stat-number"><?= number_format($pendingOrders ?? 0) ?></div>
                    <div class="stat-label">Pending Orders</div>
                </div>
            </div>
        </div>

        <div class="recent-section">
            <h3 class="section-title">
                <i class="fas fa-shopping-cart"></i>
                Recent Orders
            </h3>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                         <th>Order ID</th><th>Customer</th><th>Total</th><th>Payment</th><th>Delivery</th><th>Status</th><th>Date</th><th>Action</th> </>
                    </thead>
                    <tbody>
                        <?php if (!empty($recentOrders)): ?>
                            <?php foreach ($recentOrders as $order): ?>
                                <tr>
                                    <td><strong>#<?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?></strong></td>
                                    <td>User #<?= $order['user_id'] ?></td>
                                    <td class="fw-bold text-success">₱<?= number_format($order['total_price'], 2) ?></td>
                                    <td><?= ucfirst($order['payment_method'] ?? 'N/A') ?></td>
                                    <td><?= ucfirst($order['delivery_method'] ?? 'N/A') ?></td>
                                    <td><span class="badge badge-<?= $order['status'] == 'completed' ? 'success' : ($order['status'] == 'pending' ? 'warning' : ($order['status'] == 'cancelled' ? 'danger' : 'info')) ?>"><?= ucfirst($order['status'] ?? 'pending') ?></span></td>
                                    <td><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
                                    <td><a href="<?= base_url('/admin/order-details/' . $order['id']) ?>" class="btn-action btn-view"><i class="fas fa-eye"></i> View</a></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="8" class="text-center py-5">No recent orders found</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="recent-section">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-3">
                <h3 class="section-title mb-0">
                    <i class="fas fa-users"></i>
                    User Management
                </h3>
                <a href="<?= base_url('/admin/users/create') ?>" class="btn-add"><i class="fas fa-plus"></i> Add New User</a>
            </div>
            <div class="search-box">
                <form action="<?= base_url('/admin/dashboard') ?>" method="get" class="d-flex gap-2 w-100">
                    <input type="text" name="search" placeholder="Search users by name, email, or mobile..." value="<?= isset($search) ? $search : '' ?>">
                    <button type="submit"><i class="fas fa-search"></i> Search</button>
                    <?php if (isset($search)): ?><a href="<?= base_url('/admin/dashboard') ?>" class="btn-add" style="background: var(--text-light);">Clear</a><?php endif; ?>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead> 
                        <th>ID</th><th>Full Name</th><th>Email</th><th>Mobile</th><th>Role</th><th>Joined</th><th>Actions</th>
                    </thead>
                    <tbody>
                        <?php if (!empty($users)): ?>
                            <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= $user['id'] ?></td>
                                <td><strong><?= esc($user['fullname']) ?></strong></td>
                                <td><?= esc($user['email']) ?></td>
                                <td><?= esc($user['mobile']) ?></td>
                                <td><span class="badge badge-<?= $user['role'] == 'admin' ? 'success' : 'warning' ?>"><?= ucfirst($user['role']) ?></span></td>
                                <td><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                                <td>
                                    <a href="<?= base_url('/admin/users/edit/' . $user['id']) ?>" class="btn-action btn-edit"><i class="fas fa-edit"></i> Edit</a>
                                    <?php if ($user['id'] != session()->get('id')): ?>
                                    <a href="<?= base_url('/admin/users/delete/' . $user['id']) ?>" class="btn-action btn-delete" onclick="return confirm('Are you sure you want to delete this user?')"><i class="fas fa-trash"></i> Delete</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="7" class="text-center py-5">No users found</td></tr>
                        <?php endif; ?>
                    </tbody>
                 </table>
            </div>
            <?php if (isset($pager) && $pager->getPageCount() > 1): ?>
                <div class="pagination">
                    <?php if ($currentPage > 1): ?><a href="?page=<?= $currentPage-1 ?><?= isset($search) ? '&search='.$search : '' ?>"><i class="fas fa-chevron-left"></i> Previous</a><?php endif; ?>
                    <?php for ($i = 1; $i <= $pager->getPageCount(); $i++): ?>
                        <?php if ($i == $currentPage): ?><span class="active"><?= $i ?></span><?php else: ?><a href="?page=<?= $i ?><?= isset($search) ? '&search='.$search : '' ?>"><?= $i ?></a><?php endif; ?>
                    <?php endfor; ?>
                    <?php if ($currentPage < $pager->getPageCount()): ?><a href="?page=<?= $currentPage+1 ?><?= isset($search) ? '&search='.$search : '' ?>">Next <i class="fas fa-chevron-right"></i></a><?php endif; ?>
                </div>
                <div class="text-center mt-2">Page <?= $currentPage ?> of <?= $pager->getPageCount() ?></div>
            <?php endif; ?>
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