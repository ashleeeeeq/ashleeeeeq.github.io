<?php

namespace App\Models;

use CodeIgniter\Model;

class CartModel extends Model
{
    protected $table = 'cart';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
    'user_id',
    'product_id',
    'variation_id',
    'quantity',
    'price',
     'variation_value', 
    'name'
];

    // Timestamps
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'user_id' => 'required|integer',
        'product_id' => 'required|integer',
        'quantity' => 'required|integer|greater_than[0]'
    ];
    
    protected $validationMessages = [
        'quantity' => [
            'greater_than' => 'Quantity must be at least 1'
        ]
    ];
    
    protected $skipValidation = false;

    /**
     * Get cart items with complete product information
     */
    public function getUserCart($userId)
    {
        return $this->select('cart.*, products.product_name, products.price, products.image, products.stock')
            ->join('products', 'products.id = cart.product_id')
            ->where('cart.user_id', $userId)
            ->findAll();
    }

    /**
     * Get cart total price
     */
    public function getCartTotal($userId)
    {
        $result = $this->select('SUM(products.price * cart.quantity) as total')
            ->join('products', 'products.id = cart.product_id')
            ->where('cart.user_id', $userId)
            ->first();
        
        return $result['total'] ?? 0;
    }

    /**
     * Get cart item count
     */
    public function getCartCount($userId)
    {
        $result = $this->select('SUM(quantity) as total_items')
            ->where('user_id', $userId)
            ->first();
        
        return $result['total_items'] ?? 0;
    }

    /**
     * Check if product exists in cart
     */
    public function productInCart($userId, $productId)
    {
        return $this->where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();
    }

    /**
     * Clear entire cart
     */
    public function clearCart($userId)
    {
        return $this->where('user_id', $userId)->delete();
    }
}