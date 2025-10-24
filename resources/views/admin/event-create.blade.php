@extends('layouts.admin')

@section('title', 'Créer un événement')
@section('page-title', 'Créer un événement')

@section('content')
<div class="max-w-2xl mx-auto p-6 bg-gray-800 rounded-xl mt-8">
    <form method="POST" action="/admin/events" enctype="multipart/form-data">
        @csrf
        <div class="mb-4">
            <label class="block text-gray-300 mb-2">Titre</label>
            <input type="text" name="title" class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white" required>
        </div>
        <div class="mb-4">
            <label class="block text-gray-300 mb-2">Description</label>
            <textarea name="description" class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white"></textarea>
        </div>
        <div class="mb-4">
            <label class="block text-gray-300 mb-2">Date</label>
            <input type="datetime-local" name="date" class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white" required>
        </div>
        <div class="mb-4">
            <label class="block text-gray-300 mb-2">Lieu</label>
            <input type="text" name="location" class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white">
        </div>
        <div class="mb-4">
            <label class="block text-gray-300 mb-2">Statut</label>
            <select name="status" class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white" required>
                <option value="active">Actif</option>
                <option value="upcoming">À venir</option>
                <option value="completed">Terminé</option>
            </select>
        </div>
        <div class="flex justify-end">
            <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Créer</button>
        </div>
    </form>
</div>
@endsection
