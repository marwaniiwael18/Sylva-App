@extends('layouts.dashboard')

@section('title', 'Tree Details - ' . $tree->species . ' - Sylva')

@section('page-content')
<div class="space-y-6" x-data="treeDetails({{ $tree->toJson() }})">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('trees.index') }}" class="p-2 bg-white rounded-xl shadow-sm border border-gray-200 hover:bg-gray-50 transition-colors">
                <i data-lucide="arrow-left" class="w-5 h-5 text-gray-600"></i>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                    {{ $tree->species }}
                    <span class="text-4xl">{{ $tree->type_icon }}</span>
                </h1>
                <p class="text-gray-600">Tree ID: #{{ $tree->id }}</p>
            </div>
        </div>
        
        @if($tree->planted_by_user === Auth::id() || Auth::user()->is_admin)
        <div class="flex gap-3">
            <button @click="editTree()" class="px-6 py-3 bg-blue-50 text-blue-700 rounded-xl font-medium hover:bg-blue-100 transition-colors flex items-center gap-2">
                <i data-lucide="edit" class="w-5 h-5"></i>
                Edit Tree
            </button>
            <button @click="deleteTree()" class="px-6 py-3 bg-red-50 text-red-700 rounded-xl font-medium hover:bg-red-100 transition-colors flex items-center gap-2">
                <i data-lucide="trash-2" class="w-5 h-5"></i>
                Delete Tree
            </button>
        </div>
        @endif
    </div>

    <!-- Status and Type Badges -->
    <div class="flex gap-3">
        <span class="px-4 py-2 rounded-xl font-semibold 
            {{ $tree->status === 'Planted' ? 'bg-green-100 text-green-800' : '' }}
            {{ $tree->status === 'Not Yet' ? 'bg-yellow-100 text-yellow-800' : '' }}
            {{ $tree->status === 'Sick' ? 'bg-orange-100 text-orange-800' : '' }}
            {{ $tree->status === 'Dead' ? 'bg-red-100 text-red-800' : '' }}">
            <i data-lucide="circle" class="w-4 h-4 inline mr-2"></i>
            {{ $tree->status }}
        </span>
        
        <span class="px-4 py-2 bg-emerald-100 text-emerald-800 rounded-xl font-semibold">
            {{ $tree->type }} Tree
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Images Gallery -->
            @if($tree->image_urls && count($tree->image_urls) > 0)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                        <i data-lucide="camera" class="w-5 h-5"></i>
                        Tree Photos
                    </h2>
                </div>
                
                <div class="p-6" x-data="{ activeImage: 0 }">
                    <!-- Main Image -->
                    <div class="relative mb-4">
                        <img :src="images[activeImage]" :alt="tree.species" class="w-full h-96 object-cover rounded-xl">
                        
                        <!-- Navigation Arrows -->
                        <template x-if="images.length > 1">
                            <div>
                                <button @click="activeImage = (activeImage - 1 + images.length) % images.length" 
                                        class="absolute left-4 top-1/2 transform -translate-y-1/2 p-2 bg-black bg-opacity-50 text-white rounded-full hover:bg-opacity-70">
                                    <i data-lucide="chevron-left" class="w-5 h-5"></i>
                                </button>
                                <button @click="activeImage = (activeImage + 1) % images.length"
                                        class="absolute right-4 top-1/2 transform -translate-y-1/2 p-2 bg-black bg-opacity-50 text-white rounded-full hover:bg-opacity-70">
                                    <i data-lucide="chevron-right" class="w-5 h-5"></i>
                                </button>
                            </div>
                        </template>
                    </div>

                    <!-- Thumbnail Gallery -->
                    <template x-if="images.length > 1">
                        <div class="grid grid-cols-4 gap-2">
                            <template x-for="(image, index) in images" :key="index">
                                <img :src="image" 
                                     @click="activeImage = index"
                                     :class="activeImage === index ? 'border-2 border-emerald-500' : 'border border-gray-200'"
                                     class="w-full h-20 object-cover rounded-lg cursor-pointer hover:opacity-80 transition-opacity">
                            </template>
                        </div>
                    </template>
                </div>
            </div>
            @endif

            <!-- Tree Information -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                        <i data-lucide="info" class="w-5 h-5"></i>
                        Tree Information
                    </h2>
                </div>
                
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Species</label>
                                <p class="text-lg font-semibold text-gray-900">{{ $tree->species }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Type</label>
                                <p class="text-lg text-gray-900 flex items-center gap-2">
                                    <span class="text-2xl">{{ $tree->type_icon }}</span>
                                    {{ $tree->type }}
                                </p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                                <p class="text-lg text-gray-900">{{ $tree->status }}</p>
                            </div>
                        </div>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Planting Date</label>
                                <p class="text-lg text-gray-900">{{ $tree->planting_date_formatted }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Planted By</label>
                                <p class="text-lg text-gray-900">{{ $tree->plantedBy->name }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Date Added</label>
                                <p class="text-lg text-gray-900">{{ $tree->created_at->format('M j, Y') }}</p>
                            </div>
                        </div>
                    </div>

                    @if($tree->description)
                    <div class="pt-4 border-t border-gray-100">
                        <label class="block text-sm font-medium text-gray-500 mb-2">Description</label>
                        <p class="text-gray-900">{{ $tree->description }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Location Map -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                        <i data-lucide="map-pin" class="w-5 h-5"></i>
                        Location
                    </h2>
                </div>
                
                <div class="p-6">
                    <div class="space-y-4">
                        @if($tree->address)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Address</label>
                            <p class="text-gray-900">{{ $tree->address }}</p>
                        </div>
                        @endif
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Latitude</label>
                                <p class="text-gray-900 font-mono">{{ $tree->latitude }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Longitude</label>
                                <p class="text-gray-900 font-mono">{{ $tree->longitude }}</p>
                            </div>
                        </div>
                        
                        <!-- Simple Map Placeholder -->
                        <div class="h-64 bg-gray-100 rounded-xl flex items-center justify-center border border-gray-200" id="tree-map">
                            <div class="text-center">
                                <i data-lucide="map" class="w-12 h-12 text-gray-400 mx-auto mb-2"></i>
                                <p class="text-gray-500 font-medium">Map View</p>
                                <p class="text-sm text-gray-400">{{ $tree->latitude }}, {{ $tree->longitude }}</p>
                                <a href="https://maps.google.com/?q={{ $tree->latitude }},{{ $tree->longitude }}" 
                                   target="_blank" 
                                   class="inline-flex items-center mt-2 px-4 py-2 bg-emerald-100 text-emerald-700 rounded-lg hover:bg-emerald-200 transition-colors">
                                    <i data-lucide="external-link" class="w-4 h-4 mr-2"></i>
                                    Open in Google Maps
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tree Care Section -->
            <div id="care" class="bg-white rounded-2xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                            <i data-lucide="heart" class="w-5 h-5"></i>
                            Tree Care History
                        </h2>
                        <button 
                            @click="openAddCareModal()"
                            class="px-4 py-2 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl font-medium hover:shadow-lg transform hover:scale-105 transition-all duration-200 flex items-center gap-2 text-sm"
                        >
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            Add Care
                        </button>
                    </div>
                </div>
                
                <div class="p-6">
                    <!-- Care Loading State -->
                    <div x-show="careLoading" class="text-center py-8">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-emerald-500 border-t-transparent"></div>
                        <p class="text-gray-600 mt-2">Loading care records...</p>
                    </div>

                    <!-- Empty State -->
                    <div x-show="!careLoading && careRecords.length === 0" class="text-center py-12">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="heart" class="w-8 h-8 text-gray-400"></i>
                        </div>
                        <p class="text-gray-600 font-medium">No care records yet</p>
                        <p class="text-sm text-gray-500 mt-1">Start tracking this tree's care activities</p>
                        <button 
                            @click="openAddCareModal()"
                            class="mt-4 px-6 py-2 bg-emerald-100 text-emerald-700 rounded-xl font-medium hover:bg-emerald-200 transition-colors"
                        >
                            Add First Care
                        </button>
                    </div>

                    <!-- Care Timeline -->
                    <div x-show="!careLoading && careRecords.length > 0" class="space-y-4">
                        <template x-for="(record, index) in careRecords" :key="record.id">
                            <div class="border border-gray-200 rounded-xl p-4 hover:shadow-md transition-shadow">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 rounded-full flex items-center justify-center text-2xl"
                                             :class="getActivityColorClass(record.activity_type)">
                                            <span x-text="getActivityIcon(record.activity_type)"></span>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-900" x-text="record.activity_type_name"></h4>
                                            <p class="text-sm text-gray-500" x-text="record.performed_at_formatted"></p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <template x-if="record.condition_after">
                                            <span class="px-2 py-1 rounded-full text-xs font-medium"
                                                  :class="getConditionBadgeClass(record.condition_after)"
                                                  x-text="record.condition_name">
                                            </span>
                                        </template>
                                    </div>
                                </div>
                                
                                <template x-if="record.notes">
                                    <p class="text-gray-700 text-sm mb-3" x-text="record.notes"></p>
                                </template>

                                <div class="flex items-center justify-between text-sm">
                                    <div class="flex items-center text-gray-500">
                                        <i data-lucide="user" class="w-4 h-4 mr-1"></i>
                                        <span x-text="record.maintainer.name"></span>
                                    </div>
                                    
                                    <template x-if="canEditCare(record)">
                                        <button 
                                            @click="deleteCare(record.id)"
                                            class="text-red-600 hover:text-red-700"
                                        >
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </template>
                                </div>

                                <!-- Images -->
                                <template x-if="record.image_urls && record.image_urls.length > 0">
                                    <div class="mt-3 grid grid-cols-4 gap-2">
                                        <template x-for="imageUrl in record.image_urls" :key="imageUrl">
                                            <img :src="imageUrl" 
                                                 class="w-full h-20 object-cover rounded-lg cursor-pointer hover:opacity-80"
                                                 @click="openImageModal(imageUrl)">
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900">Quick Actions</h3>
                </div>
                
                <div class="p-6 space-y-3">
                    <a href="{{ route('map') }}?tree={{ $tree->id }}" 
                       class="w-full flex items-center gap-3 p-3 bg-emerald-50 text-emerald-700 rounded-xl hover:bg-emerald-100 transition-colors">
                        <i data-lucide="map" class="w-5 h-5"></i>
                        View on Map
                    </a>
                    
                    @if($tree->planted_by_user === Auth::id() || Auth::user()->is_admin)
                    <button @click="editTree()" 
                            class="w-full flex items-center gap-3 p-3 bg-blue-50 text-blue-700 rounded-xl hover:bg-blue-100 transition-colors">
                        <i data-lucide="edit" class="w-5 h-5"></i>
                        Edit Tree
                    </button>
                    @endif
                    
                    <button @click="shareTree()" 
                            class="w-full flex items-center gap-3 p-3 bg-gray-50 text-gray-700 rounded-xl hover:bg-gray-100 transition-colors">
                        <i data-lucide="share" class="w-5 h-5"></i>
                        Share Tree
                    </button>
                </div>
            </div>

            <!-- Statistics -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900">Tree Stats</h3>
                </div>
                
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Days since planting</span>
                        <span class="font-semibold text-gray-900" x-text="daysSincePlanting"></span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Tree Age</span>
                        <span class="font-semibold text-gray-900" x-text="treeAge"></span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Health Status</span>
                        <span :class="healthStatusColor" class="font-semibold" x-text="tree.status"></span>
                    </div>
                </div>
            </div>

            <!-- Related Trees -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900">Related Trees</h3>
                </div>
                
                <div class="p-6">
                    <a href="{{ route('trees.index') }}?type={{ $tree->type }}" 
                       class="block p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                        <p class="font-medium text-gray-900">Other {{ $tree->type }} Trees</p>
                        <p class="text-sm text-gray-600">View similar trees</p>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal (same as in trees list) -->
    <div x-show="showEditModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div x-show="showEditModal" @click.outside="closeEditModal()" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="bg-white rounded-2xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-900">Edit Tree</h2>
                    <button @click="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                        <i data-lucide="x" class="w-6 h-6"></i>
                    </button>
                </div>
            </div>

            <form @submit.prevent="updateTree()" class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Species -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Species *</label>
                        <input 
                            type="text" 
                            x-model="editForm.species"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                        >
                    </div>

                    <!-- Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Type *</label>
                        <select x-model="editForm.type" required class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                            <option value="Fruit">🍎 Fruit</option>
                            <option value="Ornamental">🌸 Ornamental</option>
                            <option value="Forest">🌲 Forest</option>
                            <option value="Medicinal">🌿 Medicinal</option>
                        </select>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                        <select x-model="editForm.status" required class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
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
                            x-model="editForm.planting_date"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                        >
                    </div>

                    <!-- Location Map Picker -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Location *</label>
                        <div class="space-y-2">
                            <div class="relative">
                                <div class="h-64 border border-gray-300 rounded-xl overflow-hidden" id="edit-location-picker-map"></div>
                                <div class="absolute top-2 left-2 bg-white bg-opacity-90 px-3 py-1 rounded-lg text-xs text-gray-600 pointer-events-none">
                                    <i data-lucide="info" class="w-3 h-3 inline mr-1"></i>
                                    Click to change location
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Latitude</label>
                                    <input 
                                        type="number" 
                                        x-model="editForm.latitude"
                                        step="any"
                                        min="-90"
                                        max="90"
                                        required
                                        readonly
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-sm"
                                    >
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Longitude</label>
                                    <input 
                                        type="number" 
                                        x-model="editForm.longitude"
                                        step="any"
                                        min="-180"
                                        max="180"
                                        required
                                        readonly
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-sm"
                                    >
                                </div>
                            </div>
                            <p class="text-xs text-emerald-600" x-show="editForm.latitude && editForm.longitude">
                                <i data-lucide="map-pin" class="w-3 h-3 inline mr-1"></i>
                                Location: <span x-text="formatCoordinates(editForm.latitude, editForm.longitude)"></span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Address -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                    <input 
                        type="text" 
                        x-model="editForm.address"
                        class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                    >
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea 
                        x-model="editForm.description"
                        rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                    ></textarea>
                </div>

                <!-- Form Actions -->
                <div class="flex gap-4 pt-6 border-t border-gray-200">
                    <button 
                        type="button" 
                        @click="closeEditModal()"
                        class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition-colors"
                    >
                        Cancel
                    </button>
                    <button 
                        type="submit"
                        :disabled="isUpdating"
                        class="flex-1 px-6 py-3 bg-gradient-to-r from-emerald-600 to-green-600 text-white rounded-xl font-medium hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                    >
                        <span x-show="!isUpdating">Update Tree</span>
                        <span x-show="isUpdating">Updating...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Care Modal -->
    <div x-show="showAddCareModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div x-show="showAddCareModal" @click.outside="closeCareModal()" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="bg-white rounded-2xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                        <i data-lucide="heart" class="w-5 h-5"></i>
                        Add Care Record
                    </h2>
                    <button @click="closeCareModal()" class="text-gray-400 hover:text-gray-600">
                        <i data-lucide="x" class="w-6 h-6"></i>
                    </button>
                </div>
            </div>

            <form @submit.prevent="submitCare()" class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Activity Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Activity Type *</label>
                        <select x-model="careForm.activity_type" required class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                            <option value="watering">💧 Watering</option>
                            <option value="pruning">✂️ Pruning</option>
                            <option value="fertilizing">🌱 Fertilizing</option>
                            <option value="disease_treatment">💊 Disease Treatment</option>
                            <option value="inspection">🔍 Inspection</option>
                            <option value="other">🛠️ Other</option>
                        </select>
                    </div>

                    <!-- Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date Performed *</label>
                        <input 
                            type="date" 
                            x-model="careForm.performed_at"
                            required
                            max="<?php echo date('Y-m-d'); ?>"
                            class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                        >
                    </div>

                    <!-- Condition After -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tree Condition After Care</label>
                        <div class="grid grid-cols-4 gap-3">
                            <label class="relative cursor-pointer">
                                <input type="radio" x-model="careForm.condition_after" value="excellent" class="peer sr-only">
                                <div class="px-4 py-3 border-2 border-gray-200 rounded-xl text-center peer-checked:border-green-500 peer-checked:bg-green-50 transition-all">
                                    <div class="text-2xl mb-1">😊</div>
                                    <div class="text-sm font-medium">Excellent</div>
                                </div>
                            </label>
                            <label class="relative cursor-pointer">
                                <input type="radio" x-model="careForm.condition_after" value="good" class="peer sr-only">
                                <div class="px-4 py-3 border-2 border-gray-200 rounded-xl text-center peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all">
                                    <div class="text-2xl mb-1">🙂</div>
                                    <div class="text-sm font-medium">Good</div>
                                </div>
                            </label>
                            <label class="relative cursor-pointer">
                                <input type="radio" x-model="careForm.condition_after" value="fair" class="peer sr-only">
                                <div class="px-4 py-3 border-2 border-gray-200 rounded-xl text-center peer-checked:border-yellow-500 peer-checked:bg-yellow-50 transition-all">
                                    <div class="text-2xl mb-1">😐</div>
                                    <div class="text-sm font-medium">Fair</div>
                                </div>
                            </label>
                            <label class="relative cursor-pointer">
                                <input type="radio" x-model="careForm.condition_after" value="poor" class="peer sr-only">
                                <div class="px-4 py-3 border-2 border-gray-200 rounded-xl text-center peer-checked:border-red-500 peer-checked:bg-red-50 transition-all">
                                    <div class="text-2xl mb-1">😞</div>
                                    <div class="text-sm font-medium">Poor</div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea 
                        x-model="careForm.notes"
                        rows="4"
                        maxlength="1000"
                        class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                        placeholder="Add any observations or details about the care activity..."
                    ></textarea>
                    <p class="text-xs text-gray-500 mt-1">Optional: Add details about the care (max 1000 characters)</p>
                </div>

                <!-- Form Actions -->
                <div class="flex gap-4 pt-6 border-t border-gray-200">
                    <button 
                        type="button" 
                        @click="closeCareModal()"
                        class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition-colors"
                    >
                        Cancel
                    </button>
                    <button 
                        type="submit"
                        :disabled="isSubmittingCare"
                        class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl font-medium hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                    >
                        <span x-show="!isSubmittingCare">Add Care</span>
                        <span x-show="isSubmittingCare">Adding...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function treeDetails(treeData) {
    return {
        tree: treeData,
        images: treeData.image_urls || [],
        showEditModal: false,
        isUpdating: false,
        editLocationPickerMap: null,
        editLocationMarker: null,
        editForm: {
            species: treeData.species,
            type: treeData.type,
            status: treeData.status,
            planting_date: treeData.planting_date,
            latitude: treeData.latitude,
            longitude: treeData.longitude,
            address: treeData.address || '',
            description: treeData.description || ''
        },
        
        // Care-related properties
        careRecords: [],
        careLoading: true,
        showAddCareModal: false,
        careForm: {
            tree_id: treeData.id,
            activity_type: 'watering',
            performed_at: new Date().toISOString().split('T')[0],
            condition_after: '',
            notes: '',
            images: []
        },
        isSubmittingCare: false,

        init() {
            this.loadCareRecords();
            
            // Check if URL has #care hash
            if (window.location.hash === '#care') {
                setTimeout(() => {
                    document.getElementById('care')?.scrollIntoView({ behavior: 'smooth' });
                }, 500);
            }
        },

        async loadCareRecords() {
            this.careLoading = true;
            try {
                const token = document.querySelector('meta[name="api-token"]')?.content || 
                             localStorage.getItem('auth_token');
                
                if (!token) {
                    console.error('No auth token found');
                    this.careLoading = false;
                    return;
                }

                const response = await fetch(`/api/tree-care?tree_id=${this.tree.id}`, {
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${token}`
                    }
                });
                
                if (response.ok) {
                    const result = await response.json();
                    this.careRecords = result.data.data || [];
                }
            } catch (error) {
                console.error('Error loading care records:', error);
            } finally {
                this.careLoading = false;
            }
        },

        openAddCareModal() {
            this.careForm = {
                tree_id: this.tree.id,
                activity_type: 'watering',
                performed_at: new Date().toISOString().split('T')[0],
                condition_after: '',
                notes: '',
                images: []
            };
            this.showAddCareModal = true;
        },

        closeCareModal() {
            this.showAddCareModal = false;
        },

        async submitCare() {
            this.isSubmittingCare = true;
            
            try {
                const token = document.querySelector('meta[name="api-token"]')?.content || 
                             localStorage.getItem('auth_token');
                
                const formData = new FormData();
                formData.append('tree_id', this.careForm.tree_id);
                formData.append('activity_type', this.careForm.activity_type);
                formData.append('performed_at', this.careForm.performed_at);
                if (this.careForm.condition_after) {
                    formData.append('condition_after', this.careForm.condition_after);
                }
                if (this.careForm.notes) {
                    formData.append('notes', this.careForm.notes);
                }

                const response = await fetch('/api/tree-care', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${token}`,
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });
                
                if (response.ok) {
                    await this.loadCareRecords();
                    this.closeCareModal();
                    alert('Care record added successfully!');
                } else {
                    const result = await response.json();
                    alert(result.message || 'Error adding care record');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error adding care record');
            } finally {
                this.isSubmittingCare = false;
            }
        },

        async deleteCare(careId) {
            if (!confirm('Are you sure you want to delete this care record?')) {
                return;
            }

            try {
                const token = document.querySelector('meta[name="api-token"]')?.content || 
                             localStorage.getItem('auth_token');
                
                const response = await fetch(`/api/tree-care/${careId}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${token}`,
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                if (response.ok) {
                    await this.loadCareRecords();
                    alert('Care record deleted successfully!');
                } else {
                    const result = await response.json();
                    alert(result.message || 'Error deleting care record');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error deleting care record');
            }
        },

        canEditCare(record) {
            const currentUserId = {{ Auth::id() }};
            const isAdmin = {{ Auth::user()->is_admin ? 'true' : 'false' }};
            return record.user_id === currentUserId || isAdmin;
        },

        getActivityIcon(activityType) {
            const icons = {
                'watering': '💧',
                'pruning': '✂️',
                'fertilizing': '🌱',
                'disease_treatment': '💊',
                'inspection': '🔍',
                'other': '🛠️'
            };
            return icons[activityType] || '🌳';
        },

        getActivityColorClass(activityType) {
            const colors = {
                'watering': 'bg-blue-50',
                'pruning': 'bg-purple-50',
                'fertilizing': 'bg-green-50',
                'disease_treatment': 'bg-red-50',
                'inspection': 'bg-yellow-50',
                'other': 'bg-gray-50'
            };
            return colors[activityType] || 'bg-gray-50';
        },

        getConditionBadgeClass(condition) {
            const classes = {
                'excellent': 'bg-green-100 text-green-800',
                'good': 'bg-blue-100 text-blue-800',
                'fair': 'bg-yellow-100 text-yellow-800',
                'poor': 'bg-red-100 text-red-800'
            };
            return classes[condition] || 'bg-gray-100 text-gray-800';
        },

        get daysSincePlanting() {
            const plantingDate = new Date(this.tree.planting_date);
            const today = new Date();
            const diffTime = Math.abs(today - plantingDate);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            return diffDays;
        },

        get treeAge() {
            const days = this.daysSincePlanting;
            if (days < 30) {
                return `${days} days`;
            } else if (days < 365) {
                const months = Math.floor(days / 30);
                return `${months} month${months > 1 ? 's' : ''}`;
            } else {
                const years = Math.floor(days / 365);
                const remainingMonths = Math.floor((days % 365) / 30);
                let ageStr = `${years} year${years > 1 ? 's' : ''}`;
                if (remainingMonths > 0) {
                    ageStr += `, ${remainingMonths} month${remainingMonths > 1 ? 's' : ''}`;
                }
                return ageStr;
            }
        },

        get healthStatusColor() {
            const colors = {
                'Planted': 'text-green-600',
                'Not Yet': 'text-yellow-600',
                'Sick': 'text-orange-600',
                'Dead': 'text-red-600'
            };
            return colors[this.tree.status] || 'text-gray-600';
        },

        editTree() {
            this.showEditModal = true;
            // Initialize map after modal is shown
            this.$nextTick(() => {
                setTimeout(() => this.initEditLocationPickerMap(), 300);
            });
        },

        async deleteTree() {
            if (!confirm('Are you sure you want to delete this tree? This action cannot be undone.')) {
                return;
            }

            try {
                const response = await fetch(`/trees/${this.tree.id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                if (response.ok) {
                    alert('Tree deleted successfully!');
                    window.location.href = '/trees';
                } else {
                    const result = await response.json();
                    alert(result.message || 'Error deleting tree');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error deleting tree');
            }
        },

        async updateTree() {
            this.isUpdating = true;
            
            try {
                const formData = new FormData();
                
                // Add form fields
                Object.keys(this.editForm).forEach(key => {
                    if (this.editForm[key] !== null && this.editForm[key] !== '') {
                        formData.append(key, this.editForm[key]);
                    }
                });

                formData.append('_method', 'PUT');

                const response = await fetch(`/trees/${this.tree.id}`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });
                
                if (response.ok) {
                    const result = await response.json();
                    
                    // Update tree data
                    this.tree = result.data;
                    this.closeEditModal();
                    
                    // Show success message and reload page to show updated data
                    alert('Tree updated successfully!');
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
                        alert(result.message || 'Error updating tree');
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error updating tree');
            } finally {
                this.isUpdating = false;
            }
        },

        closeEditModal() {
            this.showEditModal = false;
            // Clean up map
            if (this.editLocationPickerMap) {
                this.editLocationPickerMap.remove();
                this.editLocationPickerMap = null;
                this.editLocationMarker = null;
            }
        },

        shareTree() {
            if (navigator.share) {
                navigator.share({
                    title: `${this.tree.species} Tree - Sylva`,
                    text: `Check out this ${this.tree.species} tree on Sylva!`,
                    url: window.location.href
                });
            } else {
                // Fallback for browsers that don't support Web Share API
                navigator.clipboard.writeText(window.location.href).then(() => {
                    alert('Tree link copied to clipboard!');
                });
            }
        },

        initEditLocationPickerMap() {
            if (this.editLocationPickerMap) {
                this.editLocationPickerMap.remove();
            }

            // Initialize map with current tree location
            const lat = parseFloat(this.editForm.latitude);
            const lng = parseFloat(this.editForm.longitude);
            
            this.editLocationPickerMap = L.map('edit-location-picker-map').setView([lat, lng], 15);

            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>'
            }).addTo(this.editLocationPickerMap);

            // Add current location marker
            this.addEditLocationMarker(lat, lng);

            // Handle map clicks
            this.editLocationPickerMap.on('click', (e) => {
                this.setEditLocation(e.latlng.lat, e.latlng.lng);
            });

            // Invalidate size after modal is fully shown
            setTimeout(() => {
                this.editLocationPickerMap.invalidateSize();
            }, 100);
        },

        setEditLocation(lat, lng) {
            this.editForm.latitude = lat.toFixed(6);
            this.editForm.longitude = lng.toFixed(6);
            this.addEditLocationMarker(lat, lng);
            
            // Try to get address from coordinates
            this.reverseGeocodeEdit(lat, lng);
        },

        addEditLocationMarker(lat, lng) {
            if (this.editLocationMarker) {
                this.editLocationPickerMap.removeLayer(this.editLocationMarker);
            }

            this.editLocationMarker = L.marker([lat, lng], {
                icon: L.divIcon({
                    className: 'location-marker',
                    html: '<div class="location-marker"></div>',
                    iconSize: [20, 20],
                    iconAnchor: [10, 10]
                })
            }).addTo(this.editLocationPickerMap);

            // Center map on marker
            this.editLocationPickerMap.setView([lat, lng], this.editLocationPickerMap.getZoom());
        },

        async reverseGeocodeEdit(lat, lng) {
            try {
                const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&addressdetails=1`);
                const data = await response.json();
                
                if (data && data.display_name) {
                    this.editForm.address = data.display_name;
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

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
.location-marker {
    background: #10b981;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    border: 3px solid white;
    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
    cursor: pointer;
}

#edit-location-picker-map {
    z-index: 1;
}
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endpush

@endsection