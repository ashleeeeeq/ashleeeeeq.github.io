@php
    $isDonor = $ownerType === 'donor';
    $ownerName = $isDonor ? $owner->display_name : $owner->grant_name;
    $backRoute = $isDonor
        ? route('donors.show', ['donor' => $owner, 'tab' => 'deliverables'])
        : route('grants.show', ['grant' => $owner, 'tab' => 'deliverables']);
    $updateRoute = $isDonor
        ? route('donors.deliverables.update', [$owner, $deliverable])
        : route('grants.deliverables.update', [$owner, $deliverable]);
@endphp

<x-dashboardlayout name="{{ $name }}" title="Edit Deliverable">
    <div class="mb-8 space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <a href="{{ $backRoute }}" class="text-sm text-white/60 hover:text-white transition-colors inline-flex items-center gap-1 mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to {{ $isDonor ? 'donor' : 'grant' }}
                </a>
                <h1 class="text-2xl md:text-3xl font-bold tracking-tight" style="font-family: var(--font-header1); color: var(--color-white);">Edit Deliverable</h1>
                <p class="text-sm mt-1" style="color: rgba(255,255,255,0.6); font-family: var(--font-body1);">Update deliverable for {{ $ownerName }}</p>
                <div class="w-20 h-1 mt-2 rounded-full" style="background: var(--color-accent1);"></div>
            </div>
        </div>

        <form method="POST" action="{{ $updateRoute }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="rounded-2xl overflow-hidden" style="background-color: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);">
                <div class="px-6 py-4 border-b" style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center" style="background-color: rgba(255,204,51,0.15);">
                            <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-semibold text-white" style="font-family: var(--font-header1);">Deliverable Information</h2>
                            <p class="text-xs mt-0.5" style="color: rgba(255,255,255,0.5);">Update the deliverable details below</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 space-y-5">
                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label class="block mb-2 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Title <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                            <input type="text" name="title" value="{{ old('title', $deliverable->title ) }}" 
                                   class="w-full rounded-xl px-4 py-2.5 text-primary1 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-accent1"
                                   style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);"
                                   placeholder="Enter deliverable title" required>
                        </div>
                        <div>
                            <label class="block mb-2 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Progress (%) <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                            <input type="number" name="progress" min="0" max="100" value="{{ old('progress', $deliverable->progress ?? 0) }}" 
                                   class="w-full rounded-xl px-4 py-2.5 text-primary1 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-accent1"
                                   style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);"
                                   placeholder="0" required>
                        </div>
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label class="block mb-2 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Start Date <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                            <input type="date" name="start_date" value="{{ old('start_date', optional($deliverable->start_date)->format('Y-m-d')) }}" 
                                   class="w-full rounded-xl px-4 py-2.5 text-primary1 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-accent1"
                                   style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);" required>
                        </div>
                        <div>
                            <label class="block mb-2 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">End Date <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label></label>
                            <input type="date" name="end_date" value="{{ old('end_date', optional($deliverable->end_date)->format('Y-m-d')) }}" 
                                   class="w-full rounded-xl px-4 py-2.5 text-primary1 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-accent1"
                                   style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);" required>
                        </div>
                    </div>

                    <div>
                        <label class="block mb-2 text-xs font-semibold uppercase tracking-wider" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Description</label>
                        <textarea name="description" rows="4" 
                                  class="w-full rounded-xl px-4 py-2.5 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-accent1 resize-y"
                                  style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: white;"
                                  placeholder="Enter deliverable description...">{{ old('description', $deliverable->description) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 pt-4">
                <a href="{{ $backRoute }}" 
                   class="px-6 py-2.5 font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02] text-center inline-flex items-center justify-center gap-2 group"
                   style="font-family: var(--font-body1); background-color: transparent; color: var(--color-danger); border: 1px solid var(--color-danger);"
                   onmouseover="this.style.backgroundColor='rgba(248,113,113,0.1)'; this.style.color='var(--color-danger-dark)'; this.style.borderColor='var(--color-danger-dark)'"
                   onmouseout="this.style.backgroundColor='transparent'; this.style.color='var(--color-danger)'; this.style.borderColor='var(--color-danger)'">
                    <svg class="w-4 h-4 transition-all duration-300 group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2.5 font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02] hover:shadow-lg inline-flex items-center justify-center gap-2 group"
                        style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);"
                        onmouseover="this.style.backgroundColor='var(--color-accent1)'; this.style.filter='brightness(0.95)'"
                        onmouseout="this.style.backgroundColor='var(--color-accent1)'; this.style.filter='brightness(1)'">
                    <svg class="w-4 h-4 transition-all duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"></path>
                    </svg>
                    Save Deliverable
                </button>
            </div>
        </form>
    </div>
</x-dashboardlayout>