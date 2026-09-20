<x-dashboardlayout name="{{ $name }}" title="Event Details">
    <div class="mb-8 space-y-6">
        <!-- Header -->
        <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-[0_8px_30px_rgba(0,0,0,0.12)]"
            style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
            <div class="p-6 flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                <div>
                    <div class="flex flex-wrap items-center gap-3">
                        <h1 class="text-2xl md:text-3xl font-bold tracking-tight"
                            style="font-family: var(--font-header1); color: var(--color-white);">
                            {{ $event->name }}
                        </h1>
                        <span class="px-3 py-1 text-xs font-semibold rounded-full"
                            style="background-color: rgba(255,204,51,0.15); color: var(--color-accent1); font-family: var(--font-body1);">
                            {{ $event->eventType?->name ?? 'Event type' }}
                        </span>
                    </div>
                    <p class="mt-3 text-sm" style="color: rgba(255,255,255,0.6); font-family: var(--font-body1);">
                        {{ $event->program?->program_name ?? 'No program' }} ·
                        {{ $event->start?->format('M d, Y g:i A') }}
                    </p>
                    <p class="mt-1 text-sm" style="color: rgba(255,255,255,0.6); font-family: var(--font-body1);">
                        {{ $event->location ?: 'No location set' }}
                    </p>

                    @if ($event->description)
                        <div class="mt-4 p-4 rounded-xl"
                            style="background-color: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06);">
                            <p class="text-sm leading-6"
                                style="color: rgba(255,255,255,0.8); font-family: var(--font-body1);">
                                {{ $event->description }}
                            </p>
                        </div>
                    @endif
                </div>

                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('events.attendance.create', $event) }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5 font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02] hover:shadow-lg"
                        style="font-family: var(--font-body1); background-color: var(--color-accent1); color: var(--color-primary1);"
                        onmouseover="this.style.backgroundColor='var(--color-accent1)'; this.style.filter='brightness(0.95)'"
                        onmouseout="this.style.backgroundColor='var(--color-accent1)'; this.style.filter='brightness(1)'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                        </svg>
                        Attendance
                    </a>
                    <a href="{{ route('events.edit', $event) }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5 font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02]"
                        style="font-family: var(--font-body1); background-color: transparent; color: var(--color-info); border: 1px solid var(--color-info);"
                        onmouseover="this.style.backgroundColor='rgba(59,130,246,0.1)'; this.style.color='var(--color-info-dark)'"
                        onmouseout="this.style.backgroundColor='transparent'; this.style.color='var(--color-info)'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                        </svg>
                        Edit
                    </a>
                    <button type="button"
                        class="inline-flex items-center gap-2 px-5 py-2.5 font-semibold rounded-xl transition-all duration-300 hover:scale-[1.02]"
                        style="font-family: var(--font-body1); background-color: transparent; color: var(--color-danger); border: 1px solid var(--color-danger);"
                        onmouseover="this.style.backgroundColor='rgba(248,113,113,0.15)'; this.style.color='var(--color-danger-dark)'; this.style.borderColor='var(--color-danger-dark)'"
                        onmouseout="this.style.backgroundColor='transparent'; this.style.color='var(--color-danger)'; this.style.borderColor='var(--color-danger)'"
                        onclick="document.getElementById('deleteEventModal').showModal()">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>
                        Delete
                    </button>

                    <x-confirm-dialog id="deleteEventModal" title="Delete Event"
                        message="Are you sure you want to delete <strong>{{ $event->name }}</strong>? This record will be moved to the archive and automatically deleted after 90 days."
                        deleteUrl="{{ route('events.destroy', $event) }}" />
                </div>
            </div>
        </div>

        @if (session('status'))
            <div class="rounded-xl p-4 flex items-start gap-3 transition-all duration-300"
                style="background-color: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.3);">
                <svg class="w-5 h-5 shrink-0 mt-0.5" style="color: var(--color-success);" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm"
                    style="color: rgba(255,255,255,0.9); font-family: var(--font-body1);">{{ session('status') }}</span>
            </div>
        @endif

        <!-- Stats Cards -->
        <div class="grid gap-4 md:grid-cols-3">
            <div class="rounded-2xl p-5 transition-all duration-300 hover:shadow-[0_8px_30px_rgba(0,0,0,0.12)]"
                style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                        style="background-color: rgba(255,204,51,0.15);">
                        <svg class="w-5 h-5" style="color: var(--color-accent1);" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider"
                            style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Attendance Records
                        </div>
                        <div class="text-3xl font-bold mt-1"
                            style="font-family: var(--font-header1); color: var(--color-white);">
                             <span id="attendance-count">{{ $attendances->total() }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl p-5 transition-all duration-300 hover:shadow-[0_8px_30px_rgba(0,0,0,0.12)]"
                style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                        style="background-color: rgba(59,130,246,0.15);">
                        <svg class="w-5 h-5" style="color: var(--color-info);" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider"
                            style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Created By</div>
                        <div class="text-lg font-semibold mt-1"
                            style="font-family: var(--font-header1); color: var(--color-white);">
                            {{ $event->creator?->display_name ?? 'Staff' }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl p-5 transition-all duration-300 hover:shadow-[0_8px_30px_rgba(0,0,0,0.12)]"
                style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                        style="background-color: rgba(248,113,113,0.15);">
                        <svg class="w-5 h-5" style="color: var(--color-danger);" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider"
                            style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Last Updated By</div>
                        <div class="text-lg font-semibold mt-1"
                            style="font-family: var(--font-header1); color: var(--color-white);">
                            {{ $event->updater?->display_name ?? 'Staff' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance Table Card -->
        <div class="rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-[0_8px_30px_rgba(0,0,0,0.12)]"
            style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08);">
            <div class="px-6 py-4 border-b"
                style="border-color: rgba(255,255,255,0.08); background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center"
                            style="background-color: rgba(255,204,51,0.15);">
                            <svg class="w-4 h-4" style="color: var(--color-accent1);" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-semibold text-white" style="font-family: var(--font-header1);">Attendance
                                Records</h2>
                            <p class="text-xs mt-0.5" style="color: rgba(255,255,255,0.5);">List of all attendance
                                entries</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 text-xs font-semibold rounded-full"
                        style="background-color: rgba(255,204,51,0.15); color: var(--color-accent1);">
                        {{ $attendances->total() }} records
                    </span>
                </div>
            </div>
            <div class="p-6">
                @can('manage-events')
                    @include('partials.qr_panel', [
                        'token' => $event->qr_token,
                        'printRoute' => route('events.qr.print', $event),
                        'regenerateRoute' => route('events.qr.regenerate', $event),
                    ])
                @endcan

                <div class="overflow-x-auto mt-6">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b" style="border-color: rgba(255,255,255,0.08);">
                                <th class="text-left py-3 px-2 text-xs font-semibold uppercase tracking-wider"
                                    style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Name</th>
                                <th class="text-left py-3 px-2 text-xs font-semibold uppercase tracking-wider"
                                    style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Status</th>
                                <th class="text-left py-3 px-2 text-xs font-semibold uppercase tracking-wider"
                                    style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">User Type</th>
                                <th class="text-left py-3 px-2 text-xs font-semibold uppercase tracking-wider"
                                    style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Remarks</th>
                                <th class="text-left py-3 px-2 text-xs font-semibold uppercase tracking-wider"
                                    style="color: rgba(255,255,255,0.5); font-family: var(--font-body1);">Updated By
                                </th>
                        </thead>
                        <tbody id="attendance-tbody">
                            @forelse ($attendances as $attendance)
                                <tr id="attendance-row-{{ $attendance->id }}"
                                    class="border-b transition-all duration-200 hover:bg-white/5"
                                    style="border-color: rgba(255,255,255,0.05);">
                                    <td class="py-3 px-2 text-sm"
                                        style="color: rgba(255,255,255,0.85); font-family: var(--font-body1);">
                                        {{ $attendance->beneficiary?->display_name ?? ($attendance->staff?->display_name ?? 'Unknown') }}
                                    </td>
                                    <td class="py-3 px-2">
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-full
                                            @if ($attendance->attendance_status == 'present') bg-green-500/10 text-green-400 border border-green-500/20
                                            @elseif($attendance->attendance_status == 'absent') bg-red-500/10 text-red-400 border border-red-500/20
                                            @else bg-gray-500/10 text-gray-400 border border-gray-500/20 @endif">
                                            <span
                                                class="w-1.5 h-1.5 rounded-full
                                                @if ($attendance->attendance_status == 'present') bg-green-400
                                                @elseif($attendance->attendance_status == 'absent') bg-red-400
                                                @else bg-gray-400 @endif">
                                            </span>
                                            {{ ucfirst($attendance->attendance_status) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-2 text-sm"
                                        style="color: rgba(255,255,255,0.7); font-family: var(--font-body1);">
                                        @if ($attendance->beneficiary)
                                            Beneficiary
                                        @else
                                            Staff
                                        @endif
                                    </td>
                                    <td class="py-3 px-2 text-sm"
                                        style="color: rgba(255,255,255,0.6); font-family: var(--font-body1);">
                                        {{ $attendance->remarks ?: '-' }}
                                    </td>
                                    <td class="py-3 px-2 text-sm"
                                        style="color: rgba(255,255,255,0.6); font-family: var(--font-body1);">
                                        {{ $attendance->updater?->display_name ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr id="attendance-empty-row">
                                    <td colspan="5" class="py-12 text-center">
                                        <div class="flex flex-col items-center gap-2 pt-6">
                                            <svg class="w-12 h-12" style="color: rgba(255,255,255,0.2);"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="1.5"
                                                    d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                                            </svg>
                                            <p class="text-sm" style="color: rgba(255,255,255,0.4);">No attendance
                                                recorded yet.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    @if ($attendances->hasPages())
                        <div class="flex justify-center pt-6 mt-4 border-t"
                            style="border-color: rgba(255,255,255,0.08);">
                            {{ $attendances->links('pagination.custom') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Back Button -->
        <div class="flex justify-end">
            <a href="{{ route('events.index') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg font-semibold transition-all duration-300 hover:bg-white/10"
                style="font-family: var(--font-body1); color: rgba(255,255,255,0.7);"
                onmouseover="this.style.backgroundColor='rgba(255,255,255,0.1)'"
                onmouseout="this.style.backgroundColor='transparent'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"></path>
                </svg>
                Back to Events
            </a>
        </div>
    </div>

    <script>
        (function() {
            const eventId = {{ $event->id }};
            let lastPoll = new Date().toISOString();

            const statusStyles = {
                present: { color: '#22c55e', bg: 'rgba(34,197,94,0.1)', border: 'rgba(34,197,94,0.2)', dot: '#22c55e', label: 'Present' },
                absent: { color: '#ef4444', bg: 'rgba(239,68,68,0.1)', border: 'rgba(239,68,68,0.2)', dot: '#ef4444', label: 'Absent' },
                late: { color: '#eab308', bg: 'rgba(234,179,8,0.1)', border: 'rgba(234,179,8,0.2)', dot: '#eab308', label: 'Late' },
                excused: { color: '#f97316', bg: 'rgba(249,115,22,0.1)', border: 'rgba(249,115,22,0.2)', dot: '#f97316', label: 'Excused' },
            };

            function esc(str) {
                var div = document.createElement('div');
                div.appendChild(document.createTextNode(str));
                return div.innerHTML;
            }

            function capitalize(str) {
                return str.charAt(0).toUpperCase() + str.slice(1);
            }

            function buildRow(att) {
                var s = statusStyles[att.attendance_status] || { color: '#9ca3af', bg: 'rgba(156,163,175,0.1)', border: 'rgba(156,163,175,0.2)', dot: '#9ca3af', label: capitalize(att.attendance_status) };
                return '<tr id="attendance-row-' + att.id + '" class="border-b transition-all duration-200 hover:bg-white/5" style="border-color: rgba(255,255,255,0.05);">' +
                    '<td class="py-3 px-2 text-sm" style="color: rgba(255,255,255,0.85); font-family: var(--font-body1);">' + esc(att.name) + '</td>' +
                    '<td class="py-3 px-2">' +
                        '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-full" style="background-color: ' + s.bg + '; color: ' + s.color + '; border: 1px solid ' + s.border + ';">' +
                            '<span class="w-1.5 h-1.5 rounded-full" style="background-color: ' + s.dot + ';"></span> ' + s.label +
                        '</span>' +
                    '</td>' +
                    '<td class="py-3 px-2 text-sm" style="color: rgba(255,255,255,0.7); font-family: var(--font-body1);">' + esc(att.user_type) + '</td>' +
                    '<td class="py-3 px-2 text-sm" style="color: rgba(255,255,255,0.6); font-family: var(--font-body1);">' + esc(att.remarks) + '</td>' +
                    '<td class="py-3 px-2 text-sm" style="color: rgba(255,255,255,0.6); font-family: var(--font-body1);">' + esc(att.updated_by) + '</td>' +
                '</tr>';
            }

            function updateRow(row, att) {
                var s = statusStyles[att.attendance_status] || { color: '#9ca3af', bg: 'rgba(156,163,175,0.1)', border: 'rgba(156,163,175,0.2)', dot: '#9ca3af', label: capitalize(att.attendance_status) };
                var badge = row.querySelector('span.inline-flex');
                if (badge) {
                    badge.style.backgroundColor = s.bg;
                    badge.style.color = s.color;
                    badge.style.borderColor = s.border;
                    var dot = badge.querySelector('span.rounded-full');
                    if (dot) dot.style.backgroundColor = s.dot;
                    badge.childNodes[2].textContent = ' ' + s.label;
                }
                var cells = row.querySelectorAll('td');
                if (cells.length >= 5) {
                    cells[3].textContent = att.remarks;
                    cells[4].textContent = att.updated_by;
                }
            }

            function poll() {
                fetch('/events/' + eventId + '/attendances/poll?since=' + encodeURIComponent(lastPoll))
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        if (data.attendances.length === 0) return;
                        var tbody = document.getElementById('attendance-tbody');
                        var emptyRow = document.getElementById('attendance-empty-row');
                        data.attendances.forEach(function(att) {
                            var existing = document.getElementById('attendance-row-' + att.id);
                            if (existing) {
                                updateRow(existing, att);
                            } else {
                                if (emptyRow) emptyRow.remove();
                                tbody.insertAdjacentHTML('beforeend', buildRow(att));
                            }
                        });
                        var countEl = document.getElementById('attendance-count');
                        if (countEl) countEl.textContent = data.total;
                        lastPoll = data.polled_at;
                    })
                    .catch(function() {});
            }

            setInterval(poll, 2000);
        })();
    </script>
</x-dashboardlayout>
