<?php

namespace App\Contracts;

interface VisionClientInterface
{
    public function analyze(string $imagePath, string $language = 'tl'): array;
}
