@php
    $isDonor = $ownerType === 'donor';
    $backRoute = $isDonor
        ? route('donors.show', ['donor' => $owner, 'tab' => 'deliverables'])
        : route('grants.show', ['grant' => $owner, 'tab' => 'deliverables']);
@endphp

<x-dashboardlayout name="{{ $name }}" title="Deliverable Details">
    <div class="mb-8 space-y-6 pb-10">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <a href="{{ $backRoute }}" class="text-sm text-white/60 hover:text-white transition-colors inline-flex items-center gap-1 mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to deliverables
                </a>
                <h1 class="text-2xl md:text-3xl font-bold tracking-tight" style="font-family: var(--font-header1); color: var(--color-white);">Deliverable Details</h1>
                <p class="text-sm mt-1" style="color: rgba(255,255,255,0.6); font-family: var(--font-body1);">Details for deliverable {{ $deliverable->title }}</p>
                <div class="w-20 h-1 mt-2 rounded-full" style="background: var(--color-accent1);"></div>
            </div>
        </div>

        <!-- Deliverable Info Card -->
        <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-[0_8px_30px_rgba(0,0,0,0.12)]"
            style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
            <div class="px-6 py-4 border-b" style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                        <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-semibold text-white" style="font-family: var(--font-header1);">Deliverable Information</h2>
                        <p class="text-xs mt-0.5" style="color: rgba(255,255,255,0.5);">View and manage deliverable details</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="grid gap-6 md:grid-cols-2">
                    <!-- Left Column -->
                    <div class="space-y-4">
                        <div class="pb-3 border-b" style="border-color: rgba(255,255,255,0.06);">
                            <p class="text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Title</p>
                            <p class="mt-1 text-white font-semibold" style="font-family: var(--font-body1);">{{ $deliverable->title }}</p>
                        </div>

                        <div class="pb-3 border-b" style="border-color: rgba(255,255,255,0.06);">
                            <p class="text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Start Date</p>
                            <p class="mt-1 text-white font-semibold" style="font-family: var(--font-body1);">{{ $deliverable->start_date?->format('F j, Y') ?? 'N/A' }}</p>
                        </div>

                        <div class="pb-3 border-b" style="border-color: rgba(255,255,255,0.06);">
                            <p class="text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">End Date</p>
                            <p class="mt-1 text-white font-semibold" style="font-family: var(--font-body1);">{{ $deliverable->end_date?->format('F j, Y') ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-4">
                        <div class="pb-3 border-b" style="border-color: rgba(255,255,255,0.06);">
                            <p class="text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Progress</p>
                            <div class="mt-2 flex items-center gap-3">
                                <div class="flex-1 h-2 rounded-full" style="background-color: rgba(255,255,255,0.1);">
                                    <div class="h-2 rounded-full transition-all duration-500" style="width: {{ min($deliverable->progress ?? 0, 100) }}%; background-color: var(--color-accent1);"></div>
                                </div>
                                <p class="text-white font-semibold" style="font-family: var(--font-header1);">{{ $deliverable->progress ?? 0 }}%</p>
                            </div>
                        </div>

                        <div class="pb-3 border-b" style="border-color: rgba(255,255,255,0.06);">
                            <p class="text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Status</p>
                            <div class="mt-1">
                                @php
                                    $state = $deliverable->progress >= 100 ? 'Completed' : (optional($deliverable->end_date)->isPast() ? 'Overdue' : 'In progress');
                                    $stateColor = $state === 'Completed' ? '#10b981' : ($state === 'Overdue' ? '#f87171' : '#f59e0b');
                                    $stateBg = $state === 'Completed' ? 'rgba(16,185,129,0.15)' : ($state === 'Overdue' ? 'rgba(248,113,113,0.15)' : 'rgba(245,158,11,0.15)');
                                @endphp
                                <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full" 
                                      style="background-color: {{ $stateBg }}; color: {{ $stateColor }};">
                                    {{ $state }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                @if($deliverable->description)
                    <div class="mt-6 pt-4 border-t" style="border-color: rgba(255,255,255,0.06);">
                        <p class="text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Description</p>
                        <p class="mt-2 text-white leading-relaxed" style="font-family: var(--font-body1); line-height: 1.6;">{{ $deliverable->description }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-dashboardlayout>