<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = Auth::user();
        abort_unless($user, 401);

        $tab = $request->string('tab')->toString() ?: 'all';
        $allowedTabs = ['all', 'unread', 'read'];

        if (!in_array($tab, $allowedTabs, true)) {
            $tab = 'all';
        }

        $baseQuery = $user->notifications()->latest();

        $counts = [
            'all' => (clone $baseQuery)->count(),
            'unread' => (clone $baseQuery)->whereNull('read_at')->count(),
            'read' => (clone $baseQuery)->whereNotNull('read_at')->count(),
        ];

        $notificationsQuery = match ($tab) {
            'unread' => (clone $baseQuery)->whereNull('read_at'),
            'read' => (clone $baseQuery)->whereNotNull('read_at'),
            default => clone $baseQuery,
        };

        $notifications = $notificationsQuery->paginate(10)->withQueryString();

        return view('notifications.index', [
            'counts' => $counts,
            'notifications' => $notifications,
            'tab' => $tab,
        ]);
    }

    public function markAsRead(string $id): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        abort_unless($user, 401);

        $notification = $user->notifications()->findOrFail($id);
        $notification->markAsRead();

        return back()->with('status', 'Notification marked as read.');
    }

    public function markAsUnread(string $id): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        abort_unless($user, 401);

        $notification = $user->notifications()->findOrFail($id);
        $notification->update(['read_at' => null]);

        return back()->with('status', 'Notification marked as unread.');
    }

    public function destroy(string $id): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        abort_unless($user, 401);

        $notification = $user->notifications()->findOrFail($id);
        $notification->delete();

        return back()->with('status', 'Notification dismissed.');
    }

    public function markAllAsRead(): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        abort_unless($user, 401);

        $user->unreadNotifications->markAsRead();

        return back()->with('status', 'All notifications marked as read.');
    }
}
