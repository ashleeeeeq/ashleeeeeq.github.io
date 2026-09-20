<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | CREATRIX</title>
    
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


        .home-btn {
            background: var(--accent);
            color: var(--white);
            padding: 10px 30px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1rem;
            border: 2px solid var(--accent);
            box-shadow: 0 4px 15px rgba(42, 92, 55, 0.3);
        }

        .home-btn i {
            font-size: 1rem;
        }

        .home-btn:hover {
            background: var(--accent-hover);
            border-color: var(--accent-hover);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(42, 92, 55, 0.4);
        }

        .login-section {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px 20px;
        }

        .login-container {
            background: var(--card-bg);
            border-radius: 30px;
            padding: 50px;
            width: 100%;
            max-width: 480px;
            box-shadow: var(--shadow-md);
            animation: slideUp 0.5s ease;
            border: 1px solid rgba(0,0,0,0.03);
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-header {
            text-align: center;
            margin-bottom: 35px;
        }

        .login-header i {
            font-size: 3.5rem;
            color: var(--accent);
            margin-bottom: 15px;
        }

        .login-header h2 {
            font-weight: 700;
            color: var(--primary);
            font-size: 2.2rem;
            margin-bottom: 5px;
        }

        .login-header p {
            color: var(--text-light);
            font-size: 1rem;
        }

        .demo-credentials {
            background: var(--accent-light);
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 30px;
            border-left: 4px solid var(--accent);
        }

        .demo-credentials h5 {
            color: var(--primary);
            margin-bottom: 15px;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .demo-credentials p {
            margin: 8px 0;
            font-size: 0.95rem;
            color: var(--text);
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .demo-credentials .badge {
            background: var(--accent);
            color: var(--white);
            padding: 4px 12px;
            border-radius: 25px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
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
        }

        .password-wrapper {
            position: relative;
            width: 100%;
        }

        .form-control {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid var(--accent-light);
            border-radius: 15px;
            font-size: 1rem;
            transition: var(--transition);
            font-family: 'Poppins', sans-serif;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 4px rgba(42, 92, 55, 0.1);
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-light);
            cursor: pointer;
            font-size: 1.1rem;
            transition: var(--transition);
        }

        .toggle-password:hover {
            color: var(--accent);
        }

        .btn-login {
            width: 100%;
            background: var(--accent);
            color: var(--white);
            padding: 16px;
            border: none;
            border-radius: 15px;
            font-weight: 600;
            font-size: 1.1rem;
            margin-top: 15px;
            transition: var(--transition);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-login:hover {
            background: var(--accent-hover);
            transform: translateY(-3px);
            box-shadow: var(--shadow-hover);
        }

        .register-link {
            text-align: center;
            margin-top: 25px;
            padding-top: 25px;
            border-top: 2px solid var(--accent-light);
        }

        .register-link a {
            color: var(--accent);
            font-weight: 600;
            text-decoration: none;
            transition: var(--transition);
        }

        .register-link a:hover {
            color: var(--primary);
            text-decoration: underline;
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

        .alert-danger {
            background: #FEE;
            color: #E53E3E;
            border: 1px solid #FCD;
        }

        .alert-success {
            background: var(--accent-light);
            color: var(--accent);
            border: 1px solid var(--accent);
        }

        .alert-warning {
            background: #FFF3E0;
            color: #B76E0E;
            border: 1px solid #FFE5B4;
        }

        .alert i {
            font-size: 1.2rem;
        }

        footer {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: var(--white);
            padding: 40px 5% 30px;
            text-align: center;
        }

        footer p {
            margin: 5px 0;
            opacity: 0.9;
            font-size: 0.95rem;
        }

        @media (max-width: 768px) {
            header {
                flex-direction: column;
                gap: 15px;
                padding: 20px 5%;
            }
            
            .home-btn {
                padding: 8px 25px;
                font-size: 0.95rem;
            }
            
            .login-container {
                padding: 30px 20px;
            }
            
            .login-header h2 {
                font-size: 1.8rem;
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
        
        <a href="<?= base_url('/') ?>" class="home-btn">
            <i class="fas fa-home"></i> Home
        </a>
    </header>

    <div class="login-section">
        <div class="login-container">
            <div class="login-header">
                <h2>Welcome Back</h2>
                <p>Sign in to your CREATRIX COIR account</p>
            </div>


            <!-- DISPLAY ERROR MESSAGE - THIS IS WHERE THE ERROR SHOWS -->
            <?php if (isset($error) && !empty($error)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <!-- Display Session Flash Messages -->
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('warning')): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?= session()->getFlashdata('warning') ?>
                </div>
            <?php endif; ?>

            <!-- Display Validation Errors -->
            <?php if (isset($validation) && $validation->getErrors()): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <div>
                        <?php foreach ($validation->getErrors() as $error): ?>
                            <?= $error ?><br>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Display Unverified Email Message -->
            <?php if (isset($unverified_email) && $unverified_email): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-envelope"></i>
                    <div style="flex: 1;">
                        <strong>Email not verified!</strong><br>
                        Please verify your email address to log in. Check your inbox for the verification link.
                        <form method="POST" action="<?= base_url('/resend-verification') ?>" class="resend-form" style="margin-top: 10px;">
                            <?= csrf_field() ?>
                            <input type="hidden" name="email" value="<?= $unverified_email ?>">
                            <button type="submit" class="resend-btn">
                                <i class="fas fa-paper-plane"></i> Click here to resend verification email
                            </button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form action="<?= base_url('/login') ?>" method="post" id="loginForm">
                <?= csrf_field() ?>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-envelope"></i>Email Address
                    </label>
                    <input type="email" name="email" class="form-control" 
                           placeholder="Enter your email" value="<?= old('email') ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-lock"></i>Password
                    </label>
                    <div class="password-wrapper">
                        <input type="password" name="password" id="password" class="form-control" 
                               placeholder="Enter your password" required>
                        <button type="button" class="toggle-password" onclick="togglePassword()">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> Sign In
                </button>
            </form>

            <div class="register-link">
                Don't have a customer account? <a href="<?= base_url('/register') ?>">Register here</a>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 CREATRIX. All rights reserved. | For educational purposes only, and no copyright infringement is intended.</p>
        <p style="margin-top: 10px; font-size: 0.8rem;">CREATRIX Group - Promoting Sustainable Living Through Coconut Coir Innovation</p>
    </footer>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleBtn = document.querySelector('.toggle-password i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleBtn.classList.remove('fa-eye');
                toggleBtn.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleBtn.classList.remove('fa-eye-slash');
                toggleBtn.classList.add('fa-eye');
            }
        }

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.remove();
                }, 500);
            });
        }, 5000);
        
        // Add loading effect on form submit
        const loginForm = document.getElementById('loginForm');
        if (loginForm) {
            loginForm.addEventListener('submit', function() {
                const submitBtn = this.querySelector('.btn-login');
                if (submitBtn) {
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing in...';
                    submitBtn.disabled = true;
                }
            });
        }
    </script>
</body>
</html>