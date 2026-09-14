<?php

namespace App\Services\Exports;

use App\Models\Donor;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class XeroExportService
{
    /**
     * Canonical Xero-compatible headers in required order.
     */
    public function headers(): array
    {
        return ['Date', 'Amount', 'Payee', 'Description', 'Reference', 'Check Number'];
    }

    /**
     * Build Xero rows using completed donations only.
     */
    public function rows(Collection $donations, ?Donor $donor = null): array
    {
        return $donations
            ->filter(fn ($donation) => strtolower((string) $donation->status) === 'completed')
            ->values()
            ->map(function ($donation) use ($donor): array {
                $reference = $donation->gateway_reference
                    ?? $donation->receipt_number
                    ?? $donation->reference_number
                    ?? (string) $donation->id;

                $payee = $donor?->display_name
                    ?? $donation->donor?->display_name
                    ?? 'Anonymous';

                $descriptionParts = array_filter([
                    $donation->donation_type ? ucfirst($donation->donation_type) . ' donation' : null,
                    $donation->program?->program_name,
                    $donation->gateway ? 'Gateway: ' . strtoupper($donation->gateway) : null,
                ]);

                return [
                    optional($donation->transaction_date)->format('Y-m-d'),
                    number_format((float) $donation->amount, 2, '.', ''),
                    $payee,
                    implode(' | ', $descriptionParts),
                    $reference,
                    '',
                ];
            })
            ->all();
    }

    public function streamDownload(Collection $donations, ?Donor $donor, string $filename): StreamedResponse
    {
        $headers = $this->headers();
        $rows = $this->rows($donations, $donor);

        $callback = function () use ($headers, $rows): void {
            $out = fopen('php://output', 'w');
            fputcsv($out, $headers);

            foreach ($rows as $row) {
                fputcsv($out, $row);
            }

            fclose($out);
        };

        return response()->streamDownload($callback, $filename, ['Content-Type' => 'text/csv']);
    }
}
