@extends('layouts.admin')

@section('title', 'Détail de l\'événement')
@section('page-title', 'Détail de l\'événement')

@section('content')
<div class="max-w-2xl mx-auto p-6 bg-gray-800 rounded-xl mt-8">
    <h2 class="text-2xl font-bold mb-4 text-white">{{ $event->title }}</h2>
    <div class="mb-4">
        <span class="text-gray-400">Description :</span>
        <div class="text-white">{{ $event->description }}</div>
    </div>
    <div class="mb-4">
        <span class="text-gray-400">Date :</span>
        <span class="text-white">{{ \Carbon\Carbon::parse($event->date)->format('d/m/Y H:i') }}</span>
    </div>
    <div class="mb-4">
        <span class="text-gray-400">Lieu :</span>
        <span class="text-white">{{ $event->location ?? 'À définir' }}</span>
    </div>
    <div class="mb-4">
        <span class="text-gray-400">Statut :</span>
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
            @if($event->status === 'active') bg-green-900 text-green-200
            @elseif($event->status === 'upcoming') bg-yellow-900 text-yellow-200
            @else bg-gray-900 text-gray-400
            @endif">
            {{ ucfirst($event->status ?? 'upcoming') }}
        </span>
    </div>
    <div class="mb-4">
        <span class="text-gray-400">Participants :</span>
        <span class="text-white">{{ $event->participants_count ?? 0 }}</span>
    </div>
    <div class="flex gap-2 mt-6">
    <a href="/admin/events" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">Retour</a>
    </div>
</div>
<script>
function deleteEvent(eventId) {
    if (!confirm('Supprimer cet événement ?')) return;
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
            window.location.href = '/admin/events';
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
