<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Order Details | CREATRIX Admin</title>
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
        
        .btn-back {
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
        }
        
        .btn-back:hover {
            background: var(--accent-hover);
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }
        
        .order-card {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 25px;
            box-shadow: var(--shadow-sm);
            margin-bottom: 30px;
        }
        
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 2px solid var(--accent-light);
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .order-id {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
        }
        
        .badge {
            padding: 8px 16px;
            border-radius: 30px;
            font-size: 0.8rem;
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
        
        .status-form {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        .status-select {
            padding: 10px 16px;
            border-radius: 30px;
            border: 2px solid var(--accent-light);
            background: var(--white);
            font-size: 0.9rem;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s;
        }
        .status-select:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(42, 92, 55, 0.1);
        }
        .btn-update {
            background: var(--accent);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 30px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-update:hover {
            background: var(--accent-hover);
            transform: translateY(-2px);
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }
        .info-card {
            background: var(--accent-light);
            padding: 20px;
            border-radius: 16px;
            transition: all 0.3s;
        }
        .info-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-sm);
        }
        .info-card h4 {
            color: var(--primary);
            margin-bottom: 15px;
            font-size: 1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(0,0,0,0.1);
        }
        .info-card h4 i {
            color: var(--accent);
            width: 24px;
        }
        .info-row {
            margin-bottom: 12px;
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }
        .info-label {
            font-weight: 600;
            color: var(--text-light);
            min-width: 100px;
        }
        .info-value {
            color: var(--text);
            font-weight: 500;
        }
        .timeline-section {
            margin-top: 25px;
            padding: 20px;
            background: var(--accent-light);
            border-radius: 16px;
            margin-bottom: 25px;
        }
        .timeline-section h4 {
            color: var(--primary);
            margin-bottom: 20px;
            font-size: 1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .timeline-steps {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }
        .timeline-step {
            flex: 1;
            text-align: center;
            padding: 12px;
            background: var(--white);
            border-radius: 12px;
            transition: all 0.3s;
            min-width: 100px;
        }
        .timeline-step.completed {
            background: var(--success);
            color: white;
        }
        .timeline-step.current {
            background: var(--warning);
            color: white;
            box-shadow: var(--shadow-sm);
        }
        .timeline-step .step-icon {
            font-size: 1.2rem;
            margin-bottom: 6px;
        }
        .timeline-step .step-label {
            font-size: 0.75rem;
            font-weight: 500;
        }
        .table-responsive {
            overflow-x: auto;
            margin-top: 15px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 500px;
        }
        .items-table th {
            background: var(--accent-light);
            padding: 12px;
            text-align: left;
            font-weight: 600;
            font-size: 0.85rem;
        }
        .items-table td {
            padding: 12px;
            border-bottom: 1px solid var(--accent-light);
            vertical-align: middle;
        }
        .items-table tr:hover {
            background: var(--accent-light);
        }
        .total-row {
            background: var(--accent-light);
            font-weight: 700;
        }
        .total-row td {
            padding: 12px;
        }
        .pickup-info {
            background: var(--accent-light);
            padding: 20px;
            border-radius: 16px;
            margin-top: 20px;
            border-left: 4px solid var(--accent);
        }
        .alert {
            padding: 15px 20px;
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
            
            .btn-back {
                width: 100%;
                justify-content: center;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .timeline-steps {
                flex-direction: column;
            }
            
            .timeline-step {
                min-width: auto;
            }
            
            .status-form {
                flex-direction: column;
                align-items: stretch;
                width: 100%;
            }
            
            .status-select {
                width: 100%;
            }
            
            .btn-update {
                width: 100%;
                justify-content: center;
            }
            
            .order-header {
                flex-direction: column;
                align-items: flex-start;
            }
        }
        
        @media (max-width: 480px) {
            .order-id {
                font-size: 1.2rem;
            }
            
            .info-row {
                flex-direction: column;
                gap: 2px;
            }
            
            .info-label {
                min-width: auto;
            }
            
            .items-table th, 
            .items-table td {
                padding: 8px;
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
                <i class="fas fa-file-alt"></i>
                Order Details
            </h1>
            <a href="<?= base_url('/admin/orders') ?>" class="btn-back">
                <i class="fas fa-arrow-left"></i> Back to Orders
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

        <div class="order-card">
            <div class="order-header">
                <div class="order-id">Order #<?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?></div>
                <span class="badge badge-<?= $order['status'] ?>">
                    <?= ucfirst(str_replace('_', ' ', $order['status'])) ?>
                </span>
            </div>

            <!-- Status Update Form -->
            <div class="info-card" style="margin-bottom: 25px;">
                <h4><i class="fas fa-pencil-alt"></i> Update Order Status</h4>
                <form action="<?= base_url('/admin/orders/update-status/' . $order['id']) ?>" method="post" class="status-form">
                    <?= csrf_field() ?>
                    <select name="status" class="status-select">
                        <?php if ($order['delivery_method'] === 'delivery'): ?>
                            <option value="pending" <?= $order['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="confirmed" <?= $order['status'] == 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                            <option value="processing" <?= $order['status'] == 'processing' ? 'selected' : '' ?>>Processing</option>
                            <option value="to_ship" <?= $order['status'] == 'to_ship' ? 'selected' : '' ?>>Ready to Ship</option>
                            <option value="shipped" <?= $order['status'] == 'shipped' ? 'selected' : '' ?>>Shipped</option>
                            <option value="completed" <?= $order['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                            <option value="cancelled" <?= $order['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                        <?php else: ?>
                            <option value="pending" <?= $order['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="confirmed" <?= $order['status'] == 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                            <option value="processing" <?= $order['status'] == 'processing' ? 'selected' : '' ?>>Processing</option>
                            <option value="ready_for_pickup" <?= $order['status'] == 'ready_for_pickup' ? 'selected' : '' ?>>Ready for Pickup</option>
                            <option value="completed" <?= $order['status'] == 'completed' ? 'selected' : '' ?>>Picked Up</option>
                            <option value="cancelled" <?= $order['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                        <?php endif; ?>
                    </select>
                    <button type="submit" class="btn-update">
                        <i class="fas fa-save"></i> Update Status
                    </button>
                </form>
            </div>

            <div class="info-grid">
                <div class="info-card">
                    <h4><i class="fas fa-user"></i> Customer Information</h4>
                    <div class="info-row">
                        <span class="info-label">Name:</span>
                        <span class="info-value"><?= esc($order['customer']) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Email:</span>
                        <span class="info-value"><?= esc($order['user_email'] ?? 'N/A') ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Mobile:</span>
                        <span class="info-value"><?= esc($order['user_mobile'] ?? 'N/A') ?></span>
                    </div>
                </div>
                <div class="info-card">
                    <h4><i class="fas fa-credit-card"></i> Order Information</h4>
                    <div class="info-row">
                        <span class="info-label">Date:</span>
                        <span class="info-value"><?= date('F d, Y h:i A', strtotime($order['created_at'])) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Payment:</span>
                        <span class="info-value"><?= ucfirst($order['payment_method'] ?? 'N/A') ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Delivery:</span>
                        <span class="info-value"><?= ucfirst($order['delivery_method'] ?? 'N/A') ?></span>
                    </div>
                </div>
                <div class="info-card">
                    <h4><i class="fas fa-location-dot"></i> <?= $order['delivery_method'] === 'delivery' ? 'Shipping Address' : 'Pickup Location' ?></h4>
                    <?php if ($order['delivery_method'] === 'delivery'): ?>
                        <div class="info-value"><?= nl2br(esc($order['shipping_address'] ?? 'No address provided')) ?></div>
                    <?php else: ?>
                        <div class="info-value">
                            <strong>CREATRIX Store</strong><br>
                            Manila, Philippines<br>
                            <i class="fas fa-calendar-alt"></i> <strong>Pickup Date:</strong> <?= date('F d, Y', strtotime($order['pickup_date'])) ?><br>
                            <i class="fas fa-clock"></i> <strong>Time Slot:</strong> <?= esc($order['pickup_time_slot']) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Order Timeline -->
            <div class="timeline-section">
                <h4><i class="fas fa-chart-line"></i> Order Timeline</h4>
                <div class="timeline-steps">
                    <?php if ($order['delivery_method'] === 'delivery'): ?>
                        <?php
                        $deliverySteps = [
                            'pending' => ['icon' => 'fa-clock', 'label' => 'Order Placed'],
                            'confirmed' => ['icon' => 'fa-check-circle', 'label' => 'Confirmed'],
                            'processing' => ['icon' => 'fa-box', 'label' => 'Processing'],
                            'to_ship' => ['icon' => 'fa-truck', 'label' => 'Ready to Ship'],
                            'shipped' => ['icon' => 'fa-shipping-fast', 'label' => 'Shipped'],
                            'completed' => ['icon' => 'fa-flag-checkered', 'label' => 'Completed']
                        ];
                        $statusOrder = ['pending', 'confirmed', 'processing', 'to_ship', 'shipped', 'completed'];
                        $currentIndex = array_search($order['status'], $statusOrder);
                        foreach ($deliverySteps as $stepStatus => $stepInfo):
                            $stepIndex = array_search($stepStatus, $statusOrder);
                            $isCompleted = $currentIndex !== false && $stepIndex <= $currentIndex && $order['status'] !== 'cancelled';
                            $isCurrent = $order['status'] === $stepStatus && $order['status'] !== 'cancelled';
                        ?>
                        <div class="timeline-step <?= $isCompleted ? 'completed' : ($isCurrent ? 'current' : '') ?>">
                            <div class="step-icon"><i class="fas <?= $stepInfo['icon'] ?>"></i></div>
                            <div class="step-label"><?= $stepInfo['label'] ?></div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <?php
                        $pickupSteps = [
                            'pending' => ['icon' => 'fa-clock', 'label' => 'Order Placed'],
                            'confirmed' => ['icon' => 'fa-check-circle', 'label' => 'Confirmed'],
                            'processing' => ['icon' => 'fa-box', 'label' => 'Processing'],
                            'ready_for_pickup' => ['icon' => 'fa-store', 'label' => 'Ready for Pickup'],
                            'completed' => ['icon' => 'fa-check-double', 'label' => 'Picked Up']
                        ];
                        $statusOrder = ['pending', 'confirmed', 'processing', 'ready_for_pickup', 'completed'];
                        $currentIndex = array_search($order['status'], $statusOrder);
                        foreach ($pickupSteps as $stepStatus => $stepInfo):
                            $stepIndex = array_search($stepStatus, $statusOrder);
                            $isCompleted = $currentIndex !== false && $stepIndex <= $currentIndex && $order['status'] !== 'cancelled';
                            $isCurrent = $order['status'] === $stepStatus && $order['status'] !== 'cancelled';
                        ?>
                        <div class="timeline-step <?= $isCompleted ? 'completed' : ($isCurrent ? 'current' : '') ?>">
                            <div class="step-icon"><i class="fas <?= $stepInfo['icon'] ?>"></i></div>
                            <div class="step-label"><?= $stepInfo['label'] ?></div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <h4 style="color: var(--primary); margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-box"></i> Order Items
            </h4>
            <div class="table-responsive">
                <table class="items-table">
                    <thead>
                        <tr><th>Product</th><th>Price</th><th>Quantity</th><th>Subtotal</th> </thead>
                    <tbody>
                        <?php 
                        $subtotal = 0;
                        foreach ($order['items'] as $item): 
                            $itemSubtotal = $item['price'] * $item['quantity'];
                            $subtotal += $itemSubtotal;
                        ?>
                        <tr>
                            <td>
                                <?= esc($item['product_name']) ?>
                                <?php if (!empty($item['variation_name'])): ?>
                                    <br><small class="text-muted">(<?= esc($item['variation_name']) ?>)</small>
                                <?php endif; ?>
                              </td>
                            <td class="fw-bold">₱<?= number_format($item['price'], 2) ?> </td>
                            <td><?= $item['quantity'] ?> </td>
                            <td class="fw-bold text-success">₱<?= number_format($itemSubtotal, 2) ?> </td>
                         '</div>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="total-row">
                            <td colspan="3" class="text-end"><strong>Subtotal</strong> </td>
                            <td><strong>₱<?= number_format($subtotal, 2) ?></strong> </td>
                         </tr>
                        <tr class="total-row">
                            <td colspan="3" class="text-end"><strong>Shipping</strong> </td>
                            <td><strong><?= $order['delivery_method'] === 'delivery' ? '₱100.00' : 'Free' ?></strong> </td>
                         </tr>
                        <tr class="total-row" style="background: var(--accent); color: white;">
                            <td colspan="3" class="text-end"><strong>Total</strong> </td>
                            <td><strong>₱<?= number_format($order['total_price'], 2) ?></strong> </td>
                         </tr>
                    </tfoot>
                 </table>
            </div>

            <?php if ($order['delivery_method'] === 'pickup' && $order['status'] === 'ready_for_pickup'): ?>
            <div class="pickup-info">
                <i class="fas fa-store"></i>
                <strong>Pickup Information</strong><br>
                Location: CREATRIX Store, Manila<br>
                Scheduled Date: <?= date('F d, Y', strtotime($order['pickup_date'])) ?><br>
                Time Slot: <?= esc($order['pickup_time_slot']) ?><br>
                <small class="text-muted">Please prepare the order for pickup by the customer.</small>
            </div>
            <?php endif; ?>
        </div>
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
                if (!sidebar.classList.contains('open')) {
                    sidebar.classList.add('closed');
                }
            }
        });
    </script>
</body>
</html>