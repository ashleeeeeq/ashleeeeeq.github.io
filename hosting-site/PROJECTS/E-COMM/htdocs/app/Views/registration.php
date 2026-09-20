<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | CREATRIX</title>
    
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
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            --danger: #E53E3E;
            --success: #2A5C37;
        }

        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg);
            color: var(--text);
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

        .register-section { 
            flex: 1; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            padding: 60px 20px; 
        }

        .register-container {
            background: var(--card-bg);
            border-radius: 30px;
            padding: 50px;
            width: 100%;
            max-width: 720px;
            box-shadow: var(--shadow-md);
            animation: slideUp 0.5s ease;
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

        .register-header { 
            text-align: center; 
            margin-bottom: 35px; 
        }

        .register-header i {
            font-size: 3.5rem;
            color: var(--accent);
            margin-bottom: 15px;
        }

        .register-header h2 { 
            font-weight: 700; 
            color: var(--primary); 
            font-size: 2.2rem; 
            margin-bottom: 5px; 
        }

        .register-header p { 
            color: var(--text-light); 
            font-size: 1rem; 
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
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
            font-size: 0.9rem;
        }

        .form-label i {
            color: var(--accent);
            margin-right: 8px;
            width: 20px;
        }

        .password-wrapper {
            position: relative;
            width: 100%;
        }

        .form-control {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid var(--accent-light);
            border-radius: 12px;
            font-size: 0.95rem;
            transition: var(--transition);
            font-family: 'Poppins', sans-serif;
            background: var(--white);
        }

        .form-control:focus { 
            outline: none; 
            border-color: var(--accent); 
            box-shadow: 0 0 0 3px rgba(42, 92, 55, 0.1);
        }

        .form-control.is-invalid {
            border-color: var(--danger);
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

        .invalid-feedback {
            color: var(--danger);
            font-size: 0.75rem;
            margin-top: 5px;
            display: block;
        }

        .btn-register {
            width: 100%;
            background: var(--accent);
            color: var(--white);
            padding: 14px;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            margin-top: 20px;
            transition: var(--transition);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-register:hover { 
            background: var(--accent-hover); 
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }

        .login-link { 
            text-align: center; 
            margin-top: 25px; 
            padding-top: 25px;
            border-top: 2px solid var(--accent-light);
        }

        .login-link a { 
            color: var(--accent); 
            font-weight: 600; 
            text-decoration: none;
            transition: var(--transition);
        }

        .login-link a:hover { 
            color: var(--primary);
            text-decoration: underline; 
        }

        .alert {
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.9rem;
        }

        .alert-danger {
            background: #FFEBEE;
            color: var(--danger);
            border: 1px solid #FFCDD2;
        }

        .alert-success {
            background: var(--accent-light);
            color: var(--accent);
            border: 1px solid var(--accent);
        }

        .alert i {
            font-size: 1.1rem;
        }

        .info-box {
            background: var(--accent-light);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 15px;
            border-left: 4px solid var(--accent);
        }

        .info-box i {
            font-size: 1.6rem;
            color: var(--accent);
        }

        .info-box p {
            color: var(--text);
            font-size: 0.9rem;
            margin: 0;
            line-height: 1.5;
        }

        .password-strength {
            margin-top: 8px;
            font-size: 0.7rem;
        }

        .strength-bar {
            height: 4px;
            background: var(--accent-light);
            border-radius: 2px;
            margin-top: 8px;
            transition: all 0.3s;
        }

        .strength-bar.weak { width: 25%; background: var(--danger); }
        .strength-bar.medium { width: 50%; background: var(--warning); }
        .strength-bar.strong { width: 75%; background: var(--accent); }
        .strength-bar.very-strong { width: 100%; background: var(--success); }

        footer {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: var(--white);
            padding: 40px 5% 30px;
            margin-top: auto;
            text-align: center;
        }

        footer p {
            margin: 5px 0;
            opacity: 0.9;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            header {
                flex-direction: column;
                gap: 15px;
                padding: 20px 5%;
            }
            
            .home-btn {
                padding: 8px 25px;
                font-size: 0.9rem;
            }
            
            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }
            
            .register-container {
                padding: 30px 20px;
            }
            
            .register-header h2 {
                font-size: 1.8rem;
            }
            
            .info-box {
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
        
        <a href="<?= base_url('/') ?>" class="home-btn">
            <i class="fas fa-home"></i> Home
        </a>
    </header>

    <section class="register-section">
        <div class="register-container">
            <div class="register-header">
                <i class="fas fa-user-plus"></i>
                <h2>Create Account</h2>
                <p>Join CREATRIX COIR and start shopping for eco-friendly products</p>
            </div>

            <div class="info-box">
                <i class="fas fa-info-circle"></i>
                <p>This registration is for customers only. Administrators are pre-registered. If you need assistance, please contact our support team.</p>
            </div>

            <?php if (isset($validation)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= $validation->listErrors() ?>
                </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('/register') ?>" method="post" id="registerForm">
                <?= csrf_field() ?>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-user"></i> Full Name
                        </label>
                        <input type="text" name="fullname" class="form-control <?= (isset($validation) && $validation->hasError('fullname')) ? 'is-invalid' : '' ?>" 
                               value="<?= old('fullname') ?>" placeholder="Enter your full name" required>
                        <?php if (isset($validation) && $validation->hasError('fullname')): ?>
                            <div class="invalid-feedback"><?= $validation->getError('fullname') ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-envelope"></i> Email Address
                        </label>
                        <input type="email" name="email" class="form-control <?= (isset($validation) && $validation->hasError('email')) ? 'is-invalid' : '' ?>" 
                               value="<?= old('email') ?>" placeholder="Enter your email" required>
                        <?php if (isset($validation) && $validation->hasError('email')): ?>
                            <div class="invalid-feedback"><?= $validation->getError('email') ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-map-marker-alt"></i> Complete Address
                    </label>
                    <input type="text" name="address" class="form-control <?= (isset($validation) && $validation->hasError('address')) ? 'is-invalid' : '' ?>" 
                           value="<?= old('address') ?>" placeholder="Street, City, Province, Postal Code" required>
                    <?php if (isset($validation) && $validation->hasError('address')): ?>
                        <div class="invalid-feedback"><?= $validation->getError('address') ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-phone"></i> Mobile Number
                    </label>
                    <input type="tel" name="mobile" class="form-control <?= (isset($validation) && $validation->hasError('mobile')) ? 'is-invalid' : '' ?>" 
                           value="<?= old('mobile') ?>" placeholder="09XXXXXXXXX" required>
                    <?php if (isset($validation) && $validation->hasError('mobile')): ?>
                        <div class="invalid-feedback"><?= $validation->getError('mobile') ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-lock"></i> Password
                        </label>
                        <div class="password-wrapper">
                            <input type="password" name="password" id="password" class="form-control <?= (isset($validation) && $validation->hasError('password')) ? 'is-invalid' : '' ?>" 
                                   placeholder="Minimum 6 characters" required>
                            <button type="button" class="toggle-password" onclick="togglePassword('password')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="password-strength" id="passwordStrength"></div>
                        <div class="strength-bar" id="strengthBar"></div>
                        <?php if (isset($validation) && $validation->hasError('password')): ?>
                            <div class="invalid-feedback"><?= $validation->getError('password') ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-lock"></i> Confirm Password
                        </label>
                        <div class="password-wrapper">
                            <input type="password" name="passconf" id="confirmPassword" class="form-control <?= (isset($validation) && $validation->hasError('passconf')) ? 'is-invalid' : '' ?>" 
                                   placeholder="Re-enter password" required>
                            <button type="button" class="toggle-password" onclick="togglePassword('confirmPassword')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div id="passwordMatchMessage" class="invalid-feedback" style="display: none;">Passwords do not match</div>
                        <?php if (isset($validation) && $validation->hasError('passconf')): ?>
                            <div class="invalid-feedback"><?= $validation->getError('passconf') ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <button type="submit" class="btn-register" id="registerBtn">
                    <i class="fas fa-user-plus"></i> Create Account
                </button>
            </form>

            <div class="login-link">
                Already have an account? <a href="<?= base_url('/login') ?>">Sign in here</a>
            </div>
        </div>
    </section>

    <footer>
        <p>&copy; 2026 CREATRIX. All rights reserved. | For educational purposes only, and no copyright infringement is intended.</p>
        <p style="margin-top: 10px; font-size: 0.8rem;">CREATRIX Group - Promoting Sustainable Living Through Coconut Coir Innovation</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Toggle password visibility
        function togglePassword(fieldId) {
            const passwordInput = document.getElementById(fieldId);
            const toggleBtn = passwordInput.parentElement.querySelector('.toggle-password i');
            
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

        // Password strength checker
        function checkPasswordStrength(password) {
            let strength = 0;
            if (password.length >= 6) strength++;
            if (password.length >= 8) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            
            const strengthBar = document.getElementById('strengthBar');
            const strengthText = document.getElementById('passwordStrength');
            
            if (password.length === 0) {
                strengthBar.className = 'strength-bar';
                strengthText.innerHTML = '';
                return;
            }
            
            if (strength <= 1) {
                strengthBar.className = 'strength-bar weak';
                strengthText.innerHTML = '<span style="color: var(--danger);">Weak password</span>';
            } else if (strength <= 2) {
                strengthBar.className = 'strength-bar medium';
                strengthText.innerHTML = '<span style="color: var(--warning);">Fair password</span>';
            } else if (strength <= 3) {
                strengthBar.className = 'strength-bar strong';
                strengthText.innerHTML = '<span style="color: var(--accent);">Good password</span>';
            } else {
                strengthBar.className = 'strength-bar very-strong';
                strengthText.innerHTML = '<span style="color: var(--success);">Strong password</span>';
            }
        }

        // Check password match
        function checkPasswordMatch() {
            const password = document.getElementById('password').value;
            const confirm = document.getElementById('confirmPassword').value;
            const matchMessage = document.getElementById('passwordMatchMessage');
            
            if (confirm.length > 0 && password !== confirm) {
                matchMessage.style.display = 'block';
                return false;
            } else {
                matchMessage.style.display = 'none';
                return true;
            }
        }

        // Real-time validation
        document.getElementById('password').addEventListener('input', function() {
            checkPasswordStrength(this.value);
            checkPasswordMatch();
        });
        
        document.getElementById('confirmPassword').addEventListener('input', function() {
            checkPasswordMatch();
        });

        // Form submission validation
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirm = document.getElementById('confirmPassword').value;
            
            if (password !== confirm) {
                e.preventDefault();
                document.getElementById('passwordMatchMessage').style.display = 'block';
                alert('Passwords do not match. Please check your password confirmation.');
            }
        });

        // Add loading effect on form submit
        const registerForm = document.getElementById('registerForm');
        if (registerForm) {
            registerForm.addEventListener('submit', function() {
                const submitBtn = document.getElementById('registerBtn');
                if (submitBtn) {
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Account...';
                    submitBtn.disabled = true;
                }
            });
        }
    </script>
</body>
</html>