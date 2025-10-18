@extends('layouts.admin')

@section('title', 'Gestion Utilisateurs - Admin')
@section('page-title', 'Gestion des Utilisateurs')
@section('page-subtitle', 'Gérer les comptes, permissions et statuts des utilisateurs')

@section('content')
<div class="p-6 space-y-6">
    <!-- Stats rapides -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center">
                    <i data-lucide="users" class="w-6 h-6 text-white"></i>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-white">{{ $totalUsers }}</div>
                    <div class="text-sm text-gray-400">Total Utilisateurs</div>
                </div>
            </div>
        </div>
        
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 bg-green-600 rounded-xl flex items-center justify-center">
                    <i data-lucide="user-check" class="w-6 h-6 text-white"></i>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-white">{{ $activeUsers }}</div>
                    <div class="text-sm text-gray-400">Utilisateurs Actifs</div>
                </div>
            </div>
        </div>
        
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 bg-red-600 rounded-xl flex items-center justify-center">
                    <i data-lucide="shield" class="w-6 h-6 text-white"></i>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-white">{{ $adminUsers }}</div>
                    <div class="text-sm text-gray-400">Administrateurs</div>
                </div>
            </div>
        </div>
        
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 bg-yellow-600 rounded-xl flex items-center justify-center">
                    <i data-lucide="user-plus" class="w-6 h-6 text-white"></i>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-white">{{ $newUsersThisMonth }}</div>
                    <div class="text-sm text-gray-400">Nouveaux ce mois</div>
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
                       placeholder="Rechercher par nom ou email..."
                       class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>
            <select name="role" class="px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                <option value="">Tous les rôles</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Administrateurs</option>
                <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>Utilisateurs</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                <i data-lucide="search" class="w-4 h-4"></i>
            </button>
            @if(request('search') || request('role'))
            <a href="{{ route('admin.users') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i data-lucide="x" class="w-4 h-4"></i>
            </a>
            @endif
        </form>
    </div>

    <!-- Table des utilisateurs -->
    <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-700">
            <h3 class="text-lg font-semibold text-white">Liste des Utilisateurs</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Utilisateur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Rôle</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Inscription</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @foreach($users as $user)
                    <tr class="hover:bg-gray-750">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-sm font-bold text-white">{{ substr($user->name, 0, 1) }}</span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-white">{{ $user->name }}</div>
                                    <div class="text-sm text-gray-400">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->is_admin ? 'bg-red-900 text-red-200' : 'bg-blue-900 text-blue-200' }}">
                                @if($user->is_admin)
                                    <i data-lucide="shield" class="w-3 h-3 mr-1"></i>
                                    Admin
                                @else
                                    <i data-lucide="user" class="w-3 h-3 mr-1"></i>
                                    User
                                @endif
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-900 text-green-200">
                                <i data-lucide="check-circle" class="w-3 h-3 mr-1"></i>
                                Actif
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                            {{ $user->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center gap-2">
                                @if($user->id !== Auth::id())
                                <button onclick="toggleUserRole({{ $user->id }}, {{ $user->is_admin ? 'false' : 'true' }})" 
                                        class="text-yellow-400 hover:text-yellow-300 transition-colors" 
                                        title="{{ $user->is_admin ? 'Retirer admin' : 'Promouvoir admin' }}">
                                    <i data-lucide="{{ $user->is_admin ? 'user-minus' : 'user-plus' }}" class="w-4 h-4"></i>
                                </button>
                                
                                <button onclick="deleteUser({{ $user->id }})" 
                                        class="text-red-400 hover:text-red-300 transition-colors" 
                                        title="Supprimer utilisateur">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                                @else
                                <span class="text-gray-500 text-xs">(Vous)</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        @if($users->hasPages())
        <div class="px-6 py-4 border-t border-gray-700">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>

<script>
function toggleUserRole(userId, makeAdmin) {
    if (confirm(makeAdmin ? 'Promouvoir cet utilisateur en administrateur ?' : 'Retirer les privilèges administrateur ?')) {
        fetch(`/admin/users/${userId}/role`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ is_admin: makeAdmin })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur lors de la mise à jour');
            }
        });
    }
}

function deleteUser(userId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.')) {
        fetch(`/admin/users/${userId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur lors de la suppression');
            }
        });
    }
}
</script>
@endsection