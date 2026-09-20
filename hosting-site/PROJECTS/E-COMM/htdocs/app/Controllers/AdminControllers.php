<?php
namespace App\Controllers;

use App\Models\UserModel;
use App\Models\ProductModel;
use App\Models\OrderModel;

class AdminControllers extends BaseController
{
    protected $userModel;
    protected $productModel;
    protected $orderModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->productModel = new ProductModel();
        $this->orderModel = new OrderModel();
    }

    /**
     * Check if user is admin (helper method)
     */
    private function checkAdminAccess()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/login')->with('error', 'Access denied. Admin privileges required.');
        }
        return null;
    }

    /**
     * Admin Dashboard with Statistics and User Management
     */
    public function dashboard()
    {
        // Check if user is admin
        $redirect = $this->checkAdminAccess();
        if ($redirect) return $redirect;

        // Get dashboard statistics
        $data['totalProducts'] = $this->productModel->countAllResults();
        $data['totalCustomers'] = $this->userModel->where('role', 'customer')->countAllResults();
        $data['totalOrders'] = $this->orderModel->countAllResults();
        
        // Get today's sales
        $today = date('Y-m-d');
        $todaySales = $this->orderModel
            ->selectSum('total_price', 'total')
            ->where('DATE(created_at)', $today)
            ->where('status !=', 'cancelled')
            ->first();
        $data['todaySales'] = $todaySales['total'] ?? 0;

        // Get monthly sales
        $month = date('Y-m');
        $monthlySales = $this->orderModel
            ->selectSum('total_price', 'total')
            ->like('created_at', $month, 'after')
            ->where('status !=', 'cancelled')
            ->first();
        $data['monthlySales'] = $monthlySales['total'] ?? 0;

        // ** ADD THIS LINE **
        $data['pendingOrders'] = $this->orderModel->where('status', 'pending')->countAllResults();

        // Get recent orders
        $data['recentOrders'] = $this->orderModel
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->findAll();

        // User Management Section with Pagination
        $perPage = 10;
        $currentPage = $this->request->getGet('page') ? (int)$this->request->getGet('page') : 1;

        // Get paginated users
        $data['users'] = $this->userModel
            ->orderBy('created_at', 'DESC')
            ->paginate($perPage, 'users', $currentPage);

        // Set pagination data
        $data['pager'] = $this->userModel->pager;
        $data['currentPage'] = $currentPage;
        $data['perPage'] = $perPage;
        $data['totalUsers'] = $this->userModel->countAll();

        // Add search functionality
        $search = $this->request->getGet('search');
        if ($search) {
            $data['users'] = $this->userModel
                ->like('fullname', $search)
                ->orLike('email', $search)
                ->orLike('mobile', $search)
                ->paginate($perPage, 'users', $currentPage);
            $data['search'] = $search;
        }

        return view('admin_dashboard', $data);
    }

    /**
     * Display user edit form
     */
    public function editUser($id)
    {
        // Check admin access
        $redirect = $this->checkAdminAccess();
        if ($redirect) return $redirect;

        // Get user data
        $data['user'] = $this->userModel->find($id);
        
        if (!$data['user']) {
            return redirect()->to('/admin/dashboard')->with('error', 'User not found');
        }

        return view('admin_edit_user', $data);
    }

   	 /**
     * Update user information
     */
    public function updateUser($id)
    {
        // Check admin access
        $redirect = $this->checkAdminAccess();
        if ($redirect) return $redirect;

        // Get current user
        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->back()->with('error', 'User not found');
        }

        // Get posted data
        $fullname = trim($this->request->getPost('fullname'));
        $email = trim($this->request->getPost('email'));
        $mobile = trim($this->request->getPost('mobile'));
        $address = trim($this->request->getPost('address'));
        $role = $this->request->getPost('role');
        $password = $this->request->getPost('password');

        // Validate
        if (empty($fullname)) {
            return redirect()->back()->with('error', 'Full name is required')->withInput();
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->with('error', 'Valid email is required')->withInput();
        }

        // CHECK EMAIL UNIQUENESS (MANUAL)
        if ($email !== $user['email']) {
            $existingUser = $this->userModel->where('email', $email)->where('id !=', $id)->first();
            if ($existingUser) {
                return redirect()->back()->with('error', 'This email is already registered to another user')->withInput();
            }
        }

        if (empty($mobile)) {
            return redirect()->back()->with('error', 'Mobile number is required')->withInput();
        }

        if (empty($address)) {
            return redirect()->back()->with('error', 'Address is required')->withInput();
        }

        // Prepare update data
        $updateData = [
            'fullname' => $fullname,
            'email' => $email,
            'mobile' => $mobile,
            'address' => $address,
            'role' => $role
        ];

        // Update password only if provided
        if (!empty($password)) {
            $updateData['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        // Update user - SKIP VALIDATION
        if ($this->userModel->skipValidation(true)->update($id, $updateData)) {
            return redirect()->to('/admin/dashboard')->with('success', 'User updated successfully!');
        } else {
            $errors = $this->userModel->errors();
            $errorMsg = !empty($errors) ? implode(', ', $errors) : 'Unknown error';
            return redirect()->back()->with('error', 'Failed to update user: ' . $errorMsg)->withInput();
        }
    }
    /**
     * Delete user
     */
    public function deleteUser($id)
    {
        // Check admin access
        $redirect = $this->checkAdminAccess();
        if ($redirect) return $redirect;

        // Prevent admin from deleting their own account
        if ($id == session()->get('id')) {
            return redirect()->to('/admin/dashboard')->with('error', 'You cannot delete your own account!');
        }

        // Check if user exists
        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to('/admin/dashboard')->with('error', 'User not found');
        }

        // Delete user record
        if ($this->userModel->delete($id)) {
            return redirect()->to('/admin/dashboard')->with('success', 'User deleted successfully!');
        } else {
            return redirect()->to('/admin/dashboard')->with('error', 'Failed to delete user');
        }
    }

    /**
     * Add new user (admin only)
     */
    public function addUser()
    {
        // Check admin access
        $redirect = $this->checkAdminAccess();
        if ($redirect) return $redirect;

        return view('admin_add_user');
    }

    /**
     * Save new user
     */
    public function saveUser()
    {
        // Check admin access
        $redirect = $this->checkAdminAccess();
        if ($redirect) return $redirect;

        // Validate input
        $validation = \Config\Services::validation();
        $validation->setRules([
            'fullname' => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'mobile' => 'required|min_length[11]|max_length[15]',
            'address' => 'required|min_length[5]|max_length[255]',
            'role' => 'required|in_list[admin,customer]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Prepare user data
        $userData = [
            'fullname' => $this->request->getPost('fullname'),
            'email' => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'mobile' => $this->request->getPost('mobile'),
            'address' => $this->request->getPost('address'),
            'role' => $this->request->getPost('role')
        ];

        // Save user
        if ($this->userModel->save($userData)) {
            return redirect()->to('/admin/dashboard')->with('success', 'User added successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to add user');
        }
    }

    /**
     * Inventory Management with Filters
     */
    public function inventory()
    {
        // Check admin access
        $redirect = $this->checkAdminAccess();
        if ($redirect) return $redirect;

        // Get filter parameters from request
        $search   = $this->request->getGet('search');
        $category = $this->request->getGet('category');
        $stock    = $this->request->getGet('stock'); // 'in', 'low', 'out'
        $sort     = $this->request->getGet('sort') ?? 'newest'; // newest, price_asc, price_desc, name_asc, stock_asc

        // Start query builder
        $builder = $this->productModel->builder();

        // Apply search filter
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('product_name', $search)
                    ->orLike('description', $search)
                ->groupEnd();
        }

        // Apply category filter
        if (!empty($category)) {
            $builder->where('category', $category);
        }

        // Apply stock filter
        if (!empty($stock)) {
            if ($stock === 'in') {
                $builder->where('stock >', 10);
            } elseif ($stock === 'low') {
                $builder->where('stock >', 0)->where('stock <=', 10);
            } elseif ($stock === 'out') {
                $builder->where('stock', 0);
            }
        }

        // Apply sorting
        switch ($sort) {
            case 'price_asc':
                $builder->orderBy('price', 'ASC');
                break;
            case 'price_desc':
                $builder->orderBy('price', 'DESC');
                break;
            case 'name_asc':
                $builder->orderBy('product_name', 'ASC');
                break;
            case 'stock_asc':
                $builder->orderBy('stock', 'ASC');
                break;
            case 'newest':
            default:
                $builder->orderBy('created_at', 'DESC');
                break;
        }

        // Pagination
        $perPage = 15;
        $currentPage = $this->request->getGet('page') ? (int)$this->request->getGet('page') : 1;
        $total = $builder->countAllResults(false); // false to keep query builder

        $builder->limit($perPage, ($currentPage - 1) * $perPage);
        $products = $builder->get()->getResultArray();

        $data = [
            'products'     => $products,
            'search'       => $search,
            'category'     => $category,
            'stock'        => $stock,
            'sort'         => $sort,
            'pager'        => [
                'current' => $currentPage,
                'total'   => $total,
                'perPage' => $perPage,
                'last'    => ceil($total / $perPage),
            ],
        ];

        return view('admin_inventory', $data);
    }

/**
 * Add product form
 */
    public function addProduct()
    {
        $redirect = $this->checkAdminAccess();
        if ($redirect) return $redirect;
        
        return view('admin_add_product');
    }

    /**
     * Save product
     */
    public function saveProduct()
    {
        $redirect = $this->checkAdminAccess();
        if ($redirect) return $redirect;

        $validation = \Config\Services::validation();
        $validation->setRules([
            'product_name' => 'required|min_length[3]|max_length[255]',
            'description' => 'required|min_length[10]',
            'price' => 'required|numeric|greater_than[0]',
            'stock' => 'required|integer|greater_than_equal_to[0]',
            'category' => 'required|in_list[Gardening,Home & Living,Agriculture,Packaging]',
            'featured' => 'permit_empty|in_list[none,new,trending,best_seller]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('validation', $validation);
        }

        $image = $this->request->getFile('product_image');
        $imageName = null;
        if ($image && $image->isValid() && !$image->hasMoved()) {
            $imageName = $image->getRandomName();
            $image->move('uploads/products', $imageName);
        }

        $this->productModel->save([
            'product_name' => $this->request->getPost('product_name'),
            'description' => $this->request->getPost('description'),
            'price' => $this->request->getPost('price'),
            'stock' => $this->request->getPost('stock'),
            'category' => $this->request->getPost('category'),
            'featured' => $this->request->getPost('featured') ?? 'none',
            'image' => $imageName
        ]);

        return redirect()->to('/admin/inventory')->with('success', 'Product added successfully');
    }

    /**
     * Edit product form
     */
    public function editProduct($id)
    {
        $redirect = $this->checkAdminAccess();
        if ($redirect) return $redirect;

        $product = $this->productModel->find($id);
        if (!$product) {
            return redirect()->to('/admin/inventory')->with('error', 'Product not found');
        }

        return view('admin_edit_product', ['product' => $product]);
    }

    /**
     * Update product
     */
    public function updateProduct($id)
    {
        $redirect = $this->checkAdminAccess();
        if ($redirect) return $redirect;

        $product = $this->productModel->find($id);
        if (!$product) {
            return redirect()->to('/admin/inventory')->with('error', 'Product not found');
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'product_name' => 'required|min_length[3]|max_length[255]',
            'description' => 'required|min_length[10]',
            'price' => 'required|numeric|greater_than[0]',
            'stock' => 'required|integer|greater_than_equal_to[0]',
            'category' => 'required|in_list[Gardening,Home & Living,Agriculture,Packaging]',
            'featured' => 'permit_empty|in_list[none,new,trending,best_seller]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('validation', $validation);
        }

        $updateData = [
            'product_name' => $this->request->getPost('product_name'),
            'description' => $this->request->getPost('description'),
            'price' => $this->request->getPost('price'),
            'stock' => $this->request->getPost('stock'),
            'category' => $this->request->getPost('category'),
            'featured' => $this->request->getPost('featured') ?? 'none'
        ];

        // Handle image upload
        $image = $this->request->getFile('product_image');
        if ($image && $image->isValid() && !$image->hasMoved()) {
            // Delete old image if exists
            if ($product['image'] && file_exists('uploads/products/' . $product['image'])) {
                unlink('uploads/products/' . $product['image']);
            }
            $imageName = $image->getRandomName();
            $image->move('uploads/products', $imageName);
            $updateData['image'] = $imageName;
        }

        $this->productModel->update($id, $updateData);

        return redirect()->to('/admin/inventory')->with('success', 'Product updated successfully');
    }

    /**
     * Delete product
     */
    public function deleteProduct($id)
    {
        $redirect = $this->checkAdminAccess();
        if ($redirect) return $redirect;

        $product = $this->productModel->find($id);
        if (!$product) {
            return redirect()->to('/admin/inventory')->with('error', 'Product not found');
        }

        // Delete image file if exists
        if ($product['image'] && file_exists('uploads/products/' . $product['image'])) {
            unlink('uploads/products/' . $product['image']);
        }

        $this->productModel->delete($id);

        return redirect()->to('/admin/inventory')->with('success', 'Product deleted successfully');
    }

    /**
     * Sales Reports
     */
    public function reports()
    {
        // Check admin access
        $redirect = $this->checkAdminAccess();
        if ($redirect) return $redirect;

        // Get date range from request
        $startDate = $this->request->getGet('start_date') ?: date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?: date('Y-m-t');

        // Get sales data
        $data['sales'] = $this->orderModel
            ->select('DATE(created_at) as date, COUNT(*) as order_count, SUM(total_price) as total_sales')
            ->where('DATE(created_at) >=', $startDate)
            ->where('DATE(created_at) <=', $endDate)
            ->where('status !=', 'cancelled')
            ->groupBy('DATE(created_at)')
            ->orderBy('date', 'DESC')
            ->findAll();

        // Get summary
        $summary = $this->orderModel
            ->select('COUNT(*) as total_orders, SUM(total_price) as total_revenue')
            ->where('DATE(created_at) >=', $startDate)
            ->where('DATE(created_at) <=', $endDate)
            ->where('status !=', 'cancelled')
            ->first();

        $data['summary'] = $summary;
        $data['startDate'] = $startDate;
        $data['endDate'] = $endDate;

        return view('admin_reports', $data);
    }

    /**
     * Inventory Report
     */
    public function inventoryReport()
    {
        // Check admin access
        $redirect = $this->checkAdminAccess();
        if ($redirect) return $redirect;

        // Get all products with stock information
        $products = $this->productModel->findAll();
        
        // Calculate inventory metrics
        $totalValue = 0;
        $lowStockCount = 0;
        $outOfStockCount = 0;
        $totalItems = 0;

        foreach ($products as &$product) {
            $product['stock_value'] = $product['price'] * $product['stock'];
            $totalValue += $product['stock_value'];
            $totalItems += $product['stock'];
            
            if ($product['stock'] == 0) {
                $outOfStockCount++;
            } elseif ($product['stock'] < 10) {
                $lowStockCount++;
            }
        }

        $data['products'] = $products;
        $data['totalValue'] = $totalValue;
        $data['totalItems'] = $totalItems;
        $data['totalProducts'] = count($products);
        $data['lowStockCount'] = $lowStockCount;
        $data['outOfStockCount'] = $outOfStockCount;

        return view('admin_inventory_report', $data);
    }

    /**
     * Storefront Management
     */
    public function storefront()
    {
        // Check admin access
        $redirect = $this->checkAdminAccess();
        if ($redirect) return $redirect;

        $data['featuredProducts'] = $this->productModel
            ->where('featured !=', 'none')
            ->findAll();

        $data['allProducts'] = $this->productModel
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return view('admin_storefront', $data);
    }

    /**
     * Update Storefront Featured Products
     */
    public function updateStorefront()
    {
        // Check admin access
        $redirect = $this->checkAdminAccess();
        if ($redirect) return $redirect;

        $featuredProducts = $this->request->getPost('featured') ?: [];

        // Reset all products to 'none' using the query builder
        $this->productModel->builder()->update(['featured' => 'none']);

        // Update selected products
        foreach ($featuredProducts as $productId => $featuredType) {
            if (!empty($featuredType)) {
                $this->productModel->update($productId, ['featured' => $featuredType]);
            }
        }

        return redirect()->to('/admin/storefront')->with('success', 'Storefront updated successfully');
    }

    /**
     * View Orders with filter
     */
    public function orders()
    {
        // Check admin access
        $redirect = $this->checkAdminAccess();
        if ($redirect) return $redirect;

        // Get status filter from URL
        $status = $this->request->getGet('status');
        
        // Build query
        $query = $this->orderModel->orderBy('created_at', 'DESC');
        
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }
        
        $data['orders'] = $query->findAll();
        $data['status'] = $status;
        
        return view('admin_orders', $data);
    }

    /**
     * Update Order Status
     */
    public function updateOrderStatus($id)
    {
        // Check admin access
        $redirect = $this->checkAdminAccess();
        if ($redirect) return $redirect;

        $status = $this->request->getPost('status');
        
        if ($this->orderModel->update($id, ['status' => $status])) {
            return redirect()->to('/admin/orders')->with('success', 'Order status updated');
        }

        return redirect()->to('/admin/orders')->with('error', 'Failed to update order status');
    }

    /**
     * View Order Details
     */
    public function orderDetails($id)
    {
        // Check admin access
        $redirect = $this->checkAdminAccess();
        if ($redirect) return $redirect;

        $orderModel = new \App\Models\OrderModel();
        $orderItemModel = new \App\Models\OrderItemsModel();
        
        $order = $orderModel->find($id);
        if (!$order) {
            return redirect()->to('/admin/orders')->with('error', 'Order not found');
        }

        $order['items'] = $orderItemModel->getOrderItems($id);
        
        // Get user info
        $user = $this->userModel->find($order['user_id']);
        $order['customer'] = $user ? $user['fullname'] : 'Unknown';
        
        $data['order'] = $order;
        
        return view('admin_order_details', $data);
    }

    /**
     * Users list
     */
    public function users()
    {
        // Check admin access
        $redirect = $this->checkAdminAccess();
        if ($redirect) return $redirect;

        $data['users'] = $this->userModel
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return view('admin_users', $data);
    }

    /**
     * Upload profile picture for user
     */
    public function uploadProfilePicture($userId)
    {
        // Check admin access
        $redirect = $this->checkAdminAccess();
        if ($redirect) return $redirect;

        $user = $this->userModel->find($userId);
        if (!$user) {
            return redirect()->to('/admin/dashboard')->with('error', 'User not found');
        }

        $file = $this->request->getFile('profile_picture');
        
        if (!$file->isValid()) {
            return redirect()->back()->with('error', 'Invalid file upload');
        }

        // Validate file
        $validation = \Config\Services::validation();
        $validation->setRules([
            'profile_picture' => 'uploaded[profile_picture]|is_image[profile_picture]|max_size[profile_picture,2048]|mime_in[profile_picture,image/jpg,image/jpeg,image/png]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->with('error', implode(', ', $validation->getErrors()));
        }

        // Create directory if not exists
        $uploadPath = FCPATH . 'uploads/profiles/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        // Delete old profile picture if exists
        if ($user['profile_picture'] && file_exists($uploadPath . $user['profile_picture'])) {
            unlink($uploadPath . $user['profile_picture']);
        }

        // Upload new picture
        $newName = $file->getRandomName();
        $file->move($uploadPath, $newName);

        // Update user record
        $this->userModel->update($userId, ['profile_picture' => $newName]);

        return redirect()->to('/admin/dashboard')->with('success', 'Profile picture updated successfully');
    }

    /**
     * Remove profile picture
     */
    public function removeProfilePicture($userId)
    {
        // Check admin access
        $redirect = $this->checkAdminAccess();
        if ($redirect) return $redirect;

        $user = $this->userModel->find($userId);
        if (!$user) {
            return redirect()->to('/admin/dashboard')->with('error', 'User not found');
        }

        // Delete file
        if ($user['profile_picture'] && file_exists(FCPATH . 'uploads/profiles/' . $user['profile_picture'])) {
            unlink(FCPATH . 'uploads/profiles/' . $user['profile_picture']);
        }

        // Update user record
        $this->userModel->update($userId, ['profile_picture' => null]);

        return redirect()->to('/admin/dashboard')->with('success', 'Profile picture removed successfully');
    }

    /**
     * Verify user account (admin only)
     */
    public function verifyUser($userId)
    {
        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($userId);
        
        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }
        
        // Update user verification status
        $result = $userModel->update($userId, [
            'is_verified' => 1,
            'verified_at' => date('Y-m-d H:i:s'),
            'verification_token' => null
        ]);
        
        if ($result) {
            // Optionally send welcome email
            $emailLib = new \App\Libraries\EmailLib();
            $this->sendWelcomeEmail($user['email'], $user['fullname']);
            
            return redirect()->back()->with('success', 'User ' . $user['fullname'] . ' has been verified successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to verify user.');
        }
    }

    /**
     * Send welcome email after verification
     */
    private function sendWelcomeEmail($email, $name)
    {
        $subject = 'Welcome to CREATRIX Coir!';
        
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
                    <h2>Welcome to CREATRIX Coir!</h2>
                </div>
                <div class='content'>
                    <p>Dear {$name},</p>
                    <p>Your email has been verified! Welcome to the CREATRIX Coir community – where sustainability meets quality.</p>
                    <p>We're excited to have you join our mission of transforming coconut husks into valuable, eco-friendly products.</p>
                    <p>With your account, you can:</p>
                    <ul>
                        <li>Browse our extensive collection of coconut coir products</li>
                        <li>Track your orders in real-time</li>
                        <li>Save your favorite items for later</li>
                        <li>Receive exclusive offers and sustainability tips</li>
                    </ul>
                    <center>
                        <a href='" . base_url('/storefront') . "' class='button'>Start Shopping Now</a>
                    </center>
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
        
        $emailLib = new \App\Libraries\EmailLib();
        return $emailLib->sendEmail($email, $subject, $body);
    }
}