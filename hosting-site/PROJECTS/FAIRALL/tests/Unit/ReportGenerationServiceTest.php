<?php

uses(Tests\TestCase::class);

use App\Enums\ReportType;
use App\Models\User;
use App\Services\Reports\ReportGenerationService;
use App\Services\StaffDashboardService;
use Illuminate\Support\Facades\Cache;

it('caches boot payloads for identical report inputs', function () {
    Cache::flush();

    $dashboardService = \Mockery::mock(StaffDashboardService::class);
    $dashboardService->shouldReceive('buildReportPayload')
        ->once()
        ->andReturn(['cached' => true]);

    app()->instance(StaffDashboardService::class, $dashboardService);

    $user = new User(['id' => 1]);
    $user->setRelation('staff', null);

    $service = app(ReportGenerationService::class);

    $periodConfig = [
        'period_type' => 'annual',
        'year' => 2026,
    ];

    $first = $service->boot(ReportType::Funding, $periodConfig, $user);
    $second = $service->boot(ReportType::Funding, $periodConfig, $user);

    expect($first)->toBe(['cached' => true]);
    expect($second)->toBe(['cached' => true]);
});