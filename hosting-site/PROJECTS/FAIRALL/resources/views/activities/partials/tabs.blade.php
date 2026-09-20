<div class="overflow-x-auto overflow-y-hidden mb-6">
    <div class="flex gap-2 border-b min-w-max" style="border-color: rgba(255,255,255,0.08);">
        <a href="/activities/{{ $activity->id }}" 
           class="tab-item inline-flex items-center gap-2 px-5.5 py-2.5 text-sm font-medium rounded-t-lg transition-all duration-200 
                  {{ request()->routeIs('activities.show') 
                      ? 'text-accent1 border-b-2 border-accent1' 
                      : 'border-b-2 border-transparent hover:text-accent1 hover:border-b-2 hover:border-accent1' }}" 
           style="font-family: var(--font-body1); color: {{ request()->routeIs('activities.show') ? 'var(--color-accent1)' : 'rgba(255,255,255,0.6)' }};">
            <svg class="w-4 h-4 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Sessions
        </a>

        <a href="/activities/{{ $activity->id }}/participants" 
           class="tab-item inline-flex items-center gap-2 px-5.5 py-2.5 text-sm font-medium rounded-t-lg transition-all duration-200 
                  {{ request()->routeIs('activities.participants.index') 
                      ? 'text-accent1 border-b-2 border-accent1' 
                      : 'border-b-2 border-transparent hover:text-accent1 hover:border-b-2 hover:border-accent1' }}" 
           style="font-family: var(--font-body1); color: {{ request()->routeIs('activities.participants.index') ? 'var(--color-accent1)' : 'rgba(255,255,255,0.6)' }};">
            <svg class="w-4 h-4 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"></path>
            </svg>
            Participants
        </a>

        <a href="/activities/{{ $activity->id }}/history" 
           class="tab-item inline-flex items-center gap-2 px-5.5 py-2.5 text-sm font-medium rounded-t-lg transition-all duration-200 
                  {{ request()->routeIs('activities.history.index') 
                      ? 'text-accent1 border-b-2 border-accent1' 
                      : 'border-b-2 border-transparent hover:text-accent1 hover:border-b-2 hover:border-accent1' }}" 
           style="font-family: var(--font-body1); color: {{ request()->routeIs('activities.history.index') ? 'var(--color-accent1)' : 'rgba(255,255,255,0.6)' }};">
            <svg class="w-4 h-4 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Join History
        </a>
    </div>
</div>