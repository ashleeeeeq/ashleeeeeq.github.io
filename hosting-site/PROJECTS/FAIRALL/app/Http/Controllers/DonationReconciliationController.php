<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateReceiptAndNotify;
use App\Models\Donor;
use App\Models\Donation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DonationReconciliationController extends Controller
{
	public function index(): View
	{

		// Delegate rendering to the All Donations ledger with unmatched filter active
		$request = request();
		$request->merge(['unmatched' => 1]);

		return app(DonorDonationController::class)->allIndex($request);
	}

	public function update(Request $request, Donation $donation): RedirectResponse
	{
		$this->linkDonor($request, $donation);

		return back()->with('status', 'Donation linked to donor successfully.');
	}

	public function resendReceipt(Request $request, Donation $donation): RedirectResponse
	{
		if ($donation->donor_id === null) {
			$this->linkDonor($request, $donation);
		}

		GenerateReceiptAndNotify::dispatch($donation->id, true);

		return back()->with('status', 'Receipt queued for resend successfully.');
	}

	protected function linkDonor(Request $request, Donation $donation): void
	{
		$validated = $request->validate([
			'donor_id' => ['required', 'integer', 'exists:donors,id'],
		]);

		$donation->update([
			'donor_id' => $validated['donor_id'],
		]);
	}
}
