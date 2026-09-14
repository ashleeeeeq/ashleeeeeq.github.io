<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Staff;

class Allocation extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'amount_cents' => 'integer',
        'date_allocated' => 'date',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    protected $fillable = [
        'donation_id',
        'grant_id',
        'beneficiary_id',
        'amount_cents',
        'date_allocated',
        'created_by',
        'updated_by',
        'notes',
    ];

    public function donation()
    {
        return $this->belongsTo(Donation::class);
    }

    public function grant()
    {
        return $this->belongsTo(Grant::class);
    }

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(Staff::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(Staff::class, 'updated_by');
    }
}
