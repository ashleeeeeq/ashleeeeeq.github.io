<?php
namespace App\Controllers;

use App\Models\UserModel;
use App\Models\ProductModel;
use App\Models\OrderModel;

class CustomerControllers extends BaseController
{
    public function dashboard()
    {
        // Session Validation: Check if user is logged in and has customer role
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'customer') {
            return redirect()->to('/login');
        }

        // Get user data
        $userModel = new UserModel();
        $productModel = new ProductModel();
        $orderModel = new OrderModel();

        $data['user'] = $userModel->find(session()->get('id'));

        // Get featured products
        $data['featuredProducts'] = $productModel
            ->where('featured !=', 'none')
            ->limit(4)
            ->findAll();

        // Get recent orders
        $data['recentOrders'] = $orderModel
            ->where('user_id', session()->get('id'))
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->findAll();

        // Get statistics
        $data['totalOrders'] = $orderModel
            ->where('user_id', session()->get('id'))
            ->countAllResults();

        $data['pendingOrders'] = $orderModel
            ->where('user_id', session()->get('id'))
            ->where('status', 'pending')
            ->countAllResults();

        return view('customer_dashboard', $data);
    }

    public function profile()
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $userModel = new UserModel();
        $data['user'] = $userModel->find(session()->get('id'));

        return view('customer_profile', $data);
    }

    public function updateProfile()
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'fullname' => 'required|min_length[3]|max_length[100]',
            'address' => 'required|min_length[5]|max_length[255]',
            'mobile' => 'required|min_length[11]|max_length[15]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $userModel = new UserModel();
        $userModel->update(session()->get('id'), [
            'fullname' => $this->request->getPost('fullname'),
            'address' => $this->request->getPost('address'),
            'mobile' => $this->request->getPost('mobile')
        ]);

        // Update session
        session()->set('fullname', $this->request->getPost('fullname'));

        return redirect()->to('/profile')->with('success', 'Profile updated successfully');
    }
}