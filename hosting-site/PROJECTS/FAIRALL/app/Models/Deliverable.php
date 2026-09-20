<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['donor_id', 'grant_id', 'title', 'description', 'start_date', 'end_date', 'completed_at', 'progress', 'created_by', 'updated_by'])]
class Deliverable extends Model
{
    protected $table = 'deliverables';

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'completed_at' => 'datetime',
            'progress' => 'integer',
        ];
    }

    public function donor(): BelongsTo
    {
        return $this->belongsTo(Donor::class);
    }

    public function grant(): BelongsTo
    {
        return $this->belongsTo(Grant::class);
    }
}