<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kape-Con • Order Coffee</title>

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

        /* Navbar */
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

        /* Header Section */
        .header-section {
            text-align: center;
            padding: 4rem 0 2rem;
            background: linear-gradient(135deg, var(--white) 0%, var(--light-bg) 100%);
            position: relative;
            overflow: hidden;
        }

        .header-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(212, 165, 116, 0.1) 0%, transparent 70%);
            border-radius: 50%;
        }

        .header-section h1 {
            font-size: 3rem;
            font-weight: 800;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 1;
        }

        .header-section p {
            font-size: 1.1rem;
            color: #666;
            font-weight: 500;
            position: relative;
            z-index: 1;
        }

        /* Flash Messages */
        .alert {
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            font-weight: 500;
        }

        .alert-success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
        }

        .alert-danger {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
        }

        /* Menu Grid */
        .menu-section {
            padding: 3rem 0;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            margin-top: 2rem;
        }

        .coffee-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            transition: all 0.4s ease;
            border: none;
        }

        .coffee-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 50px rgba(0,0,0,0.15);
        }

        .coffee-card img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .coffee-card:hover img {
            transform: scale(1.15);
        }

        .coffee-info {
            padding: 1.5rem;
        }

        .coffee-name {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--black);
            margin-bottom: 0.5rem;
        }

        .coffee-price {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .qty-box {
            width: 100%;
            padding: 12px 15px;
            border-radius: 12px;
            border: 2px solid #e0e0e0;
            margin-top: 15px;
            font-weight: 600;
            transition: all 0.3s ease;
            font-size: 1rem;
        }

        .qty-box:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(139, 69, 19, 0.1);
        }

        .btn-order {
            width: 100%;
            background: var(--primary-color);
            color: white;
            padding: 14px;
            border: none;
            border-radius: 50px;
            margin-top: 15px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            cursor: pointer;
            box-shadow: 0 8px 20px rgba(139, 69, 19, 0.3);
        }

        .btn-order:hover {
            background: var(--primary-dark);
            transform: scale(1.05);
            box-shadow: 0 12px 30px rgba(139, 69, 19, 0.4);
        }

        /* Orders Section */
        .orders-section {
            padding: 4rem 0;
            background: var(--light-bg);
            margin-top: 4rem;
        }

        .section-title {
            font-size: 2.5rem;
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

        .orders-table {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background: var(--primary-color) !important;
            color: white;
            font-weight: 600;
            padding: 1.2rem 1rem;
            border: none;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
        }

        .table tbody td {
            padding: 1.2rem 1rem;
            vertical-align: middle;
            border-color: #f0f0f0;
            font-weight: 500;
        }

        .table tbody tr {
            transition: background 0.2s ease;
        }

        .table tbody tr:hover {
            background: #f8f6f3;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: capitalize;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-completed {
            background: #d4edda;
            color: #155724;
        }

        .status-processing {
            background: #d1ecf1;
            color: #0c5460;
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

        /* Responsive */
        @media (max-width: 768px) {
            .header-section h1 {
                font-size: 2rem;
            }

            .section-title {
                font-size: 1.8rem;
            }

            .menu-grid {
                grid-template-columns: 1fr;
            }

            .table-responsive {
                font-size: 0.85rem;
            }
        }
    </style>
</head>

<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand" href="<?= base_url('/homepage') ?>">☕ Kape-Con</a>
        <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="<?= base_url('/homepage') ?>">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= base_url('/homepage') ?>#collections">Menu</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= base_url('/homepage') ?>#products">Featured</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= base_url('/homepage') ?>#news">News</a></li>
                <li class="nav-item"><a class="nav-link active" href="<?= base_url('/order') ?>">Order</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= base_url('/profile') ?>">Profile</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Header -->
<div class="header-section">
    <div class="container">
        <h1>☕ Order Your Coffee</h1>
        <p>Choose from our handcrafted menu below and enjoy freshly brewed perfection.</p>
    </div>
</div>

<div class="container menu-section">

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

    <!-- Menu Grid -->
    <div class="menu-grid">
        <?php foreach ($menu as $item): ?>
        <div class="coffee-card">
            <img src="<?= $item['image'] ?>" alt="<?= $item['product_name'] ?>">

            <div class="coffee-info">
                <h4 class="coffee-name"><?= $item['product_name'] ?></h4>
                <div class="coffee-price">
                    Starts at ₱<?= number_format($item['price_tipid_shot'], 2) ?>
                </div>


                <form action="<?= base_url('/order') ?>" method="post">
                    <input type="hidden" name="menu_id" value="<?= $item['id'] ?>">

                    <!-- SIZE DROPDOWN -->
                    <select name="size" class="qty-box" required>
                        <option value="" disabled selected>Select Size</option>
                        <option value="tipid_shot">Tipid Shot — ₱<?= number_format($item['price_tipid_shot'], 2) ?></option>
                        <option value="tamang_tama">Tamang Tama — ₱<?= number_format($item['price_tamang_tama'], 2) ?></option>
                        <option value="todo_busog">Todo Busog — ₱<?= number_format($item['price_todo_busog'], 2) ?></option>
                    </select>

                    <!-- QUANTITY -->
                    <input type="number" class="qty-box" name="quantity" value="1" min="1" max="99" required placeholder="Quantity">

                    <button type="submit" class="btn-order">
                        <i class='bx bx-cart-add'></i> Add to Order
                    </button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

</div>

<!-- Orders Section -->
<div class="orders-section">
    <div class="container">
        <h2 class="section-title">Your Orders</h2>

        <div class="orders-table">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Coffee</th>
                            <th>Size</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (!empty($orders)): ?>
                            <?php foreach ($orders as $i => $o): ?>
                                <tr>
                                    <td><strong><?= $i + 1 ?></strong></td>
                                    <td><?= $o['product_name'] ?></td>
                                    <td>
                                        <?php
                                            // Convert size name for display
                                            $sizeMap = [
                                                'tipid_shot'   => 'Tipid Shot',
                                                'tamang_tama'  => 'Tamang Tama',
                                                'todo_busog'   => 'Todo Busog'
                                            ];
                                            echo $sizeMap[$o['size']] ?? 'Unknown';
                                        ?>
                                    </td>

                                    <td><?= $o['quantity'] ?></td>
                                    <td>₱<?= number_format($o['price'], 2) ?></td>
                                    <td><strong style="color: var(--primary-color);">₱<?= number_format($o['total'], 2) ?></strong></td>
                                    <td>
                                        <span class="status-badge status-<?= strtolower($o['status']) ?>">
                                            <?= ucfirst($o['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <i class='bx bx-coffee' style="font-size: 3rem; opacity: 0.3;"></i>
                                    <p class="mt-3">No orders yet. Start brewing!</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <div class="pagination-container">
                    <?= $pager->links('orders', 'bootstrap_full') ?>
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