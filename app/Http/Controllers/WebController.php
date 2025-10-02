<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\Report;
use App\Models\User;

class WebController extends Controller
{
    // Dashboard page
    public function dashboard()
    {
        // Get dashboard statistics
        $stats = [
            'trees_planted' => 127,
            'co2_saved' => '2.3 tons',
            'impact_score' => 847
        ];

        return view('pages.dashboard', compact('stats'));
    }

    // Map page
    public function map()
    {
        $reports = Report::with('user')->latest()->get();
        return view('pages.map', compact('reports'));
    }

    // Reports page
    public function reports()
    {
        $reports = Report::with('user')->latest()->paginate(10);
        $statistics = [
            'total_reports' => Report::count(),
            'pending_reports' => Report::where('status', 'pending')->count(),
            'validated_reports' => Report::where('status', 'validated')->count(),
            'this_month' => Report::whereMonth('created_at', date('m'))->count()
        ];
        
        return view('pages.reports', compact('reports', 'statistics'));
    }

    // Trees page
    public function trees()
    {
        return redirect()->route('trees.index');
    }
}