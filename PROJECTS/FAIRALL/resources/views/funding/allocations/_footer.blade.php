<span style="font-family: var(--font-body1);">
    <span class="text-white/60">Total Allocated:</span>
    <span class="text-white font-semibold ml-1">₱{{ number_format($totalAllocatedCents / 100, 2) }}</span>
</span>
<span style="font-family: var(--font-body1);">
    <span class="text-white/60">Balance:</span>
    <span class="{{ $balanceCents > 0 ? 'text-green-400' : 'text-white/50' }} font-semibold ml-1">
        ₱{{ number_format(max(0, $balanceCents / 100), 2) }}
    </span>
</span>
