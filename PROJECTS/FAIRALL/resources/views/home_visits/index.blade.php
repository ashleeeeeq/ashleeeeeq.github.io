<x-dashboardlayout name="{{ $name }}" title="Home Visits">
    <div class="mb-8 space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold tracking-tight"
                    style="font-family: var(--font-header1); color: var(--color-white);">Home Visits</h1>
                <p class="text-sm mt-1" style="color: rgba(255,255,255,0.6); font-family: var(--font-body1);">Monitor
                    scheduled household visits and assignments</p>
                <div class="w-20 h-1 mt-2 rounded-full" style="background: var(--color-accent1);"></div>
            </div>
            @can('manage-home-visits')
                <a href="{{ route('home-visits.create') }}"
                    class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl font-semibold transition-all duration-300 hover:scale-105 hover:shadow-lg whitespace-nowrap"
                    style="background-color: var(--color-accent1); color: var(--color-primary1); font-family: var(--font-body1);"
                    onmouseover="this.style.backgroundColor='var(--color-accent1)'; this.style.filter='brightness(0.95)'"
                    onmouseout="this.style.backgroundColor='var(--color-accent1)'; this.style.filter='brightness(1)'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15">
                        </path>
                    </svg>
                    Schedule Visit
                </a>
            @endcan
        </div>

        @if (session('status'))
            <div class="mb-6 p-4 rounded-xl flex items-center gap-3"
                style="background-color: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.3);">
                <svg class="w-5 h-5 shrink-0" style="color: var(--color-success);" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-sm"
                    style="color: var(--color-success); font-family: var(--font-body1);">{{ session('status') }}</span>
            </div>
        @endif

        <!-- Filter Bar -->
        <div class="rounded-2xl mb-6 p-4"
            style="background-color: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);">
            <form method="GET" action="" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
                <div>
                    <label class="block mb-1.5 text-xs font-semibold uppercase tracking-wider"
                        style="color: rgba(255,255,255,0.5);">Search</label>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-primary1" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                        <input type="text" name="search" placeholder="Search beneficiary, staff, or purpose..."
                            value="{{ request('search') }}"
                            class="w-full rounded-xl pl-10 pr-4 py-2 text-sm text-primary1"
                            style="background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                    </div>
                </div>
                @can('manage-users')
                    <div>
                        <label class="block mb-1.5 text-xs font-semibold uppercase tracking-wider"
                            style="color: rgba(255,255,255,0.5);">Program</label>
                        <select name="program_id" class="w-full rounded-xl px-4 py-2 text-sm"
                            style="background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: white;">
                            <option value="" style="background: var(--color-white); color: var(--color-primary1);">All
                                Programs</option>
                            <option value="no_programs" {{ request('program_id') === 'no_programs' ? 'selected' : '' }}
                                style="background: var(--color-white); color: var(--color-primary1);">No Program</option>
                            @foreach ($programs as $program)
                                <option value="{{ $program->id }}"
                                    {{ request('program_id') == $program->id ? 'selected' : '' }}
                                    style="background: var(--color-white); color: var(--color-primary1);">
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
                    <a href="{{ route('home-visits.index') }}"
                        class="flex-1 px-5 py-2 text-sm font-semibold rounded-xl transition-all duration-200 text-center"
                        style="background-color: rgba(255,255,255,0.05); color: white; border: 1px solid rgba(255,255,255,0.1);">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Home Visits Table Card -->
        <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl"
            style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
            <div class="px-6 py-4 border-b"
                style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                <div class="flex items-center justify-between gap-4 flex-wrap">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center"
                            style="background-color: rgba(255,204,51,0.15);">
                            <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-semibold text-white" style="font-family: var(--font-header1);">Visit Records
                            </h2>
                            <p class="text-xs" style="color: rgba(255,255,255,0.5);">All scheduled home visits and
                                assignments</p>
                        </div>
                    </div>
                    <span class="text-xs px-2 py-0.5 rounded-full"
                        style="background-color: rgba(255,255,255,0.08); color: rgba(255,255,255,0.6);">{{ $homeVisits->total() }}
                        visits</span>
                </div>
            </div>
            @can('manage-home-visits')
                <div class="px-4 sm:px-6 pb-3">
                    <x-bulk-actions-bar bulkUrl="{{ route('home-visits.bulk-destroy') }}" tableId="homeVisits" label="home visits" />
                </div>
            @endcan
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="w-full" data-bulk-table="homeVisits">
                        <thead>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.08);">
                                @can('manage-home-visits')
                                    <th class="text-center px-4 py-3">
                                        <input type="checkbox" data-select-all class="w-4 h-4 rounded border-white/20 bg-white/5 text-yellow-400 focus:ring-yellow-400/50 focus:ring-2 cursor-pointer">
                                    </th>
                                @endcan
                                <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider"
                                    style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Beneficiary
                                </th>
                                <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider"
                                    style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Visit Type
                                </th>
                                <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider"
                                    style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Schedule</th>
                                <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider"
                                    style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Assigned
                                    Staff</th>
                                <th class="text-center px-4 py-3 text-xs font-semibold uppercase tracking-wider"
                                    style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($homeVisits as $index => $visit)
                                <tr class="transition-colors hover:bg-white/5 {{ $index % 2 == 0 ? 'bg-white/5' : 'bg-transparent' }}"
                                    style="border-bottom: 1px solid rgba(255,255,255,0.02);">
                                    @can('manage-home-visits')
                                        <td class="px-4 py-3 text-center">
                                            <input type="checkbox" value="{{ $visit->id }}" data-row-checkbox class="w-4 h-4 rounded border-white/20 bg-white/5 text-yellow-400 focus:ring-yellow-400/50 focus:ring-2 cursor-pointer">
                                        </td>
                                    @endcan
                                    <td class="px-4 py-3 text-sm"
                                        style="color: rgba(255,255,255,0.85); font-family: var(--font-body1);">
                                        <a href="/beneficiaries/{{ $visit->beneficiary?->id }}/profile"
                                            class="hover:text-accent1 transition-colors duration-200"
                                            style="color: rgba(255,255,255,0.85);"
                                            onmouseover="this.style.color='var(--color-accent1)'"
                                            onmouseout="this.style.color='rgba(255,255,255,0.85)'">
                                            {{ $visit->beneficiary?->display_name ?? 'Unknown' }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full"
                                            style="background-color: rgba(255,204,51,0.15); color: var(--color-accent1);">
                                            {{ ucfirst(str_replace('-', ' ', $visit->visit_type)) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm"
                                        style="color: rgba(255,255,255,0.7); font-family: var(--font-body1);">
                                        {{ $visit->schedule?->format('M d, Y g:i A') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm"
                                        style="color: rgba(255,255,255,0.7); font-family: var(--font-body1);">
                                        {{ $visit->assignedStaff?->display_name ?? '—' }}
                                    </td>
                                    <td class="px-4 py-3 ">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('home-visits.show', $visit) }}"
                                                class="p-1.5 rounded-lg transition-all hover:bg-white/10"
                                                title="View">
                                                <svg class="w-4 h-4" style="color: rgba(255,255,255,0.6);"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z">
                                                    </path>
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                            </a>
                                            @can('manage-home-visits')
                                                <a href="{{ route('home-visits.edit', $visit) }}"
                                                    class="p-1.5 rounded-lg transition-all hover:bg-white/10"
                                                    title="Edit">
                                                    <svg class="w-4 h-4" style="color: rgba(255,255,255,0.6);"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10">
                                                        </path>
                                                    </svg>
                                                </a>
                                                <button type="button"
                                                    class="p-1.5 rounded-lg transition-all hover:bg-red-500/20"
                                                    title="Delete"
                                                    onclick="document.getElementById('deleteHomeVisitModal_{{ $visit->id }}').showModal()">
                                                    <svg class="w-4 h-4" style="color: #f87171;" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0">
                                                        </path>
                                                    </svg>
                                                </button>

                                                <x-confirm-dialog id="deleteHomeVisitModal_{{ $visit->id }}"
                                                    title="Delete Home Visit"
                                                    message="Are you sure you want to delete the home visit for <strong>{{ $visit->beneficiary?->display_name ?? 'Unknown' }}</strong> on <strong>{{ $visit->schedule?->format('M d, Y') }}</strong>? This record will be moved to the archive and automatically deleted after 90 days."
                                                    deleteUrl="{{ route('home-visits.destroy', $visit) }}" />
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-12 text-center">
                                        <svg class="w-12 h-12 mx-auto mb-3" style="color: rgba(255,255,255,0.2);"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25">
                                            </path>
                                        </svg>
                                        <p class="text-sm"
                                            style="color: rgba(255,255,255,0.4); font-family: var(--font-body1);">No
                                            home visits scheduled.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
        @if ($homeVisits->hasPages())
            <div class="flex justify-center pt-6 mt-4" style="border-color: rgba(255,255,255,0.08);">
                {{ $homeVisits->links('pagination.custom') }}
            </div>
        @endif
    </div>
</x-dashboardlayout>
