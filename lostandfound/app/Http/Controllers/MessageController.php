<?php

namespace App\Http\Controllers;

use App\Models\FoundItemReport;
use App\Models\LostItemReport;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function index(): View
    {
        $currentUserId = Auth::id();

        $messages = Message::query()
            ->where('sender_id', $currentUserId)
            ->orWhere('receiver_id', $currentUserId)
            ->with(['sender:id,name', 'receiver:id,name'])
            ->latest('created_at')
            ->get();

        $conversations = $messages
            ->groupBy(function (Message $message) use ($currentUserId) {
                return $message->sender_id === $currentUserId
                    ? $message->receiver_id
                    : $message->sender_id;
            })
            ->map(function (Collection $threadMessages) use ($currentUserId) {
                /** @var Message $latest */
                $latest = $threadMessages->first();
                $otherUser = $latest->sender_id === $currentUserId
                    ? $latest->receiver
                    : $latest->sender;

                return [
                    'user' => $otherUser,
                    'latest_message' => $latest,
                    'unread_count' => $threadMessages
                        ->where('receiver_id', $currentUserId)
                        ->where('read_at', null)
                        ->count(),
                ];
            })
            ->filter(fn (array $conversation) => $conversation['user'] !== null)
            ->sortByDesc(fn (array $conversation) => $conversation['latest_message']->created_at)
            ->values();

        return view('messages.index', compact('conversations'));
    }

    public function show(Request $request, User $user): View
    {
        $currentUserId = Auth::id();

        abort_if($user->id === $currentUserId, 404);

        $messages = Message::query()
            ->where(function ($query) use ($currentUserId, $user) {
                $query->where('sender_id', $currentUserId)
                    ->where('receiver_id', $user->id);
            })
            ->orWhere(function ($query) use ($currentUserId, $user) {
                $query->where('sender_id', $user->id)
                    ->where('receiver_id', $currentUserId);
            })
            ->with(['sender:id,name', 'receiver:id,name', 'lostReport:id,item_name', 'foundReport:id,item_name'])
            ->orderBy('created_at')
            ->get();

        Message::query()
            ->where('sender_id', $user->id)
            ->where('receiver_id', $currentUserId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $contextType = $request->query('context_type');
        $contextId = $request->query('context_id');

        return view('messages.show', [
            'otherUser' => $user,
            'messages' => $messages,
            'contextType' => $contextType,
            'contextId' => $contextId,
        ]);
    }

    public function store(Request $request, User $user): RedirectResponse
    {
        $currentUser = Auth::user();

        if ($user->id === $currentUser->id) {
            return back()->with('error', 'You cannot message yourself.');
        }

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
            'context_type' => ['nullable', Rule::in(['lost', 'found'])],
            'context_id' => ['nullable', 'uuid'],
        ]);

        $lostReportId = null;
        $foundReportId = null;

        if (($validated['context_type'] ?? null) === 'lost' && ! empty($validated['context_id'])) {
            LostItemReport::query()->where('id', $validated['context_id'])->firstOrFail();
            $lostReportId = $validated['context_id'];
        }

        if (($validated['context_type'] ?? null) === 'found' && ! empty($validated['context_id'])) {
            FoundItemReport::query()->where('id', $validated['context_id'])->firstOrFail();
            $foundReportId = $validated['context_id'];
        }

        Message::create([
            'sender_id' => $currentUser->id,
            'receiver_id' => $user->id,
            'lost_item_report_id' => $lostReportId,
            'found_item_report_id' => $foundReportId,
            'body' => $validated['body'],
            'sent_at' => now()->format('H:i:s'),
        ]);

        return redirect()->route('messages.show', $user)
            ->with('success', 'Message sent.');
    }
}
