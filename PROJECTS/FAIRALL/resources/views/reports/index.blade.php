<x-dashboardlayout name="{{ $name ?? auth()->user()?->display_name }}" title="Reports">
    <div class="mb-8">
        <h1 class="text-3xl md:text-4xl font-bold mb-2" style="font-family: var(--font-header1); color: white;">
            Reports
        </h1>
        <p class="text-white/70 max-w-3xl" style="font-family: var(--font-body1);">
            View and download previously generated reports.
        </p>
        <div class="w-16 h-1 mt-3 rounded-full" style="background-color: var(--color-accent1);"></div>
    </div>

    @include('reports.modal')

    <div class="space-y-4">
        @if ($reports->isEmpty())
            <div class="rounded-2xl p-6 bg-white/5 border border-white/10 text-white/70 text-center">
                <p class="text-lg mb-2">No reports generated yet.</p>
                <button type="button" onclick="reportModal.showModal()"
                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl font-semibold transition-all duration-200 hover:scale-[1.02]"
                    style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);">
                    Generate Your First Report
                </button>
            </div>
        @else
            <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl"
                style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                <div class="px-4 sm:px-6 py-3 sm:py-4 border-b"
                    style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                    <div class="flex items-center gap-3">
                        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg flex items-center justify-center"
                            style="background-color: rgba(255,204,51,0.15);">
                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" style="color: var(--color-accent1);" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-semibold text-white text-sm sm:text-base"
                                style="font-family: var(--font-header1);">Report Records</h2>
                            <p class="text-xs" style="color: rgba(255,255,255,0.5);">Generated reports</p>
                        </div>
                    </div>
                </div>
                @can('is-admin')
                    <div class="px-4 sm:px-6 pb-3">
                        <x-bulk-actions-bar bulkUrl="{{ route('reports.bulk-destroy') }}" tableId="reports" label="reports" />
                    </div>
                @endcan
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="w-full" data-bulk-table="reports">
                            <thead>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.06);">
                                    @can('is-admin')
                                        <th class="text-center px-4 py-3">
                                            <input type="checkbox" data-select-all class="w-4 h-4 rounded border-white/20 bg-white/5 text-yellow-400 focus:ring-yellow-400/50 focus:ring-2 cursor-pointer">
                                        </th>
                                    @endcan
                                    <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider"
                                        style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Type</th>
                                    <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider"
                                        style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Period
                                    </th>
                                    <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider"
                                        style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Date Range
                                    </th>
                                    <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider"
                                        style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Status
                                    </th>
                                    <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider"
                                        style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Generated
                                    </th>
                                    <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider"
                                        style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Generated
                                        By</th>
                                    <th class="text-center px-4 py-3 text-xs font-semibold uppercase tracking-wider"
                                        style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($reports as $index => $report)
                                    <tr class="transition-colors hover:bg-white/5 {{ $index % 2 == 0 ? 'bg-white/5' : 'bg-transparent' }}"
                                        style="border-bottom: 1px solid rgba(255,255,255,0.04);">
                                        @can('is-admin')
                                            <td class="px-4 py-3 text-center">
                                                <input type="checkbox" value="{{ $report->id }}" data-row-checkbox class="w-4 h-4 rounded border-white/20 bg-white/5 text-yellow-400 focus:ring-yellow-400/50 focus:ring-2 cursor-pointer">
                                            </td>
                                        @endcan
                                        <td class="px-4 py-3">
                                            <span
                                                class="text-sm text-white font-medium">{{ $report->typeEnum()?->label() ?? $report->type }}</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="text-sm text-white">{{ $report->periodLabel() }}</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="text-sm" style="color: rgba(255,255,255,0.7);">
                                                {{ $report->date_from?->format('M d, Y') }} –
                                                {{ $report->date_to?->format('M d, Y') }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            @php
                                                $statusStyle = match ($report->status) {
                                                    'completed' => [
                                                        'bg' => 'rgba(16,185,129,0.15)',
                                                        'text' => '#10b981',
                                                    ],
                                                    'generating' => [
                                                        'bg' => 'rgba(59,130,246,0.15)',
                                                        'text' => '#60a5fa',
                                                    ],
                                                    'pending' => ['bg' => 'rgba(250,204,21,0.15)', 'text' => '#facc15'],
                                                    'failed' => ['bg' => 'rgba(248,113,113,0.15)', 'text' => '#f87171'],
                                                    default => [
                                                        'bg' => 'rgba(255,255,255,0.08)',
                                                        'text' => 'rgba(255,255,255,0.6)',
                                                    ],
                                                };
                                            @endphp
                                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full"
                                                style="background-color: {{ $statusStyle['bg'] }}; color: {{ $statusStyle['text'] }}; font-family: var(--font-body1);">
                                                {{ $report->statusEnum()?->label() ?? $report->status }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="text-sm"
                                                style="color: rgba(255,255,255,0.7);">{{ $report->created_at->format('M d, Y g:i A') }}</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="text-sm"
                                                style="color: rgba(255,255,255,0.7);">{{ $report->user?->display_name ?? 'Unknown' }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            @if ($report->isCompleted())
                                                <div class="flex items-center justify-center gap-1">
                                                    <a href="{{ route('reports.preview', $report) }}"
                                                        class="p-1.5 rounded-lg transition-all hover:bg-white/10 hover:scale-110"
                                                        title="View Report" target="_blank">
                                                        <svg class="w-4 h-4" style="color: rgba(255,255,255,0.6);"
                                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z">
                                                            </path>
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z">
                                                            </path>
                                                        </svg>
                                                    </a>
                                                    <a href="{{ route('reports.pdf', $report) }}"
                                                        class="p-1.5 rounded-lg transition-all hover:bg-white/10 hover:scale-110"
                                                        title="Download PDF">
                                                        <svg class="w-4 h-4" style="color: rgba(255,255,255,0.6);"
                                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3">
                                                            </path>
                                                        </svg>
                                                    </a>
                                                    @can('is-admin')
                                                        <button type="button"
                                                            class="p-1.5 rounded-lg transition-all hover:bg-red-500/20"
                                                            title="Delete"
                                                            onclick="document.getElementById('deleteReportModal_{{ $report->id }}').showModal()">
                                                            <svg class="w-4 h-4" style="color: #f87171;" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0">
                                                                </path>
                                                            </svg>
                                                        </button>
                                                    @endcan
                                                </div>

                                                @can('is-admin')
                                                    <x-confirm-dialog id="deleteReportModal_{{ $report->id }}"
                                                        title="Delete Report"
                                                        message="Are you sure you want to delete the <strong>{{ $report->periodLabel() }}</strong> report? This record will be moved to the archive and automatically deleted after 90 days."
                                                        deleteUrl="{{ route('reports.destroy', $report) }}" />
                                                @endcan
                                            @elseif ($report->isFailed())
                                                <div class="flex items-center justify-center gap-1">
                                                    @can('is-admin')
                                                        <button type="button"
                                                            class="p-1.5 rounded-lg transition-all hover:bg-red-500/20"
                                                            title="Delete"
                                                            onclick="document.getElementById('deleteReportModal_{{ $report->id }}').showModal()">
                                                            <svg class="w-4 h-4" style="color: #f87171;" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0">
                                                                </path>
                                                            </svg>
                                                        </button>
                                                    @endcan
                                                </div>

                                                @can('is-admin')
                                                    <x-confirm-dialog id="deleteReportModal_{{ $report->id }}"
                                                        title="Delete Report"
                                                        message="Are you sure you want to delete the <strong>{{ $report->periodLabel() }}</strong> report? This record will be moved to the archive and automatically deleted after 90 days."
                                                        deleteUrl="{{ route('reports.destroy', $report) }}" />
                                                @endcan
                                            @else
                                                <svg class="w-4 h-4 mx-auto animate-spin"
                                                    style="color: var(--color-accent1);" fill="none"
                                                    viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                                        stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor"
                                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                                </svg>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if (method_exists($reports, 'hasPages') && $reports->hasPages())
                <div class="flex justify-center pt-6">
                    {{ $reports->links('pagination.custom') }}
                </div>
            @endif
        @endif
    </div>
</x-dashboardlayout>
