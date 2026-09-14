<?php

namespace App\Http\Controllers;

use App\Models\Donor;
use App\Models\Donation;
use App\Models\Program;
use App\Notifications\ManualDonationAddedNotification;
use App\Services\Exports\XeroExportService;
use App\Jobs\GenerateReceiptAndNotify;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DonorDonationController extends Controller
{
	public function __construct(
		private readonly XeroExportService $xeroExportService,
	) {}

	private function donationFormData(bool $showDonorField = false, ?Donation $donation = null): array
	{
		return [
			'name' => Auth::user()?->display_name ?? 'Staff',
			'programs' => Program::orderBy('program_name')->get(),
			'donors' => $showDonorField ? Donor::with('user')->orderBy('organization_name')->orderBy('last_name')->orderBy('first_name')->get() : collect(),
			'showDonorField' => $showDonorField,
			'donation' => $donation,
		];
	}

	public function index(Donor $donor): View
	{
		$donations = $donor->donations()->with(['program', 'subscription'])->latest('id')->paginate(10)->withQueryString();

		return view('donor-management.donations.index', [
			'name' => Auth::user()?->display_name ?? 'Staff',
			'donor' => $donor->load('user'),
			'donations' => $donations,
		]);
	}

	/**
	 * Staff: All donations ledger with filters and reconciliation tab.
	 */
	public function allIndex(Request $request): View
	{
		$donations = $this->allDonationsQuery($request)->withSum('allocations', 'amount_cents')->latest('id')->paginate(15)->withQueryString();

		return view('donor-management.donations.all', [
			'name' => Auth::user()?->display_name ?? 'Staff',
			'donations' => $donations,
			'donors' => Donor::with('user')->orderBy('organization_name')->orderBy('last_name')->orderBy('first_name')->get(),
			'programs' => Program::orderBy('program_name')->get(),
		]);
	}

	public function exportAll(Request $request, string $format)
	{
		$format = strtolower($format);

		$donations = $this->allDonationsQuery($request)
			->latest('transaction_date')
			->latest('id')
			->get();

		if ($format === 'xero') {
			$filename = 'donations-xero-' . now()->format('Ymd') . '.csv';

			return $this->xeroExportService->streamDownload($donations, null, $filename);
		}

		abort(404);
	}

	public function show(Donor $donor, Donation $donation): View
	{
		$this->ensureDonationBelongsToDonor($donor, $donation);

		$donation->load(['program', 'subscription', 'receipts', 'allocations.beneficiary']);

		return view('donor-management.donations.show', [
			'name' => Auth::user()?->display_name ?? 'Staff',
			'donor' => $donor->load('user'),
			'donation' => $donation,
		]);
	}

	public function create(Donor $donor): View
	{
		return view('donor-management.donations.create', [
			...$this->donationFormData(false),
			'donor' => $donor->load(['user']),
		]);
	}

	public function createAll(): View
	{
		$this->authorize('create', Donation::class);

		return view('donor-management.donations.create-all', [
			...$this->donationFormData(true),
		]);
	}

	public function store(Request $request, Donor $donor): RedirectResponse
	{
		if ($request->has('donations')) {
			$validated = $request->validate([
				'donations' => ['required', 'array', 'min:1', 'max:20'],
				'donations.*.program_id' => ['nullable', 'integer', 'exists:programs,id'],
				'donations.*.donation_type' => ['nullable', 'in:financial,in-kind'],
				'donations.*.reference_number' => ['nullable', 'string', 'max:150', 'distinct', Rule::unique('donations', 'reference_number')],
				'donations.*.amount' => ['required', 'numeric', 'min:0.01'],
				'donations.*.transaction_date' => ['required', 'date'],
				'donations.*.status' => ['required', 'in:pending,completed,failed,refunded'],
				'donations.*.description' => ['nullable', 'string', 'max:2000'],
			]);

			$donations = $validated['donations'];
			$count = count($donations);
			$created = [];

			DB::transaction(function () use ($donations, $donor, &$created) {
				foreach ($donations as $row) {
					$donation = Donation::create([
						'donor_id' => $donor->id,
						'program_id' => $row['program_id'] ?? null,
						'donation_type' => $row['donation_type'] ?? null,
						'gateway' => 'manual',
						'reference_number' => $row['reference_number'] ?? null,
						'gateway_reference' => null,
						'receipt_number' => null,
						'amount' => $row['amount'],
						'transaction_date' => $row['transaction_date'],
						'status' => $row['status'],
						'description' => $row['description'] ?? null,
						'metadata' => null,
						'created_by' => Auth::user()?->staff?->id,
						'updated_by' => Auth::user()?->staff?->id,
					]);
					$created[] = $donation;
				}
			});

			foreach ($created as $donation) {
				$this->notifyDonor($donor, new ManualDonationAddedNotification($donation));
				GenerateReceiptAndNotify::dispatch($donation->id);
			}

			return redirect('/donors/' . $donor->id . '?tab=donations')->with('status', $count === 1 ? 'Donation recorded successfully.' : $count . ' donations recorded successfully.');
		}

		$validated = $request->validate([
			'donor_id' => ['nullable', 'integer', 'exists:donors,id'],
			'program_id' => ['nullable', 'integer', 'exists:programs,id'],
			'donation_type' => ['nullable', 'in:financial,in-kind'],
			'reference_number' => ['nullable', 'string', 'max:150', Rule::unique('donations', 'reference_number')],
			'receipt_number' => ['nullable', 'string'],
			'amount' => ['required', 'numeric', 'min:0.01'],
			'transaction_date' => ['required', 'date'],
			'status' => ['required', 'in:pending,completed,failed,refunded'],
			'description' => ['nullable', 'string'],
			'metadata' => ['nullable', 'array'],
		]);

		$donation = Donation::create([
			'donor_id' => $donor->id,
			'program_id' => $validated['program_id'] ?? null,
			'donation_type' => $validated['donation_type'] ?? null,
			'gateway' => 'manual',
			'reference_number' => $validated['reference_number'] ?? null,
			'gateway_reference' => null,
			'receipt_number' => $validated['receipt_number'] ?? null,
			'amount' => $validated['amount'],
			'transaction_date' => $validated['transaction_date'],
			'status' => $validated['status'],
			'description' => $validated['description'] ?? null,
			'metadata' => $validated['metadata'] ?? null,
			'created_by' => Auth::user()?->staff?->id,
			'updated_by' => Auth::user()?->staff?->id,
		]);

		$this->notifyDonor($donor, new ManualDonationAddedNotification($donation));

		// Queue receipt generation and notification for this manual staff entry
		GenerateReceiptAndNotify::dispatch($donation->id);

		return redirect('/donors/' . $donor->id . '?tab=donations')->with('status', 'Donation recorded successfully.');
	}

	public function storeAll(Request $request): RedirectResponse
	{
		$this->authorize('create', Donation::class);

		if ($request->has('donations')) {
			$validated = $request->validate([
				'donations' => ['required', 'array', 'min:1', 'max:20'],
				'donations.*.donor_id' => ['nullable', 'integer', 'exists:donors,id'],
				'donations.*.program_id' => ['nullable', 'integer', 'exists:programs,id'],
				'donations.*.donation_type' => ['nullable', 'in:financial,in-kind'],
				'donations.*.reference_number' => ['nullable', 'string', 'max:150', 'distinct', Rule::unique('donations', 'reference_number')],
				'donations.*.amount' => ['required', 'numeric', 'min:0.01'],
				'donations.*.transaction_date' => ['required', 'date'],
				'donations.*.status' => ['required', 'in:pending,completed,failed,refunded'],
				'donations.*.description' => ['nullable', 'string', 'max:2000'],
			]);

			$donations = $validated['donations'];
			$count = count($donations);
			$created = [];

			DB::transaction(function () use ($donations, &$created) {
				foreach ($donations as $row) {
					$donation = Donation::create([
						'donor_id' => $row['donor_id'] ?? null,
						'program_id' => $row['program_id'] ?? null,
						'donation_type' => $row['donation_type'] ?? null,
						'gateway' => 'manual',
						'reference_number' => $row['reference_number'] ?? null,
						'gateway_reference' => null,
						'receipt_number' => null,
						'amount' => $row['amount'],
						'transaction_date' => $row['transaction_date'],
						'status' => $row['status'],
						'description' => $row['description'] ?? null,
						'metadata' => null,
						'created_by' => Auth::user()?->staff?->id,
						'updated_by' => Auth::user()?->staff?->id,
					]);
					$created[] = $donation;
				}
			});

			foreach ($created as $donation) {
				if (! empty($donation->donor_id)) {
					$this->notifyDonor($donation->donor, new ManualDonationAddedNotification($donation));
				}
				GenerateReceiptAndNotify::dispatch($donation->id);
			}

			return redirect()->route('donors.donations.all')->with('status', $count === 1 ? 'Donation recorded successfully.' : $count . ' donations recorded successfully.');
		}

		$validated = $request->validate([
			'donor_id' => ['nullable', 'integer', 'exists:donors,id'],
			'program_id' => ['nullable', 'integer', 'exists:programs,id'],
			'donation_type' => ['nullable', 'in:financial,in-kind'],
			'reference_number' => ['nullable', 'string', 'max:150', Rule::unique('donations', 'reference_number')],
			'receipt_number' => ['nullable', 'string'],
			'amount' => ['required', 'numeric', 'min:0.01'],
			'transaction_date' => ['required', 'date'],
			'status' => ['required', 'in:pending,completed,failed,refunded'],
			'description' => ['nullable', 'string'],
			'metadata' => ['nullable', 'array'],
		]);

		$donation = Donation::create([
			'donor_id' => $validated['donor_id'] ?? null,
			'program_id' => $validated['program_id'] ?? null,
			'donation_type' => $validated['donation_type'] ?? null,
			'gateway' => 'manual',
			'reference_number' => $validated['reference_number'] ?? null,
			'gateway_reference' => null,
			'receipt_number' => $validated['receipt_number'] ?? null,
			'amount' => $validated['amount'],
			'transaction_date' => $validated['transaction_date'],
			'status' => $validated['status'],
			'description' => $validated['description'] ?? null,
			'metadata' => $validated['metadata'] ?? null,
			'created_by' => Auth::user()?->staff?->id,
			'updated_by' => Auth::user()?->staff?->id,
		]);

		if (! empty($donation->donor_id)) {
			$this->notifyDonor($donation->donor, new ManualDonationAddedNotification($donation));
		}

		GenerateReceiptAndNotify::dispatch($donation->id);

		return redirect()->route('donors.donations.all')->with('status', 'Donation recorded successfully.');
	}

	public function edit(Donor $donor, Donation $donation): View
	{
		$this->ensureDonationBelongsToDonor($donor, $donation);

		$this->authorize('update', $donation);

		return view('donor-management.donations.edit', [
			...$this->donationFormData(false, $donation),
			'donor' => $donor->load(['user']),
		]);
	}

	public function editAll(Donation $donation): View
	{
		$this->authorize('update', $donation);

		return view('donor-management.donations.edit-all', [
			...$this->donationFormData(true, $donation),
		]);
	}

	public function update(Request $request, Donor $donor, Donation $donation): RedirectResponse
	{
		$this->ensureDonationBelongsToDonor($donor, $donation);

		$this->authorize('update', $donation);

		$validated = $request->validate([
			'program_id' => ['nullable', 'integer', 'exists:programs,id'],
			'donation_type' => ['nullable', 'in:financial,in-kind'],
			'reference_number' => ['nullable', 'string', 'max:150', Rule::unique('donations', 'reference_number')->ignore($donation->id)],
			'receipt_number' => ['nullable', 'string'],
			'amount' => ['required', 'numeric', 'min:0.01'],
			'transaction_date' => ['required', 'date'],
			'status' => ['required', 'in:pending,completed,failed,refunded'],
			'description' => ['nullable', 'string'],
			'metadata' => ['nullable', 'array'],
		]);

		$donation->update([
			'program_id' => $validated['program_id'] ?? null,
			'donation_type' => $validated['donation_type'] ?? null,
			// keep gateway as manual for staff-managed entries; preserve existing gateway if present
			'gateway' => $donation->gateway ?? 'manual',
			'reference_number' => $validated['reference_number'] ?? null,
			'gateway_reference' => ($donation->gateway ?? 'manual') === 'manual' ? null : $donation->gateway_reference,
			'receipt_number' => $validated['receipt_number'] ?? null,
			'amount' => $validated['amount'],
			'transaction_date' => $validated['transaction_date'],
			'status' => $validated['status'],
			'description' => $validated['description'] ?? null,
			'metadata' => $validated['metadata'] ?? null,
			'updated_by' => Auth::user()?->staff?->id,
		]);

		// Queue receipt generation after staff update as well
		GenerateReceiptAndNotify::dispatch($donation->id);

		return redirect('/donors/' . $donor->id)->with('status', 'Donation updated successfully.');
	}

	public function updateAll(Request $request, Donation $donation): RedirectResponse
	{
		$this->authorize('update', $donation);

		$validated = $request->validate([
			'donor_id' => ['nullable', 'integer', 'exists:donors,id'],
			'program_id' => ['nullable', 'integer', 'exists:programs,id'],
			'donation_type' => ['nullable', 'in:financial,in-kind'],
			'reference_number' => ['nullable', 'string', 'max:150', Rule::unique('donations', 'reference_number')->ignore($donation->id)],
			'receipt_number' => ['nullable', 'string'],
			'amount' => ['required', 'numeric', 'min:0.01'],
			'transaction_date' => ['required', 'date'],
			'status' => ['required', 'in:pending,completed,failed,refunded'],
			'description' => ['nullable', 'string'],
			'metadata' => ['nullable', 'array'],
		]);

		$donation->update([
			'donor_id' => $validated['donor_id'] ?? null,
			'program_id' => $validated['program_id'] ?? null,
			'donation_type' => $validated['donation_type'] ?? null,
			'gateway' => $donation->gateway ?? 'manual',
			'reference_number' => $validated['reference_number'] ?? null,
			'gateway_reference' => ($donation->gateway ?? 'manual') === 'manual' ? null : $donation->gateway_reference,
			'receipt_number' => $validated['receipt_number'] ?? null,
			'amount' => $validated['amount'],
			'transaction_date' => $validated['transaction_date'],
			'status' => $validated['status'],
			'description' => $validated['description'] ?? null,
			'metadata' => $validated['metadata'] ?? null,
			'updated_by' => Auth::user()?->staff?->id,
		]);

		GenerateReceiptAndNotify::dispatch($donation->id);

		return redirect()->route('donors.donations.all')->with('status', 'Donation updated successfully.');
	}

	public function destroy(Donor $donor, Donation $donation): RedirectResponse
	{
		$this->ensureDonationBelongsToDonor($donor, $donation);

		$this->authorize('delete', $donation);

		$donation->delete();

		return redirect('/donors/' . $donor->id)->with('status', 'Donation moved to archive. Will be automatically deleted after 90 days.');
	}

	public function destroyAll(Donation $donation): RedirectResponse
	{
		$this->authorize('delete', $donation);

		$donation->delete();

		return redirect()->route('donors.donations.all')->with('status', 'Donation moved to archive. Will be automatically deleted after 90 days.');
	}

	public function bulkDestroy(Request $request, Donor $donor): RedirectResponse
	{
		$validated = $request->validate([
			'ids' => ['required', 'array', 'min:1', 'max:100'],
			'ids.*' => ['integer', 'distinct', 'exists:donations,id'],
		]);

		$ids = $validated['ids'];

		DB::transaction(function () use ($ids, $donor): void {
			$donations = Donation::whereIn('id', $ids)->get();
			foreach ($donations as $donation) {
				$this->ensureDonationBelongsToDonor($donor, $donation);
				$this->authorize('delete', $donation);
				$donation->delete();
			}
		});

		$count = count($ids);

		return back()->with('status', $count . ' donations moved to archive. Will be automatically deleted after 90 days.');
	}

	public function bulkDestroyAll(Request $request): RedirectResponse
	{
		$validated = $request->validate([
			'ids' => ['required', 'array', 'min:1', 'max:100'],
			'ids.*' => ['integer', 'distinct', 'exists:donations,id'],
		]);

		$ids = $validated['ids'];

		DB::transaction(function () use ($ids): void {
			$donations = Donation::whereIn('id', $ids)->get();
			foreach ($donations as $donation) {
				$this->authorize('delete', $donation);
				$donation->delete();
			}
		});

		$count = count($ids);

		return back()->with('status', $count . ' donations moved to archive. Will be automatically deleted after 90 days.');
	}

	public function export(Donor $donor, string $format)
	{
		$format = strtolower($format);

		$donations = $donor->donations()
			->with(['program'])
			->latest('transaction_date')
			->latest('id')
			->get();

		if ($format === 'xero') {
			$filename = 'donor-' . $donor->id . '-donations-xero-' . now()->format('Ymd') . '.csv';

			return $this->xeroExportService->streamDownload($donations, $donor, $filename);
		}

		return view('donor-management.donations.export', [
			'name' => Auth::user()?->display_name ?? 'Staff',
			'donor' => $donor->load('user'),
			'donations' => $donations,
			'format' => $format,
		]);
	}

	private function ensureDonationBelongsToDonor(Donor $donor, Donation $donation): void
	{
		if ($donation->donor_id !== $donor->id) {
			abort(404);
		}
	}

	private function allDonationsQuery(Request $request): Builder
	{
		$donationsQuery = Donation::query()->with(['program', 'donor']);

		if ($request->filled('donor')) {
			$donorTerm = $request->query('donor');
			$donationsQuery->whereHas('donor', function ($q) use ($donorTerm) {
				$q->where('organization_name', 'like', "%{$donorTerm}%")
					->orWhere('last_name', 'like', "%{$donorTerm}%")
					->orWhere('first_name', 'like', "%{$donorTerm}%");
			});
		}

		if ($request->filled('program_id')) {
			if ($request->query('program_id') === 'no_programs') {
				$donationsQuery->whereNull('program_id');
			} else {
				$donationsQuery->where('program_id', $request->query('program_id'));
			}
		}

		if ($request->filled('gateway')) {
			$donationsQuery->where('gateway', $request->query('gateway'));
		}

		if ($request->filled('status')) {
			$donationsQuery->where('status', $request->query('status'));
		}

		if ($request->boolean('anonymous')) {
			$donationsQuery->where(function ($q) {
				$q->where('metadata->anonymous', true)
					->orWhereNull('donor_id');
			});
		}

		if ($request->boolean('linked')) {
			$donationsQuery->whereNotNull('donor_id');
		}

		return $donationsQuery;
	}

	private function notifyDonor(Donor $donor, object $notification): void
	{
		$donor->loadMissing('user');

		if ($donor->user) {
			$donor->user->notify($notification);
		}
	}

	public function viewReceipt(Donor $donor, Donation $donation)
	{
		$this->ensureDonationBelongsToDonor($donor, $donation);

		return $this->serveDonationReceipt($donation, false);
	}

	public function downloadReceipt(Donor $donor, Donation $donation)
	{
		$this->ensureDonationBelongsToDonor($donor, $donation);

		return $this->serveDonationReceipt($donation, true);
	}

	private function serveDonationReceipt(Donation $donation, bool $download)
	{
		$donation->loadMissing('receipts');

		$receipt = $donation->receipts->sortByDesc('id')->first();
		$receiptPath = $receipt?->path ?? $donation->receipt_path;

		abort_unless($receiptPath, 404, 'Receipt is not available for this donation.');

		$disk = Storage::disk(config('filesystems.default'));

		abort_unless($disk->exists($receiptPath), 404, 'Receipt file could not be found.');

		$filename = basename($receiptPath);
		$stream = $disk->readStream($receiptPath);

		abort_unless(is_resource($stream), 404, 'Receipt file could not be read.');

		$headers = [
			'Content-Type' => 'application/pdf',
			'Content-Disposition' => ($download ? 'attachment' : 'inline') . '; filename="' . $filename . '"',
		];

		return response()->stream(function () use ($stream): void {
			fpassthru($stream);

			if (is_resource($stream)) {
				fclose($stream);
			}
		}, 200, $headers);
	}
}
