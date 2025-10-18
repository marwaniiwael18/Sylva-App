@extends('layouts.dashboard')

@section('title', $event->title)

@section('page-content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50 to-indigo-50 p-6">
    <div class="max-w-7xl mx-auto">
        <!-- Navigation Header -->
        <div class="mb-6">
            <a href="{{ route('events.index') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-white/80 backdrop-blur-sm hover:bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-700 transition-all shadow-sm hover:shadow-md">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Retour aux événements
            </a>
        </div>

        @if(session('success'))
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 text-green-700 px-6 py-4 rounded-lg mb-6 shadow-sm flex items-center gap-3">
                <i data-lucide="check-circle" class="w-5 h-5 text-green-600"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-gradient-to-r from-red-50 to-pink-50 border-l-4 border-red-500 text-red-700 px-6 py-4 rounded-lg mb-6 shadow-sm flex items-center gap-3">
                <i data-lucide="alert-circle" class="w-5 h-5 text-red-600"></i>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Hero Section avec titre et badges -->
        <div class="bg-gradient-to-br from-white via-blue-50/30 to-indigo-50/30 backdrop-blur-sm rounded-2xl border border-white/50 shadow-xl p-6 mb-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <!-- Titre et info -->
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="px-3 py-1 text-xs font-semibold rounded-full shadow-md
                            @if($event->type === 'Tree Planting') bg-gradient-to-r from-green-400 to-emerald-500 text-white
                            @elseif($event->type === 'Maintenance') bg-gradient-to-r from-blue-400 to-cyan-500 text-white
                            @elseif($event->type === 'Awareness') bg-gradient-to-r from-yellow-400 to-orange-500 text-white
                            @else bg-gradient-to-r from-purple-400 to-pink-500 text-white
                            @endif">
                            {{ $event->type }}
                        </span>
                        
                        @if($event->is_past)
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-gradient-to-r from-gray-400 to-gray-500 text-white shadow-md">
                                <i data-lucide="clock" class="w-3 h-3 inline mr-1"></i>
                                Événement passé
                            </span>
                        @endif
                    </div>
                    
                    <h1 class="text-xl lg:text-2xl font-bold text-gray-900 mb-3 leading-tight">
                        {{ $event->title }}
                    </h1>
                    
                    <!-- Infos rapides -->
                    <div class="flex flex-wrap gap-4 text-gray-700">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <i data-lucide="calendar" class="w-4 h-4 text-blue-600"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-medium">Date</p>
                                <p class="text-xs font-bold">{{ $event->formatted_date }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                <i data-lucide="map-pin" class="w-4 h-4 text-red-600"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-medium">Lieu</p>
                                <p class="text-xs font-bold">{{ $event->location }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <i data-lucide="user" class="w-4 h-4 text-green-600"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-medium">Organisateur</p>
                                <p class="text-xs font-bold">{{ $event->organizer->name }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                <i data-lucide="users" class="w-4 h-4 text-purple-600"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-medium">Participants</p>
                                <p class="text-xs font-bold">{{ $event->participants_count }} inscrits</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                                <i data-lucide="calendar-plus" class="w-4 h-4 text-orange-600"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-medium">Créé le</p>
                                <p class="text-xs font-bold">{{ $event->created_at->format('d/m/Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                @if($canEdit)
                    <div class="flex flex-col gap-2">
                        <a href="{{ route('events.edit', $event) }}" 
                           class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-white border-2 border-blue-200 rounded-xl text-xs font-bold text-blue-700 hover:bg-blue-50 hover:border-blue-300 transition-all shadow-md hover:shadow-lg">
                            <i data-lucide="edit" class="w-4 h-4"></i>
                            Modifier
                        </a>
                        <form action="{{ route('events.destroy', $event) }}" 
                              method="POST" 
                              class="inline"
                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet événement ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-gradient-to-r from-red-500 to-pink-600 hover:from-red-600 hover:to-pink-700 text-white rounded-xl text-xs font-bold transition-all shadow-md hover:shadow-lg">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                Supprimer
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>

        <!-- Layout en 2 colonnes: Description + Chatbot -->
        <div class="grid gap-4 lg:grid-cols-2 mb-6">
            <!-- Colonne 1: Description -->
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl border border-white/50 shadow-lg p-5 hover:shadow-xl transition-shadow">
                <div class="flex items-center gap-2 mb-3 pb-3 border-b-2 border-gradient-to-r from-blue-200 to-indigo-200">
                    <div class="w-8 h-8 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-xl flex items-center justify-center shadow-md">
                        <i data-lucide="file-text" class="w-4 h-4 text-white"></i>
                    </div>
                    <h3 class="text-base font-bold text-gray-800">Description</h3>
                </div>
                <div class="text-gray-700 whitespace-pre-wrap leading-relaxed text-sm">{{ $event->description }}</div>
            </div>

            <!-- Colonne 2: Chatbot FAQ -->
            <div class="bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 rounded-lg shadow-md p-5 backdrop-blur-sm border border-white/20">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center shadow-lg">
                                <i data-lucide="message-circle" class="w-4 h-4 text-white"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-gray-800">Assistant Événement 🤖</h3>
                                <p class="text-xs text-gray-600">Posez vos questions sur cet événement</p>
                            </div>
                        </div>
                        <button id="toggle-chatbot" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-white/50 rounded-lg transition-all">
                            <i data-lucide="chevron-down" class="w-4 h-4"></i>
                        </button>
                    </div>

                    <div id="chatbot-content" class="space-y-3">
                        <!-- Suggestions de questions -->
                        <div class="bg-white/60 backdrop-blur-sm rounded-lg p-3 border border-indigo-100">
                            <p class="text-xs text-gray-600 mb-2 font-medium">💡 Questions suggérées :</p>
                            <div class="flex flex-wrap gap-2">
                                <!-- Logistique & Accès -->
                                <button onclick="askQuestion('Y a-t-il un parking disponible ?')" 
                                        class="text-xs px-3 py-1.5 bg-white hover:bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-full transition-colors">
                                    🅿️ Parking
                                </button>
                                <button onclick="askQuestion('Comment se rendre à l\'événement en transports en commun ?')" 
                                        class="text-xs px-3 py-1.5 bg-white hover:bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-full transition-colors">
                                    🚌 Transports
                                </button>
                                <button onclick="askQuestion('Quelle est l\'adresse exacte de l\'événement ?')" 
                                        class="text-xs px-3 py-1.5 bg-white hover:bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-full transition-colors">
                                    📍 Adresse
                                </button>
                                
                                <!-- Préparation -->
                                <button onclick="askQuestion('Quelle tenue est recommandée pour cet événement ?')" 
                                        class="text-xs px-3 py-1.5 bg-white hover:bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-full transition-colors">
                                    👕 Tenue
                                </button>
                                <button onclick="askQuestion('Dois-je apporter du matériel spécifique ?')" 
                                        class="text-xs px-3 py-1.5 bg-white hover:bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-full transition-colors">
                                    🎒 Matériel
                                </button>
                                <button onclick="askQuestion('Faut-il s\'inscrire à l\'avance ?')" 
                                        class="text-xs px-3 py-1.5 bg-white hover:bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-full transition-colors">
                                    📝 Inscription
                                </button>
                                
                                <!-- Restauration & Commodités -->
                                <button onclick="askQuestion('Est-ce que de la nourriture sera fournie ?')" 
                                        class="text-xs px-3 py-1.5 bg-white hover:bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-full transition-colors">
                                    🍔 Nourriture
                                </button>
                                <button onclick="askQuestion('Y a-t-il de l\'eau potable disponible ?')" 
                                        class="text-xs px-3 py-1.5 bg-white hover:bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-full transition-colors">
                                    💧 Eau
                                </button>
                                <button onclick="askQuestion('Des toilettes sont-elles disponibles sur place ?')" 
                                        class="text-xs px-3 py-1.5 bg-white hover:bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-full transition-colors">
                                    🚻 Toilettes
                                </button>
                                
                                <!-- Participants -->
                                <button onclick="askQuestion('Puis-je venir avec des enfants ?')" 
                                        class="text-xs px-3 py-1.5 bg-white hover:bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-full transition-colors">
                                    👶 Enfants
                                </button>
                                <button onclick="askQuestion('Les animaux de compagnie sont-ils autorisés ?')" 
                                        class="text-xs px-3 py-1.5 bg-white hover:bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-full transition-colors">
                                    🐕 Animaux
                                </button>
                                <button onclick="askQuestion('Combien de personnes sont déjà inscrites ?')" 
                                        class="text-xs px-3 py-1.5 bg-white hover:bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-full transition-colors">
                                    👥 Participants
                                </button>
                                
                                <!-- Informations pratiques -->
                                <button onclick="askQuestion('Quelle est la durée de l\'événement ?')" 
                                        class="text-xs px-3 py-1.5 bg-white hover:bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-full transition-colors">
                                    ⏱️ Durée
                                </button>
                                <button onclick="askQuestion('Que se passe-t-il en cas de mauvais temps ?')" 
                                        class="text-xs px-3 py-1.5 bg-white hover:bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-full transition-colors">
                                    🌧️ Météo
                                </button>
                                <button onclick="askQuestion('Y a-t-il des frais de participation ?')" 
                                        class="text-xs px-3 py-1.5 bg-white hover:bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-full transition-colors">
                                    💰 Tarif
                                </button>
                                <button onclick="askQuestion('Comment puis-je contacter l\'organisateur ?')" 
                                        class="text-xs px-3 py-1.5 bg-white hover:bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-full transition-colors">
                                    📞 Contact
                                </button>
                                
                                <!-- Accessibilité -->
                                <button onclick="askQuestion('L\'événement est-il accessible aux personnes à mobilité réduite ?')" 
                                        class="text-xs px-3 py-1.5 bg-white hover:bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-full transition-colors">
                                    ♿ Accessibilité
                                </button>
                            </div>
                        </div>

                        <!-- Zone de conversation -->
                        <div id="chat-messages" class="bg-white/60 backdrop-blur-sm rounded-lg p-4 border border-indigo-100 min-h-[200px] max-h-[400px] overflow-y-auto space-y-3">
                            <!-- Les messages seront ajoutés dynamiquement -->
                            <div class="flex items-center justify-center h-full text-gray-400 text-sm">
                                <div class="text-center">
                                    <i data-lucide="message-circle" class="w-12 h-12 mx-auto mb-2 opacity-50"></i>
                                    <p>Sélectionnez une question ou posez la vôtre !</p>
                                </div>
                            </div>
                        </div>

                        <!-- Zone de saisie -->
                        <div class="flex gap-2">
                            <input type="text" 
                                   id="chat-question" 
                                   placeholder="Posez votre question ici..."
                                   class="flex-1 px-4 py-2 border border-indigo-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white/80 backdrop-blur-sm"
                                   onkeypress="if(event.key === 'Enter') sendQuestion()">
                            <button onclick="sendQuestion()" 
                                    id="send-btn"
                                    class="px-4 py-2 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white rounded-lg font-medium transition-all shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
                                <i data-lucide="send" class="w-4 h-4"></i>
                            </button>
                        </div>

                        <!-- Indicateur de chargement -->
                        <div id="chat-loading" class="hidden items-center gap-2 text-sm text-gray-600">
                            <div class="animate-spin rounded-full h-4 w-4 border-2 border-indigo-500 border-t-transparent"></div>
                            <span>L'assistant réfléchit...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions de participation (si applicable) -->
        @auth
            @if(!$event->is_past && Auth::id() !== $event->organized_by_user_id)
                <div class="bg-gradient-to-br from-white via-blue-50/30 to-indigo-50/30 backdrop-blur-sm rounded-2xl border border-white/50 shadow-lg p-5 mb-6">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-8 h-8 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-xl flex items-center justify-center shadow-md">
                            <i data-lucide="users" class="w-4 h-4 text-white"></i>
                        </div>
                        <h3 class="text-base font-bold text-gray-800">Votre Participation</h3>
                    </div>
                        
                    @if($isParticipant)
                        <div class="bg-green-50 border border-green-200 rounded-xl p-3 mb-3">
                            <p class="text-xs text-green-700 font-semibold flex items-center gap-2">
                                <i data-lucide="check-circle" class="w-4 h-4"></i>
                                Vous participez à cet événement
                            </p>
                        </div>
                        <form action="{{ route('events.leave', $event) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-full bg-gradient-to-r from-red-500 to-pink-600 hover:from-red-600 hover:to-pink-700 text-white font-semibold py-2 px-4 rounded-xl transition-all shadow-md hover:shadow-lg text-sm">
                                <i data-lucide="user-minus" class="w-3 h-3 inline mr-1"></i>
                                Se désinscrire
                            </button>
                        </form>
                    @else
                        <form action="{{ route('events.join', $event) }}" method="POST">
                            @csrf
                            <button type="submit" 
                                    class="w-full bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-semibold py-2 px-4 rounded-xl transition-all shadow-md hover:shadow-lg text-sm">
                                <i data-lucide="user-plus" class="w-3 h-3 inline mr-1"></i>
                                Participer à l'événement
                            </button>
                        </form>
                    @endif
                </div>
            @endif
        @endauth

        <!-- Liste des participants -->
        @if($event->participants->count() > 0)
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl border border-white/50 shadow-lg p-5 mb-6 hover:shadow-xl transition-shadow">
                <div class="flex items-center gap-2 mb-4 pb-3 border-b-2 border-gradient-to-r from-green-200 to-emerald-200">
                    <div class="w-8 h-8 bg-gradient-to-br from-green-400 to-emerald-500 rounded-xl flex items-center justify-center shadow-md">
                        <i data-lucide="user-check" class="w-4 h-4 text-white"></i>
                    </div>
                    <h3 class="text-base font-bold text-gray-800">Participants inscrits ({{ $event->participants->count() }})</h3>
                </div>
                
                <div class="grid sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 max-h-[400px] overflow-y-auto pr-2">
                    @foreach($event->participants as $participant)
                        <div class="flex items-center space-x-3 p-3 rounded-xl bg-gradient-to-r from-blue-50 to-indigo-50 hover:from-blue-100 hover:to-indigo-100 transition-all border border-blue-100 hover:border-blue-200 hover:shadow-md">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white text-sm font-bold shadow-md">
                                {{ strtoupper(substr($participant->name, 0, 1)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 truncate">
                                    {{ $participant->name }}
                                </p>
                                <p class="text-xs text-gray-500 flex items-center gap-1">
                                    <i data-lucide="calendar" class="w-3 h-3"></i>
                                    {{ is_string($participant->pivot->registered_at) ? \Carbon\Carbon::parse($participant->pivot->registered_at)->format('d/m/Y') : $participant->pivot->registered_at->format('d/m/Y') }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Générateur de Posts Réseaux Sociaux -->
        @if($canEdit)
            <div class="bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 rounded-2xl shadow-xl p-5 backdrop-blur-sm border border-white/50 hover:shadow-2xl transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 via-purple-600 to-pink-600 rounded-2xl flex items-center justify-center shadow-lg">
                            <i data-lucide="share-2" class="w-5 h-5 text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900">📱 Générateur Social Media</h3>
                            <p class="text-xs text-gray-600 mt-1">Créez des posts optimisés pour chaque plateforme</p>
                        </div>
                    </div>
                    <button id="toggle-social" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-white/50 rounded-lg transition-all">
                        <i data-lucide="chevron-down" class="w-4 h-4"></i>
                    </button>
                </div>

                <div id="social-content" class="space-y-4">
                    <!-- Sélection des plateformes -->
                    <div class="bg-white/80 backdrop-blur-sm rounded-xl p-5 border border-indigo-100 shadow-sm">
                        <p class="text-sm text-gray-700 mb-4 font-bold flex items-center gap-2">
                            <i data-lucide="check-square" class="w-4 h-4 text-indigo-600"></i>
                            Sélectionnez vos plateformes :
                        </p>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <label class="group relative">
                                <input type="checkbox" name="platforms" value="facebook" checked class="peer hidden">
                                <div class="flex items-center gap-2 px-4 py-3 bg-white hover:bg-blue-50 border-2 border-blue-200 peer-checked:border-blue-500 peer-checked:bg-blue-50 rounded-xl cursor-pointer transition-all shadow-sm hover:shadow-md">
                                    <i data-lucide="facebook" class="w-5 h-5 text-blue-600"></i>
                                    <span class="text-sm font-bold text-gray-700">Facebook</span>
                                    <i data-lucide="check" class="w-4 h-4 text-blue-600 ml-auto hidden peer-checked:block"></i>
                                </div>
                            </label>
                            <label class="group relative">
                                <input type="checkbox" name="platforms" value="twitter" checked class="peer hidden">
                                <div class="flex items-center gap-2 px-4 py-3 bg-white hover:bg-sky-50 border-2 border-sky-200 peer-checked:border-sky-500 peer-checked:bg-sky-50 rounded-xl cursor-pointer transition-all shadow-sm hover:shadow-md">
                                    <i data-lucide="twitter" class="w-5 h-5 text-sky-600"></i>
                                    <span class="text-sm font-bold text-gray-700">Twitter/X</span>
                                    <i data-lucide="check" class="w-4 h-4 text-sky-600 ml-auto hidden peer-checked:block"></i>
                                </div>
                            </label>
                            <label class="group relative">
                                <input type="checkbox" name="platforms" value="instagram" checked class="peer hidden">
                                <div class="flex items-center gap-2 px-4 py-3 bg-white hover:bg-pink-50 border-2 border-pink-200 peer-checked:border-pink-500 peer-checked:bg-pink-50 rounded-xl cursor-pointer transition-all shadow-sm hover:shadow-md">
                                    <i data-lucide="instagram" class="w-5 h-5 text-pink-600"></i>
                                    <span class="text-sm font-bold text-gray-700">Instagram</span>
                                    <i data-lucide="check" class="w-4 h-4 text-pink-600 ml-auto hidden peer-checked:block"></i>
                                </div>
                            </label>
                            <label class="group relative">
                                <input type="checkbox" name="platforms" value="linkedin" checked class="peer hidden">
                                <div class="flex items-center gap-2 px-4 py-3 bg-white hover:bg-blue-50 border-2 border-blue-300 peer-checked:border-blue-600 peer-checked:bg-blue-50 rounded-xl cursor-pointer transition-all shadow-sm hover:shadow-md">
                                    <i data-lucide="linkedin" class="w-5 h-5 text-blue-700"></i>
                                    <span class="text-sm font-bold text-gray-700">LinkedIn</span>
                                    <i data-lucide="check" class="w-4 h-4 text-blue-700 ml-auto hidden peer-checked:block"></i>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Bouton de génération -->
                    <div class="flex justify-center">
                        <button 
                            onclick="generateSocialPosts()" 
                            id="generate-social-btn"
                            class="bg-gradient-to-r from-indigo-500 via-purple-600 to-pink-600 hover:from-indigo-600 hover:via-purple-700 hover:to-pink-700 text-white font-bold py-3 px-6 rounded-xl transition-all shadow-lg hover:shadow-xl flex items-center justify-center gap-2 text-sm">
                            <i data-lucide="sparkles" class="w-5 h-5"></i>
                            Générer les posts IA
                            <i data-lucide="arrow-right" class="w-5 h-5"></i>
                        </button>
                    </div>

                    <!-- Indicateur de chargement -->
                    <div id="social-loading" class="hidden items-center justify-center gap-3 text-sm text-gray-700 py-6 bg-white/60 rounded-xl">
                        <div class="animate-spin rounded-full h-6 w-6 border-3 border-indigo-500 border-t-transparent"></div>
                        <span class="font-semibold">L'IA génère vos posts personnalisés...</span>
                    </div>

                    <!-- Zone d'affichage des posts générés -->
                    <div id="social-posts-container" class="hidden space-y-4"></div>
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    // Historique de conversation pour maintenir le contexte
    let conversationHistory = [];

    // Toggle chatbot visibility
    document.getElementById('toggle-chatbot').addEventListener('click', function() {
        const content = document.getElementById('chatbot-content');
        const icon = this.querySelector('i');
        
        if (content.classList.contains('hidden')) {
            content.classList.remove('hidden');
            icon.setAttribute('data-lucide', 'chevron-down');
        } else {
            content.classList.add('hidden');
            icon.setAttribute('data-lucide', 'chevron-right');
        }
        
        // Re-initialize lucide icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });

    // Fonction pour ajouter un message à la conversation
    function addMessage(text, isUser = false) {
        const messagesContainer = document.getElementById('chat-messages');
        
        // Supprimer le placeholder s'il existe (premier message)
        const placeholder = messagesContainer.querySelector('.text-gray-400');
        if (placeholder && placeholder.closest('.flex.items-center.justify-center')) {
            placeholder.closest('.flex.items-center.justify-center').remove();
        }
        
        const messageDiv = document.createElement('div');
        messageDiv.className = `flex items-start gap-2 text-sm ${isUser ? 'flex-row-reverse' : ''}`;
        
        const avatar = document.createElement('div');
        avatar.className = `w-6 h-6 rounded-full flex items-center justify-center flex-shrink-0 ${
            isUser 
                ? 'bg-gradient-to-br from-emerald-500 to-teal-600' 
                : 'bg-gradient-to-br from-indigo-500 to-purple-600'
        }`;
        
        const icon = document.createElement('i');
        icon.setAttribute('data-lucide', isUser ? 'user' : 'sparkles');
        icon.className = 'w-3 h-3 text-white';
        avatar.appendChild(icon);
        
        const bubble = document.createElement('p');
        bubble.className = `rounded-lg px-3 py-2 shadow-sm max-w-[80%] ${
            isUser 
                ? 'bg-gradient-to-r from-emerald-500 to-teal-600 text-white ml-2' 
                : 'bg-white/80 text-gray-700 mr-2'
        }`;
        bubble.innerHTML = text;
        
        messageDiv.appendChild(avatar);
        messageDiv.appendChild(bubble);
        messagesContainer.appendChild(messageDiv);
        
        // Scroll to bottom
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
        
        // Re-initialize lucide icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }

    // Fonction pour envoyer une question
    async function sendQuestion() {
        const input = document.getElementById('chat-question');
        const question = input.value.trim();
        
        if (!question) return;
        
        // Ajouter la question de l'utilisateur
        addMessage(question, true);
        input.value = '';
        
        // Afficher le loader
        const loading = document.getElementById('chat-loading');
        const sendBtn = document.getElementById('send-btn');
        loading.classList.remove('hidden');
        loading.classList.add('flex');
        sendBtn.disabled = true;
        
        try {
            const response = await fetch('/api/ai/events/ask', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                credentials: 'same-origin', // Important pour les cookies de session Sanctum
                body: JSON.stringify({
                    event_id: {{ $event->id }},
                    question: question,
                    conversation_history: conversationHistory
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Ajouter la réponse de l'IA
                addMessage(data.answer, false);
                
                // Ajouter à l'historique
                conversationHistory.push({
                    question: question,
                    answer: data.answer
                });
                
                // Limiter l'historique à 10 échanges
                if (conversationHistory.length > 10) {
                    conversationHistory.shift();
                }
            } else {
                addMessage('❌ Désolé, je n\'ai pas pu traiter votre question. Veuillez réessayer.', false);
            }
        } catch (error) {
            console.error('Erreur:', error);
            addMessage('❌ Une erreur est survenue. Veuillez réessayer plus tard.', false);
        } finally {
            loading.classList.add('hidden');
            loading.classList.remove('flex');
            sendBtn.disabled = false;
        }
    }

    // Fonction pour poser une question suggérée
    function askQuestion(question) {
        document.getElementById('chat-question').value = question;
        sendQuestion();
    }

    // Toggle social media generator
    const toggleSocialBtn = document.getElementById('toggle-social');
    if (toggleSocialBtn) {
        toggleSocialBtn.addEventListener('click', function() {
            const content = document.getElementById('social-content');
            const icon = this.querySelector('i');
            
            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                icon.setAttribute('data-lucide', 'chevron-down');
            } else {
                content.classList.add('hidden');
                icon.setAttribute('data-lucide', 'chevron-right');
            }
            
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    }

    // Fonction pour générer les posts réseaux sociaux
    async function generateSocialPosts() {
        const selectedPlatforms = Array.from(document.querySelectorAll('input[name="platforms"]:checked'))
            .map(input => input.value);
        
        if (selectedPlatforms.length === 0) {
            alert('Veuillez sélectionner au moins une plateforme');
            return;
        }

        const loadingDiv = document.getElementById('social-loading');
        const generateBtn = document.getElementById('generate-social-btn');
        const postsContainer = document.getElementById('social-posts-container');
        
        // Afficher le loader
        loadingDiv.classList.remove('hidden');
        loadingDiv.classList.add('flex');
        generateBtn.disabled = true;
        postsContainer.classList.add('hidden');
        postsContainer.innerHTML = '';

        try {
            const response = await fetch('/api/ai/events/social-posts', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    event_id: {{ $event->id }},
                    platforms: selectedPlatforms
                })
            });

            const data = await response.json();

            if (data.success && data.posts) {
                // Afficher les posts générés
                displaySocialPosts(data.posts);
                postsContainer.classList.remove('hidden');
            } else {
                alert('Erreur lors de la génération des posts. Veuillez réessayer.');
            }
        } catch (error) {
            console.error('Erreur:', error);
            alert('Une erreur est survenue. Veuillez réessayer plus tard.');
        } finally {
            loadingDiv.classList.add('hidden');
            loadingDiv.classList.remove('flex');
            generateBtn.disabled = false;
        }
    }

    // Fonction pour afficher les posts générés
    function displaySocialPosts(posts) {
        const container = document.getElementById('social-posts-container');
        container.innerHTML = '';

        const platformIcons = {
            facebook: { icon: 'facebook', color: 'blue', bg: 'bg-blue-50', border: 'border-blue-200' },
            twitter: { icon: 'twitter', color: 'sky', bg: 'bg-sky-50', border: 'border-sky-200' },
            instagram: { icon: 'instagram', color: 'pink', bg: 'bg-pink-50', border: 'border-pink-200' },
            linkedin: { icon: 'linkedin', color: 'blue', bg: 'bg-blue-50', border: 'border-blue-300' }
        };

        posts.forEach(post => {
            const config = platformIcons[post.platform] || platformIcons.facebook;
            
            const postDiv = document.createElement('div');
            postDiv.className = `${config.bg} ${config.border} border-2 rounded-xl p-5`;
            
            let hashtagsHtml = '';
            if (post.hashtags && post.hashtags.length > 0) {
                hashtagsHtml = `
                    <div class="mt-3 flex flex-wrap gap-2">
                        ${post.hashtags.map(tag => `<span class="text-xs bg-white px-2 py-1 rounded-full text-${config.color}-600 font-medium">${tag}</span>`).join('')}
                    </div>
                `;
            }

            let suggestedImageHtml = '';
            if (post.suggested_image) {
                suggestedImageHtml = `
                    <div class="mt-3 p-3 bg-white/80 rounded-lg border border-${config.color}-100">
                        <p class="text-xs font-semibold text-gray-600 mb-1">💡 Visuel suggéré :</p>
                        <p class="text-xs text-gray-600">${post.suggested_image}</p>
                    </div>
                `;
            }
            
            postDiv.innerHTML = `
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <i data-lucide="${config.icon}" class="w-5 h-5 text-${config.color}-600"></i>
                        <span class="font-bold text-${config.color}-700 capitalize">${post.platform}</span>
                    </div>
                    <button onclick="copyToClipboard('${post.platform}')" class="p-2 hover:bg-white rounded-lg transition-colors" title="Copier">
                        <i data-lucide="copy" class="w-4 h-4 text-gray-600"></i>
                    </button>
                </div>
                <div class="bg-white/80 rounded-lg p-4 mb-2">
                    <p class="text-sm text-gray-700 whitespace-pre-wrap" id="post-${post.platform}">${post.content}</p>
                </div>
                ${hashtagsHtml}
                ${suggestedImageHtml}
                <div class="mt-3 flex gap-2">
                    <button onclick="copyToClipboard('${post.platform}')" class="flex-1 bg-white hover:bg-gray-50 text-gray-700 font-medium py-2 px-4 rounded-lg transition-all flex items-center justify-center gap-2 text-sm">
                        <i data-lucide="copy" class="w-4 h-4"></i>
                        Copier
                    </button>
                    ${post.platform !== 'instagram' ? `
                    <button onclick="sharePost('${post.platform}')" class="flex-1 bg-gradient-to-r from-${config.color}-400 to-${config.color}-500 hover:from-${config.color}-500 hover:to-${config.color}-600 text-white font-medium py-2 px-4 rounded-lg transition-all flex items-center justify-center gap-2 text-sm">
                        <i data-lucide="share-2" class="w-4 h-4"></i>
                        Partager
                    </button>
                    ` : ''}
                </div>
            `;
            
            container.appendChild(postDiv);
        });

        // Re-initialize lucide icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }

    // Fonction pour copier le texte dans le presse-papier
    function copyToClipboard(platform) {
        const textElement = document.getElementById(`post-${platform}`);
        const text = textElement.textContent;
        
        navigator.clipboard.writeText(text).then(() => {
            // Afficher un feedback
            const originalText = textElement.innerHTML;
            textElement.innerHTML = '✅ Copié !';
            setTimeout(() => {
                textElement.innerHTML = originalText;
            }, 2000);
        }).catch(err => {
            console.error('Erreur lors de la copie:', err);
            alert('Impossible de copier le texte');
        });
    }

    // Fonction pour partager sur la plateforme
    function sharePost(platform) {
        const textElement = document.getElementById(`post-${platform}`);
        const text = encodeURIComponent(textElement.textContent);
        
        const shareUrls = {
            facebook: `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(window.location.href)}&quote=${text}`,
            twitter: `https://twitter.com/intent/tweet?text=${text}`,
            linkedin: `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(window.location.href)}`
        };

        if (shareUrls[platform]) {
            window.open(shareUrls[platform], '_blank', 'width=600,height=400');
        }
    }
</script>
@endpush

@endsection