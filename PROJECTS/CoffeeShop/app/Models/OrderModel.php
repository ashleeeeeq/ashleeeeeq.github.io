<?php
namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $table      = 'orders';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'user_id',
        'menu_id',
        'product_name',
        'size',
        'quantity',
        'price',
        'total',
        'status'
    ];

    protected $useTimestamps = true;
    protected $returnType = 'array';
}