@extends('layouts.dashboard')

@section('title', 'Tree Management - Sylva')

@section('page-content')
<div class="space-y-6" x-data="treeManagement()">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Tree Management</h1>
            <p class="text-gray-600">Manage and track trees in your urban greening initiative</p>
        </div>
        <button 
            @click="openAddModal()" 
            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-emerald-600 to-green-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200"
        >
            <i data-lucide="plus" class="w-5 h-5 mr-2"></i>
            Add New Tree
        </button>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 bg-blue-50 rounded-xl">
                    <i data-lucide="trees" class="w-6 h-6 text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Trees</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $statistics['total_trees'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 bg-green-50 rounded-xl">
                    <i data-lucide="check-circle" class="w-6 h-6 text-green-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Planted</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $statistics['planted_trees'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-50 rounded-xl">
                    <i data-lucide="clock" class="w-6 h-6 text-yellow-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Not Yet Planted</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $statistics['not_yet_planted'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 bg-orange-50 rounded-xl">
                    <i data-lucide="alert-triangle" class="w-6 h-6 text-orange-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Sick</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $statistics['sick_trees'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 bg-red-50 rounded-xl">
                    <i data-lucide="x-circle" class="w-6 h-6 text-red-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Dead</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $statistics['dead_trees'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 bg-emerald-50 rounded-xl">
                    <i data-lucide="user" class="w-6 h-6 text-emerald-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">My Trees</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $statistics['my_trees'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-64">
                <div class="relative">
                    <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400"></i>
                    <input 
                        type="text" 
                        x-model="filters.search"
                        @input="applyFilters()"
                        placeholder="Search trees..."
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                    >
                </div>
            </div>
            
            <select x-model="filters.status" @change="applyFilters()" class="px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                <option value="">All Status</option>
                <option value="Planted">Planted</option>
                <option value="Not Yet">Not Yet Planted</option>
                <option value="Sick">Sick</option>
                <option value="Dead">Dead</option>
            </select>

            <select x-model="filters.type" @change="applyFilters()" class="px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                <option value="">All Types</option>
                <option value="Fruit">Fruit</option>
                <option value="Ornamental">Ornamental</option>
                <option value="Forest">Forest</option>
                <option value="Medicinal">Medicinal</option>
            </select>

            <button 
                @click="showMyTrees = !showMyTrees; applyFilters()"
                :class="showMyTrees ? 'bg-emerald-100 text-emerald-700 border-emerald-300' : 'bg-gray-100 text-gray-700 border-gray-300'"
                class="px-4 py-2 border rounded-xl font-medium transition-colors"
            >
                My Trees Only
            </button>
        </div>
    </div>

    <!-- Trees Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" id="trees-grid">
        @foreach($trees as $tree)
        <div class="tree-card bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow duration-200" 
             data-tree-id="{{ $tree->id }}"
             data-species="{{ strtolower($tree->species) }}"
             data-status="{{ $tree->status }}"
             data-type="{{ $tree->type }}"
             data-user-id="{{ $tree->planted_by_user }}">
            
            <!-- Tree Image -->
            <div class="relative h-48 bg-gradient-to-br from-emerald-100 to-green-100">
                @if($tree->image_urls && count($tree->image_urls) > 0)
                    <img src="{{ $tree->image_urls[0] }}" alt="{{ $tree->species }}" class="w-full h-full object-cover">
                @else
                    <div class="flex items-center justify-center h-full">
                        <span class="text-6xl">{{ $tree->type_icon }}</span>
                    </div>
                @endif
                
                <!-- Status Badge -->
                <div class="absolute top-3 left-3">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                        {{ $tree->status === 'Planted' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $tree->status === 'Not Yet' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $tree->status === 'Sick' ? 'bg-orange-100 text-orange-800' : '' }}
                        {{ $tree->status === 'Dead' ? 'bg-red-100 text-red-800' : '' }}">
                        {{ $tree->status }}
                    </span>
                </div>

                <!-- Action Menu -->
                <div class="absolute top-3 right-3">
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="p-2 bg-white bg-opacity-80 rounded-full hover:bg-opacity-100 transition-all">
                            <i data-lucide="more-horizontal" class="w-4 h-4 text-gray-600"></i>
                        </button>
                        
                        <div x-show="open" @click.outside="open = false" x-transition 
                             class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-10">
                            <button @click="viewTree({{ $tree->id }}); open = false" class="w-full text-left px-4 py-2 hover:bg-gray-50 flex items-center">
                                <i data-lucide="eye" class="w-4 h-4 mr-3 text-gray-500"></i>
                                View Details
                            </button>
                            @if($tree->planted_by_user === Auth::id() || Auth::user()->is_admin)
                            <button @click="editTree({{ $tree->id }}); open = false" class="w-full text-left px-4 py-2 hover:bg-gray-50 flex items-center">
                                <i data-lucide="edit" class="w-4 h-4 mr-3 text-gray-500"></i>
                                Edit Tree
                            </button>
                            <button @click="deleteTree({{ $tree->id }}); open = false" class="w-full text-left px-4 py-2 hover:bg-gray-50 flex items-center text-red-600">
                                <i data-lucide="trash-2" class="w-4 h-4 mr-3"></i>
                                Delete Tree
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tree Details -->
            <div class="p-4">
                <div class="flex items-start justify-between mb-2">
                    <h3 class="font-semibold text-gray-900 text-lg">{{ $tree->species }}</h3>
                    <span class="text-2xl">{{ $tree->type_icon }}</span>
                </div>
                
                <div class="space-y-2 text-sm text-gray-600">
                    <div class="flex items-center">
                        <i data-lucide="map-pin" class="w-4 h-4 mr-2"></i>
                        <span>{{ $tree->address ?: 'Location: ' . number_format($tree->latitude, 6) . ', ' . number_format($tree->longitude, 6) }}</span>
                    </div>
                    
                    <div class="flex items-center">
                        <i data-lucide="calendar" class="w-4 h-4 mr-2"></i>
                        <span>{{ $tree->planting_date_formatted }}</span>
                    </div>
                    
                    <div class="flex items-center">
                        <i data-lucide="user" class="w-4 h-4 mr-2"></i>
                        <span>{{ $tree->plantedBy->name }}</span>
                    </div>

                    @if($tree->description)
                    <p class="text-gray-600 text-sm mt-2 line-clamp-2">{{ $tree->description }}</p>
                    @endif
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-2 mt-4">
                    <button 
                        @click="viewTree({{ $tree->id }})"
                        class="flex-1 px-3 py-2 bg-emerald-50 text-emerald-700 rounded-xl font-medium hover:bg-emerald-100 transition-colors text-sm"
                    >
                        View Details
                    </button>
                    @if($tree->planted_by_user === Auth::id() || Auth::user()->is_admin)
                    <button 
                        @click="editTree({{ $tree->id }})"
                        class="px-3 py-2 bg-blue-50 text-blue-700 rounded-xl font-medium hover:bg-blue-100 transition-colors"
                    >
                        <i data-lucide="edit" class="w-4 h-4"></i>
                    </button>
                    @endif
                </div>
                
                <!-- Tree Care Button -->
                <button 
                    @click="openCareModal({{ $tree->id }})"
                    class="w-full mt-2 px-3 py-2 bg-gradient-to-r from-emerald-500 to-green-600 text-white rounded-xl font-medium hover:shadow-lg transform hover:scale-105 transition-all duration-200 flex items-center justify-center gap-2 text-sm"
                >
                    <i data-lucide="heart" class="w-4 h-4"></i>
                    Tree Care
                </button>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="flex justify-center">
        {{ $trees->links() }}
    </div>

    <!-- Add/Edit Tree Modal -->
    <div x-show="showModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div x-show="showModal" @click.outside="closeModal()" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="bg-white rounded-2xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-900" x-text="editingTree ? 'Edit Tree' : 'Add New Tree'"></h2>
                    <button @click="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <i data-lucide="x" class="w-6 h-6"></i>
                    </button>
                </div>
            </div>

            <form @submit.prevent="submitForm()" class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Species -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Species *</label>
                        <input 
                            type="text" 
                            x-model="form.species"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                            placeholder="e.g., Oak, Maple, Pine"
                        >
                    </div>

                    <!-- Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Type *</label>
                        <select x-model="form.type" required class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                            <option value="">Select Type</option>
                            <option value="Fruit">🍎 Fruit</option>
                            <option value="Ornamental">🌸 Ornamental</option>
                            <option value="Forest">🌲 Forest</option>
                            <option value="Medicinal">🌿 Medicinal</option>
                        </select>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                        <select x-model="form.status" required class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                            <option value="Not Yet">Not Yet Planted</option>
                            <option value="Planted">Planted</option>
                            <option value="Sick">Sick</option>
                            <option value="Dead">Dead</option>
                        </select>
                    </div>

                    <!-- Planting Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Planting Date *</label>
                        <input 
                            type="date" 
                            x-model="form.planting_date"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                        >
                    </div>

                    <!-- Location Map Picker -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Location *</label>
                        <div class="space-y-2">
                            <div class="relative">
                                <div class="h-64 border border-gray-300 rounded-xl overflow-hidden" id="location-picker-map"></div>
                                <div x-show="!form.latitude || !form.longitude" class="absolute inset-0 flex items-center justify-center bg-gray-50 bg-opacity-90 rounded-xl pointer-events-none">
                                    <div class="text-center">
                                        <i data-lucide="map-pin" class="w-8 h-8 text-gray-400 mx-auto mb-2"></i>
                                        <p class="text-sm text-gray-500 font-medium">Click on the map to select location</p>
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Latitude</label>
                                    <input 
                                        type="number" 
                                        x-model="form.latitude"
                                        step="any"
                                        min="-90"
                                        max="90"
                                        required
                                        readonly
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-sm"
                                        placeholder="Click on map"
                                    >
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Longitude</label>
                                    <input 
                                        type="number" 
                                        x-model="form.longitude"
                                        step="any"
                                        min="-180"
                                        max="180"
                                        required
                                        readonly
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-sm"
                                        placeholder="Click on map"
                                    >
                                </div>
                            </div>
                            <p class="text-xs text-emerald-600" x-show="form.latitude && form.longitude">
                                <i data-lucide="map-pin" class="w-3 h-3 inline mr-1"></i>
                                Location selected: <span x-text="formatCoordinates(form.latitude, form.longitude)"></span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Address -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                    <input 
                        type="text" 
                        x-model="form.address"
                        class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                        placeholder="e.g., Central Park, New York"
                    >
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea 
                        x-model="form.description"
                        rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                        placeholder="Additional notes about this tree..."
                    ></textarea>
                </div>

                <!-- Images -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Images
                        <span class="text-emerald-600 text-xs ml-2">🤖 AI will identify the plant automatically</span>
                    </label>
                    <input 
                        type="file" 
                        @change="handleImageUpload($event)"
                        multiple
                        accept="image/*"
                        id="tree-image-input"
                        class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                    >
                    <p class="text-sm text-gray-500 mt-1">Upload a clear photo of the tree. AI will identify the species! 📸</p>
                    
                    <!-- AI Identification Loading -->
                    <div x-show="identifyingPlant" class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-xl">
                        <div class="flex items-center">
                            <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-blue-600 mr-3"></div>
                            <span class="text-sm text-blue-700 font-medium">🤖 AI is identifying the plant...</span>
                        </div>
                    </div>

                    <!-- AI Identification Result -->
                    <div x-show="plantIdentification && plantIdentification.success && plantIdentification.data" class="mt-4 p-4 bg-gradient-to-r from-emerald-50 to-green-50 border border-emerald-200 rounded-xl">
                        <div class="flex items-start">
                            <div class="p-2 bg-emerald-100 rounded-lg mr-3">
                                <i data-lucide="sparkles" class="w-5 h-5 text-emerald-600"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-bold text-emerald-900 mb-1">✨ Plant Identified!</h4>
                                <p class="text-sm text-emerald-800 mb-2">
                                    <strong x-text="plantIdentification.data?.name || 'Unknown'"></strong>
                                    <span class="text-emerald-600 italic ml-1" x-show="plantIdentification.data?.scientific_name">
                                        (<span x-text="plantIdentification.data?.scientific_name"></span>)
                                    </span>
                                </p>
                                
                                <!-- Suggested Type -->
                                <div x-show="plantIdentification.data?.suggested_type" class="mb-2 text-xs text-emerald-700">
                                    <span class="font-medium">Suggested Type:</span>
                                    <span class="ml-1 px-2 py-0.5 bg-emerald-100 rounded-full" x-text="plantIdentification.data?.suggested_type"></span>
                                </div>
                                
                                <div class="flex items-center text-xs text-emerald-700 mb-3">
                                    <span class="font-medium">Confidence:</span>
                                    <span class="ml-1" x-text="(plantIdentification.data?.confidence || 0) + '%'"></span>
                                    <div class="ml-2 flex-1 max-w-32 h-2 bg-emerald-200 rounded-full overflow-hidden">
                                        <div class="h-full bg-emerald-500 rounded-full transition-all" :style="'width: ' + (plantIdentification.data?.confidence || 0) + '%'"></div>
                                    </div>
                                </div>
                                
                                <!-- Auto-fill Button -->
                                <button 
                                    type="button"
                                    @click="autoFillFromAI()"
                                    class="w-full mb-2 px-3 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors flex items-center justify-center"
                                >
                                    <i data-lucide="wand-2" class="w-4 h-4 mr-2"></i>
                                    Auto-fill Species, Type & Description
                                </button>
                                
                                <div x-show="plantIdentification.data?.common_names && plantIdentification.data.common_names.length > 1" class="mt-2 text-xs text-emerald-600">
                                    <span class="font-medium">Other names:</span>
                                    <span x-text="plantIdentification.data?.common_names?.slice(1).join(', ')"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- AI Identification Error -->
                    <div x-show="plantIdentification && !plantIdentification.success && plantIdentification.message" class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-xl">
                        <div class="flex items-start">
                            <i data-lucide="alert-circle" class="w-5 h-5 text-yellow-600 mr-2"></i>
                            <div>
                                <p class="text-sm text-yellow-800 font-medium">Could not identify plant</p>
                                <p class="text-xs text-yellow-700 mt-1" x-text="plantIdentification.message || 'Plant identification service is not available'"></p>
                                <p class="text-xs text-yellow-600 mt-1">You can still enter the species name manually.</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Current Images (for edit mode) -->
                    <div x-show="currentImages.length > 0" class="mt-4">
                        <p class="text-sm font-medium text-gray-700 mb-2">Current Images:</p>
                        <div class="grid grid-cols-3 gap-2">
                            <template x-for="(image, index) in currentImages" :key="index">
                                <div class="relative">
                                    <img :src="image" class="w-full h-20 object-cover rounded-lg border border-gray-200">
                                    <button 
                                        type="button"
                                        @click="removeImage(index)"
                                        class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600"
                                    >
                                        <i data-lucide="x" class="w-3 h-3"></i>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex gap-4 pt-6 border-t border-gray-200">
                    <button 
                        type="button" 
                        @click="closeModal()"
                        class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition-colors"
                    >
                        Cancel
                    </button>
                    <button 
                        type="submit"
                        :disabled="isSubmitting"
                        class="flex-1 px-6 py-3 bg-gradient-to-r from-emerald-600 to-green-600 text-white rounded-xl font-medium hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                        :class="isSubmitting && 'opacity-50 cursor-not-allowed'"
                    >
                        <span x-show="!isSubmitting" x-text="editingTree ? 'Update Tree' : 'Add Tree'"></span>
                        <span x-show="isSubmitting">Processing...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function treeManagement() {
    return {
        showModal: false,
        editingTree: null,
        isSubmitting: false,
        locationPickerMap: null,
        locationMarker: null,
        filters: {
            search: '',
            status: '',
            type: ''
        },
        showMyTrees: false,
        currentImages: [],
        imagesToDelete: [],
        identifyingPlant: false,
        plantIdentification: {
            success: false,
            data: null,
            message: ''
        },
        form: {
            species: '',
            type: '',
            status: 'Not Yet',
            planting_date: '',
            latitude: '',
            longitude: '',
            address: '',
            description: '',
            images: []
        },

        init() {
            // Set default date to today
            this.form.planting_date = new Date().toISOString().split('T')[0];
        },

        openAddModal() {
            this.editingTree = null;
            this.resetForm();
            this.showModal = true;
            // Initialize map after modal is shown
            this.$nextTick(() => {
                setTimeout(() => this.initLocationPickerMap(), 300);
            });
        },

        async editTree(treeId) {
            try {
                const response = await fetch(`/trees/${treeId}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                if (response.ok) {
                    const result = await response.json();
                    const tree = result.data;
                    
                    this.editingTree = tree;
                    this.form = {
                        species: tree.species,
                        type: tree.type,
                        status: tree.status,
                        planting_date: tree.planting_date,
                        latitude: tree.latitude,
                        longitude: tree.longitude,
                        address: tree.address || '',
                        description: tree.description || '',
                        images: []
                    };
                    this.currentImages = tree.image_urls || [];
                    this.imagesToDelete = [];
                    this.showModal = true;
                    // Initialize map after modal is shown
                    this.$nextTick(() => {
                        setTimeout(() => this.initLocationPickerMap(), 300);
                    });
                } else {
                    alert('Error loading tree data');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error loading tree data');
            }
        },

        async viewTree(treeId) {
            window.location.href = `/trees/${treeId}`;
        },

        openCareModal(treeId) {
            // Redirect to tree detail page with care section
            window.location.href = `/trees/${treeId}#care`;
        },

        async deleteTree(treeId) {
            if (!confirm('Are you sure you want to delete this tree? This action cannot be undone.')) {
                return;
            }

            try {
                const response = await fetch(`/trees/${treeId}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                if (response.ok) {
                    // Remove tree card from the page
                    const treeCard = document.querySelector(`[data-tree-id="${treeId}"]`);
                    if (treeCard) {
                        treeCard.remove();
                    }
                    alert('Tree deleted successfully!');
                } else {
                    const result = await response.json();
                    alert(result.message || 'Error deleting tree');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error deleting tree');
            }
        },

        async submitForm() {
            this.isSubmitting = true;
            
            try {
                const formData = new FormData();
                
                // Add form fields
                Object.keys(this.form).forEach(key => {
                    if (key !== 'images' && this.form[key] !== null && this.form[key] !== '') {
                        formData.append(key, this.form[key]);
                    }
                });

                // Add image files
                if (this.form.images && this.form.images.length > 0) {
                    this.form.images.forEach(image => {
                        formData.append('images[]', image);
                    });
                }

                // Add images to delete (for edit mode)
                if (this.editingTree && this.imagesToDelete.length > 0) {
                    formData.append('images_to_delete', JSON.stringify(this.imagesToDelete));
                }

                const url = this.editingTree ? `/trees/${this.editingTree.id}` : '/trees';
                const method = this.editingTree ? 'POST' : 'POST';
                
                // Add method override for PUT request
                if (this.editingTree) {
                    formData.append('_method', 'PUT');
                }

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });
                
                if (response.ok) {
                    window.location.reload();
                } else {
                    const result = await response.json();
                    if (result.errors) {
                        let errorMessage = 'Validation errors:\n';
                        Object.values(result.errors).forEach(errors => {
                            errors.forEach(error => {
                                errorMessage += '• ' + error + '\n';
                            });
                        });
                        alert(errorMessage);
                    } else {
                        alert(result.message || 'Error saving tree');
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error saving tree');
            } finally {
                this.isSubmitting = false;
            }
        },

        async handleImageUpload(event) {
            this.form.images = Array.from(event.target.files);
            
            // If we have at least one image, try to identify the plant
            if (this.form.images.length > 0) {
                await this.identifyPlantFromImage(this.form.images[0]);
            }
        },

        async identifyPlantFromImage(imageFile) {
            this.identifyingPlant = true;
            this.plantIdentification = {
                success: false,
                data: null,
                message: 'Identifying plant...'
            };

            try {
                const formData = new FormData();
                formData.append('image', imageFile);

                const response = await fetch('/api/identify-plant', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Authorization': 'Bearer ' + document.querySelector('meta[name="api-token"]')?.content
                    },
                    body: formData
                });

                const result = await response.json();
                this.plantIdentification = result;

                if (result.success && result.data) {
                    // Show success message
                    this.showNotification('Plant identified: ' + result.data.name, 'success');
                    
                    // Auto-fill species name if empty
                    if (!this.form.species) {
                        this.form.species = result.data.name;
                    }
                    
                    // Auto-fill type if empty and we have a suggestion
                    if (!this.form.type && result.data.suggested_type) {
                        this.form.type = result.data.suggested_type;
                    }
                    
                    // Auto-fill description if empty and we have AI description
                    if (!this.form.description && result.data.description) {
                        // Truncate description to reasonable length
                        let desc = result.data.description;
                        if (desc.length > 300) {
                            desc = desc.substring(0, 297) + '...';
                        }
                        this.form.description = desc;
                    }
                    
                    this.showNotification('Species, type, and description auto-filled!', 'success');
                } else {
                    // Set error state with proper structure
                    this.plantIdentification = {
                        success: false,
                        data: null,
                        message: result.message || 'Could not identify the plant. Please try again or enter details manually.'
                    };
                }
            } catch (error) {
                console.error('Error identifying plant:', error);
                this.plantIdentification = {
                    success: false,
                    data: null,
                    message: 'Plant identification service is currently unavailable. You can still enter the species manually.'
                };
            } finally {
                this.identifyingPlant = false;
            }
        },

        autoFillFromAI() {
            if (!this.plantIdentification || !this.plantIdentification.success || !this.plantIdentification.data) {
                alert('No plant identification data available.');
                return;
            }

            const data = this.plantIdentification.data;
            
            // Fill species name
            if (data.name) {
                this.form.species = data.name;
            }
            
            // Fill type if we have a suggestion
            if (data.suggested_type) {
                this.form.type = data.suggested_type;
            }
            
            // Fill description if available
            if (data.description) {
                let desc = data.description;
                // Truncate to 300 characters if too long
                if (desc.length > 300) {
                    desc = desc.substring(0, 297) + '...';
                }
                this.form.description = desc;
            }
            
            this.showNotification('✨ Auto-filled: Species, Type, and Description!', 'success');
            
            // Show a visual confirmation
            alert('✅ Auto-filled successfully!\n\n' +
                  '• Species: ' + (data.name || 'N/A') + '\n' +
                  '• Type: ' + (data.suggested_type || 'N/A') + '\n' +
                  '• Description: ' + (data.description ? 'Added' : 'N/A'));
        },

        showNotification(message, type = 'info') {
            // Simple notification - you can enhance this
            console.log(`[${type.toUpperCase()}] ${message}`);
            // You could add a toast notification library here
        },

        removeImage(index) {
            const imageUrl = this.currentImages[index];
            this.imagesToDelete.push(imageUrl);
            this.currentImages.splice(index, 1);
        },

        closeModal() {
            this.showModal = false;
            this.editingTree = null;
            this.resetForm();
            // Clean up map
            if (this.locationPickerMap) {
                this.locationPickerMap.remove();
                this.locationPickerMap = null;
                this.locationMarker = null;
            }
        },

        resetForm() {
            this.form = {
                species: '',
                type: '',
                status: 'Not Yet',
                planting_date: new Date().toISOString().split('T')[0],
                latitude: '',
                longitude: '',
                address: '',
                description: '',
                images: []
            };
            this.currentImages = [];
            this.imagesToDelete = [];
            this.plantIdentification = {
                success: false,
                data: null,
                message: ''
            };
            this.identifyingPlant = false;
        },

        applyFilters() {
            const cards = document.querySelectorAll('.tree-card');
            const currentUserId = {{ Auth::id() }};

            cards.forEach(card => {
                let visible = true;

                // Search filter
                if (this.filters.search) {
                    const species = card.dataset.species || '';
                    if (!species.includes(this.filters.search.toLowerCase())) {
                        visible = false;
                    }
                }

                // Status filter
                if (this.filters.status && card.dataset.status !== this.filters.status) {
                    visible = false;
                }

                // Type filter
                if (this.filters.type && card.dataset.type !== this.filters.type) {
                    visible = false;
                }

                // My trees filter
                if (this.showMyTrees && parseInt(card.dataset.userId) !== currentUserId) {
                    visible = false;
                }

                card.style.display = visible ? 'block' : 'none';
            });
        },

        initLocationPickerMap() {
            if (this.locationPickerMap) {
                this.locationPickerMap.remove();
            }

            // Initialize map centered on Tunisia (or use current location if available)
            const defaultLat = this.form.latitude || 33.8869;
            const defaultLng = this.form.longitude || 9.5375;
            
            this.locationPickerMap = L.map('location-picker-map').setView([defaultLat, defaultLng], 10);

            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>'
            }).addTo(this.locationPickerMap);

            // Add existing location marker if editing
            if (this.form.latitude && this.form.longitude) {
                this.addLocationMarker(this.form.latitude, this.form.longitude);
            }

            // Handle map clicks
            this.locationPickerMap.on('click', (e) => {
                this.setLocation(e.latlng.lat, e.latlng.lng);
            });

            // Invalidate size after modal is fully shown
            setTimeout(() => {
                this.locationPickerMap.invalidateSize();
            }, 100);
        },

        setLocation(lat, lng) {
            this.form.latitude = lat.toFixed(6);
            this.form.longitude = lng.toFixed(6);
            this.addLocationMarker(lat, lng);
            
            // Try to get address from coordinates
            this.reverseGeocode(lat, lng);
        },

        addLocationMarker(lat, lng) {
            if (this.locationMarker) {
                this.locationPickerMap.removeLayer(this.locationMarker);
            }

            this.locationMarker = L.marker([lat, lng], {
                icon: L.divIcon({
                    className: 'location-marker',
                    html: '<div class="location-marker"></div>',
                    iconSize: [20, 20],
                    iconAnchor: [10, 10]
                })
            }).addTo(this.locationPickerMap);

            // Center map on marker
            this.locationPickerMap.setView([lat, lng], this.locationPickerMap.getZoom());
        },

        async reverseGeocode(lat, lng) {
            try {
                const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&addressdetails=1`);
                const data = await response.json();
                
                if (data && data.display_name) {
                    this.form.address = data.display_name;
                }
            } catch (error) {
                console.error('Error getting address:', error);
            }
        },

        formatCoordinates(lat, lng) {
            if (!lat || !lng) return 'No location selected';
            return `${parseFloat(lat).toFixed(4)}, ${parseFloat(lng).toFixed(4)}`;
        }
    }
}
</script>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

#location-picker-map {
    z-index: 1;
}

.location-marker {
    background: #10b981;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    border: 3px solid white;
    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
    cursor: pointer;
}
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endpush