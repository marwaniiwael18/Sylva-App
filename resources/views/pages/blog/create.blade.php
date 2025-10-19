@extends('layouts.dashboard')

@section('title', 'Créer un Article - Blog')

@section('page-content')
<div class="p-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('blog.index') }}" 
                   class="inline-flex items-center gap-2 px-3 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Retour au blog
                </a>
            </div>
            <!-- AI Toggle Button -->
            <button type="button" id="toggle-ai" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-pink-500 to-purple-600 hover:from-pink-600 hover:to-purple-700 text-white rounded-lg text-sm font-medium transition-all">
                <i data-lucide="sparkles" class="w-4 h-4"></i>
                <span id="ai-toggle-text">Activer l'IA</span>
            </button>
        </div>
        <div class="mt-4">
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center">
                    <i data-lucide="plus-circle" class="w-5 h-5 text-white"></i>
                </div>
                Créer un nouveau Article
            </h1>
            <p class="mt-1 text-sm text-gray-600">Partagez vos idées, questions ou expériences avec la communauté</p>
        </div>
    </div>

    <!-- Two Column Layout: AI Section + Main Form -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 max-w-7xl">
        <!-- AI Generator Section (Left Column - Initially Hidden) -->
        <div id="ai-section" class="lg:col-span-1 hidden">
            <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl border-2 border-purple-200 overflow-hidden shadow-lg sticky top-6">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <i data-lucide="sparkles" class="w-5 h-5 text-purple-600"></i>
                            Assistant IA
                        </h2>
                        <span class="px-3 py-1 bg-purple-100 text-purple-700 text-xs font-semibold rounded-full">BETA</span>
                    </div>

                    <div class="space-y-4">
                        <!-- Topic Input -->
                        <div>
                            <label for="ai-topic" class="block text-sm font-medium text-gray-700 mb-2">
                                Sujet de l'article
                            </label>
                            <textarea 
                                id="ai-topic" 
                                rows="3"
                                placeholder="Ex: Les bienfaits de la plantation d'arbres urbains..."
                                class="w-full px-4 py-3 border border-purple-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 bg-white text-sm"></textarea>
                        </div>

                        <!-- Quick Settings -->
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Ton</label>
                                <select id="ai-tone" class="w-full px-3 py-2 border border-purple-200 rounded-lg focus:ring-2 focus:ring-purple-500 bg-white text-sm">
                                    <option value="professional">Professionnel</option>
                                    <option value="casual">Décontracté</option>
                                    <option value="informative" selected>Informatif</option>
                                    <option value="enthusiastic">Enthousiaste</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Longueur</label>
                                <select id="ai-length" class="w-full px-3 py-2 border border-purple-200 rounded-lg focus:ring-2 focus:ring-purple-500 bg-white text-sm">
                                    <option value="short">Court (~150 mots)</option>
                                    <option value="medium" selected>Moyen (~300 mots)</option>
                                    <option value="long">Long (~500 mots)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Generate Button -->
                        <button type="button" id="generate-btn" class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-purple-500 to-pink-600 hover:from-purple-600 hover:to-pink-700 text-white rounded-lg font-medium transition-all shadow-lg">
                            <i data-lucide="wand-2" class="w-5 h-5"></i>
                            <span id="generate-text">Générer</span>
                        </button>

                        <!-- AI Status Messages -->
                        <div id="ai-status" class="hidden">
                            <div class="flex items-center gap-2 text-sm text-purple-700 bg-purple-100 px-4 py-2 rounded-lg">
                                <div class="animate-spin">
                                    <i data-lucide="loader-2" class="w-4 h-4"></i>
                                </div>
                                <span id="status-text">Génération...</span>
                            </div>
                        </div>
                        
                        <!-- AI Tools for Content -->
                        <div class="pt-4 border-t border-purple-200 space-y-2">
                            <p class="text-xs font-medium text-gray-700 mb-2">Outils d'amélioration:</p>
                            <button type="button" onclick="improveContent('expand')" class="w-full text-xs px-3 py-2 bg-white hover:bg-purple-50 text-purple-700 border border-purple-200 rounded-lg font-medium transition-colors">
                                <i data-lucide="maximize-2" class="w-3 h-3 inline"></i> Développer le contenu
                            </button>
                            <button type="button" onclick="improveContent('shorten')" class="w-full text-xs px-3 py-2 bg-white hover:bg-purple-50 text-purple-700 border border-purple-200 rounded-lg font-medium transition-colors">
                                <i data-lucide="minimize-2" class="w-3 h-3 inline"></i> Raccourcir le contenu
                            </button>
                            <button type="button" onclick="generateTitle()" class="w-full text-xs px-3 py-2 bg-white hover:bg-purple-50 text-purple-700 border border-purple-200 rounded-lg font-medium transition-colors">
                                <i data-lucide="sparkles" class="w-3 h-3 inline"></i> Générer un titre
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Form (Right Column) -->
        <div class="lg:col-span-3" id="main-form">
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-lg">
            <form method="POST" action="{{ route('blog.store') }}" class="p-8">
                @csrf
                
                <!-- Title -->
                <div class="mb-6">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        Titre de l'article *
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
                    <a href="{{ route('blog.index') }}" 
                       class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 transition-colors font-medium">
                        <i data-lucide="x" class="w-5 h-5"></i>
                        Annuler
                    </a>
                    
                    <button type="submit" 
                            class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-8 py-3 rounded-xl font-medium transition-colors shadow-lg">
                        <i data-lucide="send" class="w-5 h-5"></i>
                        Publier l'article
                    </button>
                </div>
            </form>
        </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let aiEnabled = false;

// Toggle AI Section
document.getElementById('toggle-ai').addEventListener('click', function() {
    aiEnabled = !aiEnabled;
    const aiSection = document.getElementById('ai-section');
    const toggleText = document.getElementById('ai-toggle-text');
    const mainForm = document.getElementById('main-form');
    
    if (aiEnabled) {
        aiSection.classList.remove('hidden');
        toggleText.textContent = 'Désactiver l\'IA';
        // Adjust main form column span on larger screens
        mainForm.classList.remove('lg:col-span-3');
        mainForm.classList.add('lg:col-span-2');
    } else {
        aiSection.classList.add('hidden');
        toggleText.textContent = 'Activer l\'IA';
        // Make main form full width when AI is disabled
        mainForm.classList.remove('lg:col-span-2');
        mainForm.classList.add('lg:col-span-3');
    }
});

// Generate Content with AI
document.getElementById('generate-btn').addEventListener('click', async function() {
    const topic = document.getElementById('ai-topic').value;
    const tone = document.getElementById('ai-tone').value;
    const length = document.getElementById('ai-length').value;
    
    if (!topic.trim()) {
        alert('Veuillez décrire le sujet de votre article');
        return;
    }
    
    const btn = this;
    const btnText = document.getElementById('generate-text');
    const status = document.getElementById('ai-status');
    const statusText = document.getElementById('status-text');
    
    // Show loading state
    btn.disabled = true;
    btnText.textContent = 'Génération...';
    status.classList.remove('hidden');
    statusText.textContent = 'L\'IA analyse votre demande...';
    
    try {
        const formData = new FormData();
        formData.append('topic', topic);
        formData.append('tone', tone);
        formData.append('length', length);
        formData.append('_token', '{{ csrf_token() }}');
        
        statusText.textContent = 'Génération du contenu en cours...';
        
        const response = await fetch('{{ route('blog.ai.generate') }}', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Fill in the form
            document.getElementById('title').value = data.title;
            document.getElementById('content').value = data.content;
            
            // Show success message
            statusText.textContent = 'Contenu généré! ✨';
            status.classList.add('bg-green-100', 'text-green-700');
            status.classList.remove('bg-purple-100', 'text-purple-700');
            
            setTimeout(() => {
                status.classList.add('hidden');
                status.classList.remove('bg-green-100', 'text-green-700');
                status.classList.add('bg-purple-100', 'text-purple-700');
            }, 3000);
            
            // Scroll to title
            document.getElementById('title').scrollIntoView({ behavior: 'smooth', block: 'center' });
        } else {
            throw new Error(data.message || 'Erreur de génération');
        }
    } catch (error) {
        statusText.textContent = 'Erreur: ' + error.message;
        status.classList.add('bg-red-100', 'text-red-700');
        status.classList.remove('bg-purple-100', 'text-purple-700');
        
        setTimeout(() => {
            status.classList.add('hidden');
            status.classList.remove('bg-red-100', 'text-red-700');
            status.classList.add('bg-purple-100', 'text-purple-700');
        }, 5000);
    } finally {
        btn.disabled = false;
        btnText.textContent = 'Générer';
    }
});

// Generate Title Only
async function generateTitle() {
    const content = document.getElementById('content').value;
    if (!content.trim()) {
        alert('Veuillez d\'abord écrire du contenu');
        return;
    }
    
    try {
        const formData = new FormData();
        formData.append('topic', content.substring(0, 200));
        formData.append('tone', 'informative');
        formData.append('length', 'short');
        formData.append('_token', '{{ csrf_token() }}');
        
        const response = await fetch('{{ route('blog.ai.generate') }}', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        if (data.success && data.title) {
            document.getElementById('title').value = data.title;
        }
    } catch (error) {
        console.error('Erreur génération titre:', error);
    }
}

// Improve Content
async function improveContent(instruction) {
    const content = document.getElementById('content').value;
    
    if (!content.trim()) {
        alert('Aucun contenu à améliorer');
        return;
    }
    
    try {
        const response = await fetch('{{ route('blog.ai.improve') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ content, instruction })
        });
        
        const data = await response.json();
        
        if (data.success) {
            document.getElementById('content').value = data.content;
        } else {
            alert('Erreur: ' + data.message);
        }
    } catch (error) {
        alert('Une erreur est survenue');
        console.error(error);
    }
}
</script>
@endpush

@if(session('success'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
     class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
    {{ session('success') }}
</div>
@endif
@endsection
