<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Reports | CREATRIX Admin</title>
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
            padding: 20px 30px;
            border-radius: 15px;
            box-shadow: var(--shadow-sm);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .header h1 {
            font-size: 2rem;
            color: var(--primary);
            margin: 0;
        }
        
        .date-form {
            background: var(--card-bg);
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: var(--shadow-sm);
            border: 1px solid rgba(0,0,0,0.02);
        }
        
        .date-form .form-group {
            margin-bottom: 0;
        }
        
        .date-form label {
            font-weight: 500;
            color: var(--text);
            margin-bottom: 8px;
            display: block;
            font-size: 0.9rem;
        }
        
        .date-form input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--accent-light);
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            transition: var(--transition);
        }
        
        .date-form input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(42, 92, 55, 0.1);
        }
        
        .btn-primary {
            background: var(--accent);
            border: none;
            padding: 10px 24px;
            border-radius: 8px;
            color: white;
            cursor: pointer;
            font-weight: 500;
            transition: var(--transition);
            width: 100%;
            margin-top: 28px;
        }
        
        .btn-primary:hover {
            background: var(--accent-hover);
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 25px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: var(--card-bg);
            padding: 25px;
            border-radius: 15px;
            box-shadow: var(--shadow-sm);
            transition: transform 0.3s;
            border: 1px solid rgba(0,0,0,0.02);
        }
        
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-hover);
        }
        
        .stat-icon {
            width: 50px;
            height: 50px;
            background: var(--accent-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }
        
        .stat-icon i {
            font-size: 1.5rem;
            color: var(--accent);
        }
        
        .stat-label {
            font-size: 0.85rem;
            color: var(--text-light);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary);
        }
        
        .stat-number small {
            font-size: 1rem;
            font-weight: 500;
        }
        
        .table-responsive {
            background: var(--card-bg);
            border-radius: 15px;
            padding: 20px;
            box-shadow: var(--shadow-sm);
            overflow-x: auto;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .table th {
            background: var(--accent-light);
            color: var(--text);
            font-weight: 600;
            padding: 12px;
            text-align: left;
            font-size: 0.9rem;
        }
        
        .table td {
            padding: 12px;
            border-bottom: 1px solid var(--accent-light);
            color: var(--text);
        }
        
        .table tr:last-child td {
            border-bottom: none;
        }
        
        .text-center {
            text-align: center;
        }
        
        footer {
            margin-top: 30px;
            text-align: center;
            color: var(--text-light);
            font-size: 0.9rem;
            padding: 20px 0;
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
            
            .header {
                text-align: center;
                justify-content: center;
            }
            
            .date-form .row {
                flex-direction: column;
            }
            
            .date-form .col-md-4 {
                width: 100%;
                margin-bottom: 15px;
            }
            
            .btn-primary {
                margin-top: 0;
            }
        }
        
        @media (max-width: 480px) {
            .stat-card {
                text-align: center;
            }
            
            .stat-icon {
                margin: 0 auto 15px;
            }
            
            .table th, .table td {
                font-size: 0.8rem;
                padding: 8px;
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
            <a class="nav-link active" href="<?= base_url('/admin/reports') ?>"><i class="fas fa-chart-line"></i> Sales Reports</a>
            <a class="nav-link" href="<?= base_url('/admin/inventory-report') ?>"><i class="fas fa-file-alt"></i> Inventory Report</a>
            <a class="nav-link" href="<?= base_url('/admin/orders') ?>"><i class="fas fa-shopping-cart"></i> Orders</a>
            <hr style="border-color: rgba(255,255,255,0.2);">
            <a class="nav-link" href="<?= base_url('/logout') ?>"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Sales Reports</h1>
        </div>

        <!-- Date Range Filter -->
        <div class="date-form">
            <form method="get">
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><i class="fas fa-calendar-alt"></i> Start Date</label>
                            <input type="date" name="start_date" value="<?= $startDate ?>" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><i class="fas fa-calendar-check"></i> End Date</label>
                            <input type="date" name="end_date" value="<?= $endDate ?>" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-chart-line"></i> Generate Report
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Summary Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-label">Total Orders</div>
                <div class="stat-number"><?= number_format($summary['total_orders'] ?? 0) ?></div>
                <small style="color: var(--text-light);">orders placed</small>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-peso-sign"></i>
                </div>
                <div class="stat-label">Total Revenue</div>
                <div class="stat-number">₱<?= number_format($summary['total_revenue'] ?? 0, 2) ?></div>
                <small style="color: var(--text-light);">total sales amount</small>
            </div>
        </div>

        <!-- Daily Sales Table -->
        <div class="table-responsive">
            <table class="table">
                <thead>
                     <th><i class="fas fa-calendar-day"></i> Date</th>
                        <th><i class="fas fa-shopping-cart"></i> Orders</th>
                        <th><i class="fas fa-peso-sign"></i> Total Sales</th>
                    </thead>
                <tbody>
                    <?php if (!empty($sales)): ?>
                        <?php 
                        $totalOrders = 0;
                        $totalSales = 0;
                        foreach ($sales as $row): 
                            $totalOrders += $row['order_count'];
                            $totalSales += $row['total_sales'];
                        ?>
                         <tr>
                            <td><strong><?= date('M d, Y', strtotime($row['date'])) ?></strong></td>
                            <td><?= number_format($row['order_count']) ?></td>
                            <td class="text-success fw-bold">₱<?= number_format($row['total_sales'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <tr style="background: var(--accent-light); font-weight: bold;">
                            <td><strong>Total</strong></td>
                            <td><strong><?= number_format($totalOrders) ?></strong></td>
                            <td><strong>₱<?= number_format($totalSales, 2) ?></strong></td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center py-4">
                                <i class="fas fa-chart-line" style="font-size: 3rem; color: var(--accent-light); margin-bottom: 10px; display: block;"></i>
                                No sales data for this period
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
             </table>
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