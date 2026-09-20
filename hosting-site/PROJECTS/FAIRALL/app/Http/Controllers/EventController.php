<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventType;
use App\Models\Program;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $isAdminOrDirector = $user?->staff?->hasAnyRole(['administrator', 'executive_director']);

        $query = Event::with(['program', 'eventType', 'creator', 'updater'])
            ->withCount('attendances');

        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhereHas('program', fn($p) => $p->where('program_name', 'like', "%{$search}%"))
                    ->orWhereHas('eventType', fn($t) => $t->where('name', 'like', "%{$search}%"));
            });
        }

        $filter = request('filter', 'upcoming');

        if ($filter === 'completed') {
            $query->where('end', '<', now());
        } else {
            $query->where(function ($q) {
                $q->whereNull('end')
                    ->orWhere('end', '>=', now());
            });
        }

        if (!$isAdminOrDirector && $user?->staff?->department) {
            $deptName = Str::lower(trim($user->staff->department->name));
            $program = Program::query()
                ->whereRaw('LOWER(TRIM(program_name)) = ?', [$deptName])
                ->first();

            if ($program) {
                $query->where(function ($q) use ($program) {
                    $q->where('program_id', $program->id)
                        ->orWhereNull('program_id');
                });
            }
        }

        $events = $query->orderByDesc('start')->paginate(12, ['*'], 'events_page')->withQueryString();

        return view('events.index', [
            'name' => $user?->display_name ?? 'Staff',
            'events' => $events,
            'currentFilter' => $filter,
        ]);
    }

    public function show(Request $request, Event $event): View
    {
        $event->load([
            'program',
            'eventType',
            'creator',
            'updater',
        ]);

        $attendances = $event->attendances()
            ->with(['beneficiary.user', 'staff.department', 'updater'])
            ->orderBy('id')
            ->paginate(10, ['*'], 'attendance_page');

        return view('events.show', [
            'name' => Auth::user()?->display_name ?? 'Staff',
            'event' => $event,
            'attendances' => $attendances,
        ]);
    }

    public function create(): View
    {
        $user = Auth::user();
        $isAdminOrDirector = $user?->staff?->hasAnyRole(['administrator', 'executive_director']);
        $programs = Program::orderBy('program_name')->get();
        $eventTypes = EventType::orderBy('name')->get();

        $userProgram = null;
        if (!$isAdminOrDirector && $user?->staff?->department) {
            $deptName = Str::lower(trim($user->staff->department->name));
            $userProgram = $programs->first(function ($program) use ($deptName) {
                return Str::lower(trim($program->program_name)) === $deptName;
            });
        }

        return view('events.create', [
            'name' => $user?->display_name ?? 'Staff',
            'event' => new Event(),
            'eventTypes' => $eventTypes,
            'programs' => $programs,
            'showProgramSelector' => $isAdminOrDirector || !$userProgram,
            'userProgram' => $userProgram,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'program_id' => ['nullable', 'integer', 'exists:programs,id'],
            'event_type_id' => ['required', 'integer', 'exists:event_types,id'],
            'description' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'start' => ['required', 'date_format:Y-m-d\\TH:i'],
            'end' => ['nullable', 'date_format:Y-m-d\\TH:i'],
        ]);

        $user = Auth::user();
        $isAdminOrDirector = $user?->staff?->hasAnyRole(['administrator', 'executive_director']);

        if (!$isAdminOrDirector && $user?->staff?->department) {
            $deptName = Str::lower(trim($user->staff->department->name));
            $userProgram = Program::query()
                ->whereRaw('LOWER(TRIM(program_name)) = ?', [$deptName])
                ->first();

            if ($userProgram) {
                $validated['program_id'] = $userProgram->id;
            }
        }

        Event::create([
            'name' => $validated['name'],
            'program_id' => $validated['program_id'] ?? null,
            'event_type_id' => $validated['event_type_id'],
            'description' => $validated['description'] ?? null,
            'location' => $validated['location'] ?? null,
            'start' => $validated['start'],
            'end' => $validated['end'] ?? null,
            'qr_token' => Str::random(40),
            'created_by' => $user?->staff?->id,
            'updated_by' => $user?->staff?->id,
        ]);

        return redirect()->route('events.index')->with('status', 'Event added.');
    }

    public function edit(Event $event): View
    {
        $user = Auth::user();
        $isAdminOrDirector = $user?->staff?->hasAnyRole(['administrator', 'executive_director']);
        $programs = Program::orderBy('program_name')->get();
        $eventTypes = EventType::orderBy('name')->get();

        $userProgram = null;
        if (!$isAdminOrDirector && $user?->staff?->department) {
            $deptName = Str::lower(trim($user->staff->department->name));
            $userProgram = $programs->first(function ($program) use ($deptName) {
                return Str::lower(trim($program->program_name)) === $deptName;
            });
        }

        return view('events.edit', [
            'name' => $user?->display_name ?? 'Staff',
            'event' => $event->load(['program', 'eventType']),
            'eventTypes' => $eventTypes,
            'programs' => $programs,
            'showProgramSelector' => $isAdminOrDirector || !$userProgram,
            'userProgram' => $userProgram,
        ]);
    }

    public function update(Request $request, Event $event): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'program_id' => ['nullable', 'integer', 'exists:programs,id'],
            'event_type_id' => ['required', 'integer', 'exists:event_types,id'],
            'description' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'start' => ['required', 'date_format:Y-m-d\\TH:i'],
            'end' => ['nullable', 'date_format:Y-m-d\\TH:i'],
        ]);

        $user = Auth::user();
        $isAdminOrDirector = $user?->staff?->hasAnyRole(['administrator', 'executive_director']);

        if (!$isAdminOrDirector && $user?->staff?->department) {
            $deptName = Str::lower(trim($user->staff->department->name));
            $userProgram = Program::query()
                ->whereRaw('LOWER(TRIM(program_name)) = ?', [$deptName])
                ->first();

            if ($userProgram) {
                $validated['program_id'] = $userProgram->id;
            }
        }

        $event->update([
            'name' => $validated['name'],
            'program_id' => $validated['program_id'] ?? null,
            'event_type_id' => $validated['event_type_id'],
            'description' => $validated['description'] ?? null,
            'location' => $validated['location'] ?? null,
            'start' => $validated['start'],
            'end' => $validated['end'] ?? null,
            'updated_by' => $user?->staff?->id,
        ]);

        return redirect()->route('events.show', $event)->with('status', 'Event updated.');
    }

    public function destroy(Event $event): RedirectResponse
    {
        $event->delete();

        return redirect()->route('events.index')->with('status', 'Event deleted.');
    }

    /**
     * Regenerate QR token for event.
     */
    public function regenerateQr(Request $request, Event $event): RedirectResponse
    {
        Gate::authorize('manage-events');

        $event->update([
            'qr_token' => Str::random(40),
            'updated_by' => Auth::user()?->staff?->id,
        ]);

        return redirect()->route('events.show', $event)->with('status', 'QR regenerated.');
    }

    /**
     * Printable QR view.
     */
    public function showQr(Event $event)
    {
        return view('qr.printable', [
            'token' => $event->qr_token,
            'eventName' => $event->name
        ]);
    }
}
