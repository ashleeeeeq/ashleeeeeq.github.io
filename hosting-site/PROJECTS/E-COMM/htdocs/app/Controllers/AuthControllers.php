<?php

namespace App\Controllers;

use App\Libraries\EmailLib;
use App\Models\UserModel;

class AuthControllers extends BaseController
{
    protected UserModel $userModel;
    protected EmailLib $emailLib;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->emailLib  = new EmailLib();
    }

    public function login()
    {
        if (session()->get('isLoggedIn')) {
            if (session()->get('role') === 'admin') {
                return redirect()->to('/admin/dashboard');
            }

            return redirect()->to('/storefront');
        }

        helper(['form']);

        return view('login');
    }

    public function attemptLogin()
    {
        helper(['form']);

        $validation = \Config\Services::validation();
        $validation->setRules([
            'email'    => 'required|valid_email',
            'password' => 'required',
        ]);

        if (! $validation->withRequest($this->request)->run()) {
            return view('login', [
                'validation' => $validation,
            ]);
        }

        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $this->userModel->where('email', $email)->first();

        if (! $user || ! password_verify($password, $user['password'])) {
            // Return view with error message
            return view('login', [
                'error' => 'Invalid email or password. Please try again.',
                'old_email' => $email  // Keep email for convenience
            ]);
        }

        if (($user['role'] ?? '') === 'customer' && isset($user['is_verified']) && (int) $user['is_verified'] === 0) {
            return view('login', [
                'error' => 'Please verify your email address first. Check your inbox for the verification link.',
                'unverified_email' => $email
            ]);
        }

        session()->set([
            'id'              => $user['id'],
            'fullname'        => $user['fullname'],
            'email'           => $user['email'],
            'role'            => $user['role'],
            'profile_picture' => $user['profile_picture'] ?? null,
            'is_verified'     => $user['is_verified'] ?? 1,
            'isLoggedIn'      => true,
        ]);

        if (($user['role'] ?? '') === 'admin') {
            return redirect()->to('/admin/dashboard')
                ->with('success', 'Welcome back, ' . $user['fullname'] . '!');
        }

        return redirect()->to('/storefront')
            ->with('success', 'Welcome back, ' . $user['fullname'] . '!');
    }

    public function register()
    {
        if (session()->get('isLoggedIn')) {
            if (session()->get('role') === 'admin') {
                return redirect()->to('/admin/dashboard');
            }

            return redirect()->to('/storefront');
        }

        helper(['form']);

        return view('registration');
    }

    public function saveRegister()
    {
        helper(['form']);

        $validation = \Config\Services::validation();
        $validation->setRules([
            'fullname' => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'passconf' => 'required|matches[password]',
            'address' => 'required|min_length[5]|max_length[255]',
            'mobile' => 'required|min_length[11]|max_length[15]|numeric'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return view('registration', ['validation' => $validation]);
        }

        // Generate verification token (ONCE)
        $verificationToken = bin2hex(random_bytes(32));

        // Log the token for debugging
        log_message('debug', 'Generated token for registration: ' . $verificationToken);

        $userData = [
            'fullname' => $this->request->getPost('fullname'),
            'email'    => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'address'  => $this->request->getPost('address'),
            'mobile'   => $this->request->getPost('mobile'),
            'role'     => 'customer',
            'is_verified' => 0,
            'verification_token' => $verificationToken,  // YOUR token
            'verified_at' => null
        ];

        try {
            // Save user - use insert directly to avoid beforeInsert events
            $userId = $this->userModel->insert($userData);

            if ($userId) {
                // Send verification email
                $verificationLink = base_url('/verify-email/' . $verificationToken);

                // Log the link for debugging
                log_message('debug', 'Verification link: ' . $verificationLink);

                $emailSent = $this->sendVerificationEmail($userData['email'], $userData['fullname'], $verificationLink);

                // Verify token was saved correctly
                $savedUser = $this->userModel->find($userId);
                log_message('debug', 'Saved token in DB: ' . ($savedUser['verification_token'] ?? 'NULL'));

                if ($emailSent) {
                    return redirect()->to('/login')->with('success', 'Registration successful! A verification email has been sent to ' . $userData['email']);
                } else {
                    return redirect()->to('/login')->with('warning', 'Registration successful! However, we couldn\'t send the verification email. Please contact support.');
                }
            } else {
                $errors = $this->userModel->errors();
                return view('registration', [
                    'validation' => $validation,
                    'error' => 'Registration failed: ' . implode(', ', $errors)
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Registration failed: ' . $e->getMessage());
            return view('registration', [
                'error' => 'Registration failed. Please try again.'
            ]);
        }
    }

    public function verifyEmail($token)
    {
        log_message('debug', '=== VERIFY EMAIL DEBUG ===');
        log_message('debug', 'Token received: ' . $token);

        $userModel = new UserModel();

        // Find user by token
        $user = $userModel->where('verification_token', $token)->first();

        if ($user) {
            log_message('debug', 'User found: ' . $user['email']);
            log_message('debug', 'Current is_verified: ' . $user['is_verified']);
            log_message('debug', 'Token in DB: ' . $user['verification_token']);

            if ($user['is_verified'] == 1) {
                return redirect()->to('/login')->with('success', 'Your email is already verified. You can log in now.');
            }

            // Update user to verified
            $result = $userModel->update($user['id'], [
                'is_verified' => 1,
                'verified_at' => date('Y-m-d H:i:s'),
                'verification_token' => null
            ]);

            if ($result) {
                log_message('debug', 'User verified successfully!');
                return redirect()->to('/login')->with('success', 'Email verified successfully! You can now log in.');
            } else {
                log_message('error', 'Failed to update user');
                return redirect()->to('/login')->with('error', 'Failed to verify email. Please try again.');
            }
        } else {
            // Check if token exists but user already verified
            $userVerified = $userModel->where('verification_token', $token)->where('is_verified', 1)->first();
            if ($userVerified) {
                log_message('debug', 'Token found but user already verified');
                return redirect()->to('/login')->with('success', 'Your email is already verified. You can log in now.');
            }

            log_message('error', 'No user found with token: ' . $token);

            // Show all unverified users for debugging (remove in production)
            $unverified = $userModel->where('is_verified', 0)->findAll();
            log_message('debug', 'Unverified users: ' . count($unverified));
            foreach ($unverified as $u) {
                log_message('debug', 'User: ' . $u['email'] . ' - Token: ' . ($u['verification_token'] ?? 'NULL'));
            }

            return redirect()->to('/login')->with('error', 'Invalid or expired verification link. Please request a new one.');
        }
    }

    public function resendVerification()
    {
        $email = $this->request->getPost('email');

        if (! $email) {
            return redirect()->back()->with('error', 'Email address is required.');
        }

        $user = $this->userModel->where('email', $email)->first();

        if (! $user) {
            return redirect()->back()->with('error', 'Email address not found.');
        }

        if ((int) ($user['is_verified'] ?? 0) === 1) {
            return redirect()->to('/login')
                ->with('success', 'Your email is already verified. You can now log in.');
        }

        $newToken = bin2hex(random_bytes(32));

        $this->userModel->update($user['id'], [
            'verification_token' => $newToken,
        ]);

        $verificationLink = base_url('verify-email/' . $newToken);
        $emailSent = $this->sendVerificationEmail(
            $user['email'],
            $user['fullname'],
            $verificationLink
        );

        if ($emailSent) {
            return redirect()->back()
                ->with('success', 'A new verification email has been sent to ' . $email . '. Please check your inbox.');
        }

        return redirect()->back()
            ->with('error', 'Failed to send verification email. Please try again later.');
    }

    public function forgotPassword()
    {
        helper(['form']);

        return view('forgot_password');
    }

    public function processForgotPassword()
    {
        helper(['form']);

        $validation = \Config\Services::validation();
        $validation->setRules([
            'email' => 'required|valid_email',
        ]);

        if (! $validation->withRequest($this->request)->run()) {
            return view('forgot_password', [
                'validation' => $validation,
            ]);
        }

        $email = $this->request->getPost('email');
        $user  = $this->userModel->where('email', $email)->first();

        if (! $user) {
            return view('forgot_password', [
                'error' => 'Email not found in our records.',
            ]);
        }

        $resetToken = bin2hex(random_bytes(32));

        $this->userModel->update($user['id'], [
            'reset_token' => $resetToken,
        ]);

        $resetLink = base_url('reset-password/' . $resetToken);

        $this->sendPasswordResetEmail($email, $user['fullname'], $resetLink);

        return redirect()->to('/login')
            ->with('success', 'Password reset instructions have been sent to your email.');
    }

    public function logout()
    {
        $fullname = session()->get('fullname');
        log_message('info', 'User logged out: ' . $fullname);

        session()->destroy();

        return redirect()->to('/login')
            ->with('success', 'You have been successfully logged out.');
    }

    private function sendVerificationEmail($email, $name, $verificationLink)
    {
        $emailService = \Config\Services::email();

        $emailService->setTo($email);
        $emailService->setSubject('Verify Your Email - CREATRIX COIR');

        $message = '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Verify Your Email - CREATRIX COIR</title>
            <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }

                body {
                    font-family: "Poppins", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
                    background: #F0F4F1;
                    padding: 40px 20px;
                    line-height: 1.6;
                }

                .email-container {
                    max-width: 600px;
                    margin: 0 auto;
                    background: #FFFFFF;
                    border-radius: 24px;
                    overflow: hidden;
                    box-shadow: 0 20px 40px rgba(31, 69, 41, 0.1);
                }

                .email-header {
                    background: linear-gradient(135deg, #1F4529 0%, #2A5C37 100%);
                    padding: 40px 30px;
                    text-align: center;
                }

                .logo-icon {
                    width: 60px;
                    height: 60px;
                    background: rgba(255, 255, 255, 0.15);
                    border-radius: 50%;
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    margin-bottom: 20px;
                }

                .logo-icon img {
                    width: 32px;
                    height: 32px;
                    object-fit: contain;
                    filter: brightness(0) invert(1);
                }

                .email-header h1 {
                    color: #FFFFFF;
                    font-size: 28px;
                    font-weight: 700;
                    margin: 0 0 8px 0;
                    letter-spacing: -0.5px;
                }

                .email-header p {
                    color: rgba(255, 255, 255, 0.9);
                    font-size: 16px;
                    font-weight: 400;
                    margin: 0;
                }

                .email-content {
                    padding: 40px 30px;
                    background: #FFFFFF;
                }

                .greeting {
                    font-size: 24px;
                    font-weight: 600;
                    color: #1F4529;
                    margin-bottom: 20px;
                }

                .message-text {
                    color: #2C3B31;
                    font-size: 16px;
                    line-height: 1.8;
                    margin-bottom: 25px;
                }

                .info-box {
                    background: #F0F4F1;
                    border-left: 4px solid #2A5C37;
                    padding: 20px;
                    border-radius: 12px;
                    margin: 25px 0;
                }

                .info-box h4 {
                    color: #1F4529;
                    font-size: 16px;
                    font-weight: 600;
                    margin-bottom: 12px;
                }

                .info-box ul {
                    margin: 0;
                    padding-left: 20px;
                    color: #2C3B31;
                    font-size: 14px;
                    line-height: 1.6;
                }

                .info-box li {
                    margin: 8px 0;
                }

                .verify-button {
                    display: inline-block;
                    background: #2A5C37;
                    color: #FFFFFF !important;
                    text-decoration: none;
                    padding: 14px 32px;
                    border-radius: 50px;
                    font-weight: 600;
                    font-size: 16px;
                    transition: all 0.3s ease;
                    margin: 20px 0;
                    border: none;
                    cursor: pointer;
                }

                .verify-button:hover {
                    background: #1F4529;
                    transform: translateY(-2px);
                    box-shadow: 0 8px 20px rgba(31, 69, 41, 0.2);
                }

                .fallback-link {
                    margin-top: 25px;
                    padding-top: 20px;
                    border-top: 1px solid #DCE7E0;
                    font-size: 12px;
                    color: #647368;
                    word-break: break-all;
                }

                .fallback-link a {
                    color: #2A5C37;
                    text-decoration: none;
                }

                .fallback-link a:hover {
                    text-decoration: underline;
                }

                .expiry-note {
                    background: #F8F9FA;
                    border-radius: 12px;
                    padding: 15px;
                    margin: 25px 0 0;
                    text-align: center;
                    font-size: 13px;
                    color: #647368;
                }

                .email-footer {
                    background: #F8FAF9;
                    padding: 30px;
                    text-align: center;
                    border-top: 1px solid #E5E9E6;
                }

                .footer-text {
                    font-size: 12px;
                    color: #8B9A8F;
                    line-height: 1.6;
                }

                .footer-text p {
                    margin: 5px 0;
                }

                @media (max-width: 600px) {
                    .email-content {
                        padding: 30px 20px;
                    }

                    .greeting {
                        font-size: 20px;
                    }

                    .verify-button {
                        padding: 12px 28px;
                        font-size: 14px;
                    }
                }
            </style>
        </head>
        <body>
            <div class="email-header">
                <div style="text-align: center; margin-bottom: 5px;">
                    <img src="' . base_url('assets/creatrix_logo.png') . '" alt="CREATRIX" 
                         style="width: 70px; height: 70px; object-fit: contain; filter: brightness(0) invert(1);">
                </div>
                <h1 style="margin-top: 0; margin-bottom: 5px;">CREATRIX</h1>
                <p style="margin-top: 0;">Premium Coconut Coir Products</p>
            </div>

                <div class="email-content">
                    <div class="greeting">
                        Hello, ' . esc($name) . '!
                    </div>

                    <div class="message-text">
                        Thank you for creating an account with <strong style="color: #2A5C37;">CREATRIX</strong>. Please verify your email address to complete your registration and start exploring our sustainable coconut coir products.
                    </div>

                    <div class="info-box">
                        <h4>Benefits of verifying your email</h4>
                        <ul>
                            <li>Secure your account and protect your orders</li>
                            <li>Track your deliveries in real-time</li>
                            <li>Receive exclusive offers and updates</li>
                            <li>Access your order history</li>
                        </ul>
                    </div>

                    <div style="text-align: center;">
                        <a href="' . $verificationLink . '" class="verify-button" style="color: #FFFFFF; background: #2A5C37;">
                            Verify Email Address
                        </a>
                    </div>
                    <div class="fallback-link">
                        <p>If the button doesn\'t work, copy and paste this link into your browser:</p>
                        <a href="' . $verificationLink . '">' . $verificationLink . '</a>
                    </div>
                    <div class="expiry-note">
                        This verification link will expire in 24 hours for security reasons.
                    </div>

                    <div class="message-text" style="margin-top: 25px; font-size: 14px;">
                        If you didn\'t create an account with CREATRIX, you can safely ignore this email.
                    </div>
                </div>

                <div class="email-footer">
                    <div class="footer-text">
                        <p>&copy; 2026 CREATRIX. All rights reserved. | For educational purposes only, and no copyright infringement is intended.</p>
                        <p style="margin-top: 10px; font-size: 0.8rem;">CREATRIX Group - Promoting Sustainable Living Through Coconut Coir Innovation</p>
                    </div>
                </div>
            </div>
        </body>
        </html>';

        $emailService->setMessage($message);

        return $emailService->send();
    }

    private function sendWelcomeEmail($email, $name)
    {
        $subject = 'Welcome to CREATRIX Coir Community!';

        $body = "
        <html>
        <head>
            <style>
                body { font-family: 'Poppins', Arial, sans-serif; line-height: 1.6; color: #2C3B31; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #1F4529; color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { padding: 30px; background: #F0F4F1; border-radius: 0 0 10px 10px; }
                .button { display: inline-block; padding: 12px 24px; background: #2A5C37; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #647368; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Welcome to the CREATRIX Family!</h2>
                </div>
                <div class='content'>
                    <p>Dear {$name},</p>
                    <p>Your email has been verified! Welcome to the CREATRIX Coir community – where sustainability meets quality.</p>
                    <p>We're excited to have you join our mission of transforming coconut husks into valuable, eco-friendly products that benefit both people and the planet.</p>
                    <p>With your new account, you can:</p>
                    <ul>
                        <li>Browse our extensive collection of coconut coir products</li>
                        <li>Track your orders in real-time</li>
                        <li>Save your favorite items for later</li>
                        <li>Receive exclusive offers and sustainability tips</li>
                    </ul>
                    <center>
                        <a href='" . base_url('storefront') . "' class='button'>Start Shopping Now</a>
                    </center>
                    <p>Have questions? Check out our FAQ or contact our friendly customer support team.</p>
                    <p>Together, let's build a more sustainable future!</p>
                    <p>Best regards,<br>The CREATRIX Coir Team</p>
                </div>
                <div class='footer'>
                    <p>&copy; 2026 CREATRIX Coir. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";

        return $this->emailLib->sendEmail($email, $subject, $body);
    }

    private function sendPasswordResetEmail($email, $name, $resetLink)
    {
        $subject = 'Reset Your Password - CREATRIX Coir';

        $body = "
        <html>
        <head>
            <style>
                body { font-family: 'Poppins', Arial, sans-serif; line-height: 1.6; color: #2C3B31; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #1F4529; color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { padding: 30px; background: #F0F4F1; border-radius: 0 0 10px 10px; }
                .button { display: inline-block; padding: 12px 24px; background: #2A5C37; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #647368; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Password Reset Request</h2>
                </div>
                <div class='content'>
                    <p>Dear {$name},</p>
                    <p>We received a request to reset your password for your CREATRIX Coir account.</p>
                    <p>Click the button below to reset your password:</p>
                    <center>
                        <a href='{$resetLink}' class='button'>Reset Password</a>
                    </center>
                    <p>If you didn\'t request this, you can safely ignore this email. Your password will not be changed.</p>
                    <p>This link will expire in 1 hour for security reasons.</p>
                    <p>Best regards,<br>The CREATRIX Coir Team</p>
                </div>
                <div class='footer'>
                    <p>&copy; 2026 CREATRIX Coir. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";

        return $this->emailLib->sendEmail($email, $subject, $body);
    }

    public function debugDB()
    {
        try {
            $db = \Config\Database::connect();

            if ($db->connect()) {
                echo 'Database connected successfully!<br>';
            } else {
                echo 'Database connection failed!<br>';
            }

            $tables = $db->listTables();
            echo 'Tables in database: ' . implode(', ', $tables) . '<br>';

            $fields = $db->getFieldNames('users');
            echo 'Fields in users table: ' . implode(', ', $fields) . '<br>';

            $userCount = $this->userModel->countAll();
            echo 'Total users in table: ' . $userCount . '<br>';

            $users = $this->userModel->findAll();
            echo '<pre>';
            print_r($users);
            echo '</pre>';
        } catch (\Throwable $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function testAdminLogin()
    {
        $admin = $this->userModel->where('email', 'admin@creatrix.com')->first();

        if (! $admin) {
            echo 'Admin not found!';
            return;
        }

        echo '<h2>Admin User Found</h2>';
        echo '<strong>Email:</strong> ' . esc($admin['email']) . '<br>';
        echo '<strong>Stored Hash:</strong> ' . esc($admin['password']) . '<br>';
        echo '<strong>Is Verified:</strong> ' . (! empty($admin['is_verified']) ? 'Yes' : 'No') . '<br><br>';

        $password = 'admin123';
        $result = password_verify($password, $admin['password']);

        echo "<strong>Testing password: '{$password}'</strong><br>";
        echo '<strong>Result:</strong> ' . ($result ? '✓ MATCH' : '✗ NO MATCH') . '<br><br>';

        if (! $result) {
            echo "<a href='/fix-admin-now' style='background:green; color:white; padding:10px; text-decoration:none;'>Click here to FIX the admin password</a>";
        }
    }

    public function fixAdminNow()
    {
        $admin = $this->userModel->where('email', 'admin@creatrix.com')->first();

        if (! $admin) {
            echo 'Admin not found!';
            return;
        }

        $this->userModel->update($admin['id'], [
            'password'    => 'admin123',
            'is_verified' => 1,
            'verified_at' => date('Y-m-d H:i:s'),
        ]);

        $updatedAdmin = $this->userModel->where('email', 'admin@creatrix.com')->first();

        echo '<h2>Admin Password Reset</h2>';

        if (password_verify('admin123', $updatedAdmin['password'])) {
            echo "<span style='color:green; font-weight:bold;'>✓ SUCCESS! Admin account is now fixed.</span><br>";
            echo 'You can now login with:<br>';
            echo 'Email: admin@creatrix.com<br>';
            echo 'Password: admin123<br>';
            echo "<br><a href='/login'>Go to Login Page</a>";
            return;
        }

        echo "<span style='color:red;'>✗ Something went wrong.</span>";
    }
}