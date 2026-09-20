<x-dashboardlayout title="Allocation #{{ $allocation->id }}">
    <div class="mb-8 space-y-6 space-y-6">
        <!-- Header Section -->
        <div class="flex justify-between items-start gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold tracking-tight"
                    style="font-family: var(--font-header1); color: var(--color-white);">Allocation
                    #{{ $allocation->id }}
                </h1>
                <p class="text-sm mt-1" style="color: rgba(255,255,255,0.6); font-family: var(--font-body1);">View
                    allocation
                    details and information</p>
                <div class="w-20 h-1 mt-2 rounded-full" style="background: var(--color-accent1);"></div>
            </div>
            <div class="flex items-center gap-2 sm:gap-3 pt-1 shrink-0">
                <a href="{{ route('funding.allocations.edit', $allocation) }}"
                    class="inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-5 py-2 sm:py-2.5 text-sm sm:text-base font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02]"
                    style="font-family: var(--font-body1); background-color: transparent; color: var(--color-info); border: 1px solid var(--color-info);"
                    onmouseover="this.style.backgroundColor='rgba(59,130,246,0.1)'; this.style.color='var(--color-info-dark)'"
                    onmouseout="this.style.backgroundColor='transparent'; this.style.color='var(--color-info)'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"></path>
                    </svg>
                    Edit
                </a>
                <button type="button"
                    class="inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-5 py-2 sm:py-2.5 text-sm sm:text-base font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02]"
                    style="font-family: var(--font-body1); background-color: transparent; color: var(--color-danger); border: 1px solid var(--color-danger);"
                    onmouseover="this.style.backgroundColor='rgba(248,113,113,0.15)'; this.style.color='var(--color-danger-dark)'; this.style.borderColor='var(--color-danger-dark)'"
                    onmouseout="this.style.backgroundColor='transparent'; this.style.color='var(--color-danger)'; this.style.borderColor='var(--color-danger)'"
                    onclick="document.getElementById('deleteAllocationModal').showModal()">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"></path>
                    </svg>
                    Delete
                </button>

                <x-confirm-dialog id="deleteAllocationModal" title="Delete Allocation"
                    message="Are you sure you want to delete allocation #{{ $allocation->id }}? This action cannot be undone."
                    deleteUrl="{{ route('funding.allocations.destroy', $allocation) }}" />
            </div>
        </div>


        <!-- Allocation Details Card -->
        <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-[0_8px_30px_rgba(0,0,0,0.12)]"
            style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
            <div class="px-6 py-4 border-b"
                style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center"
                        style="background-color: rgba(255,204,51,0.15);">
                        <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25M9 16.5v.75m3-3v3M15 12v5.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-semibold text-white" style="font-family: var(--font-header1);">Allocation
                            Details</h2>
                        <p class="text-xs mt-0.5" style="color: rgba(255,255,255,0.5);">Complete information about this
                            allocation</p>
                    </div>
                </div>
            </div>


            <div class="p-6">
                <div class="grid gap-6 md:grid-cols-2">
                    <!-- Left Column -->
                    <div class="space-y-4">
                        <div class="pb-3 border-b" style="border-color: rgba(255,255,255,0.06);">
                            <dt class="text-xs font-semibold uppercase tracking-wider"
                                style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Source</dt>
                            <dd class="mt-1 text-white font-medium" style="font-family: var(--font-body1);">
                                {{ $allocation->donation ? ($allocation->donation->reference_number ?? 'Donation #' . $allocation->donation->id) : optional($allocation->grant)->grant_name ?? 'Grant #' . $allocation->grant->id }}
                            </dd>
                        </div>


                        <div class="pb-3 border-b" style="border-color: rgba(255,255,255,0.06);">
                            <dt class="text-xs font-semibold uppercase tracking-wider"
                                style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Beneficiary</dt>
                            <dd class="mt-1 text-white font-medium" style="font-family: var(--font-body1);">
                                {{ $allocation->beneficiary?->display_name ?? '-' }}</dd>
                        </div>
                    </div>


                    <!-- Right Column -->
                    <div class="space-y-4">
                        <div class="pb-3 border-b" style="border-color: rgba(255,255,255,0.06);">
                            <dt class="text-xs font-semibold uppercase tracking-wider"
                                style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Amount</dt>
                            <dd class="mt-1 text-xl font-bold text-white" style="font-family: var(--font-header1);">
                                ₱{{ number_format($allocation->amount_cents / 100, 2) }}</dd>
                        </div>


                        <div class="pb-3 border-b" style="border-color: rgba(255,255,255,0.06);">
                            <dt class="text-xs font-semibold uppercase tracking-wider"
                                style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Date Allocated
                            </dt>
                            <dd class="mt-1 text-white font-medium" style="font-family: var(--font-body1);">
                                {{ optional($allocation->date_allocated)->format('F j, Y') ?? '-' }}</dd>
                        </div>
                    </div>
                </div>


                <!-- Notes Section -->
                <div class="mt-6 pt-4 border-t" style="border-color: rgba(255,255,255,0.06);">
                    <dt class="text-xs font-semibold uppercase tracking-wider"
                        style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Notes</dt>
                    <dd class="mt-2 text-white leading-relaxed"
                        style="font-family: var(--font-body1); line-height: 1.6;">{{ $allocation->notes ?: '-' }}</dd>
                </div>
            </div>
        </div>

        <!-- Back Button -->
        <div class="flex justify-end pt-4">
            <a href="{{ route('funding.allocations.index') }}"
                class="inline-flex items-center gap-2 px-4 sm:px-5 py-2 sm:py-2.5 rounded-lg font-semibold transition-all duration-300 hover:bg-white/10 text-sm sm:text-base"
                style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);"
                onmouseover="this.style.backgroundColor='rgba(255,255,255,0.1)'"
                onmouseout="this.style.backgroundColor='transparent'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"></path>
                </svg>
                Back to Allocations
            </a>
        </div>
    </div>
</x-dashboardlayout>
