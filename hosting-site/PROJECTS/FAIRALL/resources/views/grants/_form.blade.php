@php
    $g = $grant ?? null;
@endphp

<div class="mb-8 space-y-6">

    <!-- Grant Details Section -->
    <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-[0_8px_30px_rgba(0,0,0,0.12)]"
        style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
        <div class="px-4 sm:px-6 py-3 sm:py-4 border-b"
            style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
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
                    <h3 class="font-semibold text-white text-sm sm:text-base" style="font-family: var(--font-header1);">
                        Grant Information</h3>
                    <p class="text-xs mt-0.5" style="color: rgba(255,255,255,0.5);">Enter the grant details and funding
                        information</p>
                </div>
            </div>
        </div>

        <div class="p-4 sm:p-6 space-y-5">
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block mb-2 text-sm font-semibold"
                        style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Organization Name <span
                            class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                    <input type="text" name="organization_name"
                        value="{{ old('organization_name', $g?->organization_name ?? '') }}"
                        class="w-full px-4 py-2.5 text-primary1 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 focus:border-accent1 transition-all duration-200 hover:border-accent1"
                        style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15);"
                        placeholder="Enter organization name" required>
                    <x-forms.errors name="organization_name" />
                </div>
                <div>
                    <label class="block mb-2 text-sm font-semibold"
                        style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Email <span
                            class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $g?->email ?? '') }}"
                        class="w-full px-4 py-2.5 text-primary1 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 focus:border-accent1 transition-all duration-200 hover:border-accent1"
                        style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15);"
                        placeholder="organization@example.com" required>
                    <x-forms.errors name="email" />
                </div>
                <div>
                    <label class="block mb-2 text-xs font-semibold uppercase tracking-wider"
                        style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Contact Number
                        <span class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                    <x-forms.phone-input name="contact_number"
                        value="{{ old('contact_number', $g?->contact_number ?? '') }}" />
                </div>
                <div>
                    <label class="block mb-2 text-sm font-semibold"
                        style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Grant Name <span
                            class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                    <input type="text" name="grant_name" value="{{ old('grant_name', $g?->grant_name ?? '') }}"
                        class="w-full px-4 py-2.5 text-primary1 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 focus:border-accent1 transition-all duration-200 hover:border-accent1"
                        style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15);"
                        placeholder="Enter grant name" required>
                    <x-forms.errors name="grant_name" />
                </div>
                <div>
                    <label class="block mb-2 text-sm font-semibold"
                        style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Total Amount <span
                            class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-primary1">₱</span>
                        <input type="number" step="0.01" name="total_amount"
                            value="{{ old('total_amount', $g?->total_amount ?? '') }}"
                            class="w-full pl-7 pr-4 py-2.5 text-primary1 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 focus:border-accent1 transition-all duration-200 hover:border-accent1"
                            style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15);"
                            placeholder="0.00" required>
                    </div>
                    <x-forms.errors name="total_amount" />
                </div>
                <div>
                    <label class="block mb-2 text-sm font-semibold"
                        style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Start Date <span
                            class="text-xs font-normal" style="color: var(--color-danger);">*</span></label>
                    <input type="date" name="start_date"
                        value="{{ old('start_date', optional($g?->start_date)->format('Y-m-d')) }}"
                        class="w-full px-4 py-2.5 text-primary1 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 focus:border-accent1 transition-all duration-200 hover:border-accent1"
                        style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15);"
                        required>
                    <x-forms.errors name="start_date" />
                </div>
                <div>
                    <label class="block mb-2 text-sm font-semibold"
                        style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">End Date</label>
                    <input type="date" name="end_date"
                        value="{{ old('end_date', optional($g?->end_date)->format('Y-m-d')) }}"
                        class="w-full px-4 py-2.5 text-primary1 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 focus:border-accent1 transition-all duration-200 hover:border-accent1"
                        style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15);">
                    <x-forms.errors name="end_date" />
                </div>
            </div>

            <div>
                <label class="block mb-2 text-sm font-semibold"
                    style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Description</label>
                <textarea name="description" rows="4"
                    class="w-full px-4 py-2.5 rounded-xl border focus:outline-none focus:ring-2 focus:ring-accent1 focus:border-accent1 transition-all duration-200 hover:border-accent1 resize-y"
                    style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15); color: white;"
                    placeholder="Describe the grant purpose, scope, and any additional details...">{{ old('description', $g?->description ?? '') }}</textarea>
                <x-forms.errors name="description" />
            </div>

            <div>
                <label class="block mb-2 text-sm font-semibold"
                    style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);">Linked Programs</label>
                <div class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 p-4 rounded-xl"
                    style="background-color: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06);">
                    @foreach ($programs as $program)
                        <label
                            class="inline-flex items-center gap-3 text-sm cursor-pointer transition-all duration-200 hover:bg-white/5 px-2 py-1.5 rounded-lg"
                            style="color: rgba(255,255,255,0.8); font-family: var(--font-body1);">
                            <input type="checkbox" name="program_ids[]" value="{{ $program->id }}"
                                class="w-4 h-4 rounded border-white/20 bg-white/5 focus:ring-2 focus:ring-accent1 focus:ring-offset-0 transition-all duration-200 cursor-pointer"
                                style="accent-color: var(--color-accent1);"
                                @if (in_array($program->id, old('program_ids', $g?->programs->pluck('id')->toArray() ?? []))) checked @endif>
                            <span>{{ $program->program_name ?? ($program->program_name ?? 'Program ' . $program->id) }}</span>
                        </label>
                    @endforeach
                </div>
                <x-forms.errors name="program_ids" />
            </div>
        </div>
    </div>
</div>
