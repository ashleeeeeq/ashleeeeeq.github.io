<?php

namespace App\Services;

use App\Models\Document;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadService
{
    // Allowed MIME types
    private const ALLOWED_TYPES = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'application/pdf',
    ];

    // Maximum file size in bytes (5 MB)
    private const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5 MB

    /**
     * Validate the uploaded file.
     * 
     * @param UploadedFile $file
     * @return array ['valid' => bool, 'errors' => array]
     */
    public function validate(UploadedFile $file): array
    {
        $errors = [];

        // Check file size
        if ($file->getSize() > self::MAX_FILE_SIZE) {
            $errors[] = 'File size exceeds 5 MB limit.';
        }

        // Check file type
        $mimeType = $file->getMimeType();
        if (!in_array($mimeType, self::ALLOWED_TYPES)) {
            $errors[] = 'File type not supported. Only images (JPG, PNG, GIF, WebP) and PDF files are allowed.';
        }

        // Additional validation: check actual file extension
        $extension = strtolower($file->getClientOriginalExtension());
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf'];
        if (!in_array($extension, $allowedExtensions)) {
            $errors[] = 'Invalid file extension.';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Upload a file to S3 and create a Document record.
     * 
     * @param UploadedFile $file
     * @param int $beneficiary_id
     * @param string|null $display_name
     * @param string|null $source_module
     * @param int|null $source_record_id
     * @param int|null $uploaded_by
     * 
     * @return Document|false
     */
    public function uploadFile(
        UploadedFile $file,
        int $beneficiary_id,
        ?string $display_name = null,
        ?string $source_module = null,
        ?int $source_record_id = null,
        ?int $uploaded_by = null
    ) {
        // Validate file
        $validation = $this->validate($file);
        if (!$validation['valid']) {
            \Log::warning('File validation failed', $validation['errors']);
            return false;
        }

        try {
            // Generate unique file path
            $fileName = Str::random(40) . '.' . strtolower($file->getClientOriginalExtension());
            $path = "documents/beneficiary-{$beneficiary_id}/" . $fileName;

            // Upload to S3
            $storagePath = Storage::disk('s3')->putFileAs(
                "documents/beneficiary-{$beneficiary_id}",
                $file,
                $fileName,
                'private'
            );

            if (!$storagePath) {
                \Log::error('Failed to upload file to S3');
                return false;
            }

            // Create Document record
            $document = Document::create([
                'beneficiary_id' => $beneficiary_id,
                'display_name' => $display_name ?? $file->getClientOriginalName(),
                'file_path' => $storagePath,
                'file_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'source_module' => $source_module,
                'source_record_id' => $source_record_id,
                'uploaded_by' => $uploaded_by,
            ]);

            \Log::info('File uploaded successfully', [
                'document_id' => $document->id,
                'beneficiary_id' => $beneficiary_id,
                'file_path' => $storagePath,
            ]);

            return $document;
        } catch (\Exception $e) {
            \Log::error('File upload failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get allowed file types for display.
     */
    public static function getAllowedFileTypes(): string
    {
        return 'Images (JPG, PNG, GIF, WebP), PDF';
    }

    /**
     * Format file size for display.
     */
    public static function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
