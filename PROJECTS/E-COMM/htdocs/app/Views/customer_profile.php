<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | CREATRIX</title>
    
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

        /* HEADER - Same as storefront */
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
            max-width: 800px;
            margin: 40px auto;
            padding: 0 20px;
            flex: 1;
        }

        /* Profile Card */
        .profile-card {
            background: var(--card-bg);
            border-radius: 30px;
            padding: 40px;
            box-shadow: var(--shadow-md);
            border: 1px solid rgba(0,0,0,0.03);
        }

        .profile-header {
            display: flex;
            align-items: center;
            gap: 40px;
            margin-bottom: 40px;
            flex-wrap: wrap;
        }

        .profile-picture {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            overflow: hidden;
            border: 4px solid var(--accent);
            box-shadow: var(--shadow-md);
            background: var(--accent-light);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .profile-picture img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-info h2 {
            color: var(--primary);
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 12px;
        }

        .profile-info p {
            color: var(--text);
            margin: 10px 0;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.95rem;
        }

        .profile-info p i {
            color: var(--accent);
            width: 20px;
            font-size: 1rem;
        }

        .section-title {
            color: var(--primary);
            font-size: 1.3rem;
            font-weight: 600;
            margin: 35px 0 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid var(--accent-light);
            position: relative;
        }

        .section-title:first-of-type {
            margin-top: 0;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 60px;
            height: 2px;
            background: var(--accent);
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-label {
            font-weight: 500;
            display: block;
            margin-bottom: 8px;
            color: var(--text);
            font-size: 0.95rem;
        }

        .form-label i {
            color: var(--accent);
            margin-right: 8px;
            width: 20px;
        }

        .form-control {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid var(--accent-light);
            border-radius: 12px;
            font-size: 1rem;
            transition: var(--transition);
            font-family: 'Poppins', sans-serif;
            background: var(--white);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 4px rgba(42, 92, 55, 0.1);
        }

        .btn {
            padding: 14px 28px;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 1rem;
        }

        .btn-primary {
            background: var(--accent);
            color: white;
        }

        .btn-primary:hover {
            background: var(--accent-hover);
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }

        .btn-secondary {
            background: #E53E3E;
            color: white;
        }

        .btn-secondary:hover {
            background: #c53030;
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }

        .btn-upload {
            background: var(--primary-light);
            color: white;
        }

        .btn-upload:hover {
            background: var(--primary);
            transform: translateY(-2px);
        }

        .alert {
            padding: 16px 20px;
            border-radius: 15px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.95rem;
        }

        .alert-success {
            background: var(--accent-light);
            color: var(--accent);
            border: 1px solid var(--accent);
        }

        .alert-error {
            background: #FEE2E2;
            color: #E53E3E;
            border: 1px solid #FECACA;
        }

        .alert i {
            font-size: 1.2rem;
        }

        .image-preview {
            margin-top: 10px;
            display: none;
        }

        .image-preview img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--accent);
        }

        /* Footer */
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
            
            .profile-header {
                flex-direction: column;
                text-align: center;
                gap: 20px;
            }
            
            .profile-info p {
                justify-content: center;
            }
            
            .profile-card {
                padding: 25px;
            }
            
            .section-title::after {
                left: 50%;
                transform: translateX(-50%);
            }
            
            .section-title {
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
            <a href="<?= base_url('/transactions') ?>"><i class="fas fa-history"></i> My Orders</a>
            <a href="<?= base_url('/profile') ?>" class="active"><i class="fas fa-user"></i> <?= esc(session()->get('fullname')) ?></a>
            <a href="<?= base_url('/logout') ?>"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </header>

    <div class="container">
        <!-- Flash Messages -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>
        
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <div class="profile-card">
            <!-- Profile Header -->
            <div class="profile-header">
                <div class="profile-picture">
                    <?php if(isset($user['profile_picture']) && $user['profile_picture']): ?>
                        <img src="<?= base_url('uploads/profiles/' . $user['profile_picture']) ?>" alt="Profile Picture">
                    <?php else: ?>
                        <i class="fas fa-user" style="font-size: 4rem; color: var(--accent);"></i>
                    <?php endif; ?>
                </div>
                <div class="profile-info">
                    <h2><?= esc($user['fullname']) ?></h2>
                    <p><i class="fas fa-envelope"></i> <?= esc($user['email']) ?></p>
                    <p><i class="fas fa-phone"></i> <?= esc($user['mobile']) ?></p>
                    <p><i class="fas fa-map-marker-alt"></i> <?= esc($user['address']) ?></p>
                </div>
            </div>

            <!-- Update Profile Form -->
            <h3 class="section-title">Update Profile Information</h3>
            <form action="<?= base_url('/profile/update') ?>" method="post">
                <?= csrf_field() ?>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-user"></i> Full Name
                    </label>
                    <input type="text" name="fullname" class="form-control" value="<?= esc($user['fullname']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-map-marker-alt"></i> Address
                    </label>
                    <input type="text" name="address" class="form-control" value="<?= esc($user['address']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-phone"></i> Mobile Number
                    </label>
                    <input type="tel" name="mobile" class="form-control" value="<?= esc($user['mobile']) ?>" required>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Profile
                </button>
            </form>

            <!-- Update Profile Picture -->
            <h3 class="section-title">Update Profile Picture</h3>
            <form action="<?= base_url('/profile/upload-picture') ?>" method="post" enctype="multipart/form-data" id="uploadForm">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-image"></i> Choose Image
                    </label>
                    <input type="file" name="profile_picture" accept="image/*" class="form-control" id="profilePictureInput">
                    <div class="image-preview" id="imagePreview">
                        <img src="" alt="Preview">
                    </div>
                    <small class="text-muted" style="display: block; margin-top: 5px;">
                        <i class="fas fa-info-circle"></i> Max size: 2MB. Allowed formats: JPG, JPEG, PNG
                    </small>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-upload"></i> Upload Picture
                </button>
            </form>

            <?php if(isset($user['profile_picture']) && $user['profile_picture']): ?>
            <form action="<?= base_url('/profile/remove-picture') ?>" method="post" style="margin-top: 20px;">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-secondary" onclick="return confirm('Are you sure you want to remove your profile picture?')">
                    <i class="fas fa-trash"></i> Remove Picture
                </button>
            </form>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 CREATRIX. All rights reserved. | For educational purposes only, and no copyright infringement is intended.</p>
        <p style="margin-top: 10px; font-size: 0.8rem;">CREATRIX Group - Promoting Sustainable Living Through Coconut Coir Innovation</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Image preview functionality
        const profilePictureInput = document.getElementById('profilePictureInput');
        const imagePreview = document.getElementById('imagePreview');
        const previewImg = imagePreview.querySelector('img');

        if (profilePictureInput) {
            profilePictureInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        previewImg.src = event.target.result;
                        imagePreview.style.display = 'block';
                    }
                    reader.readAsDataURL(file);
                } else {
                    imagePreview.style.display = 'none';
                    previewImg.src = '';
                }
            });
        }
    </script>
</body>
</html>