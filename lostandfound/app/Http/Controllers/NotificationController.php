<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $notifications = $user
            ->notifications()
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead(string $id): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $notification = $user->notifications()->where('id', $id)->firstOrFail();

        if ($notification->read_at === null) {
            $notification->markAsRead();
        }

        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllAsRead(): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $user->unreadNotifications->markAsRead();

        return back()->with('success', 'All notifications marked as read.');
    }
}
