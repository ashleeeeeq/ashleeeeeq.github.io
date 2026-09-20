<?php
namespace App\Models;

use CodeIgniter\Model;

class ProductVariationModel extends Model
{
    protected $table = 'product_variations';
    protected $primaryKey = 'id';
    protected $allowedFields = ['product_id', 'variation_name', 'variation_value', 'price', 'stock', 'sku'];
    protected $useTimestamps = true;
}