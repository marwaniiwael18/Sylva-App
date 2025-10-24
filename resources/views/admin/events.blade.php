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
            <button onclick="createEventAdmin()" class="px-6 py-3 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-colors flex items-center gap-2">
            <i data-lucide="plus" class="w-5 h-5"></i>
            Créer Event
        </button>
    </div>

    <!-- Liste des événements -->
    <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-x-auto mt-6">
        <table class="min-w-full divide-y divide-gray-700 text-white">
            <thead class="bg-gray-900">
                <tr>
                    <th class="px-4 py-3 text-left">Titre</th>
                    <th class="px-4 py-3 text-left">Description</th>
                    <th class="px-4 py-3 text-left">Date</th>
                    <th class="px-4 py-3 text-left">Lieu</th>
                    <th class="px-4 py-3 text-left">Statut</th>
                    <th class="px-4 py-3 text-left">Participants</th>
                    <th class="px-4 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700">
                @forelse($events ?? [] as $event)
                <tr>
                    <td class="px-4 py-3 font-semibold">{{ $event->title }}</td>
                    <td class="px-4 py-3 max-w-xs truncate">{{ $event->description }}</td>
                    <td class="px-4 py-3">{{ \Carbon\Carbon::parse($event->date)->format('d/m/Y H:i') }}</td>
                    <td class="px-4 py-3">{{ $event->location ?? 'À définir' }}</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($event->status === 'active') bg-green-900 text-green-200
                            @elseif($event->status === 'upcoming') bg-yellow-900 text-yellow-200
                            @else bg-gray-900 text-gray-400
                            @endif">
                            {{ ucfirst($event->status ?? 'upcoming') }}
                        </span>
                    </td>
                    <td class="px-4 py-3">{{ $event->participants_count ?? 0 }}</td>
                    <td class="px-4 py-3 flex gap-2">
                        <button onclick="showEvent({{ $event->id }})" class="px-2 py-1 bg-gray-700 rounded hover:bg-gray-600" title="Voir"><i data-lucide="eye" class="w-4 h-4"></i></button>
                        <button onclick="editEvent({{ $event->id }})" class="px-2 py-1 bg-blue-600 rounded hover:bg-blue-700" title="Éditer"><i data-lucide="edit" class="w-4 h-4"></i></button>
                        <button onclick="deleteEvent({{ $event->id }})" class="px-2 py-1 bg-red-600 rounded hover:bg-red-700" title="Supprimer"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-8 text-gray-400">
                        Aucun événement trouvé.<br>
                            <button onclick="createEventAdmin()" class="mt-4 px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            Créer votre premier événement
                        </button>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if(isset($events) && $events->hasPages())
        <div class="px-6 py-4 border-t border-gray-700">
            {{ $events->links() }}
        </div>
        @endif
    </div>
</div>

<script>
function createEventAdmin() {
    window.location.href = '/admin/events/create';
}
function editEvent(eventId) {
    window.location.href = `/admin/events/${eventId}/edit`;
}
function showEvent(eventId) {
    window.location.href = `/admin/events/${eventId}`;
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