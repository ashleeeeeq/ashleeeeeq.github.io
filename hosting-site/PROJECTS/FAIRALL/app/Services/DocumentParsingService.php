<?php

namespace App\Services;

use App\Contracts\VisionClientInterface;
use App\Services\Parsers\AbstractIntakeSheetParser;
use App\Services\Parsers\EducationIntakeParser;
use App\Services\Parsers\SportsIntakeParser;
use App\DTOs\ParsedDocument;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DocumentParsingService
{
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    public function __construct(
        private readonly VisionClientInterface $vision,
    ) {}

    public function parse(array $files, string $programType): ParsedDocument
    {
        $tempDir = storage_path('app/tmp/ocr');
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $tempPaths = [];

        try {
            $rawText = '';

            foreach ($files as $file) {
                $tempPath = $tempDir . '/' . Str::random(40) . '.' . $file->getClientOriginalExtension();
                $file->move($tempDir, basename($tempPath));
                $tempPaths[] = $tempPath;

                $imagePath = $this->ensureImageFile($tempPath);
                $result = $this->vision->analyze($imagePath);
                $text = $this->extractTextFromResponse($result);
                $rawText .= $text . "\n";
            }

            $parser = $this->resolveParser($programType);

            return $parser->parse($rawText);

        } catch (\Throwable $e) {
            Log::error('Document parsing failed', [
                'error' => $e->getMessage(),
                'program_type' => $programType,
                'file_count' => count($files),
            ]);
            throw $e;

        } finally {
            foreach ($tempPaths as $path) {
                $this->cleanup($path);
            }
        }
    }

    private function resolveParser(string $programType): AbstractIntakeSheetParser
    {
        return match ($programType) {
            'education' => new EducationIntakeParser(),
            'sports' => new SportsIntakeParser(),
            default => throw new \InvalidArgumentException("Unknown program type: {$programType}"),
        };
    }

    private function extractTextFromResponse(array $response): string
    {
        $annotations = $response['responses'][0]['fullTextAnnotation'] ?? null;

        if ($annotations === null) {
            Log::warning('Google Cloud Vision returned no text annotations');
            return '';
        }

        return $annotations['text'] ?? '';
    }

    private function ensureImageFile(string $filePath): string
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        if (!in_array($extension, self::ALLOWED_EXTENSIONS, true)) {
            throw new \InvalidArgumentException(
                'Auto-fill only supports JPG/PNG images. ' .
                'Upload the intake sheet pages as individual images. ' .
                'PDF files are accepted for regular document upload only.'
            );
        }

        return $filePath;
    }

    private function cleanup(string $path): void
    {
        if (file_exists($path)) {
            @unlink($path);
        }
    }
}
