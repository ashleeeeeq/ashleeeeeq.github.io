<?php

namespace App\Services\Reports;

use App\Enums\ReportType;
use App\Models\Report;

class ReportContextBuilder
{
    public function buildPrompts(Report $report): array
    {
        $type = ReportType::tryFrom($report->type);
        if (!$type || !$report->boot_payload) {
            return [];
        }

        $payload = $report->boot_payload;
        $periodLabel = $report->periodLabel();
        $prompts = [];

        match ($type) {
            ReportType::OrganizationalOverview => $this->buildMultiSectionPrompts($prompts, $payload, $periodLabel),
            ReportType::Education => $this->buildEducationPrompts($prompts, $payload, $periodLabel),
            ReportType::Sports => $this->buildSportsPrompts($prompts, $payload, $periodLabel),
            ReportType::Funding => $this->buildFundingPrompts($prompts, $payload, $periodLabel),
        };

        return $prompts;
    }

    private function buildMultiSectionPrompts(array &$prompts, array $payload, string $periodLabel): void
    {
        $prompts['executive_summary'] = $this->executiveSummaryPrompt($payload, $periodLabel);

        if (isset($payload['education'])) {
            $this->addEducationSectionPrompts($prompts, $payload, $periodLabel);
        }

        if (isset($payload['sports'])) {
            $this->addSportsSectionPrompts($prompts, $payload, $periodLabel);
        }

        if (isset($payload['funding'])) {
            $this->addFundingSectionPrompts($prompts, $payload, $periodLabel);
        }
    }

    private function buildEducationPrompts(array &$prompts, array $payload, string $periodLabel): void
    {
        $prompts['executive_summary'] = $this->executiveSummaryPrompt($payload, $periodLabel);
        if (isset($payload['education'])) {
            $this->addEducationSectionPrompts($prompts, $payload, $periodLabel);
        }
    }

    private function buildSportsPrompts(array &$prompts, array $payload, string $periodLabel): void
    {
        $prompts['executive_summary'] = $this->executiveSummaryPrompt($payload, $periodLabel);
        if (isset($payload['sports'])) {
            $this->addSportsSectionPrompts($prompts, $payload, $periodLabel);
        }
    }

    private function buildFundingPrompts(array &$prompts, array $payload, string $periodLabel): void
    {
        $prompts['executive_summary'] = $this->executiveSummaryPrompt($payload, $periodLabel);
        if (isset($payload['funding'])) {
            $this->addFundingSectionPrompts($prompts, $payload, $periodLabel);
        }
    }

    private function addEducationSectionPrompts(array &$prompts, array $payload, string $periodLabel): void
    {
        $data = $payload['education'];
        $act = $payload['educationActivity'] ?? [];

        $prompts['education'] = $this->educationNarrativePrompt($data, $periodLabel, 'Education');

        $grades = $data['averageGradesByStage'] ?? [];
        if (!empty(array_filter($grades))) {
            $prompts['academic_performance'] = $this->academicPerformancePrompt($grades, $periodLabel);
        }

        if (!empty(array_filter($data['transitionRates'] ?? [], fn($v) => $v > 0))) {
            $prompts['transition_rates'] = $this->transitionRatesPrompt($data['transitionRates'], $periodLabel, $data['transitionRatesByAcademicYear'] ?? [], $data['transitionRateBreakdown'] ?? []);
        }

        if (!empty(array_filter($data['completionRates'] ?? [], fn($v) => $v > 0))) {
            $prompts['completion_rates'] = $this->completionRatesPrompt($data['completionRates'], $periodLabel, $data['completionRatesByAcademicYear'] ?? [], $data['completionRateBreakdown'] ?? []);
        }

        if (!empty(array_filter($data['graduatesByStage'] ?? [], fn($v) => $v > 0))) {
            $prompts['graduates_by_stage'] = $this->graduatesByStagePrompt($data['graduatesByStage'], $periodLabel, $data['graduatesByStageByYear'] ?? []);
        }

        $scholarsData = $data['scholarsByEducationalStage']['data'] ?? [];
        if (!empty(array_filter($scholarsData, fn($v) => $v > 0))) {
            $prompts['scholars_by_stage'] = $this->scholarsByStagePrompt($data['scholarsByEducationalStage'], $periodLabel, $data['scholarsByEducationalStageByYear'] ?? []);
        }

        if ($act) {
            $prompts['education_participation'] = $this->educationParticipationPrompt($act, $periodLabel);
        }
    }

    private function addSportsSectionPrompts(array &$prompts, array $payload, string $periodLabel): void
    {
        $data = $payload['sports'];
        $act = $payload['sportsActivity'] ?? [];

        $prompts['sports'] = $this->sportsNarrativePrompt($data, $periodLabel, 'Sports');

        if (!empty($data['newPlayersBySportsType'])) {
            $prompts['new_players'] = $this->newPlayersPrompt($data['newPlayersBySportsType'], $periodLabel, $data['newPlayersBySportsTypeByYear'] ?? []);
        }

        if (!empty($data['activePlayersBySportsType'])) {
            $prompts['active_players'] = $this->activePlayersPrompt($data['activePlayersBySportsType'], $periodLabel, $data['activePlayersBySportsTypeByYear'] ?? []);
        }

        if ($act) {
            $prompts['sports_participation_cards'] = $this->sportsParticipationCardsPrompt($act, $periodLabel);
            $prompts['sports_participation_trends'] = $this->sportsParticipationTrendsPrompt($act, $periodLabel);
        }
    }

    private function addFundingSectionPrompts(array &$prompts, array $payload, string $periodLabel): void
    {
        $data = $payload['funding'];
        $prompts['funding'] = $this->fundingNarrativePrompt($data, $periodLabel);
        $prompts['funding_target_allocation_analysis'] = $this->fundingTargetAllocationPrompt($data, $periodLabel);
        $prompts['funding_trend_analysis'] = $this->fundingTrendAnalysisPrompt($data, $periodLabel);
    }

    private function executiveSummaryPrompt(array $payload, string $periodLabel): array
    {
        $summary = [];

        if (isset($payload['education'])) {
            $e = $payload['education'];
            $summary[] = "Education Program: {$e['scholars']} active scholars, {$e['graduatesTotal']} graduates, {$e['scholarsExited']} exited.";
        }

        if (isset($payload['sports'])) {
            $s = $payload['sports'];
            $summary[] = "Sports Program: {$s['registeredPlayers']} registered players, {$s['competitionParticipation']} competitions participated in.";
        }

        if (isset($payload['funding'])) {
            $f = $payload['funding'];
            $summary[] = "Funding: {$f['newDonors']} new donors, {$f['grantsReceived']} grants received, ₱" . number_format((float) ($f['fundsReceived'] ?? 0), 2) . " received, ₱" . number_format((float) ($f['fundsAllocated'] ?? 0), 2) . " allocated, ₱" . number_format((float) ($f['fundingTarget'] ?? 0), 2) . " target.";
        }

        $contextText = implode("\n", $summary);

        return [
            'section' => 'Executive Summary',
            'wordCount' => 200,
            'system' => 'You are a professional report writer for a non-profit organization called Fairplay For All Foundation. Write a concise, professional executive summary in paragraph form. Use US English spelling. Do not use markdown formatting. Do not wrap the response in any tags or quotes. Avoid repeating words, phrases, or sentence structures — vary your vocabulary. Write in third-person.',
            'user' => "Write a 250-word executive summary for the {$periodLabel} report covering the following data:\n\n{$contextText}. Do not assume the beginning period is the inception of the foundation.",
        ];
    }

    private function educationNarrativePrompt(array $data, string $periodLabel, string $programName): array
    {
        $text = "Beneficiaries Overview (as of {$periodLabel}):\n"
            . "- Active scholars: {$data['scholars']}\n"
            . "- Total graduates: {$data['graduatesTotal']}\n"
            . "- Scholars exited: {$data['scholarsExited']}";

        return [
            'section' => $programName . ' Program - Beneficiaries Overview',
            'wordCount' => 200,
            'system' => 'You are a professional report writer for a non-profit organization. Write a concise narrative paragraph analyzing the education program data. Use US English spelling. Do not use markdown formatting. Do not wrap the response in any tags or quotes. Avoid repeating words, phrases, or sentence structures — vary your vocabulary. Write in third-person.',
            'user' => "Write a 250-word narrative analyzing this education program data for {$periodLabel}:{$text}\n\nFocus on trends, notable changes, and overall program health. Do not assume the beginning period is the inception of the foundation.",
        ];
    }

    private function academicPerformancePrompt(array $grades, string $periodLabel): array
    {
        $text = '';
        foreach ($grades as $stage => $score) {
            $text .= "- " . ucfirst($stage) . ": {$score}\n";
        }

        return [
            'section' => 'Academic Performance',
            'wordCount' => 200,
            'system' => 'You are a professional report writer for a non-profit organization. Write a concise narrative paragraph analyzing academic performance data by educational stage. Use US English spelling. Do not use markdown formatting. Do not wrap the response in any tags or quotes. Avoid repeating words, phrases, or sentence structures — vary your vocabulary. Write in third-person.',
            'user' => "Write a 250-word narrative analyzing academic performance by educational stage for {$periodLabel}:\n{$text}\n\nHighlight notable differences between stages and overall academic health. Do not assume the beginning period is the inception of the foundation.",
        ];
    }

    private function transitionRatesPrompt(array $rates, string $periodLabel, array $byYear = [], array $breakdown = []): array
    {
        $rates = array_filter($rates, fn($v) => (float) $v > 0);
        $text = '';
        foreach ($rates as $stage => $rate) {
            $successCount = $breakdown['successCounts'][$stage] ?? null;
            $totalCount = $breakdown['totalCounts'][$stage] ?? null;
            $countText = ($successCount !== null && $totalCount !== null) ? " ({$successCount}/{$totalCount} students)" : '';
            $text .= "- " . ucfirst($stage) . ": {$rate}%{$countText}\n";
        }

        $trendText = $this->formatMultiSeriesTrend('Transition Rates trend by academic year', $byYear);

        return [
            'section' => 'Transition Rates',
            'wordCount' => 200,
            'system' => 'You are a professional report writer for a non-profit organization. Write a concise narrative paragraph analyzing student transition rates between educational stages. Use US English spelling. Do not use markdown formatting. Do not wrap the response in any tags or quotes. Avoid repeating words, phrases, or sentence structures — vary your vocabulary. Write in third-person.',
            'user' => "Write a 250-word narrative analyzing transition rates for {$periodLabel}: {$text} {$trendText} Highlight transitions needing attention and overall trends. Exclude any data with 0/0 students and 0% rate, or simply 0% rate—do not mention or factor these in. They indicate no students were present at that time. Do not assume the beginning period is the inception of the foundation.",
        ];
    }

    private function completionRatesPrompt(array $rates, string $periodLabel, array $byYear = [], array $breakdown = []): array
    {
        $rates = array_filter($rates, fn($v) => (float) $v > 0);
        $text = '';
        foreach ($rates as $stage => $rate) {
            $completedCount = $breakdown['completedCounts'][$stage] ?? null;
            $totalCount = $breakdown['totalCounts'][$stage] ?? null;
            $countText = ($completedCount !== null && $totalCount !== null) ? " ({$completedCount}/{$totalCount} students)" : '';
            $text .= "- " . ucfirst($stage) . ": {$rate}%{$countText}\n";
        }

        $trendText = $this->formatMultiSeriesTrend('Completion Rates trend by academic year', $byYear);

        return [
            'section' => 'Completion Rates',
            'wordCount' => 200,
            'system' => 'You are a professional report writer for a non-profit organization. Write a concise narrative paragraph analyzing completion rates by educational stage. Use US English spelling. Do not use markdown formatting. Do not wrap the response in any tags or quotes. Avoid repeating words, phrases, or sentence structures — vary your vocabulary. Write in third-person.',
            'user' => "Write a 250-word narrative analyzing completion rates for {$periodLabel}:\n{$text}\n\n{$trendText}Discuss performance across stages and areas for improvement. Exclude any data with 0/0 students and 0% rate, or simply 0% rate—do not mention or factor these in. They indicate no students were present at that time. Do not assume the beginning period is the inception of the foundation.",
        ];
    }

    private function graduatesByStagePrompt(array $graduates, string $periodLabel, array $byYear = []): array
    {
        $graduates = array_filter($graduates, fn($v) => (int) $v > 0);
        $trendText = $this->formatMultiSeriesTrend('Graduates by Stage trend by academic year', $byYear);

        return [
            'section' => 'Graduates by Stage',
            'wordCount' => 200,
            'system' => 'You are a professional report writer for a non-profit organization. Write a concise narrative paragraph analyzing graduate distribution by educational stage. Use US English spelling. Do not use markdown formatting. Do not wrap the response in any tags or quotes. Avoid repeating words, phrases, or sentence structures — vary your vocabulary. Write in third-person.',
            'user' => "Write a 250-word narrative analyzing graduates by educational stage for {$periodLabel}:\n"
                . json_encode($graduates)
                . "\n\n{$trendText}Discuss which stages produced the most graduates and overall trends. Do not assume the beginning period is the inception of the foundation.",
        ];
    }

    private function scholarsByStagePrompt(array $scholars, string $periodLabel, array $byYear = []): array
    {
        $filtered = ['labels' => [], 'data' => []];
        foreach ($scholars['labels'] ?? [] as $i => $label) {
            if (($scholars['data'][$i] ?? 0) > 0) {
                $filtered['labels'][] = $label;
                $filtered['data'][] = $scholars['data'][$i];
            }
        }
        $scholars = $filtered;
        $trendText = $this->formatMultiSeriesTrend('Scholars by Stage trend by academic year', $byYear);

        return [
            'section' => 'Scholars by Stage',
            'wordCount' => 200,
            'system' => 'You are a professional report writer for a non-profit organization. Write a concise narrative paragraph analyzing scholar distribution by educational stage. Use US English spelling. Do not use markdown formatting. Do not wrap the response in any tags or quotes. Avoid repeating words, phrases, or sentence structures — vary your vocabulary. Write in third-person.',
            'user' => "Write a 250-word narrative analyzing scholar distribution by educational stage for {$periodLabel}:\n"
                . json_encode($scholars)
                . "\n\n{$trendText}Discuss trends in scholar population across stages. Do not assume the beginning period is the inception of the foundation.",
        ];
    }

    private function educationParticipationPrompt(array $act, string $periodLabel): array
    {
        $trendText = $this->formatAttendanceTrendText($act);

        return [
            'section' => 'Education Program Participation',
            'wordCount' => 200,
            'system' => 'You are a professional report writer for a non-profit organization. Write a concise narrative paragraph analyzing education program participation. Use US English spelling. Do not use markdown formatting. Do not wrap the response in any tags or quotes. Avoid repeating words, phrases, or sentence structures — vary your vocabulary. Write in third-person.',
            'user' => "Write a 250-word narrative analyzing education program participation for {$periodLabel}:\n"
                . "- Average EQ session attendance: {$act['eqSessionAttendance']}%\n"
                . "- Average tutorial session attendance: {$act['tutorialSessionAttendance']}%\n\n"
                . "{$trendText}Discuss attendance levels, trends, and engagement. Do not assume the beginning period is the inception of the foundation.",
        ];
    }

    private function sportsNarrativePrompt(array $data, string $periodLabel, string $programName): array
    {
        return [
            'section' => $programName . ' Program - Beneficiaries Overview',
            'wordCount' => 200,
            'system' => 'You are a professional report writer for a non-profit organization. Write a concise narrative paragraph analyzing the sports program data. Use US English spelling. Do not use markdown formatting. Do not wrap the response in any tags or quotes. Avoid repeating words, phrases, or sentence structures — vary your vocabulary. Write in third-person.',
            'user' => "Write a 250-word narrative analyzing this sports program data for {$periodLabel}:\n"
                . "- Registered players: {$data['registeredPlayers']}\n"
                . "- Competitions participated in: {$data['competitionParticipation']}\n"
                . "Focus on trends, participation levels, and program engagement. Do not assume the beginning period is the inception of the foundation.",
        ];
    }

    private function newPlayersPrompt(array $data, string $periodLabel, array $byYear = []): array
    {
        $trendText = $this->formatMultiSeriesTrend('New Players trend by year', $byYear);

        return [
            'section' => 'New Players by Sports Type',
            'wordCount' => 200,
            'system' => 'You are a professional report writer for a non-profit organization. Write a concise narrative paragraph analyzing new player registrations by sports type. Use US English spelling. Do not use markdown formatting. Do not wrap the response in any tags or quotes. Avoid repeating words, phrases, or sentence structures — vary your vocabulary. Write in third-person.',
            'user' => "Write a 250-word narrative analyzing new player registrations by sports type for {$periodLabel}:\n"
                . json_encode($data)
                . "\n\n{$trendText}Discuss which sports are growing and overall recruitment trends. Do not assume the beginning period is the inception of the foundation.",
        ];
    }

    private function activePlayersPrompt(array $data, string $periodLabel, array $byYear = []): array
    {
        $trendText = $this->formatMultiSeriesTrend('Active Players trend by year', $byYear);

        return [
            'section' => 'Active Players by Sports Type',
            'wordCount' => 200,
            'system' => 'You are a professional report writer for a non-profit organization. Write a concise narrative paragraph analyzing currently active players by sports type. Use US English spelling. Do not use markdown formatting. Do not wrap the response in any tags or quotes. Avoid repeating words, phrases, or sentence structures — vary your vocabulary. Write in third-person.',
            'user' => "Write a 250-word narrative analyzing currently active players by sports type for {$periodLabel}:\n"
                . json_encode($data)
                . "\n\n{$trendText}Discuss player retention and popular sports. Do not assume the beginning period is the inception of the foundation.",
        ];
    }

    private function sportsParticipationCardsPrompt(array $act, string $periodLabel): array
    {
        return [
            'section' => 'Sports Program Participation – KPIs',
            'wordCount' => 150,
            'system' => 'You are a professional report writer for a non-profit organization. Write a concise narrative paragraph analyzing sports program participation KPIs. Use US English spelling. Do not use markdown formatting. Do not wrap the response in any tags or quotes. Avoid repeating words, phrases, or sentence structures — vary your vocabulary. Write in third-person.',
            'user' => "Write a 150-word narrative analyzing the sports participation KPIs for {$periodLabel}:\n"
                . "- Average training session attendance: {$act['trainingSessionAttendance']}%\n"
                . "- Total unique visits: {$act['uniqueVisits']}\n\n"
                . "Discuss attendance levels, engagement, and overall participation. Do not assume the beginning period is the inception of the foundation.",
        ];
    }

    private function sportsParticipationTrendsPrompt(array $act, string $periodLabel): array
    {
        $trendText = $this->formatAttendanceTrendText($act);

        return [
            'section' => 'Sports Program Participation – Trends',
            'wordCount' => 150,
            'system' => 'You are a professional report writer for a non-profit organization. Write a concise narrative paragraph analyzing sports participation trends over time. Use US English spelling. Do not use markdown formatting. Do not wrap the response in any tags or quotes. Avoid repeating words, phrases, or sentence structures — vary your vocabulary. Write in third-person.',
            'user' => "Write a 150-word narrative analyzing the sports participation trends for {$periodLabel}:\n"
                . "{$trendText}Discuss attendance trends and unique visits patterns. Do not assume the beginning period is the inception of the foundation.",
        ];
    }

    private function fundingNarrativePrompt(array $data, string $periodLabel): array
    {
        $monthlyText = $this->monthlyTrendText($data);

        return [
            'section' => 'Donors & Grants - Overview',
            'wordCount' => 200,
            'system' => 'You are a professional report writer for a non-profit organization. Write a concise narrative paragraph analyzing the funding data. Use US English spelling. Do not use markdown formatting. Do not wrap the response in any tags or quotes. Avoid repeating words, phrases, or sentence structures — vary your vocabulary. Write in third-person.',
            'user' => "Write a 250-word narrative analyzing this funding data for {$periodLabel}:\n"
                . "- New donors: {$data['newDonors']}\n"
                . "- Grants received: {$data['grantsReceived']}\n"
                . "- Funds received: ₱" . number_format((float) ($data['fundsReceived'] ?? 0), 2) . "\n"
                . "- Funds allocated: ₱" . number_format((float) ($data['fundsAllocated'] ?? 0), 2) . "\n"
                . "- Funding target: ₱" . number_format((float) ($data['fundingTarget'] ?? 0), 2) . "\n"
                . "Discuss funding performance, gaps and sustainability. Do not assume the beginning period is the inception of the foundation.",
        ];
    }

    private function fundingTargetAllocationPrompt(array $data, string $periodLabel): array
    {
        return [
            'section' => 'Funds Received vs Target & Allocation',
            'wordCount' => 200,
            'system' => 'You are a professional report writer for a non-profit organization. Write a concise narrative paragraph analyzing funds received versus the funding target and allocation efficiency. Use US English spelling. Do not use markdown formatting. Do not wrap the response in any tags or quotes. Avoid repeating words, phrases, or sentence structures — vary your vocabulary. Write in third-person.',
            'user' => "Write a 200-word narrative analyzing the funding performance for {$periodLabel}:\n"
                . "- Grants received: {$data['grantsReceived']}\n"
                . "- Funds received: ₱" . number_format((float) ($data['fundsReceived'] ?? 0), 2) . "\n"
                . "- Funding target: ₱" . number_format((float) ($data['fundingTarget'] ?? 0), 2) . "\n"
                . "- Received vs target: {$data['receivedProgressPercent']}%\n"
                . "- Funds allocated: ₱" . number_format((float) ($data['fundsAllocated'] ?? 0), 2) . "\n"
                . "- Allocated vs received: {$data['allocatedVsReceivedPercent']}%\n"
                . "Analyze progress toward the funding target and allocation efficiency. Do not assume the beginning period is the inception of the foundation.",
        ];
    }

    private function fundingTrendAnalysisPrompt(array $data, string $periodLabel): array
    {
        $monthlyText = $this->monthlyTrendText($data);

        return [
            'section' => 'Funds Received vs Allocated Trend',
            'wordCount' => 200,
            'system' => 'You are a professional report writer for a non-profit organization. Write a concise narrative paragraph analyzing the trend of funds received versus allocated over the period. Use US English spelling. Do not use markdown formatting. Do not wrap the response in any tags or quotes. Avoid repeating words, phrases, or sentence structures — vary your vocabulary. Write in third-person.',
            'user' => "Write a 200-word narrative analyzing the received vs allocated trend for {$periodLabel}:\n"
                . "Monthly trend (received / allocated):\n{$monthlyText}\n\n"
                . "Analyze seasonal patterns, trends, and recommendations based on the comparison. Do not assume the beginning period is the inception of the foundation.",
        ];
    }

    private function monthlyTrendText(array $data): string
    {
        $received = $data['trendReceived'] ?? $data['monthlyReceived'] ?? [];
        $allocated = $data['trendAllocated'] ?? $data['monthlyAllocated'] ?? [];
        $labels = $data['trendLabels'] ?? ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        $lines = [];
        foreach ($labels as $i => $label) {
            $r = number_format($received[$i] ?? 0, 2);
            $a = number_format($allocated[$i] ?? 0, 2);
            if ((float)($received[$i] ?? 0) > 0 || (float)($allocated[$i] ?? 0) > 0) {
                $lines[] = "  {$label}: received ₱{$r}, allocated ₱{$a}";
            }
        }
        return implode("\n", $lines);
    }

    private function formatAttendanceTrendText(array $act): string
    {
        $trends = $act['trends'] ?? [];
        if (empty($trends)) {
            return '';
        }

        $lines = [];
        foreach ($trends as $key => $series) {
            $labels = $series['labels'] ?? [];
            $data = $series['data'] ?? [];
            if (empty($labels)) continue;

            $label = match ($key) {
                'eqSessionAttendance' => 'EQ Session Attendance',
                'tutorialSessionAttendance' => 'Tutorial Session Attendance',
                'trainingSessionAttendance' => 'Training Session Attendance',
                'uniqueVisits' => 'Unique Visits',
                default => $key,
            };

            $lines[] = "{$label}:";
            foreach ($labels as $i => $lbl) {
                $val = $data[$i] ?? null;
                if ($val !== null) {
                    $suffix = $key === 'uniqueVisits' ? '' : '%';
                    $lines[] = "  {$lbl}: {$val}{$suffix}";
                }
            }
        }

        return implode("\n", $lines) . "\n\n";
    }

    private function formatMultiSeriesTrend(string $title, array $trendData): string
    {
        $labels = $trendData['labels'] ?? [];
        $datasets = $trendData['datasets'] ?? [];
        if (empty($labels) || empty($datasets)) {
            return '';
        }

        $datasets = array_values(array_filter($datasets, fn($ds) => !empty(array_filter($ds['data'] ?? [], fn($v) => (float) $v > 0))));
        if (empty($datasets)) {
            return '';
        }

        $lines = ["{$title}:"];
        foreach ($labels as $i => $label) {
            $parts = [];
            foreach ($datasets as $dataset) {
                $val = $dataset['data'][$i] ?? null;
                if ($val !== null) {
                    $parts[] = $dataset['label'] . ': ' . $val;
                }
            }
            if (!empty($parts)) {
                $lines[] = '  ' . $label . ' — ' . implode(', ', $parts);
            }
        }
        return implode("\n", $lines) . "\n\n";
    }
}
