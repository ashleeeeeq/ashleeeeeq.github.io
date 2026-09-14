<?php
namespace App\Controllers;

use CodeIgniter\Controller;
use Config\Database;
use App\Models\OrderModel;

class Order extends Controller
{
    protected $db;
    protected $orderModel;

    public function __construct()
    {
        helper(['url', 'form']);

        $this->db = Database::connect();
        $this->orderModel = new OrderModel();

        // Session protection
        if (!session()->get('user_id') || session()->get('role') !== 'user') {
            session()->setFlashdata('error', 'Please log in first.');
            header('Location: ' . base_url('/login'));
            exit;
        }
    }

    // ORDER PAGE + PAGINATION
    public function index()
    {
        $user_id = session()->get('user_id');
        $perPage = 5;

        // Get paginated orders
        $orders = $this->orderModel
            ->where('user_id', $user_id)
            ->orderBy('id', 'DESC')
            ->paginate($perPage, 'orders');

        // Pager instance
        $pager = $this->orderModel->pager;

        // Load menu items
        $menu = $this->db->table('menu')->get()->getResultArray();

        return view('order', [
            'menu'   => $menu,
            'orders' => $orders,
            'pager'  => $pager,   
        ]);
    }

    // CREATE ORDER
    public function create()
    {
        $user_id = session()->get('user_id');

        $menu_id  = $this->request->getPost('menu_id');
        $quantity = (int)$this->request->getPost('quantity');
        $size     = $this->request->getPost('size');

        if ($quantity < 1) {
            return redirect()->back()->with('error', 'Quantity must be at least 1.');
        }

        // Load menu item
        $menuItem = $this->db->table('menu')->where('id', $menu_id)->get()->getRowArray();

        if (!$menuItem) {
            return redirect()->back()->with('error', 'Invalid coffee selected.');
        }

        // Correct price mapping (IMPORTANT)
        $priceMap = [
            'tipid_shot'   => $menuItem['price_tipid_shot'],
            'tamang_tama'  => $menuItem['price_tamang_tama'],
            'todo_busog'   => $menuItem['price_todo_busog']
        ];

        // Validate size
        if (!isset($priceMap[$size])) {
            return redirect()->back()->with('error', 'Invalid size selection.');
        }

        $finalPrice = $priceMap[$size];
        $total = $finalPrice * $quantity;

        // Save to DB
        $this->orderModel->insert([
            'user_id'      => $user_id,
            'menu_id'      => $menu_id,
            'product_name' => $menuItem['product_name'],
            'size'         => $size,
            'quantity'     => $quantity,
            'price'        => $finalPrice,
            'total'        => $total,
            'status'       => 'Pending'
        ]);

        return redirect()->to('/order')->with('success', 'Order placed!');
    }
}