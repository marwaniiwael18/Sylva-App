@extends('layouts.dashboard')

@section('title', 'Dashboard - Sylva')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Vue d\'ensemble de votre activité environnementale')

@section('page-content')
<div class="p-6 space-y-6">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Users -->
        <div class="bg-white rounded-2xl p-6 border border-emerald-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Utilisateurs</p>
                    <p class="text-2xl font-bold text-emerald-900">{{ number_format($stats['total_users']) }}</p>
                </div>
                <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="users" class="w-6 h-6 text-emerald-600"></i>
                </div>
            </div>
        </div>

        <!-- Total Events -->
        <div class="bg-white rounded-2xl p-6 border border-blue-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Événements</p>
                    <p class="text-2xl font-bold text-blue-900">{{ number_format($stats['total_events']) }}</p>
                    <p class="text-xs text-blue-600">{{ $stats['active_events'] }} actifs</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="calendar" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
        </div>

        <!-- Total Donations -->
        <div class="bg-white rounded-2xl p-6 border border-purple-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Donations</p>
                    <p class="text-2xl font-bold text-purple-900">{{ number_format($stats['total_donations'], 2) }} EUR</p>
                    <p class="text-xs text-purple-600">{{ $stats['total_donations_count'] }} donations</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="heart" class="w-6 h-6 text-purple-600"></i>
                </div>
            </div>
        </div>

        <!-- Total Trees -->
        <div class="bg-white rounded-2xl p-6 border border-green-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Arbres</p>
                    <p class="text-2xl font-bold text-green-900">{{ number_format($stats['total_trees']) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="tree-pine" class="w-6 h-6 text-green-600"></i>
                </div>
            </div>
        </div>
    </div>

        <!-- Impact Score -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center">
                    <i data-lucide="award" class="w-6 h-6 text-blue-600"></i>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['impact_score'] }}</div>
                    <div class="text-sm text-gray-500">Impact Score</div>
                </div>
            </div>
            <div class="flex items-center text-sm text-green-600">
                <i data-lucide="trending-up" class="w-4 h-4 mr-1"></i>
                +25 this month
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        <!-- Quick Actions -->
        <div class="xl:col-span-2">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Quick Actions</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Report an Issue -->
                <a href="{{ route('map') }}" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-all duration-300 cursor-pointer group">
                    <div class="w-12 h-12 bg-green-600 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <i data-lucide="map-pin" class="w-6 h-6 text-white"></i>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Explore the Map</h3>
                    <p class="text-sm text-gray-600">View environmental reports and add new ones</p>
                </a>

                <!-- View Reports -->
                <a href="{{ route('reports') }}" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-all duration-300 cursor-pointer group">
                    <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <i data-lucide="flag" class="w-6 h-6 text-white"></i>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">View Reports</h3>
                    <p class="text-sm text-gray-600">Browse and manage environmental reports</p>
                </a>
            </div>
        </div>

        <!-- Recent Activity -->
        <div>
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Recent Activity</h2>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="space-y-4">
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 bg-green-50 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-sm">🌳</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-900">You contributed to tree planting efforts</p>
                            <div class="flex items-center text-xs text-gray-500 mt-1">
                                <i data-lucide="clock" class="w-3 h-3 mr-1"></i>
                                2 hours ago
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 bg-blue-50 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-sm">�</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-900">Viewed environmental reports in your area</p>
                            <div class="flex items-center text-xs text-gray-500 mt-1">
                                <i data-lucide="clock" class="w-3 h-3 mr-1"></i>
                                1 day ago
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 bg-orange-50 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-sm">📍</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-900">Reported a dead tree on Maple Street</p>
                            <div class="flex items-center text-xs text-gray-500 mt-1">
                                <i data-lucide="clock" class="w-3 h-3 mr-1"></i>
                                3 days ago
                            </div>
                        </div>
                    </div>
                </div>
                <button class="w-full mt-4 text-sm text-green-600 hover:text-green-700 font-medium">
                    View all activity
                </button>
            </div>
        </div>
    </div>

    <!-- Badges Section -->
    <div>
        <h2 class="text-xl font-semibold text-gray-900 mb-6">Your Badges</h2>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex flex-wrap gap-4">
                <div class="flex items-center space-x-2 bg-green-50 text-green-700 px-4 py-2 rounded-full">
                    <i data-lucide="award" class="w-4 h-4"></i>
                    <span class="text-sm font-medium">Tree Planter</span>
                </div>
                <div class="flex items-center space-x-2 bg-blue-50 text-blue-700 px-4 py-2 rounded-full">
                    <i data-lucide="award" class="w-4 h-4"></i>
                    <span class="text-sm font-medium">Environmental Reporter</span>
                </div>
                <div class="flex items-center space-x-2 bg-purple-50 text-purple-700 px-4 py-2 rounded-full">
                    <i data-lucide="award" class="w-4 h-4"></i>
                    <span class="text-sm font-medium">Community Guardian</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Section -->
    <div>
        <h2 class="text-xl font-semibold text-gray-900 mb-6">Impact Progress</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Trees Progress -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-900">Trees to Next Badge</h3>
                    <span class="text-sm text-gray-500">{{ $stats['trees_planted'] }}/50</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="bg-green-500 h-3 rounded-full transition-all duration-1000" style="width: {{ ($stats['trees_planted'] / 50) * 100 }}%"></div>
                </div>
                <p class="text-sm text-gray-600 mt-2">
                    {{ 50 - $stats['trees_planted'] }} more trees to unlock "Master Planter" badge
                </p>
            </div>

            <!-- Impact Score -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-900">Impact Score Goal</h3>
                    <span class="text-sm text-gray-500">{{ $stats['impact_score'] }}/1000</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="bg-blue-500 h-3 rounded-full transition-all duration-1000" style="width: {{ ($stats['impact_score'] / 1000) * 100 }}%"></div>
                </div>
                <p class="text-sm text-gray-600 mt-2">
                    {{ 1000 - $stats['impact_score'] }} points more to reach your monthly goal
                </p>
            </div>
        </div>
    </div>
</div>
@endsection