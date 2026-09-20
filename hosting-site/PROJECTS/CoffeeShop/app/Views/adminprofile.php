<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kape-Con • Admin Profile</title>

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
            position: relative;
            min-height: 100vh;
        }

        /* Animated Background */
        body::before {
            content: '';
            position: fixed;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle at 50% 50%, rgba(212, 165, 116, 0.1) 0%, transparent 50%);
            animation: rotate 30s linear infinite;
            pointer-events: none;
            z-index: 0;
        }
        
        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Navbar */
        .navbar {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95) !important;
            box-shadow: 0 2px 20px rgba(0,0,0,0.08);
            padding: 1rem 0;
            transition: all 0.3s ease;
            position: relative;
            z-index: 10;
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

        .nav-link {
            color: var(--black) !important;
            font-weight: 500;
            margin: 0 0.5rem;
        }

        /* Profile Container */
        .profile-section {
            min-height: calc(100vh - 200px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 4rem 1rem;
            position: relative;
            z-index: 1;
        }

        .profile-card {
            max-width: 650px;
            width: 100%;
            background: rgba(255, 255, 255, 0.95);
            padding: 50px;
            border-radius: 25px;
            box-shadow: 0 20px 60px rgba(139, 69, 19, 0.15);
            animation: fadeIn 0.6s ease-out;
            backdrop-filter: blur(20px);
        }

        @keyframes fadeIn {
            from { 
                opacity: 0; 
                transform: translateY(30px) scale(0.95);
            }
            to { 
                opacity: 1; 
                transform: translateY(0) scale(1);
            }
        }

        /* Header */
        .profile-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .coffee-icon {
            font-size: 3rem;
            display: inline-block;
            animation: float 3s ease-in-out infinite;
            filter: drop-shadow(0 10px 30px rgba(139, 69, 19, 0.3));
            margin-bottom: 1rem;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-15px) rotate(5deg); }
        }

        .profile-title {
            font-size: 2rem;
            font-weight: 800;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .profile-title i {
            font-size: 2rem;
        }

        .profile-subtitle {
            font-size: 0.95rem;
            color: #666;
            font-weight: 500;
        }

        /* Profile Picture */
        .profile-pic-wrapper {
            text-align: center;
            margin-bottom: 2rem;
        }

        .profile-pic {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 5px solid var(--accent-color);
            box-shadow: 0 10px 30px rgba(139, 69, 19, 0.2);
            transition: transform 0.3s ease;
        }

        .profile-pic:hover {
            transform: scale(1.05);
        }

        /* Alert */
        .alert {
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            font-weight: 500;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
        }

        .alert i {
            font-size: 1.3rem;
        }

        /* Form Groups */
        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-label {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--black);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-label i {
            color: var(--primary-color);
            font-size: 1.2rem;
        }

        .input-icon {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--grey);
            font-size: 1.3rem;
            transition: color 0.3s ease;
            pointer-events: none;
        }

        .form-control {
            background: white;
            border: 2px solid #e0e0e0;
            border-radius: 15px;
            padding: 16px 20px 16px 55px;
            font-size: 1rem;
            color: var(--black);
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
            font-weight: 500;
        }

        .form-control.no-icon {
            padding-left: 20px;
        }

        .form-control:focus {
            background: white;
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 4px rgba(139, 69, 19, 0.1);
            transform: translateY(-2px);
        }

        .form-control:focus + .input-icon {
            color: var(--primary-color);
        }

        .form-control:hover {
            border-color: var(--accent-color);
        }

        .form-control[readonly] {
            background: var(--light-bg);
            cursor: not-allowed;
        }

        .role-badge {
            display: inline-block;
            background: rgba(139, 69, 19, 0.1);
            color: var(--primary-color);
            padding: 12px 20px;
            border-radius: 12px;
            font-weight: 600;
            border: 2px solid rgba(139, 69, 19, 0.3);
            text-transform: capitalize;
            font-size: 1rem;
            width: 100%;
            text-align: center;
        }

        /* Buttons */
        .btn-save {
            width: 100%;
            padding: 18px 30px;
            border-radius: 50px;
            border: none;
            font-size: 1rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            cursor: pointer;
            background: var(--primary-color);
            color: white;
            font-family: 'Poppins', sans-serif;
            box-shadow: 0 8px 20px rgba(139, 69, 19, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 2rem;
        }

        .btn-save i {
            font-size: 1.2rem;
            transition: transform 0.3s ease;
        }

        .btn-save:hover {
            background: var(--primary-dark);
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(139, 69, 19, 0.4);
        }

        .btn-save:hover i {
            transform: scale(1.1);
        }

        .btn-back {
            width: 100%;
            padding: 16px 30px;
            border-radius: 50px;
            border: 2px solid #e0e0e0;
            font-size: 0.95rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            cursor: pointer;
            background: white;
            color: var(--black);
            font-family: 'Poppins', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 1rem;
            text-decoration: none;
        }

        .btn-back:hover {
            background: var(--light-bg);
            border-color: var(--accent-color);
            color: var(--black);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }

        .btn-back i {
            transition: transform 0.3s ease;
        }

        .btn-back:hover i {
            transform: translateX(-3px);
        }

        /* Footer */
        footer {
            background: var(--black);
            color: var(--white);
            padding: 2rem 0;
            margin-top: 4rem;
            position: relative;
            z-index: 1;
        }

        footer p {
            margin: 0;
            opacity: 0.8;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .profile-card {
                padding: 40px 30px;
            }

            .profile-title {
                font-size: 1.6rem;
            }

            .profile-pic {
                width: 120px;
                height: 120px;
            }

            .coffee-icon {
                font-size: 2.5rem;
            }
        }
    </style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand" href="<?= base_url('/admin') ?>">
            ☕ Kape-Con
            <span class="admin-badge">ADMIN</span>
        </a>
        <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="<?= base_url('/admin') ?>">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= base_url('/logout') ?>">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- PROFILE SECTION -->
<div class="profile-section">
    <div class="profile-card">

        <div class="profile-header">
            <div class="coffee-icon">☕</div>
            <h2 class="profile-title">
                <i class='bx bxs-user-circle'></i>
                Admin Profile
            </h2>
            <p class="profile-subtitle">Manage your administrator account</p>
        </div>

        <div class="profile-pic-wrapper">
            <img src="<?= base_url('admin/profile/picture/' . $user['profile_pic']) ?>"
                 onerror="this.src='<?= base_url('img/default.jpg') ?>'"
                 class="profile-pic"
                 alt="Admin Profile Picture"
                 id="profilePreview">
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success">
                <i class='bx bx-check-circle'></i>
                <span><?= session()->getFlashdata('success') ?></span>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('/admin/profile/update') ?>" method="post" enctype="multipart/form-data">

            <!-- NAME -->
            <div class="form-group">
                <label class="form-label">
                    <i class='bx bx-user'></i>
                    Full Name
                </label>    
                <input type="text" name="name" class="form-control" value="<?= $user['name'] ?>" required>
            </div>

            <!-- EMAIL -->
            <div class="form-group">
                <label class="form-label">
                    <i class='bx bx-envelope'></i>
                    Email Address
                </label>
                <input type="email" name="email" class="form-control" value="<?= $user['email'] ?>" required>
            </div>

            <!-- PROFILE PIC -->
            <div class="form-group">
                <label class="form-label">
                    <i class='bx bx-image'></i>
                    Profile Picture (optional)
                </label>
                <input type="file" name="profile_pic" accept="image/*" class="form-control no-icon" onchange="previewImage(event)">
            </div>

            <!-- ROLE (READ ONLY) -->
            <div class="form-group">
                <label class="form-label">
                    <i class='bx bx-shield'></i>
                    Account Role
                </label>
                <div class="role-badge"><?= $user['role'] ?></div>
            </div>

            <button type="submit" class="btn-save">
                <i class='bx bx-save'></i>
                Save Changes
            </button>

            <a href="<?= base_url('/admin') ?>" class="btn-back">
                <i class='bx bx-left-arrow-alt'></i>
                Back to Dashboard
            </a>

        </form>

    </div>
</div>

<!-- FOOTER -->
<footer>
    <div class="container text-center">
        <p>© <?= date('Y') ?> Kape-Con Coffee Shop. All rights reserved.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function() {
        document.getElementById('profilePreview').src = reader.result;
    }
    if (event.target.files[0]) {
        reader.readAsDataURL(event.target.files[0]);
    }
}
</script>

</body>
</html>