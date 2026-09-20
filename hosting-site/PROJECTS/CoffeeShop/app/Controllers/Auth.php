<?php
namespace App\Controllers;

use CodeIgniter\Controller;
use App\Controllers\BaseController;
use Config\Database;
use App\Models\UserModel;

class Auth extends Controller
{
    protected $db;

    public function __construct()
    {
        helper(['url', 'form']);
        $this->db = Database::connect();
    }

    public function register()
    {
        return view('register');
    }

    public function registerUser()
    {
        $validated = $this->validate([
            'name' => [
                'rules' => 'required|min_length[3]|max_length[50]',
                'errors' => [
                    'required' => 'Full name is required.',
                ]
            ],
            'email' => [
                'rules' => 'required|valid_email|is_unique[users.email]',
                'errors' => [
                    'is_unique' => 'Email already exists.',
                ]
            ],
            'password' => [
                'rules' => 'required|min_length[8]|regex_match[/(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_])/]',
                'errors' => [
                    'required' => 'Password is required.',
                    'min_length' => 'Password must be at least 8 characters long.',
                    'regex_match' => 'Password must contain at least one uppercase letter, one lowercase letter, one number,
                                    and one special character.'
                ]
            ],
            'confirm_password' => [
                'rules' => 'required|matches[password]',
                'errors' => [
                    'required' => 'Please confirm your password.',
                    'matches' => 'Passwords do not match.'
                ]
            ]
        ]);

        if (!$validated) {
            return view('register', ['validation' => $this->validator]);
        }

        // Save to Database
        $builder = $this->db->table('users');
        $plainPassword = $this->request->getPost('password');

        $data = [
            'name'            => $this->request->getPost('name'),
            'email'           => $this->request->getPost('email'),
            'password'        => password_hash($plainPassword, PASSWORD_DEFAULT),
            'plain_password'  => $plainPassword,
            'role'            => 'user'
        ];

        $builder->insert($data);

        // SEND EMAIL AFTER REGISTRATION
        $emailService = \Config\Services::email();

        $emailService->setTo($this->request->getPost('email'));
        $emailService->setFrom('alisaleyhaiba@gmail.com', 'Kape-Con');
        $emailService->setSubject('Welcome to Kape-Con!');

        $message = "
            <div style='font-family: Arial, sans-serif; color: #333; padding: 20px;'>
                <h2 style='color: #6C4A2F;'>Welcome to Kape-Con, " . $this->request->getPost('name') . "!</h2>

                <p style='font-size: 16px; line-height: 1.6;'>
                    Your account is officially brewed and ready! ☕  
                    We're excited to have you join our community — where good coffee meets good company.
                </p>

                <p style='font-size: 16px; line-height: 1.6;'>
                    You can now log in anytime and explore what Kape-Con has to offer.  
                    If you ever get lost, don’t worry — we’re always here, like that friend who never refuses a coffee invite.
                </p>

                <br>

                <a href='" . base_url('/login') . "' 
                    style='display: inline-block; padding: 12px 20px; background-color: #6C4A2F; 
                    color: white; text-decoration: none; border-radius: 6px; font-weight: bold;'>
                    Log In to Your Account
                </a>

                <br><br>

                <p style='font-size: 14px; color: #777;'>
                    Cheers,<br>
                    <strong>The Kape-Con Team</strong><br>
                    <em>Brewing connections, one cup at a time.</em>
                </p>
            </div>
        ";

        $emailService->setMessage($message);

        // If sending fails, show the exact SMTP error
        if (!$emailService->send()) {
            echo "<pre>";
            print_r($emailService->printDebugger(['headers', 'subject', 'body']));
            echo "</pre>";
            exit;
        }

        return redirect()->to('/login')->with('success', 'Registration successful! Please check your email.');
    }
    
    public function loginUser()
    {
        $validated = $this->validate([
            'email' => [
                'rules' => 'required|valid_email',
                'errors' => [
                    'required' => 'Your email is required',
                    'valid_email' => 'Please enter a valid email address',
                ],
            ],
            'password' => [
                'rules' => 'required|min_length[5]|max_length[20]',
                'errors' => [
                    'required' => 'Your password is required',
                    'min_length' => 'Password must be at least 5 characters long',
                    'max_length' => 'Password cannot exceed 20 characters',
                ],
            ],
        ]);

        if (!$validated) {
            return view('login', ['validation' => $this->validator]);
        } else {
            $email = $this->request->getPost('email');
            $password = $this->request->getPost('password');

            $userModel = new UserModel();
            $userInfo = $userModel->where('email', $email)->first();

            if (!$userInfo) {
                return view('login', ['error' => 'No account found with that email.']);
            }

            if (!password_verify($password, $userInfo['password'])) {
                return view('login', ['error' => 'Incorrect password.']);
            }

            // Store session
            session()->set([
                'user_id' => $userInfo['id'],
                'role'    => $userInfo['role']
            ]);

            // Redirect by role
            if ($userInfo['role'] === 'admin') {
                return redirect()->to('/admin');
            } else {
                return redirect()->to('/homepage');
            }
        }
    }

    public function profile()
    {
        if (!session()->get('user_id')) {
            return redirect()->to('/login');
        }

        $userModel = new \App\Models\UserModel();
        $user = $userModel->find(session()->get('user_id'));

        return view('profile', ['user' => $user]);
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'You have logged out successfully.');
    }

}