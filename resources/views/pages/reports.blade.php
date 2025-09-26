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

    <!-- Reports Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">Recent Reports</h3>
            <button onclick="openAddReportModal()" 
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors duration-200">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Add Report</span>
            </button>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Report</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Urgency</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reporter</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($reports as $report)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $report->title }}</div>
                            <div class="text-sm text-gray-500">{{ Str::limit($report->description, 50) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 capitalize">
                                {{ str_replace('_', ' ', $report->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $report->urgency === 'high' ? 'bg-red-100 text-red-800' : 
                                   ($report->urgency === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                {{ ucfirst($report->urgency) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $report->status === 'validated' ? 'bg-green-100 text-green-800' : 
                                   ($report->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ ucfirst($report->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $report->user->name ?? 'Unknown' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $report->created_at->format('M j, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('map') }}" class="text-green-600 hover:text-green-900 text-xs px-2 py-1 border border-green-600 rounded hover:bg-green-50 transition-colors duration-200">
                                    <i data-lucide="map-pin" class="w-3 h-3 inline mr-1"></i>
                                    View on Map
                                </a>
                                <button onclick="editReportModal({{ $report->id }})" class="text-blue-600 hover:text-blue-900 text-xs px-2 py-1 border border-blue-600 rounded hover:bg-blue-50 transition-colors duration-200">
                                    <i data-lucide="edit-2" class="w-3 h-3 inline mr-1"></i>
                                    Edit
                                </button>
                                <button onclick="deleteReportConfirm({{ $report->id }})" class="text-red-600 hover:text-red-900 text-xs px-2 py-1 border border-red-600 rounded hover:bg-red-50 transition-colors duration-200">
                                    <i data-lucide="trash-2" class="w-3 h-3 inline mr-1"></i>
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="text-gray-500">
                                <i data-lucide="flag" class="w-12 h-12 mx-auto mb-4 text-gray-300"></i>
                                <p class="text-lg font-medium">No reports found</p>
                                <p class="text-sm">Be the first to report an environmental issue!</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
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
                                    Edit Report
                                </h3>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                                        <input type="text" id="editTitle" name="title" required
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                        <textarea id="editDescription" name="description" required rows="3"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                                            <select id="editType" name="type" required
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                <option value="tree_planting">Tree Planting</option>
                                                <option value="maintenance">Maintenance</option>
                                                <option value="pollution">Pollution</option>
                                                <option value="green_space_suggestion">Green Space</option>
                                            </select>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Urgency</label>
                                            <select id="editUrgency" name="urgency" required
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                <option value="low">Low</option>
                                                <option value="medium">Medium</option>
                                                <option value="high">High</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Update Report
                        </button>
                        <button type="button" onclick="closeEditModal()"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
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
let currentMarker = null;

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

        const response = await fetch('/api/reports-public', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify(reportData)
        });

        const result = await response.json();

        if (response.ok && result.success) {
            // Show success message
            alert('Report added successfully!');
            closeAddModal();
            
            // Add the new report to the table instantly
            addReportToTable(result.data);
            
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
    
    // Re-initialize Lucide icons for the new row
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
    
    // Populate the form
    document.getElementById('editTitle').value = report.title;
    document.getElementById('editDescription').value = report.description;
    document.getElementById('editType').value = report.type;
    document.getElementById('editUrgency').value = report.urgency;
    
    // Show modal
    document.getElementById('editReportModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editReportModal').classList.add('hidden');
    currentEditingReportId = null;
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
    
    // Populate the form
    document.getElementById('editTitle').value = report.title;
    document.getElementById('editDescription').value = report.description;
    document.getElementById('editType').value = report.type;
    document.getElementById('editUrgency').value = report.urgency;
    
    // Show modal
    document.getElementById('editReportModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editReportModal').classList.add('hidden');
    currentEditingReportId = null;
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

// Handle edit form submission
document.getElementById('editReportForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    if (!currentEditingReportId) {
        alert('No report selected for editing');
        return;
    }
    
    const formData = new FormData(this);
    const reportData = {
        title: formData.get('title'),
        description: formData.get('description'),
        type: formData.get('type'),
        urgency: formData.get('urgency')
    };
    
    try {
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Updating...';
        submitBtn.disabled = true;

        const response = await fetch(`/api/reports-public/${currentEditingReportId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify(reportData)
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
            submitBtn.textContent = 'Update Report';
            submitBtn.disabled = false;
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
</script>
@endpush
@endsection