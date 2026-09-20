<x-dashboardlayout title="Notifications">
    <div class="mb-8 space-y-6">
        <div class="mb-8">
            <!-- Header Section -->
            <div class="flex flex-col gap-4 mb-6 md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold tracking-tight" style="font-family: var(--font-header1); color: var(--color-white);">Notifications</h1>
                    <p class="text-sm mt-1" style="color: rgba(255,255,255,0.6); font-family: var(--font-body1);">Browse all notifications by status</p>
                    <div class="w-20 h-1 mt-2 rounded-full" style="background: var(--color-accent1);"></div>
                </div>

                @if (($counts['unread'] ?? 0) > 0)
                    <form method="POST" action="{{ route('notifications.mark-all-read') }}" class="inline">
                        @csrf
                        <button type="submit" 
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold transition-all duration-300 hover:scale-[1.02] hover:shadow-lg"
                                style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);"
                                onmouseover="this.style.backgroundColor='var(--color-accent1)'; this.style.filter='brightness(0.95)'"
                                onmouseout="this.style.backgroundColor='var(--color-accent1)'; this.style.filter='brightness(1)'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Mark All as Read
                        </button>
                    </form>
                @endif
            </div>

            <!-- Tabs -->
            <div class="flex gap-2 border-b mb-6" style="border-color: rgba(255,255,255,0.08);">
                <a href="{{ route('notifications.index', ['tab' => 'all']) }}" 
                   class="px-5 py-2.5 text-sm font-medium rounded-t-lg transition-all duration-200 flex items-center gap-2"
                   style="font-family: var(--font-body1); {{ $tab === 'all' ? 'color: var(--color-accent1); border-bottom: 2px solid var(--color-accent1);' : 'color: rgba(255,255,255,0.6); border-bottom: 2px solid transparent;' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    All
                    <span class="inline-flex px-2 py-0.5 text-xs rounded-full" 
                          style="background-color: rgba(255,204,51,0.15); color: var(--color-accent1);">
                        {{ $counts['all'] ?? 0 }}
                    </span>
                </a>
                <a href="{{ route('notifications.index', ['tab' => 'unread']) }}" 
                   class="px-5 py-2.5 text-sm font-medium rounded-t-lg transition-all duration-200 flex items-center gap-2"
                   style="font-family: var(--font-body1); {{ $tab === 'unread' ? 'color: var(--color-accent1); border-bottom: 2px solid var(--color-accent1);' : 'color: rgba(255,255,255,0.6); border-bottom: 2px solid transparent;' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    Unread
                    <span class="inline-flex px-2 py-0.5 text-xs rounded-full" 
                          style="background-color: rgba(16,185,129,0.15); color: var(--color-success);">
                        {{ $counts['unread'] ?? 0 }}
                    </span>
                </a>
                <a href="{{ route('notifications.index', ['tab' => 'read']) }}" 
                   class="px-5 py-2.5 text-sm font-medium rounded-t-lg transition-all duration-200 flex items-center gap-2"
                   style="font-family: var(--font-body1); {{ $tab === 'read' ? 'color: var(--color-accent1); border-bottom: 2px solid var(--color-accent1);' : 'color: rgba(255,255,255,0.6); border-bottom: 2px solid transparent;' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Read
                    <span class="inline-flex px-2 py-0.5 text-xs rounded-full" 
                          style="background-color: rgba(255,255,255,0.15); color: rgba(255,255,255,0.6);">
                        {{ $counts['read'] ?? 0 }}
                    </span>
                </a>
            </div>

            @if ($notifications->count() > 0)
                <div class="space-y-3">
                    @foreach ($notifications as $notification)
                        @php
                            $isUnread = is_null($notification->read_at);
                        @endphp
                        @php $notifUrl = $notification->data['action_url'] ?? null; @endphp
                        <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-[0_8px_30px_rgba(0,0,0,0.12)]" 
                             style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                            <div class="p-5 flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-wrap items-center gap-2 mb-3">
                                        <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full" 
                                              style="background-color: {{ $isUnread ? 'rgba(16,185,129,0.15)' : 'rgba(255,255,255,0.08)' }}; color: {{ $isUnread ? 'var(--color-success)' : 'rgba(255,255,255,0.5)' }};">
                                            {{ $isUnread ? 'Unread' : 'Read' }}
                                        </span>
                                        @if (!empty($notification->data['type']))
                                            <span class="inline-flex px-2 py-0.5 text-xs rounded-full" style="background-color: rgba(255,204,51,0.15); color: var(--color-accent1);">
                                                {{ Str::of($notification->data['type'])->replace('_', ' ')->title()->replace('Gwa', 'GWA') }}
                                            </span>
                                        @endif
                                    </div>
                                    @if ($notifUrl)
                                        <a href="{{ $notifUrl }}"
                                            onclick="event.preventDefault(); fetch('{{ route('notifications.mark-read', $notification->id) }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } }).then(() => { window.location.href = '{{ $notifUrl }}'; });"
                                            class="font-semibold no-underline hover:underline"
                                            style="color: rgba(255,255,255,0.9); font-family: var(--font-body1);">
                                            {{ $notification->data['message'] ?? 'Notification' }}
                                        </a>
                                    @else
                                        <p class="font-semibold" style="color: rgba(255,255,255,0.9); font-family: var(--font-body1);">
                                            {{ $notification->data['message'] ?? 'Notification' }}
                                        </p>
                                    @endif
                                    <div class="flex flex-wrap items-center gap-3 mt-2 text-xs" style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">
                                        <div class="flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"></path>
                                            </svg>
                                            <span>{{ $notification->created_at->format('M d, Y g:i A') }}</span>
                                        </div>
                                        @if ($notification->read_at)
                                            <div class="flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <span>Read {{ $notification->read_at->format('M d, Y g:i A') }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex flex-wrap gap-2">
                                    @if ($isUnread)
                                        <form method="POST" action="{{ route('notifications.mark-read', $notification->id) }}">
                                            @csrf
                                            <button type="submit" 
                                                    class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all duration-200 hover:scale-[1.02]"
                                                    style="font-family: var(--font-body1); background-color: transparent; color: var(--color-success); border: 1px solid var(--color-success);"
                                                    onmouseover="this.style.backgroundColor='rgba(16,185,129,0.1)'"
                                                    onmouseout="this.style.backgroundColor='transparent'">
                                                Mark Read
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('notifications.mark-unread', $notification->id) }}">
                                            @csrf
                                            <button type="submit" 
                                                    class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all duration-200 hover:scale-[1.02]"
                                                    style="font-family: var(--font-body1); background-color: transparent; color: var(--color-info); border: 1px solid var(--color-info);"
                                                    onmouseover="this.style.backgroundColor='rgba(59,130,246,0.1)'"
                                                    onmouseout="this.style.backgroundColor='transparent'">
                                                Mark Unread
                                            </button>
                                        </form>
                                    @endif

                                    <button type="button" 
                                            class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all duration-200 hover:scale-[1.02]"
                                            style="font-family: var(--font-body1); background-color: transparent; color: var(--color-danger); border: 1px solid var(--color-danger);"
                                            onmouseover="this.style.backgroundColor='rgba(248,113,113,0.15)'"
                                            onmouseout="this.style.backgroundColor='transparent'"
                                            onclick="document.getElementById('dismissNotificationModal_{{ $notification->id }}').showModal()">
                                        Dismiss
                                    </button>

                                    <x-confirm-dialog id="dismissNotificationModal_{{ $notification->id }}" title="Dismiss Notification" message="Dismiss this notification?">
                                        <form method="POST" action="{{ route('notifications.destroy', $notification->id) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-4 py-2 font-semibold rounded-lg transition-all duration-200" style="background-color: var(--color-danger); color: white;">Dismiss</button>
                                        </form>
                                    </x-confirm-dialog>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 flex justify-center">
                    {{ $notifications->links('pagination.custom') }}
                </div>
            @else
                <div class="rounded-2xl p-12 text-center" style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                    <div class="flex flex-col items-center gap-3">
                            <svg class="w-10 h-10" style="color: rgba(255,255,255,0.2);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"></path>
                            </svg>
                        <p class="text-sm" style="color: rgba(255,255,255,0.4); font-family: var(--font-body1);">No notifications found in this tab.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-dashboardlayout>