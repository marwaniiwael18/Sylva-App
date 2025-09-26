@extends('layouts.dashboard')

@section('page-title', 'Dashboard')

@section('page-content')
<div class="p-6 space-y-8">
    <!-- Welcome Header -->
    <div class="text-center md:text-left">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">
            Welcome back, {{ explode(' ', Auth::user()->name)[0] }}! 👋
        </h1>
        <p class="text-gray-600">
            Ready to make a positive impact on the environment today?
        </p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        <!-- Trees Planted -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center">
                    <i data-lucide="tree-pine" class="w-6 h-6 text-green-600"></i>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['trees_planted'] }}</div>
                    <div class="text-sm text-gray-500">Trees Planted</div>
                </div>
            </div>
            <div class="flex items-center text-sm text-green-600">
                <i data-lucide="trending-up" class="w-4 h-4 mr-1"></i>
                +12 this month
            </div>
        </div>

        <!-- Events Attended -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center">
                    <i data-lucide="calendar" class="w-6 h-6 text-purple-600"></i>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['events_attended'] }}</div>
                    <div class="text-sm text-gray-500">Events Attended</div>
                </div>
            </div>
            <div class="flex items-center text-sm text-green-600">
                <i data-lucide="trending-up" class="w-4 h-4 mr-1"></i>
                +3 this month
            </div>
        </div>

        <!-- Projects Joined -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center">
                    <i data-lucide="users" class="w-6 h-6 text-blue-600"></i>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['projects_joined'] }}</div>
                    <div class="text-sm text-gray-500">Projects Joined</div>
                </div>
            </div>
            <div class="flex items-center text-sm text-green-600">
                <i data-lucide="trending-up" class="w-4 h-4 mr-1"></i>
                +2 this month
            </div>
        </div>

        <!-- CO2 Saved -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center">
                    <i data-lucide="target" class="w-6 h-6 text-emerald-600"></i>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['co2_saved'] }}</div>
                    <div class="text-sm text-gray-500">CO₂ Saved</div>
                </div>
            </div>
            <div class="flex items-center text-sm text-green-600">
                <i data-lucide="trending-up" class="w-4 h-4 mr-1"></i>
                +45kg this month
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        <!-- Quick Actions -->
        <div class="xl:col-span-2">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Quick Actions</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Join a Project -->
                <a href="{{ route('projects') }}" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-all duration-300 cursor-pointer group">
                    <div class="w-12 h-12 bg-green-600 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <i data-lucide="tree-pine" class="w-6 h-6 text-white"></i>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Join a Project</h3>
                    <p class="text-sm text-gray-600">Find and join ongoing green projects in your area</p>
                </a>

                <!-- Attend an Event -->
                <a href="{{ route('events') }}" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-all duration-300 cursor-pointer group">
                    <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <i data-lucide="calendar" class="w-6 h-6 text-white"></i>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Attend an Event</h3>
                    <p class="text-sm text-gray-600">Discover upcoming environmental events</p>
                </a>

                <!-- Report an Issue -->
                <a href="{{ route('map') }}" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-all duration-300 cursor-pointer group">
                    <div class="w-12 h-12 bg-orange-600 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <i data-lucide="map-pin" class="w-6 h-6 text-white"></i>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Report an Issue</h3>
                    <p class="text-sm text-gray-600">Help identify areas that need greening</p>
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
                            <p class="text-sm text-gray-900">You planted 3 trees in Central Park project</p>
                            <div class="flex items-center text-xs text-gray-500 mt-1">
                                <i data-lucide="clock" class="w-3 h-3 mr-1"></i>
                                2 hours ago
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 bg-blue-50 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-sm">📅</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-900">Joined "Community Garden Workshop" event</p>
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
                    <span class="text-sm font-medium">Community Helper</span>
                </div>
                <div class="flex items-center space-x-2 bg-purple-50 text-purple-700 px-4 py-2 rounded-full">
                    <i data-lucide="award" class="w-4 h-4"></i>
                    <span class="text-sm font-medium">Event Enthusiast</span>
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