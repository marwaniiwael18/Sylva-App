@extends('layouts.dashboard')

@section('page-title', 'Events')
@section('page-subtitle', 'Discover and participate in environmental events')

@section('page-content')
<div class="p-6 space-y-6">
    <!-- Events Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($events as $event)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition-all duration-300">
            <div class="aspect-w-16 aspect-h-9 bg-gradient-to-r from-blue-400 to-purple-500">
                <div class="flex items-center justify-center">
                    <i data-lucide="calendar" class="w-12 h-12 text-white"></i>
                </div>
            </div>
            
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $event['title'] }}</h3>
                <p class="text-sm text-gray-600 mb-4">{{ $event['description'] }}</p>
                
                <div class="space-y-2 mb-4">
                    <div class="flex items-center space-x-2 text-sm text-gray-500">
                        <i data-lucide="map-pin" class="w-4 h-4"></i>
                        <span>{{ $event['location'] }}</span>
                    </div>
                    <div class="flex items-center space-x-2 text-sm text-gray-500">
                        <i data-lucide="calendar" class="w-4 h-4"></i>
                        <span>{{ date('M j, Y', strtotime($event['date'])) }}</span>
                    </div>
                    <div class="flex items-center space-x-2 text-sm text-gray-500">
                        <i data-lucide="clock" class="w-4 h-4"></i>
                        <span>{{ $event['time'] }}</span>
                    </div>
                </div>
                
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-2 text-sm text-gray-600">
                        <i data-lucide="users" class="w-4 h-4"></i>
                        <span>{{ $event['attendees'] }}/{{ $event['max_attendees'] }} attending</span>
                    </div>
                </div>
                
                <div class="flex space-x-2">
                    <a href="{{ route('events.detail', $event['id']) }}" 
                       class="flex-1 bg-blue-600 text-white text-center py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors">
                        View Details
                    </a>
                    <button class="px-4 py-2 border border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50 transition-colors">
                        <i data-lucide="bookmark" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    
    @if(empty($events))
    <div class="text-center py-12">
        <i data-lucide="calendar" class="w-16 h-16 mx-auto mb-4 text-gray-300"></i>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No events available</h3>
        <p class="text-gray-500">Check back later for upcoming environmental events!</p>
    </div>
    @endif
</div>
@endsection