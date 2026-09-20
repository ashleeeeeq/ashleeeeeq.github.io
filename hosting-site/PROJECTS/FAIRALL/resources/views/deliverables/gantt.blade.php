@php
    $isDonor = $ownerType === 'donor';
    $showOwnerRoute = $isDonor
        ? route('donors.show', ['donor' => $owner, 'tab' => 'deliverables'])
        : route('grants.show', ['grant' => $owner, 'tab' => 'deliverables']);
    $indexRoute = $isDonor ? route('donors.deliverables.index', $owner) : route('grants.deliverables.index', $owner);
    $createRoute = $isDonor ? route('donors.deliverables.create', $owner) : route('grants.deliverables.create', $owner);
@endphp

<x-dashboardlayout name="{{ $name }}" title="Deliverables Gantt">
    <div class="mb-8 space-y-6 space-y-6">
        <!-- Header Section -->
        <div>
            <h1 class="text-2xl md:text-4xl font-bold tracking-tight"
                style="font-family: var(--font-header1); color: var(--color-white);">Deliverables Roadmap</h1>
            <p class="text-sm mt-1" style="color: rgba(255,255,255,0.6); font-family: var(--font-body1);">Drag to shift
                dates, use the
                progress handle to update completion, click to view, edit, or delete task.</p>
            <div class="w-20 h-1 mt-2 rounded-full" style="background: var(--color-accent1);"></div>
        </div>

        <!-- Gantt Chart Section -->
        <div class="rounded-2xl overflow-hidden"
            style="background-color: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);">
            <div class="px-6 py-4 border-b"
                style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center"
                            style="background-color: rgba(255,204,51,0.15);">
                            <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-semibold text-white" style="font-family: var(--font-header1);">Gantt Chart
                            </h2>
                            <p class="text-xs mt-0.5" style="color: rgba(255,255,255,0.6);">Visual timeline of
                                deliverables</p>
                        </div>
                    </div>
                    <span class="text-xs px-3 py-1 rounded-full"
                        style="background: linear-gradient(135deg, rgba(16,185,129,0.2), rgba(16,185,129,0.05)); border: 1px solid rgba(16,185,129,0.3); color: #10b981;">
                        <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        Auto-save enabled
                    </span>
                </div>
            </div>
            <div class="p-6 deliverables-gantt-shell"
                style="background: linear-gradient(135deg, rgba(255,255,255,0.02) 0%, rgba(255,255,255,0.01) 100%);">
                <div id="deliverables-gantt-chart" class="w-full" data-update-url-base="{{ $indexRoute }}"></div>
                <script type="application/json" id="deliverables-gantt-tasks">@json($chartTasks)</script>
            </div>
        </div>

        <!-- Deliverables Table -->
        <div class="rounded-2xl overflow-hidden"
            style="background-color: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);">
            <div class="px-6 py-4 border-b"
                style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                <div class="flex items-center justify-between flex-wrap gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center"
                            style="background-color: rgba(255,204,51,0.15);">
                            <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-semibold text-white" style="font-family: var(--font-header1);">
                                Deliverable
                                Records</h2>
                            <p class="text-xs mt-0.5" style="color: rgba(255,255,255,0.5);">All deliverables for
                                this
                                {{ $isDonor ? 'donor' : 'grant' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs px-2 py-1 rounded-full"
                            style="background-color: rgba(255,255,255,0.08); color: rgba(255,255,255,0.6);">{{ $deliverables->total() }}
                            deliverables</span>
                        <a href="{{ $createRoute }}"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200 hover:scale-[1.02]"
                            style="background-color: var(--color-accent1); color: var(--color-primary1);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.5v15m7.5-7.5h-15"></path>
                            </svg>
                            Add Deliverable
                        </a>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.06);">
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider"
                                style="color: rgba(255,255,255,0.5);">Title</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider"
                                style="color: rgba(255,255,255,0.5);">Start Date</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider"
                                style="color: rgba(255,255,255,0.5);">End Date</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider"
                                style="color: rgba(255,255,255,0.5);">Progress</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider"
                                style="color: rgba(255,255,255,0.5);">Status</th>
                            <th class="text-center px-4 py-3 text-xs font-semibold uppercase tracking-wider"
                                style="color: rgba(255,255,255,0.5);">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($deliverables as $index => $deliverable)
                            @php
                                $editRoute = $isDonor
                                    ? route('donors.deliverables.edit', [$owner, $deliverable])
                                    : route('grants.deliverables.edit', [$owner, $deliverable]);
                                $destroyRoute = $isDonor
                                    ? route('donors.deliverables.destroy', [$owner, $deliverable])
                                    : route('grants.deliverables.destroy', [$owner, $deliverable]);

                                $progress = $deliverable->progress ?? 0;
                                if ($progress >= 100) {
                                    $derived = 'Completed';
                                    $statusColor = '#10b981';
                                    $statusBg = 'rgba(16,185,129,0.15)';
                                } elseif (
                                    $deliverable->end_date &&
                                    $deliverable->end_date->isPast() &&
                                    $progress < 100
                                ) {
                                    $derived = 'Overdue';
                                    $statusColor = '#f87171';
                                    $statusBg = 'rgba(248,113,113,0.15)';
                                } elseif ($progress == 0) {
                                    $derived = 'Not Started';
                                    $statusColor = '#6b7280';
                                    $statusBg = 'rgba(107,114,128,0.15)';
                                } else {
                                    $derived = 'In Progress';
                                    $statusColor = '#f59e0b';
                                    $statusBg = 'rgba(245,158,11,0.15)';
                                }
                            @endphp
                            <tr data-deliverable-id="{{ $deliverable->id }}"
                                class="transition-colors hover:bg-white/5 {{ $index % 2 == 0 ? 'bg-white/5' : 'bg-transparent' }}"
                                style="border-bottom: 1px solid rgba(255,255,255,0.04);">
                                <td class="px-4 py-3 text-sm" style="color: white; font-weight: 500;">
                                    <span data-field="title">{{ $deliverable->title }}</span>
                                    <div data-field="description" class="text-xs text-white/40 mt-0.5 font-normal">
                                        {{ $deliverable->description ?? '' }}</div>
                                </td>
                                <td data-field="start" class="px-4 py-3 text-sm"
                                    style="color: rgba(255,255,255,0.7);">
                                    {{ optional($deliverable->start_date)->format('M d, Y') }}
                                </td>
                                <td data-field="end" class="px-4 py-3 text-sm" style="color: rgba(255,255,255,0.7);">
                                    {{ optional($deliverable->end_date)->format('M d, Y') }}
                                </td>
                                <td data-field="progress" class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-24 h-1.5 rounded-full"
                                            style="background-color: rgba(255,255,255,0.1);">
                                            <div class="h-1.5 rounded-full progress-fill-bar"
                                                style="width: {{ min($progress, 100) }}%; background-color: var(--color-accent1);">
                                            </div>
                                        </div>
                                        <span class="text-xs progress-pct"
                                            style="color: white;">{{ $progress }}%</span>
                                    </div>
                                </td>
                                <td data-field="status" class="px-4 py-3">
                                    <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full"
                                        style="background-color: {{ $statusBg }}; color: {{ $statusColor }};">
                                        {{ $derived }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ $editRoute }}"
                                            class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-white/10 transition-all"
                                            title="Edit">
                                            <svg class="w-4 h-4" style="color: rgba(255,255,255,0.5);" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10">
                                                </path>
                                            </svg>
                                        </a>
                                        <button type="button"
                                            class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-red-500/20 transition-all"
                                            title="Delete"
                                            onclick="document.getElementById('deleteDeliverableModal_{{ $deliverable->id }}').showModal()">
                                            <svg class="w-4 h-4" style="color: var(--color-danger);" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0">
                                                </path>
                                            </svg>
                                        </button>

                                        <x-confirm-dialog id="deleteDeliverableModal_{{ $deliverable->id }}"
                                            title="Delete Deliverable"
                                            message="Delete this deliverable? This action cannot be undone."
                                            deleteUrl="{{ $destroyRoute }}" />
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center">
                                    <p style="color: rgba(255,255,255,0.4);">No deliverables yet.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($deliverables->hasPages())
                <div class="px-6 py-4 border-t" style="border-color: rgba(255,255,255,0.08);">
                    <div class="flex justify-center">
                        {{ $deliverables->links('pagination.custom') }}
                    </div>
                </div>
            @endif
        </div>

        <!-- Back Button -->
        <div class="flex justify-end pt-4">
            <a href="{{ $showOwnerRoute }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg font-semibold transition-all duration-300 hover:bg-white/10"
                style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);"
                onmouseover="this.style.backgroundColor='rgba(255,255,255,0.1)'"
                onmouseout="this.style.backgroundColor='transparent'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"></path>
                </svg>
                Back to {{ $isDonor ? 'Donor' : 'Grant' }}
            </a>
        </div>
    </div>

    <!-- Edit Modal -->
    <dialog id="deliverable-edit-modal" class="modal">
        <div class="modal-box max-w-2xl rounded-2xl p-0 overflow-hidden"
            style="background-color: var(--color-primary1); border: 1px solid rgba(255,255,255,0.08);">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b"
                style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center"
                        style="background-color: rgba(255,204,51,0.15);">
                        <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-white" style="font-family: var(--font-header1);">Edit
                            Deliverable</h3>
                        <p class="text-xs mt-0.5" style="color: rgba(255,255,255,0.5);">Update deliverable details
                        </p>
                    </div>
                </div>
            </div>

            <!-- Modal Body -->
            <form id="deliverable-edit-form" class="p-6">
                <input type="hidden" id="deliverable-edit-id" name="id">

                <div class="space-y-5">
                    <!-- Row 1: Title & Progress -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2 text-xs font-semibold uppercase tracking-wider"
                                style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Title</label>
                            <input id="deliverable-edit-title" name="title" type="text" required
                                class="w-full rounded-xl px-4 py-2.5 text-primary1 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-accent1"
                                style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                        </div>
                        <div>
                            <label class="block mb-2 text-xs font-semibold uppercase tracking-wider"
                                style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Progress
                                (%)</label>
                            <input id="deliverable-edit-progress" name="progress" type="number" min="0"
                                max="100"
                                class="w-full rounded-xl px-4 py-2.5 text-primary1 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-accent1"
                                style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                        </div>
                    </div>

                    <!-- Row 2: Start Date & End Date -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2 text-xs font-semibold uppercase tracking-wider"
                                style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Start
                                Date</label>
                            <input id="deliverable-edit-start" name="start_date" type="date" required
                                class="w-full rounded-xl px-4 py-2.5 text-primary1 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-accent1"
                                style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                        </div>
                        <div>
                            <label class="block mb-2 text-xs font-semibold uppercase tracking-wider"
                                style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">End
                                Date</label>
                            <input id="deliverable-edit-end" name="end_date" type="date" required
                                class="w-full rounded-xl px-4 py-2.5 text-primary1 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-accent1"
                                style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                        </div>
                    </div>

                    <!-- Row 3: Description (full width) -->
                    <div>
                        <label class="block mb-2 text-xs font-semibold uppercase tracking-wider"
                            style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Description</label>
                        <textarea id="deliverable-edit-description" name="description" rows="3"
                            class="w-full rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-accent1 resize-y"
                            style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: white;"></textarea>
                    </div>
                </div>

                <!-- Modal Actions -->
                <div class="flex justify-end gap-3 pt-6 mt-4 border-t" style="border-color: rgba(255,255,255,0.08);">
                    <button type="button" id="deliverable-edit-cancel"
                        class="px-6 py-2.5 font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02] text-center inline-flex items-center justify-center gap-2 group"
                        style="font-family: var(--font-body1); background-color: transparent; color: var(--color-danger); border: 1px solid var(--color-danger);"
                        onmouseover="this.style.backgroundColor='rgba(248,113,113,0.1)'; this.style.color='var(--color-danger-dark)'; this.style.borderColor='var(--color-danger-dark)'"
                        onmouseout="this.style.backgroundColor='transparent'; this.style.color='var(--color-danger)'; this.style.borderColor='var(--color-danger)'">
                        <svg class="w-4 h-4 transition-all duration-300 group-hover:rotate-90" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-6 py-2.5 font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02] hover:shadow-lg inline-flex items-center justify-center gap-2 group"
                        style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);"
                        onmouseover="this.style.backgroundColor='var(--color-accent1)'; this.style.filter='brightness(0.95)'"
                        onmouseout="this.style.backgroundColor='var(--color-accent1)'; this.style.filter='brightness(1)'">
                        <svg class="w-4 h-4 transition-all duration-300 group-hover:translate-x-1" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.5v15m7.5-7.5h-15"></path>
                        </svg>
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </dialog>
</x-dashboardlayout>
