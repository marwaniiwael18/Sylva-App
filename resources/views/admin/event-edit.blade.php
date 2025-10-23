@extends('layouts.admin')

@section('title', 'Éditer un événement')
@section('page-title', 'Éditer un événement')

@section('content')
<div class="max-w-2xl mx-auto p-6 bg-gray-800 rounded-xl mt-8">
    <form method="POST" action="/admin/events/{{ $event->id }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label class="block text-gray-300 mb-2">Titre</label>
            <input type="text" name="title" value="{{ old('title', $event->title) }}" class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white" required>
        </div>
        <div class="mb-4">
            <label class="block text-gray-300 mb-2">Description</label>
            <textarea name="description" class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white">{{ old('description', $event->description) }}</textarea>
        </div>
        <div class="mb-4">
            <label class="block text-gray-300 mb-2">Date</label>
            <input type="datetime-local" name="date" value="{{ old('date', \Carbon\Carbon::parse($event->date)->format('Y-m-d\TH:i')) }}" class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white" required>
        </div>
        <div class="mb-4">
            <label class="block text-gray-300 mb-2">Lieu</label>
            <input type="text" name="location" value="{{ old('location', $event->location) }}" class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white">
        </div>
        <div class="mb-4">
            <label class="block text-gray-300 mb-2">Statut</label>
            <select name="status" class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white" required>
                <option value="active" {{ old('status', $event->status) == 'active' ? 'selected' : '' }}>Actif</option>
                <option value="upcoming" {{ old('status', $event->status) == 'upcoming' ? 'selected' : '' }}>À venir</option>
                <option value="completed" {{ old('status', $event->status) == 'completed' ? 'selected' : '' }}>Terminé</option>
            </select>
        </div>
        <div class="flex justify-end">
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Mettre à jour</button>
        </div>
    </form>
</div>
@endsection
