<x-dashboardlayout title="Funding">
    <div class="mb-8 space-y-6 space-y-6">
        <div>
            <h1 class="text-3xl md:text-4xl font-bold text-white" style="font-family: var(--font-header1);">Funding</h1>
            <p class="text-white/70 mt-2" style="font-family: var(--font-body1);">Track received funds, allocations, and
                annual targets for {{ $currentYear }}.</p>
            <div class="w-16 h-1 mt-3 rounded-full" style="background-color: var(--color-accent1);"></div>
            <form method="GET" class="mt-4 flex items-center gap-3">
                <label class="text-xs font-semibold uppercase tracking-wider text-white/50">Year</label>
                <select name="year" onchange="this.form.submit()"
                    class="rounded-xl px-4 py-2 text-sm"
                    style="background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: white;">
                    @for ($y = now()->year; $y >= 2020; $y--)
                        <option value="{{ $y }}" style="background: var(--color-primary1);" @selected($y === $currentYear)>{{ $y }}</option>
                    @endfor
                </select>
                <noscript><button type="submit" class="px-3 py-2 text-sm rounded-xl"
                    style="background-color: var(--color-accent1); color: var(--color-primary1);">Go</button></noscript>
            </form>
        </div>

        @include('funding._tabs')

        <div class="grid gap-4 md:grid-cols-4">

            <div class="rounded-2xl p-5 relative overflow-hidden group transition-all duration-200 hover:scale-[1.02]"
                style="background: linear-gradient(135deg, rgba(255,204,51,0.12) 0%, rgba(255,204,51,0.03) 100%); border: 1px solid rgba(255,204,51,0.15);">
                <div class="absolute top-0 right-0 w-24 h-24 opacity-10 rounded-full -translate-y-1/2 translate-x-1/2 transition-all duration-200 group-hover:opacity-20" style="background: var(--color-accent1);"></div>
                <p class="text-xs uppercase tracking-wider text-white/50">Target</p>
                <p class="mt-2 text-2xl font-bold text-white">PHP {{ number_format($targetTotalCents / 100, 2) }}</p>
                <p class="mt-2 text-sm text-white/60">Current year target total</p>
            </div>

            <div class="rounded-2xl p-5 relative overflow-hidden group transition-all duration-200 hover:scale-[1.02]"
                style="background: linear-gradient(135deg, rgba(59,130,246,0.12) 0%, rgba(59,130,246,0.03) 100%); border: 1px solid rgba(59,130,246,0.15);">
                <div class="absolute top-0 right-0 w-24 h-24 opacity-10 rounded-full -translate-y-1/2 translate-x-1/2 transition-all duration-200 group-hover:opacity-20" style="background: #3b82f6;"></div>
                <p class="text-xs uppercase tracking-wider text-white/50">Remaining from Target</p>
                <p class="mt-2 text-2xl font-bold text-white">PHP {{ number_format($remainingTargetCents / 100, 2) }}
                </p>
                <p class="mt-2 text-sm text-white/60">Funds left to be received to achieve target</p>
            </div>

            <div class="rounded-2xl p-5 relative overflow-hidden group transition-all duration-200 hover:scale-[1.02]"
                style="background: linear-gradient(135deg, rgba(16,185,129,0.12) 0%, rgba(16,185,129,0.03) 100%); border: 1px solid rgba(16,185,129,0.15);">
                <div class="absolute top-0 right-0 w-24 h-24 opacity-10 rounded-full -translate-y-1/2 translate-x-1/2 transition-all duration-200 group-hover:opacity-20" style="background: var(--color-success);"></div>
                <p class="text-xs uppercase tracking-wider text-white/50">Received</p>
                <p class="mt-2 text-2xl font-bold text-white">PHP {{ number_format($receivedTotalCents / 100, 2) }}</p>
                <p class="mt-2 text-sm text-white/60">All received donations and grants this year</p>
            </div>

            <div class="rounded-2xl p-5 relative overflow-hidden group transition-all duration-200 hover:scale-[1.02]"
                style="background: linear-gradient(135deg, rgba(168,85,247,0.12) 0%, rgba(168,85,247,0.03) 100%); border: 1px solid rgba(168,85,247,0.15);">
                <div class="absolute top-0 right-0 w-24 h-24 opacity-10 rounded-full -translate-y-1/2 translate-x-1/2 transition-all duration-200 group-hover:opacity-20" style="background: #a855f7;"></div>
                <p class="text-xs uppercase tracking-wider text-white/50">Allocated</p>
                <p class="mt-2 text-2xl font-bold text-white">PHP {{ number_format($allocatedTotalCents / 100, 2) }}</p>
                <p class="mt-2 text-sm text-white/60">Funds assigned to beneficiaries</p>
            </div>

        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <div class="rounded-2xl p-6 group transition-all duration-200 hover:scale-[1.02]"
                style="background-color: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08);">
                <div class="flex items-center justify-between gap-4 mb-4">
                    <div>
                        <h2 class="text-xl font-bold text-white" style="font-family: var(--font-header1);">Received vs
                            Target</h2>
                        <p class="text-white/60 text-sm">Progress toward this year’s fundraising target</p>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-white/50 uppercase tracking-wider">Completion</div>
                        <div class="text-2xl font-bold text-white">{{ $receivedProgressPercent }}%</div>
                    </div>
                </div>
                <div class="w-full h-4 rounded-full overflow-hidden" style="background-color: rgba(255,255,255,0.08);">
                    <div class="h-full rounded-full"
                        style="width: {{ $receivedProgressPercent }}%; background-color: var(--color-accent1);"></div>
                </div>
                <div class="mt-4 text-sm text-white/70">
                    {{ $receivedProgressPercent }}% of the target has been received.
                </div>
            </div>

            <div class="rounded-2xl p-6 group transition-all duration-200 hover:scale-[1.02]"
                style="background-color: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08);">
                <div class="flex items-center justify-between gap-4 mb-4">
                    <div>
                        <h2 class="text-xl font-bold text-white" style="font-family: var(--font-header1);">Allocated vs
                            Received</h2>
                        <p class="text-white/60 text-sm">How much of received funds have been assigned</p>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-white/50 uppercase tracking-wider">Allocation Rate</div>
                        <div class="text-2xl font-bold text-white">{{ $allocatedVsReceivedPercent }}%</div>
                    </div>
                </div>
                <div class="w-full h-4 rounded-full overflow-hidden" style="background-color: rgba(255,255,255,0.08);">
                    <div class="h-full rounded-full"
                        style="width: {{ $allocatedVsReceivedPercent }}%; background-color: var(--color-success);">
                    </div>
                </div>
                <div class="mt-4 text-sm text-white/70">
                    {{ $allocatedVsReceivedPercent }}% of funds received this year have been allocated.
                </div>
            </div>
        </div>

            <div class="rounded-2xl p-6 group transition-all duration-200 hover:scale-[1.02]"
                style="background-color: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08);">
            <div class="flex items-center justify-between gap-4 mb-5">
                <div>
                    <h2 class="text-xl font-bold text-white" style="font-family: var(--font-header1);">Monthly Trend
                    </h2>
                    <p class="text-white/60 text-sm">Received and allocated funds by month</p>
                </div>
            </div>

            <div class="h-72">
                <canvas id="monthlyTrendChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const canvas = document.getElementById('monthlyTrendChart');
            if (!canvas) return;

            const config = {
                type: 'bar',
                data: {
                    labels: @json($monthlyLabels),
                    datasets: [
                        {
                            label: 'Received',
                            data: @json(array_map(fn($v) => (float) ($v / 100), $monthlyReceived)),
                            backgroundColor: 'rgba(255, 204, 51, 1)',
                            borderColor: 'rgba(255, 204, 51, 1)',
                            borderWidth: 1,
                            borderRadius: 4,
                        },
                        {
                            label: 'Allocated',
                            data: @json(array_map(fn($v) => (float) ($v / 100), $monthlyAllocated)),
                            backgroundColor: 'rgba(16, 185, 129, 1)',
                            borderColor: 'rgba(16, 185, 129, 1)',
                            borderWidth: 1,
                            borderRadius: 4,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: 'rgba(255,255,255,0.6)',
                            },
                        },
                        tooltip: {
                            callbacks: {
                                label: function (ctx) {
                                    return ctx.dataset.label + ': PHP ' + Number(ctx.raw).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                                },
                            },
                        },
                    },
                    scales: {
                        x: {
                            grid: { color: 'rgba(255,255,255,0.06)' },
                            ticks: { color: 'rgba(255,255,255,0.5)' },
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(255,255,255,0.06)' },
                            ticks: {
                                color: 'rgba(255,255,255,0.5)',
                                callback: function (value) {
                                    return 'PHP ' + value.toLocaleString();
                                },
                            },
                        },
                    },
                },
            };

            try {
                if (typeof applyGradientBackgrounds === 'function') {
                    applyGradientBackgrounds(config);
                }
                if (typeof Chart !== 'undefined') {
                    new Chart(canvas, config);
                } else {
                    console.error('Chart.js not loaded');
                }
            } catch (e) {
                console.error('Failed to render monthly trend chart', e);
            }
        });
    </script>
</x-dashboardlayout>
