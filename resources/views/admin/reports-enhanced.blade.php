@extends('layouts.admin')

@section('title', 'Gestion Rapports - Admin')
@section('page-title', 'Gestion des Rapports')
@section('page-subtitle', 'Validation et modération des rapports environnementaux')

@push('styles')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
.leaflet-container {
    z-index: 1;
}
.modal-content {
    z-index: 1000;
}
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.image-preview-item {
    position: relative;
    border-radius: 8px;
    overflow: hidden;
}
.image-preview-remove {
    position: absolute;
    top: 4px;
    right: 4px;
    background: rgba(0, 0, 0, 0.6);
    color: white;
    border: none;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background-color 0.2s;
}
.image-preview-remove:hover {
    background: rgba(0, 0, 0, 0.8);
}
</style>
@endpush

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

    <!-- Filtres and Add Button -->
    <div class="bg-gray-800 border border-gray-700 rounded-xl p-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-white font-semibold">Filtres</h3>
            <button onclick="openAddReportModal()" 
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors duration-200">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Ajouter Rapport</span>
            </button>
        </div>
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
                <option value="validated" {{ request('status') === 'validated' ? 'selected' : '' }}>Validés</option>
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

    <!-- Liste des rapports avec feed -->
    <div class="space-y-4">
        @forelse($reports as $report)
        <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
            <div class="p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <h3 class="text-lg font-semibold text-white">{{ $report->title }}</h3>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                @if($report->status === 'pending') bg-yellow-900 text-yellow-200
                                @elseif($report->status === 'validated') bg-green-900 text-green-200
                                @else bg-red-900 text-red-200
                                @endif">
                                @if($report->status === 'pending')
                                    <i data-lucide="clock" class="w-3 h-3 mr-1"></i>
                                    En attente
                                @elseif($report->status === 'validated')
                                    <i data-lucide="check-circle" class="w-3 h-3 mr-1"></i>
                                    Validé
                                @else
                                    <i data-lucide="x-circle" class="w-3 h-3 mr-1"></i>
                                    Rejeté
                                @endif
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $report->urgency === 'high' ? 'bg-red-900 text-red-200' : 
                                   ($report->urgency === 'medium' ? 'bg-yellow-900 text-yellow-200' : 'bg-blue-900 text-blue-200') }}">
                                {{ ucfirst($report->urgency) }} Priority
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
                            @if($report->address)
                            <div class="flex items-center gap-1">
                                <i data-lucide="map-pin" class="w-4 h-4"></i>
                                {{ Str::limit($report->address, 40) }}
                            </div>
                            @endif
                        </div>
                        
                        <p class="text-gray-300 mb-4">{{ $report->description }}</p>
                        
                        <div class="flex items-center gap-3 flex-wrap">
                            @if($report->status === 'pending')
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
                            @endif
                            <button onclick="editReportModal({{ $report->id }})" 
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                <i data-lucide="edit-2" class="w-4 h-4"></i>
                                Modifier
                            </button>
                            <button onclick="deleteReportConfirm({{ $report->id }})" 
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-red-700 text-white rounded-lg hover:bg-red-800 transition-colors">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                Supprimer
                            </button>
                        </div>
                    </div>
                    
                    @if($report->images && count($report->images) > 0)
                    <div class="ml-6">
                        <img src="{{ $report->image_urls[0] }}" 
                             alt="Image du rapport" 
                             class="w-32 h-32 object-cover rounded-lg border border-gray-600">
                        @if(count($report->images) > 1)
                        <div class="mt-2 text-xs text-gray-400 text-center">
                            +{{ count($report->images) - 1 }} more
                        </div>
                        @endif
                    </div>
                    @endif
                </div>

                <!-- Report Feed Component (Comments, Votes, Reactions) -->
                <div class="mt-6 pt-6 border-t border-gray-700">
                    <div class="bg-gray-750 rounded-lg p-4">
                        @include('components.report-feed-admin', ['reportId' => $report->id])
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-12 text-center">
            <i data-lucide="inbox" class="w-16 h-16 text-gray-600 mx-auto mb-4"></i>
            <h3 class="text-lg font-semibold text-white mb-2">Aucun rapport trouvé</h3>
            <p class="text-gray-400 mb-4">Essayez de modifier vos filtres ou ajoutez un nouveau rapport</p>
            <button onclick="openAddReportModal()" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg transition-colors">
                <i data-lucide="plus" class="w-4 h-4 inline mr-2"></i>
                Ajouter un Rapport
            </button>
        </div>
        @endforelse
    </div>

    @if($reports->hasPages())
    <div class="flex justify-center">
        {{ $reports->links() }}
    </div>
    @endif
</div>

<!-- Include the same modals from reports.blade.php -->
@include('partials.report-modals')

@push('scripts')
<!-- Leaflet JavaScript -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
function approveReport(reportId) {
    if (confirm('Approuver ce rapport ?')) {
        fetch(`/admin/reports/${reportId}/validate`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ status: 'validated' })
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

// Include all the modal functions from reports.blade.php
let currentEditingReportId = null;
let reports = @json($reports->items());
let addReportMap = null;
let editReportMap = null;
let currentMarker = null;
let editCurrentMarker = null;
let selectedImages = [];
let selectedEditImages = [];
let existingImages = [];
let imagesToDelete = [];

// Copy the functions from reports.blade.php
// ... (will include in partials/report-modals partial)
</script>
@endpush
@endsection
