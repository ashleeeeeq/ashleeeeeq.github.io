<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kape-Con • Login</title>
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
        
        /* Login Container */
        .login-container {
            position: relative;
            z-index: 1;
            max-width: 480px;
            width: 100%;
            padding: 60px;
            margin: 40px 20px;
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
        
        /* Error Message */
        .error-message {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            border: none;
            border-radius: 15px;
            padding: 16px 20px;
            margin-bottom: 25px;
            color: #721c24;
            font-size: 0.9rem;
            font-weight: 500;
            animation: shake 0.5s ease-out;
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.15);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .error-message i {
            font-size: 1.3rem;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }
        
        /* Form */
        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--grey);
            font-size: 1.3rem;
            transition: color 0.3s ease;
        }
        
        .form-input {
            background: rgba(255, 255, 255, 0.8);
            border: 2px solid #e0e0e0;
            border-radius: 15px;
            padding: 18px 20px 18px 55px;
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
        
        /* Button */
        .btn-login {
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
            margin-top: 15px;
            font-family: 'Poppins', sans-serif;
            box-shadow: 0 8px 20px rgba(139, 69, 19, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-login i {
            font-size: 1.2rem;
            transition: transform 0.3s ease;
        }
        
        .btn-login:hover {
            background: var(--primary-dark);
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(139, 69, 19, 0.4);
        }

        .btn-login:hover i {
            transform: translateX(5px);
        }
        
        .btn-login:active {
            transform: translateY(-1px);
        }
        
        /* Register Link */
        .register-link {
            text-align: center;
            margin-top: 30px;
            padding-top: 30px;
            border-top: 1px solid rgba(139, 69, 19, 0.1);
        }
        
        .register-link p {
            color: #666;
            font-size: 0.95rem;
            margin-bottom: 10px;
        }

        .register-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-size: 1rem;
            font-weight: 600;
            letter-spacing: 0.3px;
            transition: all 0.3s ease;
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .register-link a i {
            transition: transform 0.3s ease;
        }
        
        .register-link a:hover {
            color: var(--primary-dark);
        }

        .register-link a:hover i {
            transform: translateX(5px);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .login-container {
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
    <div class="login-container">
        <div class="header">
            <div class="coffee-icon">☕</div>
            <h1 class="brand-name">Kape-Con</h1>
            <h2 class="page-title">Welcome Back</h2>
            <p class="page-subtitle">Sign in to continue your coffee journey</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="error-message">
                <i class='bx bx-error-circle'></i>
                <span><?= $error ?></span>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('/login') ?>" method="post">
            <div class="form-group">
                <input name="email" type="email" class="form-input" placeholder="Email Address" required>
                <i class='bx bx-envelope input-icon'></i>
            </div>

            <div class="form-group">
                <input name="password" type="password" class="form-input" placeholder="Password" required>
                <i class='bx bx-lock-alt input-icon'></i>
            </div>

            <button type="submit" class="btn-login">Sign In</button>
        </form>

        <div class="register-link">
            <p>Don't have an account?</p>
            <a href="<?= base_url('/register') ?>">
                Create Account
                <i class='bx bx-right-arrow-alt'></i>
            </a>
        </div>
    </div>
</body>
</html>