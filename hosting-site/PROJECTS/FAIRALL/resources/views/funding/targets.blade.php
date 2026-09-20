<x-dashboardlayout name="{{ $name ?? null }}" title="Funding Targets">
    <div class="mb-8 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold tracking-tight" style="font-family: var(--font-header1); color: var(--color-white);">
                    Funding Targets
                </h1>
                <p class="text-sm mt-1" style="color: rgba(255,255,255,0.6); font-family: var(--font-body1);">
                    Manage annual targets by program or for the whole organization
                </p>
                <div class="w-20 h-1 mt-2 rounded-full" style="background: var(--color-accent1);"></div>
            </div>
            <a href="{{ route('funding.targets.create') }}"
               class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl font-semibold transition-all duration-300 hover:scale-105 hover:shadow-lg whitespace-nowrap"
               style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);' }}"
               onmouseover="this.style.filter='brightness(0.95)'"
               onmouseout="this.style.filter='brightness(1)'">
                <svg class="w-4 sm:w-5 h-4 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"></path>
                </svg>
                Add Target
            </a>
        </div>

        @if (session('status'))
            <div class="mb-6 p-4 rounded-xl flex items-center gap-3" style="background-color: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.3);">
                <svg class="w-5 h-5 shrink-0" style="color: var(--color-success);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-sm" style="color: var(--color-success); font-family: var(--font-body1);">{{ session('status') }}</span>
            </div>
        @endif

        @include('funding._tabs')

        <!-- Filter Bar -->
        <div class="rounded-2xl mb-6 p-4" style="background-color: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);">
            <form method="GET" action="" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
                <div>
                    <label class="block mb-1.5 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Search</label>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-primary1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                        </svg>
                        <input type="text" name="search" placeholder="Search program, year, or notes..." value="{{ request('search') }}"
                               class="w-full rounded-xl pl-10 pr-4 py-2 text-sm text-primary1"
                               style="background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                    </div>
                </div>
                <div>
                    <label class="block mb-1.5 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Program</label>
                    <select name="program_id" class="w-full rounded-xl px-4 py-2 text-sm"
                            style="background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: white;">
                        <option value="" style="background: var(--color-white); color: var(--color-primary1);">All Programs</option>
                        @foreach ($programs as $program)
                            <option value="{{ $program->id }}" {{ request('program_id') == $program->id ? 'selected' : '' }} style="background: var(--color-white); color: var(--color-primary1);">
                                {{ $program->program_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit"
                            class="flex-1 px-5 py-2 text-sm font-semibold rounded-xl transition-all duration-200 hover:scale-[1.02]"
                            style="background-color: var(--color-accent1); color: var(--color-primary1);">
                        Filter
                    </button>
                    <a href="{{ route('funding.targets') }}"
                       class="flex-1 px-5 py-2 text-sm font-semibold rounded-xl transition-all duration-200 text-center"
                       style="background-color: rgba(255,255,255,0.05); color: white; border: 1px solid rgba(255,255,255,0.1);">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl" style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
            <div class="px-4 sm:px-6 py-3 sm:py-4 border-b" style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                <div class="flex items-center justify-between gap-4 flex-wrap">
                    <div class="flex items-center gap-3">
                        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3v1.5M3 21v-6m0 0 2.77-.693a9 9 0 0 1 6.208.682l.108.054a9 9 0 0 0 6.086.71l3.114-.732a48.524 48.524 0 0 1-.005-10.499l-3.11.732a9 9 0 0 1-6.085-.711l-.108-.054a9 9 0 0 0-6.208-.682L3 4.5M3 15V4.5"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-white text-sm sm:text-base" style="font-family: var(--font-header1);">Saved Targets</h3>
                            <p class="text-xs" style="color: rgba(255,255,255,0.5);">Edit or remove target records</p>
                        </div>
                    </div>
                    <span class="text-sm sm:text-basepx-1.5 sm:px-2 py-0.5 sm:py-1 rounded-full self-start sm:self-center" style="background-color: rgba(255,255,255,0.08); color: rgba(255,255,255,0.6); font-family: var(--font-body1);">{{ $targets->total() }} targets</span>
                </div>
            </div>

            <div class="px-4 sm:px-6 pb-3">
                <x-bulk-actions-bar bulkUrl="{{ route('funding.targets.bulk-destroy') }}" tableId="fundingTargets" label="funding targets" />
            </div>

            <div class="overflow-x-auto">
                <div class="p-6">
                    <table class="w-full min-w-200" data-bulk-table="fundingTargets">
                </div>
                    <thead>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.06);">
                            <th class="text-center px-3 sm:px-4 py-2 sm:py-3">
                                <input type="checkbox" data-select-all class="w-4 h-4 rounded border-white/20 bg-white/5 text-yellow-400 focus:ring-yellow-400/50 focus:ring-2 cursor-pointer">
                            </th>
                            <th class="text-left px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-basefont-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Program</th>
                            <th class="text-left px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-basefont-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Year</th>
                            <th class="text-left px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-basefont-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Target</th>
                            <th class="text-left px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-basefont-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Notes</th>
                            <th class="text-center px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-basefont-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Actions</th>
                         </tr>
                    </thead>
                    <tbody>
                        @forelse ($targets as $index => $target)
                            <tr class="transition-colors hover:bg-white/5 {{ $index % 2 == 0 ? 'bg-white/5' : 'bg-transparent' }}" style="border-bottom: 1px solid rgba(255,255,255,0.04);">
                                <td class="px-3 sm:px-4 py-3 align-middle text-center">
                                    <input type="checkbox" value="{{ $target->id }}" data-row-checkbox class="w-4 h-4 rounded border-white/20 bg-white/5 text-yellow-400 focus:ring-yellow-400/50 focus:ring-2 cursor-pointer">
                                </td>
                                <td class="px-3 sm:px-4 py-3 align-middle">
                                    <span class="text-sm text-white" style="font-family: var(--font-body1);">{{ $target->program?->program_name ?? 'Organization-wide' }}</span>
                                </td>
                                <td class="px-3 sm:px-4 py-3 align-middle">
                                    <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full" style="background-color: rgba(255,204,51,0.15); color: var(--color-accent1); font-family: var(--font-body1);">
                                        {{ $target->year }}
                                    </span>
                                </td>
                                <td class="px-3 sm:px-4 py-3 align-middle">
                                    <span class="text-sm font-semibold text-white" style="font-family: var(--font-body1);">
                                        ₱{{ number_format($target->target_amount_cents / 100, 2) }}
                                    </span>
                                </td>
                                <td class="px-3 sm:px-4 py-3 align-middle">
                                    <span class="text-sm text-white/60 line-clamp-2" style="font-family: var(--font-body1);">{{ $target->notes ?: '-' }}</span>
                                </td>
                                <td class="px-3 sm:px-4 py-3 align-middle">
                                    <div class="flex items-center justify-center gap-1 sm:gap-2">
                                        <button type="button"
                                            onclick="openViewModal({{ $target->id }}, '{{ addslashes($target->program?->program_name ?? 'Organization-wide') }}', {{ $target->year }}, {{ $target->target_amount_cents }}, '{{ addslashes($target->notes ?? '') }}')"
                                            class="p-1.5 sm:p-2 rounded-lg transition-all hover:bg-white/10 hover:scale-110"
                                            title="View">
                                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" style="color: rgba(255,255,255,0.6);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.964 7.182a1.01 1.01 0 0 1 0 .636C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178Z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"></path>
                                            </svg>
                                        </button>

                                        <button type="button"
                                            onclick="openEditModal({{ $target->id }}, {{ $target->program_id ?? 'null' }}, {{ $target->year }}, {{ $target->target_amount_cents / 100 }}, '{{ addslashes($target->notes ?? '') }}')"
                                            class="p-1.5 sm:p-2 rounded-lg transition-all hover:bg-white/10 hover:scale-110"
                                            title="Edit">
                                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" style="color: rgba(255,255,255,0.6);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"></path>
                                            </svg>
                                        </button>

                                        <button type="button" 
                                                class="p-1.5 sm:p-2 rounded-lg transition-all hover:bg-red-500/20 hover:scale-110" 
                                                title="Delete"
                                                onclick="document.getElementById('deleteFundingTargetModal_{{ $target->id }}').showModal()">
                                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" style="color: var(--color-danger);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"></path>
                                            </svg>
                                        </button>

                                        <x-confirm-dialog 
                                            id="deleteFundingTargetModal_{{ $target->id }}" 
                                            title="Delete Funding Target" 
                                            message="Are you sure you want to delete the funding target for <strong>{{ $target->program?->program_name ?? 'Organization-wide' }}</strong> for the year <strong>{{ $target->year }}</strong>? This action cannot be undone."
                                            deleteUrl="{{ route('funding.targets.destroy', $target) }}" />
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center">
                                    <svg class="w-10 h-10 sm:w-12 sm:h-12 mx-auto mb-3" style="color: rgba(255,255,255,0.2);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"></path>
                                    </svg>
                                    <p class="text-sm" style="color: rgba(255,255,255,0.4); font-family: var(--font-body1);">No funding targets found</p>
                                    <a href="{{ route('funding.targets.create') }}"
                                       class="inline-flex items-center gap-2 mt-3 px-3 py-1.5 sm:px-4 sm:py-2 rounded-lg text-xs sm:text-sm font-semibold transition-all duration-200 hover:scale-[1.02]"
                                       style="background-color: var(--color-accent1); color: var(--color-primary1); font-family: var(--font-body1);">
                                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"></path>
                                        </svg>
                                        Add Your First Target
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                 </table>
            </div>
        </div>

        @if (method_exists($targets, 'hasPages') && $targets->hasPages())
            <div class="flex justify-center pt-6 mt-4" style="border-color: rgba(255,255,255,0.08);">
                {{ $targets->links('pagination.custom') }}
            </div>
        @endif
    </div>

    <dialog id="editFundingTargetModal" class="p-0 rounded-2xl backdrop:bg-black/50 bg-transparent w-[90%] max-w-lg m-auto">
        <div class="rounded-2xl overflow-hidden w-full shadow-2xl" style="background-color: var(--color-white);">
            <div class="px-6 py-4 border-b bg-primary1" style="border-color: rgba(0,0,0,0.08);">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                        <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-lg" style="font-family: var(--font-header1); color: var(--color-white);">Edit Funding Target</h3>
                        <p class="text-xs" style="color: rgba(255,255,255,0.6);">Modify target details</p>
                    </div>
                    <button type="button" onclick="document.getElementById('editFundingTargetModal').close()"
                        class="ml-auto p-1.5 rounded-lg hover:bg-white/10 transition-all duration-200"
                        style="color: rgba(255,255,255,0.6);">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <form id="editFundingTargetForm" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')

               <div>
                    <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: var(--color-primary1);">Program</label>
                    <div class="relative">
                        <select id="edit_program_id" name="program_id" 
                            class="w-full px-4 py-2.5 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all duration-200 appearance-none cursor-pointer"
                            style="font-family: var(--font-body1); background-color: rgba(0,0,0,0.02); border-color: rgba(0,0,0,0.15); color: var(--color-primary1);">
                            <option value="" style="color: var(--color-primary1);">Organization-wide</option>
                            @foreach ($programs as $program)
                                <option value="{{ $program->id }}" style="color: var(--color-primary1);">{{ $program->program_name }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg class="w-4 h-4 transition-colors duration-200" style="color: var(--color-primary1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: var(--color-primary1);">Year <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                    <input id="edit_year" type="number" name="year" min="2000" max="2100" 
                        class="w-full px-4 py-2.5 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all duration-200"
                        style="font-family: var(--font-body1); background-color: rgba(0,0,0,0.02); border-color: rgba(0,0,0,0.15); color: var(--color-primary1);"
                        required>
                </div>

                <div>
                    <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: var(--color-primary1);">Target Amount <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2" style="color: rgba(0,0,0,0.5);">₱</span>
                        <input id="edit_target_amount" type="number" step="0.01" name="target_amount" 
                            class="w-full pl-7 pr-4 py-2.5 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all duration-200"
                            style="font-family: var(--font-body1); background-color: rgba(0,0,0,0.02); border-color: rgba(0,0,0,0.15); color: var(--color-primary1);"
                            required>
                    </div>
                </div>

                <div>
                    <label class="block mb-2 text-sm font-semibold" style="font-family: var(--font-body1); color: var(--color-primary1);">Notes</label>
                    <textarea id="edit_notes" name="notes" rows="3" 
                        class="w-full px-4 py-2.5 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 transition-all duration-200 resize-y"
                        style="font-family: var(--font-body1); background-color: rgba(0,0,0,0.02); border-color: rgba(0,0,0,0.15); color: var(--color-primary1);"
                        placeholder="Optional notes about this target"></textarea>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit" 
                        class="px-6 py-2.5 font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02] hover:shadow-lg inline-flex items-center justify-center gap-2 group"
                        style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);"
                        onmouseover="this.style.filter='brightness(0.95)'"
                        onmouseout="this.style.filter='brightness(1)'">
                        <svg class="w-4 h-4 transition-all duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9"></path>
                        </svg>
                        Update Target
                    </button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop absolute inset-0 -z-10">
            <button class="w-full h-full cursor-default bg-transparent outline-none">close</button>
        </form>
    </dialog>

    <!-- View Funding Target Modal -->
    <dialog id="viewFundingTargetModal" class="p-0 rounded-2xl backdrop:bg-black/50 bg-transparent w-[90%] max-w-lg m-auto">
        <div class="rounded-2xl overflow-hidden w-full shadow-2xl" style="background-color: var(--color-white);">
            <div class="px-6 py-4 border-b bg-primary1" style="border-color: rgba(0,0,0,0.08);">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                        <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3v1.5M3 21v-6m0 0 2.77-.693a9 9 0 0 1 6.208.682l.108.054a9 9 0 0 0 6.086.71l3.114-.732a48.524 48.524 0 0 1-.005-10.499l-3.11.732a9 9 0 0 1-6.085-.711l-.108-.054a9 9 0 0 0-6.208-.682L3 4.5M3 15V4.5"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-lg" style="font-family: var(--font-header1); color: var(--color-white);">Funding Target Details</h3>
                        <p class="text-xs" style="color: rgba(255,255,255,0.6);">View target information</p>
                    </div>
                    <button type="button" onclick="document.getElementById('viewFundingTargetModal').close()"
                        class="ml-auto p-1.5 rounded-lg hover:bg-white/10 transition-all duration-200"
                        style="color: rgba(255,255,255,0.6);">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="p-6 space-y-4">
                <div class="pb-3 border-b" style="border-color: rgba(0,0,0,0.06);">
                    <dt class="text-xs font-semibold uppercase tracking-wider" style="color: rgba(0,0,0,0.5); font-family: var(--font-body1);">Program</dt>
                    <dd id="view_program" class="mt-1 font-medium" style="color: var(--color-primary1); font-family: var(--font-body1);"></dd>
                </div>
                <div class="pb-3 border-b" style="border-color: rgba(0,0,0,0.06);">
                    <dt class="text-xs font-semibold uppercase tracking-wider" style="color: rgba(0,0,0,0.5); font-family: var(--font-body1);">Year</dt>
                    <dd id="view_year" class="mt-1 font-medium" style="color: var(--color-primary1); font-family: var(--font-body1);"></dd>
                </div>
                <div class="pb-3 border-b" style="border-color: rgba(0,0,0,0.06);">
                    <dt class="text-xs font-semibold uppercase tracking-wider" style="color: rgba(0,0,0,0.5); font-family: var(--font-body1);">Target Amount</dt>
                    <dd id="view_amount" class="mt-1 font-medium" style="color: var(--color-primary1); font-family: var(--font-header1);"></dd>
                </div>
                <div class="pb-3 border-b" style="border-color: rgba(0,0,0,0.06);">
                    <dt class="text-xs font-semibold uppercase tracking-wider" style="color: rgba(0,0,0,0.5); font-family: var(--font-body1);">Notes</dt>
                    <dd id="view_notes" class="mt-1 leading-relaxed" style="color: var(--color-primary1); font-family: var(--font-body1);"></dd>
                </div>

                <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 pt-4">
                    <a id="view_edit_btn" href="#"
                        class="inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-5 py-2 sm:py-2.5 text-sm sm:text-base font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02]"
                        style="font-family: var(--font-body1); background-color: transparent; color: var(--color-info); border: 1px solid var(--color-info);"
                        onmouseover="this.style.backgroundColor='rgba(59,130,246,0.1)'; this.style.color='var(--color-info-dark)'"
                        onmouseout="this.style.backgroundColor='transparent'; this.style.color='var(--color-info)'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"></path>
                        </svg>
                        Edit
                    </a>
                    <button type="button" id="view_delete_btn"
                        class="inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-5 py-2 sm:py-2.5 text-sm sm:text-base font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02]"
                        style="font-family: var(--font-body1); background-color: transparent; color: var(--color-danger); border: 1px solid var(--color-danger);"
                        onmouseover="this.style.backgroundColor='rgba(248,113,113,0.15)'; this.style.color='var(--color-danger-dark)'; this.style.borderColor='var(--color-danger-dark)'"
                        onmouseout="this.style.backgroundColor='transparent'; this.style.color='var(--color-danger)'; this.style.borderColor='var(--color-danger)'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"></path>
                        </svg>
                        Delete
                    </button>
                </div>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop absolute inset-0 -z-10">
            <button class="w-full h-full cursor-default bg-transparent outline-none">close</button>
        </form>
    </dialog>

    <script>
        function openViewModal(id, program, year, amountCents, notes) {
            document.getElementById('view_program').textContent = program;
            document.getElementById('view_year').textContent = year;
            document.getElementById('view_amount').textContent = '₱' + (amountCents / 100).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            document.getElementById('view_notes').textContent = notes || '-';

            document.getElementById('view_edit_btn').onclick = function(e) {
                e.preventDefault();
                document.getElementById('editFundingTargetForm').action = `/funding/targets/${id}`;
                document.getElementById('edit_program_id').value = '';
                document.getElementById('edit_year').value = year;
                document.getElementById('edit_target_amount').value = amountCents / 100;
                document.getElementById('edit_notes').value = notes || '';
                document.getElementById('viewFundingTargetModal').close();
                document.getElementById('editFundingTargetModal').showModal();
            };

            document.getElementById('view_delete_btn').onclick = function() {
                document.getElementById('viewFundingTargetModal').close();
                document.getElementById('deleteFundingTargetModal_' + id).showModal();
            };

            document.getElementById('viewFundingTargetModal').showModal();
        }

        function openEditModal(id, programId, year, targetAmount, notes) {
            const form = document.getElementById('editFundingTargetForm');
            form.action = `/funding/targets/${id}`;
            document.getElementById('edit_program_id').value = programId !== null ? programId : '';
            document.getElementById('edit_year').value = year;
            document.getElementById('edit_target_amount').value = targetAmount;
            document.getElementById('edit_notes').value = notes || '';
            document.getElementById('editFundingTargetModal').showModal();
        }
    </script>
</x-dashboardlayout>