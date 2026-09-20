<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Edit User | CREATRIX Admin</title>
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
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            font-weight: 500;
            display: block;
            margin-bottom: 8px;
            color: var(--text);
        }

        .form-control,
        .form-select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid var(--accent-light);
            border-radius: 10px;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s;
        }

        .form-control:focus,
        .form-select:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(42, 92, 55, 0.1);
        }

        .btn-primary {
            background: var(--accent);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 0.9rem;
        }

        .btn-primary:hover {
            background: var(--accent-hover);
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }

        .btn-danger {
            background: var(--danger);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 0.9rem;
        }

        .btn-danger:hover {
            background: #c53030;
            transform: translateY(-2px);
        }

        .btn-outline {
            background: transparent;
            border: 2px solid var(--accent);
            color: var(--accent);
            padding: 10px 20px;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-outline:hover {
            background: var(--accent);
            color: white;
        }

        .profile-picture {
            display: flex;
            align-items: center;
            gap: 25px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .profile-preview {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            overflow: hidden;
            border: 3px solid var(--accent);
            background: var(--accent-light);
            flex-shrink: 0;
        }

        .profile-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-actions {
            flex: 1;
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
        }

        .profile-actions form {
            margin: 0;
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
            background: #FEE2E2;
            color: var(--danger);
            border: 1px solid #FECACA;
        }

        .text-muted {
            color: var(--text-light);
            font-size: 0.75rem;
            margin-top: 5px;
            display: block;
        }

        .text-muted i {
            margin-right: 5px;
        }

        .form-actions {
            margin-top: 30px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .form-actions button {
            flex: 1;
            min-width: 150px;
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

            .header {
                flex-direction: column;
                text-align: center;
            }

            .header h1 {
                font-size: 1.5rem;
            }

            .btn-back {
                width: 100%;
                justify-content: center;
                white-space: normal;
            }

            .form-card {
                padding: 20px;
            }

            .profile-picture {
                flex-direction: column;
                text-align: center;
                align-items: center;
            }

            .profile-actions {
                flex-direction: column;
                width: 100%;
                align-items: stretch;
            }

            .profile-actions form {
                width: 100%;
            }

            .profile-actions .btn-primary,
            .profile-actions .btn-danger,
            .profile-actions .btn-outline {
                width: 100%;
                justify-content: center;
                padding: 12px;
            }

            .form-actions {
                flex-direction: column;
            }
            
            .form-actions button {
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .main-content {
                padding: 70px 15px 15px;
            }
            
            .header h1 {
                font-size: 1.3rem;
            }
            
            .form-control, .form-select {
                padding: 10px 12px;
                font-size: 0.85rem;
            }
            
            .btn-primary, .btn-danger {
                padding: 10px 16px;
                font-size: 0.85rem;
            }
            
            .profile-preview {
                width: 80px;
                height: 80px;
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
            <a class="nav-link" href="<?= base_url('/admin/orders') ?>"><i class="fas fa-shopping-cart"></i> Orders</a>
            <hr style="border-color: rgba(255,255,255,0.2);">
            <a class="nav-link" href="<?= base_url('/logout') ?>"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </div>

    <div class="main-content">
        <div class="header">
            <h1><i class="fas fa-user-edit"></i> Edit User</h1>
            <a href="<?= base_url('/admin/dashboard') ?>" class="btn-back">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
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

        <?php if (isset($errors) && !empty($errors)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <ul style="margin: 0; padding-left: 20px;">
                    <?php foreach ($errors as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Profile Picture Section -->
        <div class="form-card">
            <h3 style="margin-bottom: 20px; color: var(--primary);"><i class="fas fa-camera"></i> Profile Picture</h3>
            <div class="profile-picture">
                <div class="profile-preview">
                    <?php if (!empty($user['profile_picture'])): ?>
                        <img src="<?= base_url('uploads/profiles/' . $user['profile_picture']) ?>" alt="Profile Picture">
                    <?php else: ?>
                        <img src="https://via.placeholder.com/100x100/2A5C37/ffffff?text=User" alt="Default Profile">
                    <?php endif; ?>
                </div>
                <div class="profile-actions">
                    <form action="<?= base_url('/admin/users/upload-picture/' . $user['id']) ?>" method="post" enctype="multipart/form-data" id="uploadForm">
                        <?= csrf_field() ?>
                        <input type="file" name="profile_picture" id="profile_picture" style="display: none;" accept="image/jpeg,image/png,image/jpg">
                        <button type="button" class="btn-primary" onclick="document.getElementById('profile_picture').click();">
                            <i class="fas fa-upload"></i> Choose Picture
                        </button>
                        <button type="submit" class="btn-primary" id="savePictureBtn" style="display: none;">
                            <i class="fas fa-save"></i> Save Picture
                        </button>
                    </form>
                    <?php if (!empty($user['profile_picture'])): ?>
                        <form action="<?= base_url('/admin/users/remove-picture/' . $user['id']) ?>" method="post">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn-danger" onclick="return confirm('Remove profile picture?')">
                                <i class="fas fa-trash"></i> Remove Picture
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
            <small class="text-muted">
                <i class="fas fa-info-circle"></i> Allowed formats: JPG, JPEG, PNG. Max size: 2MB
            </small>
        </div>

        <!-- User Information Form -->
        <div class="form-card">
            <h3 style="margin-bottom: 20px; color: var(--primary);"><i class="fas fa-user-circle"></i> User Information</h3>
            <form action="<?= base_url('/admin/users/update/' . $user['id']) ?>" method="post">
                <?= csrf_field() ?>
                
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Full Name *</label>
                    <input type="text" name="fullname" class="form-control" value="<?= esc($user['fullname']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-envelope"></i> Email *</label>
                    <input type="email" name="email" class="form-control" value="<?= esc($user['email']) ?>" required>
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i> Leave as is to keep current email
                    </small>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current password">
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i> Only fill this field if you want to change the password
                    </small>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-phone"></i> Mobile Number *</label>
                    <input type="text" name="mobile" class="form-control" value="<?= esc($user['mobile']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-map-marker-alt"></i> Address *</label>
                    <textarea name="address" class="form-control" rows="3" required><?= esc($user['address']) ?></textarea>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-badge"></i> Role</label>
                    <select name="role" class="form-select">
                        <option value="customer" <?= $user['role'] == 'customer' ? 'selected' : '' ?>>Customer</option>
                        <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                    </select>
                </div>

                <!-- Email Verification Status -->
                <div class="form-group">
                    <label><i class="fas fa-check-circle"></i> Email Verification Status</label>
                    <select name="is_verified" class="form-select">
                        <option value="0" <?= (isset($user['is_verified']) && $user['is_verified'] == 0) ? 'selected' : '' ?>>Pending (Requires Email Verification)</option>
                        <option value="1" <?= (isset($user['is_verified']) && $user['is_verified'] == 1) ? 'selected' : '' ?>>Verified (Can Login Immediately)</option>
                    </select>
                    <?php if (isset($user['verified_at']) && $user['verified_at']): ?>
                        <small class="text-muted">
                            <i class="fas fa-check-circle"></i> Verified on: <?= date('F d, Y h:i A', strtotime($user['verified_at'])) ?>
                        </small>
                    <?php endif; ?>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i> Update User
                    </button>
                    <a href="<?= base_url('/admin/dashboard') ?>" class="btn-outline" style="text-decoration: none; text-align: center;">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
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
        
        // Initialize sidebar state
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
        
        // Profile picture upload - show save button after file selection
        const profilePictureInput = document.getElementById('profile_picture');
        const savePictureBtn = document.getElementById('savePictureBtn');
        
        if (profilePictureInput) {
            profilePictureInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const file = this.files[0];
                    const validTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                    
                    if (!validTypes.includes(file.type)) {
                        alert('Please upload a valid image file (JPG, JPEG, PNG)');
                        this.value = '';
                        if (savePictureBtn) savePictureBtn.style.display = 'none';
                        return;
                    }
                    
                    if (file.size > 2 * 1024 * 1024) {
                        alert('File size must be less than 2MB');
                        this.value = '';
                        if (savePictureBtn) savePictureBtn.style.display = 'none';
                        return;
                    }
                    
                    if (savePictureBtn) {
                        savePictureBtn.style.display = 'inline-flex';
                    }
                } else {
                    if (savePictureBtn) savePictureBtn.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>