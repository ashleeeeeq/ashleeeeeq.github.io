<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'fullname',
        'email',
        'password',
        'address',
        'mobile',
        'role',
        'profile_picture',
        'verification_token',
        'is_verified',
        'verified_at',
        'created_at',
        'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation Rules
    protected $validationRules = [
        'fullname' => 'required|min_length[3]|max_length[100]',
        'email' => 'required|valid_email',
        'password' => 'permit_empty|min_length[6]',  // CHANGE THIS LINE
        'address' => 'required|min_length[5]',
        'mobile' => 'required|min_length[11]|max_length[20]|numeric',
        'role' => 'required|in_list[admin,customer]'
    ];
    
    protected $validationMessages = [
        'fullname' => [
            'required' => 'Full name is required',
            'min_length' => 'Full name must be at least 3 characters'
        ],
        'email' => [
            'required' => 'Email address is required',
            'valid_email' => 'Please enter a valid email address',
            'is_unique' => 'This email is already registered'
        ],
        'password' => [
            'required' => 'Password is required',
            'min_length' => 'Password must be at least 6 characters'
        ],
        'address' => [
            'required' => 'Address is required',
            'min_length' => 'Please enter a complete address'
        ],
        'mobile' => [
            'required' => 'Mobile number is required',
            'min_length' => 'Mobile number must be at least 11 digits',
            'max_length' => 'Mobile number must not exceed 20 digits',
            'numeric' => 'Mobile number should contain only numbers'
        ],
        'role' => [
            'required' => 'Role is required',
            'in_list' => 'Invalid role selected'
        ]
    ];
    
    protected $skipValidation = false;

    // Password hashing before insert/update
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    /**
     * Hash password before insert/update
     */
    protected function hashPassword(array $data)
    {
        // Only hash if password is present and not empty
        if (isset($data['data']['password']) && !empty($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        } else {
            // Remove password from update data if empty
            unset($data['data']['password']);
        }

        return $data;
    }

    /**
     * Generate verification token for new users
     */
    protected function generateVerificationToken(array $data)
    {
        // Only generate token for new registrations (when is_verified is not set)
        if (!isset($data['data']['is_verified']) || $data['data']['is_verified'] == 0) {
            $data['data']['verification_token'] = $this->generateRandomToken();
        }
        
        return $data;
    }

    /**
     * Generate random verification token
     */
    public function generateRandomToken()
    {
        return bin2hex(random_bytes(32)); // 64 characters token
    }

    /**
     * Get user by email
     */
    public function getUserByEmail($email)
    {
        if (empty($email)) {
            return null;
        }

        return $this->where('email', $email)->first();
    }

    /**
     * Get user by verification token
     */
    public function getUserByToken($token)
    {
        return $this->where('verification_token', $token)
                    ->where('is_verified', 0)
                    ->first();
    }

    /**
     * Verify user account
     */
    public function verifyUser($token)
    {
        $user = $this->getUserByToken($token);
        
        if ($user) {
            return $this->update($user['id'], [
                'is_verified' => 1,
                'verified_at' => date('Y-m-d H:i:s'),
                'verification_token' => null
            ]);
        }
        
        return false;
    }

    /**
     * Check if user is verified
     */
    public function isVerified($userId)
    {
        $user = $this->find($userId);
        return $user && $user['is_verified'] == 1;
    }

    /**
     * Resend verification email - update token
     */
    public function updateVerificationToken($userId)
    {
        $token = $this->generateRandomToken();
        $this->update($userId, ['verification_token' => $token]);
        return $token;
    }

    /**
     * Get user by ID
     */
    public function getUserById($id)
    {
        return $this->find($id);
    }

    /**
     * Get all customers with pagination
     */
    public function getCustomers()
    {
        return $this->where('role', 'customer')
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    /**
     * Get paginated customers
     */
    public function getCustomersPaginated($perPage = 10)
    {
        return $this->where('role', 'customer')
            ->orderBy('created_at', 'DESC')
            ->paginate($perPage);
    }

    /**
     * Get all admins
     */
    public function getAdmins()
    {
        return $this->where('role', 'admin')
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    /**
     * Update profile information
     */
    public function updateProfile($id, $data)
    {
        $updateData = [
            'fullname' => $data['fullname'] ?? null,
            'address'  => $data['address'] ?? null,
            'mobile'   => $data['mobile'] ?? null
        ];

        // Remove null values
        $updateData = array_filter($updateData);

        if (empty($updateData)) {
            return false;
        }

        return $this->update($id, $updateData);
    }

    /**
     * Update profile picture
     */
    public function updateProfilePicture($id, $filename)
    {
        return $this->update($id, ['profile_picture' => $filename]);
    }

    /**
     * Count total users by role
     */
    public function countUsers($role = null)
    {
        if ($role) {
            return $this->where('role', $role)->countAllResults();
        }
        return $this->countAllResults();
    }

    /**
     * Count verified users
     */
    public function countVerifiedUsers()
    {
        return $this->where('is_verified', 1)->countAllResults();
    }

    /**
     * Count unverified users
     */
    public function countUnverifiedUsers()
    {
        return $this->where('is_verified', 0)->where('role', 'customer')->countAllResults();
    }

    /**
     * Get user statistics
     */
    public function getUserStats()
    {
        $total = $this->countAllResults();
        $customers = $this->where('role', 'customer')->countAllResults();
        $admins = $this->where('role', 'admin')->countAllResults();
        $verified = $this->countVerifiedUsers();
        $unverified = $this->countUnverifiedUsers();
        
        // New users this month
        $thisMonth = date('Y-m');
        $newThisMonth = $this->like('created_at', $thisMonth, 'after')
            ->countAllResults();

        return [
            'total' => $total,
            'customers' => $customers,
            'admins' => $admins,
            'verified' => $verified,
            'unverified' => $unverified,
            'new_this_month' => $newThisMonth,
            'active_users' => 0 // Temporary until OrderModel exists
        ];
    }

    /**
     * Get recent users
     */
    public function getRecentUsers($limit = 5)
    {
        return $this->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Search users by name, email, or mobile
     */
    public function searchUsers($keyword)
    {
        return $this->groupStart()
                ->like('fullname', $keyword)
                ->orLike('email', $keyword)
                ->orLike('mobile', $keyword)
            ->groupEnd()
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    /**
     * Verify user password
     */
    public function verifyPassword($id, $password)
    {
        $user = $this->find($id);
        
        if ($user && isset($user['password'])) {
            return password_verify($password, $user['password']);
        }
        
        return false;
    }

    /**
     * Change user password
     */
    public function changePassword($id, $newPassword)
    {
        return $this->update($id, ['password' => $newPassword]);
    }

    /**
     * Get user with order history
     */
    public function getUserWithOrders($id)
    {
        $user = $this->find($id);
        
        if (!$user) {
            return null;
        }

        // Check if OrderModel exists before using it
        if (class_exists('\App\Models\OrderModel')) {
            $orderModel = new \App\Models\OrderModel();
            
            if (class_exists('\App\Models\OrderItemsModel')) {
                $orderItemsModel = new \App\Models\OrderItemsModel();

                $orders = $orderModel->where('user_id', $id)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();

                // Add item details to each order
                foreach ($orders as &$order) {
                    $order['items'] = $orderItemsModel->getOrderItems($order['id']);
                    
                    // Calculate total items in order
                    $totalItems = 0;
                    foreach ($order['items'] as $item) {
                        $totalItems += $item['quantity'];
                    }
                    $order['total_items'] = $totalItems;
                }

                $user['orders'] = $orders;
                $user['total_orders'] = count($orders);
                
                // Calculate total spent
                $totalSpent = 0;
                foreach ($orders as $order) {
                    $totalSpent += $order['total_price'];
                }
                $user['total_spent'] = $totalSpent;
            } else {
                $user['orders'] = [];
                $user['total_orders'] = 0;
                $user['total_spent'] = 0;
            }
        } else {
            $user['orders'] = [];
            $user['total_orders'] = 0;
            $user['total_spent'] = 0;
        }

        // Format join date
        $user['joined_date'] = date('F d, Y', strtotime($user['created_at']));

        return $user;
    }

    /**
     * Check if email exists (for registration validation)
     */
    public function emailExists($email, $excludeId = null)
    {
        $query = $this->where('email', $email);
        
        if ($excludeId) {
            $query->where('id !=', $excludeId);
        }
        
        return $query->first() !== null;
    }

    /**
     * Delete user with checks
     */
    public function deleteUser($id)
    {
        // Don't allow deletion of own account through this method
        if (session()->get('id') == $id) {
            return false;
        }

        // Check if user has orders - only if OrderModel exists
        if (class_exists('\App\Models\OrderModel')) {
            $orderModel = new \App\Models\OrderModel();
            $orderCount = $orderModel->where('user_id', $id)->countAllResults();
            
            if ($orderCount > 0) {
                return false;
            }
        }

        return $this->delete($id);
    }

    /**
     * Get user role display name
     */
    public function getRoleDisplay($role)
    {
        $roles = [
            'admin' => ['class' => 'danger', 'name' => 'Administrator'],
            'customer' => ['class' => 'success', 'name' => 'Customer']
        ];
        
        return $roles[$role] ?? ['class' => 'secondary', 'name' => 'Unknown'];
    }

    /**
     * Validate current password before update
     */
    public function validateAndUpdatePassword($id, $currentPassword, $newPassword)
    {
        if ($this->verifyPassword($id, $currentPassword)) {
            return $this->changePassword($id, $newPassword);
        }
        
        return false;
    }

    /**
     * Get users registered between dates
     */
    public function getUsersByDateRange($startDate, $endDate)
    {
        return $this->where('DATE(created_at) >=', $startDate)
            ->where('DATE(created_at) <=', $endDate)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    /**
     * Get formatted user data for profile
     */
    public function getFormattedUser($id)
    {
        $user = $this->find($id);
        
        if ($user) {
            $user['formatted_mobile'] = $this->formatMobile($user['mobile']);
            $user['role_display'] = $this->getRoleDisplay($user['role']);
            $user['joined_date'] = date('F d, Y', strtotime($user['created_at']));
            $user['profile_picture_url'] = $user['profile_picture'] 
                ? base_url('uploads/profile_pictures/' . $user['profile_picture'])
                : base_url('assets/default-avatar.png');
            $user['verification_status'] = $user['is_verified'] ? 'Verified' : 'Pending';
            $user['verification_badge'] = $user['is_verified'] ? 'success' : 'warning';
        }
        
        return $user;
    }

    /**
     * Format mobile number
     */
    private function formatMobile($mobile)
    {
        // Format: 0912 345 6789
        if (strlen($mobile) >= 11) {
            return substr($mobile, 0, 4) . ' ' . 
                   substr($mobile, 4, 3) . ' ' . 
                   substr($mobile, 7);
        }
        return $mobile;
    }

    /**
     * Override insert method to ensure proper data handling
     */
    public function insert($data = null, bool $returnID = true)
    {
        // Ensure role is set for new registrations
        if (!isset($data['role'])) {
            $data['role'] = 'customer';
        }
        
        // Ensure is_verified is set for new users
        if (!isset($data['is_verified'])) {
            $data['is_verified'] = 0;
        }
        
        return parent::insert($data, $returnID);
    }

    /**
     * Get unverified users (for admin)
     */
    public function getUnverifiedUsers()
    {
        return $this->where('is_verified', 0)
                    ->where('role', 'customer')
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }
}