<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonitoringConfiguration extends Model
{
    protected $fillable = [
        'name',
        'threshold',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
