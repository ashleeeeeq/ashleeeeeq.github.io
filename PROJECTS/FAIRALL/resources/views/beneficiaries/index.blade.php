<x-dashboardlayout name="{{ $name }}" title="Beneficiaries">
    <div class="mb-8 space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold tracking-tight" style="font-family: var(--font-header1); color: var(--color-white);">
                    Beneficiaries
                </h1>
                <p class="text-sm mt-1" style="color: rgba(255,255,255,0.6); font-family: var(--font-body1);">
                    Manage beneficiary records and enrollment
                </p>
                <div class="w-20 h-1 mt-2 rounded-full" style="background: var(--color-accent1);"></div>
            </div>
            @can('create-beneficiaries')
                <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 w-full sm:w-auto">
                    <a href="/beneficiaries/create"
                        class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl font-semibold transition-all duration-300 hover:scale-105 hover:shadow-lg whitespace-nowrap"
                        style="background-color: var(--color-accent1); color: var(--color-primary1); font-family: var(--font-body1);">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"></path>
                        </svg>
                        Create
                    </a>
                    <a href="/beneficiaries/enrollment"
                        class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl font-semibold transition-all duration-300 hover:scale-105 hover:shadow-lg whitespace-nowrap"
                        style="background-color: var(--color-accent1); color: var(--color-primary1); font-family: var(--font-body1);">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"></path>
                        </svg>
                        Program Enrollment
                    </a>
                </div>
            @endcan
        </div>

        <!-- Success Message -->
        @if (session('status'))
            <div class="mb-6 p-4 rounded-xl flex items-center gap-3" style="background-color: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.3);">
                <svg class="w-5 h-5 shrink-0" style="color: var(--color-success);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-sm" style="color: var(--color-success); font-family: var(--font-body1);">{{ session('status') }}</span>
            </div>
        @endif

        <!-- Filter Bar -->
        <div class="rounded-2xl mb-6 p-4" style="background-color: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);">
            <form method="GET" action="" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
                <div>
                    <label class="block mb-1.5 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Search</label>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4" style="color: var(--color-primary1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                        </svg>
                        <input type="text" name="search" placeholder="Search name, email, or login ID..." value="{{ request('search') }}"
                               class="w-full rounded-xl pl-10 pr-4 py-2 text-primary1 text-sm"
                               style="background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                    </div>
                </div>
                @can('manage-users')
                    <div>
                        <label class="block mb-1.5 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Program</label>
                        <select name="program_id" class="w-full rounded-xl px-4 py-2 text-sm"
                                style="background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: white;">
                            <option value="" style="background: var(--color-white); color: var(--color-primary1);">All Programs</option>
                            <option value="no_programs" {{ request('program_id') === 'no_programs' ? 'selected' : '' }} style="background: var(--color-white); color: var(--color-primary1);">No Program</option>
                            @foreach ($programs as $program)
                                <option value="{{ $program->id }}" {{ request('program_id') == $program->id ? 'selected' : '' }} style="background: var(--color-white); color: var(--color-primary1);">
                                    {{ $program->program_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endcan
                <div class="flex gap-2 {{ auth()->user()->can('manage-users') ? '' : 'col-span-1' }}">
                    <button type="submit"
                            class="flex-1 px-5 py-2 text-sm font-semibold rounded-xl transition-all duration-200 hover:scale-[1.02]"
                            style="background-color: var(--color-accent1); color: var(--color-primary1);">
                        Filter
                    </button>
                    <a href="/beneficiaries"
                       class="flex-1 px-5 py-2 text-sm font-semibold rounded-xl transition-all duration-200 text-center"
                       style="background-color: rgba(255,255,255,0.05); color: white; border: 1px solid rgba(255,255,255,0.1);">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl" style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
            <div class="px-4 sm:px-6 py-3 sm:py-4 border-b" style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                <div class="flex items-center justify-between gap-4 flex-wrap">
                    <div class="flex items-center gap-3">
                        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 1 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-semibold text-white text-sm sm:text-base" style="font-family: var(--font-header1);">Beneficiary Records</h2>
                            <p class="text-xs" style="color: rgba(255,255,255,0.5);">All enrolled beneficiaries</p>
                        </div>
                    </div>
                    <span class="text-sm sm:text-basepx-1.5 sm:px-2 py-0.5 sm:py-1 rounded-full whitespace-nowrap" style="background-color: rgba(255,255,255,0.08); color: rgba(255,255,255,0.6); font-family: var(--font-body1);">{{ $beneficiaries->total() }} beneficiaries</span>
                </div>
            </div>
            @can('create-beneficiaries')
                <div class="px-4 sm:px-6 pb-3">
                    <x-bulk-actions-bar bulkUrl="{{ route('beneficiaries.bulk-destroy') }}" tableId="beneficiaries" label="beneficiaries" />
                </div>
            @endcan
            <div class="overflow-x-auto">
                <div class="p-6">
                    <table class="w-full min-w-225" data-bulk-table="beneficiaries">
                        <thead>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.06);">
                                @can('create-beneficiaries')
                                    <th class="text-center px-3 sm:px-4 py-2 sm:py-3">
                                        <input type="checkbox" data-select-all class="w-4 h-4 rounded border-white/20 bg-white/5 text-yellow-400 focus:ring-yellow-400/50 focus:ring-2 cursor-pointer">
                                    </th>
                                @endcan
                                <th class="text-left px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-basefont-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Login ID</th>
                                <th class="text-left px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-basefont-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Name</th>
                                <th class="text-left px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-basefont-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Grade Level</th>
                                <th class="text-left px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-basefont-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Program/s</th>
                                <th class="text-center px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-basefont-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($beneficiaries as $index => $beneficiary)
                                <tr class="transition-colors hover:bg-white/5 {{ $index % 2 == 0 ? 'bg-white/5' : 'bg-transparent' }}" style="border-bottom: 1px solid rgba(255,255,255,0.04);">
                                    @can('create-beneficiaries')
                                        <td class="px-3 sm:px-4 py-3 align-middle text-center">
                                            <input type="checkbox" value="{{ $beneficiary->id }}" data-row-checkbox class="w-4 h-4 rounded border-white/20 bg-white/5 text-yellow-400 focus:ring-yellow-400/50 focus:ring-2 cursor-pointer">
                                        </td>
                                    @endcan
                                    <td class="px-3 sm:px-4 py-3 align-middle text-left">
                                        <span class="text-xs sm:text-sm whitespace-nowrap" style="color: rgba(255,255,255,0.7); font-family: var(--font-body1);">{{ $beneficiary->user?->login_id ?? 'N/A' }}</span>
                                    </td>
                                    <td class="px-3 sm:px-4 py-3 align-middle text-left">
                                        <a href="/beneficiaries/{{ $beneficiary->id }}/profile"
                                            class="text-xs sm:text-sm font-medium text-white block max-w-37.5 truncate hover:scale-105 transition-all duration-200"
                                            style="font-family: var(--font-body1);"
                                            onmouseover="this.style.color='var(--color-accent1)'"
                                            onmouseout="this.style.color='white'"
                                            title="{{ $beneficiary->display_name }}">
                                            {{ $beneficiary->display_name }}
                                        </a>
                                    </td>
                                    <td class="px-3 sm:px-4 py-3 align-middle text-left">
                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full whitespace-nowrap" style="background-color: rgba(59,130,246,0.15); color: #60a5fa; font-family: var(--font-body1);">
                                            @php
                                                $latestAcademicRecord = $beneficiary->academicRecords
                                                    ->sortByDesc(fn($record) => [$record->academic_year])
                                                    ->first();
                                            @endphp
                                            {{ $latestAcademicRecord->grade_level ?? ($beneficiary->intakeSheet->grade_level ?? 'N/A') }}
                                        </span>
                                    </td>
                                    <td class="px-3 sm:px-4 py-3 align-middle text-left">
                                        @php
                                            $active = $beneficiary->activePrograms ?? null;
                                            $programs = $active ?: $beneficiary->programs;
                                        @endphp
                                        @if($programs && $programs->count() > 0)
                                            <div class="flex flex-wrap gap-1">
                                                @foreach ($programs as $program)
                                                    <span class="inline-flex px-2 py-1 text-xs rounded-full whitespace-nowrap" style="background-color: rgba(255,204,51,0.15); color: var(--color-accent1); font-family: var(--font-body1);">
                                                        {{ $program->program_name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-xs" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">None</span>
                                        @endif
                                    </td>
                                    <td class="px-3 sm:px-4 py-3 align-middle text-center">
                                        <div class="flex items-center justify-center gap-1 sm:gap-2">
                                            <a href="/beneficiaries/{{ $beneficiary->id }}/profile"
                                                class="p-1.5 sm:p-2 rounded-lg transition-all hover:bg-white/10 hover:scale-110"
                                                title="View">
                                                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" style="color: rgba(255,255,255,0.6);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                            </a>
                                            @can('create-beneficiaries')
                                                <a href="/beneficiaries/{{ $beneficiary->id }}/edit"
                                                    class="p-1.5 sm:p-2 rounded-lg transition-all hover:bg-white/10 hover:scale-110"
                                                    title="Edit">
                                                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" style="color: rgba(255,255,255,0.6);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"></path>
                                                    </svg>
                                                </a>
                                                <button type="button"
                                                    class="p-1.5 sm:p-2 rounded-lg transition-all hover:bg-red-500/20 hover:scale-110"
                                                    title="Delete"
                                                    onclick="document.getElementById('deleteBeneficiaryModal_{{ $beneficiary->id }}').showModal()">
                                                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" style="color: var(--color-danger);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"></path>
                                                    </svg>
                                                </button>

                                                <x-confirm-dialog id="deleteBeneficiaryModal_{{ $beneficiary->id }}"
                                                    title="Delete Beneficiary"
                                                    message="Are you sure you want to delete <strong>{{ $beneficiary->display_name }}</strong>? This action cannot be undone."
                                                    deleteUrl="/beneficiaries/{{ $beneficiary->id }}" />
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ \Auth::user()->can('create-beneficiaries') ? 6 : 4 }}" class="px-4 py-12 text-center">
                                        <svg class="w-10 h-10 sm:w-12 sm:h-12 mx-auto mb-3" style="color: rgba(255,255,255,0.2);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                        </svg>
                                        <p class="text-sm" style="color: rgba(255,255,255,0.4); font-family: var(--font-body1);">No beneficiaries found.</p>
                                        <a href="/beneficiaries/create" 
                                           class="inline-flex items-center gap-2 mt-3 px-3 py-1.5 sm:px-4 sm:py-2 rounded-lg text-xs sm:text-sm font-semibold transition-all duration-200 hover:scale-[1.02]"
                                           style="background-color: var(--color-accent1); color: var(--color-primary1); font-family: var(--font-body1);">
                                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"></path>
                                            </svg>
                                            Add Your First Beneficiary
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        @if ($beneficiaries->hasPages())
            <div class="flex justify-center pt-6 mt-4">
                {{ $beneficiaries->links('pagination.custom') }}
            </div>
        @endif
    </div>
</x-dashboardlayout>