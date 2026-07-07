<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CaseResolution;
use App\Models\FoundItemReport;
use App\Models\LostItemReport;
use App\Models\User;
use App\Notifications\PotentialMatchFound;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
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
        $resolvedCount = LostItemReport::where('status', 'resolved')->count()
            + FoundItemReport::where('status', 'resolved')->count();
        $recentLostReports = LostItemReport::query()
            ->with('user:id,name')
            ->latest()
            ->take(8)
            ->get();
        $recentFoundReports = FoundItemReport::query()
            ->with('user:id,name')
            ->latest()
            ->take(8)
            ->get();

        $sinceDate = Carbon::today()->subDays(6);

        $lostByDay = LostItemReport::query()
            ->selectRaw('DATE(created_at) as activity_date, COUNT(*) as total')
            ->whereDate('created_at', '>=', $sinceDate)
            ->groupBy('activity_date')
            ->pluck('total', 'activity_date');

        $foundByDay = FoundItemReport::query()
            ->selectRaw('DATE(created_at) as activity_date, COUNT(*) as total')
            ->whereDate('created_at', '>=', $sinceDate)
            ->groupBy('activity_date')
            ->pluck('total', 'activity_date');

        $resolvedByDay = CaseResolution::query()
            ->selectRaw('DATE(resolved_at) as activity_date, COUNT(*) as total')
            ->whereNotNull('resolved_at')
            ->whereDate('resolved_at', '>=', $sinceDate)
            ->groupBy('activity_date')
            ->pluck('total', 'activity_date');

        $activitySeries = collect(range(6, 0))
            ->map(function (int $dayOffset) use ($lostByDay, $foundByDay, $resolvedByDay) {
                $date = Carbon::today()->subDays($dayOffset);
                $key = $date->toDateString();

                return [
                    'day' => $date->format('D'),
                    'date' => $date->format('d M'),
                    'lost' => (int) ($lostByDay[$key] ?? 0),
                    'found' => (int) ($foundByDay[$key] ?? 0),
                    'resolved' => (int) ($resolvedByDay[$key] ?? 0),
                ];
            })
            ->values();

        $maxActivityCount = (int) max(1, $activitySeries
            ->flatMap(function (array $dayStats) {
                return [$dayStats['lost'], $dayStats['found'], $dayStats['resolved']];
            })
            ->max());

        return view('admin.dashboard', compact(
            'totalLost',
            'totalFound',
            'activeLost',
            'activeFound',
            'resolvedCount',
            'recentLostReports',
            'recentFoundReports',
            'activitySeries',
            'maxActivityCount'
        ));
    }

    public function reportsIndex(): View
    {
        $lostReports = LostItemReport::query()
            ->with('user:id,name')
            ->latest()
            ->paginate(15, ['*'], 'lost_page');

        $foundReports = FoundItemReport::query()
            ->with('user:id,name')
            ->latest()
            ->paginate(15, ['*'], 'found_page');

        return view('admin.reports.index', compact('lostReports', 'foundReports'));
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

    public function showReport(string $type, string $id): View
    {
        $report = $this->findReportByType($type, $id);
        $counterpartType = $type === 'lost' ? 'found' : 'lost';
        $counterpartCandidates = $type === 'lost'
            ? $this->getFoundCandidatesForLost($report)
            : $this->getLostCandidatesForFound($report);

        return view('admin.reports.show', compact('report', 'type', 'counterpartType', 'counterpartCandidates'));
    }

    public function resolveReport(Request $request, string $type, string $id): RedirectResponse
    {
        $report = $this->findReportByType($type, $id);

        $validated = $request->validate([
            'counterpart_report_id' => ['nullable', 'uuid'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $counterpartReport = null;
        if (! empty($validated['counterpart_report_id'])) {
            $counterpartReport = $type === 'lost'
                ? FoundItemReport::query()->where('id', $validated['counterpart_report_id'])->firstOrFail()
                : LostItemReport::query()->where('id', $validated['counterpart_report_id'])->firstOrFail();
        }

        DB::transaction(function () use ($report, $counterpartReport, $type, $validated) {
            $report->status = 'resolved';
            $report->save();

            if ($counterpartReport && $counterpartReport->status !== 'resolved') {
                $counterpartReport->status = 'resolved';
                $counterpartReport->save();
            }

            if ($counterpartReport) {
                CaseResolution::create([
                    'lost_report_id' => $type === 'lost' ? $report->id : $counterpartReport->id,
                    'found_report_id' => $type === 'found' ? $report->id : $counterpartReport->id,
                    'resolved_by_admin' => Session::get('admin_id'),
                    'notes' => $validated['notes'] ?? null,
                    'resolved_at' => now(),
                ]);
            }
        });

        return redirect()->route('admin.reports.show', ['type' => $type, 'id' => $id])
            ->with('success', 'Report marked as resolved.');
    }

    public function destroyReport(string $type, string $id): RedirectResponse
    {
        $report = $this->findReportByType($type, $id);

        if ($report->photo_url) {
            Storage::disk('public')->delete($report->photo_url);
        }

        $report->delete();

        return redirect()->route('admin.reports.index')
            ->with('success', 'Submission removed successfully.');
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
            $isAdminManagedFoundItem = ! empty($foundReport->reviewed_by);

            $lostOwner->notify(new PotentialMatchFound(
                'lost',
                $lostReport->id,
                $lostReport->item_name,
                'found',
                $foundReport->id,
                $foundReport->item_name,
                $score,
                $isAdminManagedFoundItem
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
            $isAdminManagedFoundItem = ! empty($foundReport->reviewed_by);

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
                    $score,
                    $isAdminManagedFoundItem
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

    private function findReportByType(string $type, string $id): LostItemReport|FoundItemReport
    {
        if (! in_array($type, ['lost', 'found'], true)) {
            abort(404);
        }

        return $type === 'lost'
            ? LostItemReport::with('user')->where('id', $id)->firstOrFail()
            : FoundItemReport::with('user')->where('id', $id)->firstOrFail();
    }
}