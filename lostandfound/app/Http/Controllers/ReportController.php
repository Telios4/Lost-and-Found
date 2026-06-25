<?php

namespace App\Http\Controllers;

use App\Models\LostItemReport;
use App\Models\FoundItemReport;
use App\Notifications\PotentialMatchFound;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ReportController extends Controller
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

    // Show lost item report form
    public function createLost()
    {
        $categories = $this->categories;
        return view('reports.create-lost', compact('categories'));
    }

    // Store lost item report
    public function storeLost(Request $request)
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

        $report = LostItemReport::create([
            'id'          => (string) Str::uuid(),
            'user_id'     => Auth::id(),
            'item_name'   => $validated['item_name'],
            'category'    => $validated['category'],
            'description' => $validated['description'],
            'location'    => $validated['location'],
            'date_lost'   => $validated['date_lost'],
            'photo_url'   => $photoUrl,
            'status'      => 'active',
        ]);

        $this->notifyPotentialMatchesForLost($report);

        return redirect()->route('reports.my')
                         ->with('success', 'Lost item report submitted successfully. Notifications were sent for potential matches.');
    }

    // Show found item report form
    public function createFound()
    {
        $categories = $this->categories;
        return view('reports.create-found', compact('categories'));
    }

    // Store found item report
    public function storeFound(Request $request)
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

        $report = FoundItemReport::create([
            'id'          => (string) Str::uuid(),
            'user_id'     => Auth::id(),
            'item_name'   => $validated['item_name'],
            'category'    => $validated['category'],
            'description' => $validated['description'],
            'location'    => $validated['location'],
            'date_found'  => $validated['date_found'],
            'photo_url'   => $photoUrl,
            'status'      => 'active',
        ]);

        $this->notifyPotentialMatchesForFound($report);

        return redirect()->route('reports.my')
                         ->with('success', 'Found item report submitted successfully. Notifications were sent for potential matches.');
    }

    // List user's own reports
    public function myReports()
    {
        $lostReports  = LostItemReport::where('user_id', Auth::id())
                                       ->latest()
                                       ->get();
        $foundReports = FoundItemReport::where('user_id', Auth::id())
                                        ->latest()
                                        ->get();

        return view('reports.my-reports', compact('lostReports', 'foundReports'));
    }

    // Show single report
    public function show(string $type, string $id)
    {
        if ($type === 'lost') {
            $report = LostItemReport::with('user')->where('id', $id)->firstOrFail();
        } else {
            $report = FoundItemReport::with('user')->where('id', $id)->firstOrFail();
        }

        $isOwner = $report->user_id === Auth::id();
        if (! $isOwner && $report->status !== 'active') {
            abort(403);
        }

        $counterpartType = $type === 'lost' ? 'found' : 'lost';
        $counterpartCandidates = $type === 'lost'
            ? $this->getFoundCandidatesForLost($report)
            : $this->getLostCandidatesForFound($report);

        return view('reports.show', compact('report', 'type', 'isOwner', 'counterpartType', 'counterpartCandidates'));
    }

    public function search(Request $request)
    {
        $validated = $request->validate([
            'q'        => ['nullable', 'string', 'max:100'],
            'type'     => ['nullable', 'in:all,lost,found'],
            'category' => ['nullable', 'string', 'in:' . implode(',', $this->categories)],
        ]);

        $term = trim((string) ($validated['q'] ?? ''));
        $type = $validated['type'] ?? 'all';
        $category = $validated['category'] ?? null;

        $lostQuery = LostItemReport::query()->where('status', 'active');
        $foundQuery = FoundItemReport::query()->where('status', 'active');

        $this->applySearchFilters($lostQuery, $term, $category);
        $this->applySearchFilters($foundQuery, $term, $category);

        $lostResults = $type === 'found'
            ? collect()
            : $lostQuery->latest()->take(60)->get();

        $foundResults = $type === 'lost'
            ? collect()
            : $foundQuery->latest()->take(60)->get();

        $matches = $this->detectMatches(
            $lostQuery->latest()->take(30)->get(),
            $foundQuery->latest()->take(30)->get()
        );

        return view('reports.search', [
            'categories' => $this->categories,
            'term' => $term,
            'selectedType' => $type,
            'selectedCategory' => $category,
            'lostResults' => $lostResults,
            'foundResults' => $foundResults,
            'matches' => $matches,
        ]);
    }

    private function applySearchFilters(Builder $query, string $term, ?string $category): void
    {
        if ($term !== '') {
            $query->where(function (Builder $innerQuery) use ($term) {
                $innerQuery->where('item_name', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%")
                    ->orWhere('location', 'like', "%{$term}%")
                    ->orWhere('category', 'like', "%{$term}%");
            });
        }

        if ($category) {
            $query->where('category', $category);
        }
    }

    private function detectMatches(Collection $lostReports, Collection $foundReports): Collection
    {
        return $lostReports->map(function ($lostReport) use ($foundReports) {
            $candidates = $foundReports
                ->map(function ($foundReport) use ($lostReport) {
                    $score = $this->calculateMatchScore($lostReport, $foundReport);

                    return [
                        'report' => $foundReport,
                        'score' => $score,
                    ];
                })
                ->filter(fn (array $candidate) => $candidate['score'] >= 55)
                ->sortByDesc('score')
                ->take(3)
                ->values();

            return [
                'lost' => $lostReport,
                'candidates' => $candidates,
            ];
        })->filter(fn (array $matchSet) => $matchSet['candidates']->isNotEmpty())
            ->values();
    }

    private function getFoundCandidatesForLost(LostItemReport $lostReport): Collection
    {
        return FoundItemReport::query()
            ->where('status', 'active')
            ->where('id', '!=', $lostReport->id)
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
            ->where('id', '!=', $foundReport->id)
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

        $this->getFoundCandidatesForLost($lostReport)
            ->each(function (array $candidate) use ($lostReport, $lostOwner) {
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

        $this->getLostCandidatesForFound($foundReport)
            ->each(function (array $candidate) use ($foundReport, $foundOwner) {
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