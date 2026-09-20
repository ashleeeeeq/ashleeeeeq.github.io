<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderItemsModel extends Model
{
    protected $table = 'order_items';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'order_id',
        'product_id',
        'quantity',
        'price'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'order_id' => 'required|integer',
        'product_id' => 'required|integer',
        'quantity' => 'required|integer|greater_than[0]',
        'price' => 'required|numeric|greater_than[0]'
    ];
    
    protected $validationMessages = [
        'price' => [
            'greater_than' => 'Price must be greater than 0'
        ]
    ];
    
    protected $skipValidation = false;

    /**
     * Get items in an order with product details
     */
    public function getOrderItems($orderId)
    {
        return $this->select('order_items.*, products.product_name, products.image, products.description')
            ->join('products', 'products.id = order_items.product_id')
            ->where('order_id', $orderId)
            ->findAll();
    }

    /**
     * Get multiple orders items at once
     */
    public function getMultipleOrdersItems(array $orderIds)
    {
        if (empty($orderIds)) {
            return [];
        }

        return $this->select('order_items.*, products.product_name, products.image')
            ->join('products', 'products.id = order_items.product_id')
            ->whereIn('order_id', $orderIds)
            ->findAll();
    }

    /**
     * Get order subtotal
     */
    public function getOrderSubtotal($orderId)
    {
        $result = $this->select('SUM(price * quantity) as subtotal')
            ->where('order_id', $orderId)
            ->first();
        
        return $result['subtotal'] ?? 0;
    }

    /**
     * Get items count for an order - FIXED: Using correct method name and return format
     */
    public function getItemCount($orderId)
    {
        $result = $this->select('SUM(quantity) as total_items')
            ->where('order_id', $orderId)
            ->first();
        
        return $result['total_items'] ?? 0;
    }

    /**
     * Add items to an order (bulk insert)
     */
    public function addOrderItems($orderId, array $items)
    {
        $data = [];
        foreach ($items as $item) {
            $data[] = [
                'order_id' => $orderId,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price']
            ];
        }

        return $this->insertBatch($data);
    }

    /**
     * Get best selling products
     */
    public function getBestSellingProducts($limit = 10)
    {
        return $this->select('products.id, products.product_name, products.image, SUM(order_items.quantity) as total_sold')
            ->join('products', 'products.id = order_items.product_id')
            ->groupBy('product_id')
            ->orderBy('total_sold', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get daily sales by items
     */
    public function getDailyItemsSales($date = null)
    {
        if (!$date) {
            $date = date('Y-m-d');
        }

        return $this->select('products.id, products.product_name, SUM(order_items.quantity) as quantity_sold, SUM(order_items.price * order_items.quantity) as revenue')
            ->join('orders', 'orders.id = order_items.order_id')
            ->join('products', 'products.id = order_items.product_id')
            ->where('DATE(orders.created_at)', $date)
            ->where('orders.status !=', 'cancelled')
            ->groupBy('product_id')
            ->findAll();
    }

    /**
     * Get monthly sales by items
     */
    public function getMonthlyItemsSales($month = null)
    {
        if (!$month) {
            $month = date('Y-m');
        }

        return $this->select('products.id, products.product_name, SUM(order_items.quantity) as quantity_sold, SUM(order_items.price * order_items.quantity) as revenue')
            ->join('orders', 'orders.id = order_items.order_id')
            ->join('products', 'products.id = order_items.product_id')
            ->like('orders.created_at', $month, 'after')
            ->where('orders.status !=', 'cancelled')
            ->groupBy('product_id')
            ->findAll();
    }

    /**
     * Delete items for a specific order (useful for order cancellation)
     */
    public function deleteOrderItems($orderId)
    {
        return $this->where('order_id', $orderId)->delete();
    }

    /**
     * Check if product exists in any order
     */
    public function isProductInOrders($productId)
    {
        return $this->where('product_id', $productId)->countAllResults() > 0;
    }
}