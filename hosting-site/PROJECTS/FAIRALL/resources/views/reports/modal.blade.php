@php
    use App\Enums\ReportType;
    use App\Http\Middleware\ReportTypeAccess;
    $allowedTypes = array_values(
        array_map(
            fn(string $v) => ['value' => $v, 'label' => ReportType::tryFrom($v)?->label() ?? $v],
            ReportTypeAccess::allowedTypesForStaff(auth()->user()?->staff),
        ),
    );
    $requiresTypeSelection = count($allowedTypes) > 1;
    $singleType = count($allowedTypes) === 1 ? $allowedTypes[0]['value'] : null;
    $descriptions = [
        'organizational_overview' => 'Complete overview across all programs and funding',
        'education' => 'Education program metrics and performance',
        'sports' => 'Sports program metrics and performance',
        'funding' => 'Donors, grants, and funding overview',
    ];
    $reportQuotaForModal = $reportQuota ?? null;
@endphp

<dialog id="reportModal" class="modal">
    <style>
        @keyframes glow-pulse {
            0%, 100% { box-shadow: 0 0 6px rgba(250,204,21,0.3); }
            50% { box-shadow: 0 0 18px rgba(250,204,21,0.7), 0 0 30px rgba(234,179,8,0.2); }
        }
        #progress-bar-fill {
            background: linear-gradient(90deg, #facc15, #eab308);
            box-shadow: 0 0 8px rgba(250,204,21,0.5);
            animation: glow-pulse 2s ease-in-out infinite;
        }
    </style>
    <div class="modal-box max-w-[80vw] max-h-[80vh] bg-white text-slate-800">
        <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
        </form>

        <h3 class="text-xl font-bold mb-4" style="font-family: var(--font-header1);">Generate Report</h3>

        <!-- Step indicator -->
        <div class="flex items-center gap-2 mb-6 text-sm" id="step-indicator">
            @if ($requiresTypeSelection)
                <span class="step-item flex items-center gap-2" data-step="0">
                    <span
                        class="step-number w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold bg-yellow-400 text-slate-900">1</span>
                    <span class="step-label hidden sm:inline text-yellow-600 font-medium">Type</span>
                    <span class="step-separator text-gray-300">→</span>
                </span>
            @endif
            <span class="step-item flex items-center gap-2" data-step="{{ $requiresTypeSelection ? '1' : '0' }}">
                <span
                    class="step-number w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold bg-gray-200 text-gray-500">{{ $requiresTypeSelection ? '2' : '1' }}</span>
                <span class="step-label hidden sm:inline text-gray-400">Period</span>
                @if ($requiresTypeSelection)
                    <span class="step-separator text-gray-300">→</span>
                @endif
            </span>
            <span class="step-item flex items-center gap-2" data-step="{{ $requiresTypeSelection ? '2' : '1' }}">
                <span
                    class="step-number w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold bg-gray-200 text-gray-500">{{ $requiresTypeSelection ? '3' : '2' }}</span>
                <span class="step-label hidden sm:inline text-gray-400">Preview</span>
            </span>
        </div>

        <!-- Step 1: Report Type -->
        @if ($requiresTypeSelection)
            <div id="step-type" class="space-y-4">
                <p class="text-gray-600">Select the type of report you want to generate.</p>
                <div class="grid gap-3 md:grid-cols-2">
                    @forelse ($allowedTypes as $type)
                        <button type="button" onclick="reportGen.selectType('{{ $type['value'] }}')"
                            class="report-type-btn p-4 rounded-xl border border-gray-200 text-left transition-all hover:bg-gray-50"
                            data-type="{{ $type['value'] }}">
                            <p class="font-semibold text-slate-800">{{ $type['label'] }}</p>
                            <p class="text-sm text-gray-500 mt-1">{{ $descriptions[$type['value']] ?? '' }}</p>
                        </button>
                    @empty
                        <p class="text-gray-400 text-sm col-span-2">No report types available for your role.</p>
                    @endforelse
                </div>
            </div>
        @endif

        <!-- Step 2: Period Configuration -->
        <div id="step-period" class="space-y-4" @if ($requiresTypeSelection) style="display:none;" @endif>
            <p class="text-gray-600">Select the reporting period.</p>

            <div class="flex gap-2 mb-4" id="period-tabs">
                <button type="button" onclick="reportGen.selectPeriod('quarterly')"
                    class="period-tab px-4 py-2 rounded-lg text-sm font-medium transition-all bg-gray-100 text-gray-600 hover:bg-gray-200">Quarterly</button>
                <button type="button" onclick="reportGen.selectPeriod('annual')"
                    class="period-tab px-4 py-2 rounded-lg text-sm font-medium transition-all bg-yellow-400 text-slate-900">Annual</button>
                <button type="button" onclick="reportGen.selectPeriod('multi_year')"
                    class="period-tab px-4 py-2 rounded-lg text-sm font-medium transition-all bg-gray-100 text-gray-600 hover:bg-gray-200">Multi-Year</button>
            </div>

            <!-- Quarterly -->
            <div id="period-quarterly" class="space-y-3 period-form" style="display:none;">
                <label class="block">
                    <span class="text-sm text-gray-600">Year</span>
                    <select id="q-year" onchange="reportGen.onQuarterlyYearChange()"
                        class="select select-bordered w-full bg-white border-gray-300">
                        @for ($y = now()->year; $y >= 2000; $y--)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
                </label>
                <label class="block">
                    <span class="text-sm text-gray-600">Quarter</span>
                    <div class="flex gap-2 mt-1" id="quarter-buttons">
                        @for ($q = 1; $q <= 4; $q++)
                            <button type="button" onclick="reportGen.setQuarter({{ $q }})"
                                class="quarter-btn flex-1 px-3 py-2 rounded-lg text-sm font-medium transition-all
                                {{ $q === 1 ? 'bg-yellow-400 text-slate-900' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}
                                {{ $q > 1 || now()->year > now()->year ? '' : '' }}"
                                data-quarter="{{ $q }}">Q{{ $q }}</button>
                        @endfor
                    </div>
                </label>
            </div>

            <!-- Annual -->
            <div id="period-annual" class="space-y-3 period-form">
                <label class="block">
                    <span class="text-sm text-gray-600">Year</span>
                    <select id="a-year" onchange="reportGen.onAnnualYearChange()"
                        class="select select-bordered w-full bg-white border-gray-300">
                        @for ($y = now()->year; $y >= 2000; $y--)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
                </label>
                <div id="annual-warning"
                    class="flex items-center gap-2 p-3 rounded-lg bg-amber-50 border border-amber-200 text-amber-700 text-sm">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                    <span>Data for the current year may not be complete. Consider waiting until the end of the
                        year.</span>
                </div>
            </div>

            <!-- Multi-Year -->
            <div id="period-multi" class="space-y-3 period-form" style="display:none;">
                <label class="block">
                    <span class="text-sm text-gray-600">From Year</span>
                    <select id="m-from" class="select select-bordered w-full bg-white border-gray-300"></select>
                </label>
                <label class="block">
                    <span class="text-sm text-gray-600">To Year</span>
                    <select id="m-to" class="select select-bordered w-full bg-white border-gray-300"></select>
                </label>
                <div id="multi-year-warning"
                    class="flex items-center gap-2 p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm"
                    style="display:none;">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                    <span>"From Year" must be earlier than "To Year".</span>
                </div>
            </div>

            <div class="flex justify-center gap-3 mt-6">
                <button type="button" id="period-back-btn"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl font-semibold transition-all duration-300 hover:scale-105 hover:shadow-lg whitespace-nowrap"
                    style="background-color: var(--color-neutral-dark1); color: var(--color-white); font-family: var(--font-body1);"
                    @unless ($requiresTypeSelection) style="display:none;" @endunless>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"></path>
                    </svg>
                    {{-- back --}}
                </button>

                <button type="button" id="generate-btn"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl font-semibold transition-all duration-300 hover:scale-105 hover:shadow-lg whitespace-nowrap"
                    style="background-color: var(--color-accent1); color: var(--color-primary1); font-family: var(--font-body1);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z" />
                    </svg>
                    <span id="generate-btn-text">Generate</span>
                    <span id="generate-btn-spinner" class="loading loading-spinner loading-sm"
                        style="display:none;"></span>
                </button>
            </div>
        </div>

        <!-- Step 3: Generating -->
        <div id="step-preview" class="space-y-4" style="display:none;">
            <div id="preview-loading" class="w-full space-y-4">
                <div class="flex items-center justify-center gap-2">
                    <span class="loading loading-spinner loading-sm text-yellow-500"></span>
                    <span id="phase-description" class="text-gray-700 font-medium">Generating AI narratives...</span>
                </div>
                <div class="mx-auto" style="width:50%;">
                    <div id="progress-bar-track" class="w-full h-5 bg-gray-200 rounded-full">
                        <div id="progress-bar-fill" class="h-full rounded-full" style="width:0%"></div>
                    </div>
                </div>
                <div class="flex items-center justify-center gap-1.5 text-sm text-gray-500">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                    <span id="helper-text">Gathering program metrics from across the system...</span>
                </div>
            </div>
            <div id="preview-success" class="flex items-center justify-center gap-2 text-green-600" style="display:none;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm font-medium">Report generated successfully!</span>
            </div>
            <div id="partial-warning" class="flex flex-col gap-3 p-4 rounded-xl border border-amber-300 bg-amber-50" style="display:none;">
                <div class="flex items-start gap-2 text-amber-700">
                    <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                    <span id="partial-warning-text" class="text-sm font-medium"></span>
                </div>
                <button type="button" id="retry-failed-btn"
                    class="self-start inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-300 hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
                    style="background-color: var(--color-accent1); color: var(--color-primary1); font-family: var(--font-body1);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182" />
                    </svg>
                    Retry Failed Sections
                </button>
            </div>
            <div id="preview-failed" class="flex flex-col items-center gap-3 text-red-600" style="display:none;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                </svg>
                <span class="text-sm font-medium">Report generation failed. Please try again.</span>
                <button type="button" id="retry-btn"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl font-semibold transition-all duration-300 hover:scale-105 hover:shadow-lg"
                    style="background-color: var(--color-accent1); color: var(--color-primary1); font-family: var(--font-body1);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182" />
                    </svg>
                    Retry
                </button>
            </div>
            <div id="pdf-preview-container" class="w-full" style="display:none;">
                <iframe id="pdf-preview-iframe" class="w-full h-125 rounded-lg border border-gray-200"
                    style="display:none;"></iframe>
            </div>
            <div id="download-buttons" class="flex gap-3 mt-6" style="display:none;"></div>
        </div>
    </div>

    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const CURRENT_YEAR = {{ now()->year }};
        const MIN_YEAR = 2000;
        const GENERATION_PHASES = [
            {
                key: 'generating_narratives',
                label: 'Generating AI narratives...',
                helperTexts: [
                    'AI model is analyzing data patterns...',
                    'Crafting detailed narrative sections for each report area...',
                    'Synthesizing trends and insights from the data...',
                    'Generating comprehensive analysis across all sections...',
                ],
                progressEnd: 75,
            },
            {
                key: 'rendering_pdf',
                label: 'Rendering PDF...',
                helperTexts: [
                    'Formatting report layout with charts and tables...',
                    'Rendering high-quality PDF document via Gotenberg...',
                    'Compositing final report pages with styles...',
                    'Preparing downloadable document for export...',
                ],
                progressEnd: 95,
            },
        ];
        const modal = document.getElementById('reportModal');
        const reportQuota = @json($reportQuotaForModal);

        const state = {
            needsTypeSelection: @json($requiresTypeSelection),
            currentStep: 0,
            selectedType: @json($singleType),
            selectedPeriod: 'annual',
            quarter: 1,
            isGenerating: false,
            reportId: null,
            reportStatus: null,
            pollInterval: null,
            generationCompleted: false,
            lastGenerationPhase: null,
            failedSections: [],
        };

        function updateStepIndicator() {
            const indicators = document.querySelectorAll('#step-indicator .step-item');
            indicators.forEach(item => {
                const step = parseInt(item.dataset.step);
                const num = item.querySelector('.step-number');
                const lbl = item.querySelector('.step-label');
                const isActive = step === state.currentStep;
                num.className =
                    `step-number w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold ${isActive ? 'bg-yellow-400 text-slate-900' : 'bg-gray-200 text-gray-500'}`;
                lbl.className =
                    `step-label hidden sm:inline ${isActive ? 'text-yellow-600 font-medium' : 'text-gray-400'}`;
            });
        }

        function showStep(step) {
            state.currentStep = step;
            const typeEl = document.getElementById('step-type');
            if (typeEl) typeEl.style.display = step === 0 ? '' : 'none';
            document.getElementById('step-period').style.display = (state.needsTypeSelection ? step === 1 :
                step === 0) ? '' : 'none';
            document.getElementById('step-preview').style.display = (state.needsTypeSelection ? step === 2 :
                step === 1) ? '' : 'none';
            updateStepIndicator();
        }

        function showQuarter(period) {
            document.querySelectorAll('.period-form').forEach(el => el.style.display = 'none');
            const map = {
                quarterly: 'period-quarterly',
                annual: 'period-annual',
                multi_year: 'period-multi'
            };
            const el = document.getElementById(map[period]);
            if (el) el.style.display = '';
            if (period === 'multi_year') {
                updateMultiYearOptions();
            }
        }

        function updateQuarterButtons(year) {
            const now = new Date();
            const currentQ = Math.ceil((now.getMonth() + 1) / 3);
            document.querySelectorAll('.quarter-btn').forEach(btn => {
                const q = parseInt(btn.dataset.quarter);
                const disabled = year > CURRENT_YEAR || (year === CURRENT_YEAR && q > currentQ);
                btn.disabled = disabled;
                const active = q === state.quarter && !disabled;
                btn.className =
                    `quarter-btn flex-1 px-3 py-2 rounded-lg text-sm font-medium transition-all ${active ? 'bg-yellow-400 text-slate-900' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'} ${disabled ? 'opacity-40 cursor-not-allowed' : ''}`;
            });
        }

        function updateAnnualWarning() {
            const year = parseInt(document.getElementById('a-year').value);
            document.getElementById('annual-warning').style.display = year === CURRENT_YEAR ? '' : 'none';
        }

        function updateMultiYearOptions() {
            const mFrom = document.getElementById('m-from');
            const mTo = document.getElementById('m-to');
            const warning = document.getElementById('multi-year-warning');

            const currentFrom = parseInt(mFrom.value) || CURRENT_YEAR - 1;
            const currentTo = parseInt(mTo.value) || CURRENT_YEAR;

            // Build "From Year" options: MIN_YEAR .. (mTo value - 1)
            const maxFrom = currentTo - 1;
            mFrom.innerHTML = '';
            for (let y = CURRENT_YEAR; y >= MIN_YEAR; y--) {
                if (y > maxFrom) continue;
                const opt = document.createElement('option');
                opt.value = y;
                opt.textContent = y;
                if (y === currentFrom && y <= maxFrom) opt.selected = true;
                mFrom.appendChild(opt);
            }
            // If currentFrom is now invalid, select the last valid
            if (currentFrom > maxFrom && mFrom.options.length > 0) {
                mFrom.value = mFrom.options[mFrom.options.length - 1].value;
            }

            // Build "To Year" options: (mFrom value + 1) .. CURRENT_YEAR
            const selectedFrom = parseInt(mFrom.value) || MIN_YEAR;
            const minTo = selectedFrom + 1;
            mTo.innerHTML = '';
            for (let y = CURRENT_YEAR; y >= MIN_YEAR; y--) {
                if (y < minTo) continue;
                const opt = document.createElement('option');
                opt.value = y;
                opt.textContent = y;
                if (y === currentTo && y >= minTo) opt.selected = true;
                mTo.appendChild(opt);
            }
            // If currentTo is now invalid, select the first valid
            if (currentTo < minTo && mTo.options.length > 0) {
                mTo.value = mTo.options[0].value;
            }

            // Hide warning when valid
            warning.style.display = 'none';
        }

        function resetPreview() {
            document.getElementById('preview-loading').style.display = '';
            document.getElementById('phase-description').textContent = 'Generating AI narratives...';
            document.getElementById('progress-bar-fill').style.width = '0%';
            document.getElementById('helper-text').textContent = 'AI model is analyzing data patterns...';
            document.getElementById('preview-success').style.display = 'none';
            document.getElementById('preview-failed').style.display = 'none';
            document.getElementById('pdf-preview-container').style.display = 'none';
            document.getElementById('pdf-preview-iframe').style.display = 'none';
            document.getElementById('download-buttons').style.display = 'none';
            document.getElementById('partial-warning').style.display = 'none';
        }

        const GENERATION_PHASE_DURATIONS = [20000, 10000];
        const TICK_MS = 100;

        let simulationActive = false;
        let animationInterval = null;
        let helperInterval = null;
        let currentPhaseIdx = 0;
        let helperTextIdx = 0;
        let phaseStartWallTime = null;
        let phaseFromWidth = 0;
        let phaseTargetWidth = 0;
        let phaseDuration = 6000;

        function setBarWidth(pct) {
            const el = document.getElementById('progress-bar-fill');
            if (el) el.style.setProperty('width', Math.min(pct, 100) + '%', 'important');
        }

        function tick() {
            if (!simulationActive) return;
            const elapsed = Date.now() - phaseStartWallTime;
            const t = Math.min(elapsed / phaseDuration, 1);
            const raw = phaseFromWidth + (phaseTargetWidth - phaseFromWidth) * t;
            setBarWidth(Math.min(raw, phaseTargetWidth));
            animationInterval = requestAnimationFrame(tick);
        }

        function startSimulation() {
            simulationActive = true;
            currentPhaseIdx = 0;
            helperTextIdx = 0;
            phaseFromWidth = 0;
            phaseTargetWidth = GENERATION_PHASES[0].progressEnd;
            phaseDuration = GENERATION_PHASE_DURATIONS[0];
            phaseStartWallTime = Date.now();
            setBarWidth(0);
            document.getElementById('phase-description').textContent = GENERATION_PHASES[0].label;
            document.getElementById('helper-text').textContent = GENERATION_PHASES[0].helperTexts[0];
            helperInterval = setInterval(() => {
                if (!simulationActive) return;
                const texts = GENERATION_PHASES[currentPhaseIdx].helperTexts;
                helperTextIdx = (helperTextIdx + 1) % texts.length;
                document.getElementById('helper-text').textContent = texts[helperTextIdx];
            }, 4000);
            animationInterval = requestAnimationFrame(tick);
        }

        function stopSimulation() {
            simulationActive = false;
            if (animationInterval) {
                cancelAnimationFrame(animationInterval);
                animationInterval = null;
            }
            if (helperInterval) {
                clearInterval(helperInterval);
                helperInterval = null;
            }
            setBarWidth(100);
        }

        function syncSimulationToPhase(phaseKey) {
            if (!simulationActive) return;
            const idx = GENERATION_PHASES.findIndex(p => p.key === phaseKey);
            if (idx === -1 || idx <= currentPhaseIdx) return;
            const currentWidth = parseFloat(document.getElementById('progress-bar-fill').style.width) || 0;
            phaseFromWidth = currentWidth;
            phaseTargetWidth = GENERATION_PHASES[idx].progressEnd;
            phaseDuration = GENERATION_PHASE_DURATIONS[idx] ?? 30000;
            phaseStartWallTime = Date.now();
            currentPhaseIdx = idx;
            document.getElementById('phase-description').textContent = GENERATION_PHASES[idx].label;
            helperTextIdx = 0;
        }

        function simulateCompletion() {
            if (!simulationActive) return;
            simulationActive = false;
            if (animationInterval) {
                clearInterval(animationInterval);
                animationInterval = null;
            }

            const renderPhase = GENERATION_PHASES.find(p => p.key === 'rendering_pdf');
            if (renderPhase) {
                currentPhaseIdx = GENERATION_PHASES.indexOf(renderPhase);
                document.getElementById('phase-description').textContent = renderPhase.label;
            }
            helperTextIdx = 0;

            const startWidth = parseFloat(document.getElementById('progress-bar-fill').style.width) || 0;
            const startTime = Date.now();
            const DURATION = 1500;

            (function fillToComplete() {
                const elapsed = Date.now() - startTime;
                const t = Math.min(elapsed / DURATION, 1);
                const eased = 1 - Math.pow(1 - t, 3);
                const val = startWidth + (100 - startWidth) * eased;
                setBarWidth(val);
                if (t < 1) {
                    animationInterval = requestAnimationFrame(fillToComplete);
                } else {
                    animationInterval = null;
                    showCompleted();
                }
            })();
        }

        function showCompleted() {
            stopSimulation();
            document.getElementById('preview-loading').style.display = 'none';
            document.getElementById('preview-success').style.display = '';

            const previewFrame = document.getElementById('pdf-preview-iframe');
            previewFrame.src = '/reports/' + state.reportId + '/preview';
            previewFrame.style.display = '';
            document.getElementById('pdf-preview-container').style.display = '';

            const dl = document.getElementById('download-buttons');
            dl.style.display = '';
            dl.innerHTML = `
            <a href="/reports/${state.reportId}/pdf" class="btn btn-warning inline-flex flex-1 items-center justify-center gap-2 px-4 py-2.5 rounded-xl font-semibold transition-all duration-300 hover:shadow-lg whitespace-nowrap" style="background-color: var(--color-accent1); color: var(--color-primary1); font-family: var(--font-body1);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.293.707l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Download PDF
            </a>`;

            renderPartialWarning();
        }

        function renderPartialWarning() {
            const warning = document.getElementById('partial-warning');
            const sections = state.failedSections || [];
            if (!sections.length) {
                warning.style.display = 'none';
                return;
            }
            document.getElementById('partial-warning-text').textContent =
                sections.length + ' section' + (sections.length > 1 ? 's' : '') +
                ' could not be generated and appear as placeholder text in this report.';
            warning.style.display = '';
        }

        async function retryFailedSections() {
            if (!state.reportId || state.reportStatus !== 'completed') return;
            const btn = document.getElementById('retry-failed-btn');
            if (btn.disabled) return;
            btn.disabled = true;

            try {
                const resp = await fetch('/reports/' + state.reportId + '/retry-failed', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')
                            ?.content ?? '',
                    },
                });
                if (!resp.ok) return;

                state.generationCompleted = false;
                state.reportStatus = 'generating';
                state.lastGenerationPhase = null;
                state.failedSections = [];
                document.getElementById('preview-success').style.display = 'none';
                document.getElementById('pdf-preview-container').style.display = 'none';
                document.getElementById('download-buttons').style.display = 'none';
                document.getElementById('partial-warning').style.display = 'none';
                resetPreview();
                startSimulation();
                startPolling();
            } finally {
                btn.disabled = false;
            }
        }

        function startPolling() {
            state.pollInterval = setInterval(async () => {
                try {
                    const resp = await fetch('/reports/' + state.reportId + '/status');
                    const data = await resp.json();
                    state.reportStatus = data.status;
                    if (data.status === 'generating' && data.generation_phase && data.generation_phase !== state.lastGenerationPhase) {
                        state.lastGenerationPhase = data.generation_phase;
                        syncSimulationToPhase(data.generation_phase);
                    }
                    if (data.status === 'completed') {
                        state.generationCompleted = true;
                        state.failedSections = data.failed_sections || [];
                        clearInterval(state.pollInterval);
                        state.pollInterval = null;
                        simulateCompletion();
                        return;
                    } else if (data.status === 'failed') {
                        stopSimulation();
                        document.getElementById('preview-loading').style.display = 'none';
                        document.getElementById('preview-failed').style.display = '';
                        clearInterval(state.pollInterval);
                        state.pollInterval = null;
                    }
                } catch (e) {}
            }, 2000);
        }

        function cleanup() {
            stopSimulation();
            if (state.pollInterval) {
                clearInterval(state.pollInterval);
                state.pollInterval = null;
            }
            if (state.generationCompleted) {
                window.location.reload();
            }
        }

        window.reportGen = {
            selectType(value) {
                state.selectedType = value;
                document.querySelectorAll('.report-type-btn').forEach(btn => {
                    const isSel = btn.dataset.type === value;
                    btn.className =
                        `report-type-btn p-4 rounded-xl border text-left transition-all ${isSel ? 'ring-2 ring-yellow-400 bg-yellow-50 border-yellow-300' : 'border-gray-200 hover:bg-gray-50'}`;
                });
                showStep(1);
                showQuarter(state.selectedPeriod);
                updateQuarterButtons(CURRENT_YEAR);
            },
            selectPeriod(value) {
                state.selectedPeriod = value;
                document.querySelectorAll('.period-tab').forEach(btn => {
                    const isSel = btn.textContent.trim().toLowerCase().replace('-', '_') ===
                        value || (value === 'multi_year' && btn.textContent.trim() ===
                            'Multi-Year');
                    btn.className =
                        `period-tab px-4 py-2 rounded-lg text-sm font-medium transition-all ${isSel ? 'bg-yellow-400 text-slate-900' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'}`;
                });
                showQuarter(value);
                if (value === 'quarterly') updateQuarterButtons(parseInt(document.getElementById('q-year')
                    .value));
                if (value === 'multi_year') updateMultiYearOptions();
            },
            setQuarter(q) {
                state.quarter = q;
                updateQuarterButtons(parseInt(document.getElementById('q-year').value));
            },
            onQuarterlyYearChange() {
                const year = parseInt(document.getElementById('q-year').value);
                updateQuarterButtons(year);
            },
            onAnnualYearChange() {
                updateAnnualWarning();
            },
        };

        // Event listeners
        document.getElementById('period-back-btn').addEventListener('click', () => showStep(0));

        // Multi-Year change listeners for dynamic option filtering
        document.getElementById('m-from').addEventListener('change', updateMultiYearOptions);
        document.getElementById('m-to').addEventListener('change', updateMultiYearOptions);

        function buildPayload() {
            const qYear = document.getElementById('q-year');
            const aYear = document.getElementById('a-year');
            const mFrom = document.getElementById('m-from');
            const mTo = document.getElementById('m-to');

            return {
                type: state.selectedType,
                period_type: state.selectedPeriod,
                ...(state.selectedPeriod === 'quarterly' ? {
                    year: parseInt(qYear.value),
                    quarter: state.quarter
                } : {}),
                ...(state.selectedPeriod === 'annual' ? {
                    year: parseInt(aYear.value)
                } : {}),
                ...(state.selectedPeriod === 'multi_year' ? {
                    from_year: parseInt(mFrom.value),
                    to_year: parseInt(mTo.value)
                } : {}),
            };
        }

        async function submitGeneration(payload) {
            // Early quota guard for users who loaded page with limit already reached (exempt admins bypass)
            const isExempt = reportQuota && (reportQuota.is_exempt || reportQuota.isExempt);
            if (!isExempt && reportQuota && reportQuota.remaining <= 0) {
                showStep(state.needsTypeSelection ? 2 : 1);
                resetPreview();
                document.getElementById('preview-loading').style.display = 'none';
                const failEl = document.getElementById('preview-failed');
                failEl.style.display = '';
                const span = failEl.querySelector('span');
                const msg = 'Report generation limit reached (5 per 24 hours). Try again ' + (reportQuota.resetAt ? new Date(reportQuota.resetAt).toLocaleString() : 'later') + '.';
                if (span) span.textContent = msg;
                else failEl.textContent = msg;
                state.isGenerating = false;
                document.getElementById('generate-btn-text').style.display = '';
                document.getElementById('generate-btn-spinner').style.display = 'none';
                document.getElementById('generate-btn').disabled = false;
                return;
            }
            state.isGenerating = true;
            state.reportId = null;
            state.reportStatus = null;
            state.lastGenerationPhase = null;
            showStep(state.needsTypeSelection ? 2 : 1);
            resetPreview();
            startSimulation();

            try {
                const resp = await fetch('/reports/generate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')
                            ?.content ?? '',
                    },
                    body: JSON.stringify(payload),
                });
                if (!resp.ok) {
                    stopSimulation();
                    document.getElementById('preview-loading').style.display = 'none';
                    const failEl = document.getElementById('preview-failed');
                    failEl.style.display = '';
                    // Surface 429 quota message with reset time
                    if (resp.status === 429) {
                        try {
                            const errData = await resp.json();
                            const msg = errData.error || 'Report generation limit reached (5 per 24 hours).';
                            // Replace inner text span if present, else set textContent
                            const span = failEl.querySelector('span');
                            if (span) span.textContent = msg;
                            else failEl.textContent = msg;
                        } catch (_) {}
                    }
                    return;
                }
                const data = await resp.json();
                state.reportId = data.id;
                startPolling();
            } catch (e) {
                stopSimulation();
                document.getElementById('preview-loading').style.display = 'none';
                document.getElementById('preview-failed').style.display = '';
            } finally {
                state.isGenerating = false;
                document.getElementById('generate-btn-text').style.display = '';
                document.getElementById('generate-btn-spinner').style.display = 'none';
                document.getElementById('generate-btn').disabled = false;
            }
        }

        document.getElementById('generate-btn').addEventListener('click', function() {
            if (state.isGenerating || !state.selectedType) return;

            if (state.selectedPeriod === 'multi_year') {
                const mFrom = document.getElementById('m-from');
                const mTo = document.getElementById('m-to');
                const warning = document.getElementById('multi-year-warning');
                const fromYear = parseInt(mFrom.value);
                const toYear = parseInt(mTo.value);
                if (fromYear >= toYear) {
                    warning.style.display = '';
                    return;
                } else {
                    warning.style.display = 'none';
                }
            }

            document.getElementById('generate-btn-text').style.display = 'none';
            document.getElementById('generate-btn-spinner').style.display = '';
            this.disabled = true;

            submitGeneration(buildPayload());
        });

        document.getElementById('retry-btn').addEventListener('click', function() {
            if (state.isGenerating) return;
            document.getElementById('preview-failed').style.display = 'none';
            submitGeneration(buildPayload());
        });

        document.getElementById('retry-failed-btn').addEventListener('click', retryFailedSections);

        modal.addEventListener('close', cleanup);
        modal.addEventListener('open', () => {
            state.selectedType = @json($singleType);
            state.selectedPeriod = 'annual';
            state.quarter = 1;
            state.reportId = null;
            state.reportStatus = null;
            state.lastGenerationPhase = null;
            cleanup();
            if (state.needsTypeSelection) {
                document.querySelectorAll('.report-type-btn').forEach(btn => {
                    btn.className =
                        'report-type-btn p-4 rounded-xl border border-gray-200 text-left transition-all hover:bg-gray-50';
                });
            }
            reportGen.selectPeriod(state.selectedPeriod);
            showStep(0);
        });
    });
</script>
