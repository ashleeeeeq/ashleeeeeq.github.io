<x-dashboardlayout name="{{ $name }}" title="Edit Donation">
    @include('donor-management.donations.form', [
        'backUrl' => route('donors.donations.index', $donor),
        'cancelUrl' => route('donors.donations.index', $donor),
        'action' => route('donors.donations.update', [$donor, $donation]),
        'isEdit' => true,
        'heading' => 'Edit Donation',
        'subheading' => 'Update donation for ' . $donor->display_name . '.',
        'submitLabel' => 'Save Donation',
        'showDonorField' => false,
        'selectedDonorId' => $donor->id,
        'programs' => $programs,
        'donors' => collect(),
        'donationType' => old('donation_type', $donation->donation_type),
        'amount' => old('amount', $donation->amount),
        'transactionDate' => old('transaction_date', optional($donation->transaction_date)->format('Y-m-d')),
        'status' => old('status', $donation->status),
        'programId' => old('program_id', $donation->program_id),
        'referenceNumber' => old('reference_number', $donation->reference_number),
        'description' => old('description', $donation->description),
    ])
</x-dashboardlayout>
