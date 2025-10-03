@extends('layouts.dashboard')

@section('title', 'Gestion des Événements')

@section('page-content')
<div class="p-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl flex items-center justify-center">
                        <i data-lucide="calendar-days" class="w-5 h-5 text-white"></i>
                    </div>
                    Événements
                </h1>
                <p class="mt-1 text-sm text-gray-600">Participez aux initiatives vertes de votre communauté</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('events.my-events') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-colors">
                    <i data-lucide="user-check" class="w-4 h-4"></i>
                    Mes Événements
                </a>
                <a href="{{ route('events.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white rounded-lg text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all transform hover:scale-105">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    Créer un Événement
                </a>
            </div>
        </div>
        
        <!-- Quick Stats -->
        <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="calendar" class="w-4 h-4 text-emerald-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">{{ \App\Models\Event::where('date', '>', now())->count() }}</p>
                        <p class="text-xs text-gray-500">Événements à venir</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="users" class="w-4 h-4 text-blue-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">{{ \DB::table('event_user')->count() }}</p>
                        <p class="text-xs text-gray-500">Participants actifs</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="activity" class="w-4 h-4 text-purple-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">{{ \App\Models\Event::count() }}</p>
                        <p class="text-xs text-gray-500">Total événements</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

        <!-- Alerts -->
        @if(session('success'))
            <div class="mb-6 rounded-lg bg-emerald-50 border border-emerald-200 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i data-lucide="check-circle" class="w-5 h-5 text-emerald-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-emerald-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 rounded-lg bg-red-50 border border-red-200 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i data-lucide="alert-circle" class="w-5 h-5 text-red-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Events Grid -->
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($events as $event)
                <div class="group relative bg-white rounded-lg border border-gray-200 hover:border-emerald-300 hover:shadow-lg transition-all duration-200 overflow-hidden">
                    <!-- Event Type Header -->
                    <div class="h-1 
                        @if($event->type === 'Tree Planting') bg-emerald-500
                        @elseif($event->type === 'Maintenance') bg-blue-500
                        @elseif($event->type === 'Awareness') bg-amber-500
                        @else bg-purple-500
                        @endif">
                    </div>
                    
                    <div class="p-5">
                        <!-- Header with title and badge -->
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 group-hover:text-emerald-600 transition-colors line-clamp-2 flex-1 mr-3">
                                {{ $event->title }}
                            </h3>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium rounded-full flex-shrink-0
                                @if($event->type === 'Tree Planting') bg-emerald-100 text-emerald-700
                                @elseif($event->type === 'Maintenance') bg-blue-100 text-blue-700
                                @elseif($event->type === 'Awareness') bg-amber-100 text-amber-700
                                @else bg-purple-100 text-purple-700
                                @endif">
                                @if($event->type === 'Tree Planting')
                                    <i data-lucide="sprout" class="w-3 h-3"></i>
                                @elseif($event->type === 'Maintenance')
                                    <i data-lucide="wrench" class="w-3 h-3"></i>
                                @elseif($event->type === 'Awareness')
                                    <i data-lucide="megaphone" class="w-3 h-3"></i>
                                @else
                                    <i data-lucide="graduation-cap" class="w-3 h-3"></i>
                                @endif
                                {{ $event->type }}
                            </span>
                        </div>

                        <!-- Description -->
                        <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                            {{ Str::limit($event->description, 100) }}
                        </p>

                        <!-- Event Details -->
                        <div class="space-y-2 mb-4">
                            <div class="flex items-center text-gray-500 text-sm">
                                <i data-lucide="calendar" class="w-4 h-4 mr-2 text-gray-400"></i>
                                <span>{{ $event->formatted_date }}</span>
                            </div>
                            
                            <div class="flex items-center text-gray-500 text-sm">
                                <i data-lucide="map-pin" class="w-4 h-4 mr-2 text-gray-400"></i>
                                <span class="truncate">{{ $event->location }}</span>
                            </div>
                            
                            <div class="flex items-center text-gray-500 text-sm">
                                <i data-lucide="user" class="w-4 h-4 mr-2 text-gray-400"></i>
                                <span>{{ $event->organizer->name }}</span>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                            <div class="flex items-center text-sm text-gray-500">
                                <i data-lucide="users" class="w-4 h-4 mr-1"></i>
                                <span>{{ $event->participants_count }} participant{{ $event->participants_count > 1 ? 's' : '' }}</span>
                            </div>
                            <a href="{{ route('events.show', $event) }}" 
                               class="inline-flex items-center gap-1 text-sm font-medium text-emerald-600 hover:text-emerald-700 transition-colors">
                                <span>Voir détails</span>
                                <i data-lucide="arrow-right" class="w-4 h-4"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="text-center py-12">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="calendar-x" class="w-8 h-8 text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun événement disponible</h3>
                        <p class="text-gray-500 mb-6 max-w-sm mx-auto">Il n'y a actuellement aucun événement programmé. Créez le premier événement pour votre communauté.</p>
                        <a href="{{ route('events.create') }}" 
                           class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                            <i data-lucide="plus-circle" class="w-4 h-4"></i>
                            <span>Créer le premier événement</span>
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

    <!-- Pagination -->
    @if($events->hasPages())
        <div class="mt-6 flex justify-center">
            {{ $events->links() }}
        </div>
    @endif
</div>

<style>
.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endsection