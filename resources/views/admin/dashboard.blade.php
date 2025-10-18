@extends('layouts.admin')

@section('title', 'Dashboard Admin - Sylva')
@section('page-title', 'Dashboard Administrateur')
@section('page-subtitle', 'Vue d\'ensemble et gestion du système')

@section('content')
<div class="p-6 space-y-6">
    <!-- Alert Admin -->
    <div class="bg-red-900 border border-red-700 rounded-lg p-4">
        <div class="flex items-center">
            <i data-lucide="shield-alert" class="w-5 h-5 text-red-400 mr-3"></i>
            <h3 class="text-sm font-medium text-red-200">Mode Administrateur</h3>
        </div>
        <div class="mt-2 text-sm text-red-300">
            Vous êtes connecté en tant qu'administrateur. Toutes vos actions sont enregistrées.
        </div>
    </div>

    <!-- Statistiques globales -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Utilisateurs -->
        <div class="bg-gray-800 rounded-xl border border-gray-700 p-6 hover:border-blue-600 transition-all">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center">
                    <i data-lucide="users" class="w-6 h-6 text-white"></i>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-white">{{ number_format($globalStats['total_users']) }}</div>
                    <div class="text-sm text-gray-400">Utilisateurs</div>
                </div>
            </div>
            <div class="flex items-center text-sm text-green-400">
                <i data-lucide="trending-up" class="w-4 h-4 mr-1"></i>
                +{{ $globalStats['new_users_this_month'] }} ce mois
            </div>
        </div>

        <!-- Événements -->
        <div class="bg-gray-800 rounded-xl border border-gray-700 p-6 hover:border-green-600 transition-all">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-600 rounded-xl flex items-center justify-center">
                    <i data-lucide="calendar" class="w-6 h-6 text-white"></i>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-white">{{ number_format($globalStats['total_events']) }}</div>
                    <div class="text-sm text-gray-400">Événements</div>
                </div>
            </div>
            <div class="flex items-center text-sm text-green-400">
                <i data-lucide="activity" class="w-4 h-4 mr-1"></i>
                {{ $globalStats['active_events'] }} actifs
            </div>
        </div>

        <!-- Donations -->
        <div class="bg-gray-800 rounded-xl border border-gray-700 p-6 hover:border-purple-600 transition-all">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-600 rounded-xl flex items-center justify-center">
                    <i data-lucide="heart" class="w-6 h-6 text-white"></i>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-white">{{ number_format($globalStats['total_donations'], 0) }}€</div>
                    <div class="text-sm text-gray-400">Donations</div>
                </div>
            </div>
            <div class="flex items-center text-sm text-green-400">
                <i data-lucide="euro" class="w-4 h-4 mr-1"></i>
                {{ number_format($globalStats['donations_this_month'], 0) }}€ ce mois
            </div>
        </div>

        <!-- Rapports en attente -->
        <div class="bg-gray-800 rounded-xl border border-gray-700 p-6 hover:border-yellow-600 transition-all">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-yellow-600 rounded-xl flex items-center justify-center">
                    <i data-lucide="flag" class="w-6 h-6 text-white"></i>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-white">{{ number_format($globalStats['pending_reports']) }}</div>
                    <div class="text-sm text-gray-400">Rapports</div>
                </div>
            </div>
            @if($globalStats['pending_reports'] > 0)
            <div class="flex items-center text-sm text-red-400">
                <i data-lucide="alert-circle" class="w-4 h-4 mr-1"></i>
                Action requise
            </div>
            @else
            <div class="flex items-center text-sm text-green-400">
                <i data-lucide="check-circle" class="w-4 h-4 mr-1"></i>
                Tout est à jour
            </div>
            @endif
        </div>
    </div>

    <!-- Actions rapides d'administration -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <a href="{{ route('admin.users') }}" class="bg-gray-800 border border-gray-700 rounded-xl p-6 hover:bg-gray-750 transition-colors group">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i data-lucide="users" class="w-6 h-6 text-white"></i>
                </div>
                <i data-lucide="arrow-right" class="w-5 h-5 text-gray-400 group-hover:text-white transition-colors"></i>
            </div>
            <h3 class="text-lg font-semibold text-white mb-2">Gestion Utilisateurs</h3>
            <p class="text-sm text-gray-400">Gérer les comptes, permissions et statuts</p>
        </a>

        <a href="{{ route('admin.reports') }}" class="bg-gray-800 border border-gray-700 rounded-xl p-6 hover:bg-gray-750 transition-colors group">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-yellow-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i data-lucide="flag" class="w-6 h-6 text-white"></i>
                </div>
                <i data-lucide="arrow-right" class="w-5 h-5 text-gray-400 group-hover:text-white transition-colors"></i>
            </div>
            <h3 class="text-lg font-semibold text-white mb-2">Validation Rapports</h3>
            <p class="text-sm text-gray-400">Approuver ou rejeter les rapports</p>
        </a>

        <a href="{{ route('admin.events') }}" class="bg-gray-800 border border-gray-700 rounded-xl p-6 hover:bg-gray-750 transition-colors group">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i data-lucide="calendar" class="w-6 h-6 text-white"></i>
                </div>
                <i data-lucide="arrow-right" class="w-5 h-5 text-gray-400 group-hover:text-white transition-colors"></i>
            </div>
            <h3 class="text-lg font-semibold text-white mb-2">Gestion Événements</h3>
            <p class="text-sm text-gray-400">Superviser et organiser les événements</p>
        </a>
    </div>

    <!-- Statistiques détaillées -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Activité récente -->
        <div class="bg-gray-800 border border-gray-700 rounded-xl">
            <div class="p-6 border-b border-gray-700">
                <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                    <i data-lucide="activity" class="w-5 h-5 text-red-400"></i>
                    Activité Récente
                </h3>
                <p class="text-sm text-gray-400 mt-1">Dernières activités sur la plateforme</p>
            </div>
            <div class="p-6">
                <div class="space-y-4 max-h-96 overflow-y-auto">
                    @foreach($recentActivity as $activity)
                    <div class="flex items-start gap-4 p-3 rounded-lg hover:bg-gray-700 transition-colors">
                        <div class="w-10 h-10 bg-{{ $activity['color'] }}-600 rounded-full flex items-center justify-center flex-shrink-0">
                            <i data-lucide="{{ $activity['icon'] }}" class="w-5 h-5 text-white"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-white">{{ $activity['title'] }}</p>
                            <p class="text-sm text-gray-400">{{ $activity['description'] }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $activity['created_at']->diffForHumans() }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Rapports en attente -->
        <div class="bg-gray-800 border border-gray-700 rounded-xl">
            <div class="p-6 border-b border-gray-700">
                <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-yellow-400"></i>
                    Rapports en Attente
                </h3>
                <p class="text-sm text-gray-400 mt-1">Nécessitent votre attention</p>
            </div>
            <div class="p-6">
                @if($pendingReports->count() > 0)
                <div class="space-y-3 max-h-80 overflow-y-auto">
                    @foreach($pendingReports as $report)
                    <div class="p-3 border border-yellow-600 rounded-lg bg-yellow-900/20">
                        <h4 class="font-medium text-white text-sm">{{ $report->title }}</h4>
                        <p class="text-xs text-gray-400 mt-1">Par {{ $report->user->name }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $report->created_at->diffForHumans() }}</p>
                        <div class="mt-2">
                            <a href="{{ route('admin.reports') }}" class="text-xs text-yellow-400 hover:text-yellow-300 font-medium">
                                Examiner →
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8">
                    <i data-lucide="check-circle" class="w-12 h-12 text-green-400 mx-auto mb-3"></i>
                    <p class="text-sm text-gray-400">Aucun rapport en attente</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Utilisateurs actifs -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
    <!-- Utilisateurs les Plus Actifs -->
    <div class="bg-gray-800 border border-gray-700 rounded-xl">
        <div class="p-6 border-b border-gray-700">
            <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                <i data-lucide="star" class="w-5 h-5 text-purple-400"></i>
                Utilisateurs les Plus Actifs
            </h3>
            <p class="text-sm text-gray-400 mt-1">Top contributeurs de la communauté</p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
                @foreach($topUsers as $topUser)
                <div class="text-center p-4 rounded-lg bg-gray-750 border border-gray-700 hover:border-purple-600 transition-all">
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-600 rounded-full flex items-center justify-center mx-auto mb-3">
                        <span class="text-white font-bold text-lg">{{ substr($topUser->name, 0, 1) }}</span>
                    </div>
                    <h4 class="font-medium text-white text-sm">{{ $topUser->name }}</h4>
                    <div class="mt-2 space-y-1">
                        <p class="text-xs text-gray-400">{{ $topUser->donations_count }} donations</p>
                        <p class="text-xs text-gray-400">{{ $topUser->organized_events_count }} événements</p>
                        <p class="text-xs text-gray-400">{{ $topUser->forum_posts_count }} posts</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Actions rapides admin -->
    <div class="bg-gray-800 border border-gray-700 rounded-xl">
        <div class="p-6 border-b border-gray-700">
            <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                <i data-lucide="zap" class="w-5 h-5 text-red-400"></i>
                Actions Rapides
            </h3>
            <p class="text-sm text-gray-400 mt-1">Outils d'administration</p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('admin.users') }}" class="flex flex-col items-center p-4 rounded-lg bg-gray-750 border border-gray-700 hover:border-blue-600 hover:bg-gray-700 transition-all group">
                    <i data-lucide="users" class="w-8 h-8 text-blue-400 mb-2 group-hover:scale-110 transition-transform"></i>
                    <span class="text-sm font-medium text-white">Gérer Utilisateurs</span>
                </a>
                
                <a href="{{ route('admin.reports') }}" class="flex flex-col items-center p-4 rounded-lg bg-gray-750 border border-gray-700 hover:border-yellow-600 hover:bg-gray-700 transition-all group">
                    <i data-lucide="flag" class="w-8 h-8 text-yellow-400 mb-2 group-hover:scale-110 transition-transform"></i>
                    <span class="text-sm font-medium text-white">Valider Rapports</span>
                </a>
                
                <a href="{{ route('admin.events') }}" class="flex flex-col items-center p-4 rounded-lg bg-gray-750 border border-gray-700 hover:border-green-600 hover:bg-gray-700 transition-all group">
                    <i data-lucide="calendar" class="w-8 h-8 text-green-400 mb-2 group-hover:scale-110 transition-transform"></i>
                    <span class="text-sm font-medium text-white">Événements</span>
                </a>
                
                <a href="{{ route('admin.forum') }}" class="flex flex-col items-center p-4 rounded-lg bg-gray-750 border border-gray-700 hover:border-purple-600 hover:bg-gray-700 transition-all group">
                    <i data-lucide="message-square" class="w-8 h-8 text-purple-400 mb-2 group-hover:scale-110 transition-transform"></i>
                    <span class="text-sm font-medium text-white">Forum</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection