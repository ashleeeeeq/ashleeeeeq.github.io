<?php

namespace App\Models;

use App\Models\Concerns\HasFormattedContact;
use App\Models\Concerns\HasPhoneNumber;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;


#[Fillable(['organization_name','email','contact_number','dial_code','grant_name', 'description', 'total_amount', 'start_date', 'end_date', 'created_by', 'updated_by'])]
class Grant extends Model
{
    use HasFormattedContact, HasPhoneNumber, SoftDeletes;
    protected $table = 'grants';

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
            'deleted_at' => 'datetime',
        ];
    }

    public function programs(): BelongsToMany
    {
        return $this->belongsToMany(Program::class, 'grant_program')
            ->withTimestamps();
    }

    public function deliverables(): HasMany
    {
        return $this->hasMany(Deliverable::class);
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(Allocation::class);
    }
    
}
