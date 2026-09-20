<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details | CREATRIX</title>
    
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
            --shadow-sm: 0 4px 6px rgba(31, 69, 41, 0.04);
            --shadow-md: 0 10px 20px rgba(31, 69, 41, 0.08);
            --shadow-hover: 0 15px 30px rgba(42, 92, 55, 0.15);
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            --warning: #F59E0B;
            --info: #3498DB;
            --danger: #E53E3E;
            --success: #2A5C37;
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
        }

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
            z-index: 100;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
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

        nav {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        nav a {
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
        }

        nav a:hover {
            background: rgba(255, 255, 255, 0.15);
            color: var(--white);
        }

        nav a.active {
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
            font-size: 0.75rem;
            margin-left: 2px;
            font-weight: 700;
        }

        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
            flex: 1;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .page-header h1 {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--primary);
            margin: 0;
        }

        .btn-back {
            background: var(--accent-light);
            color: var(--accent);
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-back:hover {
            background: var(--accent);
            color: white;
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }

        .order-card {
            background: var(--card-bg);
            border-radius: 30px;
            padding: 30px;
            box-shadow: var(--shadow-md);
            border: 1px solid rgba(0,0,0,0.03);
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

        .order-date {
            color: var(--text-light);
            font-size: 1rem;
        }

        .order-status {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 30px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .status-pending { background: #fff3cd; color: #856404; }
        .status-confirmed { background: #cce5ff; color: #004085; }
        .status-processing { background: #cce5ff; color: #004085; }
        .status-ready_for_pickup { background: #d4edda; color: #155724; }
        .status-to_ship { background: #cce5ff; color: #004085; }
        .status-shipped { background: #d4edda; color: #155724; }
        .status-completed { background: var(--accent-light); color: var(--accent); }
        .status-cancelled { background: #f8d7da; color: #721c24; }

        /* Timeline */
        .status-timeline {
            margin: 30px 0;
            padding: 20px;
            background: var(--accent-light);
            border-radius: 20px;
        }

        .status-timeline h4 {
            color: var(--primary);
            margin-bottom: 20px;
            font-size: 1.1rem;
        }

        .timeline-steps {
            position: relative;
            padding-left: 30px;
        }

        .timeline-step {
            position: relative;
            padding-bottom: 25px;
        }

        .timeline-step:last-child {
            padding-bottom: 0;
        }

        .timeline-step .step-dot {
            position: absolute;
            left: -25px;
            top: 0;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: var(--text-light);
            border: 2px solid var(--white);
            z-index: 1;
        }

        .timeline-step.completed .step-dot {
            background: var(--accent);
        }

        .timeline-step.current .step-dot {
            background: var(--warning);
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.3);
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.4); }
            70% { box-shadow: 0 0 0 8px rgba(245, 158, 11, 0); }
            100% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0); }
        }

        .timeline-step::before {
            content: '';
            position: absolute;
            left: -19px;
            top: 14px;
            width: 2px;
            height: calc(100% - 14px);
            background: var(--text-light);
        }

        .timeline-step:last-child::before {
            display: none;
        }

        .timeline-step.completed::before {
            background: var(--accent);
        }

        .timeline-step .step-content {
            background: var(--white);
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 8px;
        }

        .timeline-step .step-content strong {
            display: block;
            color: var(--primary);
            font-size: 0.95rem;
            margin-bottom: 4px;
        }

        .timeline-step .step-content small {
            font-size: 0.75rem;
            color: var(--text-light);
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .info-card {
            background: var(--accent-light);
            padding: 20px;
            border-radius: 16px;
        }

        .info-card h4 {
            color: var(--primary);
            margin-bottom: 15px;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-card h4 i {
            color: var(--accent);
        }

        .info-row {
            margin-bottom: 10px;
        }

        .info-label {
            font-weight: 600;
            color: var(--text-light);
            display: inline-block;
            width: 100px;
        }

        .info-value {
            color: var(--text);
        }

        .pickup-info {
            background: var(--accent-light);
            padding: 20px;
            border-radius: 16px;
            margin-top: 20px;
            border-left: 4px solid var(--accent);
        }

        .pickup-info i {
            color: var(--accent);
            margin-right: 10px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .items-table th {
            background: var(--accent-light);
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: var(--primary);
        }

        .items-table td {
            padding: 12px;
            border-bottom: 1px solid var(--accent-light);
        }

        .items-table tr:last-child td {
            border-bottom: none;
        }

        .product-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .product-image {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            overflow: hidden;
            background: var(--accent-light);
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-name {
            font-weight: 500;
        }

        .text-right {
            text-align: right;
        }

        .total-row {
            background: var(--accent-light);
            font-weight: 700;
        }

        .total-row td {
            padding: 15px 12px;
            border-top: 2px solid var(--accent);
        }

        .action-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 30px;
            flex-wrap: wrap;
        }

        .btn-cancel, .btn-receive, .btn-pickup {
            padding: 12px 25px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
        }

        .btn-cancel {
            background: var(--danger);
            color: white;
        }

        .btn-cancel:hover {
            background: #c53030;
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }

        .btn-receive, .btn-pickup {
            background: var(--accent);
            color: white;
        }

        .btn-receive:hover, .btn-pickup:hover {
            background: var(--accent-hover);
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }

        footer {
            background: linear-gradient(135deg, var(--primary) 0%, #3D291D 100%);
            color: rgba(255,255,255,0.8);
            padding: 50px 5% 30px;
            margin-top: 50px;
            text-align: center;
            font-size: 0.95rem;
        }

        footer p {
            margin: 8px 0;
        }

        @media (max-width: 768px) {
            header {
                flex-direction: column;
                gap: 15px;
                padding: 20px 5%;
            }
            
            nav {
                justify-content: center;
            }
            
            .order-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .action-buttons {
                justify-content: center;
            }
            
            .items-table {
                font-size: 0.9rem;
            }
            
            .product-info {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <img src="<?= base_url('assets/creatrix_logo.png') ?>" alt="CREATRIX Logo" onerror="this.src='https://via.placeholder.com/45x45/1F4529/ffffff?text=C'">
            <h2>CREATRIX COIR</h2>
        </div>
        <nav>
            <a href="<?= base_url('/storefront') ?>"><i class="fas fa-store"></i> Store</a>
            <a href="<?= base_url('/products') ?>"><i class="fas fa-box"></i> Products</a>
            <a href="<?= base_url('/cart') ?>">
                <i class="fas fa-shopping-cart"></i> Cart 
                <span class="cart-badge" id="cart-badge">
                    <?php 
                    if (session()->get('isLoggedIn')) {
                        $cartModel = new \App\Models\CartModel();
                        $cartItems = $cartModel->where('user_id', session()->get('id'))->findAll();
                        echo array_sum(array_column($cartItems, 'quantity'));
                    } else {
                        echo 0;
                    }
                    ?>
                </span>
            </a>
            <a href="<?= base_url('/transactions') ?>" class="active"><i class="fas fa-history"></i> My Orders</a>
            <?php if (session()->get('isLoggedIn')): ?>
                <a href="<?= base_url('/profile') ?>"><i class="fas fa-user"></i> <?= esc(session()->get('fullname')) ?></a>
                <a href="<?= base_url('/logout') ?>"><i class="fas fa-sign-out-alt"></i> Logout</a>
            <?php else: ?>
                <a href="<?= base_url('/login') ?>"><i class="fas fa-sign-in-alt"></i> Login</a>
                <a href="<?= base_url('/register') ?>"><i class="fas fa-user-plus"></i> Register</a>
            <?php endif; ?>
        </nav>
    </header>

    <div class="container">
        <div class="page-header">
            <h1>Order Details</h1>
            <a href="<?= base_url('/transactions') ?>" class="btn-back">
                <i class="fas fa-arrow-left"></i> Back to Transactions
            </a>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success" style="background: var(--accent-light); color: var(--accent); padding: 16px; border-radius: 12px; margin-bottom: 20px;">
                <i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-error" style="background: #FEE2E2; color: #ef4444; padding: 16px; border-radius: 12px; margin-bottom: 20px;">
                <i class="fas fa-exclamation-circle"></i> <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($order)): ?>
            <div class="order-card">
                <div class="order-header">
                    <div>
                        <div class="order-id">Order #<?= str_pad($order['id'], 8, '0', STR_PAD_LEFT) ?></div>
                        <div class="order-date">Placed on <?= date('F d, Y \a\t h:i A', strtotime($order['created_at'])) ?></div>
                    </div>
                    <div>
                        <span class="order-status status-<?= $order['status'] ?>">
                            <?= ucfirst(str_replace('_', ' ', $order['status'])) ?>
                        </span>
                    </div>
                </div>

                <!-- Status Timeline -->
                <div class="status-timeline">
                    <h4><i class="fas fa-chart-line"></i> Order Status Timeline</h4>
                    <div class="timeline-steps">
                        <?php 
                        // Define timeline steps based on delivery method
                        if ($order['delivery_method'] === 'delivery') {
                            $timelineSteps = [
                                ['status' => 'Order Placed', 'icon' => 'fa-shopping-cart', 'key' => 'created_at'],
                                ['status' => 'Confirmed', 'icon' => 'fa-check-circle', 'key' => 'seller_confirmed_at'],
                                ['status' => 'Processing', 'icon' => 'fa-box', 'key' => null],
                                ['status' => 'Shipped', 'icon' => 'fa-truck', 'key' => 'shipped_at'],
                                ['status' => 'Delivered', 'icon' => 'fa-home', 'key' => 'delivered_at']
                            ];
                        } else {
                            $timelineSteps = [
                                ['status' => 'Order Placed', 'icon' => 'fa-shopping-cart', 'key' => 'created_at'],
                                ['status' => 'Confirmed', 'icon' => 'fa-check-circle', 'key' => 'seller_confirmed_at'],
                                ['status' => 'Processing', 'icon' => 'fa-box', 'key' => null],
                                ['status' => 'Ready for Pickup', 'icon' => 'fa-store', 'key' => 'ready_at'],
                                ['status' => 'Picked Up', 'icon' => 'fa-check-double', 'key' => 'picked_up_at']
                            ];
                        }
                        
                        $currentStatusIndex = array_search($order['status'], ['pending', 'confirmed', 'processing', 'to_ship', 'shipped', 'ready_for_pickup', 'completed']);
                        
                        foreach ($timelineSteps as $index => $step):
                            $isCompleted = false;
                            $stepDate = null;
                            
                            if ($step['key'] && !empty($order[$step['key']])) {
                                $isCompleted = true;
                                $stepDate = $order[$step['key']];
                            } elseif ($step['status'] === 'Processing' && in_array($order['status'], ['confirmed', 'processing', 'to_ship', 'shipped', 'ready_for_pickup', 'completed'])) {
                                $isCompleted = true;
                            } elseif ($step['status'] === 'Order Placed') {
                                $isCompleted = true;
                                $stepDate = $order['created_at'];
                            }
                            
                            $isCurrent = $order['status'] === 'pending' && $step['status'] === 'Order Placed';
                            $isCurrent = $isCurrent || ($order['status'] === 'confirmed' && $step['status'] === 'Confirmed');
                            $isCurrent = $isCurrent || (in_array($order['status'], ['processing', 'to_ship']) && $step['status'] === 'Processing');
                            $isCurrent = $isCurrent || ($order['status'] === 'shipped' && $step['status'] === 'Shipped');
                            $isCurrent = $isCurrent || ($order['status'] === 'ready_for_pickup' && $step['status'] === 'Ready for Pickup');
                            $isCurrent = $isCurrent || (in_array($order['status'], ['completed']) && $step['status'] === ($order['delivery_method'] === 'delivery' ? 'Delivered' : 'Picked Up'));
                        ?>
                        <div class="timeline-step <?= $isCompleted ? 'completed' : ($isCurrent ? 'current' : '') ?>">
                            <div class="step-dot"></div>
                            <div class="step-content">
                                <strong><i class="fas <?= $step['icon'] ?>"></i> <?= $step['status'] ?></strong>
                                <?php if ($stepDate): ?>
                                    <small><?= date('F d, Y h:i A', strtotime($stepDate)) ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Customer Information -->
                <div class="info-grid">
                    <div class="info-card">
                        <h4><i class="fas fa-user"></i> Customer Information</h4>
                        <div class="info-row">
                            <span class="info-label">Name:</span>
                            <span class="info-value"><?= esc(session()->get('fullname')) ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Email:</span>
                            <span class="info-value"><?= esc(session()->get('email') ?? '') ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Mobile:</span>
                            <span class="info-value"><?= esc($order['user_mobile'] ?? session()->get('mobile') ?? 'N/A') ?></span>
                        </div>
                    </div>

                    <div class="info-card">
                        <h4><i class="fas fa-credit-card"></i> Payment & Delivery</h4>
                        <div class="info-row">
                            <span class="info-label">Payment:</span>
                            <span class="info-value"><?= ucfirst($order['payment_method'] ?? 'Cash') ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Delivery:</span>
                            <span class="info-value"><?= ucfirst($order['delivery_method'] ?? 'Pickup') ?></span>
                        </div>
                        <?php if ($order['delivery_method'] === 'delivery' && !empty($order['tracking_number'])): ?>
                        <div class="info-row">
                            <span class="info-label">Tracking:</span>
                            <span class="info-value"><?= esc($order['tracking_number']) ?></span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="info-card">
                        <h4><i class="fas fa-map-marker-alt"></i> <?= $order['delivery_method'] === 'delivery' ? 'Shipping Address' : 'Pickup Location' ?></h4>
                        <?php if ($order['delivery_method'] === 'delivery'): ?>
                            <div class="info-value" style="line-height: 1.6;">
                                <?= nl2br(esc($order['shipping_address'] ?? session()->get('address') ?? 'No address provided')) ?>
                            </div>
                        <?php else: ?>
                            <div class="info-value">
                                CREATRIX Store, Manila<br>
                                <strong>Pickup Date:</strong> <?= date('F d, Y', strtotime($order['pickup_date'])) ?><br>
                                <strong>Time Slot:</strong> <?= esc($order['pickup_time_slot']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Order Items -->
                <h3 style="margin: 25px 0 15px; color: var(--primary);">
                    <i class="fas fa-box"></i> Order Items
                </h3>

                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th class="text-right">Price</th>
                            <th class="text-right">Quantity</th>
                            <th class="text-right">Subtotal</th>
                        </thead>
                    <tbody>
                        <?php 
                        $subtotal = 0;
                        foreach ($order['items'] as $item): 
                            $itemSubtotal = $item['price'] * $item['quantity'];
                            $subtotal += $itemSubtotal;
                        ?>
                         <tr>
                            <td>
                                <div class="product-info">
                                    <div class="product-image">
                                        <img src="<?= base_url('uploads/products/' . ($item['image'] ?? 'default.jpg')) ?>" 
                                             alt="<?= esc($item['product_name'] ?? 'Product') ?>"
                                             onerror="this.src='https://via.placeholder.com/50x50/DCE7E0/2A5C37?text=Coir'">
                                    </div>
                                    <span class="product-name"><?= esc($item['product_name'] ?? 'Product') ?></span>
                                    <?php if (!empty($item['variation_name'])): ?>
                                        <br><small class="text-muted">(<?= esc($item['variation_name']) ?>)</small>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="text-right">₱<?= number_format($item['price'], 2) ?></td>
                            <td class="text-right"><?= $item['quantity'] ?></td>
                            <td class="text-right">₱<?= number_format($itemSubtotal, 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php 
                        $shipping = ($order['delivery_method'] ?? '') === 'delivery' ? 100 : 0;
                        $total = $subtotal + $shipping;
                        ?>
                        
                        <tr class="total-row">
                            <td colspan="3" class="text-right"><strong>Subtotal</strong></td>
                            <td class="text-right">₱<?= number_format($subtotal, 2) ?></td>
                        </tr>
                        <tr class="total-row">
                            <td colspan="3" class="text-right"><strong>Shipping</strong></td>
                            <td class="text-right">₱<?= number_format($shipping, 2) ?></td>
                        </tr>
                        <tr class="total-row">
                            <td colspan="3" class="text-right"><strong class="grand-total">Total</strong></td>
                            <td class="text-right"><strong class="grand-total">₱<?= number_format($total, 2) ?></strong></td>
                        </tr>
                    </tbody>
                </table>

                <!-- Action Buttons for Customer -->
                <div class="action-buttons">
                    <?php if ($order['status'] === 'pending'): ?>
                        <form action="<?= base_url('/order/cancel/' . $order['id']) ?>" method="post" onsubmit="return confirm('Are you sure you want to cancel this order?');">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn-cancel">
                                <i class="fas fa-times-circle"></i> Cancel Order
                            </button>
                        </form>
                    <?php endif; ?>
                    
                    <?php if ($order['status'] === 'shipped' && $order['delivery_method'] === 'delivery'): ?>
                        <form action="<?= base_url('/order/receive/' . $order['id']) ?>" method="post" onsubmit="return confirm('Confirm receipt of this order?');">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn-receive">
                                <i class="fas fa-check-circle"></i> Order Received
                            </button>
                        </form>
                    <?php endif; ?>
                    
                    <?php if ($order['status'] === 'ready_for_pickup' && $order['delivery_method'] === 'pickup'): ?>
                        <form action="<?= base_url('/order/pickup/' . $order['id']) ?>" method="post" onsubmit="return confirm('Confirm pickup of this order?');">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn-pickup">
                                <i class="fas fa-check-double"></i> Confirm Pickup
                            </button>
                        </form>
                    <?php endif; ?>
                </div>

                <?php if ($order['delivery_method'] === 'pickup' && $order['status'] === 'ready_for_pickup'): ?>
                    <div class="pickup-info">
                        <i class="fas fa-store"></i>
                        <strong>Pickup Information</strong><br>
                        Location: CREATRIX Store, Manila<br>
                        Scheduled Date: <?= date('F d, Y', strtotime($order['pickup_date'])) ?><br>
                        Time Slot: <?= esc($order['pickup_time_slot']) ?><br>
                        <small class="text-muted">Please bring your order confirmation when picking up.</small>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="empty-section" style="text-align: center; padding: 60px 20px;">
                <i class="fas fa-exclamation-circle" style="font-size: 4rem; color: var(--accent-light); margin-bottom: 20px;"></i>
                <h3>Order Not Found</h3>
                <p>The order you're looking for doesn't exist or you don't have permission to view it.</p>
                <a href="<?= base_url('/transactions') ?>" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Back to Transactions
                </a>
            </div>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; 2026 CREATRIX. All rights reserved. | For educational purposes only, and no copyright infringement is intended.</p>
        <p style="margin-top: 10px; font-size: 0.8rem;">CREATRIX Group - Promoting Sustainable Living Through Coconut Coir Innovation</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>