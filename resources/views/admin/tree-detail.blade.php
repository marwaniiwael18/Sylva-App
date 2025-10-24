@extends('layouts.admin')

@section('title', 'Détails de l\'Arbre - Admin')
@section('page-title', 'Détails de l\'Arbre')
@section('page-subtitle', 'Informations complètes et gestion de l\'arbre')

@section('content')
<div class="p-6 space-y-6">
    <!-- Retour et Actions Header -->
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.trees.index') }}" class="flex items-center gap-2 text-gray-400 hover:text-white transition-colors">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
            <span>Retour à la liste</span>
        </a>
        
        <div class="flex gap-3">
            @if($tree->status === 'Not Yet')
            <button onclick="verifyTree({{ $tree->id }})" 
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center gap-2">
                <i data-lucide="check-circle" class="w-4 h-4"></i>
                Vérifier l'arbre
            </button>
            @endif
            
            <button onclick="deleteTree({{ $tree->id }})" 
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center gap-2">
                <i data-lucide="trash-2" class="w-4 h-4"></i>
                Supprimer
            </button>
        </div>
    </div>

    <!-- En-tête de l'arbre -->
    <div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
        <div class="flex items-start gap-6">
            <!-- Image principale -->
            @if($tree->image_urls && count($tree->image_urls) > 0)
            <img src="{{ $tree->image_urls[0] }}" 
                 alt="{{ $tree->species }}" 
                 class="w-32 h-32 rounded-xl object-cover">
            @else
            <div class="w-32 h-32 bg-green-600 rounded-xl flex items-center justify-center">
                <i data-lucide="tree-pine" class="w-16 h-16 text-white"></i>
            </div>
            @endif

            <!-- Informations principales -->
            <div class="flex-1">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h1 class="text-3xl font-bold text-white mb-2">{{ $tree->species }}</h1>
                        <p class="text-gray-400">ID: #{{ $tree->id }}</p>
                    </div>
                    
                    <div class="text-4xl">{{ $tree->type_icon }}</div>
                </div>

                <div class="flex flex-wrap gap-2">
                    <!-- Badge Status -->
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                        @if($tree->status === 'Planted') bg-green-900 text-green-200
                        @elseif($tree->status === 'Not Yet') bg-yellow-900 text-yellow-200
                        @elseif($tree->status === 'Sick') bg-orange-900 text-orange-200
                        @else bg-red-900 text-red-200
                        @endif">
                        {{ $tree->status }}
                    </span>

                    <!-- Badge Type -->
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-emerald-900 text-emerald-200">
                        {{ $tree->type }}
                    </span>

                    <!-- Badge Score de Santé -->
                    @if($tree->health_score)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                        @if($tree->health_score >= 75) bg-green-900 text-green-200
                        @elseif($tree->health_score >= 50) bg-blue-900 text-blue-200
                        @elseif($tree->health_score >= 25) bg-yellow-900 text-yellow-200
                        @else bg-red-900 text-red-200
                        @endif">
                        <i data-lucide="heart" class="w-3 h-3 mr-1"></i>
                        Santé: {{ $tree->health_score }}%
                    </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques rapides -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center">
                    <i data-lucide="calendar" class="w-6 h-6 text-white"></i>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-white">{{ $stats['days_since_planting'] }}</div>
                    <div class="text-sm text-gray-400">Jours depuis plantation</div>
                </div>
            </div>
        </div>

        <div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 bg-purple-600 rounded-xl flex items-center justify-center">
                    <i data-lucide="heart" class="w-6 h-6 text-white"></i>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-white">{{ $stats['care_count'] }}</div>
                    <div class="text-sm text-gray-400">Soins enregistrés</div>
                </div>
            </div>
        </div>

        <div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 bg-green-600 rounded-xl flex items-center justify-center">
                    <i data-lucide="clock" class="w-6 h-6 text-white"></i>
                </div>
                <div class="text-right">
                    <div class="text-xl font-bold text-white">
                        @if($stats['last_care_date'])
                            {{ \Carbon\Carbon::parse($stats['last_care_date'])->diffForHumans() }}
                        @else
                            Jamais
                        @endif
                    </div>
                    <div class="text-sm text-gray-400">Dernier soin</div>
                </div>
            </div>
        </div>

        <div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 bg-orange-600 rounded-xl flex items-center justify-center">
                    <i data-lucide="user" class="w-6 h-6 text-white"></i>
                </div>
                <div class="text-right">
                    <div class="text-lg font-bold text-white">{{ $tree->plantedBy->name ?? 'Inconnu' }}</div>
                    <div class="text-sm text-gray-400">Planté par</div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Colonne principale -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Informations détaillées -->
            <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-700">
                    <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                        <i data-lucide="info" class="w-5 h-5"></i>
                        Informations Détaillées
                    </h3>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1">Espèce</label>
                            <p class="text-lg text-white">{{ $tree->species }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1">Type</label>
                            <p class="text-lg text-white">{{ $tree->type_icon }} {{ $tree->type }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1">Statut</label>
                            <p class="text-lg text-white">{{ $tree->status }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1">Date de plantation</label>
                            <p class="text-lg text-white">{{ \Carbon\Carbon::parse($tree->planting_date)->format('d/m/Y') }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1">Planté par</label>
                            <p class="text-lg text-white">
                                <a href="{{ route('admin.users') }}?search={{ $tree->plantedBy->email ?? '' }}" 
                                   class="text-red-400 hover:text-red-300">
                                    {{ $tree->plantedBy->name ?? 'Inconnu' }}
                                </a>
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1">Date d'ajout</label>
                            <p class="text-lg text-white">{{ $tree->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    @if($tree->description)
                    <div class="mt-6 pt-6 border-t border-gray-700">
                        <label class="block text-sm font-medium text-gray-400 mb-2">Description</label>
                        <p class="text-white">{{ $tree->description }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Localisation -->
            <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-700">
                    <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                        <i data-lucide="map-pin" class="w-5 h-5"></i>
                        Localisation
                    </h3>
                </div>
                
                <div class="p-6 space-y-4">
                    @if($tree->address)
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">Adresse</label>
                        <p class="text-white">{{ $tree->address }}</p>
                    </div>
                    @endif

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1">Latitude</label>
                            <p class="text-white font-mono">{{ $tree->latitude }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1">Longitude</label>
                            <p class="text-white font-mono">{{ $tree->longitude }}</p>
                        </div>
                    </div>

                    <div class="pt-4">
                        <a href="https://maps.google.com/?q={{ $tree->latitude }},{{ $tree->longitude }}" 
                           target="_blank" 
                           class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            <i data-lucide="external-link" class="w-4 h-4 mr-2"></i>
                            Ouvrir dans Google Maps
                        </a>
                    </div>
                </div>
            </div>

            <!-- Historique des soins -->
            <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-700">
                    <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                        <i data-lucide="heart" class="w-5 h-5"></i>
                        Historique des Soins ({{ $tree->careRecords->count() }})
                    </h3>
                </div>
                
                <div class="p-6">
                    @forelse($tree->careRecords->sortByDesc('performed_at') as $care)
                    <div class="border border-gray-700 rounded-lg p-4 mb-4 last:mb-0">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-xl
                                    @if($care->activity_type === 'watering') bg-blue-900
                                    @elseif($care->activity_type === 'pruning') bg-purple-900
                                    @elseif($care->activity_type === 'fertilizing') bg-green-900
                                    @elseif($care->activity_type === 'disease_treatment') bg-red-900
                                    @elseif($care->activity_type === 'inspection') bg-yellow-900
                                    @else bg-gray-700
                                    @endif">
                                    @if($care->activity_type === 'watering') 💧
                                    @elseif($care->activity_type === 'pruning') ✂️
                                    @elseif($care->activity_type === 'fertilizing') 🌱
                                    @elseif($care->activity_type === 'disease_treatment') 💊
                                    @elseif($care->activity_type === 'inspection') 🔍
                                    @else 🛠️
                                    @endif
                                </div>
                                <div>
                                    <h4 class="font-semibold text-white">{{ ucfirst(str_replace('_', ' ', $care->activity_type)) }}</h4>
                                    <p class="text-sm text-gray-400">{{ \Carbon\Carbon::parse($care->performed_at)->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                            
                            @if($care->condition_after)
                            <span class="px-2 py-1 rounded-full text-xs font-medium
                                @if($care->condition_after === 'excellent') bg-green-900 text-green-200
                                @elseif($care->condition_after === 'good') bg-blue-900 text-blue-200
                                @elseif($care->condition_after === 'fair') bg-yellow-900 text-yellow-200
                                @else bg-red-900 text-red-200
                                @endif">
                                {{ ucfirst($care->condition_after) }}
                            </span>
                            @endif
                        </div>

                        @if($care->notes)
                        <p class="text-gray-300 text-sm mb-3">{{ $care->notes }}</p>
                        @endif

                        <div class="flex items-center justify-between text-sm text-gray-400">
                            <div class="flex items-center">
                                <i data-lucide="user" class="w-4 h-4 mr-1"></i>
                                <span>{{ $care->maintainer->name ?? 'Inconnu' }}</span>
                            </div>
                            <span>{{ \Carbon\Carbon::parse($care->performed_at)->diffForHumans() }}</span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-gray-400">
                        <i data-lucide="heart" class="w-12 h-12 mx-auto mb-3 text-gray-600"></i>
                        <p>Aucun soin enregistré pour cet arbre</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Galerie d'images -->
            @if($tree->image_urls && count($tree->image_urls) > 0)
            <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-700">
                    <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                        <i data-lucide="camera" class="w-5 h-5"></i>
                        Photos ({{ count($tree->image_urls) }})
                    </h3>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-2">
                        @foreach($tree->image_urls as $imageUrl)
                        <img src="{{ $imageUrl }}" 
                             alt="Tree image" 
                             class="w-full h-24 object-cover rounded-lg cursor-pointer hover:opacity-80"
                             onclick="window.open('{{ $imageUrl }}', '_blank')"
                             onkeydown="if(event.key === 'Enter' || event.key === ' ') { event.preventDefault(); window.open('{{ $imageUrl }}', '_blank'); }"
                             tabindex="0">
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Actions rapides -->
            <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-700">
                    <h3 class="text-lg font-semibold text-white">Actions Rapides</h3>
                </div>
                
                <div class="p-6 space-y-3">
                    @if($tree->status === 'Not Yet')
                    <button onclick="verifyTree({{ $tree->id }})" 
                            class="w-full flex items-center gap-3 p-3 bg-green-900 text-green-200 rounded-lg hover:bg-green-800 transition-colors">
                        <i data-lucide="check-circle" class="w-5 h-5"></i>
                        Vérifier l'arbre
                    </button>
                    @endif

                    <a href="mailto:{{ $tree->plantedBy->email ?? '' }}" 
                       class="w-full flex items-center gap-3 p-3 bg-blue-900 text-blue-200 rounded-lg hover:bg-blue-800 transition-colors">
                        <i data-lucide="mail" class="w-5 h-5"></i>
                        Contacter le planteur
                    </a>

                    <a href="https://maps.google.com/?q={{ $tree->latitude }},{{ $tree->longitude }}" 
                       target="_blank"
                       class="w-full flex items-center gap-3 p-3 bg-purple-900 text-purple-200 rounded-lg hover:bg-purple-800 transition-colors">
                        <i data-lucide="map" class="w-5 h-5"></i>
                        Voir sur la carte
                    </a>

                    <button onclick="deleteTree({{ $tree->id }})" 
                            class="w-full flex items-center gap-3 p-3 bg-red-900 text-red-200 rounded-lg hover:bg-red-800 transition-colors">
                        <i data-lucide="trash-2" class="w-5 h-5"></i>
                        Supprimer l'arbre
                    </button>
                </div>
            </div>

            <!-- État de santé -->
            @if($tree->health_score || $tree->latestCare)
            <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-700">
                    <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                        <i data-lucide="activity" class="w-5 h-5"></i>
                        État de Santé
                    </h3>
                </div>
                
                <div class="p-6 space-y-4">
                    @if($tree->health_score)
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm text-gray-400">Score de santé</span>
                            <span class="text-lg font-bold text-white">{{ $tree->health_score }}%</span>
                        </div>
                        <div class="w-full bg-gray-700 rounded-full h-2">
                            <div class="h-2 rounded-full 
                                @if($tree->health_score >= 75) bg-green-500
                                @elseif($tree->health_score >= 50) bg-blue-500
                                @elseif($tree->health_score >= 25) bg-yellow-500
                                @else bg-red-500
                                @endif" 
                                style="width: {{ $tree->health_score }}%">
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($tree->latestCare)
                    <div class="pt-4 border-t border-gray-700">
                        <p class="text-sm text-gray-400 mb-1">Dernière condition</p>
                        <p class="text-white font-medium">{{ ucfirst($tree->latestCare->condition_after ?? 'Non évaluée') }}</p>
                    </div>
                    @endif

                    @if($tree->needsCare())
                    <div class="p-3 bg-yellow-900 bg-opacity-30 border border-yellow-700 rounded-lg">
                        <div class="flex items-start gap-2">
                            <i data-lucide="alert-triangle" class="w-4 h-4 text-yellow-400 mt-0.5"></i>
                            <div>
                                <p class="text-sm font-medium text-yellow-300">Soin nécessaire</p>
                                <p class="text-xs text-yellow-400">Cet arbre n'a pas reçu de soin depuis plus de 7 jours</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
function verifyTree(treeId) {
    if (!confirm('Êtes-vous sûr de vouloir vérifier cet arbre ?')) return;
    
    fetch(`/admin/trees/${treeId}/verify`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.message || 'Erreur lors de la vérification');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erreur lors de la vérification');
    });
}

function deleteTree(treeId) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer cet arbre ? Cette action est irréversible.')) return;
    
    fetch(`/admin/trees/${treeId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = '{{ route("admin.trees.index") }}';
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
