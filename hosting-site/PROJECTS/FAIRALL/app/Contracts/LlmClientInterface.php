<?php

namespace App\Contracts;

interface LlmClientInterface
{
    public function generate(string $systemPrompt, string $userMessage, array $context = []): string;

    public function generateBatch(array $prompts): array;
}
