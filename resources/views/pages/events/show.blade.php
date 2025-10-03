@extends('layouts.dashboard')

@section('title', $event->title)

@section('page-content')
<div class="p-6">
    <div class="max-w-4xl mx-auto">
        <!-- Page Header -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center">
                <a href="{{ route('events.index') }}" class="mr-4 p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $event->title }}</h1>
                    <p class="mt-1 text-sm text-gray-600">Détails de l'événement</p>
                </div>
            </div>
            
            @if($canEdit)
                <div class="flex space-x-3">
                    <a href="{{ route('events.edit', $event) }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-colors">
                        <i data-lucide="edit" class="w-4 h-4"></i>
                        Modifier
                    </a>
                    <form action="{{ route('events.destroy', $event) }}" 
                          method="POST" 
                          class="inline"
                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet événement ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="inline-flex items-center gap-2 px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg text-sm font-medium focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                            Supprimer
                        </button>
                    </form>
                </div>
            @endif
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-3">
            <!-- Informations principales -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <span class="px-3 py-1 text-sm font-semibold rounded-full
                            @if($event->type === 'Tree Planting') bg-green-100 text-green-800
                            @elseif($event->type === 'Maintenance') bg-blue-100 text-blue-800
                            @elseif($event->type === 'Awareness') bg-yellow-100 text-yellow-800
                            @else bg-purple-100 text-purple-800
                            @endif">
                            {{ $event->type }}
                        </span>
                        
                        @if($event->is_past)
                            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-gray-100 text-gray-800">
                                Événement passé
                            </span>
                        @endif
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center text-gray-600">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span class="font-medium">{{ $event->formatted_date }}</span>
                        </div>

                        <div class="flex items-center text-gray-600">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span>{{ $event->location }}</span>
                        </div>

                        <div class="flex items-center text-gray-600">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span>Organisé par <strong>{{ $event->organizer->name }}</strong></span>
                        </div>
                    </div>

                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">Description</h3>
                        <div class="text-gray-600 whitespace-pre-wrap">{{ $event->description }}</div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Actions de participation -->
                @auth
                    @if(!$event->is_past && Auth::id() !== $event->organized_by_user_id)
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Participation</h3>
                            
                            @if($isParticipant)
                                <form action="{{ route('events.leave', $event) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="w-full bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition duration-300">
                                        Se désinscrire
                                    </button>
                                </form>
                                <p class="text-sm text-green-600 mt-2 text-center">✓ Vous participez à cet événement</p>
                            @else
                                <form action="{{ route('events.join', $event) }}" method="POST">
                                    @csrf
                                    <button type="submit" 
                                            class="w-full bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition duration-300">
                                        Participer
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endif
                @endauth

                <!-- Statistiques -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Statistiques</h3>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Participants :</span>
                            <span class="font-semibold text-gray-800">{{ $event->participants_count }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Créé le :</span>
                            <span class="font-semibold text-gray-800">{{ $event->created_at->format('d/m/Y') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Liste des participants -->
                @if($event->participants->count() > 0)
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">
                            Participants ({{ $event->participants->count() }})
                        </h3>
                        
                        <div class="space-y-2 max-h-64 overflow-y-auto">
                            @foreach($event->participants as $participant)
                                <div class="flex items-center space-x-3 p-2 rounded hover:bg-gray-50">
                                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                                        {{ strtoupper(substr($participant->name, 0, 1)) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">
                                            {{ $participant->name }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            Inscrit le {{ is_string($participant->pivot->registered_at) ? \Carbon\Carbon::parse($participant->pivot->registered_at)->format('d/m/Y') : $participant->pivot->registered_at->format('d/m/Y') }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection