<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Event;
use App\Models\Donation;
use App\Models\BlogPost;
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
            
            'total_forum_posts' => BlogPost::count(),
            'posts_this_month' => BlogPost::whereMonth('created_at', now()->month)->count(),
            
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
        $topUsers = User::withCount(['donations', 'organizedEvents', 'blogPosts'])
            ->orderByDesc('donations_count')
            ->orderByDesc('organized_events_count')
            ->orderByDesc('blog_posts_count')
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

    public function users(Request $request)
    {
        $query = User::withCount(['donations', 'organizedEvents', 'blogPosts']);

        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filtre par rôle
        if ($request->filled('role')) {
            if ($request->role === 'admin') {
                $query->where('is_admin', true);
            } elseif ($request->role === 'user') {
                $query->where('is_admin', false);
            }
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20);

        // Statistiques
        $totalUsers = User::count();
        $activeUsers = User::whereNotNull('email_verified_at')->count();
        $adminUsers = User::where('is_admin', true)->count();
        $newUsersThisMonth = User::whereMonth('created_at', now()->month)->count();

        return view('admin.users', compact(
            'users',
            'totalUsers',
            'activeUsers',
            'adminUsers',
            'newUsersThisMonth'
        ));

    }

    /**
     * Gestion des rapports
     */

    public function reports(Request $request)
    {
        $query = Report::with('user', 'validator');

        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filtre par statut
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $reports = $query->orderBy('created_at', 'desc')->paginate(20);

        // Statistiques
        $pendingReports = Report::where('status', 'pending')->count();
        $approvedReports = Report::where('status', 'validated')->count();
        $rejectedReports = Report::where('status', 'rejected')->count();
        $totalReports = Report::count();

        return view('admin.reports', compact(
            'reports',
            'pendingReports',
            'approvedReports',
            'rejectedReports',
            'totalReports'
        ));

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


    /**
     * Supprimer un utilisateur
     */
    public function deleteUser(User $user)
    {
        // Protection: ne pas supprimer soi-même
        if ($user->id === Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous ne pouvez pas supprimer votre propre compte.'
            ], 403);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Utilisateur supprimé avec succès.'
        ]);
    }

    /**
     * Rejeter un rapport
     */
    public function rejectReport(Report $report)
    {
        $report->update([
            'status' => 'rejected',
            'validated_by' => Auth::id(),
            'validated_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Rapport rejeté avec succès.'
        ]);
    }

    /**
     * Supprimer un rapport
     */
    public function deleteReport(Report $report)
    {
        // Delete associated images if any
        if ($report->images && is_array($report->images)) {
            foreach ($report->images as $image) {
                if (file_exists(storage_path('app/public/' . $image))) {
                    unlink(storage_path('app/public/' . $image));
                }
            }
        }

        $report->delete();

        return response()->json([
            'success' => true,
            'message' => 'Rapport supprimé avec succès.'
        ]);
    }

    /**
     * Gestion des événements - Liste
     */
    public function events(Request $request)
    {
        $query = Event::query();

        // Recherche
        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        // Filtre par statut
        if ($request->filled('status')) {
            if ($request->status === 'upcoming') {
                $query->where('date', '>', now());
            } elseif ($request->status === 'active') {
                $query->where('status', 'active');
            } elseif ($request->status === 'completed') {
                $query->where('date', '<', now());
            }
        }

        $events = $query->orderBy('date', 'desc')->paginate(12);

        // Stats
        $totalEvents = Event::count();
        $activeEvents = Event::where('status', 'active')->count();
        $upcomingEvents = Event::where('date', '>', now())->count();
        $totalParticipants = DB::table('event_user')->count();

        return view('admin.events', compact(
            'events',
            'totalEvents',
            'activeEvents',
            'upcomingEvents',
            'totalParticipants'
        ));
    }

    /**
     * Supprimer un événement
     */
    public function deleteEvent(Event $event)
    {
        $event->delete();

        return response()->json([
            'success' => true,
            'message' => 'Événement supprimé avec succès.'
        ]);
    }

    /**
     * Gestion des donations - Liste
     */
    public function donations(Request $request)
    {
        $query = Donation::with(['user', 'refunds']);

        // Recherche
        if ($request->filled('search')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%");
            });
        }

        // Filtre par statut
        if ($request->filled('status')) {
            $query->where('payment_status', $request->status);
        }

        $donations = $query->orderBy('created_at', 'desc')->paginate(20);

        // Stats de base
        $totalAmount = Donation::where('payment_status', 'succeeded')->sum('amount');
        $totalDonations = Donation::count();
        $pendingDonations = Donation::where('payment_status', 'pending')->count();
        $monthlyAmount = Donation::where('payment_status', 'succeeded')
            ->whereMonth('created_at', now()->month)
            ->sum('amount');

        // Stats des remboursements
        $totalRefunds = \App\Models\Refund::count();
        $pendingRefunds = \App\Models\Refund::where('status', 'pending')->count();
        $completedRefunds = \App\Models\Refund::where('status', 'completed')->count();
        $totalRefundedAmount = \App\Models\Refund::where('status', 'completed')->sum('amount');

        // Données pour l'IA
        $aiData = [
            'total_donations' => $totalDonations,
            'total_amount' => $totalAmount,
            'avg_donation' => $totalDonations > 0 ? $totalAmount / $totalDonations : 0,
            'top_types' => Donation::selectRaw('type, COUNT(*) as count')
                ->where('payment_status', 'succeeded')
                ->groupBy('type')
                ->orderByDesc('count')
                ->limit(3)
                ->pluck('type')
                ->toArray(),
            'monthly_trend' => Donation::selectRaw('MONTH(created_at) as month, SUM(amount) as total')
                ->where('payment_status', 'succeeded')
                ->whereYear('created_at', now()->year)
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total')
                ->toArray()
        ];

        // Générer les insights IA
        $aiService = new \App\Services\DonationAIService();
        $aiInsights = $aiService->generateInsights($aiData);

        return view('admin.donations', compact(
            'donations',
            'totalAmount',
            'totalDonations',
            'pendingDonations',
            'monthlyAmount',
            'totalRefunds',
            'pendingRefunds',
            'completedRefunds',
            'totalRefundedAmount',
            'aiInsights'
        ));
    }

    /**
     * Gestion des arbres - Liste
     */
    public function trees(Request $request)
    {
        $query = Tree::with('plantedBy');

        // Recherche
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('species', 'like', "%{$request->search}%")
                  ->orWhereHas('plantedBy', function($q) use ($request) {
                      $q->where('name', 'like', "%{$request->search}%");
                  });
            });
        }

        // Filtre par statut
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $trees = $query->orderBy('planting_date', 'desc')->paginate(20);

        // Stats
        $totalTrees = Tree::count();
        $verifiedTrees = Tree::where('status', 'Planted')->count();
        $pendingTrees = Tree::where('status', 'Not Yet')->count();
        $monthlyTrees = Tree::whereMonth('planting_date', now()->month)->count();

        return view('admin.trees', compact(
            'trees',
            'totalTrees',
            'verifiedTrees',
            'pendingTrees',
            'monthlyTrees'
        ));
    }

    /**
     * Vérifier un arbre
     */
    public function verifyTree(Tree $tree)
    {
        $tree->update([
            'status' => 'Planted',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Arbre vérifié avec succès.'
        ]);
    }

    /**
     * Supprimer un arbre
     */
    public function deleteTree(Tree $tree)
    {
        $tree->delete();

        return response()->json([
            'success' => true,
            'message' => 'Arbre supprimé avec succès.'
        ]);
    }

    /**
     * Gestion du blog - Liste
     */
    public function blog(Request $request)
    {
        $query = BlogPost::with('author');

        // Recherche
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('content', 'like', "%{$request->search}%");
            });
        }

        // Filtre par statut (basé sur les vraies colonnes)
        if ($request->filled('status')) {
            // Comme il n'y a pas de colonne is_reported ou is_deleted, on filtre juste par date
            if ($request->status === 'recent') {
                $query->where('created_at', '>=', now()->subDays(7));
            }
        }

        $posts = $query->orderBy('created_at', 'desc')->paginate(20);

        // Stats
        $totalPosts = BlogPost::count();
        $reportedPosts = 0; // À implémenter avec une table reports si nécessaire
        $activeUsers = BlogPost::distinct('author_id')
            ->whereMonth('created_at', now()->month)
            ->count('author_id');
        $todayPosts = BlogPost::whereDate('created_at', today())->count();

        return view('admin.blog', compact(
            'posts',
            'totalPosts',
            'reportedPosts',
            'activeUsers',
            'todayPosts'
        ));
    }

    /**
     * Supprimer une publication blog
     */
    public function deleteBlogPost(BlogPost $blogPost)
    {
        try {
            // Delete associated comments first
            $blogPost->comments()->delete();
            
            // Delete the post
            $blogPost->delete();

            return response()->json([
                'success' => true,
                'message' => 'Publication supprimée avec succès.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Paramètres admin
     */
    public function settings()
    {
        return view('admin.settings');
    }

    /**
     * Traiter un remboursement
     */
    public function processRefund(Request $request, Donation $donation)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $donation->getTotalRefundableAmount(),
            'reason' => 'required|string|max:500',
        ]);

        try {
            $refund = $donation->processRefund(
                $request->amount,
                $request->reason,
                auth()->id()
            );

            return response()->json([
                'success' => true,
                'message' => 'Demande de remboursement créée avec succès.',
                'refund' => $refund
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du remboursement: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approuver un remboursement
     */
    public function approveRefund(Request $request, \App\Models\Refund $refund)
    {
        try {
            $refund->markAsProcessing();

            // Check if Stripe charge ID exists
            if ($refund->donation->stripe_charge_id) {
                // Process the Stripe refund
                $stripeRefund = $refund->processStripeRefund();

                return response()->json([
                    'success' => true,
                    'message' => 'Remboursement approuvé et traité via Stripe avec succès.',
                    'stripe_refund_id' => $stripeRefund->id
                ]);
            } else {
                // No Stripe charge ID - mark as completed manually
                $refund->markAsCompleted();

                return response()->json([
                    'success' => true,
                    'message' => 'Remboursement approuvé. Note: Aucun traitement Stripe (pas d\'ID de charge disponible).'
                ]);
            }
        } catch (\Exception $e) {
            $refund->markAsFailed($e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'approbation du remboursement: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Rejeter un remboursement
     */
    public function rejectRefund(Request $request, \App\Models\Refund $refund)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        try {
            $refund->markAsFailed($request->reason);

            return response()->json([
                'success' => true,
                'message' => 'Remboursement rejeté.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du rejet du remboursement: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Générer un message de remerciement IA
     */
    public function generateThankYou(Request $request, Donation $donation)
    {
        try {
            $aiService = new \App\Services\DonationAIService();
            $message = $aiService->generateThankYouMessage($donation);

            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la génération du message: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Analyser les risques de remboursement
     */
    public function analyzeRefundRisk(Request $request, Donation $donation)
    {
        try {
            $aiService = new \App\Services\DonationAIService();
            $analysis = $aiService->analyzeRefundRisk($donation);

            return response()->json([
                'success' => true,
                'analysis' => $analysis
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'analyse: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Générer des recommandations de campagne
     */
    public function generateCampaignRecommendations(Request $request)
    {
        try {
            $historicalData = [
                'seasonal_patterns' => ['Spring: +40%', 'Summer: +20%', 'Fall: +35%', 'Winter: -15%'],
                'successful_campaigns' => ['Tree Planting Drive', 'Earth Day Special', 'Holiday Giving'],
                'donor_demographics' => ['25-35 years: 45%', '36-50 years: 35%', '50+ years: 20%']
            ];

            $aiService = new \App\Services\DonationAIService();
            $recommendations = $aiService->generateCampaignRecommendations($historicalData);

            return response()->json([
                'success' => true,
                'recommendations' => $recommendations
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la génération des recommandations: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export donations data as CSV
     */
    public function exportDonations(Request $request)
    {
        try {
            $query = Donation::with(['user', 'event', 'refunds']);

            // Apply filters if provided
            if ($request->filled('status')) {
                $query->where('payment_status', $request->status);
            }

            if ($request->filled('type')) {
                $query->where('type', $request->type);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->whereHas('user', function($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                                  ->orWhere('email', 'like', "%{$search}%");
                    })->orWhereHas('event', function($eventQuery) use ($search) {
                        $eventQuery->where('title', 'like', "%{$search}%");
                    });
                });
            }

            $donations = $query->orderBy('created_at', 'desc')->get();

            $filename = 'donations-export-' . now()->format('Y-m-d') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ];

            $callback = function() use ($donations) {
                $file = fopen('php://output', 'w');

                // CSV headers
                fputcsv($file, [
                    'ID',
                    'Donateur',
                    'Email',
                    'Montant',
                    'Devise',
                    'Projet',
                    'Statut',
                    'Remboursements',
                    'Date de création'
                ]);

                // CSV data
                foreach ($donations as $donation) {
                    $refundInfo = $donation->refunds->count() > 0
                        ? $donation->refunds->map(function($refund) {
                            return number_format($refund->amount, 2) . '€ (' . $refund->status . ')';
                        })->join('; ')
                        : 'Aucun';

                    fputcsv($file, [
                        $donation->id,
                        $donation->user->name ?? 'Anonyme',
                        $donation->user->email ?? '',
                        number_format((float) $donation->amount, 2),
                        $donation->currency,
                        $donation->event->title ?? 'Général',
                        $donation->payment_status,
                        $refundInfo,
                        $donation->created_at->format('Y-m-d H:i:s')
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'export: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mise à jour des paramètres
     */
    public function updateSettings(Request $request)
    {
        // À implémenter selon vos besoins
        return redirect()->back()->with('success', 'Paramètres mis à jour avec succès.');
    }

}