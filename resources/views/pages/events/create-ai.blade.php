@extends('layouts.dashboard')

@section('title', 'Créer un Événement')

@section('page-content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50 to-indigo-100 p-6">
    <div class="max-w-7xl mx-auto">
        <!-- Page Header -->
        <div class="mb-6">
            <div class="flex items-center mb-4">
                <a href="{{ route('events.index') }}" class="mr-3 p-2 text-gray-400 hover:text-gray-600 hover:bg-white/50 rounded-xl transition-all">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold bg-gradient-to-r from-emerald-600 to-green-600 bg-clip-text text-transparent flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-green-600 rounded-2xl flex items-center justify-center shadow-lg">
                            <i data-lucide="sparkles" class="w-5 h-5 text-white"></i>
                        </div>
                        Créer un Événement
                    </h1>
                    <p class="mt-1 text-sm text-gray-600 font-medium">✨ Utilisez l'IA pour créer un événement en quelques secondes!</p>
                </div>
            </div>
        </div>

        <!-- Grid Layout: AI Assistant + Form -->
        <div class="grid lg:grid-cols-2 gap-5">
            <!-- Colonne 1: AI Assistant (Gauche) -->
            <div class="space-y-4">
                <!-- AI Assistant Card -->
                <div class="bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 rounded-2xl shadow-lg border border-blue-100/50 p-5 text-gray-700 sticky top-6">
                    <div class="flex items-start gap-3 mb-4">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm">
                            <i data-lucide="bot" class="w-5 h-5 text-blue-400"></i>
                        </div>
                        <div class="flex-1">
                            <h2 class="text-base font-bold mb-1 text-gray-700">🤖 Assistant IA</h2>
                            <p class="text-xs text-gray-500">Décrivez simplement votre événement et laissez l'IA faire le reste!</p>
                        </div>
                    </div>

                    <div class="bg-white/60 backdrop-blur rounded-xl p-4 border border-blue-100/50">
                        <label for="ai-input" class="block text-xs font-semibold mb-2 text-gray-600">
                            💬 Décrivez votre événement :
                        </label>
                        <textarea 
                            id="ai-input" 
                            rows="4" 
                            class="w-full px-3 py-2 bg-white/80 text-gray-700 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-300 transition-all text-xs placeholder-gray-400"
                            placeholder="Ex: Je veux organiser une plantation d'oliviers dans le nord de la Tunisie pour restaurer l'écosystème local..."
                        ></textarea>
                        
                        <button 
                            type="button"
                            id="generate-btn"
                            class="mt-3 w-full bg-gradient-to-r from-blue-200 to-indigo-200 hover:from-blue-300 hover:to-indigo-300 text-gray-700 font-semibold py-2 px-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-300 transition-all shadow-sm flex items-center justify-center gap-2 text-xs"
                        >
                            <i data-lucide="sparkles" class="w-4 h-4"></i>
                            <span>Générer avec l'IA</span>
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </button>

                        <!-- Loading State -->
                        <div id="loading-state" class="hidden mt-3 text-center">
                            <div class="inline-flex items-center gap-2 bg-blue-50 border border-blue-100 px-4 py-2 rounded-full">
                                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-300"></div>
                                <span class="font-medium text-xs text-gray-600">Génération en cours...</span>
                            </div>
                        </div>

                        <!-- Success Message -->
                        <div id="success-message" class="hidden mt-3 bg-green-50 border border-green-200 rounded-lg p-2 flex items-center gap-2">
                            <i data-lucide="check-circle" class="w-4 h-4 text-green-400"></i>
                            <span class="font-medium text-xs text-gray-600">✅ Formulaire rempli automatiquement!</span>
                        </div>

                        <!-- AI Suggestions Box -->
                        <div id="ai-suggestions" class="hidden mt-3 bg-amber-50/50 backdrop-blur border border-amber-100 rounded-xl p-3">
                            <div class="flex items-start gap-2 mb-2">
                                <i data-lucide="lightbulb" class="w-4 h-4 text-amber-400 mt-1"></i>
                                <div>
                                    <h4 class="font-semibold text-xs mb-2 text-gray-600">💡 Suggestions de l'IA</h4>
                                    <div id="suggestions-content" class="text-xs text-gray-500 space-y-1"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tips Box -->
                    <div class="mt-4 bg-white/60 backdrop-blur border border-blue-100/50 rounded-xl p-3">
                        <h4 class="font-semibold text-xs mb-2 flex items-center gap-2 text-gray-600">
                            <i data-lucide="info" class="w-3 h-3 text-blue-400"></i>
                            💡 Conseils pour une meilleure génération
                        </h4>
                        <ul class="text-xs text-gray-500 space-y-1.5">
                            <li class="flex items-start gap-2">
                                <span class="text-blue-300">•</span>
                                <span>Soyez précis sur le lieu et la date</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-blue-300">•</span>
                                <span>Mentionnez le type d'activité souhaitée</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-blue-300">•</span>
                                <span>Indiquez le public cible (familles, étudiants, etc.)</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-blue-300">•</span>
                                <span>Plus de détails = meilleur résultat !</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Colonne 2: Form (Droite) -->
            <div class="space-y-4">
                <!-- Form Card -->
                <div class="bg-white/80 backdrop-blur rounded-2xl border border-white/50 shadow-xl p-5">
            <div class="flex items-center gap-2 mb-4 pb-3 border-b border-gray-200">
                <i data-lucide="edit-3" class="w-5 h-5 text-blue-600"></i>
                <h3 class="text-lg font-bold text-gray-800">Détails de l'événement</h3>
            </div>

            <form action="{{ route('events.store') }}" method="POST" id="event-form">
                @csrf

                <div class="grid gap-4">
                    <!-- Titre -->
                    <div>
                        <label for="title" class="block text-xs font-bold text-gray-700 mb-2 flex items-center gap-1">
                            <i data-lucide="type" class="w-3 h-3 text-blue-600"></i>
                            Titre de l'événement *
                        </label>
                        <input type="text" 
                               id="title" 
                               name="title" 
                               value="{{ old('title') }}"
                               class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm @error('title') border-red-500 @enderror"
                               placeholder="Ex: Journée de plantation d'arbres"
                               required>
                        @error('title')
                            <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                <i data-lucide="alert-circle" class="w-3 h-3"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-xs font-bold text-gray-700 mb-2 flex items-center gap-1">
                            <i data-lucide="file-text" class="w-3 h-3 text-blue-600"></i>
                            Description *
                        </label>
                        <textarea 
                            id="description" 
                            name="description" 
                            rows="5"
                            class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm @error('description') border-red-500 @enderror"
                            placeholder="Décrivez votre événement en détail..."
                            required>{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                <i data-lucide="alert-circle" class="w-3 h-3"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Type d'événement -->
                    <div>
                        <label for="type" class="block text-xs font-bold text-gray-700 mb-2 flex items-center gap-1">
                            <i data-lucide="tag" class="w-3 h-3 text-blue-600"></i>
                            Type d'événement *
                        </label>
                        <select 
                            id="type" 
                            name="type"
                            class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm @error('type') border-red-500 @enderror"
                            required>
                            <option value="">Sélectionnez un type</option>
                            <option value="Tree Planting" {{ old('type') == 'Tree Planting' ? 'selected' : '' }}>🌱 Tree Planting</option>
                            <option value="Maintenance" {{ old('type') == 'Maintenance' ? 'selected' : '' }}>🔧 Maintenance</option>
                            <option value="Awareness" {{ old('type') == 'Awareness' ? 'selected' : '' }}>📢 Awareness</option>
                            <option value="Workshop" {{ old('type') == 'Workshop' ? 'selected' : '' }}>🎓 Workshop</option>
                        </select>
                        @error('type')
                            <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                <i data-lucide="alert-circle" class="w-3 h-3"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="grid md:grid-cols-2 gap-4">
                        <!-- Date -->
                        <div>
                            <label for="date" class="block text-xs font-bold text-gray-700 mb-2 flex items-center gap-1">
                                <i data-lucide="calendar" class="w-3 h-3 text-blue-600"></i>
                                Date et heure *
                            </label>
                            <input type="datetime-local" 
                                   id="date" 
                                   name="date" 
                                   value="{{ old('date') }}"
                                   class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm @error('date') border-red-500 @enderror"
                                   required>
                            @error('date')
                                <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                    <i data-lucide="alert-circle" class="w-3 h-3"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Lieu -->
                        <div>
                            <label for="location" class="block text-xs font-bold text-gray-700 mb-2 flex items-center gap-1">
                                <i data-lucide="map-pin" class="w-3 h-3 text-blue-600"></i>
                                Lieu *
                            </label>
                            <input type="text" 
                                   id="location" 
                                   name="location" 
                                   value="{{ old('location') }}"
                                   class="w-full px-3 py-2 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm @error('location') border-red-500 @enderror"
                                   placeholder="Ex: Parc Belvedère, Tunis"
                                   required>
                            @error('location')
                                <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                    <i data-lucide="alert-circle" class="w-3 h-3"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <!-- Boutons d'action -->
                    <div class="flex gap-3 pt-4 border-t border-gray-200">
                        <button 
                            type="submit"
                            class="flex-1 bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white font-bold py-3 px-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500/30 transition-all shadow-lg flex items-center justify-center gap-2 text-sm"
                        >
                            <i data-lucide="check-circle" class="w-4 h-4"></i>
                            Créer l'événement
                        </button>
                        <a 
                            href="{{ route('events.index') }}"
                            class="px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl focus:outline-none focus:ring-2 focus:ring-gray-300/30 transition-all flex items-center justify-center gap-2 text-sm"
                        >
                            <i data-lucide="x" class="w-4 h-4"></i>
                            Annuler
                        </a>
                    </div>
                </div>
            </form>
        </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const generateBtn = document.getElementById('generate-btn');
    const aiInput = document.getElementById('ai-input');
    const loadingState = document.getElementById('loading-state');
    const successMessage = document.getElementById('success-message');
    const aiSuggestions = document.getElementById('ai-suggestions');
    
    generateBtn.addEventListener('click', async function() {
        const userInput = aiInput.value.trim();
        
        if (!userInput) {
            alert('Veuillez décrire votre événement d\'abord!');
            return;
        }

        // Show loading
        loadingState.classList.remove('hidden');
        successMessage.classList.add('hidden');
        generateBtn.disabled = true;

        try {
            const response = await fetch('/api/ai/generate-event', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Authorization': 'Bearer ' + localStorage.getItem('auth_token')
                },
                body: JSON.stringify({
                    description: userInput
                })
            });

            const data = await response.json();

            if (data.success && data.data) {
                // Fill form fields
                document.getElementById('title').value = data.data.title || '';
                document.getElementById('description').value = data.data.description || '';
                
                // Set type
                const typeSelect = document.getElementById('type');
                if (data.data.type) {
                    typeSelect.value = data.data.type;
                }
                
                // Set location if suggested
                if (data.data.location_suggestion) {
                    document.getElementById('location').value = data.data.location_suggestion;
                }

                // Set date if provided
                if (data.data.date) {
                    document.getElementById('date').value = data.data.date;
                }

                // Show suggestions
                if (data.data.recommendations && data.data.recommendations.length > 0) {
                    const suggestionsContent = document.getElementById('suggestions-content');
                    let html = '<ul class="space-y-2">';
                    
                    if (data.data.best_period) {
                        html += `<li class="flex items-start gap-2"><span class="text-blue-600">📅</span><strong>Période idéale:</strong> ${data.data.best_period}</li>`;
                    }
                    
                    if (data.data.duration) {
                        html += `<li class="flex items-start gap-2"><span class="text-blue-600">⏱️</span><strong>Durée:</strong> ${data.data.duration}</li>`;
                    }
                    
                    if (data.data.materials_needed) {
                        html += `<li class="flex items-start gap-2"><span class="text-blue-600">🎒</span><strong>Matériel:</strong> ${data.data.materials_needed}</li>`;
                    }
                    
                    html += '<li class="font-semibold mt-3">Recommandations:</li>';
                    data.data.recommendations.forEach(rec => {
                        html += `<li class="flex items-start gap-2"><span class="text-emerald-600">✓</span>${rec}</li>`;
                    });
                    
                    html += '</ul>';
                    suggestionsContent.innerHTML = html;
                    aiSuggestions.classList.remove('hidden');
                }

                // Show success message
                successMessage.classList.remove('hidden');
                
                // Scroll to form
                document.getElementById('event-form').scrollIntoView({ behavior: 'smooth', block: 'start' });
            } else {
                alert('Erreur: ' + (data.message || 'Impossible de générer l\'événement'));
            }

        } catch (error) {
            console.error('Error:', error);
            alert('Une erreur s\'est produite. Veuillez réessayer.');
        } finally {
            loadingState.classList.add('hidden');
            generateBtn.disabled = false;
        }
    });
});
</script>
@endpush
@endsection
