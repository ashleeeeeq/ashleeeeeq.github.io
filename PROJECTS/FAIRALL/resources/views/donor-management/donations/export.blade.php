<x-dashboardlayout name="{{ $name }}" title="Export Donations">
    <div class="max-w-4xl mx-auto space-y-6">
        <div>
            <a href="{{ route('donors.donations.index', $donor) }}" class="text-sm text-white/60 hover:text-white transition-colors">Back to donations</a>
            <h1 class="mt-2 text-3xl md:text-4xl font-bold text-white">Export Donations ({{ strtoupper($format) }})</h1>
            <p class="text-white/70 mt-2">PDF export placeholder — implement PDF generation as needed.</p>
        </div>

        <div class="rounded-2xl p-6" style="background-color: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08);">
            <ul>
                @foreach ($donations as $donation)
                    <li class="py-2 border-b border-white/8">
                        {{ optional($donation->transaction_date)->format('Y-m-d') }}
                        — {{ number_format((float) $donation->amount, 2) }} {{ $donation->currency }}
                        — {{ $donation->donation_type ? ucfirst($donation->donation_type) : ucfirst($donation->status) }}
                        — {{ $donation->gateway_reference ?? $donation->reference_number ?? $donation->receipt_number ?? ('Donation #' . $donation->id) }}
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</x-dashboardlayout>
