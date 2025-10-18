@extends('layouts.admin')

@section('title', 'Gestion Forum - Admin')
@section('page-title', 'Gestion du Forum')
@section('page-subtitle', 'Modérer et gérer les publications du forum')

@section('content')
<div class="p-6 space-y-6">
    <!-- Stats rapides -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center">
                    <i data-lucide="message-square" class="w-6 h-6 text-white"></i>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-white">{{ number_format($totalPosts ?? 0) }}</div>
                    <div class="text-sm text-gray-400">Total Publications</div>
                </div>
            </div>
        </div>
        
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 bg-yellow-600 rounded-xl flex items-center justify-center">
                    <i data-lucide="alert-circle" class="w-6 h-6 text-white"></i>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-white">{{ number_format($reportedPosts ?? 0) }}</div>
                    <div class="text-sm text-gray-400">Signalés</div>
                </div>
            </div>
        </div>
        
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 bg-green-600 rounded-xl flex items-center justify-center">
                    <i data-lucide="users" class="w-6 h-6 text-white"></i>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-white">{{ number_format($activeUsers ?? 0) }}</div>
                    <div class="text-sm text-gray-400">Utilisateurs Actifs</div>
                </div>
            </div>
        </div>
        
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 bg-purple-600 rounded-xl flex items-center justify-center">
                    <i data-lucide="calendar" class="w-6 h-6 text-white"></i>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-white">{{ number_format($todayPosts ?? 0) }}</div>
                    <div class="text-sm text-gray-400">Aujourd'hui</div>
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
                       placeholder="Rechercher dans les publications..."
                       class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>
            <select name="status" class="px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                <option value="">Tous les statuts</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Actif</option>
                <option value="reported" {{ request('status') === 'reported' ? 'selected' : '' }}>Signalé</option>
                <option value="deleted" {{ request('status') === 'deleted' ? 'selected' : '' }}>Supprimé</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                <i data-lucide="search" class="w-4 h-4"></i>
            </button>
            @if(request('search') || request('status'))
            <a href="{{ route('admin.forum') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i data-lucide="x" class="w-4 h-4"></i>
            </a>
            @endif
        </form>
    </div>

    <!-- Liste des publications -->
    <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-700">
            <h3 class="text-lg font-semibold text-white">Publications du Forum</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Publication</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Auteur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Catégorie</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Date</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-300 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse($posts ?? [] as $post)
                    <tr class="hover:bg-gray-750">
                        <td class="px-6 py-4">
                            <div class="flex items-start">
                                @if($post->image)
                                <img src="{{ asset('storage/' . $post->image) }}" 
                                     alt="Post" 
                                     class="w-12 h-12 rounded-lg object-cover mr-3">
                                @else
                                <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center mr-3">
                                    <i data-lucide="message-square" class="w-6 h-6 text-white"></i>
                                </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-medium text-white truncate">{{ $post->title }}</div>
                                    <div class="text-sm text-gray-400 line-clamp-2">{{ Str::limit($post->content, 100) }}</div>
                                    <div class="mt-1 flex items-center text-xs text-gray-500">
                                        <i data-lucide="eye" class="w-3 h-3 mr-1"></i>
                                        {{ $post->views_count ?? 0 }}
                                        <span class="mx-2">•</span>
                                        <i data-lucide="message-circle" class="w-3 h-3 mr-1"></i>
                                        {{ $post->comments_count ?? 0 }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-white">{{ $post->author->name ?? 'N/A' }}</div>
                            <div class="text-sm text-gray-400">{{ $post->author->email ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-900 text-blue-200">
                                Général
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-900 text-green-200">
                                <i data-lucide="check-circle" class="w-3 h-3 mr-1"></i>
                                Actif
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                            {{ $post->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button onclick="viewPost({{ $post->id }})" 
                                    class="text-blue-400 hover:text-blue-300 mr-3">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </button>
                            <button onclick="deletePost({{ $post->id }})" 
                                    class="text-red-400 hover:text-red-300">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                            <i data-lucide="message-square" class="w-12 h-12 mx-auto mb-3 text-gray-600"></i>
                            <p>Aucune publication trouvée</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if(isset($posts) && $posts->hasPages())
        <div class="px-6 py-4 border-t border-gray-700">
            {{ $posts->links() }}
        </div>
        @endif
    </div>
</div>

<script>
function viewPost(postId) {
    // Redirection vers la page de détail du post
    window.open(`/forum/${postId}`, '_blank');
}

function deletePost(postId) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer cette publication ?')) return;
    
    fetch(`/admin/forum/${postId}`, {
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