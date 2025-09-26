@extends('layouts.dashboard')

@section('page-title', 'Map')

@section('page-content')
<div class="relative h-full w-full" x-data="mapComponent()">
    <!-- Map Container -->
    <div id="map" class="w-full h-full rounded-xl shadow-lg"></div>

    <!-- Map Legend -->
    <div class="absolute top-4 left-4 bg-white/95 backdrop-blur-sm rounded-xl shadow-2xl p-4 border border-gray-200 z-[1000] max-w-48">
        <h3 class="font-bold text-gray-900 mb-3 text-sm flex items-center gap-2">
            <i data-lucide="info" class="w-4 h-4"></i>
            Map Legend
        </h3>
        <div class="space-y-2">
            <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-red-500 rounded-full shadow-sm"></div>
                <span class="text-xs font-medium text-gray-700">High Priority</span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-orange-500 rounded-full shadow-sm"></div>
                <span class="text-xs font-medium text-gray-700">Medium Priority</span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-green-500 rounded-full shadow-sm"></div>
                <span class="text-xs font-medium text-gray-700">Low Priority</span>
            </div>
        </div>
        
        <div class="mt-3 pt-3 border-t border-gray-200">
            <h4 class="font-semibold text-gray-800 mb-2 text-xs">Report Types</h4>
            <div class="space-y-1">
                <div class="flex items-center space-x-1">
                    <i data-lucide="tree-pine" class="w-3 h-3 text-green-600"></i>
                    <span class="text-xs text-gray-600">Tree Planting</span>
                </div>
                <div class="flex items-center space-x-1">
                    <i data-lucide="wrench" class="w-3 h-3 text-orange-600"></i>
                    <span class="text-xs text-gray-600">Maintenance</span>
                </div>
                <div class="flex items-center space-x-1">
                    <i data-lucide="alert-triangle" class="w-3 h-3 text-red-600"></i>
                    <span class="text-xs text-gray-600">Pollution</span>
                </div>
                <div class="flex items-center space-x-1">
                    <i data-lucide="leaf" class="w-3 h-3 text-emerald-600"></i>
                    <span class="text-xs text-gray-600">Green Space</span>
                </div>
            </div>
        </div>
    </div>

    <!-- New Floating Action Buttons -->
    <div class="absolute bottom-6 right-6 z-[1000] flex flex-col space-y-3">
        <!-- Add Report Button -->
        <button
            x-on:click="toggleAddReportMode()"
            class="w-16 h-16 rounded-full shadow-2xl flex items-center justify-center text-white transition-all duration-300 border-4 border-white hover:scale-110 transform focus:outline-none focus:ring-4 focus:ring-offset-2"
            :class="addReportMode ? 'bg-red-500 hover:bg-red-600 rotate-45 focus:ring-red-300' : 'bg-green-500 hover:bg-green-600 focus:ring-green-300'"
        >
            <i :data-lucide="addReportMode ? 'x' : 'plus'" class="w-6 h-6"></i>
        </button>

        <!-- Search Button -->
        <button
            x-on:click="openSearchDialog()"
            class="w-14 h-14 rounded-full shadow-xl flex items-center justify-center text-white transition-all duration-300 border-3 border-white hover:scale-110 transform bg-blue-500 hover:bg-blue-600 focus:outline-none focus:ring-4 focus:ring-blue-300 focus:ring-offset-2"
        >
            <i data-lucide="search" class="w-5 h-5"></i>
        </button>
    </div>

    <!-- Search Bar Overlay -->
    <div x-show="showSearchDialog" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute top-4 left-1/2 transform -translate-x-1/2 z-[1001] w-full max-w-md"
         x-cloak>
        <div class="bg-white rounded-lg shadow-2xl border border-gray-200 p-4">
            <div class="flex items-center space-x-3 mb-4">
                <i data-lucide="search" class="w-5 h-5 text-blue-500"></i>
                <h3 class="text-lg font-semibold text-gray-900">Search Reports</h3>
                <button x-on:click="closeSearchDialog()" class="ml-auto p-1 hover:bg-gray-100 rounded">
                    <i data-lucide="x" class="w-4 h-4 text-gray-500"></i>
                </button>
            </div>
            
            <div class="space-y-3">
                <!-- Location Search with Autocomplete -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search by location</label>
                    <div class="relative">
                        <input type="text" 
                               x-model="searchQuery"
                               x-on:input="searchPlaces()"
                               placeholder="Search for places in Paris..."
                               class="w-full px-3 py-2 pl-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <i data-lucide="map-pin" class="absolute left-3 top-3 w-4 h-4 text-gray-400"></i>
                    </div>
                    <!-- Autocomplete suggestions -->
                    <div x-show="searchSuggestions.length > 0" 
                         class="absolute bg-white border border-gray-200 rounded-lg mt-1 w-full shadow-lg z-[1002]"
                         x-cloak>
                        <template x-for="suggestion in searchSuggestions" :key="suggestion.place_id">
                            <div x-on:click="selectPlace(suggestion)" 
                                 class="px-3 py-2 hover:bg-gray-100 cursor-pointer border-b border-gray-100 last:border-b-0">
                                <div class="font-medium text-sm" x-text="suggestion.display_name"></div>
                            </div>
                        </template>
                    </div>
                </div>
                
                <!-- Filter by Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Filter by type</label>
                    <select x-model="searchType" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Types</option>
                        <option value="tree_planting">Tree Planting</option>
                        <option value="maintenance">Maintenance</option>
                        <option value="pollution">Pollution</option>
                        <option value="green_space_suggestion">Green Space</option>
                    </select>
                </div>
                
                <!-- Filter by Urgency -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Filter by urgency</label>
                    <select x-model="searchUrgency" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Priorities</option>
                        <option value="low">Low Priority</option>
                        <option value="medium">Medium Priority</option>
                        <option value="high">High Priority</option>
                    </select>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex space-x-2 pt-2">
                    <button x-on:click="applySearch()" 
                            class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-medium">
                        Apply Filters
                    </button>
                    <button x-on:click="clearFilters()" 
                            class="flex-1 px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 text-sm font-medium">
                        Clear All
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Report Mode Instructions -->
    <div x-show="addReportMode" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-[999] bg-green-600 text-white px-6 py-3 rounded-full shadow-lg pointer-events-none"
         x-cloak>
        <div class="flex items-center space-x-2">
            <i data-lucide="crosshair" class="w-5 h-5 animate-pulse"></i>
            <span class="font-medium">Click anywhere on the map to place a report</span>
        </div>
    </div>

    <!-- Report Form Modal -->
            Map Legend
        </h3>
        <div class="space-y-2">
            <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-red-500 rounded-full shadow-sm"></div>
                <span class="text-xs font-medium text-gray-700">High Priority</span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-orange-500 rounded-full shadow-sm"></div>
                <span class="text-xs font-medium text-gray-700">Medium Priority</span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-green-500 rounded-full shadow-sm"></div>
                <span class="text-xs font-medium text-gray-700">Low Priority</span>
            </div>
        </div>
        
        <div class="mt-3 pt-3 border-t border-gray-200">
            <h4 class="font-semibold text-gray-800 mb-2 text-xs">Report Types</h4>
            <div class="space-y-1">
                <div class="flex items-center space-x-1">
                    <i data-lucide="tree-pine" class="w-3 h-3 text-green-600"></i>
                    <span class="text-xs text-gray-600">Tree Planting</span>
                </div>
                <div class="flex items-center space-x-1">
                    <i data-lucide="alert-triangle" class="w-3 h-3 text-orange-600"></i>
                    <span class="text-xs text-gray-600">Maintenance</span>
                </div>
                <div class="flex items-center space-x-1">
                    <i data-lucide="alert-triangle" class="w-3 h-3 text-red-600"></i>
                    <span class="text-xs text-gray-600">Pollution</span>
                </div>
                <div class="flex items-center space-x-1">
                    <i data-lucide="check-circle" class="w-3 h-3 text-emerald-600"></i>
                    <span class="text-xs text-gray-600">Green Space</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Form Modal -->
    <div x-show="showReportForm" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[10000] overflow-y-auto"
         x-cloak>
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form x-on:submit.prevent="submitReport()">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                    Add New Report
                                </h3>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                                        <input type="text" x-model="reportForm.title" required
                                               class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none transition-colors duration-200"
                                               placeholder="Enter report title">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                        <textarea x-model="reportForm.description" required rows="3"
                                                class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none transition-colors duration-200"
                                                placeholder="Describe the issue or suggestion"></textarea>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                                            <select x-model="reportForm.type" required
                                                    class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none transition-colors duration-200">
                                                <option value="">Select type</option>
                                                <option value="tree_planting">Tree Planting</option>
                                                <option value="maintenance">Maintenance</option>
                                                <option value="pollution">Pollution</option>
                                                <option value="green_space_suggestion">Green Space</option>
                                            </select>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Urgency</label>
                                            <select x-model="reportForm.urgency" required
                                                    class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none transition-colors duration-200">
                                                <option value="">Select urgency</option>
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
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Submit Report
                        </button>
                        <button type="button" x-on:click="closeReportForm()"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Report Form Modal -->
    <div x-show="showEditForm" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[10000] overflow-y-auto"
         x-cloak>
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="edit-form" x-on:submit.prevent="updateReport()">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                    Edit Report
                                </h3>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                                        <input type="text" x-model="reportForm.title" required
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                        <textarea x-model="reportForm.description" required
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 rows-3"></textarea>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                                            <select x-model="reportForm.type" required
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                <option value="">Select type</option>
                                                <option value="tree_planting">Tree Planting</option>
                                                <option value="maintenance">Maintenance</option>
                                                <option value="pollution">Pollution</option>
                                                <option value="green_space_suggestion">Green Space</option>
                                            </select>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Urgency</label>
                                            <select x-model="reportForm.urgency" required
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                <option value="">Select urgency</option>
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
                        <button type="button" x-on:click="closeEditForm()"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Search Dialog Modal -->
    <div x-show="showSearchDialog"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[10000] overflow-y-auto"
         x-cloak>
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                <i data-lucide="search" class="w-5 h-5 inline mr-2"></i>
                                Search & Filter Reports
                            </h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Search by title or description</label>
                                    <input type="text" x-model="searchQuery" placeholder="Enter search keywords..."
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Filter by type</label>
                                    <select x-model="searchType"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">All Types</option>
                                        <option value="tree_planting">Tree Planting</option>
                                        <option value="maintenance">Maintenance</option>
                                        <option value="pollution">Pollution</option>
                                        <option value="green_space_suggestion">Green Space Suggestion</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Filter by urgency</label>
                                    <select x-model="searchUrgency"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">All Priorities</option>
                                        <option value="low">Low Priority</option>
                                        <option value="medium">Medium Priority</option>
                                        <option value="high">High Priority</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" x-on:click="applyFilters()"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Apply Filters
                    </button>
                    <button type="button" x-on:click="clearFilters()"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Clear All
                    </button>
                    <button type="button" x-on:click="closeSearchDialog()"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
.custom-popup .leaflet-popup-content-wrapper {
    border-radius: 12px;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.custom-popup .leaflet-popup-content {
    margin: 0;
    line-height: 1.4;
}

.custom-popup .leaflet-popup-tip {
    border-left-color: white;
    border-right-color: white;
}

.map-container {
    position: relative;
    height: 100vh;
    overflow: hidden;
}

/* Loading animation */
.loading-spinner {
    border: 3px solid #f3f4f6;
    border-top: 3px solid #10b981;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Custom marker animations */
.leaflet-marker-icon {
    transition: transform 0.2s ease-in-out;
}

.leaflet-marker-icon:hover {
    transform: scale(1.1);
    z-index: 1000;
}

/* Form improvements */
.form-input:focus {
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
function mapComponent() {
    return {
        map: null,
        addReportMode: false,
        showReportForm: false,
        showEditForm: false,
        showSearchDialog: false,
        newReportLocation: null,
        editingReport: null,
        reports: @json($reports),
        allReports: @json($reports), // Keep copy for search filtering
        searchQuery: '',
        searchType: '',
        searchUrgency: '',
        searchSuggestions: [],
        reportForm: {
            title: '',
            description: '',
            type: '',
            urgency: '',
            latitude: '',
            longitude: '',
        },
        
        init() {
            this.initMap();
            this.loadReports();
        },
        
        initMap() {
            this.map = L.map('map').setView([48.8566, 2.3522], 12);
            
            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>'
            }).addTo(this.map);
            
            // Add click event listener
            this.map.on('click', (e) => {
                if (this.addReportMode) {
                    this.newReportLocation = {
                        latitude: e.latlng.lat,
                        longitude: e.latlng.lng
                    };
                    this.reportForm.latitude = e.latlng.lat;
                    this.reportForm.longitude = e.latlng.lng;
                    this.showReportForm = true;
                    this.addReportMode = false; // Exit add mode after placing report
                }
            });
        },
        
        loadReports() {
            if (!this.reports || this.reports.length === 0) {
                console.log('No reports to display on map');
                return;
            }

            console.log(`Loading ${this.reports.length} reports on map`);
            
            this.reports.forEach(report => {
                if (!report.latitude || !report.longitude) {
                    console.warn('Report missing coordinates:', report);
                    return;
                }

                // Create custom icon based on urgency
                const iconColor = this.getMarkerColor(report.urgency);
                const marker = L.circleMarker([parseFloat(report.latitude), parseFloat(report.longitude)], {
                    radius: 8,
                    fillColor: iconColor,
                    color: '#fff',
                    weight: 2,
                    opacity: 1,
                    fillOpacity: 0.8
                }).addTo(this.map);
                
                const popupContent = `
                    <div class="p-4 max-w-sm">
                        <h3 class="font-bold text-lg mb-2 text-gray-900">${report.title}</h3>
                        <p class="text-sm text-gray-600 mb-3 leading-relaxed">${report.description}</p>
                        
                        <div class="flex flex-wrap gap-2 mb-3">
                            <span class="px-2 py-1 rounded-full text-xs font-medium ${this.getUrgencyClass(report.urgency)}">
                                ${this.capitalizeFirst(report.urgency)} priority
                            </span>
                            <span class="px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                ${this.formatReportType(report.type)}
                            </span>
                        </div>
                        
                        <div class="text-xs text-gray-500 mb-3">
                            <i data-lucide="calendar" class="w-3 h-3 inline mr-1"></i>
                            ${new Date(report.created_at).toLocaleDateString()}
                        </div>
                        
                        <div class="flex gap-2">
                            <button class="edit-report-btn flex-1 px-3 py-2 bg-blue-500 text-white text-xs rounded-lg hover:bg-blue-600 transition-colors duration-200 flex items-center justify-center gap-1" data-report-id="${report.id}">
                                <i data-lucide="edit-2" class="w-3 h-3"></i>
                                Edit
                            </button>
                            <button class="delete-report-btn flex-1 px-3 py-2 bg-red-500 text-white text-xs rounded-lg hover:bg-red-600 transition-colors duration-200 flex items-center justify-center gap-1" data-report-id="${report.id}">
                                <i data-lucide="trash-2" class="w-3 h-3"></i>
                                Delete
                            </button>
                        </div>
                    </div>
                `;
                
                marker.bindPopup(popupContent, {
                    maxWidth: 300,
                    className: 'custom-popup'
                });

                // Add event listeners when popup opens
                marker.on('popupopen', () => {
                    // Re-initialize Lucide icons for new popup content
                    setTimeout(() => {
                        lucide.createIcons();
                        
                        // Add event listeners to buttons
                        const editBtn = document.querySelector(`.edit-report-btn[data-report-id="${report.id}"]`);
                        const deleteBtn = document.querySelector(`.delete-report-btn[data-report-id="${report.id}"]`);
                        
                        if (editBtn) {
                            editBtn.addEventListener('click', (e) => {
                                e.preventDefault();
                                this.editReport(report.id);
                            });
                        }
                        
                        if (deleteBtn) {
                            deleteBtn.addEventListener('click', (e) => {
                                e.preventDefault();
                                this.deleteReport(report.id);
                            });
                        }
                    }, 100);
                });
            });
        },

        getMarkerColor(urgency) {
            const colors = {
                'high': '#ef4444',
                'medium': '#f97316',
                'low': '#22c55e'
            };
            return colors[urgency] || '#6b7280';
        },

        capitalizeFirst(str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
        },

        formatReportType(type) {
            const typeMap = {
                'tree_planting': 'Tree Planting',
                'maintenance': 'Maintenance',
                'pollution': 'Pollution',
                'green_space_suggestion': 'Green Space Suggestion'
            };
            return typeMap[type] || type;
        },
        
        getUrgencyClass(urgency) {
            const classes = {
                'high': 'bg-red-100 text-red-800',
                'medium': 'bg-orange-100 text-orange-800',
                'low': 'bg-green-100 text-green-800'
            };
            return classes[urgency] || 'bg-gray-100 text-gray-800';
        },
        
        toggleAddReportMode() {
            this.addReportMode = !this.addReportMode;
            this.newReportLocation = null;
            if (!this.addReportMode) {
                this.showReportForm = false;
            }
        },

        openSearchDialog() {
            this.showSearchDialog = true;
        },

        closeSearchDialog() {
            this.showSearchDialog = false;
            this.searchQuery = '';
            this.searchType = '';
            this.searchUrgency = '';
        },

        applyFilters() {
            let filteredReports = [...this.allReports];

            // Filter by search query
            if (this.searchQuery.trim()) {
                const query = this.searchQuery.toLowerCase();
                filteredReports = filteredReports.filter(report =>
                    report.title.toLowerCase().includes(query) ||
                    report.description.toLowerCase().includes(query)
                );
            }

            // Filter by type
            if (this.searchType) {
                filteredReports = filteredReports.filter(report => report.type === this.searchType);
            }

            // Filter by urgency
            if (this.searchUrgency) {
                filteredReports = filteredReports.filter(report => report.urgency === this.searchUrgency);
            }

            this.reports = filteredReports;
            this.reloadMap();
            this.closeSearchDialog();

            // Show user feedback
            alert(`Found ${filteredReports.length} report${filteredReports.length !== 1 ? 's' : ''} matching your criteria.`);
        },

        clearFilters() {
            this.reports = [...this.allReports];
            this.searchQuery = '';
            this.searchType = '';
            this.searchUrgency = '';
            this.reloadMap();
            this.closeSearchDialog();
            alert('All filters cleared. Showing all reports.');
        },

        async searchPlaces() {
            if (!this.searchQuery.trim() || this.searchQuery.length < 3) {
                this.searchSuggestions = [];
                return;
            }

            try {
                // Using OpenStreetMap Nominatim API for place search
                const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(this.searchQuery)}&limit=5&addressdetails=1`);
                const data = await response.json();
                
                this.searchSuggestions = data.map(place => ({
                    name: place.display_name,
                    lat: parseFloat(place.lat),
                    lon: parseFloat(place.lon),
                    type: place.type || 'location'
                }));
            } catch (error) {
                console.error('Error searching places:', error);
                this.searchSuggestions = [];
            }
        },

        selectPlace(place) {
            // Center the map on the selected place
            if (this.map) {
                this.map.setView([place.lat, place.lon], 15);
                
                // Add a temporary marker to show the selected location
                if (this.selectedLocationMarker) {
                    this.map.removeLayer(this.selectedLocationMarker);
                }
                
                this.selectedLocationMarker = L.marker([place.lat, place.lon], {
                    icon: L.divIcon({
                        className: 'selected-location-marker',
                        html: '<div style="background: #3b82f6; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3);"></div>',
                        iconSize: [20, 20],
                        iconAnchor: [10, 10]
                    })
                }).addTo(this.map);
                
                // Remove the marker after 3 seconds
                setTimeout(() => {
                    if (this.selectedLocationMarker) {
                        this.map.removeLayer(this.selectedLocationMarker);
                        this.selectedLocationMarker = null;
                    }
                }, 3000);
            }
            
            // Clear search
            this.searchQuery = '';
            this.searchSuggestions = [];
            this.closeSearchDialog();
        },
        
        closeReportForm() {
            this.showReportForm = false;
            this.addReportMode = false;
            this.newReportLocation = null;
            this.resetForm();
        },
        
        resetForm() {
            this.reportForm = {
                title: '',
                description: '',
                type: '',
                urgency: '',
                latitude: '',
                longitude: '',
            };
        },
        
        async submitReport() {
            try {
                // Show loading state
                const submitBtn = document.querySelector('button[type="submit"]');
                const originalText = submitBtn.textContent;
                submitBtn.textContent = 'Submitting...';
                submitBtn.disabled = true;

                const response = await fetch('/api/reports-public', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(this.reportForm)
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    // Show success message
                    alert('Report submitted successfully!');
                    this.closeReportForm();
                    
                    // Add the new report to the map immediately
                    const newReport = result.data;
                    const iconColor = this.getMarkerColor(newReport.urgency);
                    const marker = L.circleMarker([parseFloat(newReport.latitude), parseFloat(newReport.longitude)], {
                        radius: 8,
                        fillColor: iconColor,
                        color: '#fff',
                        weight: 2,
                        opacity: 1,
                        fillOpacity: 0.8
                    }).addTo(this.map);
                    
                    const popupContent = `
                        <div class="p-4 max-w-sm">
                            <h3 class="font-bold text-lg mb-2 text-gray-900">${newReport.title}</h3>
                            <p class="text-sm text-gray-600 mb-3 leading-relaxed">${newReport.description}</p>
                            
                            <div class="flex flex-wrap gap-2 mb-3">
                                <span class="px-2 py-1 rounded-full text-xs font-medium ${this.getUrgencyClass(newReport.urgency)}">
                                    ${this.capitalizeFirst(newReport.urgency)} priority
                                </span>
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                    ${this.formatReportType(newReport.type)}
                                </span>
                            </div>
                            
                            <div class="text-xs text-gray-500 mb-3">
                                <i data-lucide="calendar" class="w-3 h-3 inline mr-1"></i>
                                Just now
                            </div>
                            
                            <div class="flex gap-2">
                                <button class="edit-report-btn flex-1 px-3 py-2 bg-blue-500 text-white text-xs rounded-lg hover:bg-blue-600 transition-colors duration-200 flex items-center justify-center gap-1" data-report-id="${newReport.id}">
                                    <i data-lucide="edit-2" class="w-3 h-3"></i>
                                    Edit
                                </button>
                                <button class="delete-report-btn flex-1 px-3 py-2 bg-red-500 text-white text-xs rounded-lg hover:bg-red-600 transition-colors duration-200 flex items-center justify-center gap-1" data-report-id="${newReport.id}">
                                    <i data-lucide="trash-2" class="w-3 h-3"></i>
                                    Delete
                                </button>
                            </div>
                        </div>
                    `;
                    
                    marker.bindPopup(popupContent, {
                        maxWidth: 300,
                        className: 'custom-popup'
                    });
                    
                    // Add event listeners for new marker
                    marker.on('popupopen', () => {
                        setTimeout(() => {
                            lucide.createIcons();
                            
                            const editBtn = document.querySelector(`.edit-report-btn[data-report-id="${newReport.id}"]`);
                            const deleteBtn = document.querySelector(`.delete-report-btn[data-report-id="${newReport.id}"]`);
                            
                            if (editBtn) {
                                editBtn.addEventListener('click', (e) => {
                                    e.preventDefault();
                                    this.editReport(newReport.id);
                                });
                            }
                            
                            if (deleteBtn) {
                                deleteBtn.addEventListener('click', (e) => {
                                    e.preventDefault();
                                    this.deleteReport(newReport.id);
                                });
                            }
                        }, 100);
                    });
                    
                    this.reports.push(newReport);
                } else {
                    throw new Error(result.message || 'Failed to submit report');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error submitting report: ' + error.message);
            } finally {
                // Reset button state
                const submitBtn = document.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.textContent = 'Submit Report';
                    submitBtn.disabled = false;
                }
            }
        },

        editReport(reportId) {
            const report = this.reports.find(r => r.id == reportId);
            if (report) {
                this.editingReport = report;
                this.reportForm = {
                    title: report.title,
                    description: report.description,
                    type: report.type,
                    urgency: report.urgency,
                    latitude: report.latitude,
                    longitude: report.longitude,
                };
                this.showEditForm = true;
            }
        },

        async updateReport() {
            try {
                const submitBtn = document.querySelector('#edit-form button[type="submit"]');
                const originalText = submitBtn.textContent;
                submitBtn.textContent = 'Updating...';
                submitBtn.disabled = true;

                const response = await fetch(`/api/reports-public/${this.editingReport.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(this.reportForm)
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    alert('Report updated successfully!');
                    this.closeEditForm();
                    // Update the report in the list
                    const index = this.reports.findIndex(r => r.id == this.editingReport.id);
                    if (index !== -1) {
                        this.reports[index] = result.data;
                    }
                    // Reload the map to show updated markers
                    this.reloadMap();
                } else {
                    throw new Error(result.message || 'Failed to update report');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error updating report: ' + error.message);
            } finally {
                const submitBtn = document.querySelector('#edit-form button[type="submit"]');
                if (submitBtn) {
                    submitBtn.textContent = 'Update Report';
                    submitBtn.disabled = false;
                }
            }
        },

        async deleteReport(reportId) {
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
                    // Remove the report from the list
                    this.reports = this.reports.filter(r => r.id != reportId);
                    // Reload the map to remove the marker
                    this.reloadMap();
                } else {
                    throw new Error(result.message || 'Failed to delete report');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error deleting report: ' + error.message);
            }
        },

        closeEditForm() {
            this.showEditForm = false;
            this.editingReport = null;
            this.resetForm();
        },

        reloadMap() {
            // Clear existing markers
            this.map.eachLayer((layer) => {
                if (layer instanceof L.CircleMarker) {
                    this.map.removeLayer(layer);
                }
            });
            // Reload reports
            this.loadReports();
        }
    }
}

// Initialize Lucide icons after Alpine renders
document.addEventListener('alpine:init', () => {
    setTimeout(() => {
        lucide.createIcons();
    }, 100);
});
</script>
@endpush