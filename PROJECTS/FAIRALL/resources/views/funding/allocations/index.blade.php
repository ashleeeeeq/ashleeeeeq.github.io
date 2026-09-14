<x-dashboardlayout title="Allocations">
    <div class="mb-8 space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold tracking-tight"
                    style="font-family: var(--font-header1); color: var(--color-white);">
                    Allocations
                </h1>
                <p class="text-sm mt-1" style="color: rgba(255,255,255,0.6); font-family: var(--font-body1);">
                    Assign donation and grant funds to beneficiaries
                </p>
                <div class="w-20 h-1 mt-2 rounded-full" style="background: var(--color-accent1);"></div>
            </div>
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-3 w-full sm:w-auto">
                <a href="{{ route('funding.allocations.create') }}"
                    class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl font-semibold transition-all duration-300 hover:scale-105 hover:shadow-lg whitespace-nowrap w-full sm:w-auto"
                    style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);"
                    onmouseover="this.style.filter='brightness(0.95)'" onmouseout="this.style.filter='brightness(1)'">
                    <svg class="w-4 sm:w-5 h-4 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.5v15m7.5-7.5h-15">
                        </path>
                    </svg>
                    Allocate Funds
                </a>
                <button type="button" onclick="document.getElementById('exportCsvModal').showModal()"
                    class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl font-semibold transition-all duration-300 hover:scale-105 hover:shadow-lg whitespace-nowrap w-full sm:w-auto"
                    style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.06); color: white; border: 1px solid rgba(255,255,255,0.1);"
                    onmouseover="this.style.backgroundColor='rgba(255,255,255,0.1)'"
                    onmouseout="this.style.backgroundColor='rgba(255,255,255,0.06)'">
                    <svg class="w-4 sm:w-5 h-4 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    Export Allocation CSV
                </button>
            </div>
        </div>

        @include('funding._tabs')

        <!-- Filter Bar -->
        <div class="rounded-2xl mb-6 p-4"
            style="background-color: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);">
            <form method="GET" action="" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
                @if (request()->query('tab'))
                    <input type="hidden" name="tab" value="{{ request()->query('tab') }}">
                @endif
                <div>
                    <label class="block mb-1.5 text-xs font-semibold uppercase tracking-wider"
                        style="color: rgba(255,255,255,0.5);">Search</label>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-primary1" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                        <input type="text" name="search" placeholder="Search ref #, grant name, or beneficiary..."
                            value="{{ request('search') }}"
                            class="w-full rounded-xl pl-10 pr-4 py-2 text-sm text-primary1"
                            style="background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                    </div>
                </div>
                <div>
                    <label class="block mb-1.5 text-xs font-semibold uppercase tracking-wider"
                        style="color: rgba(255,255,255,0.5);">Type</label>
                    <select name="type" class="w-full rounded-xl px-4 py-2 text-sm"
                        style="background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: white;">
                        <option value="donation" {{ request('type', 'donation') == 'donation' ? 'selected' : '' }}
                            style="background: var(--color-white); color: var(--color-primary1);">Donation</option>
                        <option value="grant" {{ request('type') == 'grant' ? 'selected' : '' }}
                            style="background: var(--color-white); color: var(--color-primary1);">Grant</option>
                        <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}
                            style="background: var(--color-white); color: var(--color-primary1);">All</option>
                    </select>
                </div>
                <div>
                    <label class="block mb-1.5 text-xs font-semibold uppercase tracking-wider"
                        style="color: rgba(255,255,255,0.5);">Program</label>
                    <select name="program_id" class="w-full rounded-xl px-4 py-2 text-sm"
                        style="background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: white;">
                        <option value="" style="background: var(--color-white); color: var(--color-primary1);">All
                            Programs</option>
                        @foreach ($programs as $program)
                            <option value="{{ $program->id }}"
                                {{ request('program_id') == $program->id ? 'selected' : '' }}
                                style="background: var(--color-white); color: var(--color-primary1);">
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
                    <a href="{{ route('funding.allocations.index') }}"
                        class="flex-1 px-5 py-2 text-sm font-semibold rounded-xl transition-all duration-200 text-center"
                        style="background-color: rgba(255,255,255,0.05); color: white; border: 1px solid rgba(255,255,255,0.1);">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Allocations Cards -->
        <div class="px-4 sm:px-6">
            <x-bulk-actions-bar bulkUrl="{{ route('funding.allocations.bulk-destroy') }}" tableId="allocations" label="allocations" />
        </div>
        <div class="space-y-4" data-bulk-table="allocations">
            <div class="flex items-center gap-2 px-4 py-2 rounded-lg" style="background-color: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06);">
                <input type="checkbox" data-select-all class="w-4 h-4 rounded border-white/20 bg-white/5 text-yellow-400 focus:ring-yellow-400/50 focus:ring-2 cursor-pointer">
                <span class="text-sm" style="color: rgba(255,255,255,0.7); font-family: var(--font-body1);">Select all allocations on page</span>
            </div>
            @forelse ($sources as $source)
                @php
                    $totalAllocCents = (int) $source['model']->allocations->sum('amount_cents');
                    $totalAllocated = $totalAllocCents / 100;
                    $sourceAmount =
                        $source['type'] === 'donation'
                            ? (float) $source['model']->amount
                            : (float) ($source['model']->total_amount ?? 0);
                    $balance = $sourceAmount - $totalAllocated;
                    $sourceLabel =
                        $source['type'] === 'donation'
                            ? $source['model']->reference_number ?? 'Donation #' . $source['model']->id
                            : $source['model']->grant_name ?? 'Grant #' . $source['model']->id;
                @endphp
                <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-xl"
                    data-source-type="{{ $source['type'] }}"
                    data-source-id="{{ $source['model']->id }}"
                    style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                    <div class="px-4 sm:px-6 py-3 sm:py-4 border-b flex items-center justify-between gap-3 flex-wrap"
                        style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg flex items-center justify-center"
                                style="background-color: rgba(255,204,51,0.15);">
                                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" style="color: var(--color-accent1);"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m3.75 9v7.5m2.25-6.466a9.016 9.016 0 0 0-3.461-.203c-.536.072-.974.478-1.021 1.017a4.559 4.559 0 0 0-.018.402c0 .464.336.844.775.994l2.95 1.012c.44.15.775.53.775.994 0 .136-.006.27-.018.402-.047.539-.485.945-1.021 1.017a9.077 9.077 0 0 1-3.461-.203M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z">
                                    </path>
                                </svg>
                            </div>
                            @if ($source['type'] === 'donation')
                                <a href="{{ route('donors.donations.show', ['donor' => $source['model']->donor->id, 'donation' => $source['model']->id]) }}"
                                    class="text-accent1 font-semibold underline underline-offset-2 transition-colors text-sm sm:text-base"
                                    style="font-family: var(--font-body1);"
                                    onmouseover="this.style.color='var(--color-accent1-dark)'"
                                    onmouseout="this.style.color='var(--color-accent1)'">
                                    {{ $sourceLabel }}
                                </a>
                            @else
                                <a href="{{ route('grants.show', $source['model']->id) }}"
                                    class="text-accent1 font-semibold underline underline-offset-2 transition-colors text-sm sm:text-base"
                                    style="font-family: var(--font-body1);"
                                    onmouseover="this.style.color='var(--color-accent1-dark)'"
                                    onmouseout="this.style.color='var(--color-accent1)'">
                                    {{ $sourceLabel }}
                                </a>
                            @endif
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            @php $isFullyAllocated = (int) round($balance * 100) <= 0; @endphp
                            @if ($isFullyAllocated)
                                <span
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold shrink-0 cursor-not-allowed opacity-50"
                                    style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); color: rgba(255,255,255,0.5); border: 1px solid rgba(255,255,255,0.08);"
                                    title="Fully allocated — no remaining balance">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"></path>
                                    </svg>
                                    Allocate Funds
                                </span>
                            @else
                                @if ($source['type'] === 'donation')
                                    <a href="{{ route('funding.allocations.create', ['allocation_source' => 'donation', 'donation_id' => $source['model']->id, 'program_filter' => $source['model']->program_id ?? '']) }}"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200 hover:scale-[1.02] shrink-0"
                                        style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"></path>
                                        </svg>
                                        Allocate Funds
                                    </a>
                                @else
                                    <a href="{{ route('funding.allocations.create', ['allocation_source' => 'grant', 'grant_id' => $source['model']->id, 'program_filter' => '']) }}"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200 hover:scale-[1.02] shrink-0"
                                        style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"></path>
                                        </svg>
                                        Allocate Funds
                                    </a>
                                @endif
                            @endif
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full"
                                style="background-color: {{ $source['type'] === 'donation' ? 'rgba(16,185,129,0.15)' : 'rgba(255,204,51,0.15)' }}; color: {{ $source['type'] === 'donation' ? 'var(--color-success)' : 'var(--color-accent1)' }}; font-family: var(--font-body1);">
                                {{ $source['type'] === 'donation' ? 'Donation' : 'Grant' }}
                            </span>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead>
                                <tr class="text-white/60 text-xs uppercase tracking-wider border-b"
                                    style="border-color: rgba(255,255,255,0.06);">
                                    <th class="px-4 py-2 font-medium text-center"><span class="sr-only">Select</span></th>
                                    <th class="px-4 py-2 font-medium">Beneficiary</th>
                                    <th class="px-4 py-2 font-medium">Ref #</th>
                                    <th class="px-4 py-2 font-medium">Amount</th>
                                    <th class="px-4 py-2 font-medium">Date Allocated</th>
                                    <th class="px-4 py-2 font-medium text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody data-poll-target="true">
                                @include('funding.allocations._row', ['allocations' => $source['model']->allocations])
                            </tbody>
                        </table>
                    </div>
                    <div data-poll-footer="true" class="px-4 sm:px-6 py-3 flex items-center justify-between gap-4 flex-wrap text-sm"
                        style="border-top: 1px solid rgba(255,255,255,0.06); background-color: rgba(255,255,255,0.02);">
                        @include('funding.allocations._footer', ['totalAllocatedCents' => $totalAllocCents, 'balanceCents' => (int) round($balance * 100)])
                    </div>
                </div>
            @empty
                <div class="rounded-2xl py-12 text-center"
                    style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                    <svg class="w-10 h-10 sm:w-12 sm:h-12 mx-auto mb-3" style="color: rgba(255,255,255,0.2);"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m3.75 9v7.5m2.25-6.466a9.016 9.016 0 0 0-3.461-.203c-.536.072-.974.478-1.021 1.017a4.559 4.559 0 0 0-.018.402c0 .464.336.844.775.994l2.95 1.012c.44.15.775.53.775.994 0 .136-.006.27-.018.402-.047.539-.485.945-1.021 1.017a9.077 9.077 0 0 1-3.461-.203M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z">
                        </path>
                    </svg>
                    <p class="text-sm" style="color: rgba(255,255,255,0.4); font-family: var(--font-body1);">No
                        allocations yet.</p>
                    <a href="{{ route('funding.allocations.create') }}"
                        class="inline-flex items-center gap-2 mt-3 px-3 py-1.5 sm:px-4 sm:py-2 rounded-lg text-xs sm:text-sm font-semibold transition-all duration-200 hover:scale-[1.02]"
                        style="background-color: var(--color-accent1); color: var(--color-primary1); font-family: var(--font-body1);">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.5v15m7.5-7.5h-15">
                            </path>
                        </svg>
                        Create Your First Allocation
                    </a>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if ($sources->hasPages())
            <div class="flex justify-center pt-6 mt-4" style="border-color: rgba(255,255,255,0.08);">
                {{ $sources->links('pagination.custom') }}
            </div>
        @endif
    </div>

    <!-- Export CSV Confirmation Modal -->
    <dialog id="exportCsvModal" class="fixed inset-0 m-auto rounded-2xl overflow-hidden backdrop:backdrop-blur-sm">
        <div class="rounded-2xl p-6 max-w-md mx-auto text-center"
            style="background-color: var(--color-white); border: 1px solid rgba(0,0,0,0.08); width: 100%;">
            <div class="flex flex-col items-center gap-3 mb-5">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center"
                    style="background-color: rgba(255,204,51,0.15);">
                    <svg class="w-6 h-6" style="color: var(--color-accent1);" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold" style="font-family: var(--font-header1); color: var(--color-primary1);">
                    Export Allocations</h3>
            </div>

            <p class="text-sm leading-relaxed mb-6"
                style="color: var(--color-primary1); font-family: var(--font-body1);">
                The exported CSV will include all allocations matching your <strong>current filters</strong>
                @if (request()->filled('search') ||
                        request()->filled('type') ||
                        request()->filled('program_id') ||
                        request()->filled('donor_id') ||
                        request()->filled('grant_id'))
                    <span>(search, type, program, and any source filters)</span>
                @endif
                . Continue?
            </p>

            <div class="flex justify-center gap-3">
                <button type="button" id="exportCsvBtn"
                    onclick="exportAllocations()"
                    class="px-6 py-2.5 font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02] hover:shadow-lg inline-flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100"
                    style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);"
                    onmouseover="if(!this.disabled)this.style.filter='brightness(0.95)'" onmouseout="this.style.filter='brightness(1)'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    <span id="exportCsvBtnText">Export</span>
                </button>
                <button type="button" onclick="document.getElementById('exportCsvModal').close()"
                    class="px-6 py-2.5 font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02] inline-flex items-center justify-center gap-2 group"
                    style="font-family: var(--font-body1); background-color: transparent; color: var(--color-primary1); border: 1px solid rgba(0,0,0,0.15);"
                    onmouseover="this.style.backgroundColor='rgba(0,0,0,0.03)'"
                    onmouseout="this.style.backgroundColor='transparent'">
                    Cancel
                </button>
            </div>
        </div>
    </dialog>

    <script>
        function exportAllocations() {
            const btn = document.getElementById('exportCsvBtn');
            const text = document.getElementById('exportCsvBtnText');

            btn.disabled = true;
            text.textContent = 'Downloading...';

            document.getElementById('exportCsvModal').close();

            const link = document.createElement('a');
            link.href = '{{ route("funding.allocations.export-csv", request()->query()) }}';
            link.style.display = 'none';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        document.getElementById('exportCsvModal').addEventListener('close', function () {
            const btn = document.getElementById('exportCsvBtn');
            btn.disabled = false;
            document.getElementById('exportCsvBtnText').textContent = 'Export';
        });
    </script>

    <script>
        (function() {
            var cards = document.querySelectorAll('[data-source-type]');
            if (!cards.length) return;

            setInterval(function() {
                // Pause polling when bulk bar is visible (any selection) or any bulk dialog is open
                var bar = document.getElementById('bulkBar-allocations');
                if (bar && !bar.classList.contains('hidden')) return;
                if (document.querySelector('dialog[open]')) return;

                // Capture global checked ids once before any fetch
                var wrapper = document.querySelector('[data-bulk-table="allocations"]');
                var globalCheckedIds = new Set();
                if (wrapper) {
                    wrapper.querySelectorAll('[data-row-checkbox]:checked').forEach(function(cb){ globalCheckedIds.add(cb.value); });
                }

                var pending = [];
                cards.forEach(function(card) {
                    var type = card.getAttribute('data-source-type');
                    var id = card.getAttribute('data-source-id');
                    var target = card.querySelector('[data-poll-target]');
                    var footer = card.querySelector('[data-poll-footer]');
                    if (!target) return;

                    var p = fetch('/funding/allocations/poll?type=' + encodeURIComponent(type) + '&id=' + encodeURIComponent(id))
                        .then(function(r) { return r.json(); })
                        .then(function(data) {
                            // If selection was made while fetch was in-flight, skip DOM replacement to avoid wiping new selection or phantom reselect
                            var barNow = document.getElementById('bulkBar-allocations');
                            var dialogOpenNow = document.querySelector('dialog[open]');
                            var hasSelectionNow = barNow && !barNow.classList.contains('hidden');
                            // If bar became visible after fetch started (user selected), skip update for this card
                            // Also if bar was hidden but user deselected after snapshot, globalCheckedIds would be stale; we already paused when bar visible, so this covers select-after-fetch case
                            if (hasSelectionNow || dialogOpenNow) {
                                // Still need to check if this card's data changed due to server? Skip to avoid wiping
                                return;
                            }
                            // If globalCheckedIds was empty at snapshot but now there is selection (user selected during fetch), also skip
                            var currentHasSelection = wrapper && wrapper.querySelectorAll('[data-row-checkbox]:checked').length > 0;
                            if (globalCheckedIds.size === 0 && currentHasSelection) return;

                            target.innerHTML = data.rows;
                            if (footer) footer.innerHTML = data.footer;
                            // Restore checked state from global snapshot (cross-card correct)
                            target.querySelectorAll('[data-row-checkbox]').forEach(function(cb){
                                if (globalCheckedIds.has(cb.value)) cb.checked = true;
                            });
                        })
                        .catch(function() {});
                    pending.push(p);
                });

                // After all cards finish, do single global bar sync
                Promise.all(pending).then(function(){
                    var wrapper2 = document.querySelector('[data-bulk-table="allocations"]');
                    var bar2 = document.getElementById('bulkBar-allocations');
                    if (wrapper2 && bar2) {
                        var allChecked = wrapper2.querySelectorAll('[data-row-checkbox]:checked').length;
                        var allEnabled = wrapper2.querySelectorAll('[data-row-checkbox]:not(:disabled)').length;
                        var selectAll = wrapper2.querySelector('[data-select-all]');
                        var countEl = bar2.querySelector('[data-selected-count]');
                        var deleteBtn = bar2.querySelector('[data-bulk-delete-btn]');
                        bar2.classList.toggle('hidden', allChecked === 0);
                        bar2.classList.toggle('flex', allChecked > 0);
                        if (deleteBtn) deleteBtn.disabled = allChecked === 0;
                        if (countEl) countEl.textContent = String(allChecked);
                        if (selectAll) {
                            selectAll.checked = allEnabled > 0 && allChecked === allEnabled;
                            selectAll.indeterminate = allChecked > 0 && allChecked < allEnabled;
                        }
                        var dialog = document.getElementById('bulkConfirm-allocations');
                        if (dialog) {
                            var confirmCountEl = dialog.querySelector('[data-bulk-confirm-count]');
                            if (confirmCountEl) confirmCountEl.textContent = String(allChecked);
                        }
                    }
                });
            }, 3000);
        })();
    </script>
</x-dashboardlayout>
