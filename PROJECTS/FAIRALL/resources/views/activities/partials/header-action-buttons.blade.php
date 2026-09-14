<div class="flex flex-wrap gap-2">
    <a href="/activities/{{ $activity->id }}/sessions/create" 
       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg font-semibold transition-all duration-200 hover:scale-[1.02]"
       style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);"
       onmouseover="this.style.backgroundColor='var(--color-accent1)'; this.style.filter='brightness(0.95)'"
       onmouseout="this.style.backgroundColor='var(--color-accent1)'; this.style.filter='brightness(1)'">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"></path>
        </svg>
        Add Session
    </a>
    @can('create-activities')
        <a href="/activities/{{ $activity->id }}/edit" 
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg font-semibold transition-all duration-200 hover:scale-[1.02]"
           style="font-family: var(--font-body1); background-color: transparent; color: var(--color-info); border: 1px solid var(--color-info);"
           onmouseover="this.style.backgroundColor='rgba(59,130,246,0.1)'; this.style.color='var(--color-info-dark)'"
           onmouseout="this.style.backgroundColor='transparent'; this.style.color='var(--color-info)'">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"></path>
            </svg>
            Edit
        </a>
        <button type="button"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg font-semibold transition-all duration-200 hover:scale-[1.02]"
                style="font-family: var(--font-body1); background-color: transparent; color: var(--color-danger); border: 1px solid var(--color-danger);"
                onmouseover="this.style.backgroundColor='rgba(248,113,113,0.15)'; this.style.color='var(--color-danger-dark)'; this.style.borderColor='var(--color-danger-dark)'"
                onmouseout="this.style.backgroundColor='transparent'; this.style.color='var(--color-danger)'; this.style.borderColor='var(--color-danger)'"
                onclick="document.getElementById('deleteActivityModal_{{ $activity->id }}').showModal()">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"></path>
            </svg>
            Delete
        </button>

        <x-confirm-dialog
            id="deleteActivityModal_{{ $activity->id }}"
            title="Delete Activity"
            message="Are you sure you want to delete <strong>{{ $activity->name }}</strong>? This record will be moved to the archive and automatically deleted after 90 days."
            deleteUrl="{{ route('activities.destroy', $activity) }}" />
    @endcan
</div>