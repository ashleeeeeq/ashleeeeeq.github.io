<x-dashboardlayout name="{{ $name }}" title="Injury Records">
    <div class="mb-8 space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-white" style="font-family: var(--font-header1);">Injury Records</h1>
                <p class="text-white/60 text-sm mt-1" style="font-family: var(--font-body1);">Manage injury and recovery records for {{ $beneficiary->display_name }}</p>
                <div class="w-16 h-1 mt-2 rounded-full" style="background-color: var(--color-accent1);"></div>
            </div>
        </div>

        @include('beneficiaries.partials.profile-tabs')

        <!-- Injury Records Card -->
        <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl"
            style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
            <div class="px-4 sm:px-6 py-3 sm:py-4 border-b"
                style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                <div class="flex items-center justify-between gap-4 flex-wrap">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                            <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-semibold text-white" style="font-family: var(--font-header1);">Injury Records</h2>
                            <p class="text-xs text-white/50">Track injuries, recovery progress, and medical history</p>
                        </div>
                    </div>
                    <a href="/beneficiaries/{{ $beneficiary->id }}/injury-records/create" 
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
                @if ($injuryRecords->isEmpty())
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 mx-auto mb-4" style="color: rgba(255,255,255,0.2);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"></path>
                        </svg>
                        <p class="text-sm" style="color: rgba(255,255,255,0.4);">No injury records found.</p>
                        <p class="text-xs mt-1" style="color: rgba(255,255,255,0.3);">Click "Create New Record" to add an injury record.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-225">
                            <thead>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.06);">
                                    <th class="text-left px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-basefont-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Type</th>
                                    <th class="text-center px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-basefont-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Body Part</th>
                                    <th class="text-center px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-basefont-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Severity</th>
                                    <th class="text-center px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-basefont-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Status</th>
                                    <th class="text-center px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-basefont-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Recovery Dates</th>
                                    <th class="text-center px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-basefont-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($injuryRecords as $index => $record)
                                    @php
                                        $severityColors = [
                                            'mild' => ['bg' => 'rgba(16,185,129,0.15)', 'color' => 'var(--color-success)'],
                                            'moderate' => ['bg' => 'rgba(255,204,51,0.15)', 'color' => 'var(--color-accent1)'],
                                            'severe' => ['bg' => 'rgba(248,113,113,0.15)', 'color' => 'var(--color-danger)'],
                                        ];
                                        $severityColor = $severityColors[$record->severity] ?? ['bg' => 'rgba(255,255,255,0.1)', 'color' => 'rgba(255,255,255,0.7)'];
                                        
                                        $statusColors = [
                                            'ongoing' => ['bg' => 'rgba(255,204,51,0.15)', 'color' => 'var(--color-accent1)'],
                                            'recovering' => ['bg' => 'rgba(59,130,246,0.15)', 'color' => 'var(--color-info)'],
                                            'recovered' => ['bg' => 'rgba(16,185,129,0.15)', 'color' => 'var(--color-success)'],
                                        ];
                                        $statusColor = $statusColors[$record->status] ?? ['bg' => 'rgba(255,255,255,0.1)', 'color' => 'rgba(255,255,255,0.7)'];
                                    @endphp
                                    <tr class="transition-colors hover:bg-white/5 {{ $index % 2 == 0 ? 'bg-white/5' : 'bg-transparent' }}" style="border-bottom: 1px solid rgba(255,255,255,0.04);">
                                        <td class="px-3 sm:px-4 py-3 align-middle text-left">
                                            <div class="flex items-left">
                                                <span class="text-sm text-white">{{ $record->injury_type }}</span>
                                            </div>
                                        </div>
                                        <td class="px-3 sm:px-4 py-3 align-middle text-center text-sm text-white">{{ $record->body_part }}</div>
                                        <td class="px-3 sm:px-4 py-3 align-middle text-center">
                                            <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full" 
                                                  style="background-color: {{ $severityColor['bg'] }}; color: {{ $severityColor['color'] }};">
                                                {{ ucfirst($record->severity) }}
                                            </span>
                                        </div>
                                        <td class="px-3 sm:px-4 py-3 align-middle text-center">
                                            <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full" 
                                                  style="background-color: {{ $statusColor['bg'] }}; color: {{ $statusColor['color'] }};">
                                                {{ ucfirst($record->status) }}
                                            </span>
                                        </div>
                                        <td class="px-3 sm:px-4 py-3 align-middle text-center">
                                            <div class="text-sm" style="color: rgba(255,255,255,0.8);">
                                                <div>{{ \Carbon\Carbon::parse($record->recovery_start_date)->format('M d, Y') }}</div>
                                                @if ($record->recovery_end_date)
                                                    <div class="text-xs" style="color: rgba(255,255,255,0.5);">
                                                        to {{ \Carbon\Carbon::parse($record->recovery_end_date)->format('M d, Y') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <td class="px-3 sm:px-4 py-3 align-middle text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <a href="/beneficiaries/{{ $beneficiary->id }}/injury-records/{{ $record->id }}" 
                                                   class="p-1.5 sm:p-2 rounded-lg transition-all hover:bg-white/10 hover:scale-110" title="View">
                                                    <svg class="w-4 h-4" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    </svg>
                                                </a>
                                                <a href="/beneficiaries/{{ $beneficiary->id }}/injury-records/{{ $record->id }}/edit" 
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
                                                    title="Delete Injury Record"
                                                    message="Are you sure you want to delete the injury record for <strong>{{ $record->injury_type }}</strong>? This action cannot be undone."
                                                    deleteUrl="/beneficiaries/{{ $beneficiary->id }}/injury-records/{{ $record->id }}" />
                                            </div>
                                        </div>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-12 text-center">
                                            <p class="text-sm" style="color: rgba(255,255,255,0.4);">No injury records found.</p>
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