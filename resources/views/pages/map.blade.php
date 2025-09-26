@extends('layouts.dashboard')

@section('page-title', 'Map')

@section('page-content')
<div class="h-full p-6">
    <div class="relative h-full w-full bg-white rounded-xl shadow-lg overflow-hidden" x-data="mapComponent()">
        <!-- Map Container -->
        <div id="map" class="w-full h-full"></div>

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

        <!-- Simple Working Floating Buttons -->
        <div class="absolute bottom-6 right-6 z-[1000] flex flex-col space-y-3">
            <!-- Add Report Button -->
            <button
                x-on:click="toggleAddReportMode()"
                class="w-16 h-16 rounded-full shadow-2xl flex items-center justify-center text-white transition-all duration-300 border-4 border-white hover:scale-110 transform focus:outline-none focus:ring-4 focus:ring-offset-2"
                :class="addReportMode ? 'bg-red-500 hover:bg-red-600 focus:ring-red-300' : 'bg-green-500 hover:bg-green-600 focus:ring-green-300'"
                id="addReportBtn"
            >
                <i :data-lucide="addReportMode ? 'x' : 'plus'" class="w-6 h-6"></i>
            </button>

            <!-- Search Button -->
            <button
                x-on:click="showSearchDialog = true"
                class="w-14 h-14 rounded-full shadow-xl flex items-center justify-center text-white transition-all duration-300 border-3 border-white hover:scale-110 transform bg-blue-500 hover:bg-blue-600 focus:outline-none focus:ring-4 focus:ring-blue-300 focus:ring-offset-2"
            >
                <i data-lucide="search" class="w-5 h-5"></i>
            </button>
        </div>    <!-- Add Report Mode Instructions -->
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
                                    <i data-lucide="plus-circle" class="w-5 h-5 inline mr-2 text-green-600"></i>
                                    Add New Report
                                </h3>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
                                        <input type="text" x-model="reportForm.title" required
                                               placeholder="Enter report title"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                                        <textarea x-model="reportForm.description" required rows="3"
                                                placeholder="Describe the environmental issue or suggestion"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"></textarea>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Type *</label>
                                            <select x-model="reportForm.type" required
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
                                            <select x-model="reportForm.urgency" required
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                                <option value="">Select urgency</option>
                                                <option value="low">🟢 Low</option>
                                                <option value="medium">🟡 Medium</option>
                                                <option value="high">🔴 High</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                                        <input type="text" x-model="reportForm.address"
                                               placeholder="Optional: Enter full address"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Images 
                                            <span class="text-xs text-gray-500">(Optional, max 5 images, 2MB each)</span>
                                        </label>
                                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-green-400 transition-colors">
                                            <input type="file" x-ref="imageInput" multiple accept="image/*" 
                                                   class="hidden" x-on:change="handleImageUpload($event)">
                                            <div x-show="!selectedImages.length">
                                                <i data-lucide="camera" class="w-12 h-12 mx-auto text-gray-400 mb-4"></i>
                                                <p class="text-sm text-gray-600 mb-2">Click to upload images or drag and drop</p>
                                                <p class="text-xs text-gray-500">PNG, JPG up to 2MB each</p>
                                                <button type="button" x-on:click="$refs.imageInput.click()" 
                                                        class="mt-3 px-4 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors">
                                                    <i data-lucide="plus" class="w-4 h-4 inline mr-1"></i>
                                                    Select Images
                                                </button>
                                            </div>
                                            <div x-show="selectedImages.length > 0" class="mt-4">
                                                <div class="grid grid-cols-2 gap-4" x-ref="previewContainer">
                                                    <template x-for="(image, index) in selectedImages" :key="index">
                                                        <div class="relative">
                                                            <img :src="image.preview" class="w-full h-24 object-cover rounded-lg">
                                                            <button type="button" x-on:click="removeImage(index)" 
                                                                    class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 hover:bg-red-600">
                                                                <i data-lucide="x" class="w-3 h-3"></i>
                                                            </button>
                                                        </div>
                                                    </template>
                                                </div>
                                                <button type="button" x-on:click="$refs.imageInput.click()" 
                                                        class="mt-3 px-4 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors">
                                                    <i data-lucide="plus" class="w-4 h-4 inline mr-1"></i>
                                                    Add More Images
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-center">
                                        <p class="text-xs text-gray-500">* Required fields</p>
                                        <p class="text-sm text-blue-600 mt-1">
                                            <i data-lucide="map-pin" class="w-4 h-4 inline mr-1"></i>
                                            Location: <span x-text="formatCoordinates(reportForm.latitude, reportForm.longitude)"></span>
                                        </p>
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
                        <button type="button" x-on:click="closeReportForm()"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
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
                                    <i data-lucide="edit-2" class="w-5 h-5 inline mr-2 text-blue-600"></i>
                                    Edit Report
                                </h3>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
                                        <input type="text" x-model="reportForm.title" required
                                               placeholder="Enter report title"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                                        <textarea x-model="reportForm.description" required rows="3"
                                                placeholder="Describe the environmental issue or suggestion"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Type *</label>
                                            <select x-model="reportForm.type" required
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
                                            <select x-model="reportForm.urgency" required
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                <option value="">Select urgency</option>
                                                <option value="low">🟢 Low</option>
                                                <option value="medium">🟡 Medium</option>
                                                <option value="high">🔴 High</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                                        <input type="text" x-model="reportForm.address"
                                               placeholder="Optional: Enter full address"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>

                                    <!-- Current Images Display -->
                                    <div x-show="editingReport && editingReport.image_urls && editingReport.image_urls.length > 0">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Current Images</label>
                                        <div class="grid grid-cols-2 gap-4 mb-4">
                                            <template x-for="(imageUrl, index) in editingReport.image_urls" :key="index">
                                                <div class="relative">
                                                    <img :src="imageUrl" class="w-full h-24 object-cover rounded-lg">
                                                    <div class="absolute bottom-1 left-1 bg-black/60 text-white px-1 py-0.5 rounded text-xs">
                                                        Current
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Update Images 
                                            <span class="text-xs text-gray-500">(Optional, will replace current images)</span>
                                        </label>
                                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors">
                                            <input type="file" x-ref="editImageInput" multiple accept="image/*" 
                                                   class="hidden" x-on:change="handleEditImageUpload($event)">
                                            <div x-show="!editSelectedImages.length">
                                                <i data-lucide="camera" class="w-12 h-12 mx-auto text-gray-400 mb-4"></i>
                                                <p class="text-sm text-gray-600 mb-2">Click to upload new images</p>
                                                <p class="text-xs text-gray-500">PNG, JPG up to 2MB each</p>
                                                <button type="button" x-on:click="$refs.editImageInput.click()" 
                                                        class="mt-3 px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors">
                                                    <i data-lucide="plus" class="w-4 h-4 inline mr-1"></i>
                                                    Select Images
                                                </button>
                                            </div>
                                            <div x-show="editSelectedImages.length > 0" class="mt-4">
                                                <div class="grid grid-cols-2 gap-4">
                                                    <template x-for="(image, index) in editSelectedImages" :key="index">
                                                        <div class="relative">
                                                            <img :src="image.preview" class="w-full h-24 object-cover rounded-lg">
                                                            <button type="button" x-on:click="removeEditImage(index)" 
                                                                    class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 hover:bg-red-600">
                                                                <i data-lucide="x" class="w-3 h-3"></i>
                                                            </button>
                                                        </div>
                                                    </template>
                                                </div>
                                                <button type="button" x-on:click="$refs.editImageInput.click()" 
                                                        class="mt-3 px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors">
                                                    <i data-lucide="plus" class="w-4 h-4 inline mr-1"></i>
                                                    Add More Images
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-center">
                                        <p class="text-xs text-gray-500">* Required fields</p>
                                        <p class="text-sm text-blue-600 mt-1">
                                            <i data-lucide="map-pin" class="w-4 h-4 inline mr-1"></i>
                                            Location: <span x-text="formatCoordinates(reportForm.latitude, reportForm.longitude)"></span>
                                        </p>
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
                        <button type="button" x-on:click="closeEditForm()"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
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
        selectedImages: [],
        editSelectedImages: [],
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
            address: ''
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
            
            // Click event listener for adding reports
            this.map.on('click', (e) => {
                if (this.addReportMode) {
                    this.newReportLocation = { lat: e.latlng.lat, lng: e.latlng.lng };
                    this.reportForm.latitude = e.latlng.lat.toFixed(6);
                    this.reportForm.longitude = e.latlng.lng.toFixed(6);
                    this.showReportForm = true;
                    this.addReportMode = false;
                }
            });
        },
        
        toggleAddReportMode() {
            this.addReportMode = !this.addReportMode;
            this.newReportLocation = null;
            if (!this.addReportMode) {
                this.showReportForm = false;
            }
        },
        
        handleImageUpload(event) {
            const files = Array.from(event.target.files);
            const maxFiles = 5;
            const maxSize = 2 * 1024 * 1024; // 2MB
            
            // Check file count
            if (this.selectedImages.length + files.length > maxFiles) {
                alert(`Maximum ${maxFiles} images allowed`);
                return;
            }
            
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
                
                // Create preview
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.selectedImages.push({
                        file: file,
                        preview: e.target.result,
                        name: file.name
                    });
                };
                reader.readAsDataURL(file);
            });
            
            // Clear input
            event.target.value = '';
        },
        
        handleEditImageUpload(event) {
            const files = Array.from(event.target.files);
            const maxFiles = 5;
            const maxSize = 2 * 1024 * 1024; // 2MB
            
            // Check file count
            if (this.editSelectedImages.length + files.length > maxFiles) {
                alert(`Maximum ${maxFiles} images allowed`);
                return;
            }
            
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
                
                // Create preview
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.editSelectedImages.push({
                        file: file,
                        preview: e.target.result,
                        name: file.name
                    });
                };
                reader.readAsDataURL(file);
            });
            
            // Clear input
            event.target.value = '';
        },
        
        removeImage(index) {
            this.selectedImages.splice(index, 1);
        },
        
        removeEditImage(index) {
            this.editSelectedImages.splice(index, 1);
        },
        
        formatCoordinates(lat, lng) {
            if (!lat || !lng) return 'No location selected';
            return `${parseFloat(lat).toFixed(4)}, ${parseFloat(lng).toFixed(4)}`;
        },
        
        loadReports() {
            if (!this.reports || this.reports.length === 0) {
                console.log('No reports to display on map');
                return;
            }

            console.log(`Loading ${this.reports.length} reports on map`);
            
            this.reports.forEach(report => {
                this.addReportToMap(report);
            });
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
                    address: report.address || ''
                };
                this.editSelectedImages = [];
                this.showEditForm = true;
            }
        },

        async updateReport() {
            try {
                const submitBtn = document.querySelector('#edit-form button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i data-lucide="loader" class="w-4 h-4 mr-2 animate-spin"></i>Updating...';
                submitBtn.disabled = true;

                // Create FormData for file upload if there are new images
                let requestData;
                let headers = {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                };

                if (this.editSelectedImages.length > 0) {
                    // Use FormData for file uploads
                    requestData = new FormData();
                    Object.keys(this.reportForm).forEach(key => {
                        if (this.reportForm[key] !== null && this.reportForm[key] !== '') {
                            requestData.append(key, this.reportForm[key]);
                        }
                    });
                    
                    // Add images
                    this.editSelectedImages.forEach((imageObj, index) => {
                        requestData.append('images[]', imageObj.file);
                    });
                } else {
                    // Use JSON for updates without images
                    headers['Content-Type'] = 'application/json';
                    requestData = JSON.stringify(this.reportForm);
                }

                const response = await fetch(`/api/reports-public/${this.editingReport.id}`, {
                    method: 'PUT',
                    headers: headers,
                    body: requestData
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
                    submitBtn.innerHTML = '<i data-lucide="check" class="w-4 h-4 mr-2"></i>Update Report';
                    submitBtn.disabled = false;
                }
                // Re-initialize Lucide icons
                setTimeout(() => lucide.createIcons(), 100);
            }
        },

        closeEditForm() {
            this.showEditForm = false;
            this.editingReport = null;
            this.editSelectedImages = [];
            this.resetForm();
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
            this.selectedImages = [];
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
                address: ''
            };
        },
        
        async submitReport() {
            try {
                // Show loading state
                const submitBtn = document.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i data-lucide="loader" class="w-4 h-4 mr-2 animate-spin"></i>Adding...';
                submitBtn.disabled = true;

                // Create FormData for file upload
                const formData = new FormData();
                
                // Add form fields
                Object.keys(this.reportForm).forEach(key => {
                    if (this.reportForm[key] !== null && this.reportForm[key] !== '') {
                        formData.append(key, this.reportForm[key]);
                    }
                });
                
                // Add images
                this.selectedImages.forEach((imageObj, index) => {
                    formData.append('images[]', imageObj.file);
                });

                const response = await fetch('/api/reports-public', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    // Show success message
                    alert('Report submitted successfully!');
                    this.closeReportForm();
                    
                    // Add the new report to the map immediately
                    const newReport = result.data;
                    this.addReportToMap(newReport);
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
                    submitBtn.innerHTML = '<i data-lucide="plus" class="w-4 h-4 mr-2"></i>Add Report';
                    submitBtn.disabled = false;
                }
                // Re-initialize Lucide icons
                setTimeout(() => lucide.createIcons(), 100);
            }
        },

        addReportToMap(report) {
            if (!report.latitude || !report.longitude) return;

            const iconColor = this.getMarkerColor(report.urgency);
            const marker = L.circleMarker([parseFloat(report.latitude), parseFloat(report.longitude)], {
                radius: 8,
                fillColor: iconColor,
                color: '#fff',
                weight: 2,
                opacity: 1,
                fillOpacity: 0.8
            }).addTo(this.map);
            
            const popupContent = this.createPopupContent(report);
            marker.bindPopup(popupContent, {
                maxWidth: 300,
                className: 'custom-popup'
            });

            // Add event listeners when popup opens
            marker.on('popupopen', () => {
                setTimeout(() => {
                    lucide.createIcons();
                    this.setupPopupButtons(report.id);
                }, 100);
            });
        },

        createPopupContent(report) {
            // Handle images display
            let imageSection = '';
            if (report.image_urls && report.image_urls.length > 0) {
                const firstImage = report.image_urls[0];
                const imageCount = report.image_urls.length > 1 ? `<div class="absolute top-1 right-1 bg-black/60 text-white px-1 py-0.5 rounded text-xs">+${report.image_urls.length}</div>` : '';
                imageSection = `
                    <div class="relative mb-3">
                        <img src="${firstImage}" class="w-full h-32 object-cover rounded-lg" alt="${report.title}">
                        ${imageCount}
                    </div>
                `;
            }

            return `
                <div class="p-4 max-w-sm">
                    ${imageSection}
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
        },

        setupPopupButtons(reportId) {
            const editBtn = document.querySelector(`.edit-report-btn[data-report-id="${reportId}"]`);
            const deleteBtn = document.querySelector(`.delete-report-btn[data-report-id="${reportId}"]`);
            
            if (editBtn) {
                editBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.editReport(reportId);
                });
            }
            
            if (deleteBtn) {
                deleteBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.deleteReport(reportId);
                });
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