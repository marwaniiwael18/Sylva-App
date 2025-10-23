@extends('layouts.admin')

@section('title', 'Gestion Rapports - Admin')
@section('page-title', 'Gestion des Rapports')
@section('page-subtitle', 'Validation et modération des rapports environnementaux')

@push('styles')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
.leaflet-container { z-index: 1; }
.modal-content { z-index: 1000; }
.image-preview-item { position: relative; border-radius: 8px; overflow: hidden; }
.image-preview-remove {
    position: absolute; top: 4px; right: 4px; background: rgba(0, 0, 0, 0.6); color: white;
    border: none; border-radius: 50%; width: 24px; height: 24px; display: flex;
    align-items: center; justify-content: center; cursor: pointer; transition: background-color 0.2s;
}
.image-preview-remove:hover { background: rgba(0, 0, 0, 0.8); }
.line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.line-clamp-3 { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }

/* Modal text colors - ensure text is visible */
#addReportModal input,
#addReportModal textarea,
#addReportModal select,
#editReportModal input,
#editReportModal textarea,
#editReportModal select {
    color: #1f2937 !important;
    background-color: white !important;
}
#addReportModal label,
#editReportModal label {
    color: #374151 !important;
}
#addReportModal .text-gray-500,
#addReportModal .text-gray-600,
#addReportModal .text-gray-700,
#editReportModal .text-gray-500,
#editReportModal .text-gray-600,
#editReportModal .text-gray-700 {
    color: #4b5563 !important;
}
#addReportModal h3,
#editReportModal h3 {
    color: #111827 !important;
}

/* Fix comment textarea in report feed - make text visible */
.bg-gray-900 textarea,
.bg-gray-900 input[type="text"] {
    color: #1f2937 !important;
    background-color: white !important;
}

/* Ensure all text in dark backgrounds is visible */
.bg-gray-900 .text-gray-600,
.bg-gray-900 .text-gray-500,
.bg-gray-900 .placeholder-gray-400::placeholder {
    color: #9ca3af !important;
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
        <form method="GET" class="flex items-center gap-4 flex-wrap">
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

    <!-- Liste des rapports -->
    <div class="space-y-4">
        @forelse($reports as $report)
        <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
            <div class="p-6">
                <div class="flex items-start justify-between mb-4 flex-wrap gap-4">
                    <div class="flex-1 min-w-[300px]">
                        <div class="flex items-center gap-3 mb-2 flex-wrap">
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
                        
                        <div class="flex items-center gap-4 text-sm text-gray-400 mb-3 flex-wrap">
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
                        
                        @if($report->status === 'pending')
                        <div class="flex items-center gap-3 flex-wrap">
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
                        @else
                        <div class="flex items-center gap-3 flex-wrap">
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
                        @endif
                    </div>
                    
                    @if($report->images && count($report->images) > 0)
                    <div class="flex-shrink-0">
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

                <!-- Report Feed Component -->
                <div class="mt-6 pt-6 border-t border-gray-700">
                    <div class="bg-gray-900 rounded-lg p-4">
                        <h4 class="text-white font-semibold mb-4 flex items-center gap-2">
                            <i data-lucide="message-square" class="w-4 h-4"></i>
                            Activité et Commentaires
                        </h4>
                        @include('components.report-feed', ['reportId' => $report->id])
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-12 text-center">
            <i data-lucide="inbox" class="w-16 h-16 text-gray-600 mx-auto mb-4"></i>
            <h3 class="text-lg font-semibold text-white mb-2">Aucun rapport trouvé</h3>
            <p class="text-gray-400 mb-4">Essayez de modifier vos filtres ou ajoutez un nouveau rapport</p>
            <button onclick="openAddReportModal()" class="inline-block bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg transition-colors">
                <i data-lucide="plus" class="w-4 h-4 inline mr-2"></i> Ajouter un Rapport
            </button>
        </div>
        @endforelse
    </div>

    @if($reports->hasPages())
    <div class="flex justify-center">
        {{ $reports->links() }}
    </div>
    @endif

    <!-- Add Report Modal -->
    <div id="addReportModal" class="fixed inset-0 z-50 hidden overflow-y-auto" style="background-color: rgba(0, 0, 0, 0.5);">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="addReportForm">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                <i data-lucide="plus-circle" class="w-5 h-5 inline mr-2 text-green-600"></i>
                                Add New Report
                            </h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
                                    <input type="text" id="addTitle" name="title" required
                                           placeholder="Enter report title"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                                    <textarea id="addDescription" name="description" required rows="3"
                                            placeholder="Describe the environmental issue or suggestion"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"></textarea>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Type *</label>
                                        <select id="addType" name="type" required
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                            <option value="">Select type</option>
                                            <option value="tree_planting">🌳 Tree Planting</option>
                                            <option value="maintenance">🔧 Maintenance</option>
                                            <option value="pollution">⚠️ Pollution</option>
                                            <option value="green_space_suggestion">🌱 Green Space Suggestion</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Urgency *</label>
                                        <select id="addUrgency" name="urgency" required
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                            <option value="">Select urgency</option>
                                            <option value="low">🟢 Low</option>
                                            <option value="medium">🟡 Medium</option>
                                            <option value="high">🔴 High</option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Location * 
                                        <span class="text-xs text-gray-500">(Click on the map to select location)</span>
                                    </label>
                                    
                                    <div class="border border-gray-300 rounded-lg overflow-hidden">
                                        <div id="addReportMap" class="h-64 bg-gray-100 relative">
                                            <div class="absolute inset-0 flex items-center justify-center">
                                                <div class="text-center">
                                                    <i data-lucide="map-pin" class="w-8 h-8 mx-auto text-gray-400 mb-2"></i>
                                                    <p class="text-sm text-gray-500">Loading map...</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" id="addLatitude" name="latitude" required>
                                    <input type="hidden" id="addLongitude" name="longitude" required>
                                    <div class="mt-2 text-xs text-gray-600">
                                        <span id="selectedCoordinates">No location selected</span>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                                    <input type="text" id="addAddress" name="address"
                                           placeholder="Optional: Enter full address"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Images 
                                        <span class="text-xs text-gray-500">(Optional, max 5 images, 2MB each)</span>
                                    </label>
                                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-green-400 transition-colors">
                                        <input type="file" id="addImages" name="images[]" multiple accept="image/*" 
                                               class="hidden" onchange="handleImageUpload(this)">
                                        <div id="imageUploadArea">
                                            <i data-lucide="camera" class="w-12 h-12 mx-auto text-gray-400 mb-4"></i>
                                            <p class="text-sm text-gray-600 mb-2">Click to upload images or drag and drop</p>
                                            <p class="text-xs text-gray-500">PNG, JPG, WebP up to 5MB each</p>
                                            <button type="button" onclick="document.getElementById('addImages').click()" 
                                                    class="mt-3 px-4 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors">
                                                <i data-lucide="plus" class="w-4 h-4 inline mr-1"></i>
                                                Select Images
                                            </button>
                                        </div>
                                        <div id="imagePreview" class="hidden mt-4">
                                            <div class="grid grid-cols-2 gap-4" id="previewContainer">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between">
                                    <button type="button" onclick="getCurrentLocation()" 
                                            class="text-sm bg-blue-100 hover:bg-blue-200 text-blue-700 px-3 py-2 rounded-lg flex items-center space-x-1 transition-colors">
                                        <i data-lucide="map-pin" class="w-4 h-4"></i>
                                        <span>Use Current Location</span>
                                    </button>
                                    <span class="text-xs text-gray-500">* Required fields</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                        <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                        Add Report
                    </button>
                    <button type="button" onclick="closeAddModal()"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

    <!-- Edit Report Modal -->
    <div id="editReportModal" class="fixed inset-0 z-50 hidden overflow-y-auto" style="background-color: rgba(0, 0, 0, 0.5);">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="editReportForm">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                    <i data-lucide="edit-2" class="w-5 h-5 inline mr-2 text-blue-600"></i>
                                    Edit Report
                                </h3>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
                                        <input type="text" id="editTitle" name="title" required
                                               placeholder="Enter report title"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                                        <textarea id="editDescription" name="description" required rows="3"
                                                placeholder="Describe the environmental issue or suggestion"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Type *</label>
                                            <select id="editType" name="type" required
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                <option value="">Select type</option>
                                                <option value="tree_planting">🌳 Tree Planting</option>
                                                <option value="maintenance">🔧 Maintenance</option>
                                                <option value="pollution">⚠️ Pollution</option>
                                                <option value="green_space_suggestion">🌱 Green Space Suggestion</option>
                                            </select>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Urgency *</label>
                                            <select id="editUrgency" name="urgency" required
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                <option value="">Select urgency</option>
                                                <option value="low">🟢 Low</option>
                                                <option value="medium">🟡 Medium</option>
                                                <option value="high">🔴 High</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Location * 
                                            <span class="text-xs text-gray-500">(Click on the map to select location)</span>
                                        </label>
                                        
                                        <div class="border border-gray-300 rounded-lg overflow-hidden">
                                            <div id="editReportMap" class="h-64 bg-gray-100 relative">
                                                <div class="absolute inset-0 flex items-center justify-center">
                                                    <div class="text-center">
                                                        <i data-lucide="map-pin" class="w-8 h-8 mx-auto text-gray-400 mb-2"></i>
                                                        <p class="text-sm text-gray-500">Loading map...</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" id="editLatitude" name="latitude" required>
                                        <input type="hidden" id="editLongitude" name="longitude" required>
                                        <div class="mt-2 text-xs text-gray-600">
                                            <span id="editSelectedCoordinates">No location selected</span>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                                        <input type="text" id="editAddress" name="address"
                                               placeholder="Optional: Enter full address"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Images 
                                            <span class="text-xs text-gray-500">(Optional, max 5 images, 2MB each)</span>
                                        </label>
                                        
                                        <!-- Existing Images Display -->
                                        <div id="editExistingImages" class="hidden mb-4">
                                            <h4 class="text-sm font-medium text-gray-700 mb-2">Current Images:</h4>
                                            <div id="editExistingImagesContainer" class="grid grid-cols-2 gap-4 mb-3">
                                                <!-- Existing images will be populated here -->
                                            </div>
                                        </div>
                                        
                                        <!-- New Images Upload -->
                                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors">
                                            <input type="file" id="editImages" name="images[]" multiple accept="image/*" 
                                                   class="hidden" onchange="handleEditImageUpload(this)">
                                            <div id="editImageUploadArea">
                                                <i data-lucide="camera" class="w-12 h-12 mx-auto text-gray-400 mb-4"></i>
                                                <p class="text-sm text-gray-600 mb-2">Click to upload new images or drag and drop</p>
                                                <p class="text-xs text-gray-500">PNG, JPG, WebP up to 5MB each</p>
                                                <button type="button" onclick="document.getElementById('editImages').click()" 
                                                        class="mt-3 px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors">
                                                    <i data-lucide="plus" class="w-4 h-4 inline mr-1"></i>
                                                    Select Images
                                                </button>
                                            </div>
                                            <div id="editImagePreview" class="hidden mt-4">
                                                <div class="grid grid-cols-2 gap-4" id="editPreviewContainer">
                                                    <!-- New image previews will be added here -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between">
                                        <button type="button" onclick="getEditCurrentLocation()" 
                                                class="text-sm bg-blue-100 hover:bg-blue-200 text-blue-700 px-3 py-2 rounded-lg flex items-center space-x-1 transition-colors">
                                            <i data-lucide="map-pin" class="w-4 h-4"></i>
                                            <span>Use Current Location</span>
                                        </button>
                                        <span class="text-xs text-gray-500">* Required fields</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                            <i data-lucide="check" class="w-4 h-4 mr-2"></i>
                            Update Report
                        </button>
                        <button type="button" onclick="closeEditModal()"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<!-- Leaflet JavaScript -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
let reports = @json($reports->items());
let addReportMap = null;
let editReportMap = null;
let currentMarker = null;
let editCurrentMarker = null;
let selectedImages = [];
let selectedEditImages = [];
let existingImages = [];
let imagesToDelete = [];
let currentEditingReportId = null;

function approveReport(reportId) {
    if (confirm('Approuver ce rapport ?')) {
        fetch(`/admin/reports/${reportId}/validate`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ action: 'validate', status: 'validated' })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur lors de l\'approbation');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erreur lors de l\'approbation');
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
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erreur lors du rejet');
        });
    }
}

function deleteReportConfirm(reportId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce rapport ? Cette action est irréversible.')) {
        deleteReport(reportId);
    }
}

async function deleteReport(reportId) {
    try {
        const response = await fetch(`/admin/reports/${reportId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        });

        const result = await response.json();

        if (response.ok && result.success) {
            alert('Rapport supprimé avec succès!');
            location.reload();
        } else {
            throw new Error(result.message || 'Failed to delete report');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error deleting report: ' + error.message);
    }
}

// Add Report Modal Functions
function openAddReportModal() {
    console.log('Opening add report modal...');
    const modal = document.getElementById('addReportModal');
    if (!modal) {
        console.error('Modal element not found!');
        alert('Error: Modal not found. Please refresh the page.');
        return;
    }
    modal.classList.remove('hidden');
    console.log('Modal should be visible now');
    
    // Initialize map after modal is shown
    setTimeout(() => {
        initializeAddReportMap();
    }, 100);
}

function closeAddModal() {
    document.getElementById('addReportModal').classList.add('hidden');
    document.getElementById('addReportForm').reset();
    if (addReportMap) {
        addReportMap.remove();
        addReportMap = null;
    }
    currentMarker = null;
    selectedImages = [];
    document.getElementById('addLatitude').value = '';
    document.getElementById('addLongitude').value = '';
    document.getElementById('selectedCoordinates').textContent = 'No location selected';
    document.getElementById('imagePreview').classList.add('hidden');
    document.getElementById('previewContainer').innerHTML = '';
}

function initializeAddReportMap() {
    if (addReportMap) {
        addReportMap.remove();
    }
    
    const defaultLat = 40.7128;
    const defaultLng = -74.0060;
    
    addReportMap = L.map('addReportMap').setView([defaultLat, defaultLng], 12);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap contributors'
    }).addTo(addReportMap);
    
    addReportMap.on('click', function(e) {
        const lat = e.latlng.lat;
        const lng = e.latlng.lng;
        
        if (currentMarker) {
            addReportMap.removeLayer(currentMarker);
        }
        
        currentMarker = L.marker([lat, lng]).addTo(addReportMap);
        
        document.getElementById('addLatitude').value = lat.toFixed(6);
        document.getElementById('addLongitude').value = lng.toFixed(6);
        
        document.getElementById('selectedCoordinates').textContent = 
            `Selected: ${lat.toFixed(6)}, ${lng.toFixed(6)}`;
        
        getAddressFromCoordinates(lat, lng);
    });
    
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            const userLat = position.coords.latitude;
            const userLng = position.coords.longitude;
            addReportMap.setView([userLat, userLng], 15);
        }, function() {});
    }
}

async function getAddressFromCoordinates(lat, lng) {
    try {
        const response = await fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`);
        const data = await response.json();
        if (data.display_name) {
            document.getElementById('addAddress').value = data.display_name;
        }
    } catch (error) {
        console.log('Could not get address:', error);
    }
}

function getCurrentLocation() {
    if (!navigator.geolocation) {
        alert('Geolocation is not supported by this browser.');
        return;
    }

    const btn = event.target.closest('button');
    const originalHtml = btn.innerHTML;
    btn.innerHTML = '<i data-lucide="loader" class="w-4 h-4 animate-spin"></i><span class="ml-1">Getting location...</span>';
    btn.disabled = true;

    navigator.geolocation.getCurrentPosition(
        function(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            
            if (addReportMap) {
                addReportMap.setView([lat, lng], 16);
                
                if (currentMarker) {
                    addReportMap.removeLayer(currentMarker);
                }
                
                currentMarker = L.marker([lat, lng]).addTo(addReportMap);
                
                document.getElementById('addLatitude').value = lat.toFixed(6);
                document.getElementById('addLongitude').value = lng.toFixed(6);
                
                document.getElementById('selectedCoordinates').textContent = 
                    `Selected: ${lat.toFixed(6)}, ${lng.toFixed(6)}`;
                
                getAddressFromCoordinates(lat, lng);
            }
                
            btn.innerHTML = originalHtml;
            btn.disabled = false;
            lucide.createIcons();
            
            alert('Current location set successfully!');
        },
        function(error) {
            btn.innerHTML = originalHtml;
            btn.disabled = false;
            lucide.createIcons();
            
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    alert("User denied the request for Geolocation.");
                    break;
                case error.POSITION_UNAVAILABLE:
                    alert("Location information is unavailable.");
                    break;
                case error.TIMEOUT:
                    alert("The request to get user location timed out.");
                    break;
                default:
                    alert("An unknown error occurred while getting location.");
                    break;
            }
        }
    );
}

function handleImageUpload(input) {
    const files = Array.from(input.files);
    const maxFiles = 5;
    const maxSize = 2 * 1024 * 1024;
    
    if (selectedImages.length + files.length > maxFiles) {
        alert(`Maximum ${maxFiles} images allowed`);
        return;
    }
    
    files.forEach(file => {
        if (file.size > maxSize) {
            alert(`${file.name} is too large. Maximum size is 2MB.`);
            return;
        }
        
        if (!file.type.startsWith('image/')) {
            alert(`${file.name} is not an image file.`);
            return;
        }
        
        selectedImages.push(file);
        
        const reader = new FileReader();
        reader.onload = function(e) {
            addImagePreview(file.name, e.target.result, selectedImages.length - 1);
        };
        reader.readAsDataURL(file);
    });
    
    if (selectedImages.length > 0) {
        document.getElementById('imagePreview').classList.remove('hidden');
    }
}

function addImagePreview(fileName, src, index) {
    const container = document.getElementById('previewContainer');
    const previewDiv = document.createElement('div');
    previewDiv.className = 'image-preview-item';
    previewDiv.innerHTML = `
        <img src="${src}" alt="${fileName}" class="w-full h-24 object-cover rounded-lg">
        <button type="button" class="image-preview-remove" onclick="removeImage(${index})">
            <i data-lucide="x" class="w-3 h-3"></i>
        </button>
        <div class="absolute bottom-1 left-1 bg-black/60 text-white px-1 py-0.5 rounded text-xs truncate max-w-full">
            ${fileName}
        </div>
    `;
    
    container.appendChild(previewDiv);
    lucide.createIcons();
}

function removeImage(index) {
    selectedImages.splice(index, 1);
    
    const container = document.getElementById('previewContainer');
    container.innerHTML = '';
    
    selectedImages.forEach((file, i) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            addImagePreview(file.name, e.target.result, i);
        };
        reader.readAsDataURL(file);
    });
    
    if (selectedImages.length === 0) {
        document.getElementById('imagePreview').classList.add('hidden');
        document.getElementById('addImages').value = '';
    }
}

// Handle add form submission
document.addEventListener('DOMContentLoaded', function() {
    const addForm = document.getElementById('addReportForm');
    if (!addForm) {
        console.error('Add report form not found!');
        return;
    }
    
    addForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        console.log('Form submitted');
        
        const formData = new FormData(this);
        
        const reportData = {
            title: formData.get('title'),
            description: formData.get('description'),
            type: formData.get('type'),
            urgency: formData.get('urgency'),
            latitude: parseFloat(formData.get('latitude')),
            longitude: parseFloat(formData.get('longitude')),
            address: formData.get('address') || null
        };

        if (!reportData.title || !reportData.description || !reportData.type || !reportData.urgency) {
            alert('Please fill in all required fields');
            return;
        }
        
        if (isNaN(reportData.latitude) || isNaN(reportData.longitude)) {
            alert('Please select a location on the map');
            return;
        }
        
        try {
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalHtml = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i data-lucide="loader" class="w-4 h-4 mr-2 animate-spin"></i>Adding...';
            submitBtn.disabled = true;

            const submitFormData = new FormData();
            Object.keys(reportData).forEach(key => {
                if (reportData[key] !== null) {
                    submitFormData.append(key, reportData[key]);
                }
            });
            
            selectedImages.forEach((file, index) => {
                submitFormData.append('images[]', file);
            });

            const response = await fetch('/api/reports-public', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: submitFormData
            });

            const result = await response.json();

            if (response.ok && result.success) {
                alert('Report added successfully!');
                closeAddModal();
                location.reload();
            } else {
                throw new Error(result.message || 'Failed to add report');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error adding report: ' + error.message);
        } finally {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.innerHTML = '<i data-lucide="plus" class="w-4 h-4 mr-2"></i>Add Report';
                submitBtn.disabled = false;
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            }
        }
    });
});

function editReportModal(reportId) {
    const report = reports.find(r => r.id == reportId);
    if (!report) {
        alert('Report not found');
        return;
    }
    
    currentEditingReportId = reportId;
    
    // Reset edit form variables
    selectedEditImages = [];
    existingImages = [];
    imagesToDelete = [];
    
    // Populate the form
    document.getElementById('editTitle').value = report.title;
    document.getElementById('editDescription').value = report.description;
    document.getElementById('editType').value = report.type;
    document.getElementById('editUrgency').value = report.urgency;
    document.getElementById('editAddress').value = report.address || '';
    document.getElementById('editLatitude').value = report.latitude || '';
    document.getElementById('editLongitude').value = report.longitude || '';
    
    // Update coordinate display
    if (report.latitude && report.longitude) {
        document.getElementById('editSelectedCoordinates').textContent = 
            `Selected: ${parseFloat(report.latitude).toFixed(6)}, ${parseFloat(report.longitude).toFixed(6)}`;
    } else {
        document.getElementById('editSelectedCoordinates').textContent = 'No location selected';
    }
    
    // Display existing images
    if (report.image_urls && report.image_urls.length > 0) {
        displayEditExistingImages(report.image_urls);
    } else {
        document.getElementById('editExistingImages').classList.add('hidden');
    }
    
    // Clear new image previews
    document.getElementById('editImagePreview').classList.add('hidden');
    document.getElementById('editPreviewContainer').innerHTML = '';
    
    // Show modal
    document.getElementById('editReportModal').classList.remove('hidden');
    
    // Initialize map after modal is shown
    setTimeout(() => {
        initializeEditReportMap(report);
    }, 100);
}

function closeEditModal() {
    document.getElementById('editReportModal').classList.add('hidden');
    document.getElementById('editReportForm').reset();
    currentEditingReportId = null;
    selectedEditImages = [];
    existingImages = [];
    imagesToDelete = [];
    
    // Reset map
    if (editReportMap) {
        editReportMap.remove();
        editReportMap = null;
    }
    editCurrentMarker = null;
    
    // Clear previews
    document.getElementById('editExistingImages').classList.add('hidden');
    document.getElementById('editImagePreview').classList.add('hidden');
    document.getElementById('editSelectedCoordinates').textContent = 'No location selected';
}

function initializeEditReportMap(report) {
    if (editReportMap) {
        editReportMap.remove();
    }
    
    // Use report location or default
    const defaultLat = report.latitude || 40.7128;
    const defaultLng = report.longitude || -74.0060;
    const zoomLevel = report.latitude ? 16 : 12;
    
    // Initialize map
    editReportMap = L.map('editReportMap').setView([defaultLat, defaultLng], zoomLevel);
    
    // Add tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap contributors'
    }).addTo(editReportMap);
    
    // Add marker if report has coordinates
    if (report.latitude && report.longitude) {
        editCurrentMarker = L.marker([report.latitude, report.longitude]).addTo(editReportMap);
    }
    
    // Add click event to map
    editReportMap.on('click', function(e) {
        const lat = e.latlng.lat;
        const lng = e.latlng.lng;
        
        // Remove previous marker
        if (editCurrentMarker) {
            editReportMap.removeLayer(editCurrentMarker);
        }
        
        // Add new marker
        editCurrentMarker = L.marker([lat, lng]).addTo(editReportMap);
        
        // Update hidden inputs
        document.getElementById('editLatitude').value = lat.toFixed(6);
        document.getElementById('editLongitude').value = lng.toFixed(6);
        
        // Update coordinate display
        document.getElementById('editSelectedCoordinates').textContent = 
            `Selected: ${lat.toFixed(6)}, ${lng.toFixed(6)}`;
        
        // Try to get address using reverse geocoding
        getEditAddressFromCoordinates(lat, lng);
    });
}

async function getEditAddressFromCoordinates(lat, lng) {
    try {
        const response = await fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`);
        const data = await response.json();
        
        if (data.display_name) {
            document.getElementById('editAddress').value = data.display_name;
        }
    } catch (error) {
        console.log('Could not get address:', error);
    }
}

function getEditCurrentLocation() {
    if (!navigator.geolocation) {
        alert('Geolocation is not supported by this browser.');
        return;
    }

    const btn = event.target.closest('button');
    const originalHtml = btn.innerHTML;
    btn.innerHTML = '<i data-lucide="loader" class="w-4 h-4 animate-spin"></i><span class="ml-1">Getting location...</span>';
    btn.disabled = true;

    navigator.geolocation.getCurrentPosition(
        function(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            
            // Update map view and add marker
            if (editReportMap) {
                editReportMap.setView([lat, lng], 16);
                
                // Remove previous marker
                if (editCurrentMarker) {
                    editReportMap.removeLayer(editCurrentMarker);
                }
                
                // Add new marker
                editCurrentMarker = L.marker([lat, lng]).addTo(editReportMap);
                
                // Update hidden inputs
                document.getElementById('editLatitude').value = lat.toFixed(6);
                document.getElementById('editLongitude').value = lng.toFixed(6);
                
                // Update coordinate display
                document.getElementById('editSelectedCoordinates').textContent = 
                    `Selected: ${lat.toFixed(6)}, ${lng.toFixed(6)}`;
                
                // Try to get address
                getEditAddressFromCoordinates(lat, lng);
            }
                
            btn.innerHTML = originalHtml;
            btn.disabled = false;
            lucide.createIcons();
            
            alert('Current location set successfully!');
        },
        function(error) {
            btn.innerHTML = originalHtml;
            btn.disabled = false;
            lucide.createIcons();
            
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    alert("User denied the request for Geolocation.");
                    break;
                case error.POSITION_UNAVAILABLE:
                    alert("Location information is unavailable.");
                    break;
                case error.TIMEOUT:
                    alert("The request to get user location timed out.");
                    break;
                default:
                    alert("An unknown error occurred while getting location.");
                    break;
            }
        }
    );
}

function handleEditImageUpload(input) {
    const files = Array.from(input.files);
    const maxFiles = 5;
    const maxSize = 2 * 1024 * 1024; // 2MB
    
    // Check total file count (existing + selected + new)
    const totalImages = existingImages.length + selectedEditImages.length + files.length;
    if (totalImages > maxFiles) {
        alert(`Maximum ${maxFiles} images allowed total`);
        return;
    }
    
    // Process each file
    files.forEach(file => {
        // Check file size
        if (file.size > maxSize) {
            alert(`${file.name} is too large. Maximum size is 2MB.`);
            return;
        }
        
        // Check file type
        if (!file.type.startsWith('image/')) {
            alert(`${file.name} is not an image file.`);
            return;
        }
        
        selectedEditImages.push(file);
        
        // Create preview
        const reader = new FileReader();
        reader.onload = function(e) {
            addEditImagePreview(file.name, e.target.result, selectedEditImages.length - 1, 'new');
        };
        reader.readAsDataURL(file);
    });
    
    // Show preview area
    if (selectedEditImages.length > 0) {
        document.getElementById('editImagePreview').classList.remove('hidden');
    }
}

function addEditImagePreview(fileName, src, index, type) {
    const container = document.getElementById('editPreviewContainer');
    const previewDiv = document.createElement('div');
    previewDiv.className = 'image-preview-item relative';
    previewDiv.innerHTML = `
        <img src="${src}" alt="${fileName}" class="w-full h-24 object-cover rounded-lg">
        <button type="button" class="image-preview-remove absolute top-1 right-1 bg-red-500 hover:bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs" onclick="removeEditImage(${index}, '${type}')">
            <i data-lucide="x" class="w-3 h-3"></i>
        </button>
        <div class="absolute bottom-1 left-1 bg-black/60 text-white px-1 py-0.5 rounded text-xs truncate max-w-full">
            ${fileName}
        </div>
    `;
    
    container.appendChild(previewDiv);
    
    // Re-initialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}

function displayEditExistingImages(images) {
    if (!images || images.length === 0) {
        document.getElementById('editExistingImages').classList.add('hidden');
        return;
    }
    
    existingImages = [...images];
    const container = document.getElementById('editExistingImagesContainer');
    container.innerHTML = '';
    
    images.forEach((imageUrl, index) => {
        const previewDiv = document.createElement('div');
        previewDiv.className = 'image-preview-item relative';
        previewDiv.innerHTML = `
            <img src="${imageUrl}" alt="Existing image ${index + 1}" class="w-full h-24 object-cover rounded-lg">
            <button type="button" class="image-preview-remove absolute top-1 right-1 bg-red-500 hover:bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs" onclick="removeEditImage(${index}, 'existing')">
                <i data-lucide="x" class="w-3 h-3"></i>
            </button>
            <div class="absolute bottom-1 left-1 bg-green-600 text-white px-2 py-0.5 rounded text-xs">
                Current
            </div>
        `;
        
        container.appendChild(previewDiv);
    });
    
    document.getElementById('editExistingImages').classList.remove('hidden');
    
    // Re-initialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}

function removeEditImage(index, type) {
    if (type === 'existing') {
        // Mark for deletion
        imagesToDelete.push(existingImages[index]);
        existingImages.splice(index, 1);
        displayEditExistingImages(existingImages);
    } else if (type === 'new') {
        // Remove from new images
        selectedEditImages.splice(index, 1);
        
        // Rebuild new image previews
        const container = document.getElementById('editPreviewContainer');
        container.innerHTML = '';
        
        selectedEditImages.forEach((file, i) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                addEditImagePreview(file.name, e.target.result, i, 'new');
            };
            reader.readAsDataURL(file);
        });
        
        // Hide preview if no new images
        if (selectedEditImages.length === 0) {
            document.getElementById('editImagePreview').classList.add('hidden');
        }
    }
}

// Reinitialize Lucide icons after page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Page loaded, initializing Lucide icons...');
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
        console.log('Lucide icons initialized');
    } else {
        console.error('Lucide library not loaded!');
    }

    // Handle edit form submission
    const editForm = document.getElementById('editReportForm');
    if (editForm) {
        editForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            if (!currentEditingReportId) {
                alert('No report selected for editing');
                return;
            }
            
            // Create FormData for file upload
            const formData = new FormData();
            formData.append('_method', 'PUT'); // Method spoofing for Laravel
            formData.append('title', document.getElementById('editTitle').value);
            formData.append('description', document.getElementById('editDescription').value);
            formData.append('type', document.getElementById('editType').value);
            formData.append('urgency', document.getElementById('editUrgency').value);
            formData.append('address', document.getElementById('editAddress').value || '');
            formData.append('latitude', document.getElementById('editLatitude').value);
            formData.append('longitude', document.getElementById('editLongitude').value);
            
            // Add new images
            selectedEditImages.forEach((file, index) => {
                formData.append('images[]', file);
            });
            
            // Add information about images to delete
            if (imagesToDelete.length > 0) {
                formData.append('images_to_delete', JSON.stringify(imagesToDelete));
            }
            
            // Validation
            if (!formData.get('title') || !formData.get('description') || !formData.get('type') || !formData.get('urgency')) {
                alert('Please fill in all required fields');
                return;
            }
            
            if (!formData.get('latitude') || !formData.get('longitude')) {
                alert('Please select a location on the map');
                return;
            }
            
            try {
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalHtml = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i data-lucide="loader" class="w-4 h-4 mr-2 animate-spin"></i>Updating...';
                submitBtn.disabled = true;

                const response = await fetch(`/api/reports-public/${currentEditingReportId}`, {
                    method: 'POST', // Changed to POST with _method field
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    alert('Report updated successfully!');
                    closeEditModal();
                    location.reload();
                } else {
                    throw new Error(result.message || 'Failed to update report');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error updating report: ' + error.message);
            } finally {
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.innerHTML = '<i data-lucide="check" class="w-4 h-4 mr-2"></i>Update Report';
                    submitBtn.disabled = false;
                    if (typeof lucide !== 'undefined') {
                        lucide.createIcons();
                    }
                }
            }
        });
    }

    // Close modals when clicking outside
    const addModal = document.getElementById('addReportModal');
    if (addModal) {
        addModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeAddModal();
            }
        });
    }

    const editModal = document.getElementById('editReportModal');
    if (editModal) {
        editModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditModal();
            }
        });
    }
});
</script>
@endpush