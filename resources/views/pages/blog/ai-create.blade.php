@extends('layouts.dashboard')

@section('title', 'Créer un Article avec IA - Blog')

@section('page-content')
<div class="p-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('blog.index') }}" 
               class="inline-flex items-center gap-2 px-3 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Retour au blog
            </a>
        </div>
        <div class="mt-4">
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center">
                    <i data-lucide="sparkles" class="w-5 h-5 text-white"></i>
                </div>
                Générer un Article avec l'IA
            </h1>
            <p class="mt-1 text-sm text-gray-600">Laissez l'intelligence artificielle vous aider à créer du contenu de qualité</p>
        </div>
    </div>

    <div class="max-w-4xl">
        <!-- AI Generator Section -->
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-lg mb-6">
            <div class="p-8">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <i data-lucide="wand-2" class="w-5 h-5 text-purple-600"></i>
                    Paramètres de génération
                </h2>

                <form id="ai-form" class="space-y-6">
                    @csrf
                    
                    <!-- Topic Input -->
                    <div>
                        <label for="topic" class="block text-sm font-medium text-gray-700 mb-2">
                            Sujet de l'article *
                        </label>
                        <textarea 
                            id="topic" 
                            name="topic" 
                            rows="3"
                            required
                            placeholder="Ex: Les bienfaits de la plantation d'arbres en milieu urbain..."
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"></textarea>
                        <p class="mt-1 text-sm text-gray-500">Décrivez le sujet que vous souhaitez aborder</p>
                    </div>

                    <!-- Tone Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Ton de l'article
                        </label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <label class="relative flex items-center justify-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-purple-500 transition-colors">
                                <input type="radio" name="tone" value="professional" class="sr-only peer" checked>
                                <div class="text-center peer-checked:text-purple-600">
                                    <i data-lucide="briefcase" class="w-5 h-5 mx-auto mb-1"></i>
                                    <span class="text-sm font-medium">Professionnel</span>
                                </div>
                                <div class="absolute inset-0 border-2 border-purple-500 rounded-lg opacity-0 peer-checked:opacity-100"></div>
                            </label>
                            <label class="relative flex items-center justify-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-purple-500 transition-colors">
                                <input type="radio" name="tone" value="casual" class="sr-only peer">
                                <div class="text-center peer-checked:text-purple-600">
                                    <i data-lucide="coffee" class="w-5 h-5 mx-auto mb-1"></i>
                                    <span class="text-sm font-medium">Décontracté</span>
                                </div>
                                <div class="absolute inset-0 border-2 border-purple-500 rounded-lg opacity-0 peer-checked:opacity-100"></div>
                            </label>
                            <label class="relative flex items-center justify-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-purple-500 transition-colors">
                                <input type="radio" name="tone" value="informative" class="sr-only peer">
                                <div class="text-center peer-checked:text-purple-600">
                                    <i data-lucide="book-open" class="w-5 h-5 mx-auto mb-1"></i>
                                    <span class="text-sm font-medium">Informatif</span>
                                </div>
                                <div class="absolute inset-0 border-2 border-purple-500 rounded-lg opacity-0 peer-checked:opacity-100"></div>
                            </label>
                            <label class="relative flex items-center justify-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-purple-500 transition-colors">
                                <input type="radio" name="tone" value="enthusiastic" class="sr-only peer">
                                <div class="text-center peer-checked:text-purple-600">
                                    <i data-lucide="zap" class="w-5 h-5 mx-auto mb-1"></i>
                                    <span class="text-sm font-medium">Enthousiaste</span>
                                </div>
                                <div class="absolute inset-0 border-2 border-purple-500 rounded-lg opacity-0 peer-checked:opacity-100"></div>
                            </label>
                        </div>
                    </div>

                    <!-- Length Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Longueur de l'article
                        </label>
                        <div class="grid grid-cols-3 gap-3">
                            <label class="relative flex items-center justify-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-purple-500 transition-colors">
                                <input type="radio" name="length" value="short" class="sr-only peer">
                                <div class="text-center peer-checked:text-purple-600">
                                    <span class="text-sm font-medium">Court</span>
                                    <p class="text-xs text-gray-500">~150 mots</p>
                                </div>
                                <div class="absolute inset-0 border-2 border-purple-500 rounded-lg opacity-0 peer-checked:opacity-100"></div>
                            </label>
                            <label class="relative flex items-center justify-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-purple-500 transition-colors">
                                <input type="radio" name="length" value="medium" class="sr-only peer" checked>
                                <div class="text-center peer-checked:text-purple-600">
                                    <span class="text-sm font-medium">Moyen</span>
                                    <p class="text-xs text-gray-500">~300 mots</p>
                                </div>
                                <div class="absolute inset-0 border-2 border-purple-500 rounded-lg opacity-0 peer-checked:opacity-100"></div>
                            </label>
                            <label class="relative flex items-center justify-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-purple-500 transition-colors">
                                <input type="radio" name="length" value="long" class="sr-only peer">
                                <div class="text-center peer-checked:text-purple-600">
                                    <span class="text-sm font-medium">Long</span>
                                    <p class="text-xs text-gray-500">~500 mots</p>
                                </div>
                                <div class="absolute inset-0 border-2 border-purple-500 rounded-lg opacity-0 peer-checked:opacity-100"></div>
                            </label>
                        </div>
                    </div>

                    <!-- Generate Button -->
                    <div>
                        <button type="submit" id="generate-btn" class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-purple-500 to-indigo-600 hover:from-purple-600 hover:to-indigo-700 text-white rounded-lg font-medium focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition-all">
                            <i data-lucide="sparkles" class="w-5 h-5"></i>
                            <span id="generate-btn-text">Générer avec l'IA</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Generated Content Section (Initially Hidden) -->
        <div id="generated-section" class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-lg hidden">
            <form method="POST" action="{{ route('blog.ai.store') }}" class="p-8">
                @csrf
                
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <i data-lucide="file-text" class="w-5 h-5 text-green-600"></i>
                    Contenu généré
                </h2>

                <!-- Title -->
                <div class="mb-6">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        Titre de l'article *
                    </label>
                    <input type="text" 
                           id="title" 
                           name="title" 
                           required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                </div>

                <!-- Content -->
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-2">
                        <label for="content" class="block text-sm font-medium text-gray-700">
                            Contenu *
                        </label>
                        <div class="flex gap-2">
                            <button type="button" onclick="improveContent('shorten')" class="text-xs px-3 py-1 bg-gray-100 hover:bg-gray-200 rounded text-gray-700">
                                <i data-lucide="minimize-2" class="w-3 h-3 inline"></i> Raccourcir
                            </button>
                            <button type="button" onclick="improveContent('expand')" class="text-xs px-3 py-1 bg-gray-100 hover:bg-gray-200 rounded text-gray-700">
                                <i data-lucide="maximize-2" class="w-3 h-3 inline"></i> Développer
                            </button>
                        </div>
                    </div>
                    <textarea 
                        id="content" 
                        name="content" 
                        rows="15" 
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent font-mono text-sm"></textarea>
                </div>

                <!-- Event Selection -->
                <div class="mb-6">
                    <label for="related_event_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Événement associé (optionnel)
                    </label>
                    <select id="related_event_id" 
                            name="related_event_id" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                        <option value="">Aucun événement</option>
                        @foreach($events as $event)
                            <option value="{{ $event->id }}">{{ $event->title }} - {{ $event->date->format('d/m/Y') }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-4">
                    <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-colors">
                        <i data-lucide="check" class="w-5 h-5"></i>
                        Publier l'article
                    </button>
                    <button type="button" onclick="regenerate()" class="px-6 py-3 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg font-medium transition-colors">
                        <i data-lucide="refresh-cw" class="w-5 h-5 inline"></i>
                        Régénérer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('ai-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const btn = document.getElementById('generate-btn');
    const btnText = document.getElementById('generate-btn-text');
    const originalText = btnText.textContent;
    
    // Disable button and show loading
    btn.disabled = true;
    btnText.textContent = 'Génération en cours...';
    
    try {
        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());
        
        const response = await fetch('{{ route('blog.ai.generate') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Fill in the generated content
            document.getElementById('title').value = result.title;
            document.getElementById('content').value = result.content;
            
            // Show the generated section
            document.getElementById('generated-section').classList.remove('hidden');
            
            // Scroll to generated section
            document.getElementById('generated-section').scrollIntoView({ behavior: 'smooth' });
        } else {
            alert('Erreur: ' + result.message);
        }
    } catch (error) {
        alert('Une erreur est survenue lors de la génération.');
        console.error(error);
    } finally {
        btn.disabled = false;
        btnText.textContent = originalText;
    }
});

async function improveContent(instruction) {
    const content = document.getElementById('content').value;
    
    if (!content) {
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
        alert('Une erreur est survenue.');
        console.error(error);
    }
}

function regenerate() {
    document.getElementById('generated-section').classList.add('hidden');
    document.getElementById('ai-form').scrollIntoView({ behavior: 'smooth' });
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
