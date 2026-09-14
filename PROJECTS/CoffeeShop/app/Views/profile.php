<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile • Kape-Con</title>

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

        .nav-link.text-danger {
            color: #dc3545 !important;
        }

        .nav-link.text-danger:hover {
            color: #c82333 !important;
        }

        /* Profile Card */
        .profile-section {
            min-height: calc(100vh - 200px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 4rem 1rem;
        }

        .profile-card {
            max-width: 700px;
            width: 100%;
            background: white;
            padding: 50px;
            border-radius: 25px;
            box-shadow: 0 20px 60px rgba(139, 69, 19, 0.15);
            animation: fadeIn 0.6s ease-out;
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

        .profile-pic-wrapper {
            text-align: center;
            margin-bottom: 2rem;
            position: relative;
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

        .profile-title {
            text-align: center;
            font-weight: 800;
            color: var(--primary-color);
            margin: 1.5rem 0 2rem;
            font-size: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .profile-title i {
            font-size: 2rem;
        }

        .info-group {
            margin-bottom: 1.5rem;
        }

        .info-label {
            font-weight: 600;
            color: var(--black);
            margin-bottom: 8px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-label i {
            color: var(--primary-color);
            font-size: 1.2rem;
        }

        .info-value {
            padding: 16px 20px;
            font-size: 1.05rem;
            background: var(--light-bg);
            border-radius: 12px;
            border: 2px solid #e0e0e0;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .info-value:hover {
            border-color: var(--accent-color);
        }

        .role-badge {
            display: inline-block;
            background: rgba(139, 69, 19, 0.1);
            color: var(--primary-color);
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            border: 1px solid rgba(139, 69, 19, 0.3);
            text-transform: capitalize;
        }

        .btn-edit {
            width: 100%;
            background: var(--accent-color);
            color: var(--black);
            padding: 16px;
            border: none;
            border-radius: 50px;
            margin-top: 2rem;
            font-weight: 600;
            font-size: 1rem;
            box-shadow: 0 8px 20px rgba(212, 165, 116, 0.3);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-edit:hover {
            background: #c49563;
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(212, 165, 116, 0.4);
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

        /* Modal Styling */
        .modal-content {
            border-radius: 20px;
            border: none;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        }

        .modal-header {
            border-bottom: 1px solid rgba(139, 69, 19, 0.1);
            padding: 1.5rem 2rem;
            background: var(--light-bg);
            border-radius: 20px 20px 0 0;
        }

        .modal-title {
            font-weight: 700;
            color: var(--primary-color);
            font-size: 1.5rem;
        }

        .modal-body {
            padding: 2rem;
        }

        .modal-footer {
            border-top: 1px solid rgba(139, 69, 19, 0.1);
            padding: 1.5rem 2rem;
            background: var(--light-bg);
            border-radius: 0 0 20px 20px;
        }

        .preview-img-wrapper {
            text-align: center;
            margin-bottom: 2rem;
        }

        .preview-img {
            width: 140px;
            height: 140px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid var(--accent-color);
            box-shadow: 0 8px 20px rgba(139, 69, 19, 0.15);
        }

        .form-label {
            font-weight: 600;
            color: var(--black);
            margin-bottom: 8px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-label i {
            color: var(--primary-color);
        }

        .form-control {
            background: white;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            padding: 14px 18px;
            font-size: 1rem;
            color: var(--black);
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
            font-weight: 500;
        }

        .form-control:focus {
            background: white;
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 4px rgba(139, 69, 19, 0.1);
        }

        .form-control:hover {
            border-color: var(--accent-color);
        }

        .form-control[readonly] {
            background: var(--light-bg);
            cursor: not-allowed;
        }

        .text-danger {
            color: #dc3545;
            font-size: 0.85rem;
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .text-danger i {
            font-size: 1rem;
        }

        .btn-secondary {
            background: white;
            border: 2px solid #e0e0e0;
            color: var(--black);
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: var(--light-bg);
            border-color: var(--accent-color);
            color: var(--black);
        }

        .btn-primary {
            background: var(--primary-color);
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            box-shadow: 0 8px 20px rgba(139, 69, 19, 0.3);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(139, 69, 19, 0.4);
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
            .profile-card {
                padding: 30px 25px;
            }

            .profile-title {
                font-size: 1.6rem;
            }

            .profile-pic {
                width: 120px;
                height: 120px;
            }
        }
    </style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg sticky-top">
  <div class="container">
    <a class="navbar-brand" href="<?= base_url('homepage') ?>">☕ Kape-Con</a>

    <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navMenu">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="<?= base_url('homepage') ?>#hero">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= base_url('homepage') ?>#collections">Menu</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= base_url('homepage') ?>#products">Featured</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= base_url('homepage') ?>#news">News</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= base_url('/order') ?>">Order</a></li>
        <li class="nav-item"><a class="nav-link active" href="<?= base_url('/profile') ?>">Profile</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- PROFILE SECTION -->
<div class="profile-section">
    <div class="profile-card">
        <div class="profile-pic-wrapper">
            <img class="profile-pic"
                src="<?= $user['profile_pic'] 
                ? base_url('profile/pic/' . $user['profile_pic'])
                : base_url('img/default.jpg') ?>"
                alt="Profile Picture">
        </div>

        <h2 class="profile-title">
            <i class='bx bxs-user-circle'></i>
            My Profile
        </h2>

        <div class="info-group">
            <div class="info-label">
                <i class='bx bx-user'></i>
                Full Name
            </div>
            <div class="info-value"><?= $user['name'] ?></div>
        </div>

        <div class="info-group">
            <div class="info-label">
                <i class='bx bx-envelope'></i>
                Email Address
            </div>
            <div class="info-value"><?= $user['email'] ?></div>
        </div>

        <div class="info-group">
            <div class="info-label">
                <i class='bx bx-shield'></i>
                Account Role
            </div>
            <div>
                <span class="role-badge"><?= $user['role'] ?></span>
            </div>
        </div>

        <button class="btn btn-edit" data-bs-toggle="modal" data-bs-target="#editProfileModal">
            <i class='bx bx-edit'></i>
            Edit Profile
        </button>

        <a href="<?= base_url('/logout') ?>">
            <button class="btn-logout">
                <i class='bx bx-log-out'></i>
                Logout
            </button>
        </a>
    </div>
</div>

<!-- EDIT PROFILE MODAL -->
<div class="modal fade" id="editProfileModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

    <form action="<?= base_url('profile/update') ?>" method="post" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title">
              <i class='bx bx-edit'></i> Edit Profile
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">

          <!-- PROFILE IMAGE PREVIEW -->
          <div class="preview-img-wrapper">
            <img id="previewImg"
                src="<?= $user['profile_pic'] 
                        ? base_url('profile/pic/' . $user['profile_pic']) 
                        : base_url('img/default.jpg') ?>"
                class="preview-img"
                alt="Profile Preview">
          </div>

          <div class="mb-3">
              <label class="form-label">
                  <i class='bx bx-image'></i>
                  Change Profile Picture (optional)
              </label>
              <input type="file" class="form-control" name="profile_pic" accept="image/*"
                    onchange="previewImage(event)">
          </div>

          <div class="mb-3">
              <label class="form-label">
                  <i class='bx bx-user'></i>
                  Full Name
              </label>
              <input type="text" class="form-control" name="name" value="<?= $user['name'] ?>" required>
          </div>

          <div class="mb-3">
              <label class="form-label">
                  <i class='bx bx-envelope'></i>
                  Email Address
              </label>
              <input type="email" class="form-control" name="email" value="<?= $user['email'] ?>" required>
          </div>

          <div class="mb-3">
              <label class="form-label">
                  <i class='bx bx-shield'></i>
                  Role
              </label>
              <input type="text" class="form-control" value="<?= $user['role'] ?>" readonly>
              <small class="text-danger">
                  <i class='bx bx-info-circle'></i>
                  Role cannot be changed by users
              </small>
          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
              Cancel
          </button>
          <button type="submit" class="btn btn-primary">
              <i class='bx bx-save'></i> Save Changes
          </button>
        </div>

      </form>
    </div>
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
        document.getElementById('previewImg').src = reader.result;
    }
    reader.readAsDataURL(event.target.files[0]);
}
</script>

</body>
</html>