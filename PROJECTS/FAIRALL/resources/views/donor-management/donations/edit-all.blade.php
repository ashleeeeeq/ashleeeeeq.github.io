<x-dashboardlayout name="{{ $name }}" title="Edit Donation">
    @include('donor-management.donations.form', [
        'backUrl' => route('donors.donations.all'),
        'cancelUrl' => route('donors.donations.all'),
        'action' => route('donors.donations.update-all', $donation),
        'isEdit' => true,
        'heading' => 'Edit Donation',
        'subheading' => 'Update this donation from the all-donations ledger.',
        'submitLabel' => 'Save Donation',
        'showDonorField' => true,
        'selectedDonorId' => old('donor_id', $donation->donor_id),
        'programs' => $programs,
        'donors' => $donors,
        'donationType' => old('donation_type', $donation->donation_type),
        'amount' => old('amount', $donation->amount),
        'transactionDate' => old('transaction_date', optional($donation->transaction_date)->format('Y-m-d')),
        'status' => old('status', $donation->status),
        'programId' => old('program_id', $donation->program_id),
        'referenceNumber' => old('reference_number', $donation->reference_number),
        'description' => old('description', $donation->description),
    ])
</x-dashboardlayout>