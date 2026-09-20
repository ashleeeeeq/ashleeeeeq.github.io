<?php

namespace App\Models;

use CodeIgniter\Model;

class MenuModel extends Model
{
    protected $table = 'menu';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'product_name',
        'image',
        'price_tipid_shot',
        'price_tamang_tama',
        'price_todo_busog'
    ];
}