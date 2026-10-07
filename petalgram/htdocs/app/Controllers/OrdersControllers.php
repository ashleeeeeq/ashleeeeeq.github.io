<?php

namespace App\Controllers;

use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\OrderItemsModel;

class OrdersControllers extends BaseController
{
    public function history()
    {
        // Check if user is logged in
        $redirect = $this->requireLogin();
        if ($redirect) return $redirect;

        $orderModel = new OrderModel();
        $orderItemModel = new OrderItemsModel();

        // Get user orders
        $orders = $orderModel
            ->where('user_id', session()->get('id'))
            ->orderBy('created_at', 'DESC')
            ->findAll();

        // Get order items for each order
        foreach ($orders as &$order) {
            $order['items'] = $orderItemModel
                ->select('order_items.*, products.product_name, products.image')
                ->join('products', 'products.id = order_items.product_id')
                ->where('order_id', $order['id'])
                ->findAll();
        }

        $data['orders'] = $orders;

        return view('transactions', $data);
    }

    public function details($id)
    {
        // Check if user is logged in
        $redirect = $this->requireLogin();
        if ($redirect) return $redirect;

        $orderModel = new OrderModel();
        $orderItemModel = new OrderItemsModel();

        // Get order
        $order = $orderModel->find($id);

        // Verify ownership
        if (!$order || $order['user_id'] != session()->get('id')) {
            return redirect()->to('/transactions')->with('error', 'Order not found');
        }

        // Get order items
        $order['items'] = $orderItemModel
            ->select('order_items.*, products.product_name, products.image, products.description')
            ->join('products', 'products.id = order_items.product_id')
            ->where('order_id', $id)
            ->findAll();

        $data['order'] = $order;

        return view('order_details', $data);
    }

    public function cancel($id)
    {
        // Check if user is logged in
        $redirect = $this->requireLogin();
        if ($redirect) return $redirect;

        $orderModel = new OrderModel();

        // Get order
        $order = $orderModel->find($id);

        // Verify ownership and status
        if (!$order || $order['user_id'] != session()->get('id')) {
            return redirect()->to('/transactions')->with('error', 'Order not found');
        }

        if ($order['status'] !== 'pending') {
            return redirect()->back()->with('error', 'Only pending orders can be cancelled');
        }

        $orderModel->update($id, ['status' => 'cancelled']);

        return redirect()->to('/transactions')->with('success', 'Order cancelled successfully');
    }
}