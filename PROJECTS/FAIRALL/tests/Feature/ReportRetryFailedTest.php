<?php

use App\Jobs\GenerateReportJob;
use App\Models\Report;
use App\Models\Staff;
use App\Models\User;
use App\Services\Llm\GroqClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

function staffUser(string $email): User
{
    $user = User::factory()->create([
        'email' => $email,
        'password' => 'Password123!',
        'user_type' => 'staff',
        'password_changed_at' => now(),
    ]);

    Staff::create([
        'user_id' => $user->id,
        'first_name' => 'Report',
        'last_name' => 'Tester',
        'role' => 'administrator',
        'contact_number' => '9170000001',
        'dial_code' => '+63',
    ]);

    return $user;
}

function makeReport(int $userId, array $narratives): Report
{
    return Report::create([
        'user_id' => $userId,
        'type' => 'funding',
        'period_type' => 'annual',
        'period_config' => ['period_type' => 'annual', 'year' => 2026],
        'date_from' => '2026-01-01',
        'date_to' => '2026-12-31',
        'status' => 'completed',
        'narrative_cache' => $narratives,
    ]);
}

beforeEach(function () {
    $this->user = staffUser('report-admin@test.com');

    $this->post('/login', [
        'email' => 'report-admin@test.com',
        'password' => 'Password123!',
    ]);
});

test('status endpoint lists sections that failed generation', function () {
    $report = makeReport($this->user->id, [
        'executive_summary' => 'All good.',
        'funding' => GroqClient::UNAVAILABLE_PLACEHOLDER,
        'funding_trend_analysis' => GroqClient::UNAVAILABLE_PLACEHOLDER,
    ]);

    $this->getJson("/reports/{$report->id}/status")
        ->assertOk()
        ->assertJsonPath('failed_sections', ['funding', 'funding_trend_analysis']);
});

test('status endpoint returns empty failed sections when all narratives generated', function () {
    $report = makeReport($this->user->id, [
        'executive_summary' => 'All good.',
        'funding' => 'Also good.',
    ]);

    $this->getJson("/reports/{$report->id}/status")
        ->assertOk()
        ->assertJsonPath('failed_sections', []);
});

test('retry-failed resets report and re-dispatches generation job', function () {
    Queue::fake();

    $report = makeReport($this->user->id, [
        'funding' => GroqClient::UNAVAILABLE_PLACEHOLDER,
    ]);

    $this->postJson("/reports/{$report->id}/retry-failed")
        ->assertOk()
        ->assertJsonPath('status', 'pending')
        ->assertJsonPath('retrying_sections', ['funding']);

    Queue::assertPushed(GenerateReportJob::class, fn (GenerateReportJob $job) => $job->reportId === $report->id);

    expect($report->fresh()->status)->toBe('pending');
});

test('retry-failed rejects reports without failed sections', function () {
    Queue::fake();

    $report = makeReport($this->user->id, [
        'funding' => 'A perfectly fine narrative.',
    ]);

    $this->postJson("/reports/{$report->id}/retry-failed")->assertStatus(409);

    Queue::assertNothingPushed();
});

test('retry-failed is forbidden for non-owners', function () {
    Queue::fake();

    $other = staffUser('other-owner@test.com');
    $report = makeReport($other->id, [
        'funding' => GroqClient::UNAVAILABLE_PLACEHOLDER,
    ]);

    $this->postJson("/reports/{$report->id}/retry-failed")->assertStatus(403);

    Queue::assertNothingPushed();
});
