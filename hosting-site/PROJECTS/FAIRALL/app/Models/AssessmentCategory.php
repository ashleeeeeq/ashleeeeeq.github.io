<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssessmentCategory extends Model
{
    use SoftDeletes;
    protected $fillable = ['assessment_name'];

    // relationships

    public function ffaAssessmentRecords(): HasMany
    {
        return $this->hasMany(FfaAssessmentRecord::class);
    }
}
