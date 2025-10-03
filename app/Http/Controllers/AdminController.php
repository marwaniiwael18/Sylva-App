<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Event;
use App\Models\Donation;
use App\Models\ForumPost;
use App\Models\Tree;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Le middleware admin est appliqué via les routes
     */

    /**
     * Dashboard administrateur avec vue d'ensemble complète
     */
    public function dashboard()
    {
        // Statistiques globales du système
        $globalStats = [
            'total_users' => User::count(),
            'new_users_this_month' => User::whereMonth('created_at', now()->month)->count(),
            'admin_users' => User::where('is_admin', true)->count(),
            'moderator_users' => User::where('is_moderator', true)->count(),
            
            'total_events' => Event::count(),
            'active_events' => Event::where('status', 'active')->count(),
            'upcoming_events' => Event::where('date', '>', now())->where('status', 'active')->count(),
            'events_this_month' => Event::whereMonth('created_at', now()->month)->count(),
            
            'total_donations' => Donation::where('payment_status', 'succeeded')->sum('amount'),
            'total_donations_count' => Donation::where('payment_status', 'succeeded')->count(),
            'donations_this_month' => Donation::where('payment_status', 'succeeded')
                ->whereMonth('created_at', now()->month)
                ->sum('amount'),
            'avg_donation' => Donation::where('payment_status', 'succeeded')->avg('amount'),
            
            'total_trees' => Tree::count(),
            'trees_this_month' => Tree::whereMonth('planting_date', now()->month)->count(),
            'healthy_trees' => Tree::where('status', 'healthy')->count(),
            
            'total_forum_posts' => ForumPost::count(),
            'posts_this_month' => ForumPost::whereMonth('created_at', now()->month)->count(),
            
            'total_reports' => Report::count(),
            'pending_reports' => Report::where('status', 'pending')->count(),
            'validated_reports' => Report::where('status', 'validated')->count(),
            'rejected_reports' => Report::where('status', 'rejected')->count(),
        ];

        // Activité récente (tous types)
        $recentActivity = collect()
            ->merge(
                User::latest()->take(5)->get()->map(function ($user) {
                    return [
                        'type' => 'user_registration',
                        'title' => "Nouvel utilisateur : {$user->name}",
                        'description' => "S'est inscrit",
                        'created_at' => $user->created_at,
                        'icon' => 'user-plus',
                        'color' => 'blue'
                    ];
                })
            )
            ->merge(
                Event::latest()->take(3)->get()->map(function ($event) {
                    return [
                        'type' => 'event_created',
                        'title' => "Nouvel événement : {$event->title}",
                        'description' => "Créé pour le {$event->date->format('d/m/Y')}",
                        'created_at' => $event->created_at,
                        'icon' => 'calendar-plus',
                        'color' => 'green'
                    ];
                })
            )
            ->merge(
                Donation::where('payment_status', 'succeeded')->latest()->take(3)->get()->map(function ($donation) {
                    return [
                        'type' => 'donation',
                        'title' => "Nouvelle donation : {$donation->amount}€",
                        'description' => "Par {$donation->user->name}",
                        'created_at' => $donation->created_at,
                        'icon' => 'heart',
                        'color' => 'purple'
                    ];
                })
            )
            ->merge(
                Report::latest()->take(3)->get()->map(function ($report) {
                    return [
                        'type' => 'report',
                        'title' => "Nouveau rapport : {$report->title}",
                        'description' => "Status: {$report->status}",
                        'created_at' => $report->created_at,
                        'icon' => 'flag',
                        'color' => 'yellow'
                    ];
                })
            )
            ->sortByDesc('created_at')
            ->take(10);

        // Graphiques pour les 6 derniers mois
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyData[] = [
                'month' => $date->format('M'),
                'users' => User::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)->count(),
                'events' => Event::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)->count(),
                'donations' => Donation::where('payment_status', 'succeeded')
                    ->whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->sum('amount'),
                'reports' => Report::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)->count(),
            ];
        }

        // Utilisateurs les plus actifs
        $topUsers = User::withCount(['donations', 'organizedEvents', 'forumPosts'])
            ->orderByDesc('donations_count')
            ->orderByDesc('organized_events_count')
            ->orderByDesc('forum_posts_count')
            ->take(5)
            ->get();

        // Rapports nécessitant une attention
        $pendingReports = Report::with('user')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'globalStats',
            'recentActivity', 
            'monthlyData',
            'topUsers',
            'pendingReports'
        ));
    }

    /**
     * Gestion des utilisateurs
     */
    public function users()
    {
        $users = User::withCount(['donations', 'organizedEvents', 'forumPosts'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.users', compact('users'));
    }

    /**
     * Gestion des rapports
     */
    public function reports()
    {
        $reports = Report::with('user', 'validator')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $reportStats = [
            'pending' => Report::where('status', 'pending')->count(),
            'validated' => Report::where('status', 'validated')->count(),
            'rejected' => Report::where('status', 'rejected')->count(),
            'total' => Report::count(),
        ];

        return view('admin.reports', compact('reports', 'reportStats'));
    }

    /**
     * Valider un rapport
     */
    public function validateReport(Request $request, Report $report)
    {
        $request->validate([
            'action' => 'required|in:validate,reject',
            'notes' => 'nullable|string|max:1000'
        ]);

        $report->update([
            'status' => $request->action === 'validate' ? 'validated' : 'rejected',
            'validated_by' => Auth::id(),
            'validated_at' => now(),
            'validation_notes' => $request->notes
        ]);

        return redirect()->back()->with('success', 
            $request->action === 'validate' 
                ? 'Rapport validé avec succès.' 
                : 'Rapport rejeté.'
        );
    }

    /**
     * Modifier le rôle d'un utilisateur
     */
    public function updateUserRole(Request $request, User $user)
    {
        $request->validate([
            'is_admin' => 'boolean',
            'is_moderator' => 'boolean'
        ]);

        $user->update([
            'is_admin' => $request->boolean('is_admin'),
            'is_moderator' => $request->boolean('is_moderator')
        ]);

        return redirect()->back()->with('success', 'Rôle utilisateur mis à jour.');
    }
}