<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

#[Signature('app:ping-gotenberg')]
#[Description('Ping Gotenberg to prevent Render free-tier spin-down')]
class PingGotenberg extends Command
{
    public function handle(): int
    {
        $url = rtrim(config('services.gotenberg.url', 'http://localhost:3000'), '/') . '/health';

        try {
            $response = Http::timeout(10)->get($url);

            if ($response->successful()) {
                $this->info('Gotenberg is healthy.');
                return Command::SUCCESS;
            }

            $this->warn('Gotenberg responded with: ' . $response->status());
            return Command::FAILURE;
        } catch (\Exception $e) {
            Log::warning('Gotenberg ping failed: ' . $e->getMessage());
            $this->error('Gotenberg ping failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
