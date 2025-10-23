@extends('layouts.admin')

@section('title', 'Gestion Rapports - Admin')
@section('page-title', 'Gestion des Rapports')
@section('page-subtitle', 'Validation et modération des rapports environnementaux')

@section('content')
<div class="p-6 space-y-6">
    <!-- Stats rapides -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 bg-yellow-600 rounded-xl flex items-center justify-center">
                    <i data-lucide="clock" class="w-6 h-6 text-white"></i>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-white">{{ $pendingReports }}</div>
                    <div class="text-sm text-gray-400">En attente</div>
                </div>
            </div>
        </div>
        
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 bg-green-600 rounded-xl flex items-center justify-center">
                    <i data-lucide="check-circle" class="w-6 h-6 text-white"></i>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-white">{{ $approvedReports }}</div>
                    <div class="text-sm text-gray-400">Approuvés</div>
                </div>
            </div>
        </div>
        
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 bg-red-600 rounded-xl flex items-center justify-center">
                    <i data-lucide="x-circle" class="w-6 h-6 text-white"></i>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-white">{{ $rejectedReports }}</div>
                    <div class="text-sm text-gray-400">Rejetés</div>
                </div>
            </div>
        </div>
        
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center">
                    <i data-lucide="flag" class="w-6 h-6 text-white"></i>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-white">{{ $totalReports }}</div>
                    <div class="text-sm text-gray-400">Total</div>
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
                       placeholder="Rechercher par titre ou auteur..."
                       class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>
            <select name="status" class="px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-red-500">
                <option value="">Tous les statuts</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>En attente</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approuvés</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejetés</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                <i data-lucide="search" class="w-4 h-4"></i>
            </button>
            @if(request('search') || request('status'))
            <a href="{{ route('admin.reports') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i data-lucide="x" class="w-4 h-4"></i>
            </a>
            @endif
        </form>
    </div>

    <!-- Liste des rapports -->
    <div class="space-y-4">
        @foreach($reports as $report)
        <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
            <div class="p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <h3 class="text-lg font-semibold text-white">{{ $report->title }}</h3>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                @if($report->status === 'pending') bg-yellow-900 text-yellow-200
                                @elseif($report->status === 'approved') bg-green-900 text-green-200
                                @else bg-red-900 text-red-200
                                @endif">
                                @if($report->status === 'pending')
                                    <i data-lucide="clock" class="w-3 h-3 mr-1"></i>
                                    En attente
                                @elseif($report->status === 'approved')
                                    <i data-lucide="check-circle" class="w-3 h-3 mr-1"></i>
                                    Approuvé
                                @else
                                    <i data-lucide="x-circle" class="w-3 h-3 mr-1"></i>
                                    Rejeté
                                @endif
                            </span>
                        </div>
                        
                        <div class="flex items-center gap-4 text-sm text-gray-400 mb-3">
                            <div class="flex items-center gap-1">
                                <i data-lucide="user" class="w-4 h-4"></i>
                                {{ $report->user->name }}
                            </div>
                            <div class="flex items-center gap-1">
                                <i data-lucide="calendar" class="w-4 h-4"></i>
                                {{ $report->created_at->format('d/m/Y à H:i') }}
                            </div>
                            <div class="flex items-center gap-1">
                                <i data-lucide="map-pin" class="w-4 h-4"></i>
                                {{ $report->location }}
                            </div>
                        </div>
                        
                        <p class="text-gray-300 mb-4">{{ Str::limit($report->description, 200) }}</p>
                        
                        @if($report->status === 'pending')
                        <div class="flex items-center gap-3">
                            <button onclick="approveReport({{ $report->id }})" 
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                <i data-lucide="check" class="w-4 h-4"></i>
                                Approuver
                            </button>
                            <button onclick="rejectReport({{ $report->id }})" 
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                <i data-lucide="x" class="w-4 h-4"></i>
                                Rejeter
                            </button>
                        </div>
                        @endif
                    </div>
                    
                    @if($report->image)
                    <div class="ml-6">
                        <img src="{{ asset('storage/' . $report->image) }}" 
                             alt="Image du rapport" 
                             class="w-32 h-32 object-cover rounded-lg border border-gray-600">
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @if($reports->hasPages())
    <div class="flex justify-center">
        {{ $reports->links() }}
    </div>
    @endif
</div>

<script>
function approveReport(reportId) {
    if (confirm('Approuver ce rapport ?')) {
        fetch(`/admin/reports/${reportId}/validate`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ status: 'approved' })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur lors de l\'approbation');
            }
        });
    }
}

function rejectReport(reportId) {
    const reason = prompt('Raison du rejet (optionnel) :');
    if (reason !== null) {
        fetch(`/admin/reports/${reportId}/reject`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ 
                status: 'rejected',
                reason: reason 
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur lors du rejet');
            }
        });
    }
}
</script>
@endsection