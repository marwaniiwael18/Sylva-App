@extends('layouts.admin')

@section('title', 'Gestion Arbres - Admin')
@section('page-title', 'Gestion des Arbres')
@section('page-subtitle', 'Vérifier et gérer les arbres plantés')

@section('content')
<div class="p-6 space-y-6">
    <!-- Stats rapides -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 bg-green-600 rounded-xl flex items-center justify-center">
                    <i data-lucide="trees" class="w-6 h-6 text-white"></i>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-white">{{ number_format($totalTrees ?? 0) }}</div>
                    <div class="text-sm text-gray-400">Total Arbres</div>
                </div>
            </div>
        </div>
        
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center">
                    <i data-lucide="check-circle" class="w-6 h-6 text-white"></i>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-white">{{ number_format($verifiedTrees ?? 0) }}</div>
                    <div class="text-sm text-gray-400">Vérifiés</div>
                </div>
            </div>
        </div>
        
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 bg-yellow-600 rounded-xl flex items-center justify-center">
                    <i data-lucide="clock" class="w-6 h-6 text-white"></i>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-white">{{ number_format($pendingTrees ?? 0) }}</div>
                    <div class="text-sm text-gray-400">En Attente</div>
                </div>
            </div>
        </div>
        
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 bg-purple-600 rounded-xl flex items-center justify-center">
                    <i data-lucide="sprout" class="w-6 h-6 text-white"></i>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-white">{{ number_format($monthlyTrees ?? 0) }}</div>
                    <div class="text-sm text-gray-400">Ce Mois</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-gray-800 border border-gray-700 rounded-xl p-4">
        <form method="GET" class="flex items-center gap-4">
            <div class="flex-1">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Rechercher par espèce ou planteur..."
                       class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>
            <select name="status" class="px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                <option value="">Tous les statuts</option>
                <option value="verified" {{ request('status') === 'verified' ? 'selected' : '' }}>Vérifié</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>En attente</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejeté</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                <i data-lucide="search" class="w-4 h-4"></i>
            </button>
            @if(request('search') || request('status'))
            <a href="{{ route('admin.trees') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i data-lucide="x" class="w-4 h-4"></i>
            </a>
            @endif
        </form>
    </div>

    <!-- Liste des arbres -->
    <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-700">
            <h3 class="text-lg font-semibold text-white">Liste des Arbres</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Arbre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Planté par</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Localisation</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Date</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-300 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse($trees ?? [] as $tree)
                    <tr class="hover:bg-gray-750">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                @if($tree->image)
                                <img src="{{ asset('storage/' . $tree->image) }}" 
                                     alt="Tree" 
                                     class="w-10 h-10 rounded-lg object-cover mr-3">
                                @else
                                <div class="w-10 h-10 bg-green-600 rounded-lg flex items-center justify-center mr-3">
                                    <i data-lucide="tree-pine" class="w-5 h-5 text-white"></i>
                                </div>
                                @endif
                                <div>
                                    <div class="text-sm font-medium text-white">{{ $tree->species }}</div>
                                    <div class="text-sm text-gray-400">{{ number_format($tree->height ?? 0, 2) }}m</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-white">{{ $tree->plantedBy->name ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-300">{{ $tree->address ?? 'Non spécifié' }}</div>
                            @if($tree->latitude && $tree->longitude)
                            <div class="text-xs text-gray-500">{{ $tree->latitude }}, {{ $tree->longitude }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                @if($tree->status === 'Planted') bg-green-900 text-green-200
                                @elseif($tree->status === 'Not Yet') bg-yellow-900 text-yellow-200
                                @else bg-red-900 text-red-200
                                @endif">
                                @if($tree->status === 'Planted')
                                    <i data-lucide="check-circle" class="w-3 h-3 mr-1"></i>
                                    Planté
                                @elseif($tree->status === 'Not Yet')
                                    <i data-lucide="clock" class="w-3 h-3 mr-1"></i>
                                    En attente
                                @else
                                    <i data-lucide="x-circle" class="w-3 h-3 mr-1"></i>
                                    {{ $tree->status }}
                                @endif
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                            {{ \Carbon\Carbon::parse($tree->planting_date)->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            @if($tree->status === 'Not Yet')
                            <button onclick="verifyTree({{ $tree->id }})" 
                                    class="text-green-400 hover:text-green-300 mr-3">
                                <i data-lucide="check" class="w-4 h-4"></i>
                            </button>
                            @endif
                            <button onclick="deleteTree({{ $tree->id }})" 
                                    class="text-red-400 hover:text-red-300">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                            <i data-lucide="trees" class="w-12 h-12 mx-auto mb-3 text-gray-600"></i>
                            <p>Aucun arbre trouvé</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if(isset($trees) && $trees->hasPages())
        <div class="px-6 py-4 border-t border-gray-700">
            {{ $trees->links() }}
        </div>
        @endif
    </div>
</div>

<script>
function verifyTree(treeId) {
    if (!confirm('Êtes-vous sûr de vouloir vérifier cet arbre ?')) return;
    
    fetch(`/admin/trees/${treeId}/verify`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
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
    if (!confirm('Êtes-vous sûr de vouloir supprimer cet arbre ?')) return;
    
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
            window.location.reload();
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