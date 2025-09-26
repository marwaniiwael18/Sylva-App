@extends('layouts.dashboard')

@section('page-title', $event['title'])
@section('page-subtitle', $event['location'])

@section('page-content')
<div class="p-6 space-y-6">
    <!-- Event Hero -->
    <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl p-8 text-white">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <h1 class="text-3xl font-bold mb-4">{{ $event['title'] }}</h1>
                <p class="text-lg text-blue-100 mb-6">{{ $event['description'] }}</p>
                
                <div class="flex items-center space-x-6 text-sm">
                    <div class="flex items-center space-x-2">
                        <i data-lucide="map-pin" class="w-4 h-4"></i>
                        <span>{{ $event['location'] }}</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i data-lucide="calendar" class="w-4 h-4"></i>
                        <span>{{ date('M j, Y', strtotime($event['date'])) }}</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i data-lucide="clock" class="w-4 h-4"></i>
                        <span>{{ $event['time'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Event Details -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Event Details</h2>
                <div class="space-y-4">
                    <p class="text-gray-700 leading-relaxed">
                        Join us for {{ $event['title'] }}! This hands-on workshop will teach you sustainable 
                        gardening practices that you can apply in your own home or community garden. 
                        Perfect for beginners and experienced gardeners alike.
                    </p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-medium text-gray-900 mb-2">What you'll learn:</h4>
                            <ul class="text-sm text-gray-600 space-y-1">
                                <li>• Organic composting techniques</li>
                                <li>• Water-efficient irrigation methods</li>
                                <li>• Companion planting strategies</li>
                                <li>• Seasonal maintenance tips</li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900 mb-2">What to bring:</h4>
                            <ul class="text-sm text-gray-600 space-y-1">
                                <li>• Notebook and pen</li>
                                <li>• Water bottle</li>
                                <li>• Comfortable clothes</li>
                                <li>• Enthusiasm to learn!</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Schedule -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Event Schedule</h2>
                <div class="space-y-4">
                    <div class="flex items-start space-x-4">
                        <div class="w-16 text-sm font-medium text-gray-600">{{ $event['time'] }}</div>
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-900">Registration & Welcome</h4>
                            <p class="text-sm text-gray-600">Check-in and meet fellow participants</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4">
                        <div class="w-16 text-sm font-medium text-gray-600">10:30</div>
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-900">Introduction Session</h4>
                            <p class="text-sm text-gray-600">Overview of sustainable gardening principles</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4">
                        <div class="w-16 text-sm font-medium text-gray-600">11:30</div>
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-900">Hands-on Workshop</h4>
                            <p class="text-sm text-gray-600">Practical composting and planting activities</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4">
                        <div class="w-16 text-sm font-medium text-gray-600">13:00</div>
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-900">Lunch Break</h4>
                            <p class="text-sm text-gray-600">Light refreshments provided</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4">
                        <div class="w-16 text-sm font-medium text-gray-600">14:00</div>
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-900">Q&A and Wrap-up</h4>
                            <p class="text-sm text-gray-600">Final questions and take-home resources</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Register for Event -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Register for Event</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">Attendees</span>
                        <span class="font-medium">{{ $event['attendees'] }}/{{ $event['max_attendees'] }}</span>
                    </div>
                    
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-500 h-2 rounded-full" 
                             style="width: {{ ($event['attendees'] / $event['max_attendees']) * 100 }}%"></div>
                    </div>
                    
                    <button class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        Register Now
                    </button>
                    
                    <button class="w-full border border-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-50 transition-colors">
                        Add to Calendar
                    </button>
                </div>
            </div>

            <!-- Event Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Event Information</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <i data-lucide="calendar" class="w-4 h-4 text-blue-600"></i>
                            <span class="text-sm text-gray-600">Date</span>
                        </div>
                        <span class="font-medium">{{ date('M j, Y', strtotime($event['date'])) }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <i data-lucide="clock" class="w-4 h-4 text-green-600"></i>
                            <span class="text-sm text-gray-600">Time</span>
                        </div>
                        <span class="font-medium">{{ $event['time'] }}</span>
                    </div>
                    
                    <div class="flex items-start justify-between">
                        <div class="flex items-center space-x-2">
                            <i data-lucide="map-pin" class="w-4 h-4 text-purple-600"></i>
                            <span class="text-sm text-gray-600">Location</span>
                        </div>
                        <span class="font-medium text-right">{{ $event['location'] }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <i data-lucide="users" class="w-4 h-4 text-orange-600"></i>
                            <span class="text-sm text-gray-600">Capacity</span>
                        </div>
                        <span class="font-medium">{{ $event['max_attendees'] }} people</span>
                    </div>
                </div>
            </div>

            <!-- Share Event -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Share Event</h3>
                <div class="flex space-x-2">
                    <button class="flex-1 bg-blue-600 text-white py-2 px-3 rounded text-sm hover:bg-blue-700 transition-colors">
                        Facebook
                    </button>
                    <button class="flex-1 bg-blue-400 text-white py-2 px-3 rounded text-sm hover:bg-blue-500 transition-colors">
                        Twitter
                    </button>
                    <button class="flex-1 bg-gray-600 text-white py-2 px-3 rounded text-sm hover:bg-gray-700 transition-colors">
                        Email
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection