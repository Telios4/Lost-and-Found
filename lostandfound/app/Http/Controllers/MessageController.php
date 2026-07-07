<?php

namespace App\Http\Controllers;

use App\Models\FoundItemReport;
use App\Models\LostItemReport;
use App\Models\Message;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
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

        $contextType = $request->query('context_type');
        $contextId = $request->query('context_id');

        abort_unless($this->canMessageUser(Auth::user(), $user, $contextType, $contextId), 403);

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

        if (! $this->canMessageUser($currentUser, $user, $validated['context_type'] ?? null, $validated['context_id'] ?? null)) {
            return redirect()->route('reports.search')
                ->with('error', 'Messaging is only available when there is a possible match with one of your active reports.');
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

    private function canMessageUser(User $currentUser, User $otherUser, ?string $contextType = null, ?string $contextId = null): bool
    {
        if ($contextType && $contextId) {
            if ($contextType === 'lost') {
                $lostReport = LostItemReport::query()
                    ->where('id', $contextId)
                    ->where('user_id', $otherUser->id)
                    ->where('status', 'active')
                    ->first();

                if (! $lostReport) {
                    return false;
                }

                return FoundItemReport::query()
                    ->where('user_id', $currentUser->id)
                    ->where('status', 'active')
                    ->latest()
                    ->take(40)
                    ->get()
                    ->contains(fn (FoundItemReport $foundReport) => $this->calculateMatchScore($lostReport, $foundReport) >= 55);
            }

            if ($contextType === 'found') {
                $foundReport = FoundItemReport::query()
                    ->where('id', $contextId)
                    ->where('user_id', $otherUser->id)
                    ->where('status', 'active')
                    ->first();

                if (! $foundReport) {
                    return false;
                }

                return LostItemReport::query()
                    ->where('user_id', $currentUser->id)
                    ->where('status', 'active')
                    ->latest()
                    ->take(40)
                    ->get()
                    ->contains(fn (LostItemReport $lostReport) => $this->calculateMatchScore($lostReport, $foundReport) >= 55);
            }
        }

        $currentLostReports = LostItemReport::query()
            ->where('user_id', $currentUser->id)
            ->where('status', 'active')
            ->latest()
            ->take(40)
            ->get();

        $currentFoundReports = FoundItemReport::query()
            ->where('user_id', $currentUser->id)
            ->where('status', 'active')
            ->latest()
            ->take(40)
            ->get();

        $otherLostReports = LostItemReport::query()
            ->where('user_id', $otherUser->id)
            ->where('status', 'active')
            ->latest()
            ->take(40)
            ->get();

        $otherFoundReports = FoundItemReport::query()
            ->where('user_id', $otherUser->id)
            ->where('status', 'active')
            ->latest()
            ->take(40)
            ->get();

        $lostToFoundMatchExists = $currentLostReports->contains(function (LostItemReport $lostReport) use ($otherFoundReports) {
            return $otherFoundReports->contains(fn (FoundItemReport $foundReport) => $this->calculateMatchScore($lostReport, $foundReport) >= 55);
        });

        if ($lostToFoundMatchExists) {
            return true;
        }

        return $currentFoundReports->contains(function (FoundItemReport $foundReport) use ($otherLostReports) {
            return $otherLostReports->contains(fn (LostItemReport $lostReport) => $this->calculateMatchScore($lostReport, $foundReport) >= 55);
        });
    }

    private function calculateMatchScore(LostItemReport $lostReport, FoundItemReport $foundReport): int
    {
        $score = 0;

        if (strcasecmp($lostReport->category, $foundReport->category) === 0) {
            $score += 35;
        }

        similar_text(
            Str::lower($lostReport->item_name),
            Str::lower($foundReport->item_name),
            $nameSimilarity
        );
        $score += (int) round(($nameSimilarity / 100) * 25);

        similar_text(
            Str::lower(Str::limit($lostReport->description, 180, '')),
            Str::lower(Str::limit($foundReport->description, 180, '')),
            $descriptionSimilarity
        );
        $score += (int) round(($descriptionSimilarity / 100) * 20);

        $lostLocationTokens = collect(explode(' ', Str::lower($lostReport->location)))
            ->map(fn (string $token) => preg_replace('/[^a-z0-9]/', '', $token))
            ->filter(fn (?string $token) => $token && strlen($token) >= 3);

        $foundLocationTokens = collect(explode(' ', Str::lower($foundReport->location)))
            ->map(fn (string $token) => preg_replace('/[^a-z0-9]/', '', $token))
            ->filter(fn (?string $token) => $token && strlen($token) >= 3);

        $locationOverlap = $lostLocationTokens->intersect($foundLocationTokens)->count();
        if ($locationOverlap > 0) {
            $score += min(15, $locationOverlap * 5);
        }

        $daysApart = abs((int) Carbon::parse($lostReport->date_lost)->diffInDays(Carbon::parse($foundReport->date_found), false));
        if ($daysApart <= 30) {
            $score += max(1, 5 - intdiv($daysApart, 7));
        }

        return min(100, $score);
    }
}
