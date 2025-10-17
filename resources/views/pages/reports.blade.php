@extends('layouts.dashboard')

@section('page-title', 'Reports')
@section('page-subtitle', 'Manage environmental reports and track their status')

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
.search-result-item {
    cursor: pointer;
    transition: background-color 0.2s;
}
.search-result-item:hover {
    background-color: #f3f4f6;
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

@section('page-content')
<div class="p-6 space-y-6">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Reports</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $statistics['total_reports'] }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                    <i data-lucide="flag" class="w-5 h-5 text-blue-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Pending</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $statistics['pending_reports'] }}</p>
                </div>
                <div class="w-10 h-10 bg-yellow-50 rounded-lg flex items-center justify-center">
                    <i data-lucide="clock" class="w-5 h-5 text-yellow-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Validated</p>
                    <p class="text-2xl font-bold text-green-600">{{ $statistics['validated_reports'] }}</p>
                </div>
                <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center">
                    <i data-lucide="check-circle" class="w-5 h-5 text-green-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">This Month</p>
                    <p class="text-2xl font-bold text-purple-600">{{ $statistics['this_month'] }}</p>
                </div>
                <div class="w-10 h-10 bg-purple-50 rounded-lg flex items-center justify-center">
                    <i data-lucide="calendar" class="w-5 h-5 text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Reports Cards -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">Recent Reports</h3>
                <button onclick="openAddReportModal()" 
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors duration-200">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>Add Report</span>
                </button>
            </div>
            
            <div class="p-6">
                @if($reports->count() > 0)
                <div id="reportsContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($reports as $report)
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden">
                        <!-- Image carousel if images exist -->
                        @if($report->images && count($report->images) > 0)
                        <div class="relative h-48 bg-gray-100">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent z-10"></div>
                            <img src="{{ $report->image_urls[0] }}" 
                                 alt="{{ $report->title }}" 
                                 class="w-full h-full object-cover">
                            @if(count($report->images) > 1)
                            <div class="absolute top-2 right-2 z-20 bg-black/60 text-white px-2 py-1 rounded-full text-xs">
                                <i data-lucide="image" class="w-3 h-3 inline mr-1"></i>
                                {{ count($report->images) }}
                            </div>
                            @endif
                        </div>
                        @else
                        <div class="h-32 bg-gradient-to-br from-green-100 to-blue-100 flex items-center justify-center">
                            <div class="text-center">
                                @php
                                    $icon = match($report->type) {
                                        'tree_planting' => 'tree-pine',
                                        'maintenance' => 'wrench',
                                        'pollution' => 'alert-triangle',
                                        'green_space_suggestion' => 'leaf',
                                        default => 'map-pin'
                                    };
                                @endphp
                                <i data-lucide="{{ $icon }}" class="w-8 h-8 text-green-600"></i>
                            </div>
                        </div>
                        @endif
                        
                        <div class="p-4">
                            <!-- Header with title and urgency -->
                            <div class="flex items-start justify-between mb-3">
                                <h4 class="text-lg font-semibold text-gray-900 line-clamp-2 flex-1 mr-2">
                                    {{ $report->title }}
                                </h4>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium flex-shrink-0
                                    {{ $report->urgency === 'high' ? 'bg-red-100 text-red-800' : 
                                       ($report->urgency === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                    {{ ucfirst($report->urgency) }}
                                </span>
                            </div>
                            
                            <!-- Description -->
                            <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                                {{ $report->description }}
                            </p>
                            
                            <!-- Meta information -->
                            <div class="space-y-2 mb-4">
                                <div class="flex items-center text-sm text-gray-500">
                                    <i data-lucide="tag" class="w-4 h-4 mr-2"></i>
                                    <span class="capitalize">{{ str_replace('_', ' ', $report->type) }}</span>
                                </div>
                                <div class="flex items-center text-sm text-gray-500">
                                    <i data-lucide="user" class="w-4 h-4 mr-2"></i>
                                    <span>{{ $report->user->name ?? 'Unknown' }}</span>
                                </div>
                                <div class="flex items-center text-sm text-gray-500">
                                    <i data-lucide="calendar" class="w-4 h-4 mr-2"></i>
                                    <span>{{ $report->created_at->format('M j, Y') }}</span>
                                </div>
                                @if($report->address)
                                <div class="flex items-center text-sm text-gray-500">
                                    <i data-lucide="map-pin" class="w-4 h-4 mr-2"></i>
                                    <span class="truncate">{{ Str::limit($report->address, 40) }}</span>
                                </div>
                                @endif
                            </div>
                            
                            <!-- Status badge -->
                            <div class="flex items-center justify-between mb-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $report->status === 'validated' ? 'bg-green-100 text-green-800' : 
                                       ($report->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                    <i data-lucide="{{ $report->status === 'validated' ? 'check-circle' : ($report->status === 'pending' ? 'clock' : 'circle') }}" 
                                       class="w-3 h-3 mr-1"></i>
                                    {{ ucfirst($report->status) }}
                                </span>
                            </div>
                            
                            <!-- Action buttons -->
                            <div class="flex space-x-2">
                                <a href="{{ route('map') }}" 
                                   class="flex-1 text-center bg-green-50 text-green-700 hover:bg-green-100 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                                    <i data-lucide="map-pin" class="w-4 h-4 inline mr-1"></i>
                                    View on Map
                                </a>
                                <button onclick="editReportModal({{ $report->id }})" 
                                        class="flex-1 bg-blue-50 text-blue-700 hover:bg-blue-100 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                                    <i data-lucide="edit-2" class="w-4 h-4 inline mr-1"></i>
                                    Edit
                                </button>
                                <button onclick="deleteReportConfirm({{ $report->id }})" 
                                        class="bg-red-50 text-red-700 hover:bg-red-100 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                                    <i data-lucide="trash-2" class="w-4 h-4 inline"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-12">
                    <div class="text-gray-500">
                        <i data-lucide="flag" class="w-16 h-16 mx-auto mb-4 text-gray-300"></i>
                        <p class="text-xl font-medium mb-2">No reports found</p>
                        <p class="text-sm mb-6">Be the first to report an environmental issue!</p>
                        <p class="text-sm text-gray-400">Click the "Add Report" button above to get started.</p>
                    </div>
                </div>
                @endif
            </div>
            
            @if($reports->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $reports->links() }}
            </div>
            @endif
        </div>

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
                                        <div class="flex items-center justify-between mb-2">
                                            <label class="block text-sm font-medium text-gray-700">Description *</label>
                                            <button type="button" onclick="generateDescription()" 
                                                    class="text-xs bg-purple-100 hover:bg-purple-200 text-purple-700 px-3 py-1 rounded-lg flex items-center space-x-1 transition-colors">
                                                <i data-lucide="sparkles" class="w-3 h-3"></i>
                                                <span>Generate with AI</span>
                                            </button>
                                        </div>
                                        <textarea id="addDescription" name="description" required rows="3"
                                                placeholder="Describe the environmental issue or suggestion (or use AI to generate)"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"></textarea>
                                        <div id="aiGenerationStatus" class="hidden mt-1 text-xs text-gray-500"></div>
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
                                        <!-- Hidden inputs for coordinates -->
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
                                                <p class="text-xs text-gray-500">PNG, JPG up to 2MB each</p>
                                                <button type="button" onclick="document.getElementById('addImages').click()" 
                                                        class="mt-3 px-4 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors">
                                                    <i data-lucide="plus" class="w-4 h-4 inline mr-1"></i>
                                                    Select Images
                                                </button>
                                            </div>
                                            <div id="imagePreview" class="hidden mt-4">
                                                <div class="grid grid-cols-2 gap-4" id="previewContainer">
                                                    <!-- Image previews will be added here -->
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
                                        <!-- Hidden inputs for coordinates -->
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
                                                <p class="text-xs text-gray-500">PNG, JPG up to 2MB each</p>
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

@push('scripts')
<!-- Leaflet JavaScript -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
let currentEditingReportId = null;
let reports = @json($reports->items());
let addReportMap = null;
let editReportMap = null;
let currentMarker = null;
let editCurrentMarker = null;
let searchTimeout = null;
let selectedImages = [];

// Gemini AI Configuration
const GEMINI_API_KEY = 'AIzaSyBwSyHbc1uN-yNIsgVl48Z8AwxWEeEeR1g';
const GEMINI_API_URL = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent';

// AI Description Generation Function
async function generateDescription() {
    const titleInput = document.getElementById('addTitle');
    const descriptionTextarea = document.getElementById('addDescription');
    const statusDiv = document.getElementById('aiGenerationStatus');
    const generateBtn = event.target.closest('button');
    
    const title = titleInput.value.trim();
    
    if (!title) {
        alert('Please enter a title first to generate description');
        titleInput.focus();
        return;
    }
    
    // Show loading state
    const originalHtml = generateBtn.innerHTML;
    generateBtn.innerHTML = '<i data-lucide="loader" class="w-3 h-3 animate-spin"></i><span>Generating...</span>';
    generateBtn.disabled = true;
    lucide.createIcons();
    
    statusDiv.textContent = 'AI is generating an attractive description...';
    statusDiv.className = 'mt-1 text-xs text-blue-600';
    statusDiv.classList.remove('hidden');
    
    try {
        const prompt = `Generate an attractive, concise, and promotional environmental report description for: "${title}". 
        
Requirements:
- Under 100 words
- Sound natural and engaging
- Focus on environmental impact and community benefit
- Use professional yet accessible language
- Highlight the importance and urgency if applicable
- Make it compelling for stakeholders to take action

Generate only the description text without any labels or extra formatting.`;

        const response = await fetch(`${GEMINI_API_URL}?key=${GEMINI_API_KEY}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                contents: [{
                    parts: [{
                        text: prompt
                    }]
                }],
                generationConfig: {
                    temperature: 0.7,
                    maxOutputTokens: 200,
                    topP: 0.8,
                    topK: 40
                }
            })
        });

        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.error?.message || 'Failed to generate description');
        }

        const data = await response.json();
        const generatedText = data.candidates[0]?.content?.parts[0]?.text;

        if (generatedText) {
            descriptionTextarea.value = generatedText.trim();
            statusDiv.textContent = '✨ Description generated successfully!';
            statusDiv.className = 'mt-1 text-xs text-green-600';
            
            // Add a nice animation to the textarea
            descriptionTextarea.style.backgroundColor = '#f0fdf4';
            setTimeout(() => {
                descriptionTextarea.style.backgroundColor = '';
                statusDiv.classList.add('hidden');
            }, 3000);
        } else {
            throw new Error('No description generated');
        }
    } catch (error) {
        console.error('Error generating description:', error);
        statusDiv.textContent = '❌ Failed to generate description. Please try again or write manually.';
        statusDiv.className = 'mt-1 text-xs text-red-600';
        setTimeout(() => {
            statusDiv.classList.add('hidden');
        }, 5000);
    } finally {
        generateBtn.innerHTML = originalHtml;
        generateBtn.disabled = false;
        lucide.createIcons();
    }
}

// Add Report Functions
function openAddReportModal() {
    document.getElementById('addReportModal').classList.remove('hidden');
    
    // Initialize map after modal is shown
    setTimeout(() => {
        initializeAddReportMap();
    }, 100);
}

function closeAddModal() {
    document.getElementById('addReportModal').classList.add('hidden');
    document.getElementById('addReportForm').reset();
    
    // Reset map and coordinates
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

async function searchLocation(query) {
    const resultsContainer = document.getElementById('searchResults');
    
    if (!resultsContainer) return;
    
    // Show loading state
    resultsContainer.innerHTML = '<div class="p-3 text-sm text-gray-500"><i data-lucide="loader" class="w-4 h-4 inline mr-2 animate-spin"></i>Searching...</div>';
    resultsContainer.classList.remove('hidden');
    
    try {
        // Use Nominatim API for geocoding with better parameters
        const response = await fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(query)}&format=json&limit=8&addressdetails=1&extratags=1&namedetails=1`, {
            headers: {
                'User-Agent': 'Sylva-Environmental-App/1.0'
            }
        });
        
        if (!response.ok) {
            throw new Error('Search service unavailable');
        }
        
        const results = await response.json();
        
        if (results.length === 0) {
            resultsContainer.innerHTML = '<div class="p-3 text-sm text-gray-500">No locations found. Try a different search term.</div>';
        } else {
            resultsContainer.innerHTML = results.map((result, index) => {
                const displayName = result.display_name || 'Unknown location';
                const mainName = displayName.split(',')[0] || 'Unknown';
                const fullAddress = displayName;
                
                return `
                    <div class="search-result-item p-3 text-sm border-b border-gray-100 last:border-b-0 hover:bg-gray-50 cursor-pointer" 
                         onclick="selectSearchResult(${result.lat}, ${result.lon}, '${fullAddress.replace(/'/g, "\\'")}', '${mainName.replace(/'/g, "\\'")}')">
                        <div class="font-medium text-gray-900 flex items-center">
                            <i data-lucide="map-pin" class="w-4 h-4 mr-2 text-green-600"></i>
                            ${mainName}
                        </div>
                        <div class="text-gray-500 text-xs mt-1 line-clamp-2">${fullAddress}</div>
                    </div>
                `;
            }).join('');
            
            // Re-initialize Lucide icons
            lucide.createIcons();
        }
        
        resultsContainer.classList.remove('hidden');
    } catch (error) {
        console.error('Search error:', error);
        resultsContainer.innerHTML = '<div class="p-3 text-sm text-red-500">Search failed. Please try again.</div>';
        resultsContainer.classList.remove('hidden');
    }
}

async function searchEditLocation(query) {
    const resultsContainer = document.getElementById('editSearchResults');
    
    if (!resultsContainer) return;
    
    // Show loading state
    resultsContainer.innerHTML = '<div class="p-3 text-sm text-gray-500"><i data-lucide="loader" class="w-4 h-4 inline mr-2 animate-spin"></i>Searching...</div>';
    resultsContainer.classList.remove('hidden');
    
    try {
        // Use Nominatim API for geocoding with better parameters
        const response = await fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(query)}&format=json&limit=8&addressdetails=1&extratags=1&namedetails=1`, {
            headers: {
                'User-Agent': 'Sylva-Environmental-App/1.0'
            }
        });
        
        if (!response.ok) {
            throw new Error('Search service unavailable');
        }
        
        const results = await response.json();
        
        if (results.length === 0) {
            resultsContainer.innerHTML = '<div class="p-3 text-sm text-gray-500">No locations found. Try a different search term.</div>';
        } else {
            resultsContainer.innerHTML = results.map((result, index) => {
                const displayName = result.display_name || 'Unknown location';
                const mainName = displayName.split(',')[0] || 'Unknown';
                const fullAddress = displayName;
                
                return `
                    <div class="search-result-item p-3 text-sm border-b border-gray-100 last:border-b-0 hover:bg-gray-50 cursor-pointer" 
                         onclick="selectEditSearchResult(${result.lat}, ${result.lon}, '${fullAddress.replace(/'/g, "\\'")}', '${mainName.replace(/'/g, "\\'")}')">
                        <div class="font-medium text-gray-900 flex items-center">
                            <i data-lucide="map-pin" class="w-4 h-4 mr-2 text-blue-600"></i>
                            ${mainName}
                        </div>
                        <div class="text-gray-500 text-xs mt-1 line-clamp-2">${fullAddress}</div>
                    </div>
                `;
            }).join('');
            
            // Re-initialize Lucide icons
            lucide.createIcons();
        }
        
        resultsContainer.classList.remove('hidden');
    } catch (error) {
        console.error('Search error:', error);
        resultsContainer.innerHTML = '<div class="p-3 text-sm text-red-500">Search failed. Please try again.</div>';
        resultsContainer.classList.remove('hidden');
    }
}

function selectSearchResult(lat, lng, address, shortName) {
    // Update map view and add marker
    if (addReportMap) {
        addReportMap.setView([lat, lng], 16);
        
        // Remove previous marker
        if (currentMarker) {
            addReportMap.removeLayer(currentMarker);
        }
        
        // Add new marker
        currentMarker = L.marker([lat, lng]).addTo(addReportMap);
        
        // Update hidden inputs
        document.getElementById('addLatitude').value = parseFloat(lat).toFixed(6);
        document.getElementById('addLongitude').value = parseFloat(lng).toFixed(6);
        
        // Update coordinate display and address
        document.getElementById('selectedCoordinates').textContent = 
            `Selected: ${parseFloat(lat).toFixed(6)}, ${parseFloat(lng).toFixed(6)}`;
        document.getElementById('addAddress').value = address;
        
        // Clear search and show short name in search input
        document.getElementById('locationSearch').value = shortName || address.split(',')[0];
        document.getElementById('searchResults').classList.add('hidden');
    }
}

function selectEditSearchResult(lat, lng, address, shortName) {
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
        document.getElementById('editLatitude').value = parseFloat(lat).toFixed(6);
        document.getElementById('editLongitude').value = parseFloat(lng).toFixed(6);
        
        // Update coordinate display and address
        document.getElementById('editSelectedCoordinates').textContent = 
            `Selected: ${parseFloat(lat).toFixed(6)}, ${parseFloat(lng).toFixed(6)}`;
        document.getElementById('editAddress').value = address;
        
        // Clear search and show short name in search input
        document.getElementById('editLocationSearch').value = shortName || address.split(',')[0];
        document.getElementById('editSearchResults').classList.add('hidden');
    }
}

function handleImageUpload(input) {
    const files = Array.from(input.files);
    const maxFiles = 5;
    const maxSize = 2 * 1024 * 1024; // 2MB
    
    // Check file count
    if (selectedImages.length + files.length > maxFiles) {
        alert(`Maximum ${maxFiles} images allowed`);
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
        
        selectedImages.push(file);
        
        // Create preview
        const reader = new FileReader();
        reader.onload = function(e) {
            addImagePreview(file.name, e.target.result, selectedImages.length - 1);
        };
        reader.readAsDataURL(file);
    });
    
    // Show preview area
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
    
    // Re-initialize Lucide icons
    lucide.createIcons();
}

function removeImage(index) {
    selectedImages.splice(index, 1);
    
    // Rebuild preview
    const container = document.getElementById('previewContainer');
    container.innerHTML = '';
    
    selectedImages.forEach((file, i) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            addImagePreview(file.name, e.target.result, i);
        };
        reader.readAsDataURL(file);
    });
    
    // Hide preview if no images
    if (selectedImages.length === 0) {
        document.getElementById('imagePreview').classList.add('hidden');
    }
}

// Edit form image handling
let selectedEditImages = [];
let existingImages = [];
let imagesToDelete = [];

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
    lucide.createIcons();
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
    lucide.createIcons();
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

// Add Report Functions
function openAddReportModal() {
    document.getElementById('addReportModal').classList.remove('hidden');
    
    // Initialize map after modal is shown
    setTimeout(() => {
        initializeAddReportMap();
    }, 100);
}

function closeAddModal() {
    document.getElementById('addReportModal').classList.add('hidden');
    document.getElementById('addReportForm').reset();
    
    // Reset map and coordinates
    if (addReportMap) {
        addReportMap.remove();
        addReportMap = null;
    }
    currentMarker = null;
    document.getElementById('addLatitude').value = '';
    document.getElementById('addLongitude').value = '';
    document.getElementById('selectedCoordinates').textContent = 'No location selected';
}

function initializeAddReportMap() {
    if (addReportMap) {
        addReportMap.remove();
    }
    
    // Default to New York City center
    const defaultLat = 40.7128;
    const defaultLng = -74.0060;
    
    // Initialize map
    addReportMap = L.map('addReportMap').setView([defaultLat, defaultLng], 12);
    
    // Add tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap contributors'
    }).addTo(addReportMap);
    
    // Add click event to map
    addReportMap.on('click', function(e) {
        const lat = e.latlng.lat;
        const lng = e.latlng.lng;
        
        // Remove previous marker
        if (currentMarker) {
            addReportMap.removeLayer(currentMarker);
        }
        
        // Add new marker
        currentMarker = L.marker([lat, lng]).addTo(addReportMap);
        
        // Update hidden inputs
        document.getElementById('addLatitude').value = lat.toFixed(6);
        document.getElementById('addLongitude').value = lng.toFixed(6);
        
        // Update coordinate display
        document.getElementById('selectedCoordinates').textContent = 
            `Selected: ${lat.toFixed(6)}, ${lng.toFixed(6)}`;
        
        // Try to get address using reverse geocoding
        getAddressFromCoordinates(lat, lng);
    });
    
    // Try to get user's current location
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            const userLat = position.coords.latitude;
            const userLng = position.coords.longitude;
            addReportMap.setView([userLat, userLng], 15);
        }, function() {
            // If geolocation fails, keep default location
        });
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

    // Show loading state
    const btn = event.target;
    const originalHtml = btn.innerHTML;
    btn.innerHTML = '<i data-lucide="loader" class="w-4 h-4 animate-spin"></i><span>Getting location...</span>';
    btn.disabled = true;

    navigator.geolocation.getCurrentPosition(
        function(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            
            // Update map view and add marker
            if (addReportMap) {
                addReportMap.setView([lat, lng], 16);
                
                // Remove previous marker
                if (currentMarker) {
                    addReportMap.removeLayer(currentMarker);
                }
                
                // Add new marker
                currentMarker = L.marker([lat, lng]).addTo(addReportMap);
                
                // Update hidden inputs
                document.getElementById('addLatitude').value = lat.toFixed(6);
                document.getElementById('addLongitude').value = lng.toFixed(6);
                
                // Update coordinate display
                document.getElementById('selectedCoordinates').textContent = 
                    `Selected: ${lat.toFixed(6)}, ${lng.toFixed(6)}`;
                
                // Try to get address
                getAddressFromCoordinates(lat, lng);
            }
                
            // Reset button
            btn.innerHTML = originalHtml;
            btn.disabled = false;
            
            alert('Current location set successfully!');
        },
        function(error) {
            // Reset button
            btn.innerHTML = originalHtml;
            btn.disabled = false;
            
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

// Handle add form submission
document.getElementById('addReportForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    // Add selected images to form data
    selectedImages.forEach((file, index) => {
        formData.append('images[]', file);
    });
    
    const reportData = {
        title: formData.get('title'),
        description: formData.get('description'),
        type: formData.get('type'),
        urgency: formData.get('urgency'),
        latitude: parseFloat(formData.get('latitude')),
        longitude: parseFloat(formData.get('longitude')),
        address: formData.get('address') || null
    };

    // Validation
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

        // Create FormData for file upload
        const submitFormData = new FormData();
        Object.keys(reportData).forEach(key => {
            if (reportData[key] !== null) {
                submitFormData.append(key, reportData[key]);
            }
        });
        
        // Add images
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
            // Show success message
            alert('Report added successfully!');
            closeAddModal();
            
            // Add the new report to the cards instantly
            addReportToCards(result.data);
            
            // Update statistics
            updateStatistics();
            
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
        }
    }
});

// Function to add new report to cards instantly
function addReportToCards(report) {
    const container = document.getElementById('reportsContainer');
    
    // Remove "no reports found" message if it exists
    const noReportsMsg = document.querySelector('.text-center.py-12');
    if (noReportsMsg) {
        noReportsMsg.remove();
    }
    
    // If container doesn't exist (no reports before), create it
    if (!container) {
        const reportsSection = document.querySelector('.bg-white.rounded-lg.shadow-sm.border.border-gray-200 .p-6');
        reportsSection.innerHTML = '<div id="reportsContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"></div>';
        const newContainer = document.getElementById('reportsContainer');
        addCardToContainer(newContainer, report);
    } else {
        addCardToContainer(container, report);
    }
    
    // Add to reports array
    reports.unshift(report);
}

function addCardToContainer(container, report) {
    // Format type display
    const typeDisplay = report.type.replace('_', ' ').split(' ').map(word => 
        word.charAt(0).toUpperCase() + word.slice(1)
    ).join(' ');

    // Create urgency color class
    const urgencyClass = report.urgency === 'high' ? 'bg-red-100 text-red-800' : 
                        (report.urgency === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800');
    
    // Get type icon
    const iconMap = {
        'tree_planting': 'tree-pine',
        'maintenance': 'wrench',
        'pollution': 'alert-triangle',
        'green_space_suggestion': 'leaf'
    };
    const icon = iconMap[report.type] || 'map-pin';

    // Create image section
    let imageSection = '';
    if (report.image_urls && report.image_urls.length > 0) {
        const imageCount = report.image_urls.length > 1 ? `<div class="absolute top-2 right-2 z-20 bg-black/60 text-white px-2 py-1 rounded-full text-xs"><i data-lucide="image" class="w-3 h-3 inline mr-1"></i>${report.image_urls.length}</div>` : '';
        imageSection = `
            <div class="relative h-48 bg-gray-100">
                <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent z-10"></div>
                <img src="${report.image_urls[0]}" alt="${report.title}" class="w-full h-full object-cover">
                ${imageCount}
            </div>
        `;
    } else {
        imageSection = `
            <div class="h-32 bg-gradient-to-br from-green-100 to-blue-100 flex items-center justify-center">
                <div class="text-center">
                    <i data-lucide="${icon}" class="w-8 h-8 text-green-600"></i>
                </div>
            </div>
        `;
    }

    // Create new card
    const newCard = document.createElement('div');
    newCard.className = 'bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden';
    newCard.innerHTML = `
        ${imageSection}
        <div class="p-4">
            <div class="flex items-start justify-between mb-3">
                <h4 class="text-lg font-semibold text-gray-900 line-clamp-2 flex-1 mr-2">
                    ${report.title}
                </h4>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium flex-shrink-0 ${urgencyClass}">
                    ${report.urgency.charAt(0).toUpperCase() + report.urgency.slice(1)}
                </span>
            </div>
            
            <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                ${report.description}
            </p>
            
            <div class="space-y-2 mb-4">
                <div class="flex items-center text-sm text-gray-500">
                    <i data-lucide="tag" class="w-4 h-4 mr-2"></i>
                    <span class="capitalize">${typeDisplay}</span>
                </div>
                <div class="flex items-center text-sm text-gray-500">
                    <i data-lucide="user" class="w-4 h-4 mr-2"></i>
                    <span>${report.user ? report.user.name : 'Test User'}</span>
                </div>
                <div class="flex items-center text-sm text-gray-500">
                    <i data-lucide="calendar" class="w-4 h-4 mr-2"></i>
                    <span>${new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</span>
                </div>
                ${report.address ? `
                <div class="flex items-center text-sm text-gray-500">
                    <i data-lucide="map-pin" class="w-4 h-4 mr-2"></i>
                    <span class="truncate">${report.address.length > 40 ? report.address.substring(0, 40) + '...' : report.address}</span>
                </div>
                ` : ''}
            </div>
            
            <div class="flex items-center justify-between mb-4">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                    <i data-lucide="clock" class="w-3 h-3 mr-1"></i>
                    Pending
                </span>
            </div>
            
            <div class="flex space-x-2">
                <a href="/map" class="flex-1 text-center bg-green-50 text-green-700 hover:bg-green-100 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                    <i data-lucide="map-pin" class="w-4 h-4 inline mr-1"></i>
                    View on Map
                </a>
                <button onclick="editReportModal(${report.id})" class="flex-1 bg-blue-50 text-blue-700 hover:bg-blue-100 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                    <i data-lucide="edit-2" class="w-4 h-4 inline mr-1"></i>
                    Edit
                </button>
                <button onclick="deleteReportConfirm(${report.id})" class="bg-red-50 text-red-700 hover:bg-red-100 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                    <i data-lucide="trash-2" class="w-4 h-4 inline"></i>
                </button>
            </div>
        </div>
    `;
    
    // Insert at the beginning of the container
    container.insertBefore(newCard, container.firstChild);
    
    // Add a subtle highlight animation
    newCard.style.backgroundColor = '#f0fdf4';
    setTimeout(() => {
        newCard.style.backgroundColor = '';
    }, 3000);
    
    // Re-initialize Lucide icons
    lucide.createIcons();
}

// Function to update statistics
function updateStatistics() {
    // Update total reports count
    const totalElement = document.querySelector('.grid .bg-white:first-child p.text-2xl');
    if (totalElement) {
        const currentTotal = parseInt(totalElement.textContent);
        totalElement.textContent = currentTotal + 1;
    }
    
    // Update pending reports count
    const pendingElement = document.querySelector('.grid .bg-white:nth-child(2) p.text-2xl');
    if (pendingElement) {
        const currentPending = parseInt(pendingElement.textContent);
        pendingElement.textContent = currentPending + 1;
    }
    
    // Update this month count
    const thisMonthElement = document.querySelector('.grid .bg-white:last-child p.text-2xl');
    if (thisMonthElement) {
        const currentThisMonth = parseInt(thisMonthElement.textContent);
        thisMonthElement.textContent = currentThisMonth + 1;
    }
}

// Edit Report Functions
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

async function deleteReportConfirm(reportId) {
    if (!confirm('Are you sure you want to delete this report? This action cannot be undone.')) {
        return;
    }
    
    try {
        const response = await fetch(`/api/reports-public/${reportId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        });

        const result = await response.json();

        if (response.ok && result.success) {
            alert('Report deleted successfully!');
            // Reload the page to reflect changes
            window.location.reload();
        } else {
            throw new Error(result.message || 'Failed to delete report');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error deleting report: ' + error.message);
    }
}

// Function to add new report to table instantly
function addReportToTable(report) {
    const tbody = document.querySelector('table tbody');
    
    // Remove "no reports found" message if it exists
    const noReportsMsg = tbody.querySelector('tr td[colspan="7"]');
    if (noReportsMsg) {
        noReportsMsg.parentElement.remove();
    }

    // Format type display
    const typeDisplay = report.type.replace('_', ' ').split(' ').map(word => 
        word.charAt(0).toUpperCase() + word.slice(1)
    ).join(' ');

    // Create urgency color class
    const urgencyClass = report.urgency === 'high' ? 'bg-red-100 text-red-800' : 
                        (report.urgency === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800');

    // Create new row
    const newRow = document.createElement('tr');
    newRow.className = 'hover:bg-gray-50';
    newRow.innerHTML = `
        <td class="px-6 py-4 whitespace-nowrap">
            <div class="text-sm font-medium text-gray-900">${report.title}</div>
            <div class="text-sm text-gray-500">${report.description.length > 50 ? report.description.substring(0, 50) + '...' : report.description}</div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                ${typeDisplay}
            </span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${urgencyClass}">
                ${report.urgency.charAt(0).toUpperCase() + report.urgency.slice(1)}
            </span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                Pending
            </span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
            ${report.user ? report.user.name : 'Test User'}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            ${new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
            <div class="flex space-x-2">
                <a href="/map" class="text-green-600 hover:text-green-900 text-xs px-2 py-1 border border-green-600 rounded hover:bg-green-50 transition-colors duration-200">
                    <i data-lucide="map-pin" class="w-3 h-3 inline mr-1"></i>
                    View on Map
                </a>
                <button onclick="editReportModal(${report.id})" class="text-blue-600 hover:text-blue-900 text-xs px-2 py-1 border border-blue-600 rounded hover:bg-blue-50 transition-colors duration-200">
                    <i data-lucide="edit-2" class="w-3 h-3 inline mr-1"></i>
                    Edit
                </button>
                <button onclick="deleteReportConfirm(${report.id})" class="text-red-600 hover:text-red-900 text-xs px-2 py-1 border border-red-600 rounded hover:bg-red-50 transition-colors duration-200">
                    <i data-lucide="trash-2" class="w-3 h-3 inline mr-1"></i>
                    Delete
                </button>
            </div>
        </td>
    `;
    
    // Insert at the beginning of the table
    tbody.insertBefore(newRow, tbody.firstChild);
    
    // Add to reports array
    reports.unshift(report);
    
    // Add a subtle highlight animation
    newRow.style.backgroundColor = '#f0fdf4';
    setTimeout(() => {
        newRow.style.backgroundColor = '';
    }, 3000);
}

// Handle edit form submission
document.getElementById('editReportForm').addEventListener('submit', async function(e) {
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
            // Reload the page to reflect changes
            window.location.reload();
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
            lucide.createIcons();
        }
    }
});

// Close modals when clicking outside
document.getElementById('addReportModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeAddModal();
    }
});

document.getElementById('editReportModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditModal();
    }
});

// Additional functions for edit form functionality
function initializeEditReportMap(report) {
    if (editReportMap) {
        editReportMap.remove();
    }
    
    // Use report location or default to New York City center
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
        const response = await fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`, {
            headers: {
                'User-Agent': 'Sylva-Environmental-App/1.0'
            }
        });
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

    // Show loading state
    const btn = event.target;
    const originalHtml = btn.innerHTML;
    btn.innerHTML = '<i data-lucide="loader" class="w-4 h-4 animate-spin"></i><span>Getting location...</span>';
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
                
            // Reset button
            btn.innerHTML = originalHtml;
            btn.disabled = false;
            
            alert('Current location set successfully!');
        },
        function(error) {
            // Reset button
            btn.innerHTML = originalHtml;
            btn.disabled = false;
            
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
</script>
@endpush
@endsection