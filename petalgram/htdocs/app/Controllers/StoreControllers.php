<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\UserModel;

class StoreControllers extends BaseController
{
    public function index()
    {
        $productModel = new ProductModel();
        
        // Get featured products for homepage sections
        $data = $this->getHomepageProducts($productModel);
        
        // Get categories with counts for sidebar or filtering
        $data['categories'] = $productModel->getCategories();
        
        // Get random products for "You Might Also Like" section
        $data['recommended'] = $productModel
            ->where('stock >', 0)
            ->orderBy('RAND()')
            ->limit(4)
            ->findAll();
        
        return view('storefront', $data);
    }

    /**
     * Get products for homepage sections (new, best sellers, trending)
     */
    private function getHomepageProducts($productModel)
    {
        $data = [];

        // Get new products (exactly 3)
        $data['newProducts'] = $productModel
            ->where('featured', 'new')
            ->orderBy('created_at', 'DESC')
            ->limit(3)
            ->findAll();

        // If less than 3, get additional non-featured products
        if (count($data['newProducts']) < 3) {
            $additional = $productModel
                ->where('featured', 'none')
                ->orderBy('created_at', 'DESC')
                ->limit(3 - count($data['newProducts']))
                ->findAll();
            $data['newProducts'] = array_merge($data['newProducts'], $additional);
        }

        // Get best sellers (exactly 3)
        $data['bestSellers'] = $productModel
            ->where('featured', 'best_seller')
            ->orderBy('created_at', 'DESC')
            ->limit(3)
            ->findAll();

        // If less than 3, get additional products
        if (count($data['bestSellers']) < 3) {
            $additional = $productModel
                ->where('featured', 'none')
                ->orderBy('created_at', 'DESC')
                ->limit(3 - count($data['bestSellers']))
                ->findAll();
            $data['bestSellers'] = array_merge($data['bestSellers'], $additional);
        }

        // Get trending products (exactly 3)
        $data['trending'] = $productModel
            ->where('featured', 'trending')
            ->orderBy('created_at', 'DESC')
            ->limit(3)
            ->findAll();

        // If less than 3, get additional products
        if (count($data['trending']) < 3) {
            $additional = $productModel
                ->where('featured', 'none')
                ->orderBy('created_at', 'DESC')
                ->limit(3 - count($data['trending']))
                ->findAll();
            $data['trending'] = array_merge($data['trending'], $additional);
        }

        return $data;
    }

    /**
     * Display all products with filtering options
     */
    public function products()
    {
        $productModel = new ProductModel();
        
        // Get filter parameters
        $category = $this->request->getGet('category');
        $minPrice = $this->request->getGet('min_price');
        $maxPrice = $this->request->getGet('max_price');
        $sort = $this->request->getGet('sort') ?? 'newest';
        $search = $this->request->getGet('search');
        
        // Build query
        $query = $productModel->where('stock >', 0);
        
        // Apply category filter
        if ($category && $category != 'all') {
            $query->where('category', $category);
        }
        
        // Apply price range filter
        if ($minPrice && is_numeric($minPrice)) {
            $query->where('price >=', $minPrice);
        }
        if ($maxPrice && is_numeric($maxPrice)) {
            $query->where('price <=', $maxPrice);
        }
        
        // Apply search
        if ($search) {
            $query->groupStart()
                ->like('product_name', $search)
                ->orLike('description', $search)
                ->groupEnd();
        }
        
        // Apply sorting
        switch ($sort) {
            case 'price_low':
                $query->orderBy('price', 'ASC');
                break;
            case 'price_high':
                $query->orderBy('price', 'DESC');
                break;
            case 'name_asc':
                $query->orderBy('product_name', 'ASC');
                break;
            case 'name_desc':
                $query->orderBy('product_name', 'DESC');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'DESC');
                break;
        }
        
        // Pagination
        $data['products'] = $query->paginate(12);
        $data['pager'] = $productModel->pager;
        
        // Get categories for filter sidebar
        $data['categories'] = $productModel->getCategories();
        
        // Get price ranges for filter
        $priceStats = $productModel
            ->select('MIN(price) as min_price, MAX(price) as max_price')
            ->first();
        $data['minPrice'] = $priceStats['min_price'] ?? 0;
        $data['maxPrice'] = $priceStats['max_price'] ?? 10000;
        
        // Pass filter values back to view
        $data['selectedCategory'] = $category;
        $data['selectedMinPrice'] = $minPrice;
        $data['selectedMaxPrice'] = $maxPrice;
        $data['selectedSort'] = $sort;
        $data['searchTerm'] = $search;
        
        return view('products_list', $data);
    }

    /**
     * Display products by category
     */
    public function category($category)
    {
        $productModel = new ProductModel();
        
        // Decode URL-friendly category name
        $category = str_replace('-', ' ', $category);
        
        $data['products'] = $productModel->getByCategory($category);
        $data['category'] = $category;
        
        // Get related categories
        $data['categories'] = $productModel->getCategories();
        
        return view('category_products', $data);
    }

    /**
     * Search products
     */
    public function search()
    {
        $keyword = $this->request->getGet('q');
        
        if (!$keyword) {
            return redirect()->to('/products');
        }
        
        $productModel = new ProductModel();
        $data['products'] = $productModel->searchProducts($keyword);
        $data['keyword'] = $keyword;
        $data['count'] = count($data['products']);
        
        return view('search_results', $data);
    }

    /**
     * Display user profile
     */
    public function profile()
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Please login to view your profile');
        }

        $userModel = new UserModel();
        $data['user'] = $userModel->find(session()->get('id'));
        
        // Get user's order history
        $orderModel = new \App\Models\OrderModel();
        $data['orders'] = $orderModel->where('user_id', session()->get('id'))
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->findAll();

        return view('customer_profile', $data);
    }

    /**
     * Update user profile
     */
    public function updateProfile()
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Please login to update your profile');
        }

        $rules = [
            'fullname' => 'required|min_length[3]|max_length[100]',
            'address' => 'required|min_length[5]|max_length[500]',
            'mobile' => 'required|min_length[11]|max_length[15]|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userModel = new UserModel();
        
        $updateData = [
            'fullname' => $this->request->getPost('fullname'),
            'address' => $this->request->getPost('address'),
            'mobile' => $this->request->getPost('mobile')
        ];
        
        // Handle profile picture upload if provided
        $profilePic = $this->request->getFile('profile_picture');
        if ($profilePic && $profilePic->isValid() && !$profilePic->hasMoved()) {
            $newName = $profilePic->getRandomName();
            $profilePic->move('uploads/profiles', $newName);
            $updateData['profile_picture'] = $newName;
            
            // Delete old profile picture if exists
            $oldUser = $userModel->find(session()->get('id'));
            if ($oldUser && $oldUser['profile_picture'] && file_exists('uploads/profiles/' . $oldUser['profile_picture'])) {
                unlink('uploads/profiles/' . $oldUser['profile_picture']);
            }
        }

        $userModel->update(session()->get('id'), $updateData);

        // Update session
        session()->set('fullname', $this->request->getPost('fullname'));

        return redirect()->to('/profile')->with('success', 'Profile updated successfully');
    }

    /**
     * Change password
     */
    public function changePassword()
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $rules = [
            'current_password' => 'required',
            'new_password' => 'required|min_length[8]',
            'confirm_password' => 'required|matches[new_password]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $userModel = new UserModel();
        $user = $userModel->find(session()->get('id'));

        // Verify current password
        if (!password_verify($this->request->getPost('current_password'), $user['password'])) {
            return redirect()->back()->with('error', 'Current password is incorrect');
        }

        // Update password
        $userModel->update(session()->get('id'), [
            'password' => password_hash($this->request->getPost('new_password'), PASSWORD_DEFAULT)
        ]);

        return redirect()->to('/profile')->with('success', 'Password changed successfully');
    }

    /**
     * View order history
     */
    public function orders()
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $orderModel = new \App\Models\OrderModel();
        $data['orders'] = $orderModel->getUserOrders(session()->get('id'));
        
        return view('order_history', $data);
    }

    /**
     * View specific order details
     */
    public function orderDetails($orderId)
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $orderModel = new \App\Models\OrderModel();
        $order = $orderModel->getOrderDetails($orderId);
        
        // Verify order belongs to user
        if (!$order || $order['user_id'] != session()->get('id')) {
            return redirect()->to('/orders')->with('error', 'Order not found');
        }

        $data['order'] = $order;
        return view('order_details', $data);
    }

    /**
     * Get products by price range (AJAX endpoint)
     */
    public function filterByPrice()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/products');
        }

        $min = $this->request->getPost('min');
        $max = $this->request->getPost('max');

        $productModel = new ProductModel();
        $products = $productModel->getByPriceRange($min, $max);

        return $this->response->setJSON([
            'success' => true,
            'products' => $products
        ]);
    }

    /**
     * Get quick product info for modal (AJAX endpoint)
     */
    public function quickView($productId)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/products');
        }

        $productModel = new ProductModel();
        $product = $productModel->getProductDetails($productId);

        if (!$product) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Product not found'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'product' => $product
        ]);
    }

    /**
     * Get new arrivals (for API/widget)
     */
    public function newArrivals()
    {
        $productModel = new ProductModel();
        $products = $productModel->getNewProducts(8);

        return $this->response->setJSON([
            'success' => true,
            'products' => $products
        ]);
    }

    /**
     * Get best sellers (for API/widget)
     */
    public function bestSellers()
    {
        $productModel = new ProductModel();
        $products = $productModel->getBestSellers(8);

        return $this->response->setJSON([
            'success' => true,
            'products' => $products
        ]);
    }

    /**
     * Get trending products (for API/widget)
     */
    public function trending()
    {
        $productModel = new ProductModel();
        $products = $productModel->getTrendingProducts(8);

        return $this->response->setJSON([
            'success' => true,
            'products' => $products
        ]);
    }
}