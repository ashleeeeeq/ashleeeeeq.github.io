<x-dashboardlayout name="{{ $name }}" title="Create Competition">
    <div class="mb-8 space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold tracking-tight" style="font-family: var(--font-header1); color: var(--color-white);">Create Competition</h1>
                <p class="text-sm mt-1" style="color: rgba(255,255,255,0.6); font-family: var(--font-body1);">Set up a new competition and track results</p>
                <div class="w-20 h-1 mt-2 rounded-full" style="background: var(--color-accent1);"></div>
            </div>
        </div>

        @if ($errors->any())
            <div class="mb-6 p-4 rounded-xl flex items-start gap-3" style="background-color: rgba(248,113,113,0.1); border: 1px solid rgba(248,113,113,0.3);">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: var(--color-danger);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"></path>
                </svg>
                <div>
                    <span class="text-sm font-semibold" style="color: var(--color-danger);">Please fix the following errors:</span>
                    <ul class="mt-1 text-sm space-y-1" style="color: rgba(255,255,255,0.7);">
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('competitions.store') }}" class="space-y-6">
            @csrf

            @include('competitions._form')

            <!-- Form Actions -->
            <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 pt-4">
                <a href="{{ route('competitions.index') }}" 
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
                    Create Competition
                </button>
            </div>
        </form>
    </div>
</x-dashboardlayout>