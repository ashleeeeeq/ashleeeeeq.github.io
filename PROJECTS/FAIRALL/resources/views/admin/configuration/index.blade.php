<x-dashboardlayout name="{{ $name }}" title="Configuration">
    <div class="mb-8 space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-white" style="font-family: var(--font-header1);">
                    Configuration</h1>
                <p class="text-white/60 text-sm mt-1" style="font-family: var(--font-body1);">Manage reference data used
                    across the system</p>
                <div class="w-16 h-1 mt-2 rounded-full" style="background-color: var(--color-accent1);"></div>
            </div>
        </div>

        @if (session('status'))
            <div class="mb-6 p-4 rounded-xl flex items-center gap-3"
                style="background-color: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3);">
                <svg class="w-5 h-5 shrink-0" style="color: #10b981;" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-sm"
                    style="color: #10b981; font-family: var(--font-body1);">{{ session('status') }}</span>
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-2">
            <!-- Monitoring Configurations -->
            <div class="rounded-xl overflow-hidden lg:col-span-2" style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                <div class="px-4 sm:px-6 py-3 sm:py-4 border-b"
                    style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                            style="background-color: rgba(255,204,51,0.15);">
                            <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008Z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-white" style="font-family: var(--font-header1);">Monitoring
                                Configurations</h3>
                            <p class="text-white/50 text-xs" style="font-family: var(--font-body1);">Only threshold
                                values can be edited</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.06);">
                                    <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider"
                                        style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Name</th>
                                    <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider"
                                        style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Threshold
                                    </th>
                                    <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider"
                                        style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Status
                                    </th>
                                    <th class="text-center px-4 py-3 text-xs font-semibold uppercase tracking-wider"
                                        style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($monitoringConfigurations as $index => $monitoringConfiguration)
                                    <tr class="transition-colors hover:bg-white/5 {{ $index % 2 == 0 ? 'bg-white/5' : 'bg-transparent' }}"
                                        style="border-bottom: 1px solid rgba(255,255,255,0.04);">
                                        <td class="px-4 py-3">
                                            <span
                                                class="text-sm text-white">{{ Str::of($monitoringConfiguration->name)->replace('_', ' ')->title() }}</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <form id="monitoring-config-form-{{ $monitoringConfiguration->id }}"
                                                method="POST"
                                                action="{{ route('configuration.monitoring-configurations.update', $monitoringConfiguration->id) }}">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="is_active" value="0">
                                                <input type="number" name="threshold" min="0" step="1"
                                                    value="{{ $monitoringConfiguration->threshold }}"
                                                    class="w-full max-w-35 px-3 py-2 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1/50 focus:border-accent1 transition-all duration-200 hover:border-accent1"
                                                    style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: var(--color-primary1);">
                                            </form>
                                        </td>
                                        <td class="px-4 py-3">
                                            <label class="inline-flex cursor-pointer">
                                                <input type="checkbox"
                                                    form="monitoring-config-form-{{ $monitoringConfiguration->id }}"
                                                    name="is_active" value="1" class="sr-only peer"
                                                    {{ $monitoringConfiguration->is_active ? 'checked' : '' }}
                                                    onchange="document.getElementById('monitoring-status-text-{{ $monitoringConfiguration->id }}').textContent = this.checked ? 'Active' : 'Inactive';">
                                                <div
                                                    class="w-11 h-6 bg-gray-200 rounded-full peer-focus:ring-2 peer-focus:ring-yellow-400/50 peer-checked:bg-green-500">
                                                </div>
                                                <span id="monitoring-status-text-{{ $monitoringConfiguration->id }}"
                                                    class="ml-3 text-sm"
                                                    style="color: rgba(255,255,255,0.75);">{{ $monitoringConfiguration->is_active ? 'Active' : 'Inactive' }}</span>
                                            </label>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <button type="submit"
                                                form="monitoring-config-form-{{ $monitoringConfiguration->id }}"
                                                class="px-4 py-2 font-semibold rounded-lg transition-all duration-300 hover:scale-[1.02]"
                                                style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);">
                                                Save
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-12 text-center">
                                            <p class="text-sm" style="color: rgba(255,255,255,0.4);">No monitoring
                                                configurations found</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Beneficiary Status Types -->
            <div class="rounded-xl overflow-hidden" style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                <div class="px-4 sm:px-6 py-3 sm:py-4 border-b"
                    style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                            style="background-color: rgba(255,204,51,0.15);">
                            <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-white" style="font-family: var(--font-header1);">Beneficiary
                                Status Types</h3>
                            <p class="text-white/50 text-xs" style="font-family: var(--font-body1);">Used in
                                beneficiary
                                status history</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <form method="POST" action="/configuration/status-types"
                        class="flex flex-col md:flex-row gap-3 mb-6">
                        @csrf
                        <div class="flex flex-wrap gap-3 items-center">
                            @foreach ($programs as $program)
                                <label class="flex items-center gap-1.5 text-sm text-white/70 cursor-pointer">
                                    <input type="checkbox" name="program_ids[]" value="{{ $program->id }}"
                                        class="rounded border-gray-300 text-yellow-400 focus:ring-yellow-400/50"
                                        @checked(in_array($program->id, old('program_ids', [])))>
                                    {{ $program->program_name }}
                                </label>
                            @endforeach
                        </div>

                        <input name="status_name" value="{{ old('status_name') }}"
                            class="flex-1 px-4 py-2.5 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1/50 focus:border-accent1 transition-all duration-200 hover:border-accent1"
                            style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15); color: var(--color-primary1);"
                            placeholder="Add a status type" required>
                        <button type="submit"
                            class="px-4 py-2.5 font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02]"
                            style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);">
                            Add
                        </button>
                    </form>
                    <x-forms.errors name="status_name" />

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.06);">
                                    <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider"
                                        style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Name</th>
                                    <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider"
                                        style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Program/s
                                    </th>
                                    <th class="text-center px-4 py-3 text-xs font-semibold uppercase tracking-wider"
                                        style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($statusTypes as $index => $statusType)
                                    <tr class="transition-colors hover:bg-white/5 {{ $index % 2 == 0 ? 'bg-white/5' : 'bg-transparent' }}"
                                        style="border-bottom: 1px solid rgba(255,255,255,0.04);">
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-3">
                                                <span class="text-sm text-white">{{ $statusType->status_name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span
                                                class="text-sm text-white/70">{{ $statusType->program_names ?: 'Unassigned' }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <button type="button"
                                                    onclick="openEditModal(@js($statusType->id), @js($statusType->status_name), 'status', @js($statusType->program_id_list))"
                                                    class="p-1.5 rounded-lg transition-all hover:bg-white/10"
                                                    title="Edit">
                                                    <svg class="w-4 h-4" style="color: rgba(255,255,255,0.6);"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10">
                                                        </path>
                                                    </svg>
                                                </button>
                                                <button type="button"
                                                    class="p-1.5 rounded-lg transition-all hover:bg-red-500/20"
                                                    title="Delete"
                                                    onclick="document.getElementById('deleteStatusTypeModal_{{ $statusType->id }}').showModal()">
                                                    <svg class="w-4 h-4" style="color: #f87171;" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0">
                                                        </path>
                                                    </svg>
                                                </button>

                                                <x-confirm-dialog id="deleteStatusTypeModal_{{ $statusType->id }}"
                                                    title="Delete Status Type"
                                                    message="Are you sure you want to delete <strong>{{ $statusType->status_name }}</strong>? This record will be moved to the archive and automatically deleted after 90 days."
                                                    deleteUrl="/configuration/status-types/{{ $statusType->id }}" />
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-4 py-12 text-center">
                                            <svg class="w-12 h-12 mx-auto mb-3" style="color: rgba(255,255,255,0.3);"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                </path>
                                            </svg>
                                            <p class="text-sm" style="color: rgba(255,255,255,0.4);">No status types
                                                found</p>
                                    <tr>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- FFA Assessment Categories -->
            <div class="rounded-xl overflow-hidden" style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                <div class="px-4 sm:px-6 py-3 sm:py-4 border-b"
                    style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                            style="background-color: rgba(255,204,51,0.15);">
                            <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-white" style="font-family: var(--font-header1);">FFA
                                Assessment Categories</h3>
                            <p class="text-white/50 text-xs" style="font-family: var(--font-body1);">Used in FFA
                                assessment records</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <form method="POST" action="/configuration/assessment-categories" class="flex gap-3 mb-6">
                        @csrf
                        <input name="assessment_name" value="{{ old('assessment_name') }}"
                            class="flex-1 px-4 py-2.5 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1/50 focus:border-accent1 transition-all duration-200 hover:border-accent1"
                            style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15); color: var(--color-primary1);"
                            placeholder="Add a category" required>
                        <button type="submit"
                            class="px-4 py-2.5 font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02]"
                            style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);">
                            Add
                        </button>
                    </form>
                    <x-forms.errors name="assessment_name" />

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.06);">
                                    <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider"
                                        style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Name</th>
                                    <th class="text-center px-4 py-3 text-xs font-semibold uppercase tracking-wider"
                                        style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($assessmentCategories as $index => $assessmentCategory)
                                    <tr class="transition-colors hover:bg-white/5 {{ $index % 2 == 0 ? 'bg-white/5' : 'bg-transparent' }}"
                                        style="border-bottom: 1px solid rgba(255,255,255,0.04);">
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-3">
                                                <span
                                                    class="text-sm text-white">{{ $assessmentCategory->assessment_name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <button type="button"
                                                    onclick="openEditModal(@js($assessmentCategory->id), @js($assessmentCategory->assessment_name), 'assessment')"
                                                    class="p-1.5 rounded-lg transition-all hover:bg-white/10"
                                                    title="Edit">
                                                    <svg class="w-4 h-4" style="color: rgba(255,255,255,0.6);"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10">
                                                        </path>
                                                    </svg>
                                                </button>
                                                <button type="button"
                                                    class="p-1.5 rounded-lg transition-all hover:bg-red-500/20"
                                                    title="Delete"
                                                    onclick="document.getElementById('deleteAssessmentCategoryModal_{{ $assessmentCategory->id }}').showModal()">
                                                    <svg class="w-4 h-4" style="color: #f87171;" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0">
                                                        </path>
                                                    </svg>
                                                </button>

                                                <x-confirm-dialog
                                                    id="deleteAssessmentCategoryModal_{{ $assessmentCategory->id }}"
                                                    title="Delete Assessment Category"
                                                    message="Are you sure you want to delete <strong>{{ $assessmentCategory->assessment_name }}</strong>? This record will be moved to the archive and automatically deleted after 90 days."
                                                    deleteUrl="/configuration/assessment-categories/{{ $assessmentCategory->id }}" />
                                            </div>
                                    </tr>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="px-4 py-12 text-center">
                                            <svg class="w-12 h-12 mx-auto mb-3" style="color: rgba(255,255,255,0.3);"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                                </path>
                                            </svg>
                                            <p class="text-sm" style="color: rgba(255,255,255,0.4);">No assessment
                                                categories found</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-2 mt-6">
            <!-- Sport Types -->
            <div class="rounded-xl overflow-hidden" style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                <div class="px-4 sm:px-6 py-3 sm:py-4 border-b"
                    style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                            style="background-color: rgba(255,204,51,0.15);">
                            <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 0 1-.982-3.172M9.497 14.25a7.454 7.454 0 0 0 .981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 0 0 7.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 0 0 2.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 0 1 2.916.52 6.003 6.003 0 0 1-5.395 4.972m0 0a6.726 6.726 0 0 1-2.749 1.35m0 0a6.772 6.772 0 0 1-3.044 0"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-white" style="font-family: var(--font-header1);">Sport Types
                            </h3>
                            <p class="text-white/50 text-xs" style="font-family: var(--font-body1);">Used in
                                sport-related activities</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('configuration.sport-types.store') }}"
                        class="flex gap-3 mb-6">
                        @csrf
                        <input name="name" value="{{ old('name') }}"
                            class="flex-1 px-4 py-2.5 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1/50 focus:border-accent1 transition-all duration-200 hover:border-accent1"
                            style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15); color: var(--color-primary1);"
                            placeholder="Add a sport type" required>
                        <button type="submit"
                            class="px-4 py-2.5 font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02]"
                            style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);">
                            Add
                        </button>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.06);">
                                    <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider"
                                        style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Name</th>
                                    <th class="text-center px-4 py-3 text-xs font-semibold uppercase tracking-wider"
                                        style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($sportTypes as $index => $sportType)
                                    <tr class="transition-colors hover:bg-white/5 {{ $index % 2 == 0 ? 'bg-white/5' : 'bg-transparent' }}"
                                        style="border-bottom: 1px solid rgba(255,255,255,0.04);">
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-3">
                                                <span class="text-sm text-white">{{ $sportType->name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <button type="button"
                                                    onclick="openEditModal('{{ $sportType->id }}', '{{ $sportType->name }}', 'sport')"
                                                    class="p-1.5 rounded-lg transition-all hover:bg-white/10"
                                                    title="Edit">
                                                    <svg class="w-4 h-4" style="color: rgba(255,255,255,0.6);"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10">
                                                        </path>
                                                    </svg>
                                                </button>
                                                <button type="button"
                                                    class="px-3 py-2 font-semibold rounded-lg transition-all duration-300 hover:bg-red-500/20"
                                                    style="color: #f87171;"
                                                    onclick="document.getElementById('deleteSportTypeModal_{{ $sportType->id }}').showModal()">
                                                    <svg class="w-4 h-4" style="color: #f87171;" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0">
                                                        </path>
                                                    </svg>
                                                </button>

                                                <x-confirm-dialog id="deleteSportTypeModal_{{ $sportType->id }}"
                                                    title="Delete Sport Type"
                                                    message="Are you sure you want to delete <strong>{{ $sportType->name }}</strong>? This record will be moved to the archive and automatically deleted after 90 days."
                                                    deleteUrl="{{ route('configuration.sport-types.destroy', $sportType->id) }}" />
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="px-4 py-12 text-center">
                                            <p class="text-sm" style="color: rgba(255,255,255,0.4);">No sport types
                                                found</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Activity Types -->
            <div class="rounded-xl overflow-hidden" style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                <div class="px-4 sm:px-6 py-3 sm:py-4 border-b"
                    style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                            style="background-color: rgba(255,204,51,0.15);">
                            <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0M12 12.75h.008v.008H12v-.008Z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-white" style="font-family: var(--font-header1);">Activity
                                Types</h3>
                            <p class="text-white/50 text-xs" style="font-family: var(--font-body1);">Used when
                                creating activities</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('configuration.activity-types.store') }}"
                        class="grid gap-3 mb-6">
                        @csrf
                        <input name="name" value="{{ old('name') }}"
                            class="w-full px-4 py-2.5 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1/50 focus:border-accent1 transition-all duration-200 hover:border-accent1"
                            style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15); color: var(--color-primary1);"
                            placeholder="Add an activity type" required>
                        <select name="program_id"
                            class="w-full px-4 py-2.5 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1/50 focus:border-accent1 transition-all duration-200 hover:border-accent1"
                            style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15); color: white;">
                            <option value="" style="color: var(--color-primary1);">Unassigned program</option>
                            @foreach ($programs as $program)
                                <option value="{{ $program->id }}" style="color: var(--color-primary1);">
                                    {{ $program->program_name }}</option>
                            @endforeach
                        </select>
                        <button type="submit"
                            class="px-4 py-2.5 font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02] justify-self-start"
                            style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);">
                            Add
                        </button>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.06);">
                                    <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider"
                                        style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Details
                                    </th>
                                    <th class="text-center px-4 py-3 text-xs font-semibold uppercase tracking-wider"
                                        style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($activityTypes as $index => $activityType)
                                    <tr class="transition-colors hover:bg-white/5 {{ $index % 2 == 0 ? 'bg-white/5' : 'bg-transparent' }}"
                                        style="border-bottom: 1px solid rgba(255,255,255,0.04);">
                                        <td class="px-4 py-3">
                                            <div class="flex flex-col">
                                                <span class="text-sm text-white">{{ $activityType->name }}</span>
                                                <span
                                                    class="text-xs text-white/50">{{ $activityType->program_name ?? 'Unassigned program' }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <button type="button"
                                                    onclick="openEditModal('{{ $activityType->id }}', '{{ $activityType->name }}', 'activity', '{{ $activityType->program_id ?? '' }}')"
                                                    class="p-1.5 rounded-lg transition-all hover:bg-white/10"
                                                    title="Edit">
                                                    <svg class="w-4 h-4" style="color: rgba(255,255,255,0.6);"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10">
                                                        </path>
                                                    </svg>
                                                </button>
                                                <button type="button"
                                                    class="px-3 py-2 font-semibold rounded-lg transition-all duration-300 hover:bg-red-500/20"
                                                    style="color: #f87171;"
                                                    onclick="document.getElementById('deleteActivityTypeModal_{{ $activityType->id }}').showModal()">
                                                    <svg class="w-4 h-4" style="color: #f87171;" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0">
                                                        </path>
                                                    </svg>
                                                </button>

                                                <x-confirm-dialog id="deleteActivityTypeModal_{{ $activityType->id }}"
                                                    title="Delete Activity Type"
                                                    message="Are you sure you want to delete <strong>{{ $activityType->name }}</strong>? This record will be moved to the archive and automatically deleted after 90 days."
                                                    deleteUrl="{{ route('configuration.activity-types.destroy', $activityType->id) }}" />
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="px-4 py-12 text-center">
                                            <p class="text-sm" style="color: rgba(255,255,255,0.4);">No activity types
                                                found</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Event Types -->
            <div class="rounded-xl overflow-hidden" style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                <div class="px-4 sm:px-6 py-3 sm:py-4 border-b"
                    style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                            style="background-color: rgba(255,204,51,0.15);">
                            <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-white" style="font-family: var(--font-header1);">Event Types
                            </h3>
                            <p class="text-white/50 text-xs" style="font-family: var(--font-body1);">Used in event
                                records</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('configuration.event-types.store') }}"
                        class="flex gap-3 mb-6">
                        @csrf
                        <input name="name" value="{{ old('name') }}"
                            class="flex-1 px-4 py-2.5 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1/50 focus:border-accent1 transition-all duration-200 hover:border-accent1"
                            style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15); color: var(--color-primary1);"
                            placeholder="Add an event type" required>
                        <button type="submit"
                            class="px-4 py-2.5 font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02]"
                            style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);">
                            Add
                        </button>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.06);">
                                    <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider"
                                        style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Name</th>
                                    <th class="text-center px-4 py-3 text-xs font-semibold uppercase tracking-wider"
                                        style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($eventTypes as $index => $eventType)
                                    <tr class="transition-colors hover:bg-white/5 {{ $index % 2 == 0 ? 'bg-white/5' : 'bg-transparent' }}"
                                        style="border-bottom: 1px solid rgba(255,255,255,0.04);">
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-3">
                                                <span class="text-sm text-white">{{ $eventType->name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <button type="button"
                                                    onclick="openEditModal('{{ $eventType->id }}', '{{ $eventType->name }}', 'event')"
                                                    class="p-1.5 rounded-lg transition-all hover:bg-white/10"
                                                    title="Edit">
                                                    <svg class="w-4 h-4" style="color: rgba(255,255,255,0.6);"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10">
                                                        </path>
                                                    </svg>
                                                </button>
                                                <button type="button"
                                                    class="px-3 py-2 font-semibold rounded-lg transition-all duration-300 hover:bg-red-500/20"
                                                    style="color: #f87171;"
                                                    onclick="document.getElementById('deleteEventTypeModal_{{ $eventType->id }}').showModal()">
                                                    <svg class="w-4 h-4" style="color: #f87171;" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0">
                                                        </path>
                                                    </svg>
                                                </button>

                                                <x-confirm-dialog id="deleteEventTypeModal_{{ $eventType->id }}"
                                                    title="Delete Event Type"
                                                    message="Are you sure you want to delete <strong>{{ $eventType->name }}</strong>? This record will be moved to the archive and automatically deleted after 90 days."
                                                    deleteUrl="{{ route('configuration.event-types.destroy', $eventType->id) }}" />
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="px-4 py-12 text-center">
                                            <p class="text-sm" style="color: rgba(255,255,255,0.4);">No event types
                                                found</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal - Hidden by default -->
    <div id="editModal" class="fixed inset-0 z-50 hidden items-center justify-center"
        style="background-color: rgba(0,0,0,0.5);">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6 border-3"
            style="border-color: var(--color-accent1);">
            <h3 class="text-xl font-bold mb-4" style="color: var(--color-primary1);">Edit Item</h3>
            <form id="editForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div id="editModalBody">
                    <input type="text" id="editInput" name="field_name"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-yellow-400/50"
                        style="color: var(--color-primary1);" required>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeModal()"
                        class="px-4 py-2 font-semibold rounded-lg transition-all duration-300 hover:scale-[1.02] text-center"
                        style="background-color: transparent; color: var(--color-danger); border: 1px solid var(--color-danger);"
                        onmouseover="this.style.backgroundColor='var(--color-danger-light)'; this.style.color='var(--color-danger-dark)'; this.style.borderColor='var(--color-danger-dark)'"
                        onmouseout="this.style.backgroundColor='transparent'; this.style.color='var(--color-danger)'; this.style.borderColor='var(--color-danger)'">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 font-semibold rounded-lg transition-all duration-300 hover:scale-[1.02] hover:shadow-lg"
                        style="background-color: var(--color-accent1); color: var(--color-primary1);">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
        /* Ensure modal form text is black for readability */
        #editModal input,
        #editModal select,
        #editModal textarea,
        #editModal .text-sm {
            color: #000 !important;
        }
    </style>

    <script>
        const programs = @json($programs ?? []);

        function openEditModal(id, name, type, extra = '') {
            const modal = document.getElementById('editModal');
            const form = document.getElementById('editForm');
            const body = document.getElementById('editModalBody');

            // reset body
            body.innerHTML = '';

            if (type === 'status') {
                form.action = '/configuration/status-types/' + id;
                const selectedIds = Array.isArray(extra) ? extra.map(String) : [];
                let programCheckboxes = '';

                for (const p of programs) {
                    const checked = selectedIds.includes(String(p.id)) ? 'checked' : '';
                    programCheckboxes += `
                        <label class="flex items-center gap-1.5 text-sm cursor-pointer mb-1" style="color: var(--color-primary1);">
                            <input type="checkbox" name="program_ids[]" value="${p.id}" ${checked}
                                class="rounded border-gray-300 text-yellow-400 focus:ring-yellow-400/50">
                            ${p.program_name}
                        </label>
                    `;
                }

                body.innerHTML = `
                    <input type="text" id="editInput" name="status_name" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-yellow-400/50 mb-3" value="${name}" required>
                    <div class="mb-2 text-sm font-semibold" style="color: var(--color-primary1);">Programs</div>
                    ${programCheckboxes}
                `;
            } else if (type === 'assessment' || type === 'category') {
                form.action = '/configuration/assessment-categories/' + id;
                body.innerHTML =
                    `<input type="text" id="editInput" name="assessment_name" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-yellow-400/50" value="${name}" required>`;
            } else if (type === 'sport') {
                form.action = '/configuration/sport-types/' + id;
                body.innerHTML =
                    `<input type="text" id="editInput" name="name" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-yellow-400/50" value="${name}" required>`;
            } else if (type === 'event') {
                form.action = '/configuration/event-types/' + id;
                body.innerHTML =
                    `<input type="text" id="editInput" name="name" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-yellow-400/50" value="${name}" required>`;
            } else if (type === 'activity') {
                form.action = '/configuration/activity-types/' + id;
                let programOptions = '<option value="">Unassigned program</option>';
                for (const p of programs) {
                    const selected = ('' + p.id) === ('' + extra) ? 'selected' : '';
                    programOptions += `<option value="${p.id}" ${selected}>${p.program_name}</option>`;
                }
                body.innerHTML = `
                    <input type="text" id="editInput" name="name" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-yellow-400/50 mb-3" value="${name}" required>
                    <select name="program_id" id="editProgramSelect" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-yellow-400/50">${programOptions}</select>
                `;
            } else if (type === 'monitoring') {
                form.action = '/configuration/monitoring-configurations/' + id;
                const isActive = extra === '1' || extra === 1 || extra === true;
                body.innerHTML = `
                    <div class="mb-3">
                        <label class="text-sm">Name</label>
                        <div class="text-sm font-semibold">${name}</div>
                    </div>
                    <div class="flex items-center gap-3">
                        <input type="hidden" name="is_active" value="0">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" id="editIsActive" class="sr-only peer" ${isActive ? 'checked' : ''}>
                            <div class="w-11 h-6 bg-gray-200 rounded-full peer-focus:ring-2 peer-focus:ring-yellow-400/50 peer-checked:bg-green-500"></div>
                            <span class="ml-3 text-sm">Active</span>
                        </label>
                    </div>
                `;
            }

            // show modal and focus
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            const focusEl = document.getElementById('editInput') || document.getElementById('editIsActive') || document
                .getElementById('editProgramSelect');
            if (focusEl) focusEl.focus();
        }

        window.openEditModal = openEditModal;

        function closeModal() {
            const modal = document.getElementById('editModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        window.closeModal = closeModal;

        // Close modal when clicking outside
        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>
</x-dashboardlayout>
