<?php

namespace App\Http\Controllers;

use App\Models\LostItemReport;
use App\Models\FoundItemReport;
use Illuminate\Http\Request;
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

        LostItemReport::create([
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

        return redirect()->route('reports.my')
                         ->with('success', 'Lost item report submitted successfully.');
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

        FoundItemReport::create([
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

        return redirect()->route('reports.my')
                         ->with('success', 'Found item report submitted successfully.');
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
            $report = LostItemReport::where('id', $id)
                                     ->where('user_id', Auth::id())
                                     ->firstOrFail();
        } else {
            $report = FoundItemReport::where('id', $id)
                                      ->where('user_id', Auth::id())
                                      ->firstOrFail();
        }

        return view('reports.show', compact('report', 'type'));
    }
}