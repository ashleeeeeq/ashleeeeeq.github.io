<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'product_name',
        'description',
        'price',
        'stock',
        'image',
        'category',
        'featured',
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
        'product_name' => 'required|min_length[3]|max_length[255]',
        'description' => 'required|min_length[10]',
        'price' => 'required|numeric|greater_than[0]',
        'stock' => 'required|integer|greater_than_equal_to[0]',
        'category' => 'required|in_list[Gardening,Home & Living,Agriculture,Packaging]',
        'featured' => 'permit_empty|in_list[none,new,trending,best_seller]'
    ];
    
    protected $validationMessages = [
        'product_name' => [
            'required' => 'Product name is required',
            'min_length' => 'Product name must be at least 3 characters'
        ],
        'description' => [
            'required' => 'Product description is required',
            'min_length' => 'Description must be at least 10 characters'
        ],
        'price' => [
            'required' => 'Price is required',
            'numeric' => 'Price must be a number',
            'greater_than' => 'Price must be greater than 0'
        ],
        'stock' => [
            'integer' => 'Stock must be a whole number',
            'greater_than_equal_to' => 'Stock cannot be negative'
        ],
        'category' => [
            'required' => 'Please select a category',
            'in_list' => 'Please select a valid category'
        ]
    ];
    
    protected $skipValidation = false;

    /**
     * Get all featured products with specific types
     */
    public function getFeaturedProducts()
    {
        return $this->where('featured !=', 'none')
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    /**
     * Get new products (for homepage)
     */
    public function getNewProducts($limit = 4)
    {
        return $this->where('featured', 'new')
            ->orWhere('featured', 'none')
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get best selling products
     */
    public function getBestSellers($limit = 4)
    {
        return $this->where('featured', 'best_seller')
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get trending products
     */
    public function getTrendingProducts($limit = 4)
    {
        return $this->where('featured', 'trending')
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get products with stock available
     */
    public function getAvailableProducts()
    {
        return $this->where('stock >', 0)
            ->orderBy('product_name', 'ASC')
            ->findAll();
    }

    /**
     * Get products by category
     */
    public function getByCategory($category)
    {
        return $this->where('category', $category)
            ->where('stock >', 0)
            ->orderBy('product_name', 'ASC')
            ->findAll();
    }

    /**
     * Get products by price range
     */
    public function getByPriceRange($min, $max)
    {
        return $this->where('price >=', $min)
            ->where('price <=', $max)
            ->where('stock >', 0)
            ->orderBy('price', 'ASC')
            ->findAll();
    }

    /**
     * Search products by name or description
     */
    public function searchProducts($keyword)
    {
        return $this->groupStart()
                ->like('product_name', $keyword)
                ->orLike('description', $keyword)
            ->groupEnd()
            ->where('stock >', 0)
            ->orderBy('product_name', 'ASC')
            ->findAll();
    }

    /**
     * Reduce stock after purchase
     */
    public function reduceStock($productId, $quantity)
    {
        $product = $this->find($productId);

        if ($product && $product['stock'] >= $quantity) {
            $newStock = $product['stock'] - $quantity;
            
            return $this->update($productId, [
                'stock' => $newStock
            ]);
        }

        return false;
    }

    /**
     * Increase stock (for cancelled orders)
     */
    public function increaseStock($productId, $quantity)
    {
        $product = $this->find($productId);

        if ($product) {
            $newStock = $product['stock'] + $quantity;
            
            return $this->update($productId, [
                'stock' => $newStock
            ]);
        }

        return false;
    }

    /**
     * Check if product has sufficient stock
     */
    public function hasSufficientStock($productId, $quantity)
    {
        $product = $this->find($productId);
        return $product && $product['stock'] >= $quantity;
    }

    /**
     * Get low stock products (for admin alerts)
     */
    public function getLowStockProducts($threshold = 10)
    {
        return $this->where('stock <', $threshold)
            ->where('stock >', 0)
            ->orderBy('stock', 'ASC')
            ->findAll();
    }

    /**
     * Get out of stock products
     */
    public function getOutOfStockProducts()
    {
        return $this->where('stock', 0)
            ->orderBy('product_name', 'ASC')
            ->findAll();
    }

    /**
     * Get inventory summary
     */
    public function getInventorySummary()
    {
        $products = $this->findAll();
        
        $summary = [
            'total_products' => count($products),
            'total_stock' => 0,
            'total_value' => 0,
            'low_stock_count' => 0,
            'out_of_stock_count' => 0,
            'categories' => []
        ];

        foreach ($products as $product) {
            $summary['total_stock'] += $product['stock'];
            $summary['total_value'] += $product['price'] * $product['stock'];
            
            if ($product['stock'] == 0) {
                $summary['out_of_stock_count']++;
            } elseif ($product['stock'] < 10) {
                $summary['low_stock_count']++;
            }

            // Category summary
            if (!isset($summary['categories'][$product['category']])) {
                $summary['categories'][$product['category']] = [
                    'count' => 0,
                    'stock' => 0,
                    'value' => 0
                ];
            }
            
            $summary['categories'][$product['category']]['count']++;
            $summary['categories'][$product['category']]['stock'] += $product['stock'];
            $summary['categories'][$product['category']]['value'] += $product['price'] * $product['stock'];
        }

        return $summary;
    }

    /**
     * Get product categories with counts
     */
    public function getCategories()
    {
        return $this->select('category, COUNT(*) as product_count, SUM(stock) as total_stock')
            ->groupBy('category')
            ->orderBy('category', 'ASC')
            ->findAll();
    }

    /**
     * Get product by ID with formatted data
     */
    public function getProductDetails($id)
    {
        $product = $this->find($id);
        
        if ($product) {
            $product['formatted_price'] = '₱' . number_format($product['price'], 2);
            $product['stock_status'] = $this->getStockStatus($product['stock']);
            $product['featured_label'] = $this->getFeaturedLabel($product['featured']);
        }
        
        return $product;
    }

    /**
     * Get stock status text
     */
    private function getStockStatus($stock)
    {
        if ($stock <= 0) {
            return ['class' => 'danger', 'text' => 'Out of Stock'];
        } elseif ($stock < 10) {
            return ['class' => 'warning', 'text' => 'Low Stock'];
        } else {
            return ['class' => 'success', 'text' => 'In Stock'];
        }
    }

    /**
     * Get featured label
     */
    private function getFeaturedLabel($featured)
    {
        $labels = [
            'new' => ['class' => 'success', 'text' => 'New'],
            'trending' => ['class' => 'info', 'text' => 'Trending'],
            'best_seller' => ['class' => 'warning', 'text' => 'Best Seller'],
            'none' => ['class' => 'secondary', 'text' => 'Regular']
        ];
        
        return $labels[$featured] ?? ['class' => 'secondary', 'text' => 'Regular'];
    }

    /**
     * Update product featured status
     */
    public function updateFeaturedStatus($productId, $status)
    {
        if (!in_array($status, ['none', 'new', 'trending', 'best_seller'])) {
            return false;
        }
        
        return $this->update($productId, ['featured' => $status]);
    }

    /**
     * Get products for storefront
     */
    public function getStorefrontProducts()
    {
        return [
            'featured' => $this->getFeaturedProducts(),
            'new' => $this->getNewProducts(4),
            'best_sellers' => $this->getBestSellers(4),
            'trending' => $this->getTrendingProducts(4)
        ];
    }

    /**
     * Get variation for products
     */
    public function getVariations($productId)
    {
        return $this->db->table('product_variations')
            ->where('product_id', $productId)
            ->orderBy('price', 'ASC')
            ->get()
            ->getResultArray();
    }
}