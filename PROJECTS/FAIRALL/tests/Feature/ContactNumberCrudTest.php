<?php

use App\Models\User;
use App\Models\Staff;
use App\Models\Donor;
use App\Models\Grant;
use App\Models\Beneficiary;
use App\Models\BeneficiaryGuardian;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $user = User::factory()->create([
        'email' => 'admin@test.com',
        'password' => 'Password123!',
        'user_type' => 'staff',
        'password_changed_at' => now(),
    ]);
    Staff::create([
        'user_id' => $user->id,
        'first_name' => 'Admin',
        'last_name' => 'User',
        'role' => 'administrator',
        'contact_number' => '9170000001',
        'dial_code' => '+63',
    ]);
    $this->post('/login', [
        'email' => 'admin@test.com',
        'password' => 'Password123!',
    ]);
});

test('staff can be created with contact number and dial code', function () {
    $dept = Department::create(['name' => 'Test Dept']);
    $position = Position::create(['name' => 'Test Position']);

    $response = $this->post('/users', [
        'role' => 'program_staff',
        'first_name' => 'Test',
        'last_name' => 'Staff',
        'email' => 'staff-create@test.com',
        'address_line' => '123 Test St',
        'country' => 'Philippines',
        'province' => 'Metro Manila',
        'city' => 'Quezon City',
        'zip' => '1100',
        'department_id' => $dept->id,
        'position_id' => $position->id,
        'contact_number_code' => '+63',
        'contact_number' => '912345678',
    ]);

    $response->assertRedirect();

    $user = User::where('email', 'staff-create@test.com')->first();
    expect($user)->not->toBeNull();
    expect($user->staff)->not->toBeNull();
    expect($user->staff->contact_number)->toBe('912345678');
    expect($user->staff->dial_code)->toBe('+63');
});

test('staff contact number can be updated', function () {
    $dept = Department::create(['name' => 'Test Dept']);
    $position = Position::create(['name' => 'Test Position']);

    $user = User::factory()->create([
        'email' => 'staff-update@test.com',
        'password' => 'Password123!',
        'user_type' => 'staff',
    ]);
    $staff = Staff::create([
        'user_id' => $user->id,
        'first_name' => 'Updatable',
        'last_name' => 'Staff',
        'role' => 'program_staff',
        'contact_number' => '9170000001',
        'dial_code' => '+63',
        'department_id' => $dept->id,
        'position_id' => $position->id,
    ]);

    $response = $this->put("/users/{$user->id}", [
        'role' => 'program_staff',
        'first_name' => 'Updatable',
        'last_name' => 'Staff',
        'email' => 'staff-update@test.com',
        'department_id' => $dept->id,
        'position_id' => $position->id,
        'address_line' => '123 Test St',
        'country' => 'Philippines',
        'province' => 'Metro Manila',
        'city' => 'Quezon City',
        'zip' => '1100',
        'contact_number_code' => '+1',
        'contact_number' => '987654321',
    ]);

    $response->assertRedirect();

    $staff->refresh();
    expect($staff->contact_number)->toBe('987654321');
    expect($staff->dial_code)->toBe('+1');
});

test('donor can be created with contact number and dial code', function () {
    $response = $this->post('/donors', [
        'donor_type' => 'individual',
        'first_name' => 'Test',
        'last_name' => 'Donor',
        'email' => 'donor-create@test.com',
        'contact_number_code' => '+63',
        'contact_number' => '912345678',
    ]);

    $response->assertRedirect();

    $donor = Donor::latest('id')->first();
    expect($donor)->not->toBeNull();
    expect($donor->contact_number)->toBe('912345678');
    expect($donor->dial_code)->toBe('+63');
});

test('donor contact number can be updated', function () {
    $user = User::factory()->create([
        'email' => 'donor-update@test.com',
        'password' => 'Password123!',
        'user_type' => 'donor',
    ]);
    $donor = Donor::create([
        'user_id' => $user->id,
        'donor_type' => 'individual',
        'first_name' => 'Test',
        'last_name' => 'Donor',
        'contact_number' => '912345678',
        'dial_code' => '+63',
    ]);

    $response = $this->put("/donors/{$donor->id}", [
        'donor_type' => 'individual',
        'first_name' => 'Test',
        'last_name' => 'Donor',
        'email' => 'donor-update@test.com',
        'contact_number_code' => '+1',
        'contact_number' => '987654321',
    ]);

    $response->assertRedirect();

    $donor->refresh();
    expect($donor->contact_number)->toBe('987654321');
    expect($donor->dial_code)->toBe('+1');
});

test('grant can be created with contact number', function () {
    $response = $this->post('/grants', [
        'organization_name' => 'Test Organization',
        'email' => 'grant-with-contact@test.com',
        'grant_name' => 'Test Grant',
        'total_amount' => 10000,
        'start_date' => '2026-01-01',
        'end_date' => '2026-12-31',
        'contact_number_code' => '+63',
        'contact_number' => '912345678',
    ]);

    $response->assertRedirect();

    $grant = Grant::latest('id')->first();
    expect($grant)->not->toBeNull();
    expect($grant->contact_number)->toBe('912345678');
    expect($grant->dial_code)->toBe('+63');
});

test('grant can be created without contact number', function () {
    $response = $this->post('/grants', [
        'organization_name' => 'Test Organization',
        'email' => 'grant-no-contact@test.com',
        'grant_name' => 'Test Grant',
        'total_amount' => 10000,
        'start_date' => '2026-01-01',
        'end_date' => '2026-12-31',
    ]);

    $response->assertRedirect();

    $grant = Grant::latest('id')->first();
    expect($grant)->not->toBeNull();
    expect($grant->contact_number)->toBeNull();
    expect($grant->dial_code)->toBeNull();
});

test('grant contact number can be updated', function () {
    $grant = Grant::create([
        'organization_name' => 'Test Organization',
        'email' => 'grant-update@test.com',
        'grant_name' => 'Test Grant',
        'total_amount' => 10000,
        'start_date' => '2026-01-01',
        'end_date' => '2026-12-31',
        'contact_number' => '912345678',
        'dial_code' => '+63',
    ]);

    $response = $this->put("/grants/{$grant->id}", [
        'organization_name' => 'Test Organization',
        'email' => 'grant-update@test.com',
        'grant_name' => 'Test Grant',
        'total_amount' => 20000,
        'start_date' => '2026-01-01',
        'end_date' => '2026-12-31',
        'contact_number_code' => '+1',
        'contact_number' => '987654321',
    ]);

    $response->assertRedirect();

    $grant->refresh();
    expect($grant->contact_number)->toBe('987654321');
    expect($grant->dial_code)->toBe('+1');
});

test('invalid dial code format is rejected', function () {
    $response = $this->post('/donors', [
        'donor_type' => 'individual',
        'first_name' => 'Test',
        'last_name' => 'Donor',
        'email' => 'donor-validation@test.com',
        'contact_number_code' => 'abc',
        'contact_number' => '912345678',
    ]);

    $response->assertSessionHasErrors('contact_number_code');
});

test('invalid contact number format is rejected', function () {
    $response = $this->post('/donors', [
        'donor_type' => 'individual',
        'first_name' => 'Test',
        'last_name' => 'Donor',
        'email' => 'donor-validation2@test.com',
        'contact_number_code' => '+63',
        'contact_number' => 'not-a-number',
    ]);

    $response->assertSessionHasErrors('contact_number');
});

test('beneficiary factory sets contact number and dial code', function () {
    $beneficiary = Beneficiary::factory()->create();

    expect($beneficiary->contact_number)->not->toBeNull();
    expect($beneficiary->dial_code)->toBe('+63');
    expect($beneficiary->formatted_contact)->toStartWith('+63');
});

test('beneficiary guardian factory sets contact number and dial code', function () {
    $beneficiary = Beneficiary::factory()->create();
    $guardian = BeneficiaryGuardian::factory()->create([
        'beneficiary_id' => $beneficiary->id,
    ]);

    expect($guardian->contact_number)->not->toBeNull();
    expect($guardian->dial_code)->toBe('+63');
    expect($guardian->formatted_contact)->toStartWith('+63');
});

test('formatted_contact accessor formats correctly', function () {
    $beneficiary = Beneficiary::factory()->create([
        'contact_number' => '9171234567',
        'dial_code' => '+63',
    ]);

    expect($beneficiary->formatted_contact)->toBe('+63 917 123 4567');
});
