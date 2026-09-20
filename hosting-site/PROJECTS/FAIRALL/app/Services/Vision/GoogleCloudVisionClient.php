<?php

namespace App\Services\Vision;

use App\Contracts\VisionClientInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleCloudVisionClient implements VisionClientInterface
{
    private string $apiKey;

    private int $timeout;

    public function __construct()
    {
        $this->apiKey = config('services.google_cloud_vision.api_key');
        $this->timeout = config('services.google_cloud_vision.timeout', 30);
    }

    public function analyze(string $imagePath, string $language = 'tl'): array
    {
        if (empty($this->apiKey)) {
            throw new \RuntimeException('Google Cloud Vision API key is not configured.');
        }

        $imageData = base64_encode(file_get_contents($imagePath));

        $response = Http::timeout($this->timeout)
            ->retry(2, 5000)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post('https://vision.googleapis.com/v1/images:annotate?key=' . $this->apiKey, [
                'requests' => [[
                    'image' => ['content' => $imageData],
                    'features' => [['type' => 'DOCUMENT_TEXT_DETECTION']],
                    'imageContext' => [
                        'languageHints' => [$language, 'fil', 'en'],
                    ],
                ]],
            ]);

        if ($response->failed()) {
            Log::error('Google Cloud Vision API failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            $response->throw();
        }

        return $response->json();
    }
}
