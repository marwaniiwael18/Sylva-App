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

class WebController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        
        // Statistics générales
        $stats = [
            'total_users' => User::count(),
            'total_events' => Event::count(),
            'active_events' => Event::where('status', 'active')->count(),
            'upcoming_events' => Event::where('date', '>', now())->where('status', 'active')->count(),
            'total_donations' => Donation::where('payment_status', 'succeeded')->sum('amount'),
            'total_donations_count' => Donation::where('payment_status', 'succeeded')->count(),
            'total_trees' => Tree::count(),
            'trees_planted' => Tree::where('planted_by_user', $user->id)->count(),
            'total_forum_posts' => ForumPost::count(),
            'pending_reports' => Report::where('status', 'pending')->count(),
            'impact_score' => $this->calculateImpactScore($user),
        ];

        // Statistiques utilisateur
        $userStats = [
            'my_donations' => $user->donations()->where('payment_status', 'succeeded')->count(),
            'my_donation_amount' => $user->donations()->where('payment_status', 'succeeded')->sum('amount'),
            'my_events_organized' => $user->organizedEvents()->count(),
            'my_events_participating' => $user->participatingEvents()->count(),
            'my_forum_posts' => $user->forumPosts()->count(),
            'my_trees' => Tree::where('planted_by_user', $user->id)->count(),
        ];

        // Événements récents
        $recentEvents = Event::with('organizer')
            ->where('status', 'active')
            ->orderBy('date', 'desc')
            ->limit(5)
            ->get();

        // Événements à venir
        $upcomingEvents = Event::with('organizer')
            ->where('date', '>', now())
            ->where('status', 'active')
            ->orderBy('date', 'asc')
            ->limit(3)
            ->get();

        // Donations récentes
        $recentDonations = Donation::with(['user', 'event'])
            ->where('payment_status', 'succeeded')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Posts du forum récents
        $recentForumPosts = ForumPost::with(['author', 'relatedEvent'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Graphique des donations par mois (6 derniers mois)
        $donationsByMonth = Donation::where('payment_status', 'succeeded')
            ->where('created_at', '>=', now()->subMonths(6))
            ->selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, SUM(amount) as total')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        // Événements par type
        $eventsByType = Event::where('status', 'active')
            ->selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->get();

        return view('pages.dashboard', compact(
            'stats',
            'userStats', 
            'recentEvents',
            'upcomingEvents',
            'recentDonations',
            'recentForumPosts',
            'donationsByMonth',
            'eventsByType'
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