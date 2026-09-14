<div class="dropdown dropdown-end">
    <div tabindex="0" role="button" class="btn btn-ghost btn-circle hover:bg-white/10 transition-all">
        <div class="indicator">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            @if (auth()->user()->unreadNotifications->count() > 0)
                <span class="badge badge-xs indicator-item"
                    style="background-color: var(--color-accent1); border: none;">{{ auth()->user()->unreadNotifications->count() }}</span>
            @endif
        </div>
    </div>
    <div tabindex="0"
        class="card card-compact dropdown-content bg-white z-1 mt-2 w-80 shadow-xl rounded-xl border border-gray-100">
        <div class="card-body p-4">
            <div class="flex justify-between items-center mb-2">
                <span class="text-lg font-bold" style="color: var(--color-primary1);">Notifications</span>
                @php $unreadCount = auth()->user()->unreadNotifications->count(); @endphp
                @if ($unreadCount > 0)
                    <span class="text-xs text-gray-400">{{ $unreadCount }} new</span>
                @else
                    <span class="text-xs text-gray-400">All read</span>
                @endif
            </div>
            <div class="space-y-2 max-h-96 overflow-y-auto">
                @forelse (auth()->user()->unreadNotifications()->orderByDesc('created_at')->limit(5)->get() as $notification)
                    @php $notifUrl = $notification->data['action_url'] ?? '#'; @endphp
                    <a href="{{ $notifUrl }}"
                        onclick="event.preventDefault(); fetch('{{ route('notifications.mark-read', $notification->id) }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } }).then(() => { window.location.href = '{{ $notifUrl }}'; });"
                        class="flex items-start gap-2 p-2 rounded-lg hover:bg-gray-50 transition-colors border-l-3 border-blue-500 no-underline">
                        <div class="flex-1 text-sm">
                            <p class="text-gray-800 font-medium">{{ $notification->data['message'] ?? 'Notification' }}
                            </p>
                            <p class="text-gray-400 text-xs mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                    </a>
                @empty
                    <div class="text-center py-4">
                        <p class="text-gray-500 text-sm">No unread notifications</p>
                    </div>
                @endforelse
            </div>
            <div class="card-actions mt-3 flex gap-2">
                <a href="{{ route('notifications.index') }}"
                    class="btn btn-sm flex-1 rounded-lg font-semibold transition-all duration-300 hover:scale-[1.02] hover:shadow-lg text-nowrap"
                    style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);"
                    onmouseover="this.style.backgroundColor='var(--color-accent1)'; this.style.filter='brightness(0.95)'"
                    onmouseout="this.style.backgroundColor='var(--color-accent1)'; this.style.filter='brightness(1)'">
                    View All</a>
                @if ($unreadCount > 0)
                    <form method="POST" action="{{ route('notifications.mark-all-read') }}" class="flex-1">
                        @csrf
                        <button type="submit"
                            class="btn btn-sm flex-1 rounded-lg font-semibold transition-all duration-300 hover:scale-[1.02] hover:shadow-lg text-nowrap"
                            style="font-family: var(--font-body1); background-color: var(--color-success); color: var(--color-primary1);"
                            onmouseover="this.style.backgroundColor='var(--color-success)'; this.style.filter='brightness(0.95)'"
                            onmouseout="this.style.backgroundColor='var(--color-success)'; this.style.filter='brightness(1)'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                            Mark All as Read
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
