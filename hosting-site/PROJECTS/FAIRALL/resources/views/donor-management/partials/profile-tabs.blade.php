<div class="overflow-x-auto overflow-y-hidden mb-6">
    <div class="flex gap-2 border-b border-white/20 min-w-max sm:min-w-0">
        <a href="{{ route('donors.show', ['donor' => $donor, 'tab' => 'metrics']) }}"
           class="tab-item inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-5 py-2 sm:py-2.5 text-xs sm:text-sm font-medium rounded-t-lg transition-all duration-200 whitespace-nowrap {{ ($activeTab ?? 'metrics') === 'metrics' ? 'text-accent1 border-b-2 border-accent1' : 'text-white/60 border-b-2 border-transparent hover:text-accent1 hover:border-b-2 hover:border-accent1' }}">
            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75"></path>
            </svg>
            Metrics
        </a>

        <a href="{{ route('donors.show', ['donor' => $donor, 'tab' => 'donations']) }}"
           class="tab-item inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-5 py-2 sm:py-2.5 text-xs sm:text-sm font-medium rounded-t-lg transition-all duration-200 whitespace-nowrap {{ ($activeTab ?? '') === 'donations' ? 'text-accent1 border-b-2 border-accent1' : 'text-white/60 border-b-2 border-transparent hover:text-accent1 hover:border-b-2 hover:border-accent1' }}">
            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"></path>
            </svg>
            Donations
        </a>

        <a href="{{ route('donors.show', ['donor' => $donor, 'tab' => 'allocations']) }}"
           class="tab-item inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-5 py-2 sm:py-2.5 text-xs sm:text-sm font-medium rounded-t-lg transition-all duration-200 whitespace-nowrap {{ ($activeTab ?? '') === 'allocations' ? 'text-accent1 border-b-2 border-accent1' : 'text-white/60 border-b-2 border-transparent hover:text-accent1 hover:border-b-2 hover:border-accent1' }}">
            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m3.75 9v7.5m2.25-6.466a9.016 9.016 0 0 0-3.461-.203c-.536.072-.974.478-1.021 1.017a4.559 4.559 0 0 0-.018.402c0 .464.336.844.775.994l2.95 1.012c.44.15.775.53.775.994 0 .136-.006.27-.018.402-.047.539-.485.945-1.021 1.017a9.077 9.077 0 0 1-3.461-.203M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"></path>
            </svg>
            Allocations
        </a>

        <a href="{{ route('donors.show', ['donor' => $donor, 'tab' => 'deliverables']) }}"
           class="tab-item inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-5 py-2 sm:py-2.5 text-xs sm:text-sm font-medium rounded-t-lg transition-all duration-200 whitespace-nowrap {{ ($activeTab ?? '') === 'deliverables' ? 'text-accent1 border-b-2 border-accent1' : 'text-white/60 border-b-2 border-transparent hover:text-accent1 hover:border-b-2 hover:border-accent1' }}">
            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0Z"></path>
            </svg>
            Deliverables
        </a>
    </div>
</div>