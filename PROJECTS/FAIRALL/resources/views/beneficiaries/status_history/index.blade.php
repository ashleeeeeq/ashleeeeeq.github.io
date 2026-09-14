<x-dashboardlayout name="{{ $name }}" title="Status History">
    <div class="mb-8 space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-white" style="font-family: var(--font-header1);">Status History</h1>
                <p class="text-white/60 text-sm mt-1" style="font-family: var(--font-body1);">Track beneficiary status changes over time</p>
                <div class="w-16 h-1 mt-2 rounded-full" style="background-color: var(--color-accent1);"></div>
            </div>
        </div>

        @include('beneficiaries.partials.profile-tabs')

        <!-- Status History Card -->
        <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl"
            style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
            <div class="px-4 sm:px-6 py-3 sm:py-4 border-b"
                style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                <div class="flex items-center justify-between gap-4 flex-wrap">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                            <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-semibold text-white" style="font-family: var(--font-header1);">Status Records</h2>
                            <p class="text-xs text-white/50">{{ $beneficiary->display_name }}</p>
                        </div>
                    </div>
                    <a href="/beneficiaries/{{ $beneficiary->id }}/status-history/create" 
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold transition-all duration-300 hover:scale-105 hover:shadow-lg whitespace-nowrap"
                       style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"></path>
                        </svg>
                        Create New Record
                    </a>
                </div>
            </div>
            <div class="p-6">
                @if ($statusRecords->isEmpty())
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 mx-auto mb-4" style="color: rgba(255,255,255,0.2);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"></path>
                        </svg>
                        <p class="text-sm" style="color: rgba(255,255,255,0.4);">No status records found.</p>
                        <p class="text-xs mt-1" style="color: rgba(255,255,255,0.3);">Click "Create New Record" to add a status record.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-225">
                            <thead>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.06);">
                                    <th class="text-left px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-basefont-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Status</th>
                                    <th class="text-center px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-basefont-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Start Date</th>
                                    <th class="text-center px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-basefont-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">End Date</th>
                                    <th class="text-center px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-basefont-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Current</th>
                                    <th class="text-center px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-basefont-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($statusRecords as $index => $record)
                                    <tr class="transition-colors hover:bg-white/5 {{ $index % 2 == 0 ? 'bg-white/5' : 'bg-transparent' }}" style="border-bottom: 1px solid rgba(255,255,255,0.04);">
                                        <td class="px-3 sm:px-4 py-3 align-middle text-left">
                                            <div class="flex items-left gap-2">
                                                <span class="text-sm text-white">{{ $record->statusType->status_name ?? 'Status' }}</span>
                                            </div>
                                        </div>
                                        <td class="px-3 sm:px-4 py-3 align-middle text-center text-sm" style="color: rgba(255,255,255,0.8);">
                                            {{ optional($record->start_date)->format('M d, Y') }}
                                        </div>
                                        <td class="px-3 sm:px-4 py-3 align-middle text-center text-sm" style="color: rgba(255,255,255,0.8);">
                                            {{ $record->end_date ? $record->end_date->format('M d, Y') : 'Ongoing' }}
                                        </div>
                                        <td class="px-3 sm:px-4 py-3 align-middle text-center">
                                            @if (is_null($record->end_date) || $record->end_date->isToday() || $record->end_date->isFuture())
                                                <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full" style="background-color: rgba(16,185,129,0.15); color: var(--color-success);">
                                                    Active
                                                </span>
                                            @else
                                                <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full" style="background-color: rgba(255,255,255,0.1); color: rgba(255,255,255,0.6);">
                                                    Inactive
                                                </span>
                                            @endif
                                        </div>
                                        <td class="px-3 sm:px-4 py-3 align-middle text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <a href="/beneficiaries/{{ $beneficiary->id }}/status-history/{{ $record->id }}/edit" 
                                                   class="p-1.5 sm:p-2 rounded-lg transition-all hover:bg-white/10 hover:scale-110" title="Edit">
                                                    <svg class="w-4 h-4" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"></path>
                                                    </svg>
                                                </a>
                                                <button class="p-1.5 rounded-lg transition-all hover:bg-red-500/20" title="Delete"
                                                        onclick="document.getElementById('deleteModal_{{ $record->id }}').showModal()">
                                                    <svg class="w-4 h-4" style="color: var(--color-danger);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"></path>
                                                    </svg>
                                                </button>

                                                <x-confirm-dialog id="deleteModal_{{ $record->id }}"
                                                    title="Delete Status Record"
                                                    message="Are you sure you want to delete this status record for <strong>{{ $record->statusType->status_name ?? 'Status' }}</strong>? This action cannot be undone."
                                                    deleteUrl="/beneficiaries/{{ $beneficiary->id }}/status-history/{{ $record->id }}" />
                                            </div>
                                        </div>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-12 text-center">
                                            <p class="text-sm" style="color: rgba(255,255,255,0.4);">No status records found.</p>
                                        </div>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-dashboardlayout>