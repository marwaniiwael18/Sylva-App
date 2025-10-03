@extends('layouts.dashboard')

@section('title', 'Mes Événements')

@section('page-content')
<div class="p-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                        <i data-lucide="user-check" class="w-5 h-5 text-white"></i>
                    </div>
                    Mes Événements
                </h1>
                <p class="mt-1 text-sm text-gray-600">Gérez vos événements organisés et vos participations</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('events.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                    <i data-lucide="list" class="w-4 h-4"></i>
                    Tous les événements
                </a>
                <a href="{{ route('events.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white rounded-lg text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all transform hover:scale-105">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    Créer un événement
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Événements organisés -->
    <div class="mb-8">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Événements que j'organise</h2>
        
        @if($organizedEvents->count() > 0)
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach($organizedEvents as $event)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300">
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-xl font-semibold text-gray-800">{{ $event->title }}</h3>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    @if($event->type === 'Tree Planting') bg-green-100 text-green-800
                                    @elseif($event->type === 'Maintenance') bg-blue-100 text-blue-800
                                    @elseif($event->type === 'Awareness') bg-yellow-100 text-yellow-800
                                    @else bg-purple-100 text-purple-800
                                    @endif">
                                    {{ $event->type }}
                                </span>
                            </div>

                            <p class="text-gray-600 mb-4 line-clamp-3">{{ Str::limit($event->description, 100) }}</p>

                            <div class="space-y-2 text-sm text-gray-500">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2z"></path>
                                    </svg>
                                    {{ $event->formatted_date }}
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    {{ $event->location }}
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path>
                                    </svg>
                                    {{ $event->participants->count() }} participant(s)
                                </div>
                            </div>

                            <div class="mt-4 pt-4 border-t border-gray-200 flex space-x-2">
                                <a href="{{ route('events.show', $event) }}" class="flex-1 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-3 rounded transition duration-300 text-center text-sm">
                                    Voir
                                </a>
                                <a href="{{ route('events.edit', $event) }}" class="flex-1 bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-3 rounded transition duration-300 text-center text-sm">
                                    Modifier
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-gray-50 rounded-lg p-8 text-center">
                <div class="text-gray-500 text-lg mb-4">Vous n'avez organisé aucun événement</div>
                <a href="{{ route('events.create') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition duration-300">
                    Créer votre premier événement
                </a>
            </div>
        @endif
    </div>

    <!-- Événements auxquels je participe -->
    <div>
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Événements auxquels je participe</h2>
        
        @if($participatingEvents->count() > 0)
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach($participatingEvents as $event)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300">
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-xl font-semibold text-gray-800">{{ $event->title }}</h3>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    @if($event->type === 'Tree Planting') bg-green-100 text-green-800
                                    @elseif($event->type === 'Maintenance') bg-blue-100 text-blue-800
                                    @elseif($event->type === 'Awareness') bg-yellow-100 text-yellow-800
                                    @else bg-purple-100 text-purple-800
                                    @endif">
                                    {{ $event->type }}
                                </span>
                            </div>

                            <p class="text-gray-600 mb-4 line-clamp-3">{{ Str::limit($event->description, 100) }}</p>

                            <div class="space-y-2 text-sm text-gray-500">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2z"></path>
                                    </svg>
                                    {{ $event->formatted_date }}
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    {{ $event->location }}
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Organisé par {{ $event->organizer->name }}
                                </div>
                                <div class="text-green-600 font-medium">
                                    ✓ Inscrit le {{ is_string($event->pivot->registered_at) ? \Carbon\Carbon::parse($event->pivot->registered_at)->format('d/m/Y') : $event->pivot->registered_at->format('d/m/Y') }}
                                </div>
                            </div>

                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <a href="{{ route('events.show', $event) }}" class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-300 inline-block text-center">
                                    Voir les détails
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-gray-50 rounded-lg p-8 text-center">
                <div class="text-gray-500 text-lg mb-4">Vous ne participez à aucun événement</div>
                <a href="{{ route('events.index') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-300">
                    Découvrir les événements
                </a>
            </div>
        @endif
    </div>
</div>
@endsection