<div class="overflow-x-auto overflow-y-hidden mb-6">
    <div class="flex gap-2 border-b min-w-max sm:min-w-0" style="border-color: rgba(255,255,255,0.1);">
        <a href="{{ route('funding.index') }}"
           class="tab-item inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-5 py-2 sm:py-2.5 text-xs sm:text-sm font-medium rounded-t-lg transition-all duration-200 whitespace-nowrap {{ request()->routeIs('funding.index') ? 'text-accent1 border-b-2 border-accent1' : 'text-white/60 border-b-2 border-transparent hover:text-accent1 hover:border-b-2 hover:border-accent1' }}"
           style="{{ request()->routeIs('funding.index') ? '' : 'border-bottom-width: 2px;' }}">
            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"></path>
            </svg>
            Overview
        </a>


        <a href="{{ route('funding.allocations.index') }}"
           class="tab-item inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-5 py-2 sm:py-2.5 text-xs sm:text-sm font-medium rounded-t-lg transition-all duration-200 whitespace-nowrap {{ request()->routeIs('funding.allocations.*') ? 'text-accent1 border-b-2 border-accent1' : 'text-white/60 border-b-2 border-transparent hover:text-accent1 hover:border-b-2 hover:border-accent1' }}">
            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m3.75 9v7.5m2.25-6.466a9.016 9.016 0 0 0-3.461-.203c-.536.072-.974.478-1.021 1.017a4.559 4.559 0 0 0-.018.402c0 .464.336.844.775.994l2.95 1.012c.44.15.775.53.775.994 0 .136-.006.27-.018.402-.047.539-.485.945-1.021 1.017a9.077 9.077 0 0 1-3.461-.203M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"></path>
            </svg>
            Allocations
        </a>


        <a href="{{ route('funding.targets') }}"
           class="tab-item inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-5 py-2 sm:py-2.5 text-xs sm:text-sm font-medium rounded-t-lg transition-all duration-200 whitespace-nowrap {{ request()->routeIs('funding.targets*') ? 'text-accent1 border-b-2 border-accent1' : 'text-white/60 border-b-2 border-transparent hover:text-accent1 hover:border-b-2 hover:border-accent1' }}">
            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3v1.5M3 21v-6m0 0 2.77-.693a9 9 0 0 1 6.208.682l.108.054a9 9 0 0 0 6.086.71l3.114-.732a48.524 48.524 0 0 1-.005-10.499l-3.11.732a9 9 0 0 1-6.085-.711l-.108-.054a9 9 0 0 0-6.208-.682L3 4.5M3 15V4.5"></path>
            </svg>
            Targets
        </a>
    </div>
</div>

