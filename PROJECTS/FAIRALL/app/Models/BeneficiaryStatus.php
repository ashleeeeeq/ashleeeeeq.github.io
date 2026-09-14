<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BeneficiaryStatus extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $fillable = [
        'beneficiary_id',
        'beneficiary_status_type_id',
        'start_date',
        'end_date',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function beneficiary(): BelongsTo
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function statusType(): BelongsTo
    {
        return $this->belongsTo(BeneficiaryStatusType::class, 'beneficiary_status_type_id');
    }
}
