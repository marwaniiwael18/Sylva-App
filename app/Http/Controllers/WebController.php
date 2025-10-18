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
use Illuminate\Support\Str;

use Illuminate\Support\Facades\Http;

use App\Models\ReportActivity;


class WebController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        
        // Statistiques personnelles de l'utilisateur seulement
        $userStats = [
            'my_donations' => $user->donations()->where('payment_status', 'succeeded')->count(),
            'my_donation_amount' => $user->donations()->where('payment_status', 'succeeded')->sum('amount'),
            'my_events_organized' => $user->organizedEvents()->count(),
            'my_events_participating' => $user->participatingEvents()->count(),
            'my_forum_posts' => $user->forumPosts()->count(),
            'my_trees' => Tree::where('planted_by_user', $user->id)->count(),
            'my_reports' => Report::where('user_id', $user->id)->count(),
            'impact_score' => $this->calculateImpactScore($user),
        ];

        // Mes événements récents (organisés)
        $myRecentEvents = $user->organizedEvents()
            ->orderBy('date', 'desc')
            ->limit(3)
            ->get();

        // Mes événements à venir (participant)
        $myUpcomingEvents = $user->participatingEvents()
            ->where('date', '>', now())
            ->orderBy('date', 'asc')
            ->limit(3)
            ->get();

        // Mes donations récentes
        $myRecentDonations = $user->donations()
            ->where('payment_status', 'succeeded')
            ->with('event')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Mes posts du forum récents
        $myRecentForumPosts = $user->forumPosts()
            ->with('relatedEvent')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Mes rapports récents
        $myRecentReports = Report::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Graphique de mes donations par mois (6 derniers mois)
        $myDonationsByMonth = $user->donations()
            ->where('payment_status', 'succeeded')
            ->where('created_at', '>=', now()->subMonths(6))
            ->selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, SUM(amount) as total')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        // Mes activités récentes par type
        $myActivities = collect()
            ->merge(
                $myRecentDonations->map(function ($donation) {
                    return [
                        'type' => 'donation',
                        'title' => "Donation de {$donation->amount}€",
                        'description' => $donation->event ? "Pour l'événement: {$donation->event->title}" : "Donation générale",
                        'created_at' => $donation->created_at,
                        'icon' => 'heart',
                        'color' => 'purple'
                    ];
                })
            )
            ->merge(
                $myRecentEvents->map(function ($event) {
                    return [
                        'type' => 'event_created',
                        'title' => "Événement organisé: {$event->title}",
                        'description' => "Prévu le {$event->date->format('d/m/Y')}",
                        'created_at' => $event->created_at,
                        'icon' => 'calendar-plus',
                        'color' => 'green'
                    ];
                })
            )
            ->merge(
                $myRecentForumPosts->map(function ($post) {
                    return [
                        'type' => 'forum_post',
                        'title' => "Post forum: {$post->title}",
                        'description' => Str::limit($post->content, 50),
                        'created_at' => $post->created_at,
                        'icon' => 'message-square',
                        'color' => 'blue'
                    ];
                })
            )
            ->merge(
                $myRecentReports->map(function ($report) {
                    return [
                        'type' => 'report',
                        'title' => "Rapport: {$report->title}",
                        'description' => "Status: {$report->status}",
                        'created_at' => $report->created_at,
                        'icon' => 'flag',
                        'color' => 'yellow'
                    ];
                })
            )
            ->sortByDesc('created_at')
            ->take(8);

        return view('pages.dashboard', compact(
            'userStats',
            'myRecentEvents',
            'myUpcomingEvents', 
            'myRecentDonations',
            'myRecentForumPosts',
            'myRecentReports',
            'myDonationsByMonth',
            'myActivities'
        ));
    }

    public function map()
    {
        $user = Auth::user();
        
        // Récupérer tous les arbres avec leurs coordonnées
        $trees = Tree::select('id', 'species', 'latitude', 'longitude', 'planting_date', 'status')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        // Récupérer les événements avec localisation
        $events = Event::select('id', 'title', 'date', 'location', 'type')
            ->where('status', 'active')
            ->where('date', '>=', now())
            ->get();

        // Récupérer les rapports avec localisation selon les permissions
        if ($user && $user->canValidateReports()) {
            // Admin/Modérateur : voir tous les rapports avec coordonnées
            $reports = Report::with('user')
                ->select('id', 'title', 'type', 'status', 'latitude', 'longitude', 'address', 'urgency', 'user_id', 'created_at')
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            // Utilisateur normal : voir ses propres rapports + les rapports validés des autres avec coordonnées
            $reports = Report::with('user')
                ->select('id', 'title', 'type', 'status', 'latitude', 'longitude', 'address', 'urgency', 'user_id', 'created_at')
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->where(function($query) use ($user) {
                    if ($user) {
                        $query->where('user_id', $user->id) // Ses propres rapports
                              ->orWhere('status', 'validated'); // Rapports validés des autres
                    } else {
                        $query->where('status', 'validated'); // Invités : seulement rapports validés
                    }
                })
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('pages.map', compact('trees', 'events', 'reports'));
    }

    public function reports()
    {
        $user = Auth::user();
        
        if ($user->canValidateReports()) {
            // Admin/Modérateur : voir tous les rapports
            $reports = Report::with('user')
                ->orderBy('created_at', 'desc')
                ->paginate(20);
                
            $statistics = [
                'pending_reports' => Report::where('status', 'pending')->count(),
                'validated_reports' => Report::where('status', 'validated')->count(),
                'rejected_reports' => Report::where('status', 'rejected')->count(),
                'total_reports' => Report::count(),
                'this_month' => Report::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count(),
            ];
        } else {
            // Utilisateur normal : voir ses propres rapports + les rapports validés des autres
            $reports = Report::with('user')
                ->where(function($query) use ($user) {
                    $query->where('user_id', $user->id) // Ses propres rapports (tous statuts)
                          ->orWhere('status', 'validated'); // Rapports validés des autres
                })
                ->orderBy('created_at', 'desc')
                ->paginate(20);
                
            $userReportsCount = Report::where('user_id', $user->id)->count();
            $userValidatedReports = Report::where('user_id', $user->id)->where('status', 'validated')->count();
            $userPendingReports = Report::where('user_id', $user->id)->where('status', 'pending')->count();
            
            $statistics = [
                'pending_reports' => $userPendingReports, // Ses rapports en attente
                'validated_reports' => Report::where('status', 'validated')->count(), // Tous les rapports validés
                'rejected_reports' => Report::where('user_id', $user->id)->where('status', 'rejected')->count(), // Ses rapports rejetés
                'total_reports' => $userReportsCount, // Ses propres rapports
                'this_month' => Report::where('user_id', $user->id)
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count(), // Ses rapports ce mois
            ];
        }

        return view('pages.reports', compact('reports', 'statistics'));
    }

    // Repports Feed page
    public function communityFeed()
    {
        // Get all reports with their activities
        $reports = Report::with(['user', 'activities.user'])
            ->withCount(['comments', 'votes', 'reactions'])
            ->latest()
            ->paginate(10);
        
        // Get overall statistics
        $statistics = [
            'total_comments' => ReportActivity::where('activity_type', 'comment')->count(),
            'total_votes' => ReportActivity::where('activity_type', 'vote')->count(),
            'total_reactions' => ReportActivity::where('activity_type', 'reaction')->count(),
            'active_discussions' => Report::has('comments')->count()
        ];
        
        return view('pages.community-feed', compact('reports', 'statistics'));
    }

    // Trees page
    public function trees()
    {
        return redirect()->route('trees.index');
    }

    /**
     * Calcule le score d'impact de l'utilisateur basé sur ses activités
     */
    private function calculateImpactScore($user)
    {
        $score = 0;
        
        // Points pour les donations (1 point par 10€)
        $donationScore = $user->donations()->where('payment_status', 'succeeded')->sum('amount') / 10;
        $score += $donationScore;
        
        // Points pour les événements organisés (50 points par événement)
        $eventsOrganized = $user->organizedEvents()->count();
        $score += $eventsOrganized * 50;
        
        // Points pour la participation aux événements (10 points par participation)
        $eventsParticipating = $user->participatingEvents()->count();
        $score += $eventsParticipating * 10;
        
        // Points pour les posts du forum (5 points par post)
        $forumPosts = $user->forumPosts()->count();
        $score += $forumPosts * 5;
        
        // Points pour les arbres plantés (20 points par arbre)
        $treesPlanted = Tree::where('planted_by_user', $user->id)->count();
        $score += $treesPlanted * 20;
        
        return round($score);
    }
}