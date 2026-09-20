<?php
namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UserModel;
use App\Models\OrderModel;

class Admin extends Controller
{
    protected $userModel;
    protected $orderModel;

    public function __construct()
    {
        helper(['url', 'form']);

        if (!session()->get('user_id') || session()->get('role') !== 'admin') {
            session()->setFlashdata('error', 'Access denied.');
            redirect()->to('/login')->send();
            exit;
        }

        $this->userModel  = new UserModel();
        $this->orderModel = new OrderModel();
    }

    public function index()
    {
        // USERS pagination
        $users = $this->userModel->paginate(5, 'users');
        $pager = $this->userModel->pager;

        // ORDERS pagination + JOIN users
        $orders = $this->orderModel
            ->select('orders.*, users.name AS user_name')
            ->join('users', 'users.id = orders.user_id', 'left')
            ->paginate(5, 'orders');
        $ordersPager = $this->orderModel->pager;

        // REAL total user count
        $totalUsers = $this->userModel->countAll();

        // REAL total orders
        $totalOrders = $this->orderModel->countAll();

        // Total completed
        $completedOrders = $this->orderModel
            ->where('status', 'Completed')
            ->countAllResults();

        return view('admin_dashboard', [
            'users'           => $users,
            'orders'          => $orders,
            'pager'           => $pager,
            'ordersPager'     => $ordersPager,
            'totalUsers'      => $totalUsers,
            'totalOrders'     => $totalOrders,
            'completedOrders' => $completedOrders
        ]);
    }

    public function edit($id)
    {
        $user = $this->userModel->find($id);

        if (!$user) {
            return redirect()->to('/admin')->with('error', 'User not found.');
        }

        return view('edit_user', ['user' => $user]);
    }

    public function update($id)
    {
        $plain = $this->request->getPost('plain_password');

        // Validate password only if admin typed something new
        if (!empty($plain)) {
            if (
                strlen($plain) < 8 ||
                !preg_match('/[A-Z]/', $plain) ||
                !preg_match('/[a-z]/', $plain) ||
                !preg_match('/\d/', $plain) ||
                !preg_match('/[\W_]/', $plain)
            ) {
                return redirect()->back()->with('error',
                    'Password must contain uppercase, lowercase, number, and special character.'
                );
            }
        }

        // Prepare data
        $data = [
            'name'  => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'role'  => $this->request->getPost('role')
        ];

        // If password changed, update both
        if (!empty($plain)) {
            $data['plain_password'] = $plain;
            $data['password'] = password_hash($plain, PASSWORD_DEFAULT);
        }

        // Update DB via model
        if (!$this->userModel->update($id, $data)) {
            return redirect()->back()->with('error', 'Failed to update user.');
        }

        return redirect()->to('/admin')->with('success', 'User updated successfully.');
    }

    public function updateStatus($orderId)
    {
        $newStatus = $this->request->getPost('status');

        if (!in_array($newStatus, ['Pending', 'Processing', 'Completed'])) {
            return redirect()->back()->with('error', 'Invalid status.');
        }

        $this->orderModel->update($orderId, ['status' => $newStatus]);

        return redirect()->back()->with('success', 'Order status updated.');
    }

    public function delete($id)
    {
        $this->userModel->delete($id);
        return redirect()->to('/admin')->with('success', 'User deleted.');
    }
}