@extends('layouts.dashboard')

@section('page-title', 'Projects')
@section('page-subtitle', 'Join environmental projects in your community')

@section('page-content')
<div class="p-6 space-y-6">
    <!-- Projects Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($projects as $project)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition-all duration-300">
            <div class="aspect-w-16 aspect-h-9 bg-gradient-to-r from-green-400 to-emerald-500">
                <div class="flex items-center justify-center">
                    <i data-lucide="tree-pine" class="w-12 h-12 text-white"></i>
                </div>
            </div>
            
            <div class="p-6">
                <div class="flex items-start justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">{{ $project['title'] }}</h3>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                        {{ $project['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ ucfirst($project['status']) }}
                    </span>
                </div>
                
                <p class="text-sm text-gray-600 mb-4">{{ $project['description'] }}</p>
                
                <div class="flex items-center space-x-2 text-sm text-gray-500 mb-4">
                    <i data-lucide="map-pin" class="w-4 h-4"></i>
                    <span>{{ $project['location'] }}</span>
                </div>
                
                <!-- Progress Bar -->
                <div class="mb-4">
                    <div class="flex justify-between text-sm text-gray-600 mb-2">
                        <span>Progress</span>
                        <span>{{ $project['progress'] }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-500 h-2 rounded-full transition-all duration-300" 
                             style="width: {{ $project['progress'] }}%"></div>
                    </div>
                </div>
                
                <!-- Participants -->
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-2 text-sm text-gray-600">
                        <i data-lucide="users" class="w-4 h-4"></i>
                        <span>{{ $project['participants'] }}/{{ $project['target_participants'] }} participants</span>
                    </div>
                </div>
                
                <!-- Dates -->
                <div class="text-xs text-gray-500 mb-4">
                    <span>{{ date('M j', strtotime($project['start_date'])) }} - {{ date('M j, Y', strtotime($project['end_date'])) }}</span>
                </div>
                
                <!-- Actions -->
                <div class="flex space-x-2">
                    <a href="{{ route('projects.detail', $project['id']) }}" 
                       class="flex-1 bg-green-600 text-white text-center py-2 px-4 rounded-lg hover:bg-green-700 transition-colors">
                        View Details
                    </a>
                    <button class="px-4 py-2 border border-green-600 text-green-600 rounded-lg hover:bg-green-50 transition-colors">
                        <i data-lucide="heart" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    
    @if(empty($projects))
    <div class="text-center py-12">
        <i data-lucide="folder" class="w-16 h-16 mx-auto mb-4 text-gray-300"></i>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No projects available</h3>
        <p class="text-gray-500">Check back later for new environmental projects to join!</p>
    </div>
    @endif
</div>
@endsection