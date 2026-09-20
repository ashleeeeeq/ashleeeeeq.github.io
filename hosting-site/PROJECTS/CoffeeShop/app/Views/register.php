<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kape-Con • Register</title>
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
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: var(--white);
            color: var(--black);
            overflow-x: hidden;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            position: relative;
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

        /* Decorative Elements */
        body::after {
            content: '☕';
            position: fixed;
            font-size: 15rem;
            opacity: 0.03;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            pointer-events: none;
            z-index: 0;
        }
        
        /* Register Container */
        .register-container {
            position: relative;
            z-index: 1;
            max-width: 520px;
            width: 100%;
            padding: 60px;
            background: rgba(255, 255, 255, 0.95);
            border: none;
            border-radius: 25px;
            backdrop-filter: blur(20px);
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
        
        /* Header */
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .coffee-icon {
            font-size: 4rem;
            display: inline-block;
            animation: float 3s ease-in-out infinite;
            filter: drop-shadow(0 10px 30px rgba(139, 69, 19, 0.3));
            margin-bottom: 20px;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-15px) rotate(5deg); }
        }
        
        .brand-name {
            font-size: 2rem;
            font-weight: 800;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
            letter-spacing: -0.5px;
        }
        
        .page-title {
            font-size: 1.8rem;
            font-weight: 700;
            margin: 1rem 0 0.5rem;
            color: var(--black);
        }
        
        .page-subtitle {
            font-size: 0.95rem;
            color: #666;
            font-weight: 500;
        }
        
        /* Alert Messages */
        .alert {
            border-radius: 15px;
            border: none;
            padding: 16px 20px;
            margin-bottom: 25px;
            font-size: 0.9rem;
            font-weight: 500;
            animation: slideDown 0.4s ease-out;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert i {
            font-size: 1.3rem;
        }
        
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .alert-success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
        }
        
        /* Form */
        .form-group {
            margin-bottom: 25px;
            position: relative;
        }
        
        .form-label {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--black);
            margin-bottom: 8px;
            display: block;
        }

        .input-icon {
            position: absolute;
            left: 20px;
            bottom: 18px;
            color: var(--grey);
            font-size: 1.3rem;
            transition: color 0.3s ease;
        }
        
        .form-input {
            background: rgba(255, 255, 255, 0.8);
            border: 2px solid #e0e0e0;
            border-radius: 15px;
            padding: 16px 20px 16px 55px;
            font-size: 1rem;
            color: var(--black);
            transition: all 0.3s ease;
            width: 100%;
            font-family: 'Poppins', sans-serif;
            font-weight: 500;
        }
        
        .form-input::placeholder {
            color: #999;
            font-weight: 400;
        }
        
        .form-input:focus {
            background: white;
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 4px rgba(139, 69, 19, 0.1);
            transform: translateY(-2px);
        }

        .form-input:focus + .input-icon {
            color: var(--primary-color);
        }
        
        .form-input:hover {
            border-color: var(--accent-color);
        }
        
        /* Button Group */
        .button-group {
            margin-top: 35px;
            padding-top: 30px;
            border-top: 1px solid rgba(139, 69, 19, 0.1);
        }
        
        /* Button */
        .btn-register {
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
        }

        .btn-register i {
            font-size: 1.2rem;
            transition: transform 0.3s ease;
        }
        
        .btn-register:hover {
            background: var(--primary-dark);
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(139, 69, 19, 0.4);
        }

        .btn-register:hover i {
            transform: translateX(5px);
        }
        
        .btn-register:active {
            transform: translateY(-1px);
        }
        
        /* Login Link */
        .login-link {
            text-align: center;
            margin-top: 20px;
        }

        .login-link p {
            color: #666;
            font-size: 0.95rem;
            margin-bottom: 10px;
        }
        
        .login-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-size: 1rem;
            font-weight: 600;
            letter-spacing: 0.3px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .login-link a i {
            transition: transform 0.3s ease;
        }
        
        .login-link a:hover {
            color: var(--primary-dark);
        }

        .login-link a:hover i {
            transform: translateX(5px);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .register-container {
                padding: 40px 30px;
            }
            
            .brand-name {
                font-size: 1.6rem;
            }

            .page-title {
                font-size: 1.5rem;
            }

            .coffee-icon {
                font-size: 3rem;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="header">
            <div class="coffee-icon">☕</div>
            <h1 class="brand-name">Kape-Con</h1>
            <h2 class="page-title">Join Our Community</h2>
            <p class="page-subtitle">Create your account and start your coffee journey</p>
        </div>

        <?php if(session()->getFlashdata('success')): ?>
            <div class="alert alert-success">
                <i class='bx bx-check-circle'></i>
                <span><?= session()->getFlashdata('success') ?></span>
            </div>
        <?php endif; ?>

        <?php if(isset($validation)): ?>
            <div class="alert alert-danger">
                <i class='bx bx-error-circle'></i>
                <span><?= $validation->listErrors() ?></span>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('/register') ?>" method="post">
            <div class="form-group">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-input" placeholder="Enter your full name" required>
                <i class='bx bx-user input-icon'></i>
            </div>

            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-input" placeholder="your@email.com" required>
                <i class='bx bx-envelope input-icon'></i>
            </div>

            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-input" placeholder="Create a strong password" required>
                <i class='bx bx-lock-alt input-icon'></i>
            </div>

            <div class="form-group">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="confirm_password" class="form-input" placeholder="Confirm your password" required>
                <i class='bx bx-lock-alt input-icon'></i>
            </div>

            <div class="button-group">
                <button type="submit" class="btn-register">Create Account</button>
                
                <div class="login-link">
                    <p>Already have an account?</p>
                    <a href="<?= base_url('/login') ?>">
                        Sign In
                        <i class='bx bx-right-arrow-alt'></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</body>
</html>