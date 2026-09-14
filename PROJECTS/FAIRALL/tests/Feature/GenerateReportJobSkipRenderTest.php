<?php

use App\Contracts\LlmClientInterface;
use App\Jobs\GenerateReportJob;
use App\Models\Report;
use App\Models\Staff;
use App\Models\User;
use App\Services\Reports\ReportContextBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('s3');

    $this->user = User::factory()->create([
        'email' => 'job-admin@test.com',
        'password' => 'Password123!',
        'user_type' => 'staff',
        'password_changed_at' => now(),
    ]);

    Staff::create([
        'user_id' => $this->user->id,
        'first_name' => 'Job',
        'last_name' => 'Tester',
        'role' => 'administrator',
        'contact_number' => '9170000001',
        'dial_code' => '+63',
    ]);

    $this->post('/login', [
        'email' => 'job-admin@test.com',
        'password' => 'Password123!',
    ]);

    Http::fake([
        '*gotenberg*' => Http::response('%PDF-fake-bytes', 200),
    ]);
});

function makePendingReport(int $userId): Report
{
    return Report::create([
        'user_id' => $userId,
        'type' => 'funding',
        'period_type' => 'annual',
        'period_config' => ['period_type' => 'annual', 'year' => 2026],
        'date_from' => '2026-01-01',
        'date_to' => '2026-12-31',
        'status' => 'pending',
        'boot_payload' => [
            'funding' => [
                'newDonors' => 12,
                'grantsReceived' => 3,
                'fundsReceived' => 185000.00,
                'fundsAllocated' => 142000.00,
                'fundingTarget' => 250000.00,
                'receivedProgressPercent' => 74,
                'allocatedVsReceivedPercent' => 76.8,
            ],
        ],
    ]);
}

function runJob(int $reportId): void
{
    (new GenerateReportJob($reportId))->handle(
        app(LlmClientInterface::class),
        app(ReportContextBuilder::class),
    );
}

test('skips pdf rendering when a retry recovers no new narratives', function () {
    $narratives = [
        'executive_summary' => 'Summary narrative.',
        'funding' => 'Funding narrative.',
    ];

    $llm = Mockery::mock(LlmClientInterface::class);
    $llm->shouldReceive('generateBatch')->twice()->andReturn($narratives);
    $this->app->instance(LlmClientInterface::class, $llm);

    $report = makePendingReport($this->user->id);

    runJob($report->id);

    $report->refresh();
    expect($report->status)->toBe('completed');
    expect($report->pdf_path)->not->toBeNull();
    expect(Storage::disk('s3')->exists($report->pdf_path))->toBeTrue();

    $pathAfterFirstRun = $report->pdf_path;

    $report->update(['status' => 'pending']);

    runJob($report->id);

    $report->refresh();
    expect($report->status)->toBe('completed');
    expect($report->pdf_path)->toBe($pathAfterFirstRun);
});

test('re-renders the pdf when narratives change between runs', function () {
    $firstNarratives = [
        'executive_summary' => 'First summary.',
        'funding' => 'First funding narrative.',
    ];

    $secondNarratives = [
        'executive_summary' => 'Second summary.',
        'funding' => 'Recovered funding narrative.',
    ];

    $llm = Mockery::mock(LlmClientInterface::class);
    $llm->shouldReceive('generateBatch')->once()->andReturn($firstNarratives);
    $llm->shouldReceive('generateBatch')->once()->andReturn($secondNarratives);
    $this->app->instance(LlmClientInterface::class, $llm);

    $report = makePendingReport($this->user->id);

    runJob($report->id);
    $report->refresh();
    $pathAfterFirstRun = $report->pdf_path;

    $report->update(['status' => 'pending']);

    runJob($report->id);

    $report->refresh();
    expect($report->status)->toBe('completed');
    expect($report->pdf_path)->not->toBe($pathAfterFirstRun);
});
