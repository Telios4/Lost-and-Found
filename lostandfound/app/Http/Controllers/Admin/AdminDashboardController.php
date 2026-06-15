<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LostItemReport;
use App\Models\FoundItemReport;

class AdminDashboardController extends Controller
{
    public function index()
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
}