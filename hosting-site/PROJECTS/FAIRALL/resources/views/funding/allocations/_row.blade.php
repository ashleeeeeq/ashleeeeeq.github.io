@foreach ($allocations as $allocation)
    <tr class="border-b hover:bg-white/5 transition-colors"
        style="border-color: rgba(255,255,255,0.04);">
        <td class="px-4 py-2 text-center">
            <input type="checkbox" value="{{ $allocation->id }}" data-row-checkbox class="w-4 h-4 rounded border-white/20 bg-white/5 text-yellow-400 focus:ring-yellow-400/50 focus:ring-2 cursor-pointer">
        </td>
        <td class="px-4 py-2">
            <a href="/beneficiaries/{{ $allocation->beneficiary?->id }}/profile"
                class="text-sm font-medium hover:text-accent1 transition-colors duration-200"
                style="color: white; font-family: var(--font-body1);"
                onmouseover="this.style.color='var(--color-accent1)'"
                onmouseout="this.style.color='white'">
                {{ $allocation->beneficiary?->display_name ?? '-' }}
            </a>
        </td>
        <td class="px-4 py-2">
            <span class="text-xs sm:text-sm text-white/70" style="font-family: var(--font-body1);">
                {{ $allocation->donation?->reference_number ?? $allocation->grant?->grant_name ?? '-' }}
            </span>
        </td>
        <td class="px-4 py-2">
            <span class="text-xs sm:text-sm font-semibold text-white"
                style="font-family: var(--font-body1);">
                ₱{{ number_format($allocation->amount_cents / 100, 2) }}</span>
        </td>
        <td class="px-4 py-2">
            <span class="text-xs sm:text-sm text-white/70"
                style="font-family: var(--font-body1);">
                {{ optional($allocation->date_allocated)->format('M d, Y') ?? '-' }}</span>
        </td>
        <td class="px-4 py-2">
            <div class="flex items-center justify-center gap-1 sm:gap-2">
                <a href="{{ route('funding.allocations.show', $allocation) }}"
                    class="p-1 sm:p-1.5 rounded-lg transition-all hover:bg-white/10 hover:scale-110"
                    title="View">
                    <svg class="w-3.5 h-3.5" style="color: rgba(255,255,255,0.6);"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2"
                            d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.964 7.182a1.01 1.01 0 0 1 0 .636C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178Z">
                        </path>
                        <path stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z">
                        </path>
                    </svg>
                </a>
                <a href="{{ route('funding.allocations.edit', $allocation) }}"
                    class="p-1 sm:p-1.5 rounded-lg transition-all hover:bg-white/10 hover:scale-110"
                    title="Edit">
                    <svg class="w-3.5 h-3.5" style="color: rgba(255,255,255,0.6);"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2"
                            d="M16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10">
                        </path>
                    </svg>
                </a>
                <button type="button"
                    class="p-1 sm:p-1.5 rounded-lg transition-all hover:bg-red-500/20 hover:scale-110"
                    title="Delete"
                    onclick="document.getElementById('deleteAllocModal_{{ $allocation->id }}').showModal()">
                    <svg class="w-3.5 h-3.5" style="color: var(--color-danger);"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2"
                            d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0">
                        </path>
                    </svg>
                </button>
                <x-confirm-dialog id="deleteAllocModal_{{ $allocation->id }}"
                    title="Delete Allocation"
                    message="Are you sure you want to delete this allocation for <strong>{{ $allocation->beneficiary?->display_name ?? 'beneficiary' }}</strong> of <strong>₱{{ number_format($allocation->amount_cents / 100, 2) }}</strong>? This action cannot be undone."
                    deleteUrl="{{ route('funding.allocations.destroy', $allocation) }}" />
            </div>
        </td>
    </tr>
@endforeach
