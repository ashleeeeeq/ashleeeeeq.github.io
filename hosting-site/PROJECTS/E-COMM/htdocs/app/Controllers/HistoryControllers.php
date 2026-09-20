<?php

namespace App\Controllers;

use App\Models\OrderModel;
use App\Models\OrderItemsModel;
use App\Models\ProductModel;

class HistoryControllers extends BaseController
{
    public function index()
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Please login to view your transaction history');
        }

        $orderModel = new OrderModel();
        
        // Get user's orders with item counts
        $orders = $orderModel->getUserOrders(session()->get('id'));
        
        // Add item counts to each order
        $orderItemsModel = new OrderItemsModel();
        foreach ($orders as &$order) {
            $items = $orderItemsModel->where('order_id', $order['id'])->findAll();
            $order['item_count'] = count($items);
            $order['status_label'] = $orderModel->getStatusLabel($order['status']);
            $order['status_badge'] = $orderModel->getStatusBadgeClass($order['status']);
        }
        
        $data['transactions'] = $orders;
        
        return view('transaction_history', $data);
    }

    public function details($orderId)
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Please login to view order details');
        }

        $orderModel = new OrderModel();
        $order = $orderModel->getOrderWithItems($orderId);
        
        // Verify order belongs to user
        if (!$order || $order['user_id'] != session()->get('id')) {
            return redirect()->to('/transactions')->with('error', 'Order not found');
        }

        $order['status_label'] = $orderModel->getStatusLabel($order['status']);
        $order['status_badge'] = $orderModel->getStatusBadgeClass($order['status']);
        $order['can_cancel'] = $orderModel->canCancel($orderId);
        $order['can_receive'] = $orderModel->canReceive($orderId);
        $order['can_pickup'] = $orderModel->canPickup($orderId);
        
        $data['order'] = $order;
        
        return view('order_details', $data);
    }

    public function cancel($orderId)
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Please login to cancel order');
        }

        $orderModel = new OrderModel();
        $order = $orderModel->find($orderId);
        
        // Verify order belongs to user and is cancellable
        if (!$order || $order['user_id'] != session()->get('id')) {
            return redirect()->to('/transactions')->with('error', 'Order not found');
        }

        if (!$orderModel->canCancel($orderId)) {
            return redirect()->to('/transactions')->with('error', 'This order cannot be cancelled at this time');
        }

        // Update order status to cancelled
        $orderModel->update($orderId, ['status' => OrderModel::STATUS_CANCELLED]);

        // Restore stock
        $orderItemsModel = new OrderItemsModel();
        $items = $orderItemsModel->where('order_id', $orderId)->findAll();
        
        $productModel = new ProductModel();
        foreach ($items as $item) {
            $product = $productModel->find($item['product_id']);
            if ($product) {
                $productModel->update($item['product_id'], [
                    'stock' => $product['stock'] + $item['quantity']
                ]);
            }
        }

        return redirect()->to('/transactions')->with('success', 'Order cancelled successfully');
    }

    public function receive($orderId)
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Please login to confirm receipt');
        }

        $orderModel = new OrderModel();
        $order = $orderModel->find($orderId);
        
        // Verify order belongs to user and can be received
        if (!$order || $order['user_id'] != session()->get('id')) {
            return redirect()->to('/transactions')->with('error', 'Order not found');
        }

        if (!$orderModel->canReceive($orderId)) {
            return redirect()->to('/transactions')->with('error', 'Cannot confirm receipt at this time');
        }

        // Update order status to completed
        $orderModel->updateStatus($orderId, OrderModel::STATUS_COMPLETED);
        
        return redirect()->to('/transactions')->with('success', 'Order marked as received! Thank you for your purchase.');
    }

    public function pickup($orderId)
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Please login to confirm pickup');
        }

        $orderModel = new OrderModel();
        $order = $orderModel->find($orderId);
        
        // Verify order belongs to user and can be picked up
        if (!$order || $order['user_id'] != session()->get('id')) {
            return redirect()->to('/transactions')->with('error', 'Order not found');
        }

        if (!$orderModel->canPickup($orderId)) {
            return redirect()->to('/transactions')->with('error', 'Cannot confirm pickup at this time');
        }

        // Update order status to completed
        $orderModel->updateStatus($orderId, OrderModel::STATUS_COMPLETED);
        
        return redirect()->to('/transactions')->with('success', 'Pickup confirmed! Thank you for shopping with CREATRIX.');
    }
}