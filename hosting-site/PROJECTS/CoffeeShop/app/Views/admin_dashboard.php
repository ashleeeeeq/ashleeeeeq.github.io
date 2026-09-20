<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kape-Con • Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #8B4513;
            --primary-dark: #6F3609;
            --accent-color: #D4A574;
            --black: #2c2c2c;
            --white: #faf8f5;
            --grey: #c8b6a6;
            --light-bg: #f8f6f3;
        }

        * {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--white);
            color: var(--black);
        }

        /* Enhanced Navbar */
        .navbar {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95) !important;
            box-shadow: 0 2px 20px rgba(0,0,0,0.08);
            padding: 1rem 0;
            transition: all 0.3s ease;
        }

        .navbar-brand {
            color: var(--primary-color) !important;
            font-weight: 800;
            font-size: 1.5rem;
            letter-spacing: -0.5px;
        }

        .navbar-brand:hover {
            color: var(--primary-dark) !important;
        }

        .nav-link {
            color: var(--black) !important;
            font-weight: 500;
            margin: 0 0.5rem;
            position: relative;
            transition: color 0.3s ease;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 2px;
            background: var(--primary-color);
            transition: width 0.3s ease;
        }

        .nav-link:hover {
            color: var(--primary-color) !important;
        }

        .nav-link:hover::after,
        .nav-link.active::after {
            width: 60%;
        }

        .admin-badge {
            background: var(--primary-color);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-left: 10px;
            letter-spacing: 0.5px;
        }

        /* Header Section */
        .dashboard-header {
            background: linear-gradient(135deg, var(--white) 0%, var(--light-bg) 100%);
            padding: 4rem 0 3rem;
            position: relative;
            overflow: hidden;
        }

        .dashboard-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(212, 165, 116, 0.1) 0%, transparent 70%);
            border-radius: 50%;
        }

        .coffee-icon {
            font-size: 4rem;
            display: inline-block;
            animation: float 3s ease-in-out infinite;
            filter: drop-shadow(0 10px 30px rgba(139, 69, 19, 0.3));
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        .dashboard-title {
            font-size: 3rem;
            font-weight: 800;
            color: var(--primary-color);
            margin: 1rem 0 0.5rem;
            position: relative;
        }

        .dashboard-subtitle {
            font-size: 1rem;
            color: #666;
            font-weight: 500;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        /* Stats Cards */
        .stats-section {
            margin-top: -40px;
            margin-bottom: 3rem;
            position: relative;
            z-index: 10;
        }

        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            transition: all 0.4s ease;
            border: none;
        }

        .stat-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 50px rgba(0,0,0,0.15);
        }

        .stat-icon {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--black);
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 0.9rem;
            color: #666;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Alert Messages */
        .alert {
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            font-weight: 500;
            margin-bottom: 2rem;
        }

        .alert-success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
        }

        .alert-danger {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
        }

        /* Section */
        .section {
            margin-bottom: 4rem;
        }

        .section-title {
            font-size: 2rem;
            font-weight: 800;
            color: var(--primary-color);
            margin-bottom: 2rem;
            position: relative;
            display: inline-block;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 60px;
            height: 4px;
            background: var(--primary-color);
            border-radius: 2px;
        }

        /* Table Container */
        .table-container {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            transition: all 0.4s ease;
        }

        .table-container:hover {
            box-shadow: 0 15px 40px rgba(0,0,0,0.12);
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background: var(--primary-color);
            color: white;
            font-weight: 600;
            padding: 1.2rem 1rem;
            border: none;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        .table tbody td {
            padding: 1.2rem 1rem;
            vertical-align: middle;
            border-color: #f0f0f0;
            font-weight: 500;
        }

        .table tbody tr {
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            background: #f8f6f3;
        }

        /* Badges */
        .badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .badge.bg-secondary {
            background: #6c757d !important;
        }

        .badge.bg-success {
            background: #28a745 !important;
        }

        .badge.bg-warning {
            background: #ffc107 !important;
            color: #000 !important;
        }

        .badge.bg-info {
            background: #17a2b8 !important;
        }

        .role-badge {
            background: rgba(139, 69, 19, 0.1) !important;
            color: var(--primary-color) !important;
            border: 1px solid rgba(139, 69, 19, 0.3);
        }

        /* Buttons */
        .btn {
            padding: 8px 20px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-warning {
            background: var(--accent-color);
            color: var(--black);
            box-shadow: 0 4px 15px rgba(212, 165, 116, 0.3);
        }

        .btn-warning:hover {
            background: #c49563;
            color: var(--black);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(212, 165, 116, 0.4);
        }

        .btn-danger {
            background: #dc3545;
            color: white;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
        }

        .btn-danger:hover {
            background: #c82333;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(220, 53, 69, 0.4);
        }

        .btn-logout {
            width: 100%;
            background: var(--primary-color);
            color: white;
            padding: 16px;
            border: none;
            border-radius: 50px;
            margin-top: 15px;
            font-weight: 600;
            font-size: 1rem;
            box-shadow: 0 8px 20px rgba(139, 69, 19, 0.3);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-logout:hover {
            background: var(--primary-dark);
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(139, 69, 19, 0.4);
        }

        /* Status Pill */
        .status-pill {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-pending {
            background: rgba(52, 152, 219, 0.15);  
            color: #2c82c9;
        }

        .status-processing {
            background: rgba(241, 196, 15, 0.18);  
            color: #b38a00;
        }

        .status-completed {
            background: rgba(46, 204, 113, 0.18);   
            color: #2ecc71;
        }

        .status-select {
            border-radius: 12px !important;
            background: #fafafa !important;
            border: 1px solid #ddd !important;
            padding: 6px 10px !important;
            font-size: 0.8rem !important;
            font-weight: 500;
            width: 135px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .status-select:hover {
            border-color: #bbb !important;
        }

        .status-select:focus {
            border-color: #8B4513 !important;
            box-shadow: 0 0 0 3px rgba(139, 69, 19, 0.2) !important;
        }

        /* Footer */
        footer {
            background: var(--black);
            color: var(--white);
            padding: 2rem 0;
            margin-top: 4rem;
        }

        footer p {
            margin: 0;
            opacity: 0.8;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem 0;
            color: #999;
        }

        .empty-state i {
            font-size: 3rem;
            opacity: 0.3;
            margin-bottom: 1rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .dashboard-title {
                font-size: 2rem;
            }

            .section-title {
                font-size: 1.5rem;
            }

            .stat-card {
                margin-bottom: 1rem;
            }

            .table-responsive {
                font-size: 0.85rem;
            }

            .btn {
                font-size: 0.75rem;
                padding: 6px 15px;
            }
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand" href="homepage.php">
            ☕ Kape-Con
            <span class="admin-badge">ADMIN</span>
        </a>
        <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="<?= base_url('/admin/profile') ?>">Profile</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= base_url('/logout') ?>">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Dashboard Header -->
<div class="dashboard-header">
    <div class="container text-center">
        <div class="coffee-icon">☕</div>
        <h1 class="dashboard-title">Admin Dashboard</h1>
        <p class="dashboard-subtitle">Manage Your Coffee Shop</p>
    </div>
</div>

<div class="container">

    <!-- Stats Section -->
    <div class="stats-section">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="stat-card text-center">
                    <i class='bx bx-user stat-icon'></i>
                    <div class="stat-value"><?= $totalUsers ?></div>
                    <div class="stat-label">Team Members</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card text-center">
                    <i class='bx bx-receipt stat-icon'></i>
                    <div class="stat-value"><?= $totalOrders ?></div>
                    <div class="stat-label">Total Orders</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card text-center">
                    <i class='bx bx-coffee stat-icon'></i>
                    <div class="stat-value"><?= $completedOrders ?></div>
                    <div class="stat-label">Completed</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
            <i class='bx bx-check-circle'></i> <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <i class='bx bx-error-circle'></i> <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <!-- Users Table -->
    <div class="section">
        <h2 class="section-title"><i class='bx bx-group'></i> Team Members</h2>
        <div class="table-container">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Password</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($users)): ?>
                            <?php foreach ($users as $index => $u): ?>
                                <tr>
                                    <td><strong><?= $index + 1 ?></strong></td>
                                    <td><?= esc($u['name']) ?></td>
                                    <td><?= esc($u['email']) ?></td>
                                    <td><code><?= esc($u['plain_password']) ?></code></td>
                                    <td><span class="badge role-badge"><?= esc($u['role']) ?></span></td>
                                    <td>
                                        <a href="<?= base_url('/admin/edit/'.$u['id']) ?>" class="btn btn-sm btn-warning">
                                            <i class='bx bx-edit'></i> Edit
                                        </a>
                                        <a href="<?= base_url('/admin/delete/'.$u['id']) ?>" 
                                            class="btn btn-sm btn-danger" 
                                            onclick="return confirm('Are you sure you want to delete this user?')">
                                            <i class='bx bx-trash'></i> Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="empty-state">
                                    <i class='bx bx-user-x'></i>
                                    <p>No team members found</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <div class="d-flex justify-content-center mt-3">
                    <?= $pager->links('users', 'full-bootstrap') ?>
                </div>

            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="section">
        <h2 class="section-title"><i class='bx bx-receipt'></i> Recent Orders</h2>
        <div class="table-container">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Customer</th>
                            <th>Order Details</th>
                            <th>Size</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($orders)): ?>
                            <?php foreach ($orders as $index => $o): ?>
                                <tr>
                                    <td><strong><?= $index + 1 ?></strong></td>
                                    <td><?= esc($o['user_name'] ?? 'Unknown User') ?></td>
                                    <td><?= esc($o['product_name']) ?></td>

                                    <td>
                                        <?php
                                            $sizeMap = [
                                                'tipid_shot'   => 'Tipid Shot',
                                                'tamang_tama'  => 'Tamang Tama',
                                                'todo_busog'   => 'Todo Busog'
                                            ];
                                            echo $sizeMap[$o['size']] ?? 'Unknown';
                                        ?>
                                    </td>

                                    <td><strong>×<?= esc($o['quantity']) ?></strong></td>

                                    <td>₱<?= number_format($o['price'], 2) ?></td>

                                    <td>
                                        <form action="<?= base_url('admin/updateStatus/' . $o['id']) ?>" method="post" class="m-0 p-0">
                                            <?= csrf_field() ?>
                                            <?php
                                                $statusClass = match($o['status']) {
                                                    'Completed'  => 'status-completed',
                                                    'Processing' => 'status-processing',
                                                    'Pending'    => 'status-pending',
                                                    default      => 'status-pending'
                                                };
                                            ?>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="status-pill <?= $statusClass ?>">
                                                    <?= esc($o['status']) ?>
                                                </span>
                                                <select name="status" class="status-select" onchange="this.form.submit()">
                                                    <option value="Pending"    <?= $o['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                                    <option value="Processing" <?= $o['status'] === 'Processing' ? 'selected' : '' ?>>Processing</option>
                                                    <option value="Completed"  <?= $o['status'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
                                                </select>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="empty-state">
                                    <i class='bx bx-coffee'></i>
                                    <p>No orders yet. Start brewing!</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <div class="d-flex justify-content-center mt-3">
                    <?= $pager->links('orders', 'full-bootstrap') ?>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Footer -->
<footer>
    <div class="container text-center">
        <p>© <?= date('Y') ?> Kape-Con Coffee Shop. All rights reserved.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>