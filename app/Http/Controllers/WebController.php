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
            'events_attended' => 23,
            'projects_joined' => 8,
            'impact_score' => 847,
            'co2_saved' => '2.3 tons',
            'badges_earned' => 5
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

    // Projects page
    public function projects()
    {
        // Mock data for now (can be replaced with actual Project model later)
        $projects = [
            [
                'id' => 1,
                'title' => 'Central Park Tree Restoration',
                'description' => 'Restore native tree species in Central Park',
                'location' => 'Central Park, NY',
                'status' => 'active',
                'progress' => 75,
                'participants' => 45,
                'target_participants' => 60,
                'start_date' => '2024-01-15',
                'end_date' => '2024-06-15'
            ],
            [
                'id' => 2,
                'title' => 'Brooklyn Rooftop Gardens',
                'description' => 'Create rooftop gardens across Brooklyn',
                'location' => 'Brooklyn, NY',
                'status' => 'active',
                'progress' => 45,
                'participants' => 32,
                'target_participants' => 50,
                'start_date' => '2024-02-01',
                'end_date' => '2024-08-01'
            ]
        ];

        return view('pages.projects', compact('projects'));
    }

    // Project detail page
    public function projectDetail($id)
    {
        // Mock data for now
        $project = [
            'id' => $id,
            'title' => 'Central Park Tree Restoration',
            'description' => 'Restore native tree species in Central Park',
            'location' => 'Central Park, NY',
            'status' => 'active',
            'progress' => 75,
            'participants' => 45,
            'target_participants' => 60,
            'start_date' => '2024-01-15',
            'end_date' => '2024-06-15'
        ];

        return view('pages.project-detail', compact('project'));
    }

    // Events page
    public function events()
    {
        // Mock data for now
        $events = [
            [
                'id' => 1,
                'title' => 'Community Garden Workshop',
                'description' => 'Learn sustainable gardening practices',
                'location' => 'Brooklyn Community Center',
                'date' => '2024-03-15',
                'time' => '10:00',
                'attendees' => 25,
                'max_attendees' => 40
            ],
            [
                'id' => 2,
                'title' => 'Tree Planting Day',
                'description' => 'Join us for a day of tree planting',
                'location' => 'Prospect Park',
                'date' => '2024-03-22',
                'time' => '09:00',
                'attendees' => 67,
                'max_attendees' => 80
            ]
        ];

        return view('pages.events', compact('events'));
    }

    // Event detail page
    public function eventDetail($id)
    {
        // Mock data for now
        $event = [
            'id' => $id,
            'title' => 'Community Garden Workshop',
            'description' => 'Learn sustainable gardening practices',
            'location' => 'Brooklyn Community Center',
            'date' => '2024-03-15',
            'time' => '10:00',
            'attendees' => 25,
            'max_attendees' => 40
        ];

        return view('pages.event-detail', compact('event'));
    }

    // Feedback page
    public function feedback()
    {
        return view('pages.feedback');
    }

    // Impact page
    public function impact()
    {
        $impactData = [
            'trees_planted' => 127,
            'co2_saved' => 2.3,
            'community_members' => 245,
            'projects_completed' => 12,
            'monthly_growth' => 15.4
        ];

        return view('pages.impact', compact('impactData'));
    }
}