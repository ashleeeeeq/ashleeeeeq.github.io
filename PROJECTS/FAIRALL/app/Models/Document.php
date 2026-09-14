<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class Document extends Model
{
    public $timestamps = true;

    protected $table = 'documents';

    protected $fillable = [
        'beneficiary_id',
        'display_name',
        'file_path',
        'file_type',
        'file_size',
        'source_module',
        'source_record_id',
        'uploaded_by',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    // Relationships

    public function beneficiary(): BelongsTo
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function uploadedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function sourceable(): MorphTo
    {
        return $this->morphTo('sourceable', 'source_module', 'source_record_id');
    }

    public function getSourceLabelAttribute(): string
    {
        return match ($this->source_module) {
            'academic_record' => 'Academic Record',
            'ffa_assessment' => 'FFA Assessment',
            'injury_record' => 'Injury Record',
            'beneficiary' => 'Beneficiary',
            default => $this->source_module ? ucfirst(str_replace('_', ' ', $this->source_module)) : 'Manual Upload',
        };
    }

    // Methods

    /**
     * Get a signed/temporary URL for downloading the file.
     */
    public function getDownloadUrl(): string
    {
        $disk = Storage::disk('s3');

        return call_user_func(
            [$disk, 'temporaryUrl'],
            $this->file_path,
            now()->addMinutes(60) // URL expires in 60 minutes
        );
    }

    /**
     * Get a signed URL for previewing the file (used in image/PDF viewers).
     */
    public function getPreviewUrl(): string
    {
        return $this->getDownloadUrl();
    }

    /**
     * Check if file is an image.
     */
    public function isImage(): bool
    {
        return in_array($this->file_type, ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
    }

    /**
     * Check if file is a PDF.
     */
    public function isPdf(): bool
    {
        return $this->file_type === 'application/pdf';
    }

    /**
     * Delete the file from storage and the database record.
     */
    public function deleteFile(): bool
    {
        try {
            Storage::disk('s3')->delete($this->file_path);
            $this->delete();
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to delete document: ' . $e->getMessage());
            return false;
        }
    }
}
