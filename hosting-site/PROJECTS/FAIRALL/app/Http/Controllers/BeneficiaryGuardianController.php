<?php

namespace App\Http\Controllers;

use App\Models\Beneficiary;
use App\Models\BeneficiaryGuardian;
use App\Services\GlobalAddressService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BeneficiaryGuardianController extends Controller
{
    public function index(Beneficiary $beneficiary): View
    {
        $beneficiary->load(['guardians']);

        $guardians = $beneficiary->guardians
            ->sortByDesc(fn($guardian) => [$guardian->guardian_type, $guardian->id])
            ->values();

        return view('beneficiaries.guardians.index', [
            'name' => Auth::user()?->display_name ?? 'Staff',
            'beneficiary' => $beneficiary,
            'guardians' => $guardians,
        ]);
    }

    public function create(Beneficiary $beneficiary): View
    {
        return view('beneficiaries.guardians.create', [
            'name' => Auth::user()?->display_name ?? 'Staff',
            'beneficiary' => $beneficiary,
        ]);
    }

    public function store(Request $request, Beneficiary $beneficiary, GlobalAddressService $addressService): RedirectResponse
    {
        $validated = $request->validate([
            'guardian_type' => ['required', Rule::in(['mother', 'father', 'guardian'])],
            'first_name' => ['required', 'string', 'max:100', 'not_regex:/\d/'],
            'middle_name' => ['nullable', 'string', 'max:100', 'not_regex:/\d/'],
            'last_name' => ['required', 'string', 'max:100', 'not_regex:/\d/'],
            'civil_status' => ['nullable', 'string', 'max:50'],
            'birth_date' => ['nullable', 'date'],
            'place_of_birth' => ['nullable', 'string', 'max:80'],
            'sex' => ['nullable', Rule::in(['male', 'female'])],
            'contact_number_code' => ['required', 'string', 'starts_with:+', 'max:6'],
            'contact_number' => ['required', 'regex:/^[0-9]{6,15}$/'],
            'highest_education' => ['nullable', Rule::in(['pre-school', 'elementary', 'high_school', 'shs', 'college', 'post-grad'])],
            'job' => ['nullable', 'string', 'max:50'],
            'estimated_salary' => ['nullable', 'integer'],
            'deceased' => ['sometimes', 'boolean'],
            'address_line' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'zip' => ['nullable', 'string', 'max:20'],
        ], [
            'first_name.not_regex' => 'The first name must not contain numbers.',
            'middle_name.not_regex' => 'The middle name must not contain numbers.',
            'last_name.not_regex' => 'The last name must not contain numbers.',
        ]);

        $guardian = $beneficiary->guardians()->create([
            'guardian_type' => $validated['guardian_type'],
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'] ?? null,
            'last_name' => $validated['last_name'],
            'civil_status' => $validated['civil_status'] ?? null,
            'birth_date' => $validated['birth_date'] ?? null,
            'place_of_birth' => $validated['place_of_birth'] ?? null,
            'sex' => $validated['sex'] ?? null,
            'contact_number' => $validated['contact_number'],
            'dial_code' => $validated['contact_number_code'] ?? '+63',
            'highest_education' => $validated['highest_education'] ?? null,
            'job' => $validated['job'] ?? null,
            'estimated_salary' => $validated['estimated_salary'] ?? null,
            'deceased' => (bool) ($validated['deceased'] ?? false),
            'created_by' => Auth::user()?->staff?->id,
        ]);

        $this->saveGuardianAddress($guardian, $validated, $addressService);

        return redirect("/beneficiaries/{$beneficiary->id}/guardians")->with('status', 'Guardian added successfully.');
    }

    public function edit(Beneficiary $beneficiary, $guardianId): View
    {
        $guardian = BeneficiaryGuardian::with('address')
            ->where('id', $guardianId)
            ->where('beneficiary_id', $beneficiary->id)
            ->firstOrFail();

        return view('beneficiaries.guardians.edit', [
            'name' => Auth::user()?->display_name ?? 'Staff',
            'beneficiary' => $beneficiary,
            'guardian' => $guardian,
        ]);
    }

    public function show(Beneficiary $beneficiary, $guardianId): View
    {
        $guardian = BeneficiaryGuardian::with('address')
            ->where('id', $guardianId)
            ->where('beneficiary_id', $beneficiary->id)
            ->firstOrFail();

        return view('beneficiaries.guardians.show', [
            'name' => Auth::user()?->display_name ?? 'Staff',
            'beneficiary' => $beneficiary,
            'guardian' => $guardian,
        ]);
    }

    public function update(Request $request, Beneficiary $beneficiary, $guardianId, GlobalAddressService $addressService): RedirectResponse
    {
        $guardian = BeneficiaryGuardian::where('id', $guardianId)
            ->where('beneficiary_id', $beneficiary->id)
            ->firstOrFail();

        $validated = $request->validate([
            'guardian_type' => ['required', Rule::in(['mother', 'father', 'guardian'])],
            'first_name' => ['required', 'string', 'max:100', 'not_regex:/\d/'],
            'middle_name' => ['nullable', 'string', 'max:100', 'not_regex:/\d/'],
            'last_name' => ['required', 'string', 'max:100', 'not_regex:/\d/'],
            'civil_status' => ['nullable', 'string', 'max:50'],
            'birth_date' => ['nullable', 'date'],
            'place_of_birth' => ['nullable', 'string', 'max:80'],
            'sex' => ['nullable', Rule::in(['male', 'female'])],
            'contact_number_code' => ['required', 'string', 'starts_with:+', 'max:6'],
            'contact_number' => ['required', 'regex:/^[0-9]{6,15}$/'],
            'highest_education' => ['nullable', Rule::in(['pre-school', 'elementary', 'high_school', 'shs', 'college', 'post-grad'])],
            'job' => ['nullable', 'string', 'max:50'],
            'estimated_salary' => ['nullable', 'integer'],
            'deceased' => ['sometimes', 'boolean'],
            'address_line' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'zip' => ['nullable', 'string', 'max:20'],
        ], [
            'first_name.not_regex' => 'The first name must not contain numbers.',
            'middle_name.not_regex' => 'The middle name must not contain numbers.',
            'last_name.not_regex' => 'The last name must not contain numbers.',
        ]);

        $guardian->update([
            'guardian_type' => $validated['guardian_type'],
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'] ?? null,
            'last_name' => $validated['last_name'],
            'civil_status' => $validated['civil_status'] ?? null,
            'birth_date' => $validated['birth_date'] ?? null,
            'place_of_birth' => $validated['place_of_birth'] ?? null,
            'sex' => $validated['sex'] ?? null,
            'contact_number' => $validated['contact_number'],
            'dial_code' => $validated['contact_number_code'] ?? '+63',
            'highest_education' => $validated['highest_education'] ?? null,
            'job' => $validated['job'] ?? null,
            'estimated_salary' => $validated['estimated_salary'] ?? null,
            'deceased' => (bool) ($validated['deceased'] ?? false),
            'updated_by' => Auth::user()?->staff?->id,
        ]);

        $this->saveGuardianAddress($guardian, $validated, $addressService);

        return redirect("/beneficiaries/{$beneficiary->id}/guardians")->with('status', 'Guardian updated successfully.');
    }

    private function saveGuardianAddress($guardian, array $validated, GlobalAddressService $addressService): void
    {
        $addressFields = array_filter([
            'address_line' => $validated['address_line'] ?? '',
            'country' => $validated['country'] ?? '',
            'province' => $validated['province'] ?? '',
            'city' => $validated['city'] ?? '',
            'zip' => $validated['zip'] ?? '',
        ], fn($v) => $v !== '');

        if (empty($addressFields)) {
            return;
        }

        if (!empty($addressFields['country'])) {
            $addressFields['country'] = $addressService->resolveCountry($addressFields['country']);
        }
        if (!empty($addressFields['province'])) {
            $addressFields['province'] = $addressService->resolveProvince(
                $addressFields['province'],
                $addressFields['country'] ?? ''
            );
        }
        if (!empty($addressFields['city'])) {
            $addressFields['city'] = $addressService->resolveCity(
                $addressFields['city'],
                $addressFields['province'] ?? '',
                $addressFields['country'] ?? ''
            );
        }

        if ($guardian->address) {
            $guardian->address()->update($addressFields);
        } else {
            $guardian->address()->create($addressFields);
        }
    }

    public function destroy(Beneficiary $beneficiary, $guardianId): RedirectResponse
    {
        BeneficiaryGuardian::where('id', $guardianId)
            ->where('beneficiary_id', $beneficiary->id)
            ->delete();

        return redirect("/beneficiaries/{$beneficiary->id}/guardians")->with('status', 'Guardian deleted successfully.');
    }
}
