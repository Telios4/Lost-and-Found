<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FoundItemReport;
use App\Models\LostItemReport;
use App\Models\User;
use App\Notifications\PotentialMatchFound;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    private array $categories = [
        'Electronics',
        'Clothing & Accessories',
        'Documents & ID',
        'Books & Stationery',
        'Keys',
        'Wallet & Bags',
        'Jewellery',
        'Sports Equipment',
        'Other',
    ];

    public function index(): View
    {
        $totalLost     = LostItemReport::count();
        $totalFound    = FoundItemReport::count();
        $activeLost    = LostItemReport::where('status', 'active')->count();
        $activeFound   = FoundItemReport::where('status', 'active')->count();
        $resolvedCount = LostItemReport::where('status', 'resolved')->count();

        return view('admin.dashboard', compact(
            'totalLost',
            'totalFound',
            'activeLost',
            'activeFound',
            'resolvedCount'
        ));
    }

    public function createLost(): View
    {
        $categories = $this->categories;

        return view('admin.reports.create-lost', compact('categories'));
    }

    public function storeLost(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'item_name'   => ['required', 'string', 'max:255'],
            'category'    => ['required', 'string', 'in:' . implode(',', $this->categories)],
            'description' => ['required', 'string', 'min:10', 'max:1000'],
            'location'    => ['required', 'string', 'max:255'],
            'date_lost'   => ['required', 'date', 'before_or_equal:today'],
            'photo'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $photoUrl = null;
        if ($request->hasFile('photo')) {
            $photoUrl = $request->file('photo')->store('report-photos', 'public');
        }

        $systemUser = $this->getOrCreateAdminSystemUser();

        $report = LostItemReport::create([
            'id'          => (string) Str::uuid(),
            'user_id'     => $systemUser->id,
            'reviewed_by' => Session::get('admin_id'),
            'item_name'   => $validated['item_name'],
            'category'    => $validated['category'],
            'description' => $validated['description'],
            'location'    => $validated['location'],
            'date_lost'   => $validated['date_lost'],
            'photo_url'   => $photoUrl,
            'status'      => 'active',
        ]);

        $this->notifyPotentialMatchesForLost($report);

        return redirect()->route('admin.dashboard')
            ->with('success', 'Lost item report posted by admin.');
    }

    public function createFound(): View
    {
        $categories = $this->categories;

        return view('admin.reports.create-found', compact('categories'));
    }

    public function storeFound(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'item_name'   => ['required', 'string', 'max:255'],
            'category'    => ['required', 'string', 'in:' . implode(',', $this->categories)],
            'description' => ['required', 'string', 'min:10', 'max:1000'],
            'location'    => ['required', 'string', 'max:255'],
            'date_found'  => ['required', 'date', 'before_or_equal:today'],
            'photo'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $photoUrl = null;
        if ($request->hasFile('photo')) {
            $photoUrl = $request->file('photo')->store('report-photos', 'public');
        }

        $systemUser = $this->getOrCreateAdminSystemUser();

        $report = FoundItemReport::create([
            'id'          => (string) Str::uuid(),
            'user_id'     => $systemUser->id,
            'reviewed_by' => Session::get('admin_id'),
            'item_name'   => $validated['item_name'],
            'category'    => $validated['category'],
            'description' => $validated['description'],
            'location'    => $validated['location'],
            'date_found'  => $validated['date_found'],
            'photo_url'   => $photoUrl,
            'status'      => 'active',
        ]);

        $this->notifyPotentialMatchesForFound($report);

        return redirect()->route('admin.dashboard')
            ->with('success', 'Found item report posted by admin.');
    }

    private function getOrCreateAdminSystemUser(): User
    {
        $adminId = (string) Session::get('admin_id');
        $adminName = (string) Session::get('admin_name', 'Administrator');
        $systemEmail = sprintf('admin.%s@lostandfound.local', $adminId);

        return User::firstOrCreate(
            ['email' => $systemEmail],
            [
                'id' => (string) Str::uuid(),
                'name' => $adminName . ' (Admin)',
                'password' => bcrypt(Str::random(32)),
            ]
        );
    }

    private function getFoundCandidatesForLost(LostItemReport $lostReport): Collection
    {
        return FoundItemReport::query()
            ->where('status', 'active')
            ->latest()
            ->take(40)
            ->get()
            ->map(function (FoundItemReport $foundReport) use ($lostReport) {
                return [
                    'report' => $foundReport,
                    'score' => $this->calculateMatchScore($lostReport, $foundReport),
                ];
            })
            ->filter(fn (array $candidate) => $candidate['score'] >= 55)
            ->sortByDesc('score')
            ->take(4)
            ->values();
    }

    private function getLostCandidatesForFound(FoundItemReport $foundReport): Collection
    {
        return LostItemReport::query()
            ->where('status', 'active')
            ->latest()
            ->take(40)
            ->get()
            ->map(function (LostItemReport $lostReport) use ($foundReport) {
                return [
                    'report' => $lostReport,
                    'score' => $this->calculateMatchScore($lostReport, $foundReport),
                ];
            })
            ->filter(fn (array $candidate) => $candidate['score'] >= 55)
            ->sortByDesc('score')
            ->take(4)
            ->values();
    }

    private function notifyPotentialMatchesForLost(LostItemReport $lostReport): void
    {
        $lostOwner = $lostReport->user;
        if (! $lostOwner) {
            return;
        }

        $this->getFoundCandidatesForLost($lostReport)->each(function (array $candidate) use ($lostReport, $lostOwner) {
            /** @var FoundItemReport $foundReport */
            $foundReport = $candidate['report'];
            $score = $candidate['score'];

            $lostOwner->notify(new PotentialMatchFound(
                'lost',
                $lostReport->id,
                $lostReport->item_name,
                'found',
                $foundReport->id,
                $foundReport->item_name,
                $score
            ));

            if ($foundReport->user && $foundReport->user_id !== $lostOwner->id) {
                $foundReport->user->notify(new PotentialMatchFound(
                    'found',
                    $foundReport->id,
                    $foundReport->item_name,
                    'lost',
                    $lostReport->id,
                    $lostReport->item_name,
                    $score
                ));
            }
        });
    }

    private function notifyPotentialMatchesForFound(FoundItemReport $foundReport): void
    {
        $foundOwner = $foundReport->user;
        if (! $foundOwner) {
            return;
        }

        $this->getLostCandidatesForFound($foundReport)->each(function (array $candidate) use ($foundReport, $foundOwner) {
            /** @var LostItemReport $lostReport */
            $lostReport = $candidate['report'];
            $score = $candidate['score'];

            $foundOwner->notify(new PotentialMatchFound(
                'found',
                $foundReport->id,
                $foundReport->item_name,
                'lost',
                $lostReport->id,
                $lostReport->item_name,
                $score
            ));

            if ($lostReport->user && $lostReport->user_id !== $foundOwner->id) {
                $lostReport->user->notify(new PotentialMatchFound(
                    'lost',
                    $lostReport->id,
                    $lostReport->item_name,
                    'found',
                    $foundReport->id,
                    $foundReport->item_name,
                    $score
                ));
            }
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