@extends('layouts.admin')

@section('title', 'Gestion Events - Admin')
@section('page-title', 'Gestion des Événements')
@section('page-subtitle', 'Créer et gérer les événements de la plateforme')

@section('content')
<div class="p-6 space-y-6">
    <!-- Stats rapides -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 bg-purple-600 rounded-xl flex items-center justify-center">
                    <i data-lucide="calendar" class="w-6 h-6 text-white"></i>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-white">{{ number_format($totalEvents ?? 0) }}</div>
                    <div class="text-sm text-gray-400">Total Events</div>
                </div>
            </div>
        </div>
        
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 bg-green-600 rounded-xl flex items-center justify-center">
                    <i data-lucide="play-circle" class="w-6 h-6 text-white"></i>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-white">{{ number_format($activeEvents ?? 0) }}</div>
                    <div class="text-sm text-gray-400">Actifs</div>
                </div>
            </div>
        </div>
        
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center">
                    <i data-lucide="users" class="w-6 h-6 text-white"></i>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-white">{{ number_format($totalParticipants ?? 0) }}</div>
                    <div class="text-sm text-gray-400">Participants</div>
                </div>
            </div>
        </div>
        
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 bg-yellow-600 rounded-xl flex items-center justify-center">
                    <i data-lucide="clock" class="w-6 h-6 text-white"></i>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-white">{{ number_format($upcomingEvents ?? 0) }}</div>
                    <div class="text-sm text-gray-400">À Venir</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="flex justify-between items-center">
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-4 flex-1 mr-4">
            <form method="GET" class="flex items-center gap-4">
                <div class="flex-1">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Rechercher un événement..."
                           class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                <select name="status" class="px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                    <option value="">Tous</option>
                    <option value="upcoming" {{ request('status') === 'upcoming' ? 'selected' : '' }}>À venir</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Actif</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Terminé</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </button>
            </form>
        </div>
        <button onclick="createEvent()" class="px-6 py-3 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-colors flex items-center gap-2">
            <i data-lucide="plus" class="w-5 h-5"></i>
            Créer Event
        </button>
    </div>

    <!-- Liste des événements -->
    <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-700">
            <h3 class="text-lg font-semibold text-white">Tous les Événements</h3>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
            @forelse($events ?? [] as $event)
            <div class="bg-gray-750 border border-gray-700 rounded-xl overflow-hidden hover:border-gray-600 transition-all">
                @if($event->image)
                <img src="{{ asset('storage/' . $event->image) }}" 
                     alt="{{ $event->title }}" 
                     class="w-full h-48 object-cover">
                @else
                <div class="w-full h-48 bg-gradient-to-br from-purple-600 to-blue-600 flex items-center justify-center">
                    <i data-lucide="calendar" class="w-16 h-16 text-white"></i>
                </div>
                @endif
                
                <div class="p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            @if($event->status === 'active') bg-green-900 text-green-200
                            @elseif($event->status === 'upcoming') bg-yellow-900 text-yellow-200
                            @else bg-gray-900 text-gray-400
                            @endif">
                            {{ ucfirst($event->status ?? 'upcoming') }}
                        </span>
                        <span class="text-xs text-gray-400">
                            <i data-lucide="users" class="w-3 h-3 inline"></i>
                            {{ $event->participants_count ?? 0 }}
                        </span>
                    </div>
                    
                    <h4 class="text-lg font-semibold text-white mb-2">{{ $event->title }}</h4>
                    <p class="text-sm text-gray-400 mb-3 line-clamp-2">{{ $event->description }}</p>
                    
                    <div class="space-y-2 mb-4">
                        <div class="flex items-center text-sm text-gray-400">
                            <i data-lucide="calendar" class="w-4 h-4 mr-2"></i>
                            {{ \Carbon\Carbon::parse($event->date)->format('d/m/Y H:i') }}
                        </div>
                        <div class="flex items-center text-sm text-gray-400">
                            <i data-lucide="map-pin" class="w-4 h-4 mr-2"></i>
                            {{ $event->location ?? 'À définir' }}
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <button onclick="editEvent({{ $event->id }})" 
                                class="flex-1 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                            <i data-lucide="edit" class="w-4 h-4 inline mr-1"></i>
                            Éditer
                        </button>
                        <button onclick="deleteEvent({{ $event->id }})" 
                                class="px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full py-12 text-center text-gray-400">
                <i data-lucide="calendar" class="w-16 h-16 mx-auto mb-4 text-gray-600"></i>
                <p class="text-lg">Aucun événement trouvé</p>
                <button onclick="createEvent()" class="mt-4 px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    Créer votre premier événement
                </button>
            </div>
            @endforelse
        </div>
        
        @if(isset($events) && $events->hasPages())
        <div class="px-6 py-4 border-t border-gray-700">
            {{ $events->links() }}
        </div>
        @endif
    </div>
</div>

<script>
function createEvent() {
    window.location.href = '/admin/events/create';
}

function editEvent(eventId) {
    window.location.href = `/admin/events/${eventId}/edit`;
}

function deleteEvent(eventId) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer cet événement ?')) return;
    
    fetch(`/admin/events/${eventId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.message || 'Erreur lors de la suppression');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erreur lors de la suppression');
    });
}
</script>
@endsection