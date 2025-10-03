@extends('layouts.dashboard')

@section('title', 'Modifier l\'événement')

@section('page-content')
<div class="p-6">
    <div class="max-w-2xl mx-auto">
        <!-- Page Header -->
        <div class="mb-6">
            <div class="flex items-center mb-4">
                <a href="{{ route('events.show', $event) }}" class="mr-4 p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                            <i data-lucide="edit" class="w-5 h-5 text-white"></i>
                        </div>
                        Modifier l'événement
                    </h1>
                    <p class="mt-1 text-sm text-gray-600">Modifiez les détails de votre événement</p>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
            <form action="{{ route('events.update', $event) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid gap-6">
                    <!-- Titre -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                            Titre de l'événement *
                        </label>
                        <input type="text" 
                               id="title" 
                               name="title" 
                               value="{{ old('title', $event->title) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('title') border-red-500 @enderror"
                               placeholder="Ex: Journée de plantation d'arbres"
                               required>
                        @error('title')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Description *
                        </label>
                        <textarea id="description" 
                                  name="description" 
                                  rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror"
                                  placeholder="Décrivez l'événement en détail..."
                                  required>{{ old('description', $event->description) }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Date et heure -->
                    <div>
                        <label for="date" class="block text-sm font-medium text-gray-700 mb-2">
                            Date et heure *
                        </label>
                        <input type="datetime-local" 
                               id="date" 
                               name="date" 
                               value="{{ old('date', $event->date->format('Y-m-d\TH:i')) }}"
                               min="{{ now()->format('Y-m-d\TH:i') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('date') border-red-500 @enderror"
                               required>
                        @error('date')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Lieu -->
                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                            Lieu *
                        </label>
                        <input type="text" 
                               id="location" 
                               name="location" 
                               value="{{ old('location', $event->location) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('location') border-red-500 @enderror"
                               placeholder="Ex: Parc de Belvedère, Tunis"
                               required>
                        @error('location')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Type -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                            Type d'événement *
                        </label>
                        <select id="type" 
                                name="type"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('type') border-red-500 @enderror"
                                required>
                            <option value="">Sélectionnez un type</option>
                            @foreach(\App\Models\Event::TYPES as $type)
                                <option value="{{ $type }}" {{ old('type', $event->type) === $type ? 'selected' : '' }}>
                                    {{ $type }}
                                </option>
                            @endforeach
                        </select>
                        @error('type')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Boutons -->
                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                        <a href="{{ route('events.show', $event) }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                            Annuler
                        </a>
                        <button type="submit" 
                                class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white rounded-lg text-sm font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all transform hover:scale-105">
                            <i data-lucide="save" class="w-4 h-4"></i>
                            Mettre à jour
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection