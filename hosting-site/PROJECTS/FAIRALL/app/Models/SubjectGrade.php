<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubjectGrade extends Model
{
    use HasFactory, SoftDeletes;

    // relationships

    public function academicRecord(): BelongsTo
    {
        return $this->belongsTo(AcademicRecord::class);
    }

    protected $fillable = [
        'academic_record_id',
        'subject_name',
        'grade',
    ];

    protected $casts = [
        'grade' => 'decimal:2',
    ];
}
