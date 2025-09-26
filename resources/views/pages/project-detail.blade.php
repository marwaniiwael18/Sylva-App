@extends('layouts.dashboard')

@section('page-title', $project['title'])
@section('page-subtitle', $project['location'])

@section('page-content')
<div class="p-6 space-y-6">
    <!-- Project Hero -->
    <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl p-8 text-white">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <h1 class="text-3xl font-bold mb-4">{{ $project['title'] }}</h1>
                <p class="text-lg text-green-100 mb-6">{{ $project['description'] }}</p>
                
                <div class="flex items-center space-x-6 text-sm">
                    <div class="flex items-center space-x-2">
                        <i data-lucide="map-pin" class="w-4 h-4"></i>
                        <span>{{ $project['location'] }}</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i data-lucide="calendar" class="w-4 h-4"></i>
                        <span>{{ date('M j', strtotime($project['start_date'])) }} - {{ date('M j, Y', strtotime($project['end_date'])) }}</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i data-lucide="users" class="w-4 h-4"></i>
                        <span>{{ $project['participants'] }}/{{ $project['target_participants'] }} participants</span>
                    </div>
                </div>
            </div>
            
            <div class="text-right">
                <div class="text-4xl font-bold mb-2">{{ $project['progress'] }}%</div>
                <div class="text-green-200">Complete</div>
            </div>
        </div>
        
        <!-- Progress Bar -->
        <div class="mt-6">
            <div class="w-full bg-green-400/30 rounded-full h-3">
                <div class="bg-white h-3 rounded-full transition-all duration-500" 
                     style="width: {{ $project['progress'] }}%"></div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Project Details -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Project Details</h2>
                <div class="space-y-4">
                    <p class="text-gray-700 leading-relaxed">
                        {{ $project['description'] }}. This initiative aims to restore and enhance the natural ecosystem 
                        while providing educational opportunities for the community to learn about environmental conservation.
                    </p>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <h4 class="font-medium text-gray-900 mb-2">What you'll do:</h4>
                            <ul class="text-sm text-gray-600 space-y-1">
                                <li>• Plant native tree species</li>
                                <li>• Learn about soil preparation</li>
                                <li>• Participate in maintenance activities</li>
                                <li>• Connect with like-minded individuals</li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900 mb-2">What to bring:</h4>
                            <ul class="text-sm text-gray-600 space-y-1">
                                <li>• Comfortable work clothes</li>
                                <li>• Work gloves (optional)</li>
                                <li>• Water bottle</li>
                                <li>• Enthusiasm for the environment!</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timeline -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Project Timeline</h2>
                <div class="space-y-4">
                    <div class="flex items-center space-x-4">
                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                            <i data-lucide="check" class="w-4 h-4 text-white"></i>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">Planning & Site Preparation</h4>
                            <p class="text-sm text-gray-600">Completed {{ date('M j, Y', strtotime($project['start_date'])) }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                            <i data-lucide="check" class="w-4 h-4 text-white"></i>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">Community Mobilization</h4>
                            <p class="text-sm text-gray-600">In Progress</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                            <span class="w-2 h-2 bg-white rounded-full"></span>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">Tree Planting Phase</h4>
                            <p class="text-sm text-gray-600">Starting next month</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                            <span class="w-2 h-2 bg-white rounded-full"></span>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">Maintenance & Monitoring</h4>
                            <p class="text-sm text-gray-600">Expected {{ date('M Y', strtotime($project['end_date'])) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Join Project -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Join This Project</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">Participants</span>
                        <span class="font-medium">{{ $project['participants'] }}/{{ $project['target_participants'] }}</span>
                    </div>
                    
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-500 h-2 rounded-full" 
                             style="width: {{ ($project['participants'] / $project['target_participants']) * 100 }}%"></div>
                    </div>
                    
                    <button class="w-full bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 transition-colors font-medium">
                        Join Project
                    </button>
                    
                    <button class="w-full border border-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-50 transition-colors">
                        Add to Favorites
                    </button>
                </div>
            </div>

            <!-- Project Stats -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Impact Stats</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <i data-lucide="tree-pine" class="w-4 h-4 text-green-600"></i>
                            <span class="text-sm text-gray-600">Trees Planned</span>
                        </div>
                        <span class="font-medium">150</span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <i data-lucide="target" class="w-4 h-4 text-blue-600"></i>
                            <span class="text-sm text-gray-600">CO₂ Impact</span>
                        </div>
                        <span class="font-medium">2.5 tons</span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <i data-lucide="map" class="w-4 h-4 text-purple-600"></i>
                            <span class="text-sm text-gray-600">Area Covered</span>
                        </div>
                        <span class="font-medium">5 hectares</span>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Recent Activity</h3>
                <div class="space-y-3">
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i data-lucide="user-plus" class="w-4 h-4 text-green-600"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-900">5 new participants joined</p>
                            <p class="text-xs text-gray-500">2 hours ago</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i data-lucide="message-circle" class="w-4 h-4 text-blue-600"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-900">New project update posted</p>
                            <p class="text-xs text-gray-500">1 day ago</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i data-lucide="calendar" class="w-4 h-4 text-purple-600"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-900">Next meeting scheduled</p>
                            <p class="text-xs text-gray-500">3 days ago</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection