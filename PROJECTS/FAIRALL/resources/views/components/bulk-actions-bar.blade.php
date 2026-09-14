@props([
    'bulkUrl',
    'tableId',
    'label' => 'items',
])

{{-- Bulk bar - hidden until at least one row is selected --}}
<div id="bulkBar-{{ $tableId }}" data-bulk-bar="{{ $tableId }}" class="hidden flex flex-wrap items-center gap-3 px-4 py-2.5 rounded-xl mt-4" style="background-color: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.10);">
    <span class="text-sm flex items-center gap-1.5" style="color: rgba(255,255,255,0.7); font-family: var(--font-body1);">
        <span class="font-semibold" style="color: var(--color-accent1);" data-selected-count>0</span>
        <span>selected</span>
    </span>

    <button type="button"
            data-bulk-delete-btn
            disabled
            class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-lg text-sm font-semibold transition-all duration-200 disabled:opacity-40 disabled:cursor-not-allowed hover:scale-[1.02] hover:shadow-md"
            style="background-color: var(--color-danger); color: white; font-family: var(--font-body1);">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"></path>
        </svg>
        Delete Selected
    </button>

    <button type="button"
            data-bulk-clear
            class="text-xs px-3 py-1.5 rounded-lg transition-colors hover:bg-white/10"
            style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">
        Clear
    </button>

    {{-- Hidden form that will be populated with ids[] and submitted after confirmation --}}
    <form id="bulkForm-{{ $tableId }}" method="POST" action="{{ $bulkUrl }}" class="hidden">
        @csrf
        @method('DELETE')
        <div data-bulk-ids></div>
    </form>
</div>

{{-- Shared single confirm dialog for this table (count injected via JS) --}}
<x-confirm-dialog id="bulkConfirm-{{ $tableId }}"
                  title="Delete Selected"
                  :message="'Are you sure you want to delete <span data-bulk-confirm-count class=\'font-bold\'>0</span> '.$label.'? They will be moved to the archive and can be restored within 90 days.'">
    <button type="button"
            id="bulkConfirmBtn-{{ $tableId }}"
            data-bulk-confirm-submit
            class="px-6 py-2.5 font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02] hover:shadow-lg inline-flex items-center justify-center gap-2"
            style="font-family: var(--font-body1); background-color: var(--color-danger); color: white;">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"></path>
        </svg>
        Delete
    </button>
</x-confirm-dialog>
