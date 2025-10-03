@extends('layouts.dashboard')

@section('title', 'Créer un Post - Forum')

@section('page-content')
<div class="p-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('forum.index') }}" 
               class="inline-flex items-center gap-2 px-3 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Retour au forum
            </a>
        </div>
        <div class="mt-4">
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center">
                    <i data-lucide="plus-circle" class="w-5 h-5 text-white"></i>
                </div>
                Créer un nouveau post
            </h1>
            <p class="mt-1 text-sm text-gray-600">Partagez vos idées, questions ou expériences avec la communauté</p>
        </div>
    </div>

    <!-- Form -->
    <div class="max-w-4xl">
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-lg">
            <form method="POST" action="{{ route('forum.store') }}" class="p-8">
                @csrf
                
                <!-- Title -->
                <div class="mb-6">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        Titre du post *
                    </label>
                    <input type="text" 
                           id="title" 
                           name="title" 
                           value="{{ old('title') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent @error('title') border-red-500 @enderror"
                           placeholder="Ex: Comment entretenir les oliviers plantés la semaine dernière ?"
                           required>
                    @error('title')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Related Event -->
                <div class="mb-6">
                    <label for="related_event_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Événement associé (optionnel)
                    </label>
                    <select id="related_event_id" 
                            name="related_event_id" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent @error('related_event_id') border-red-500 @enderror">
                        <option value="">Aucun événement associé</option>
                        @foreach($events as $event)
                            <option value="{{ $event->id }}" {{ old('related_event_id') == $event->id ? 'selected' : '' }}>
                                {{ $event->title }} - {{ $event->created_at->format('d/m/Y') }}
                            </option>
                        @endforeach
                    </select>
                    @error('related_event_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-sm text-gray-500">
                        Associez votre post à un événement spécifique pour faciliter la navigation et les discussions contextuelles.
                    </p>
                </div>

                <!-- Content -->
                <div class="mb-8">
                    <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                        Contenu *
                    </label>
                    <textarea id="content" 
                              name="content" 
                              rows="12"
                              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent @error('content') border-red-500 @enderror"
                              placeholder="Décrivez votre question, partagez votre expérience ou lancez une discussion..."
                              required>{{ old('content') }}</textarea>
                    @error('content')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <a href="{{ route('forum.index') }}" 
                       class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 transition-colors font-medium">
                        <i data-lucide="x" class="w-5 h-5"></i>
                        Annuler
                    </a>
                    
                    <div class="flex items-center gap-3">
                        <button type="submit" 
                                class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-8 py-3 rounded-xl font-medium transition-colors shadow-lg">
                            <i data-lucide="send" class="w-5 h-5"></i>
                            Publier le post
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Tips Sidebar -->
<div class="fixed right-6 top-1/2 transform -translate-y-1/2 hidden xl:block">
    <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-6 max-w-xs">
        <h3 class="font-semibold text-emerald-800 mb-3 flex items-center gap-2">
            <i data-lucide="lightbulb" class="w-5 h-5"></i>
            Conseils pour un bon post
        </h3>
        <ul class="space-y-2 text-sm text-emerald-700">
            <li class="flex items-start gap-2">
                <div class="w-1.5 h-1.5 bg-emerald-400 rounded-full mt-2 flex-shrink-0"></div>
                Soyez précis dans votre titre
            </li>
            <li class="flex items-start gap-2">
                <div class="w-1.5 h-1.5 bg-emerald-400 rounded-full mt-2 flex-shrink-0"></div>
                Associez votre post à un événement si pertinent
            </li>
            <li class="flex items-start gap-2">
                <div class="w-1.5 h-1.5 bg-emerald-400 rounded-full mt-2 flex-shrink-0"></div>
                Décrivez clairement votre question ou votre expérience
            </li>
            <li class="flex items-start gap-2">
                <div class="w-1.5 h-1.5 bg-emerald-400 rounded-full mt-2 flex-shrink-0"></div>
                Restez respectueux et constructif
            </li>
        </ul>
    </div>
</div>
@endsection