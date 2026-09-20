<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>{{ $report->typeEnum()?->label() ?? 'Report' }} – FAIRALL</title>
    <script src="chart.umd.js"></script>
    <link rel="icon" type="image/png" href="{{ asset('images/FAIRALL_LOGO.png') }}" sizes="48x48">
    <style>
        @page {
            margin: 0;
            size: A4;
            background-color: var(--color-primary1);
        }

        html {
            background-color: var(--color-primary1);
        }

        canvas {
            width: 100%;
            max-height: 210px;
        }

        .progress-bar {
            height: 6px;
            border-radius: 3px;
            background: rgba(255, 255, 255, 0.1);
            margin: 4px 0;
            overflow: hidden;
        }

        .progress-bar .fill {
            height: 100%;
            border-radius: 3px;
        }

        @page table-page {
            padding-top: 2rem;
            background-color: var(--color-primary1);
        }

        /* @media print {
            * {
                outline: 1px solid red;
            }
        } */
    </style>
</head>

<body class="bg-primary1 text-white leading-relaxed p-15 m-0"
    style="font-size:9.5pt;-webkit-print-color-adjust:exact;print-color-adjust:exact">

    @php
        $payload = $payload ?? [];
        $edu = $payload['education'] ?? [];
        $act = $payload['educationActivity'] ?? [];
        $spt = $payload['sports'] ?? [];
        $sptAct = $payload['sportsActivity'] ?? [];
        $fnd = $payload['funding'] ?? [];
        $fndTrendPeriod = $fnd['trendPeriod'] ?? 'monthly';
        $fndTrendPeriodLabel = match ($fndTrendPeriod) {
            'daily' => 'Day',
            'weekly' => 'Week',
            'monthly' => 'Month',
            'yearly' => 'Year',
            default => 'Month',
        };
        $hasEdu = !empty($edu);
        $hasSpt = !empty($spt);
        $hasFnd = !empty($fnd);
        $secNum = 1;
    @endphp

    <div class="text-center [page-break-after:always] min-h-[90vh] flex flex-col items-center justify-center">

        <div class="m-auto">
            <h1 class="font-extrabold uppercase tracking-wide mb-5 text-white text-5xl">
                {{ $report->typeEnum()?->label() ?? 'Organizational Overview' }}</h1>
            <hr class="w-150 h-0.5 bg-accent1 mx-auto mb-4 border-0 rounded">
            <p class="text-white/50 mb-10 text-2xl">Fairplay for All Foundation – Performance Report</p>
            <p class="text-white/35 text-lg">{{ $report->periodLabel() }}</p>
            <p class="text-white/35 mt-1 text-lg">
                {{ $report->date_from?->format('F d, Y') }} – {{ $report->date_to?->format('F d, Y') }}
            </p>
        </div>

        <p class="text-white/50 mb-20 text-lg">Disclaimer: This report was created by FAIRALL and
            contains
            AI generated insights that may not be accurate. A review may still be required.</p>

    </div>

    <div class="[page-break-after:always] min-h-[90vh] pt-8">
        <h2 class="font-bold text-white mt-6 mb-3 pb-1.5 border-b border-white/10 text-2xl">Table of Contents
        </h2>
        <ol class="list-none [counter-reset:toc]">
            <li class="[counter-increment:toc] text-white/70 border-b border-dotted border-white/8 before:content-[counter(toc)_'._'] before:font-bold before:text-accent1 before:mr-1.5 text-xl"
                style="padding-top:7px;padding-bottom:7px;border-white/6"><a href="#executive-summary" class="text-white/70 no-underline">Executive Summary</a></li>
            @if ($hasEdu)
                <li class="[counter-increment:toc] text-white/70 border-b border-dotted border-white/8 before:content-[counter(toc)_'._'] before:font-bold before:text-accent1 before:mr-1.5 text-xl"
                    style="padding-top:7px;padding-bottom:7px;border-white/6"><a href="#education-program" class="text-white/70 no-underline">Education Program</a>
                    <ul class="list-disc marker:text-accent1 ml-12 text-lg mt-1 mb-1">
                        <li>Education Beneficiaries Overview</li>
                        @if (!empty($narratives['academic_performance']))
                            <li>Average Academic Performance by Educational Stage</li>
                        @endif
                        @if (!empty($narratives['transition_rates']))
                            <li>Transition Rates</li>
                        @endif
                        @if (!empty($narratives['completion_rates']))
                            <li>Completion Rates</li>
                        @endif
                        @if (!empty($narratives['graduates_by_stage']))
                            <li>Number of Graduates by Educational Stage</li>
                        @endif
                        @if (!empty($narratives['scholars_by_stage']))
                            <li>Number of Scholars by Educational Stage</li>
                        @endif
                        @if (!empty($narratives['education_participation']))
                            <li>Program Participation</li>
                        @endif
                    </ul>
                </li>
            @endif
            @if ($hasSpt)
                <li class="[counter-increment:toc] text-white/70 border-b border-dotted border-white/8 before:content-[counter(toc)_'._'] before:font-bold before:text-accent1 before:mr-1.5 text-xl"
                    style="padding-top:7px;padding-bottom:7px;border-white/6"><a href="#sports-program" class="text-white/70 no-underline">Sports Program</a>
                    <ul class="list-disc marker:text-accent1 ml-12 text-lg mt-1 mb-1">
                        <li>Sports Beneficiaries Overview</li>
                        @if (!empty($narratives['new_players']))
                            <li>Number of New Players by Sports Type</li>
                        @endif
                        @if (!empty($narratives['active_players']))
                            <li>Currently Active Players by Sports Type</li>
                        @endif
                        @if (!empty($narratives['sports_participation_cards']) || !empty($narratives['sports_participation_trends']))
                            <li>Program Participation</li>
                        @endif
                    </ul>
                </li>
            @endif
            @if ($hasFnd)
                <li class="[counter-increment:toc] text-white/70 border-b border-dotted border-white/8 before:content-[counter(toc)_'._'] before:font-bold before:text-accent1 before:mr-1.5 text-xl"
                    style="padding-top:7px;padding-bottom:7px;border-white/6"><a href="#donors-grants" class="text-white/70 no-underline">Donors &amp; Grants</a>
                    <ul class="list-disc marker:text-accent1 ml-12 text-lg mt-1 mb-1">
                        <li>Funding Overview</li>
                        <li>Funds Received vs Target</li>
                        <li>Allocated vs Received</li>
                        <li>Funds Received vs Allocated Comparison by {{ $fndTrendPeriodLabel }}</li>
                    </ul>
                </li>
            @endif
        </ol>
        @php
            $hasAppTables =
                ($hasEdu &&
                    (!empty($edu['activeScholarsList']) ||
                        !empty($edu['graduatesList']) ||
                        !empty($edu['exitedScholarsList']))) ||
                ($hasSpt && (!empty($spt['registeredPlayersList']) || !empty($spt['competitionsList']))) ||
                ($hasFnd && (!empty($fnd['newDonorsList']) || !empty($fnd['grantsReceivedList'])));
        @endphp
        @if ($hasAppTables)
            <li class="[counter-increment:toc] list-none text-white/70 border-b border-dotted border-white/8 before:content-[counter(toc)_'._'] before:font-bold before:text-accent1 before:mr-1.5 text-xl"
                style="padding-top:7px;padding-bottom:7px;border-white/6"><a href="#appendices" class="text-white/70 no-underline">Appendices</a>
                <ul class="list-disc marker:text-accent1 ml-12 text-lg mt-1 mb-1">
                    @if (!empty($edu['activeScholarsList']))
                        <li>Active Scholars List</li>
                    @endif
                    @if (!empty($edu['graduatesList']))
                        <li>Graduates List</li>
                    @endif
                    @if (!empty($edu['exitedScholarsList']))
                        <li>Exited Scholars List</li>
                    @endif
                    @if (!empty($spt['registeredPlayersList']))
                        <li>Registered Players List</li>
                    @endif
                    @if (!empty($spt['competitionsList']))
                        <li>Competitions List</li>
                    @endif
                    @if (!empty($fnd['newDonorsList']))
                        <li>New Donors List</li>
                    @endif
                    @if (!empty($fnd['grantsReceivedList']))
                        <li>Grants Received List</li>
                    @endif
                </ul>
            </li>
        @endif
    </div>

    <div
        @if ($hasEdu || $hasSpt || $hasFnd) class="[page-break-before:always] min-h-[90vh] flex flex-col justify-between pt-8" @endif>
        <div>
            <h2 id="executive-summary" class="font-bold text-white mt-6 mb-3 pb-1.5 border-b border-white/10 text-2xl">1. Executive
                Summary</h2>
            @if (!empty($narratives['executive_summary']))
                <div class="text-white/70 leading-relaxed text-sm pb-5 text-justify">
                    {{ $narratives['executive_summary'] }}</div>
            @endif
        </div>

        <div class="mt-auto">
            <h4 class="font-semibold text-white/60 mt-3 mb-1.5 uppercase tracking-wider text-lg"
                style="letter-spacing:0.2em">Key KPIs</h4>
            <div class="grid grid-cols-2 gap-1.5 my-2 mb-3.5">
                @if ($hasEdu)
                    <div class="p-2 px-3  text-white/70" style="font-size:12pt">
                        <strong class="text-white">Active Scholars:</strong>
                        {{ $edu['scholars'] ?? 0 }}
                    </div>
                    <div class="p-2 px-3  text-white/70" style="font-size:12pt">
                        <strong class="text-white">Graduates:</strong>
                        {{ $edu['graduatesTotal'] ?? 0 }}
                    </div>
                    <div class="p-2 px-3  text-white/70" style="font-size:12pt">
                        <strong class="text-white">Scholars Exited:</strong>
                        {{ $edu['scholarsExited'] ?? 0 }}
                    </div>
                @endif
                @if ($hasSpt)
                    <div class="p-2 px-3  text-white/70" style="font-size:12pt">
                        <strong class="text-white">Registered Players:</strong>
                        {{ $spt['registeredPlayers'] ?? 0 }}
                    </div>
                    <div class="p-2 px-3  text-white/70" style="font-size:12pt">
                        <strong class="text-white">Competitions:</strong>
                        {{ $spt['competitionParticipation'] ?? 0 }}
                    </div>
                @endif
                @if ($hasFnd)
                    <div class="p-2 px-3  text-white/70" style="font-size:12pt">
                        <strong class="text-white">New Donors:</strong>
                        {{ $fnd['newDonors'] ?? 0 }}
                    </div>
                    <div class="p-2 px-3  text-white/70" style="font-size:12pt">
                        <strong class="text-white">Grants Received:</strong>
                        {{ $fnd['grantsReceived'] ?? 0 }}
                    </div>
                    <div class="p-2 px-3  text-white/70" style="font-size:12pt">
                        <strong class="text-white">Funds Received:</strong>
                        ₱{{ number_format((float) ($fnd['fundsReceived'] ?? 0), 2) }}
                    </div>
                    <div class="p-2 px-3  text-white/70" style="font-size:12pt">
                        <strong class="text-white">Funds Allocated:</strong>
                        ₱{{ number_format((float) ($fnd['fundsAllocated'] ?? 0), 2) }}
                    </div>
                    <div class="p-2 px-3  text-white/70" style="font-size:12pt">
                        <strong class="text-white">Funding Target:</strong>
                        ₱{{ number_format((float) ($fnd['fundingTarget'] ?? 0), 2) }}
                    </div>
                @endif
            </div>
        </div>

    </div>

    @if ($hasEdu)
        <div class="[page-break-before:always] pt-8">
            <h2 id="education-program" class="font-bold text-white mt-6 mb-3 pb-1.5 border-b border-white/10 text-2xl">2.
                Education Program</h2>

            <h3 class="font-bold text-white/90 mb-2 text-xl" style="margin-top:18px">Beneficiaries Overview
            </h3>
            <div class="flex gap-2 mb-3.5">
                <div class="flex-1 p-3 rounded-2xl relative overflow-hidden"
                    style="background: linear-gradient(135deg, rgba(59,130,246,0.12) 0%, rgba(59,130,246,0.03) 100%); border: 1px solid rgba(59,130,246,0.15);">
                    <div class="absolute top-0 right-0 w-16 h-16 opacity-10 rounded-full -translate-y-1/2 translate-x-1/2"
                        style="background: #3b82f6;"></div>
                    <div
                        style="font-size:10pt;text-transform:uppercase;letter-spacing:0.2em;color:rgba(255,255,255,0.5);">
                        Active Scholars</div>
                    <div class="font-extrabold text-white mt-2 text-xl">{{ $edu['scholars'] ?? 0 }}</div>
                </div>
                <div class="flex-1 p-3 rounded-2xl relative overflow-hidden"
                    style="background: linear-gradient(135deg, rgba(16,185,129,0.12) 0%, rgba(16,185,129,0.03) 100%); border: 1px solid rgba(16,185,129,0.15);">
                    <div class="absolute top-0 right-0 w-16 h-16 opacity-10 rounded-full -translate-y-1/2 translate-x-1/2"
                        style="background: #10b981;"></div>
                    <div
                        style="font-size:10pt;text-transform:uppercase;letter-spacing:0.2em;color:rgba(255,255,255,0.5);">
                        Graduates</div>
                    <div class="font-extrabold text-white mt-2 text-xl">{{ $edu['graduatesTotal'] ?? 0 }}</div>
                </div>
                <div class="flex-1 p-3 rounded-2xl relative overflow-hidden"
                    style="background: linear-gradient(135deg, rgba(245,158,11,0.12) 0%, rgba(245,158,11,0.03) 100%); border: 1px solid rgba(245,158,11,0.15);">
                    <div class="absolute top-0 right-0 w-16 h-16 opacity-10 rounded-full -translate-y-1/2 translate-x-1/2"
                        style="background: #f59e0b;"></div>
                    <div
                        style="font-size:10pt;text-transform:uppercase;letter-spacing:0.2em;color:rgba(255,255,255,0.5);">
                        Exited</div>
                    <div class="font-extrabold text-white mt-2 text-xl">{{ $edu['scholarsExited'] ?? 0 }}</div>
                </div>
            </div>
            <p class="text-white/50 mt-4 mb-4 italic">See appendices for the <a href="#app-active-scholars"
                    class="text-accent1 underline">list of active scholars</a>, <a href="#app-graduates"
                    class="text-accent1 underline">graduates</a>, and <a href="#app-exited-scholars"
                    class="text-accent1 underline">exited beneficiaries</a>.</p>
            @if (!empty($narratives['education']))
                <h2 class="uppercase text-lg pt-5 text-white mb-2">Analysis</h2>
                <hr class="w-50 h-0.5 bg-accent1/50 mb-4 border-0 rounded">
                <div class="text-white/70 leading-relaxed text-sm pb-5 text-justify">{{ $narratives['education'] }}
                </div>
            @endif

            <div style="padding-top:1rem;break-inside:avoid;page-break-inside:avoid;">
                <h3 class="font-bold text-white/90 mb-2.5 text-xl" style="margin-top:18px">Academic Performance by
                    Stage</h3>
                @php $grades = $edu['averageGradesByStage'] ?? []; @endphp
                @if (!empty($grades))
                    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:0.5rem;margin-bottom:0.875rem;">
                        <div class="rounded-2xl p-3 text-center relative overflow-hidden"
                            style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05);">
                            <div class="absolute top-0 left-0 w-full h-20 opacity-20"
                                style="background: linear-gradient(180deg, #ffcc33 0%, transparent 100%);"></div>
                            <div class="relative z-10">
                                <div style="width:112px;height:112px;margin:0 auto 0.5rem;position:relative;">
                                    <svg viewBox="0 0 112 112"
                                        style="width:100%;height:100%;transform:rotate(-90deg);">
                                        <circle cx="56" cy="56" r="48" fill="none"
                                            stroke="rgba(255,255,255,0.05)" stroke-width="7" />
                                        <circle cx="56" cy="56" r="48" fill="none" stroke="#ffcc33"
                                            stroke-width="7" stroke-linecap="round" stroke-dasharray="301.59"
                                            stroke-dashoffset="{{ ($grades['elementary'] ?? 0) > 0 ? 301.59 - (301.59 * (float) ($grades['elementary'] ?? 0)) / 100 : 301.59 }}" />
                                    </svg>
                                    <div
                                        style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;">
                                        <span class="font-extrabold"
                                            style="font-size:16pt;color:#ffcc33;">{{ $grades['elementary'] ?? 'N/A' }}</span>
                                    </div>
                                </div>
                                <div
                                    style="font-size:10pt;text-transform:uppercase;letter-spacing:0.2em;color:rgba(255,255,255,0.4);">
                                    Elementary</div>
                                <div style="font-size:10pt;color:rgba(255,255,255,0.2);">Grade Level 1–6</div>
                            </div>
                        </div>
                        <div class="rounded-2xl p-3 text-center relative overflow-hidden"
                            style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05);">
                            <div class="absolute top-0 left-0 w-full h-20 opacity-20"
                                style="background: linear-gradient(180deg, #3b82f6 0%, transparent 100%);"></div>
                            <div class="relative z-10">
                                <div style="width:112px;height:112px;margin:0 auto 0.5rem;position:relative;">
                                    <svg viewBox="0 0 112 112"
                                        style="width:100%;height:100%;transform:rotate(-90deg);">
                                        <circle cx="56" cy="56" r="48" fill="none"
                                            stroke="rgba(255,255,255,0.05)" stroke-width="7" />
                                        <circle cx="56" cy="56" r="48" fill="none" stroke="#3b82f6"
                                            stroke-width="7" stroke-linecap="round" stroke-dasharray="301.59"
                                            stroke-dashoffset="{{ ($grades['high_school'] ?? 0) > 0 ? 301.59 - (301.59 * (float) ($grades['high_school'] ?? 0)) / 100 : 301.59 }}" />
                                    </svg>
                                    <div
                                        style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;">
                                        <span class="font-extrabold"
                                            style="font-size:16pt;color:#3b82f6;">{{ $grades['high_school'] ?? 'N/A' }}</span>
                                    </div>
                                </div>
                                <div
                                    style="font-size:10pt;text-transform:uppercase;letter-spacing:0.2em;color:rgba(255,255,255,0.4);">
                                    High School</div>
                                <div style="font-size:10pt;color:rgba(255,255,255,0.2);">Grade Level 7–10</div>
                            </div>
                        </div>
                        <div class="rounded-2xl p-3 text-center relative overflow-hidden"
                            style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05);">
                            <div class="absolute top-0 left-0 w-full h-20 opacity-20"
                                style="background: linear-gradient(180deg, #10b981 0%, transparent 100%);"></div>
                            <div class="relative z-10">
                                <div style="width:112px;height:112px;margin:0 auto 0.5rem;position:relative;">
                                    <svg viewBox="0 0 112 112"
                                        style="width:100%;height:100%;transform:rotate(-90deg);">
                                        <circle cx="56" cy="56" r="48" fill="none"
                                            stroke="rgba(255,255,255,0.05)" stroke-width="7" />
                                        <circle cx="56" cy="56" r="48" fill="none" stroke="#10b981"
                                            stroke-width="7" stroke-linecap="round" stroke-dasharray="301.59"
                                            stroke-dashoffset="{{ ($grades['shs'] ?? 0) > 0 ? 301.59 - (301.59 * (float) ($grades['shs'] ?? 0)) / 100 : 301.59 }}" />
                                    </svg>
                                    <div
                                        style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;">
                                        <span class="font-extrabold"
                                            style="font-size:16pt;color:#10b981;">{{ $grades['shs'] ?? 'N/A' }}</span>
                                    </div>
                                </div>
                                <div
                                    style="font-size:10pt;text-transform:uppercase;letter-spacing:0.2em;color:rgba(255,255,255,0.4);">
                                    SHS</div>
                                <div style="font-size:10pt;color:rgba(255,255,255,0.2);">Grade Level 11–12</div>
                            </div>
                        </div>
                        <div class="rounded-2xl p-3 text-center relative overflow-hidden"
                            style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05);">
                            <div class="absolute top-0 left-0 w-full h-20 opacity-20"
                                style="background: linear-gradient(180deg, #f59e0b 0%, transparent 100%);"></div>
                            <div class="relative z-10">
                                <div style="width:112px;height:112px;margin:0 auto 0.5rem;position:relative;">
                                    <svg viewBox="0 0 112 112"
                                        style="width:100%;height:100%;transform:rotate(-90deg);">
                                        <circle cx="56" cy="56" r="48" fill="none"
                                            stroke="rgba(255,255,255,0.05)" stroke-width="7" />
                                        <circle cx="56" cy="56" r="48" fill="none" stroke="#f59e0b"
                                            stroke-width="7" stroke-linecap="round" stroke-dasharray="301.59"
                                            stroke-dashoffset="{{ ($grades['college'] ?? 0) > 0 ? 301.59 - (301.59 * (float) ($grades['college'] ?? 0)) / 100 : 301.59 }}" />
                                    </svg>
                                    <div
                                        style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;">
                                        <span class="font-extrabold"
                                            style="font-size:16pt;color:#f59e0b;">{{ $grades['college'] ?? 'N/A' }}</span>
                                    </div>
                                </div>
                                <div
                                    style="font-size:10pt;text-transform:uppercase;letter-spacing:0.2em;color:rgba(255,255,255,0.4);">
                                    College</div>
                                <div style="font-size:10pt;color:rgba(255,255,255,0.2);">Tertiary Education</div>
                            </div>
                        </div>
                    </div>
                @endif
                @if (!empty($narratives['academic_performance']))
                    <h2 class="uppercase text-lg pt-5 text-white mb-2">Analysis</h2>
                    <hr class="w-50 h-0.5 bg-accent1/50 mb-4 border-0 rounded">
                    <div class="text-white/70 leading-relaxed text-sm pb-5 text-justify">
                        {{ $narratives['academic_performance'] }}
                    </div>
                    {{-- Education Program Part 2 --}}
                    <div class="[page-break-before:always]"></div>
                @endif
            </div>

            @php
                $tr = $edu['transitionRatesByAcademicYear'] ?? [];
                if (!empty($tr['datasets'])) {
                    $tr['datasets'] = array_values(
                        array_filter($tr['datasets'], function ($ds) {
                            return !empty(array_filter($ds['data'] ?? [], fn($v) => $v > 0));
                        }),
                    );
                }
            @endphp
            @if (!empty($tr['labels']) && !empty($tr['datasets']))
                <div style="padding-top:1rem;break-inside:avoid;page-break-inside:avoid;">
                    <h3 class="font-bold text-white/90 mb-2.5 text-xl" style="margin-top:18px">Transition Rates</h3>
                    <div
                        class="bg-white/4 border border-white/8 rounded-2xl p-3 text-center my-3 min-h-52.5 flex items-center justify-center">
                        <canvas id="ch-edu-tr"></canvas>
                    </div>
                    @if (!empty($narratives['transition_rates']))
                        <h2 class="uppercase text-lg pt-5 text-white mb-2">Analysis</h2>
                        <hr class="w-50 h-0.5 bg-accent1/50 mb-4 border-0 rounded">
                        <div class="text-white/70 leading-relaxed text-sm pb-5 text-justify">
                            {{ $narratives['transition_rates'] }}
                        </div>
                        {{-- Education Program Part 3 --}}
                        <div class="[page-break-before:always]"></div>
                    @endif
                </div>
            @endif

            @php
                $cr = $edu['completionRatesByAcademicYear'] ?? [];
                if (!empty($cr['datasets'])) {
                    $cr['datasets'] = array_values(
                        array_filter($cr['datasets'], function ($ds) {
                            return !empty(array_filter($ds['data'] ?? [], fn($v) => $v > 0));
                        }),
                    );
                }
            @endphp
            @if (!empty($cr['labels']) && !empty($cr['datasets']))
                <div style="padding-top:1rem;break-inside:avoid;page-break-inside:avoid;">
                    <h3 class="font-bold text-white/90 mb-2.5 text-xl" style="margin-top:18px">Completion Rates
                    </h3>
                    <div
                        class="bg-white/4 border border-white/8 rounded-2xl p-3 text-center my-3 min-h-52.5 flex items-center justify-center">
                        <canvas id="ch-edu-cr"></canvas>
                    </div>
                    @if (!empty($narratives['completion_rates']))
                        <h2 class="uppercase text-lg pt-5 text-white mb-2">Analysis</h2>
                        <hr class="w-50 h-0.5 bg-accent1/50 mb-4 border-0 rounded">
                        <div class="text-white/70 leading-relaxed text-sm pb-5 text-justify">
                            {{ $narratives['completion_rates'] }}
                        </div>
                    @endif
                </div>
            @endif

            @php
                $gs = $edu['graduatesByStageByYear'] ?? [];
                if (!empty($gs['datasets'])) {
                    $gs['datasets'] = array_values(
                        array_filter($gs['datasets'], function ($ds) {
                            return !empty(array_filter($ds['data'] ?? [], fn($v) => $v > 0));
                        }),
                    );
                }
            @endphp
            @if (!empty($gs['datasets']))
                <div style="padding-top:1rem;break-inside:avoid;page-break-inside:avoid;">
                    <h3 class="font-bold text-white/90 mb-2.5 text-xl" style="margin-top:18px">Graduates by
                        Educational Stage</h3>
                    <div
                        class="bg-white/4 border border-white/8 rounded-2xl p-3 text-center my-3 min-h-52.5 flex items-center justify-center">
                        <canvas id="ch-edu-gs"></canvas>
                    </div>
                    @if (!empty($narratives['graduates_by_stage']))
                        <h2 class="uppercase text-lg pt-5 text-white mb-2">Analysis</h2>
                        <hr class="w-50 h-0.5 bg-accent1/50 mb-4 border-0 rounded">
                        <div class="text-white/70 leading-relaxed text-sm pb-5 text-justify">
                            {{ $narratives['graduates_by_stage'] }}
                        </div>
                    @endif
                </div>
            @endif

            @php
                $ss = $edu['scholarsByEducationalStageByYear'] ?? [];
                if (!empty($ss['datasets'])) {
                    $ss['datasets'] = array_values(
                        array_filter($ss['datasets'], function ($ds) {
                            return !empty(array_filter($ds['data'] ?? [], fn($v) => $v > 0));
                        }),
                    );
                }
            @endphp
            @if (!empty($ss['datasets']))
                <div style="padding-top:1rem;break-inside:avoid;page-break-inside:avoid;">
                    <h3 class="font-bold text-white/90 mb-2.5 text-xl" style="margin-top:18px">Scholars by
                        Educational Stage</h3>
                    <div
                        class="bg-white/4 border border-white/8 rounded-2xl p-3 text-center my-3 min-h-52.5 flex items-center justify-center">
                        <canvas id="ch-edu-ss"></canvas>
                    </div>
                    @if (!empty($narratives['scholars_by_stage']))
                        <h2 class="uppercase text-lg pt-5 text-white mb-2">Analysis</h2>
                        <hr class="w-50 h-0.5 bg-accent1/50 mb-4 border-0 rounded">
                        <div class="text-white/70 leading-relaxed text-sm pb-5 text-justify">
                            {{ $narratives['scholars_by_stage'] }}
                        </div>
                    @endif
                    {{-- Education Program Part 4 --}}
                    <div class="[page-break-before:always]"></div>
                </div>
            @endif

            @php $eq = $act['trends']['eqSessionAttendance'] ?? []; @endphp
            @php $tut = $act['trends']['tutorialSessionAttendance'] ?? []; @endphp
            @if (!empty($eq['labels']) || !empty($tut['labels']))
                <div style="padding-top:1rem;break-inside:avoid;page-break-inside:avoid;">
                    <h3 class="font-bold text-white/90 mb-2.5 text-xl" style="margin-top:18px">Program
                        Participation – Attendance Trends</h3>
                    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:0.5rem;margin-bottom:0.875rem;">
                        <div class="rounded-2xl p-3 text-center relative overflow-hidden"
                            style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05);">
                            <div class="absolute top-0 left-0 w-full h-20 opacity-20"
                                style="background: linear-gradient(180deg, #ffcc33 0%, transparent 100%);"></div>
                            <div class="relative z-10">
                                <div style="width:112px;height:112px;margin:0 auto 0.5rem;position:relative;">
                                    <svg viewBox="0 0 112 112"
                                        style="width:100%;height:100%;transform:rotate(-90deg);">
                                        <circle cx="56" cy="56" r="48" fill="none"
                                            stroke="rgba(255,255,255,0.05)" stroke-width="7" />
                                        <circle cx="56" cy="56" r="48" fill="none" stroke="#ffcc33"
                                            stroke-width="7" stroke-linecap="round" stroke-dasharray="301.59"
                                            stroke-dashoffset="{{ ($act['eqSessionAttendance'] ?? 0) > 0 ? 301.59 - (301.59 * (float) ($act['eqSessionAttendance'] ?? 0)) / 100 : 301.59 }}" />
                                    </svg>
                                    <div
                                        style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;">
                                        <span class="font-extrabold"
                                            style="font-size:16pt;color:#ffcc33;">{{ $act['eqSessionAttendance'] ?? 0 }}%</span>
                                    </div>
                                </div>
                                <div
                                    style="font-size:10pt;text-transform:uppercase;letter-spacing:0.2em;color:rgba(255,255,255,0.4);">
                                    EQ Session</div>
                            </div>
                        </div>
                        <div class="rounded-2xl p-3 text-center relative overflow-hidden"
                            style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05);">
                            <div class="absolute top-0 left-0 w-full h-20 opacity-20"
                                style="background: linear-gradient(180deg, #3b82f6 0%, transparent 100%);"></div>
                            <div class="relative z-10">
                                <div style="width:112px;height:112px;margin:0 auto 0.5rem;position:relative;">
                                    <svg viewBox="0 0 112 112"
                                        style="width:100%;height:100%;transform:rotate(-90deg);">
                                        <circle cx="56" cy="56" r="48" fill="none"
                                            stroke="rgba(255,255,255,0.05)" stroke-width="7" />
                                        <circle cx="56" cy="56" r="48" fill="none" stroke="#3b82f6"
                                            stroke-width="7" stroke-linecap="round" stroke-dasharray="301.59"
                                            stroke-dashoffset="{{ ($act['tutorialSessionAttendance'] ?? 0) > 0 ? 301.59 - (301.59 * (float) ($act['tutorialSessionAttendance'] ?? 0)) / 100 : 301.59 }}" />
                                    </svg>
                                    <div
                                        style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;">
                                        <span class="font-extrabold"
                                            style="font-size:16pt;color:#3b82f6;">{{ $act['tutorialSessionAttendance'] ?? 0 }}%</span>
                                    </div>
                                </div>
                                <div
                                    style="font-size:10pt;text-transform:uppercase;letter-spacing:0.2em;color:rgba(255,255,255,0.4);">
                                    Tutorial Session</div>
                            </div>
                        </div>
                    </div>
                    @if (!empty($eq['labels']) || !empty($tut['labels']))
                        <div
                            class="bg-white/4 border border-white/8 rounded-2xl p-3 text-center my-3 min-h-52.5 flex items-center justify-center">
                            <canvas id="ch-edu-attendance"></canvas>
                        </div>
                    @endif
                    @if (!empty($narratives['education_participation']))
                        <h2 class="uppercase text-lg pt-5 text-white mb-2">Analysis</h2>
                        <hr class="w-50 h-0.5 bg-accent1/50 mb-4 border-0 rounded">
                        <div class="text-white/70 leading-relaxed text-sm pb-5 text-justify">
                            {{ $narratives['education_participation'] }}</div>
                    @endif
                </div>
            @endif
        </div>
    @endif

    @if ($hasSpt)
        <div class="[page-break-before:always] pt-8">
            <h2 id="sports-program" class="font-bold text-white mt-6 mb-3 pb-1.5 border-b border-white/10 text-2xl">
                {{ $hasEdu ? '3' : '2' }}. Sports Program</h2>
            <div style="padding-top:1rem;break-inside:avoid;page-break-inside:avoid;">
                <h3 class="font-bold text-white/90 mb-2.5 text-xl" style="margin-top:18px">Beneficiaries Overview
                </h3>
                <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:0.5rem;margin-bottom:0.875rem;">
                    <div class="rounded-2xl p-3 relative overflow-hidden"
                        style="background: linear-gradient(135deg, rgba(16,185,129,0.12) 0%, rgba(16,185,129,0.03) 100%); border: 1px solid rgba(16,185,129,0.15);">
                        <div class="absolute top-0 right-0 w-16 h-16 opacity-10 rounded-full -translate-y-1/2 translate-x-1/2"
                            style="background: #10b981;"></div>
                        <div
                            style="font-size:10pt;text-transform:uppercase;letter-spacing:0.2em;color:rgba(255,255,255,0.5);">
                            Registered Players</div>
                        <div class="font-extrabold text-white mt-2 text-xl">{{ $spt['registeredPlayers'] ?? 0 }}</div>
                    </div>
                    <div class="rounded-2xl p-3 relative overflow-hidden"
                        style="background: linear-gradient(135deg, rgba(168,85,247,0.12) 0%, rgba(168,85,247,0.03) 100%); border: 1px solid rgba(168,85,247,0.15);">
                        <div class="absolute top-0 right-0 w-16 h-16 opacity-10 rounded-full -translate-y-1/2 translate-x-1/2"
                            style="background: #a855f7;"></div>
                        <div
                            style="font-size:10pt;text-transform:uppercase;letter-spacing:0.2em;color:rgba(255,255,255,0.5);">
                            Competitions</div>
                        <div class="font-extrabold text-white mt-2 text-xl">
                            {{ $spt['competitionParticipation'] ?? 0 }}</div>
                    </div>
                </div>
                <p class="text-white/50 mt-4 mb-4 italic">See appendices for the <a href="#app-registered-players"
                        class="text-accent1 underline">list of registered players</a> and <a href="#app-competitions"
                        class="text-accent1 underline">competitions</a>.</p>
                @if (!empty($narratives['sports']))
                    <h2 class="uppercase text-lg pt-5 text-white mb-2">Analysis</h2>
                    <hr class="w-50 h-0.5 bg-accent1/50 mb-4 border-0 rounded">
                    <div class="text-white/70 leading-relaxed text-sm pb-5 text-justify">{{ $narratives['sports'] }}
                    </div>
                @endif

            </div>
            @php
                $np = $spt['newPlayersBySportsTypeByYear'] ?? [];
                if (!empty($np['datasets'])) {
                    $np['datasets'] = array_values(
                        array_filter(
                            $np['datasets'],
                            fn($ds) => !empty(array_filter($ds['data'] ?? [], fn($v) => $v > 0)),
                        ),
                    );
                }
            @endphp
            @if (!empty($np['datasets']))
                <div style="padding-top:1rem;break-inside:avoid;page-break-inside:avoid;">
                    <h3 class="font-bold text-white/90 mb-2.5 text-xl" style="margin-top:18px">New Players by
                        Sports Type</h3>
                    <div
                        class="bg-white/4 border border-white/8 rounded-2xl p-3 text-center my-3 min-h-52.5 flex items-center justify-center">
                        <canvas id="ch-spt-np"></canvas>
                    </div>
                    @if (!empty($narratives['new_players']))
                        <h2 class="uppercase text-lg pt-5 text-white mb-2">Analysis</h2>
                        <hr class="w-50 h-0.5 bg-accent1/50 mb-4 border-0 rounded">
                        <div class="text-white/70 leading-relaxed text-sm pb-5 text-justify">
                            {{ $narratives['new_players'] }}</div>
                    @endif
                </div>
            @endif

            @php
                $ap = $spt['activePlayersBySportsTypeByYear'] ?? [];
                if (!empty($ap['datasets'])) {
                    $ap['datasets'] = array_values(
                        array_filter(
                            $ap['datasets'],
                            fn($ds) => !empty(array_filter($ds['data'] ?? [], fn($v) => $v > 0)),
                        ),
                    );
                }
            @endphp
            @if (!empty($ap['datasets']))
                <div style="padding-top:1rem;break-inside:avoid;page-break-inside:avoid;">
                    <h3 class="font-bold text-white/90 mb-2.5 text-xl" style="margin-top:18px">Currently Active
                        Players by Sports Type</h3>
                    <div
                        class="bg-white/4 border border-white/8 rounded-2xl p-3 text-center my-3 min-h-52.5 flex items-center justify-center">
                        <canvas id="ch-spt-ap"></canvas>
                    </div>
                    @if (!empty($narratives['active_players']))
                        <h2 class="uppercase text-lg pt-5 text-white mb-2">Analysis</h2>
                        <hr class="w-50 h-0.5 bg-accent1/50 mb-4 border-0 rounded">
                        <div class="text-white/70 leading-relaxed text-sm pb-5 text-justify">
                            {{ $narratives['active_players'] }}
                        </div>
                    @endif
                    {{-- Sports Program Part 2 --}}
                    <div class="[page-break-before:always]"></div>
                </div>
            @endif

            @php $sta = $sptAct['trends']['trainingSessionAttendance'] ?? []; @endphp
            @php $uv = $sptAct['trends']['uniqueVisits'] ?? []; @endphp
            @if (!empty($sta['labels']) || !empty($uv['labels']))
                <div style="padding-top:1rem;break-inside:avoid;page-break-inside:avoid;">
                    <h3 class="font-bold text-white/90 mb-2.5 text-xl" style="margin-top:18px">Program
                        Participation – Training Attendance</h3>
                    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:0.5rem;margin-bottom:0.875rem;">
                        <div class="rounded-2xl p-3 relative overflow-hidden"
                            style="background: linear-gradient(135deg, rgba(59,130,246,0.12) 0%, rgba(59,130,246,0.03) 100%); border: 1px solid rgba(59,130,246,0.15);">
                            <div class="absolute top-0 right-0 w-16 h-16 opacity-10 rounded-full -translate-y-1/2 translate-x-1/2"
                                style="background: #3b82f6;"></div>
                            <div
                                style="font-size:10pt;text-transform:uppercase;letter-spacing:0.2em;color:rgba(255,255,255,0.5);">
                                Training Session</div>
                            <div class="font-extrabold text-white mt-2 text-xl">
                                {{ $sptAct['trainingSessionAttendance'] ?? 0 }}%</div>
                        </div>
                        <div class="rounded-2xl p-3 relative overflow-hidden"
                            style="background: linear-gradient(135deg, rgba(168,85,247,0.12) 0%, rgba(168,85,247,0.03) 100%); border: 1px solid rgba(168,85,247,0.15);">
                            <div class="absolute top-0 right-0 w-16 h-16 opacity-10 rounded-full -translate-y-1/2 translate-x-1/2"
                                style="background: #a855f7;"></div>
                            <div
                                style="font-size:10pt;text-transform:uppercase;letter-spacing:0.2em;color:rgba(255,255,255,0.5);">
                                Unique Visits</div>
                            <div class="font-extrabold text-white mt-2 text-xl">{{ $sptAct['uniqueVisits'] ?? 0 }}
                            </div>
                        </div>
                    </div>
                    @if (!empty($narratives['sports_participation_cards']))
                        <h2 class="uppercase text-lg pt-5 text-white mb-2">Analysis</h2>
                        <hr class="w-50 h-0.5 bg-accent1/50 mb-4 border-0 rounded">
                        <div class="text-white/70 leading-relaxed text-sm pb-5 text-justify">
                            {{ $narratives['sports_participation_cards'] }}
                        </div>
                    @endif
                    @if (!empty($sta['labels']))
                        <div style="padding-top:1rem;break-inside:avoid;page-break-inside:avoid;">
                            <h3 class="font-bold text-white/90 mb-2.5 text-xl" style="margin-top:18px">Training
                                Session
                                Attendance Trend</h3>
                            <div
                                class="bg-white/4 border border-white/8 rounded-2xl p-3 text-center my-3 min-h-52.5 flex items-center justify-center">
                                <canvas id="ch-spt-sta"></canvas>
                            </div>
                        </div>
                    @endif
                    @if (!empty($uv['labels']))
                        <div style="padding-top:1rem;break-inside:avoid;page-break-inside:avoid;">
                            <h3 class="font-bold text-white/90 mb-2.5 text-xl" style="margin-top:18px">Number of
                                Unique Visits
                            </h3>
                            <div
                                class="bg-white/4 border border-white/8 rounded-2xl p-3 text-center my-3 min-h-52.5 flex items-center justify-center">
                                <canvas id="ch-spt-uv"></canvas>
                            </div>
                        </div>
                        <div style="padding-top:1rem;break-inside:avoid;page-break-inside:avoid;">
                            @if (!empty($narratives['sports_participation_trends']))
                                <h2 class="uppercase text-lg pt-5 text-white mb-2">Analysis</h2>
                                <hr class="w-50 h-0.5 bg-accent1/50 mb-4 border-0 rounded">
                                <div class="text-white/70 leading-relaxed text-sm pb-5 text-justify">
                                    {{ $narratives['sports_participation_trends'] }}
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            @endif
        </div>
    @endif

    @if ($hasFnd)
        <div class="[page-break-before:always] pt-8">
            <h2 id="donors-grants" class="font-bold text-white mt-6 mb-3 pb-1.5 border-b border-white/10 text-2xl">
                {{ ($hasEdu ? 1 : 0) + ($hasSpt ? 1 : 0) + 2 }}. Donors &amp; Grants</h2>

            <div style="padding-top:1rem;break-inside:avoid;page-break-inside:avoid;">
                <h3 class="font-bold text-white/90 mb-2.5 text-xl" style="margin-top:18px">Overview</h3>
                <div
                    style="display:grid;grid-template-columns:repeat(2,1fr);gap:0.5rem;margin-bottom:0.5rem;break-inside:avoid;page-break-inside:avoid;">
                    <div class="rounded-2xl p-3 relative overflow-hidden"
                        style="background: linear-gradient(135deg, rgba(16,185,129,0.12) 0%, rgba(16,185,129,0.03) 100%); border: 1px solid rgba(16,185,129,0.15);">
                        <div class="absolute top-0 right-0 w-16 h-16 opacity-10 rounded-full -translate-y-1/2 translate-x-1/2"
                            style="background: #10b981;"></div>
                        <div
                            style="font-size:10pt;text-transform:uppercase;letter-spacing:0.2em;color:rgba(255,255,255,0.5);">
                            New Donors</div>
                        <div class="font-extrabold text-white mt-2 text-xl">{{ $fnd['newDonors'] ?? 0 }}</div>
                    </div>
                    <div class="rounded-2xl p-3 relative overflow-hidden"
                        style="background: linear-gradient(135deg, rgba(99,102,241,0.12) 0%, rgba(99,102,241,0.03) 100%); border: 1px solid rgba(99,102,241,0.15);">
                        <div class="absolute top-0 right-0 w-16 h-16 opacity-10 rounded-full -translate-y-1/2 translate-x-1/2"
                            style="background: #6366f1;"></div>
                        <div
                            style="font-size:10pt;text-transform:uppercase;letter-spacing:0.2em;color:rgba(255,255,255,0.5);">
                            Grants Received</div>
                        <div class="font-extrabold text-white mt-2 text-xl">{{ $fnd['grantsReceived'] ?? 0 }}</div>
                    </div>
                </div>
                <p class="text-white/50 mt-4 mb-4 italic">See appendices for the <a href="#app-new-donors"
                        class="text-accent1 underline">list of new donors</a> and <a href="#app-grants-received"
                        class="text-accent1 underline">grants received</a>.</p>
                <div
                    style="display:grid;grid-template-columns:repeat(3,1fr);gap:0.5rem;margin-bottom:0.875rem;break-inside:avoid;page-break-inside:avoid;">
                    <div class="rounded-2xl p-3 relative overflow-hidden"
                        style="background: linear-gradient(135deg, rgba(59,130,246,0.12) 0%, rgba(59,130,246,0.03) 100%); border: 1px solid rgba(59,130,246,0.15);">
                        <div class="absolute top-0 right-0 w-16 h-16 opacity-10 rounded-full -translate-y-1/2 translate-x-1/2"
                            style="background: #3b82f6;"></div>
                        <div
                            style="font-size:10pt;text-transform:uppercase;letter-spacing:0.2em;color:rgba(255,255,255,0.5);">
                            Funds Received</div>
                        <div class="font-extrabold text-white mt-2 text-xl">
                            ₱{{ number_format((float) ($fnd['fundsReceived'] ?? 0), 2) }}</div>
                    </div>
                    <div class="rounded-2xl p-3 relative overflow-hidden"
                        style="background: linear-gradient(135deg, rgba(245,158,11,0.12) 0%, rgba(245,158,11,0.03) 100%); border: 1px solid rgba(245,158,11,0.15);">
                        <div class="absolute top-0 right-0 w-16 h-16 opacity-10 rounded-full -translate-y-1/2 translate-x-1/2"
                            style="background: #f59e0b;"></div>
                        <div
                            style="font-size:10pt;text-transform:uppercase;letter-spacing:0.2em;color:rgba(255,255,255,0.5);">
                            Funds Allocated</div>
                        <div class="font-extrabold text-white mt-2 text-xl">
                            ₱{{ number_format((float) ($fnd['fundsAllocated'] ?? 0), 2) }}</div>
                    </div>
                    <div class="rounded-2xl p-3 relative overflow-hidden"
                        style="background: linear-gradient(135deg, rgba(168,85,247,0.12) 0%, rgba(168,85,247,0.03) 100%); border: 1px solid rgba(168,85,247,0.15);">
                        <div class="absolute top-0 right-0 w-16 h-16 opacity-10 rounded-full -translate-y-1/2 translate-x-1/2"
                            style="background: #a855f7;"></div>
                        <div
                            style="font-size:10pt;text-transform:uppercase;letter-spacing:0.2em;color:rgba(255,255,255,0.5);">
                            Target</div>
                        <div class="font-extrabold text-white mt-2 text-xl">
                            ₱{{ number_format((float) ($fnd['fundingTarget'] ?? 0), 2) }}</div>
                    </div>
                </div>
                @if (!empty($narratives['funding']))
                    <h2 class="uppercase text-lg pt-5 text-white mb-2">Analysis</h2>
                    <hr class="w-50 h-0.5 bg-accent1/50 mb-4 border-0 rounded">
                    <div class="text-white/70 leading-relaxed text-sm pb-5 text-justify">{{ $narratives['funding'] }}
                    </div>

                    {{-- Donor & Grants Part 2 --}}
                    <div class="[page-break-before:always]"></div>
                @endif
            </div>

            <div style="padding-top:1rem;break-inside:avoid;page-break-inside:avoid;">
                <h3 class="font-bold text-white/90 mb-2.5 text-xl" style="margin-top:18px">Funds Received vs Target
                </h3>
                <div class="rounded-2xl p-5 mb-3"
                    style="background-color: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08);">
                    <div
                        style="display:flex;align-items:center;justify-content:space-between;gap:1rem;margin-bottom:1rem;">
                        <div>
                            <div class="font-bold text-white text-xl" style="font-family: var(--font-header1);">Funds
                                Received vs Target</div>
                            <div style="color:rgba(255,255,255,0.6);font-size:10pt;">Progress toward this year's
                                fundraising target</div>
                        </div>
                        <div style="text-align:right;">
                            <div
                                style="font-size:10pt;color:rgba(255,255,255,0.5);text-transform:uppercase;letter-spacing:0.2em;">
                                Completion</div>
                            <div class="font-bold text-white text-2xl">{{ $fnd['receivedProgressPercent'] ?? 0 }}%
                            </div>
                        </div>
                    </div>
                    <div class="progress-bar" style="height:8px;background-color:rgba(255,255,255,0.08);">
                        <div class="fill"
                            style="width:{{ min((float) ($fnd['receivedProgressPercent'] ?? 0), 100) }}%;background:#ffcc33;height:100%;">
                        </div>
                    </div>
                    <div style="margin-top:1rem;font-size:10pt;color:rgba(255,255,255,0.7);">
                        {{ $fnd['receivedProgressPercent'] ?? 0 }}% of the target has been received.
                    </div>
                </div>
                <div class="rounded-2xl p-5 mb-2.5"
                    style="background-color: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08);">
                    <div
                        style="display:flex;align-items:center;justify-content:space-between;gap:1rem;margin-bottom:1rem;">
                        <div>
                            <div class="font-bold text-white text-xl" style="font-family: var(--font-header1);">
                                Allocated vs Received</div>
                            <div style="color:rgba(255,255,255,0.6);font-size:10pt;">How much of received funds have
                                been assigned</div>
                        </div>
                        <div style="text-align:right;">
                            <div
                                style="font-size:10pt;color:rgba(255,255,255,0.5);text-transform:uppercase;letter-spacing:0.2em;">
                                Allocation Rate</div>
                            <div class="font-bold text-white text-2xl">{{ $fnd['allocatedVsReceivedPercent'] ?? 0 }}%
                            </div>
                        </div>
                    </div>
                    <div class="progress-bar" style="height:8px;background-color:rgba(255,255,255,0.08);">
                        <div class="fill"
                            style="width:{{ min((float) ($fnd['allocatedVsReceivedPercent'] ?? 0), 100) }}%;background:#10b981;height:100%;">
                        </div>
                    </div>
                    <div style="margin-top:1rem;font-size:10pt;color:rgba(255,255,255,0.7);">
                        {{ $fnd['allocatedVsReceivedPercent'] ?? 0 }}% of funds received this year have been allocated.
                    </div>
                </div>

                @if (!empty($narratives['funding_target_allocation_analysis']))
                    <div style="padding-top:1rem;">
                        <h2 class="uppercase text-lg pt-2 text-white mb-2">Analysis</h2>
                        <hr class="w-50 h-0.5 bg-accent1/50 mb-4 border-0 rounded">
                        <div class="text-white/70 leading-relaxed text-sm text-justify">
                            {{ $narratives['funding_target_allocation_analysis'] }}</div>
                    </div>
                @endif
            </div>

            <div style="padding-top:1.5rem;break-inside:avoid;page-break-inside:avoid;">
                <div class="flex items-center justify-between gap-4 mb-3">
                    <div>
                        <div class="font-bold text-white text-xl" style="font-family: var(--font-header1);">Funds
                            Received vs Allocated Comparison by {{ $fndTrendPeriodLabel }}</div>
                        <div style="color:rgba(255,255,255,0.6);font-size:10pt;">Received vs allocated funds by
                            {{ strtolower($fndTrendPeriodLabel) }}</div>
                    </div>
                </div>
                <div
                    class="bg-white/4 border border-white/8 rounded-2xl p-3 text-center my-3 min-h-52.5 flex items-center justify-center">
                    <canvas id="ch-fnd-monthly"></canvas>
                </div>

                @if (!empty($narratives['funding_trend_analysis']))
                    <div style="padding-top:0.5rem;">
                        <h2 class="uppercase text-lg pt-2 text-white mb-2">Analysis</h2>
                        <hr class="w-50 h-0.5 bg-accent1/50 mb-4 border-0 rounded">
                        <div class="text-white/70 leading-relaxed text-sm text-justify">
                            {{ $narratives['funding_trend_analysis'] }}</div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- Appendices section --}}
    @php
        $hasAppTables =
            ($hasEdu &&
                (!empty($edu['activeScholarsList']) ||
                    !empty($edu['graduatesList']) ||
                    !empty($edu['exitedScholarsList']))) ||
            ($hasSpt && (!empty($spt['registeredPlayersList']) || !empty($spt['competitionsList']))) ||
            ($hasFnd && (!empty($fnd['newDonorsList']) || !empty($fnd['grantsReceivedList'])));
    @endphp
    @if ($hasAppTables)
        <div class="[page-break-before:always]" style="page:table-page;">
            <h2 id="appendices" class="font-bold text-white mt-6 mb-3 pb-1.5 border-b border-white/10 text-2xl">
                {{ ($hasEdu ? 1 : 0) + ($hasSpt ? 1 : 0) + ($hasFnd ? 1 : 0) + 2 }}. Appendices</h2>
            @php $appIsFirst = true; @endphp

            @if (!empty($edu['activeScholarsList']))
                <div id="app-active-scholars" @if (!$appIsFirst) class="[page-break-before:always]" @endif style="padding-top:1rem;">
                    @php $appIsFirst = false; @endphp
                    <h3 class="font-bold text-white/90 mb-2.5 text-xl" style="margin-top:18px">Active Scholars
                    </h3>
                    <p class="text-white/70 leading-relaxed text-sm pb-2 text-justify">
                        This table lists the currently active scholars within the Education program for the
                        reporting period. It provides a quick reference of each scholar's current grade level.
                    </p>
                    <table style="width:100%;border-collapse:collapse;margin-bottom:2rem;">
                        <thead>
                            <tr style="background:rgba(255,255,255,0.08);padding-top:2rem;">
                                <th
                                    style="padding:0.45rem 0.6rem;text-align:left;color:rgba(255,255,255,0.85);font-size:10pt;border-bottom:1px solid rgba(255,255,255,0.1);font-weight:600;">
                                    Name</th>
                                <th
                                    style="padding:0.45rem 0.6rem;text-align:left;color:rgba(255,255,255,0.85);font-size:10pt;border-bottom:1px solid rgba(255,255,255,0.1);font-weight:600;">
                                    Grade Level</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($edu['activeScholarsList'] as $entry)
                                <tr>
                                    <td
                                        style="padding:0.35rem 0.6rem;color:rgba(255,255,255,0.7);font-size:9pt;border-bottom:1px solid rgba(255,255,255,0.05);">
                                        {{ $entry['name'] }}</td>
                                    <td
                                        style="padding:0.35rem 0.6rem;color:rgba(255,255,255,0.7);font-size:9pt;border-bottom:1px solid rgba(255,255,255,0.05);">
                                        {{ $entry['grade_level'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            @if (!empty($edu['graduatesList']))
                <div id="app-graduates" @if (!$appIsFirst) class="[page-break-before:always]" @endif style="padding-top:1rem;">
                    @php $appIsFirst = false; @endphp
                    <h3 class="font-bold text-white/90 mb-2.5 text-xl" style="margin-top:18px">Graduates</h3>
                    <p class="text-white/70 leading-relaxed text-sm pb-2 text-justify">
                        This table lists the beneficiaries who completed a terminal grade level or graduated from
                        college within the reporting period. It includes their grade level at graduation.
                    </p>
                    <table style="width:100%;border-collapse:collapse;margin-bottom:2rem;">
                        <thead>
                            <tr style="background:rgba(255,255,255,0.08);padding-top:2rem;">
                                <th
                                    style="padding:0.45rem 0.6rem;text-align:left;color:rgba(255,255,255,0.85);font-size:10pt;border-bottom:1px solid rgba(255,255,255,0.1);font-weight:600;">
                                    Name</th>
                                <th
                                    style="padding:0.45rem 0.6rem;text-align:left;color:rgba(255,255,255,0.85);font-size:10pt;border-bottom:1px solid rgba(255,255,255,0.1);font-weight:600;">
                                    Grade Level</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($edu['graduatesList'] as $entry)
                                <tr>
                                    <td
                                        style="padding:0.35rem 0.6rem;color:rgba(255,255,255,0.7);font-size:9pt;border-bottom:1px solid rgba(255,255,255,0.05);">
                                        {{ $entry['name'] }}</td>
                                    <td
                                        style="padding:0.35rem 0.6rem;color:rgba(255,255,255,0.7);font-size:9pt;border-bottom:1px solid rgba(255,255,255,0.05);">
                                        {{ $entry['grade_level'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            @if (!empty($edu['exitedScholarsList']))
                <div id="app-exited-scholars" @if (!$appIsFirst) class="[page-break-before:always]" @endif style="padding-top:1rem;">
                    @php $appIsFirst = false; @endphp
                    <h3 class="font-bold text-white/90 mb-2.5 text-xl" style="margin-top:18px">Exited Scholars</h3>
                    <p class="text-white/70 leading-relaxed text-sm pb-2 text-justify">
                        This table lists the scholars whose scholarship ended during the reporting period. It
                        provides visibility into attrition and their last known grade level.
                    </p>
                    <table style="width:100%;border-collapse:collapse;margin-bottom:2rem;">
                        <thead>
                            <tr style="background:rgba(255,255,255,0.08);padding-top:2rem;">
                                <th
                                    style="padding:0.45rem 0.6rem;text-align:left;color:rgba(255,255,255,0.85);font-size:10pt;border-bottom:1px solid rgba(255,255,255,0.1);font-weight:600;">
                                    Name</th>
                                <th
                                    style="padding:0.45rem 0.6rem;text-align:left;color:rgba(255,255,255,0.85);font-size:10pt;border-bottom:1px solid rgba(255,255,255,0.1);font-weight:600;">
                                    Grade Level</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($edu['exitedScholarsList'] as $entry)
                                <tr>
                                    <td
                                        style="padding:0.35rem 0.6rem;color:rgba(255,255,255,0.7);font-size:9pt;border-bottom:1px solid rgba(255,255,255,0.05);">
                                        {{ $entry['name'] }}</td>
                                    <td
                                        style="padding:0.35rem 0.6rem;color:rgba(255,255,255,0.7);font-size:9pt;border-bottom:1px solid rgba(255,255,255,0.05);">
                                        {{ $entry['grade_level'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            @if (!empty($spt['registeredPlayersList']))
                <div id="app-registered-players" @if (!$appIsFirst) class="[page-break-before:always]" @endif style="padding-top:1rem;">
                    @php $appIsFirst = false; @endphp
                    <h3 class="font-bold text-white/90 mb-2.5 text-xl" style="margin-top:18px">Registered Players
                    </h3>
                    <p class="text-white/70 leading-relaxed text-sm pb-2 text-justify">
                        This table lists the beneficiaries who are currently registered as players within the
                        Sports program for the reporting period. It provides a quick reference of each player's
                        name and age.
                    </p>
                    <table style="width:100%;border-collapse:collapse;margin-bottom:2rem;">
                        <thead>
                            <tr style="background:rgba(255,255,255,0.08);padding-top:2rem;">
                                <th
                                    style="padding:0.45rem 0.6rem;text-align:left;color:rgba(255,255,255,0.85);font-size:10pt;border-bottom:1px solid rgba(255,255,255,0.1);font-weight:600;">
                                    Name</th>
                                <th
                                    style="padding:0.45rem 0.6rem;text-align:left;color:rgba(255,255,255,0.85);font-size:10pt;border-bottom:1px solid rgba(255,255,255,0.1);font-weight:600;">
                                    Age</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($spt['registeredPlayersList'] as $entry)
                                <tr>
                                    <td
                                        style="padding:0.35rem 0.6rem;color:rgba(255,255,255,0.7);font-size:9pt;border-bottom:1px solid rgba(255,255,255,0.05);">
                                        {{ $entry['name'] }}</td>
                                    <td
                                        style="padding:0.35rem 0.6rem;color:rgba(255,255,255,0.7);font-size:9pt;border-bottom:1px solid rgba(255,255,255,0.05);">
                                        {{ $entry['age'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            @if (!empty($spt['competitionsList']))
                <div id="app-competitions" @if (!$appIsFirst) class="[page-break-before:always]" @endif style="padding-top:1rem;">
                    @php $appIsFirst = false; @endphp
                    <h3 class="font-bold text-white/90 mb-2.5 text-xl" style="margin-top:18px">Competitions</h3>
                    <p class="text-white/70 leading-relaxed text-sm pb-2 text-justify">
                        This section lists the competitions held during the reporting period along with their
                        results. It provides a quick reference of each competition's date and the performance
                        of participating beneficiaries.
                    </p>
                    <div style="display:grid;grid-template-columns:repeat(1,1fr);gap:0.5rem;margin-bottom:0.875rem;">
                        @foreach ($spt['competitionsList'] as $comp)
                            <div class="rounded-2xl p-3 relative overflow-hidden"
                                style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05);break-inside:avoid;page-break-inside:avoid;">
                                <div style="display:flex;justify-content:space-between;margin-bottom:0.5rem;">
                                    <div>
                                        <div
                                            style="font-size:8pt;text-transform:uppercase;letter-spacing:0.2em;color:rgba(255,255,255,0.4);">
                                            Name</div>
                                        <div style="font-weight:600;color:#fff;font-size:10pt;">{{ $comp['name'] }}
                                        </div>
                                    </div>
                                    <div style="text-align:right;">
                                        <div
                                            style="font-size:8pt;text-transform:uppercase;letter-spacing:0.2em;color:rgba(255,255,255,0.4);">
                                            Date</div>
                                        <div style="font-weight:600;color:#fff;font-size:10pt;">{{ $comp['date'] }}
                                        </div>
                                    </div>
                                </div>
                                <div
                                    style="margin-top:0.75rem;padding-top:0.5rem;border-top:1px solid rgba(255,255,255,0.05);">
                                    <div
                                        style="font-size:8pt;text-transform:uppercase;letter-spacing:0.2em;color:rgba(255,255,255,0.4);">
                                        Results</div>
                                    @foreach ($comp['results'] as $res)
                                        <div style="color:rgba(255,255,255,0.7);font-size:9pt;padding:0.15rem 0;">
                                            {{ $res['name'] }} — {{ $res['result'] }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if (!empty($fnd['newDonorsList']))
                <div id="app-new-donors" @if (!$appIsFirst) class="[page-break-before:always]" @endif style="padding-top:1rem;">
                    @php $appIsFirst = false; @endphp
                    <h3 class="font-bold text-white/90 mb-2.5 text-xl" style="margin-top:18px">New Donors</h3>
                    <p class="text-white/70 leading-relaxed text-sm pb-2 text-justify">
                        This table lists the new donors who have registered during the reporting period. It
                        provides a quick reference of each donor's name and type.
                    </p>
                    <table style="width:100%;border-collapse:collapse;margin-bottom:2rem;">
                        <thead>
                            <tr style="background:rgba(255,255,255,0.08);padding-top:2rem;">
                                <th
                                    style="padding:0.45rem 0.6rem;text-align:left;color:rgba(255,255,255,0.85);font-size:10pt;border-bottom:1px solid rgba(255,255,255,0.1);font-weight:600;">
                                    Name</th>
                                <th
                                    style="padding:0.45rem 0.6rem;text-align:left;color:rgba(255,255,255,0.85);font-size:10pt;border-bottom:1px solid rgba(255,255,255,0.1);font-weight:600;">
                                    Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($fnd['newDonorsList'] as $entry)
                                <tr>
                                    <td
                                        style="padding:0.35rem 0.6rem;color:rgba(255,255,255,0.7);font-size:9pt;border-bottom:1px solid rgba(255,255,255,0.05);">
                                        {{ $entry['name'] }}</td>
                                    <td
                                        style="padding:0.35rem 0.6rem;color:rgba(255,255,255,0.7);font-size:9pt;border-bottom:1px solid rgba(255,255,255,0.05);">
                                        {{ $entry['type'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            @if (!empty($fnd['grantsReceivedList']))
                <div id="app-grants-received" @if (!$appIsFirst) class="[page-break-before:always]" @endif style="padding-top:1rem;">
                    @php $appIsFirst = false; @endphp
                    <h3 class="font-bold text-white/90 mb-2.5 text-xl" style="margin-top:18px">Grants Received</h3>
                    <p class="text-white/70 leading-relaxed text-sm pb-2 text-justify">
                        This table lists the grants whose start date falls within the reporting period. It
                        provides a quick reference of the granting organization, grant name, amount, and associated
                        program(s).
                    </p>
                    <table style="width:100%;border-collapse:collapse;margin-bottom:2rem;">
                        <thead>
                            <tr style="background:rgba(255,255,255,0.08);padding-top:2rem;">
                                <th
                                    style="padding:0.45rem 0.6rem;text-align:left;color:rgba(255,255,255,0.85);font-size:10pt;border-bottom:1px solid rgba(255,255,255,0.1);font-weight:600;">
                                    Organization Name</th>
                                <th
                                    style="padding:0.45rem 0.6rem;text-align:left;color:rgba(255,255,255,0.85);font-size:10pt;border-bottom:1px solid rgba(255,255,255,0.1);font-weight:600;">
                                    Grant Name</th>
                                <th
                                    style="padding:0.45rem 0.6rem;text-align:left;color:rgba(255,255,255,0.85);font-size:10pt;border-bottom:1px solid rgba(255,255,255,0.1);font-weight:600;">
                                    Amount</th>
                                <th
                                    style="padding:0.45rem 0.6rem;text-align:left;color:rgba(255,255,255,0.85);font-size:10pt;border-bottom:1px solid rgba(255,255,255,0.1);font-weight:600;">
                                    Associated Program(s)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($fnd['grantsReceivedList'] as $entry)
                                <tr>
                                    <td
                                        style="padding:0.35rem 0.6rem;color:rgba(255,255,255,0.7);font-size:9pt;border-bottom:1px solid rgba(255,255,255,0.05);">
                                        {{ $entry['organization_name'] }}</td>
                                    <td
                                        style="padding:0.35rem 0.6rem;color:rgba(255,255,255,0.7);font-size:9pt;border-bottom:1px solid rgba(255,255,255,0.05);">
                                        {{ $entry['grant_name'] }}</td>
                                    <td
                                        style="padding:0.35rem 0.6rem;color:rgba(255,255,255,0.7);font-size:9pt;border-bottom:1px solid rgba(255,255,255,0.05);">
                                        ₱{{ number_format((float) ($entry['amount'] ?? 0), 2) }}</td>
                                    <td
                                        style="padding:0.35rem 0.6rem;color:rgba(255,255,255,0.7);font-size:9pt;border-bottom:1px solid rgba(255,255,255,0.05);">
                                        {{ $entry['program'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif

    @php
        // Push filtered datasets back into payload so JS renders the same filtered data
        $edu = $payload['education'] ?? [];
        foreach (
            [
                'transitionRatesByAcademicYear',
                'completionRatesByAcademicYear',
                'graduatesByStageByYear',
                'scholarsByEducationalStageByYear',
            ]
            as $chartKey
        ) {
            if (!empty($edu[$chartKey]['datasets'])) {
                $edu[$chartKey]['datasets'] = array_values(
                    array_filter(
                        $edu[$chartKey]['datasets'],
                        fn($ds) => !empty(array_filter($ds['data'] ?? [], fn($v) => $v > 0)),
                    ),
                );
            }
        }
        $payload['education'] = $edu;

        $spt = $payload['sports'] ?? [];
        foreach (['newPlayersBySportsTypeByYear', 'activePlayersBySportsTypeByYear'] as $chartKey) {
            if (!empty($spt[$chartKey]['datasets'])) {
                $spt[$chartKey]['datasets'] = array_values(
                    array_filter(
                        $spt[$chartKey]['datasets'],
                        fn($ds) => !empty(array_filter($ds['data'] ?? [], fn($v) => $v > 0)),
                    ),
                );
            }
        }
        $payload['sports'] = $spt;
    @endphp
    <script>
        (function() {
            var STAGE_RGB = ['255,204,51', '59,130,246', '16,185,129', '234,88,12'];
            var SPORT_RGB = ['244,114,182', '14,165,233', '34,197,94', '245,158,11', '168,85,247', '239,68,68',
                '20,184,166', '99,102,241'
            ];

            function rgbToHsl(r, g, b) {
                r /= 255;
                g /= 255;
                b /= 255;
                var max = Math.max(r, g, b),
                    min = Math.min(r, g, b);
                var h, s, l = (max + min) / 2;
                if (max === min) {
                    h = s = 0;
                } else {
                    var d = max - min;
                    s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
                    switch (max) {
                        case r:
                            h = (g - b) / d + (g < b ? 6 : 0);
                            break;
                        case g:
                            h = (b - r) / d + 2;
                            break;
                        case b:
                            h = (r - g) / d + 4;
                            break;
                    }
                    h /= 6;
                }
                return [h * 360, s, l];
            }

            function hslToRgb(h, s, l) {
                h /= 360;

                function hue2rgb(p, q, t) {
                    if (t < 0) t += 1;
                    if (t > 1) t -= 1;
                    if (t < 1 / 6) return p + (q - p) * 6 * t;
                    if (t < 1 / 2) return q;
                    if (t < 2 / 3) return p + (q - p) * (2 / 3 - t) * 6;
                    return p;
                }
                var r, g, b;
                if (s === 0) {
                    r = g = b = l;
                } else {
                    var q = l < 0.5 ? l * (1 + s) : l + s - l * s;
                    var p = 2 * l - q;
                    r = hue2rgb(p, q, h + 1 / 3);
                    g = hue2rgb(p, q, h);
                    b = hue2rgb(p, q, h - 1 / 3);
                }
                return [Math.round(r * 255), Math.round(g * 255), Math.round(b * 255)];
            }

            function nextWheelColor(rgbStr, degrees) {
                degrees = degrees || 45;
                var p = rgbStr.split(",").map(Number);
                var hsl = rgbToHsl(p[0], p[1], p[2]);
                hsl[0] = (hsl[0] + degrees) % 360;
                return hslToRgb(hsl[0], hsl[1], hsl[2]).join(",");
            }

            function colorFor(id, i) {
                if (id === 'ch-edu-attendance') return i === 0 ? '255,204,51' : '59,130,246';
                if (id === 'ch-spt-sta') return '16,185,129';
                if (id === 'ch-spt-uv') return '244,114,182';
                return (id.indexOf('spt-') !== -1 ? SPORT_RGB : STAGE_RGB)[i];
            }

            function palette(id) {
                if (id === 'ch-edu-attendance' || id === 'ch-spt-sta' || id === 'ch-spt-uv') return null;
                if (id.indexOf('spt-') !== -1) return SPORT_RGB;
                return STAGE_RGB;
            }

            function isPercent(title) {
                return title && (/Rate/i.test(title) || /Attendance/i.test(title) || /Completion/i.test(title));
            }

            function chartType(labels) {
                return labels.length <= 1 ? 'bar' : 'line';
            }

            function makeChart(id, labels, datasets, title) {
                var c = document.getElementById(id);
                if (!c || !labels || !labels.length || !datasets || !datasets.length) return;
                var isBar = chartType(labels) === 'bar';
                var isMultiDataset = datasets.length > 1;
                var cols = palette(id);

                var opts = {
                    type: isBar ? 'bar' : 'line',
                    data: {
                        labels: labels,
                        datasets: datasets.map(function(ds, i) {
                            var co = cols ? cols[i % cols.length] : colorFor(id, i);
                            var cfg = {
                                label: ds.label || '',
                                data: ds.data || [],
                                borderRadius: isBar ? 12 : 0,
                                borderSkipped: isBar ? false : undefined,
                                tension: 0.25,
                            };
                            if (isBar) {
                                cfg.backgroundColor = function(ctx) {
                                    var chart = ctx.chart;
                                    if (!chart.chartArea) return 'rgba(' + co + ',0.88)';
                                    var g = chart.ctx.createLinearGradient(0, chart.chartArea.top, 0,
                                        chart.chartArea.bottom);
                                    g.addColorStop(0, 'rgba(' + co + ',0.88)');
                                    g.addColorStop(1, 'rgba(' + co + ',0.30)');
                                    return g;
                                };
                            } else {
                                // Horizontal gradient on line border (matching dashboard)
                                var nextRgb = nextWheelColor(co, 45);
                                cfg.borderColor = function(ctx) {
                                    var chart = ctx.chart;
                                    if (!chart.chartArea) return 'rgba(' + co + ',1)';
                                    var g = chart.ctx.createLinearGradient(chart.chartArea.left, 0,
                                        chart.chartArea.right, 0);
                                    g.addColorStop(0, 'rgba(' + co + ',1)');
                                    g.addColorStop(.75, 'rgba(' + nextRgb + ',1)');
                                    g.addColorStop(1, 'rgba(' + nextRgb + ',1)');
                                    return g;
                                };
                                cfg.backgroundColor = function(ctx) {
                                    var chart = ctx.chart;
                                    if (!chart.chartArea) return 'rgba(' + co + ',0.12)';
                                    var g = chart.ctx.createLinearGradient(0, chart.chartArea.top, 0,
                                        chart.chartArea.bottom);
                                    g.addColorStop(0, 'rgba(' + co + ',0.25)');
                                    g.addColorStop(1, 'rgba(' + co + ',0.02)');
                                    return g;
                                };
                                if (ds.fill !== undefined) cfg.fill = ds.fill;
                            }
                            return cfg;
                        }),
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        color: 'rgba(255,255,255,0.7)',
                        borderColor: 'rgba(255,255,255,0.1)',
                        plugins: {
                            legend: {
                                display: isMultiDataset,
                                position: 'bottom',
                                labels: {
                                    color: 'rgba(255,255,255,0.72)'
                                }
                            },
                            title: {
                                display: !!title,
                                text: title || '',
                                font: {
                                    size: 10,
                                    weight: 'bold'
                                },
                                color: 'rgba(255,255,255,0.8)'
                            },
                        },
                        scales: {
                            x: {
                                ticks: {
                                    font: {
                                        size: 8
                                    },
                                    color: 'rgba(255,255,255,0.5)'
                                },
                                grid: {
                                    color: 'rgba(255,255,255,0.1)',
                                    display: !isBar
                                },
                            },
                            y: {
                                beginAtZero: true,
                                suggestedMax: isPercent(title) ? 100 : undefined,
                                ticks: {
                                    font: {
                                        size: 8
                                    },
                                    color: 'rgba(255,255,255,0.5)',
                                    precision: isPercent(title) ? 0 : undefined
                                },
                                grid: {
                                    color: 'rgba(255,255,255,0.1)'
                                },
                            },
                        },
                    },
                };
                try {
                    new Chart(c, opts);
                } catch (e) {
                    console.error(e);
                }
            }

            var P = @json($payload);

            var edu = P.education || {};
            if (edu.transitionRatesByAcademicYear) {
                var d = edu.transitionRatesByAcademicYear;
                makeChart('ch-edu-tr', d.labels, d.datasets, 'Transition Rates');
            }
            if (edu.completionRatesByAcademicYear) {
                var d = edu.completionRatesByAcademicYear;
                makeChart('ch-edu-cr', d.labels, d.datasets, 'Completion Rates');
            }
            if (edu.graduatesByStageByYear) {
                var d = edu.graduatesByStageByYear;
                makeChart('ch-edu-gs', d.labels, d.datasets, 'Graduates by Stage');
            }
            if (edu.scholarsByEducationalStageByYear) {
                var d = edu.scholarsByEducationalStageByYear;
                makeChart('ch-edu-ss', d.labels, d.datasets, 'Scholars by Stage');
            }

            var act = P.educationActivity || {};
            var eqD = act.trends && act.trends.eqSessionAttendance;
            var tutD = act.trends && act.trends.tutorialSessionAttendance;
            if (eqD && tutD && eqD.labels && tutD.labels) {
                makeChart('ch-edu-attendance', eqD.labels, [{
                    label: 'EQ Session',
                    data: eqD.data
                }, {
                    label: 'Tutorial',
                    data: tutD.data
                }], 'Attendance Trends');
            } else if (eqD && eqD.labels) {
                makeChart('ch-edu-attendance', eqD.labels, [{
                    label: 'EQ Session',
                    data: eqD.data
                }], 'Attendance Trends');
            } else if (tutD && tutD.labels) {
                makeChart('ch-edu-attendance', tutD.labels, [{
                    label: 'Tutorial',
                    data: tutD.data
                }], 'Attendance Trends');
            }

            var spt = P.sports || {};
            if (spt.newPlayersBySportsTypeByYear) {
                var d = spt.newPlayersBySportsTypeByYear;
                makeChart('ch-spt-np', d.labels, d.datasets, 'New Players by Sport');
            }
            if (spt.activePlayersBySportsTypeByYear) {
                var d = spt.activePlayersBySportsTypeByYear;
                makeChart('ch-spt-ap', d.labels, d.datasets, 'Active Players by Sport');
            }

            var sptA = P.sportsActivity || {};
            if (sptA.trends && sptA.trends.trainingSessionAttendance) {
                var d = sptA.trends.trainingSessionAttendance;
                makeChart('ch-spt-sta', d.labels, [{
                    label: 'Attendance',
                    data: d.data
                }], 'Training Attendance');
            }
            if (sptA.trends && sptA.trends.uniqueVisits) {
                var d = sptA.trends.uniqueVisits;
                makeChart('ch-spt-uv', d.labels, [{
                    label: 'Visits',
                    data: d.data,
                    fill: true
                }], 'Unique Visits');
            }

            var fnd = P.funding || {};
            if (fnd.fundsReceived !== undefined) {
                var ctx = document.getElementById('ch-fnd-monthly');
                if (ctx) {
                    var labels = fnd.trendLabels || ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep',
                        'Oct', 'Nov', 'Dec'
                    ];
                    var recv = fnd.trendReceived || fnd.monthlyReceived || [];
                    var alloc = fnd.trendAllocated || fnd.monthlyAllocated || [];

                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Received',
                                data: recv,
                                backgroundColor: function(ctx) {
                                    var chart = ctx.chart;
                                    if (!chart.chartArea) return 'rgba(255,204,51,0.88)';
                                    var g = chart.ctx.createLinearGradient(0, chart.chartArea.top,
                                        0, chart.chartArea.bottom);
                                    g.addColorStop(0, 'rgba(255,204,51,0.88)');
                                    g.addColorStop(1, 'rgba(255,204,51,0.30)');
                                    return g;
                                },
                                borderColor: 'rgba(255,204,51,1)',
                                borderWidth: 1,
                                borderRadius: 4,
                            }, {
                                label: 'Allocated',
                                data: alloc,
                                backgroundColor: function(ctx) {
                                    var chart = ctx.chart;
                                    if (!chart.chartArea) return 'rgba(16,185,129,0.88)';
                                    var g = chart.ctx.createLinearGradient(0, chart.chartArea.top,
                                        0, chart.chartArea.bottom);
                                    g.addColorStop(0, 'rgba(16,185,129,0.88)');
                                    g.addColorStop(1, 'rgba(16,185,129,0.30)');
                                    return g;
                                },
                                borderColor: 'rgba(16,185,129,1)',
                                borderWidth: 1,
                                borderRadius: 4,
                            }],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        color: 'rgba(255,255,255,0.72)'
                                    },
                                },
                            },
                            scales: {
                                x: {
                                    grid: {
                                        color: 'rgba(255,255,255,0.06)'
                                    },
                                    ticks: {
                                        color: 'rgba(255,255,255,0.5)'
                                    },
                                },
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        color: 'rgba(255,255,255,0.06)'
                                    },
                                    ticks: {
                                        color: 'rgba(255,255,255,0.5)'
                                    },
                                },
                            },
                        },
                    });
                }
            }
        })();

        window.status = 'ready';
    </script>
</body>

</html>
