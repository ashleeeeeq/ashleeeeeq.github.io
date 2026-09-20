<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = $_POST['name'];
    $email = $_POST['email'];
    $plain_password = $_POST['plain_password'];
    $role  = $_POST['role'];

    $db = \Config\Database::connect();
    $builder = $db->table('users');

    $errors = [];

    // Strong password validation (only if password is entered)
    if (!empty($plain_password)) {
        if (strlen($plain_password) < 8) {
            $errors[] = "Password must be at least 8 characters long.";
        } elseif (!preg_match('/[A-Z]/', $plain_password)) {
            $errors[] = "Password must contain at least one uppercase letter.";
        } elseif (!preg_match('/[a-z]/', $plain_password)) {
            $errors[] = "Password must contain at least one lowercase letter.";
        } elseif (!preg_match('/\d/', $plain_password)) {
            $errors[] = "Password must contain at least one number.";
        } elseif (!preg_match('/[\W_]/', $plain_password)) {
            $errors[] = "Password must contain at least one special character.";
        }
    }

    if (empty($errors)) {
        $data = [
            'name'  => $name,
            'email' => $email,
            'role'  => $role,
        ];

        if (!empty($plain_password)) {
            $data['plain_password'] = $plain_password;
            $data['password'] = password_hash($plain_password, PASSWORD_DEFAULT);
        }

        $builder->where('id', $user['id'])->update($data);
        echo "<p style='color:green;'>User updated successfully!</p>";
    } else {
        foreach ($errors as $err) {
            echo "<p style='color:red;'>$err</p>";
        }
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kape-Con • Edit User</title>
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
        
        /* Edit Container */
        .edit-container {
            position: relative;
            z-index: 1;
            max-width: 600px;
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
            font-size: 3.5rem;
            display: inline-block;
            animation: float 3s ease-in-out infinite;
            filter: drop-shadow(0 10px 30px rgba(139, 69, 19, 0.3));
            margin-bottom: 20px;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-15px) rotate(-5deg); }
        }
        
        .brand-name {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
            letter-spacing: -0.5px;
        }
        
        .page-title {
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0.5rem 0;
            color: var(--black);
        }

        /* Messages */
        .message {
            border-radius: 15px;
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

        .message i {
            font-size: 1.3rem;
        }

        .message-success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
        }

        .message-error {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
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

        .password-icon {
            transform: translateY(-225%);
            z-index: 2;
        }
        
        .form-control {
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

        .form-control:focus + .input-icon, .form-control:focus + .password-icon {
            color: var(--primary-color);
        }
        
        .form-control:hover {
            border-color: var(--accent-color);
        }
        
        select.form-control {
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg width='12' height='8' viewBox='0 0 12 8' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 1L6 6L11 1' stroke='%238B4513' stroke-width='2' stroke-linecap='round'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 20px center;
            padding-right: 50px;
        }

        .helper-text {
            font-size: 0.8rem;
            color: #666;
            margin-top: 6px;
            display: flex;
            align-items: flex-start;
            gap: 5px;
        }

        .helper-text i {
            font-size: 0.9rem;
            margin-top: 2px;
        }
        
        /* Button Group */
        .button-group {
            display: flex;
            gap: 15px;
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid rgba(139, 69, 19, 0.1);
        }
        
        .btn {
            flex: 1;
            padding: 16px 30px;
            border-radius: 50px;
            border: none;
            font-size: 0.95rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-family: 'Poppins', sans-serif;
        }

        .btn i {
            font-size: 1.2rem;
            transition: transform 0.3s ease;
        }
        
        .btn-success {
            background: var(--primary-color);
            color: white;
            box-shadow: 0 8px 20px rgba(139, 69, 19, 0.3);
        }
        
        .btn-success:hover {
            background: var(--primary-dark);
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(139, 69, 19, 0.4);
        }

        .btn-success:hover i {
            transform: scale(1.1);
        }
        
        .btn-secondary {
            background: white;
            border: 2px solid #e0e0e0;
            color: var(--black);
        }
        
        .btn-secondary:hover {
            background: var(--light-bg);
            border-color: var(--accent-color);
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }

        .btn-secondary:hover i {
            transform: translateX(-3px);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .edit-container {
                padding: 40px 30px;
            }
            
            .page-title {
                font-size: 1.5rem;
            }

            .coffee-icon {
                font-size: 2.5rem;
            }
            
            .button-group {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="edit-container">
        <div class="header">
            <div class="coffee-icon">✏️</div>
            <h1 class="brand-name">Kape-Con</h1>
            <h2 class="page-title">Edit User Account</h2>
        </div>

        <form action="<?= base_url('/admin/update/'.$user['id']) ?>" method="post">
            <div class="form-group">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-control" value="<?= esc($user['name']) ?>" required>
                <i class='bx bx-user input-icon'></i>
            </div>

            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" value="<?= esc($user['email']) ?>" required>
                <i class='bx bx-envelope input-icon'></i>
            </div>

            <div class="form-group">
                <label class="form-label">Password</label>
                <i class='bx bx-lock-alt input-icon password-icon'></i>
                <input type="text" name="plain_password" class="form-control" value="<?= esc($user['plain_password']) ?>" required>
                <small class="helper-text">
                    <i class='bx bx-info-circle'></i>
                    <span>Must contain 8+ characters, uppercase, lowercase, number, and special character.</span>
                </small>
            </div>

            <div class="form-group">
                <label class="form-label">Role</label>
                <select name="role" class="form-control no-icon">
                    <option value="user" <?= $user['role'] == 'user' ? 'selected' : '' ?>>User</option>
                    <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                </select>
            </div>

            <div class="button-group">
                <button type="submit" class="btn btn-success">
                    <i class='bx bx-save'></i>
                    Save Changes
                </button>
                <a href="<?= base_url('/admin') ?>" class="btn btn-secondary">
                    <i class='bx bx-left-arrow-alt'></i>
                    Cancel
                </a>
            </div>
        </form>
    </div>
</body>
</html>