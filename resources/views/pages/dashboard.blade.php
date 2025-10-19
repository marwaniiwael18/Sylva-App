@extends('layouts.dashboard')

@section('title', 'Mon Dashboard - Sylva')
@section('page-title', 'Mon Dashboard')
@section('page-subtitle', 'Vue d\'ensemble de votre activité environnementale personnelle')

@section('page-content')
<div class="p-6 space-y-6">
    <!-- Personal Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Mes Donations -->
        <div class="bg-white rounded-2xl p-6 border border-purple-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Mes Donations</p>
                    <p class="text-2xl font-bold text-purple-900">{{ number_format($userStats['my_donation_amount'], 2) }} EUR</p>
                    <p class="text-xs text-purple-600">{{ $userStats['my_donations'] }} donations</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="heart" class="w-6 h-6 text-purple-600"></i>
                </div>
            </div>
        </div>

        <!-- Mes Événements -->
        <div class="bg-white rounded-2xl p-6 border border-blue-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Mes Événements</p>
                    <p class="text-2xl font-bold text-blue-900">{{ number_format($userStats['my_events_organized']) }}</p>
                    <p class="text-xs text-blue-600">{{ $userStats['my_events_participating'] }} participations</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="calendar" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
        </div>

        <!-- Mes Arbres -->
        <div class="bg-white rounded-2xl p-6 border border-green-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Mes Arbres</p>
                    <p class="text-2xl font-bold text-green-900">{{ number_format($userStats['my_trees']) }}</p>
                    <p class="text-xs text-green-600">arbres plantés</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="tree-pine" class="w-6 h-6 text-green-600"></i>
                </div>
            </div>
        </div>

        <!-- Mon Score d'Impact -->
        <div class="bg-white rounded-2xl p-6 border border-yellow-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Score d'Impact</p>
                    <p class="text-2xl font-bold text-yellow-900">{{ number_format($userStats['impact_score']) }}</p>
                    <p class="text-xs text-yellow-600">points totaux</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="award" class="w-6 h-6 text-yellow-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Personal Progress Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Trees Progress -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900">Progression Arbres</h3>
                <span class="text-sm text-gray-500">{{ $userStats['my_trees'] }}/50</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3">
                <div class="bg-green-500 h-3 rounded-full transition-all duration-1000" style="width: {{ min(($userStats['my_trees'] / 50) * 100, 100) }}%"></div>
            </div>
            <p class="text-sm text-gray-600 mt-2">
                @if($userStats['my_trees'] >= 50)
                    🎉 Félicitations ! Vous avez atteint le badge "Master Planter"
                @else
                    {{ 50 - $userStats['my_trees'] }} arbres de plus pour débloquer le badge "Master Planter"
                @endif
            </p>
        </div>

        <!-- Impact Score Progress -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900">Objectif Impact Score</h3>
                <span class="text-sm text-gray-500">{{ $userStats['impact_score'] }}/1000</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3">
                <div class="bg-blue-500 h-3 rounded-full transition-all duration-1000" style="width: {{ min(($userStats['impact_score'] / 1000) * 100, 100) }}%"></div>
            </div>
            <p class="text-sm text-gray-600 mt-2">
                @if($userStats['impact_score'] >= 1000)
                    🎉 Objectif atteint ! Vous êtes un champion de l'environnement !
                @else
                    {{ 1000 - $userStats['impact_score'] }} points de plus pour atteindre votre objectif mensuel
                @endif
            </p>
        </div>
    </div>

    <!-- My Activities Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- My Recent Activities -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <i data-lucide="activity" class="w-5 h-5 text-blue-600"></i>
                    Mes Activités Récentes
                </h3>
                <p class="text-sm text-gray-600 mt-1">Vos dernières contributions</p>
            </div>
            <div class="p-6">
                @if(isset($myActivities) && $myActivities && $myActivities->count() > 0)
                <div class="space-y-4 max-h-80 overflow-y-auto">
                    @foreach($myActivities as $activity)
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
                @else
                <div class="text-center py-8">
                    <i data-lucide="activity" class="w-12 h-12 text-gray-400 mx-auto mb-3"></i>
                    <p class="text-sm text-gray-600">Aucune activité récente</p>
                    <p class="text-xs text-gray-500 mt-1">Commencez à participer pour voir vos activités ici</p>
                </div>
                @endif
            </div>
        </div>

        <!-- My Statistics Overview -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <i data-lucide="bar-chart-3" class="w-5 h-5 text-green-600"></i>
                    Vue d'ensemble
                </h3>
                <p class="text-sm text-gray-600 mt-1">Votre impact en chiffres</p>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <i data-lucide="heart" class="w-5 h-5 text-purple-600"></i>
                            <span class="text-sm font-medium text-gray-700">Total donations</span>
                        </div>
                        <span class="text-sm font-bold text-purple-600">{{ $userStats['my_donations'] }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <i data-lucide="calendar" class="w-5 h-5 text-blue-600"></i>
                            <span class="text-sm font-medium text-gray-700">Événements organisés</span>
                        </div>
                        <span class="text-sm font-bold text-blue-600">{{ $userStats['my_events_organized'] }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <i data-lucide="message-square" class="w-5 h-5 text-orange-600"></i>
                            <span class="text-sm font-medium text-gray-700">Posts forum</span>
                        </div>
                        <span class="text-sm font-bold text-orange-600">{{ $userStats['my_forum_posts'] }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <i data-lucide="flag" class="w-5 h-5 text-red-600"></i>
                            <span class="text-sm font-medium text-gray-700">Rapports créés</span>
                        </div>
                        <span class="text-sm font-bold text-red-600">{{ $userStats['my_reports'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                <i data-lucide="zap" class="w-5 h-5 text-yellow-600"></i>
                Actions Rapides
            </h3>
            <p class="text-sm text-gray-600 mt-1">Participez à la communauté</p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('events.create') }}" class="flex flex-col items-center p-4 rounded-lg border border-gray-200 hover:shadow-md transition-all hover:border-blue-300">
                    <i data-lucide="calendar-plus" class="w-8 h-8 text-blue-600 mb-2"></i>
                    <span class="text-sm font-medium text-gray-900">Créer Événement</span>
                </a>
                
                <a href="{{ route('donations.create') }}" class="flex flex-col items-center p-4 rounded-lg border border-gray-200 hover:shadow-md transition-all hover:border-purple-300">
                    <i data-lucide="heart" class="w-8 h-8 text-purple-600 mb-2"></i>
                    <span class="text-sm font-medium text-gray-900">Faire un Don</span>
                </a>
                
                <a href="{{ route('trees.create') }}" class="flex flex-col items-center p-4 rounded-lg border border-gray-200 hover:shadow-md transition-all hover:border-green-300">
                    <i data-lucide="tree-pine" class="w-8 h-8 text-green-600 mb-2"></i>
                    <span class="text-sm font-medium text-gray-900">Planter Arbre</span>
                </a>
                
                <a href="{{ route('blog.ai.create') }}" class="flex flex-col items-center p-4 rounded-lg border border-gray-200 hover:shadow-md transition-all hover:border-purple-300">
                    <i data-lucide="sparkles" class="w-8 h-8 text-purple-600 mb-2"></i>
                    <span class="text-sm font-medium text-gray-900">Article IA</span>
                </a>
                
                <a href="{{ route('blog.create') }}" class="flex flex-col items-center p-4 rounded-lg border border-gray-200 hover:shadow-md transition-all hover:border-orange-300">
                    <i data-lucide="message-circle" class="w-8 h-8 text-orange-500 mb-2"></i>
                    <span class="text-sm text-gray-700">Nouveau Article</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection