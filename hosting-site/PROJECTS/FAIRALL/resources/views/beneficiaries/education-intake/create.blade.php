<x-dashboardlayout name="{{ $name }}" title="Education Intake">
    <div class="mx-auto max-w-3xl">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-white" style="font-family: var(--font-header1);">Education Intake Sheet</h1>
                <p class="text-white/60 text-sm mt-1" style="font-family: var(--font-body1);">Beneficiary: <span class="font-semibold text-white/80">{{ $beneficiary->display_name }}</span></p>
                <div class="w-16 h-1 mt-2 rounded-full" style="background-color: var(--color-accent1);"></div>
            </div>
        </div>

        <div class="mb-6 p-4 rounded-xl" style="background-color: rgba(59,130,246,0.1); border: 1px solid rgba(59,130,246,0.3);">
            <p class="text-sm" style="color: #60a5fa; font-family: var(--font-body1);">Please complete this form to finish enrollment in the Education program.</p>
        </div>

        <form method="POST" action="/beneficiaries/{{ $beneficiary->id }}/education-intake">
            @csrf

            @php
                $intake = old('intake', $intakeSheet?->toArray() ?? []);
            @endphp
            @include('beneficiaries.partials.intake-fields', ['intake' => $intake])

            <div class="flex justify-between gap-3 pt-6 mt-6 border-t" style="border-color: rgba(255,255,255,0.1);">
                <a href="/beneficiaries/enrollment"
                    class="px-6 py-2.5 font-semibold rounded-lg xs:rounded-sm transition-all duration-300 hover:scale-[1.02] bg-transparent text-red-600 border border-red-600 hover:bg-red-50 hover:text-red-700 inline-flex items-center gap-2"
                    style="font-family: var(--font-body1);">
                    Cancel
                </a>
                <button type="submit"
                    class="px-6 py-2.5 font-semibold rounded-lg xs:rounded-sm transition-all duration-300 hover:scale-[1.02] inline-flex items-center gap-2"
                    style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);">
                    Complete Enrollment
                </button>
            </div>
        </form>
    </div>
</x-dashboardlayout>
