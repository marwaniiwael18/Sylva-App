@extends('layouts.dashboard')

@section('title', 'Administration - Dashboard')

@section('page-content')
<div class="p-6">
    <!-- Admin Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-pink-600 rounded-xl flex items-center justify-center">
                        <i data-lucide="shield-check" class="w-7 h-7 text-white"></i>
                    </div>
                    Administration
                </h1>
                <p class="mt-1 text-lg text-gray-600">Dashboard administrateur - Vue d'ensemble du système</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-2 px-3 py-2 bg-red-100 text-red-800 rounded-lg text-sm font-medium">
                    <i data-lucide="user-check" class="w-4 h-4"></i>
                    Administrateur
                </span>
            </div>
        </div>
    </div>

    <!-- Statistiques globales -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Utilisateurs -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center">
                    <i data-lucide="users" class="w-6 h-6 text-blue-600"></i>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-gray-900">{{ number_format($globalStats['total_users']) }}</div>
                    <div class="text-sm text-gray-500">Utilisateurs Total</div>
                </div>
            </div>
            <div class="flex items-center text-sm text-green-600">
                <i data-lucide="trending-up" class="w-4 h-4 mr-1"></i>
                +{{ $globalStats['new_users_this_month'] }} ce mois
            </div>
        </div>

        <!-- Événements -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center">
                    <i data-lucide="calendar-days" class="w-6 h-6 text-green-600"></i>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-gray-900">{{ number_format($globalStats['total_events']) }}</div>
                    <div class="text-sm text-gray-500">Événements</div>
                </div>
            </div>
            <div class="flex items-center text-sm text-green-600">
                <i data-lucide="activity" class="w-4 h-4 mr-1"></i>
                {{ $globalStats['active_events'] }} actifs
            </div>
        </div>

        <!-- Donations -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center">
                    <i data-lucide="heart" class="w-6 h-6 text-purple-600"></i>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-gray-900">{{ number_format($globalStats['total_donations'], 0) }}€</div>
                    <div class="text-sm text-gray-500">Donations Total</div>
                </div>
            </div>
            <div class="flex items-center text-sm text-green-600">
                <i data-lucide="euro" class="w-4 h-4 mr-1"></i>
                {{ number_format($globalStats['donations_this_month'], 0) }}€ ce mois
            </div>
        </div>

        <!-- Rapports en attente -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-yellow-50 rounded-xl flex items-center justify-center">
                    <i data-lucide="flag" class="w-6 h-6 text-yellow-600"></i>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-gray-900">{{ number_format($globalStats['pending_reports']) }}</div>
                    <div class="text-sm text-gray-500">Rapports en attente</div>
                </div>
            </div>
            @if($globalStats['pending_reports'] > 0)
            <div class="flex items-center text-sm text-red-600">
                <i data-lucide="alert-circle" class="w-4 h-4 mr-1"></i>
                Action requise
            </div>
            @else
            <div class="flex items-center text-sm text-green-600">
                <i data-lucide="check-circle" class="w-4 h-4 mr-1"></i>
                Tout est à jour
            </div>
            @endif
        </div>
    </div>

    <!-- Statistiques détaillées -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- Activité récente -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <i data-lucide="activity" class="w-5 h-5 text-blue-600"></i>
                    Activité Récente
                </h3>
                <p class="text-sm text-gray-600 mt-1">Dernières activités sur la plateforme</p>
            </div>
            <div class="p-6">
                <div class="space-y-4 max-h-96 overflow-y-auto">
                    @foreach($recentActivity as $activity)
                    <div class="flex items-start gap-4 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="w-10 h-10 bg-{{ $activity['color'] }}-50 rounded-full flex items-center justify-center flex-shrink-0">
                            <i data-lucide="{{ $activity['icon'] }}" class="w-5 h-5 text-{{ $activity['color'] }}-600"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">{{ $activity['title'] }}</p>
                            <p class="text-sm text-gray-500">{{ $activity['description'] }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $activity['created_at']->diffForHumans() }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Rapports en attente -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-yellow-600"></i>
                    Rapports en Attente
                </h3>
                <p class="text-sm text-gray-600 mt-1">Nécessitent votre attention</p>
            </div>
            <div class="p-6">
                @if($pendingReports->count() > 0)
                <div class="space-y-3 max-h-80 overflow-y-auto">
                    @foreach($pendingReports as $report)
                    <div class="p-3 border border-yellow-200 rounded-lg bg-yellow-50">
                        <h4 class="font-medium text-gray-900 text-sm">{{ $report->title }}</h4>
                        <p class="text-xs text-gray-600 mt-1">Par {{ $report->user->name }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $report->created_at->diffForHumans() }}</p>
                        <div class="mt-2">
                            <a href="{{ route('admin.reports') }}" class="text-xs text-yellow-700 hover:text-yellow-800 font-medium">
                                Examiner →
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8">
                    <i data-lucide="check-circle" class="w-12 h-12 text-green-600 mx-auto mb-3"></i>
                    <p class="text-sm text-gray-600">Aucun rapport en attente</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Utilisateurs actifs -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                <i data-lucide="star" class="w-5 h-5 text-purple-600"></i>
                Utilisateurs les Plus Actifs
            </h3>
            <p class="text-sm text-gray-600 mt-1">Top contributeurs de la communauté</p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
                @foreach($topUsers as $topUser)
                <div class="text-center p-4 rounded-lg border border-gray-200 hover:shadow-md transition-shadow">
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-400 to-pink-500 rounded-full flex items-center justify-center mx-auto mb-3">
                        <span class="text-white font-bold text-lg">{{ substr($topUser->name, 0, 1) }}</span>
                    </div>
                    <h4 class="font-medium text-gray-900 text-sm">{{ $topUser->name }}</h4>
                    <div class="mt-2 space-y-1">
                        <p class="text-xs text-gray-600">{{ $topUser->donations_count }} donations</p>
                        <p class="text-xs text-gray-600">{{ $topUser->organized_events_count }} événements</p>
                        <p class="text-xs text-gray-600">{{ $topUser->forum_posts_count }} posts</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Actions rapides admin -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                <i data-lucide="zap" class="w-5 h-5 text-red-600"></i>
                Actions Rapides
            </h3>
            <p class="text-sm text-gray-600 mt-1">Outils d'administration</p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('admin.users') }}" class="flex flex-col items-center p-4 rounded-lg border border-gray-200 hover:shadow-md transition-all hover:border-blue-300">
                    <i data-lucide="users" class="w-8 h-8 text-blue-600 mb-2"></i>
                    <span class="text-sm font-medium text-gray-900">Gérer Utilisateurs</span>
                </a>
                
                <a href="{{ route('admin.reports') }}" class="flex flex-col items-center p-4 rounded-lg border border-gray-200 hover:shadow-md transition-all hover:border-yellow-300">
                    <i data-lucide="flag" class="w-8 h-8 text-yellow-600 mb-2"></i>
                    <span class="text-sm font-medium text-gray-900">Valider Rapports</span>
                </a>
                
                <a href="{{ route('events.index') }}" class="flex flex-col items-center p-4 rounded-lg border border-gray-200 hover:shadow-md transition-all hover:border-green-300">
                    <i data-lucide="calendar" class="w-8 h-8 text-green-600 mb-2"></i>
                    <span class="text-sm font-medium text-gray-900">Événements</span>
                </a>
                
                <a href="{{ route('forum.index') }}" class="flex flex-col items-center p-4 rounded-lg border border-gray-200 hover:shadow-md transition-all hover:border-purple-300">
                    <i data-lucide="message-square" class="w-8 h-8 text-purple-600 mb-2"></i>
                    <span class="text-sm font-medium text-gray-900">Forum</span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js pour les graphiques -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Graphique des données mensuelles (si nécessaire)
    if (document.getElementById('monthlyChart')) {
        const ctx = document.getElementById('monthlyChart').getContext('2d');
        const monthlyData = @json($monthlyData);
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: monthlyData.map(data => data.month),
                datasets: [
                    {
                        label: 'Utilisateurs',
                        data: monthlyData.map(data => data.users),
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4
                    },
                    {
                        label: 'Événements',
                        data: monthlyData.map(data => data.events),
                        borderColor: 'rgb(34, 197, 94)',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        tension: 0.4
                    },
                    {
                        label: 'Donations (€)',
                        data: monthlyData.map(data => data.donations),
                        borderColor: 'rgb(168, 85, 247)',
                        backgroundColor: 'rgba(168, 85, 247, 0.1)',
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Évolution des Métriques (6 derniers mois)'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
});
</script>
@endsection