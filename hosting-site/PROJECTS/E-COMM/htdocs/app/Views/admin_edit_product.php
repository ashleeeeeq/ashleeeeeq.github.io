<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Edit Product | CREATRIX Admin</title>
    
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

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ===== SIDEBAR WITH HAMBURGER ===== */
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

        .user-info {
            padding: 12px 15px;
            margin-top: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-top: 1px solid rgba(255,255,255,0.2);
            padding-top: 15px;
        }

        .user-info i {
            font-size: 1.2rem;
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
            padding: 10px 24px;
            border-radius: 10px;
            text-decoration: none;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
            white-space: nowrap;
        }

        .btn-back:hover {
            background: var(--accent-hover);
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }

        .form-card {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 30px;
            box-shadow: var(--shadow-sm);
        }

        .form-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid var(--accent-light);
        }

        .form-header i {
            font-size: 3rem;
            color: var(--accent);
            margin-bottom: 15px;
        }

        .form-header h2 {
            font-weight: 700;
            color: var(--primary);
            font-size: 1.8rem;
            margin-bottom: 8px;
        }

        .form-header p {
            color: var(--text-light);
            font-size: 0.9rem;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-label {
            font-weight: 500;
            display: block;
            margin-bottom: 8px;
            color: var(--text);
            font-size: 0.9rem;
        }

        .form-label i {
            color: var(--accent);
            margin-right: 8px;
            width: 20px;
        }

        .form-control, .form-select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid var(--accent-light);
            border-radius: 12px;
            font-size: 0.95rem;
            transition: all 0.3s;
            background: var(--white);
            font-family: 'Poppins', sans-serif;
        }

        .form-control:focus, .form-select:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(42, 92, 55, 0.1);
        }

        .image-preview {
            margin-top: 10px;
            padding: 20px;
            border: 2px dashed var(--accent-light);
            border-radius: 12px;
            text-align: center;
            background: var(--bg);
            transition: all 0.3s;
        }

        .image-preview:hover {
            border-color: var(--accent);
        }

        .image-preview img {
            max-width: 200px;
            max-height: 200px;
            border-radius: 12px;
            margin-bottom: 10px;
        }

        .image-preview p {
            color: var(--text-light);
            font-size: 0.8rem;
            margin: 0;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid var(--accent-light);
        }

        .btn {
            flex: 1;
            padding: 12px 20px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-update {
            background: var(--accent);
            color: white;
        }

        .btn-update:hover {
            background: var(--accent-hover);
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }

        .btn-cancel {
            background: var(--text-light);
            color: white;
        }

        .btn-cancel:hover {
            background: var(--text);
            transform: translateY(-2px);
        }

        .btn-delete {
            background: var(--danger);
            color: white;
            width: 100%;
            margin-top: 20px;
        }

        .btn-delete:hover {
            background: #c53030;
            transform: translateY(-2px);
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

        .info-text {
            font-size: 0.75rem;
            color: var(--text-light);
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        footer {
            margin-top: 30px;
            text-align: center;
            color: var(--text-light);
            font-size: 0.85rem;
            padding: 20px;
        }

        /* RESPONSIVE DESIGN */
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
                white-space: normal;
            }
            
            .form-card {
                padding: 20px;
            }
            
            .form-header h2 {
                font-size: 1.4rem;
            }
            
            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }
            
            .form-buttons {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
            }
        }
        
        @media (max-width: 480px) {
            .main-content {
                padding: 70px 15px 15px;
            }
            
            .form-card {
                padding: 15px;
            }
            
            .form-header i {
                font-size: 2.5rem;
            }
            
            .form-header h2 {
                font-size: 1.2rem;
            }
            
            .image-preview img {
                max-width: 150px;
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

    <!-- Sidebar -->
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
            <div class="user-info">
                <i class="fas fa-user-circle"></i>
                <span><?= session()->get('fullname') ?></span>
            </div>
            <a class="nav-link" href="<?= base_url('/logout') ?>"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </div>

    <div class="main-content">
        <div class="page-header">
            <h1>
                <i class="fas fa-edit"></i>
                Edit Product
            </h1>
            <a href="<?= base_url('/admin/inventory') ?>" class="btn-back">
                <i class="fas fa-arrow-left"></i> Back to Inventory
            </a>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <?php if (isset($validation)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?= $validation->listErrors() ?>
            </div>
        <?php endif; ?>

        <div class="form-card">
            <div class="form-header">
                <i class="fas fa-pencil-alt"></i>
                <h2>Product Information</h2>
                <p>Update the details of your product</p>
            </div>

            <form action="<?= base_url('/admin/product/update/' . $product['id']) ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-tag"></i> Product Name
                    </label>
                    <input type="text" name="product_name" class="form-control" 
                           value="<?= set_value('product_name', $product['product_name']) ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-align-left"></i> Description
                    </label>
                    <textarea name="description" class="form-control" rows="4" required><?= set_value('description', $product['description']) ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-coin"></i> Price (₱)
                        </label>
                        <input type="number" name="price" class="form-control" 
                               value="<?= set_value('price', $product['price']) ?>" step="0.01" min="0" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-cubes"></i> Stock Quantity
                        </label>
                        <input type="number" name="stock" class="form-control" 
                               value="<?= set_value('stock', $product['stock']) ?>" min="0" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-list"></i> Category
                        </label>
                        <select name="category" class="form-select" required>
                            <option value="Gardening" <?= $product['category'] == 'Gardening' ? 'selected' : '' ?>>Gardening</option>
                            <option value="Home & Living" <?= $product['category'] == 'Home & Living' ? 'selected' : '' ?>>Home & Living</option>
                            <option value="Agriculture" <?= $product['category'] == 'Agriculture' ? 'selected' : '' ?>>Agriculture</option>
                            <option value="Packaging" <?= $product['category'] == 'Packaging' ? 'selected' : '' ?>>Packaging</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-certificate"></i> Featured Status
                        </label>
                        <select name="featured" class="form-select">
                            <option value="none" <?= $product['featured'] == 'none' ? 'selected' : '' ?>>None</option>
                            <option value="new" <?= $product['featured'] == 'new' ? 'selected' : '' ?>>New Arrival</option>
                            <option value="trending" <?= $product['featured'] == 'trending' ? 'selected' : '' ?>>Trending</option>
                            <option value="best_seller" <?= $product['featured'] == 'best_seller' ? 'selected' : '' ?>>Best Seller</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-image"></i> Product Image
                    </label>
                    <?php if (!empty($product['image'])): ?>
                        <div class="image-preview" id="currentImagePreview">
                            <img src="<?= base_url('uploads/products/' . $product['image']) ?>" alt="Current Product Image">
                            <p>Current Image</p>
                        </div>
                    <?php endif; ?>
                    <input type="file" name="product_image" id="productImage" class="form-control" accept="image/jpeg,image/png,image/jpg">
                    <div class="info-text">
                        <i class="fas fa-info-circle"></i> Leave empty to keep current image. Max size: 2MB. Allowed formats: JPG, JPEG, PNG
                    </div>
                    <div class="image-preview" id="newImagePreview" style="display: none;">
                        <img id="previewImg" src="" alt="New Image Preview">
                        <p>New Image Preview</p>
                    </div>
                </div>

                <div class="form-buttons">
                    <button type="submit" class="btn btn-update">
                        <i class="fas fa-save"></i> Update Product
                    </button>
                    <a href="<?= base_url('/admin/inventory') ?>" class="btn btn-cancel">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>

            <!-- Delete Section -->
            <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid var(--accent-light);">
                <div class="form-header" style="margin-bottom: 15px;">
                    <i class="fas fa-trash-alt" style="color: var(--danger);"></i>
                    <h2 style="font-size: 1.2rem; color: var(--danger);">Danger Zone</h2>
                    <p style="font-size: 0.8rem;">This action cannot be undone</p>
                </div>
                <form action="<?= base_url('/admin/product/delete/' . $product['id']) ?>" method="post">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this product? This action cannot be undone and will remove all associated data.')">
                        <i class="fas fa-trash"></i> Delete Product Permanently
                    </button>
                </form>
            </div>
        </div>

        <footer>
            <p>&copy; 2026 CREATRIX. For educational purposes only, and no copyright infringement is intended.</p>
            <p>CREATRIX Group - Promoting Sustainable Living Through Coconut Coir Innovation</p>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Mobile menu toggle logic
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
        
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                closeMenu();
            }
        });
        
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    closeMenu();
                }
            });
        });
        
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
        
        // Preview image before upload
        const fileInput = document.getElementById('productImage');
        const currentPreview = document.getElementById('currentImagePreview');
        const newPreview = document.getElementById('newImagePreview');
        const previewImg = document.getElementById('previewImg');
        
        if (fileInput) {
            fileInput.addEventListener('change', function(e) {
                if (this.files && this.files[0]) {
                    const file = this.files[0];
                    const validTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                    
                    if (!validTypes.includes(file.type)) {
                        alert('Please upload a valid image file (JPG, JPEG, PNG)');
                        this.value = '';
                        if (newPreview) newPreview.style.display = 'none';
                        if (currentPreview) currentPreview.style.display = 'block';
                        return;
                    }
                    
                    if (file.size > 2 * 1024 * 1024) {
                        alert('File size must be less than 2MB');
                        this.value = '';
                        if (newPreview) newPreview.style.display = 'none';
                        if (currentPreview) currentPreview.style.display = 'block';
                        return;
                    }
                    
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        if (previewImg) {
                            previewImg.src = event.target.result;
                        }
                        if (newPreview) {
                            newPreview.style.display = 'block';
                        }
                        if (currentPreview) {
                            currentPreview.style.display = 'none';
                        }
                    }
                    reader.readAsDataURL(this.files[0]);
                } else {
                    if (newPreview) newPreview.style.display = 'none';
                    if (currentPreview) currentPreview.style.display = 'block';
                }
            });
        }
    </script>
</body>
</html>