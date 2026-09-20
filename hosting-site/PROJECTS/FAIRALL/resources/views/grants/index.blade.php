<x-dashboardlayout name="{{ $name }}" title="Grants">
    <div class="mb-8 space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold tracking-tight"
                    style="font-family: var(--font-header1); color: var(--color-white);">
                    Grant Management
                </h1>
                <p class="text-sm mt-1" style="color: rgba(255,255,255,0.6); font-family: var(--font-body1);">
                    Manage grants, track funding, and monitor allocations
                </p>
                <div class="w-20 h-1 mt-2 rounded-full" style="background: var(--color-accent1);"></div>
            </div>
            <a href="/grants/create"
                class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl font-semibold transition-all duration-300 hover:scale-105 hover:shadow-lg whitespace-nowrap"
                style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);"
                onmouseover="this.style.filter='brightness(0.95)'" onmouseout="this.style.filter='brightness(1)'">
                <svg class="w-4 sm:w-5 h-4 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15">
                    </path>
                </svg>
                Add Grant
            </a>
        </div>

        <!-- Filter Bar -->
        <div class="rounded-2xl mb-6 p-4" style="background-color: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);">
            <form method="GET" action="" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
                <div>
                    <label class="block mb-1.5 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Search</label>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-primary1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                        </svg>
                        <input type="text" name="search" placeholder="Search grant name, organization, or email..." value="{{ request('search') }}"
                               class="w-full rounded-xl pl-10 pr-4 py-2 text-sm text-primary1"
                               style="background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                    </div>
                </div>
                <div>
                    <label class="block mb-1.5 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5);">Program</label>
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
                <div class="flex gap-2">
                    <button type="submit"
                            class="flex-1 px-5 py-2 text-sm font-semibold rounded-xl transition-all duration-200 hover:scale-[1.02]"
                            style="background-color: var(--color-accent1); color: var(--color-primary1);">
                        Filter
                    </button>
                    <a href="{{ url('/grants') }}"
                       class="flex-1 px-5 py-2 text-sm font-semibold rounded-xl transition-all duration-200 text-center"
                       style="background-color: rgba(255,255,255,0.05); color: white; border: 1px solid rgba(255,255,255,0.1);">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Grants Table Card -->
        <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl"
            style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
            <div class="px-4 sm:px-6 py-3 sm:py-4 border-b"
                style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg flex items-center justify-center"
                            style="background-color: rgba(255,204,51,0.15);">
                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" style="color: var(--color-accent1);" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m3.75 9v7.5m2.25-6.466a9.016 9.016 0 0 0-3.461-.203c-.536.072-.974.478-1.021 1.017a4.559 4.559 0 0 0-.018.402c0 .464.336.844.775.994l2.95 1.012c.44.15.775.53.775.994 0 .136-.006.27-.018.402-.047.539-.485.945-1.021 1.017a9.077 9.077 0 0 1-3.461-.203M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-semibold text-white text-sm sm:text-base"
                                style="font-family: var(--font-header1);">Grant Records</h2>
                            <p class="text-xs" style="color: rgba(255,255,255,0.5);">All grants and funding information
                            </p>
                        </div>
                    </div>
                    <span
                        class="text-sm sm:text-basepx-1.5 sm:px-2 py-0.5 sm:py-1 rounded-full self-start sm:self-center whitespace-nowrap"
                        style="background-color: rgba(255,255,255,0.08); color: rgba(255,255,255,0.6); font-family: var(--font-body1);">{{ $grants->total() }}
                        total</span>
                </div>
            </div>
            <div class="px-4 sm:px-6 pb-3">
                <x-bulk-actions-bar bulkUrl="{{ route('grants.bulk-destroy') }}" tableId="grants" label="grants" />
            </div>

            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-225" data-bulk-table="grants">
                        <thead>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.06);">
                                <th class="text-center px-3 sm:px-4 py-2 sm:py-3">
                                    <input type="checkbox" data-select-all class="w-4 h-4 rounded border-white/20 bg-white/5 text-yellow-400 focus:ring-yellow-400/50 focus:ring-2 cursor-pointer">
                                </th>
                                <th class="text-left px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-basefont-semibold uppercase tracking-wider"
                                    style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Grant</th>
                                <th class="text-left px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-basefont-semibold uppercase tracking-wider"
                                    style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Organization
                                </th>
                                <th class="text-left px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-basefont-semibold uppercase tracking-wider"
                                    style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Total</th>
                                <th class="text-left px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-basefont-semibold uppercase tracking-wider"
                                    style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Alloc. Rate</th>
                                <th class="text-left px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-basefont-semibold uppercase tracking-wider"
                                    style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Period</th>
                                <th class="text-center px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-basefont-semibold uppercase tracking-wider"
                                    style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Linked
                                    Programs</th>
                                <th class="text-center px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-basefont-semibold uppercase tracking-wider"
                                    style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($grants as $index => $grant)
                                <tr class="transition-colors hover:bg-white/5 {{ $index % 2 == 0 ? 'bg-white/5' : 'bg-transparent' }}"
                                    style="border-bottom: 1px solid rgba(255,255,255,0.04);">
                                    <td class="px-3 sm:px-4 py-3 align-middle text-center">
                                        <input type="checkbox" value="{{ $grant->id }}" data-row-checkbox class="w-4 h-4 rounded border-white/20 bg-white/5 text-yellow-400 focus:ring-yellow-400/50 focus:ring-2 cursor-pointer">
                                    </td>
                                    <td class="px-3 sm:px-4 py-3 align-middle">
                                        <div class="font-medium text-white text-xs sm:text-sm"
                                            style="font-family: var(--font-body1);">{{ $grant->grant_name }}</div>
                                        @if ($grant->description)
                                            <div class="text-sm sm:text-basetext-white/40 mt-0.5 line-clamp-1"
                                                style="font-family: var(--font-body1);">
                                                {{ Str::limit($grant->description, 60) }}</div>
                                        @endif
                                    </td>
                                    <td class="px-3 sm:px-4 py-3 align-middle">
                                        <span class="text-xs sm:text-sm block max-w-37.5 truncate"
                                            style="color: rgba(255,255,255,0.7); font-family: var(--font-body1);"
                                            title="{{ $grant->organization_name ?? '—' }}">
                                            {{ $grant->organization_name ?? '—' }}
                                        </span>
                                    </td>
                                    <td class="px-3 sm:px-4 py-3 align-middle">
                                        <span class="text-xs sm:text-sm font-semibold text-white whitespace-nowrap"
                                            style="font-family: var(--font-body1);">
                                            ₱{{ number_format($grant->total_amount, 2) }}
                                        </span>
                                    </td>
                                    <td class="px-3 sm:px-4 py-3 align-middle">
                                        @php
                                            $grantAllocSum = (int) ($grant->allocations_sum_amount_cents ?? 0);
                                            $grantRate = (float) $grant->total_amount > 0 ? min(100, round(($grantAllocSum / 100 / (float) $grant->total_amount) * 100)) : 0;
                                        @endphp
                                        <div class="flex items-center gap-2 max-w-[120px]">
                                            <div class="flex-1 h-2 rounded-full overflow-hidden" style="background-color: rgba(255,255,255,0.08);">
                                                <div class="h-full rounded-full" style="width: {{ $grantRate }}%; background-color: {{ $grantRate === 100 ? 'var(--color-success)' : ($grantRate > 0 ? 'var(--color-accent1)' : 'rgba(255,255,255,0.2)') }};"></div>
                                            </div>
                                            <span class="text-xs font-semibold whitespace-nowrap {{ $grantRate === 100 ? 'text-green-400' : 'text-white/70' }}">
                                                {{ $grantRate }}%
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-3 sm:px-4 py-3 align-middle">
                                        <div class="text-xs sm:text-sm"
                                            style="color: rgba(255,255,255,0.7); font-family: var(--font-body1);">
                                            <div class="whitespace-nowrap">
                                                {{ optional($grant->start_date)->format('M d, Y') ?? '—' }}</div>
                                            <div class="text-white/40 text-sm sm:text-xs">to
                                                {{ optional($grant->end_date)->format('M d, Y') ?? 'ongoing' }}</div>
                                        </div>
                                    </td>
                                    <td class="px-3 sm:px-4 py-3 align-middle">
                                        @if ($grant->programs->count())
                                            <div class="flex flex-wrap gap-1.5 justify-center">
                                                @foreach ($grant->programs as $program)
                                                    <span
                                                        class="inline-flex px-2 py-1 text-xs font-medium rounded-full whitespace-nowrap"
                                                        style="background-color: rgba(255,204,51,0.15); color: var(--color-accent1); font-family: var(--font-body1);">
                                                        {{ $program->program_name ?? 'Program' }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-xs text-white/40"
                                                style="font-family: var(--font-body1);">No linked programs</span>
                                        @endif
                                    </td>
                                    <td class="px-3 sm:px-4 py-3 align-middle">
                                        <div class="flex items-center justify-center gap-1 sm:gap-2">
                                            <a href="/grants/{{ $grant->id }}"
                                                class="p-1.5 sm:p-2 rounded-lg transition-all hover:bg-white/10 hover:scale-110"
                                                title="View">
                                                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4"
                                                    style="color: rgba(255,255,255,0.6);" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z">
                                                    </path>
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                            </a>
                                            <a href="/grants/{{ $grant->id }}/edit"
                                                class="p-1.5 sm:p-2 rounded-lg transition-all hover:bg-white/10 hover:scale-110"
                                                title="Edit">
                                                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4"
                                                    style="color: rgba(255,255,255,0.6);" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10">
                                                    </path>
                                                </svg>
                                            </a>
                                            <button type="button"
                                                class="p-1.5 sm:p-2 rounded-lg transition-all hover:bg-red-500/20 hover:scale-110"
                                                title="Delete"
                                                onclick="document.getElementById('deleteGrantModal_{{ $grant->id }}').showModal()">
                                                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4"
                                                    style="color: var(--color-danger);" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0">
                                                    </path>
                                                </svg>
                                            </button>

                                             <x-confirm-dialog id="deleteGrantModal_{{ $grant->id }}"
                                                 title="Delete Grant"
                                                 message="Are you sure you want to delete the grant <strong>{{ $grant->grant_name }}</strong>? This record will be moved to the archive and automatically deleted after 90 days."
                                                 deleteUrl="/grants/{{ $grant->id }}" />
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-12 text-center">
                                        <svg class="w-10 h-10 sm:w-12 sm:h-12 mx-auto mb-3"
                                            style="color: rgba(255,255,255,0.2);" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m3.75 9v7.5m2.25-6.466a9.016 9.016 0 0 0-3.461-.203c-.536.072-.974.478-1.021 1.017a4.559 4.559 0 0 0-.018.402c0 .464.336.844.775.994l2.95 1.012c.44.15.775.53.775.994 0 .136-.006.27-.018.402-.047.539-.485.945-1.021 1.017a9.077 9.077 0 0 1-3.461-.203M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z">
                                            </path>
                                        </svg>
                                        <p class="text-sm"
                                            style="color: rgba(255,255,255,0.4); font-family: var(--font-body1);">No
                                            grants yet.</p>
                                        <a href="/grants/create"
                                            class="inline-flex items-center gap-2 mt-3 px-3 py-1.5 sm:px-4 sm:py-2 rounded-lg text-xs sm:text-sm font-semibold transition-all duration-200 hover:scale-[1.02]"
                                            style="background-color: var(--color-accent1); color: var(--color-primary1); font-family: var(--font-body1);">
                                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4.5v15m7.5-7.5h-15"></path>
                                            </svg>
                                            Add Your First Grant
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
        @if ($grants->hasPages())
            <div class="flex justify-center pt-6 mt-4">
                {{ $grants->links('pagination.custom') }}
            </div>
        @endif
    </div>
</x-dashboardlayout>
