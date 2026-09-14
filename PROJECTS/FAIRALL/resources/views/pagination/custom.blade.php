@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-center">
        {{-- Mobile View --}}
        <div class="flex-1 flex justify-between sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="px-4 py-2 text-sm font-semibold rounded-xl mr-2" style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); color: rgba(255,255,255,0.4); border: 1px solid rgba(255,255,255,0.1);">
                    Previous
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="px-4 py-2 mr-2 text-sm font-semibold rounded-xl transition-all duration-200 hover:scale-[1.02]" style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.08); color: white; border: 1px solid rgba(255,255,255,0.15);">
                    Previous
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="px-4 py-2 text-sm font-semibold rounded-xl transition-all duration-200 hover:scale-[1.02]" style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.08); color: white; border: 1px solid rgba(255,255,255,0.15);">
                    Next
                </a>
            @else
                <span class="px-4 py-2 text-sm font-semibold rounded-xl" style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); color: rgba(255,255,255,0.4); border: 1px solid rgba(255,255,255,0.1);">
                    Next
                </span>
            @endif
        </div>

        {{-- Desktop View --}}
        <div class="hidden sm:flex sm:items-center sm:justify-center">
            <div class="flex items-center gap-1.5">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <span class="px-3 py-2 text-sm rounded-xl transition-all duration-200" style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); color: rgba(255,255,255,0.3); border: 1px solid rgba(255,255,255,0.08); cursor: not-allowed;">
                        ←
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-2 text-sm rounded-xl transition-all duration-200 hover:scale-[1.02] hover:border-accent1" style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.06); color: white; border: 1px solid rgba(255,255,255,0.12);">
                        ←
                    </a>
                @endif

                {{-- Pagination Elements - Show up to 5 page numbers --}}
                @php
                    $currentPage = $paginator->currentPage();
                    $lastPage = $paginator->lastPage();
                    
                    // Calculate which pages to show
                    $start = max(1, $currentPage - 2);
                    $end = min($lastPage, $currentPage + 2);
                    
                    // Adjust if at the beginning
                    if ($currentPage <= 3) {
                        $end = min($lastPage, 5);
                    }
                    // Adjust if at the end
                    if ($currentPage >= $lastPage - 2) {
                        $start = max(1, $lastPage - 4);
                    }
                @endphp

                {{-- Page numbers --}}
                @for ($i = $start; $i <= $end; $i++)
                    @if ($i == $currentPage)
                        <span class="px-3.5 py-2 text-sm font-semibold rounded-xl transition-all duration-200" style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1); border: 1px solid var(--color-accent1);">
                            {{ $i }}
                        </span>
                    @else
                        <a href="{{ $paginator->url($i) }}" class="px-3.5 py-2 text-sm rounded-xl transition-all duration-200 hover:scale-[1.02] hover:border-accent1 hover:bg-accent1/10" style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.06); color: white; border: 1px solid rgba(255,255,255,0.12);">
                            {{ $i }}
                        </a>
                    @endif
                @endfor

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-2 text-sm rounded-xl transition-all duration-200 hover:scale-[1.02] hover:border-accent1" style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.06); color: white; border: 1px solid rgba(255,255,255,0.12);">
                        →
                    </a>
                @else
                    <span class="px-3 py-2 text-sm rounded-xl" style="font-family: var(--font-body1); background-color: rgba(255,255,255,0.05); color: rgba(255,255,255,0.3); border: 1px solid rgba(255,255,255,0.08); cursor: not-allowed;">
                        →
                    </span>
                @endif
            </div>
        </div>
    </nav>
@endif