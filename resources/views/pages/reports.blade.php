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
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Reports</h3>
                    <button onclick="openAddReportModal()" 
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors duration-200">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        <span>Add Report</span>
                    </button>
                </div>

                <!-- Search and Filters -->
                <div x-data="{ 
                    searchQuery: '',
                    selectedType: 'all',
                    selectedUrgency: 'all',
                    selectedStatus: 'all'
                }" class="space-y-4">
                    <!-- Search Bar -->
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="search" class="w-5 h-5 text-gray-400"></i>
                        </div>
                        <input 
                            type="text" 
                            x-model="searchQuery"
                            @input="filterReportsPage()"
                            placeholder="Search reports by title, description, or location..." 
                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        >
                        <div x-show="searchQuery.length > 0" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <button @click="searchQuery = ''; filterReportsPage()" class="text-gray-400 hover:text-gray-600">
                                <i data-lucide="x" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Filters Row -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Type Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Report Type</label>
                            <div class="flex flex-wrap gap-2">
                                <button 
                                    @click="selectedType = 'all'; filterReportsPage()"
                                    :class="selectedType === 'all' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                    class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all"
                                >
                                    All
                                </button>
                                <button 
                                    @click="selectedType = 'tree_planting'; filterReportsPage()"
                                    :class="selectedType === 'tree_planting' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                    class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all"
                                >
                                    <i data-lucide="tree-pine" class="w-3 h-3 inline mr-1"></i>
                                    Tree
                                </button>
                                <button 
                                    @click="selectedType = 'maintenance'; filterReportsPage()"
                                    :class="selectedType === 'maintenance' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                    class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all"
                                >
                                    <i data-lucide="wrench" class="w-3 h-3 inline mr-1"></i>
                                    Maintenance
                                </button>
                                <button 
                                    @click="selectedType = 'pollution'; filterReportsPage()"
                                    :class="selectedType === 'pollution' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                    class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all"
                                >
                                    <i data-lucide="alert-triangle" class="w-3 h-3 inline mr-1"></i>
                                    Pollution
                                </button>
                                <button 
                                    @click="selectedType = 'green_space_suggestion'; filterReportsPage()"
                                    :class="selectedType === 'green_space_suggestion' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                    class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all"
                                >
                                    <i data-lucide="leaf" class="w-3 h-3 inline mr-1"></i>
                                    Green
                                </button>
                            </div>
                        </div>

                        <!-- Priority Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                            <div class="flex flex-wrap gap-2">
                                <button 
                                    @click="selectedUrgency = 'all'; filterReportsPage()"
                                    :class="selectedUrgency === 'all' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                    class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all"
                                >
                                    All
                                </button>
                                <button 
                                    @click="selectedUrgency = 'low'; filterReportsPage()"
                                    :class="selectedUrgency === 'low' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                    class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all"
                                >
                                    <span class="w-2 h-2 bg-blue-500 rounded-full inline-block mr-1"></span>
                                    Low
                                </button>
                                <button 
                                    @click="selectedUrgency = 'medium'; filterReportsPage()"
                                    :class="selectedUrgency === 'medium' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                    class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all"
                                >
                                    <span class="w-2 h-2 bg-yellow-500 rounded-full inline-block mr-1"></span>
                                    Medium
                                </button>
                                <button 
                                    @click="selectedUrgency = 'high'; filterReportsPage()"
                                    :class="selectedUrgency === 'high' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                    class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all"
                                >
                                    <span class="w-2 h-2 bg-red-500 rounded-full inline-block mr-1"></span>
                                    High
                                </button>
                            </div>
                        </div>

                        <!-- Status Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <div class="flex flex-wrap gap-2">
                                <button 
                                    @click="selectedStatus = 'all'; filterReportsPage()"
                                    :class="selectedStatus === 'all' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                    class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all"
                                >
                                    All
                                </button>
                                <button 
                                    @click="selectedStatus = 'pending'; filterReportsPage()"
                                    :class="selectedStatus === 'pending' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                    class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all"
                                >
                                    <i data-lucide="clock" class="w-3 h-3 inline mr-1"></i>
                                    Pending
                                </button>
                                <button 
                                    @click="selectedStatus = 'validated'; filterReportsPage()"
                                    :class="selectedStatus === 'validated' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                    class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all"
                                >
                                    <i data-lucide="check-circle" class="w-3 h-3 inline mr-1"></i>
                                    Validated
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Active Filters Display -->
                    <div x-show="searchQuery || selectedType !== 'all' || selectedUrgency !== 'all' || selectedStatus !== 'all'" class="flex items-center space-x-2">
                        <span class="text-sm text-gray-600">Active filters:</span>
                        <span x-show="searchQuery" class="inline-flex items-center px-2 py-1 rounded-md text-xs bg-green-100 text-green-800">
                            Search: <span x-text="searchQuery.substring(0, 20)" class="ml-1"></span>
                            <button @click="searchQuery = ''; filterReportsPage()" class="ml-1">×</button>
                        </span>
                        <span x-show="selectedType !== 'all'" class="inline-flex items-center px-2 py-1 rounded-md text-xs bg-green-100 text-green-800">
                            Type: <span x-text="selectedType.replace('_', ' ')" class="ml-1 capitalize"></span>
                            <button @click="selectedType = 'all'; filterReportsPage()" class="ml-1">×</button>
                        </span>
                        <span x-show="selectedUrgency !== 'all'" class="inline-flex items-center px-2 py-1 rounded-md text-xs bg-green-100 text-green-800">
                            Priority: <span x-text="selectedUrgency" class="ml-1 capitalize"></span>
                            <button @click="selectedUrgency = 'all'; filterReportsPage()" class="ml-1">×</button>
                        </span>
                        <span x-show="selectedStatus !== 'all'" class="inline-flex items-center px-2 py-1 rounded-md text-xs bg-green-100 text-green-800">
                            Status: <span x-text="selectedStatus" class="ml-1 capitalize"></span>
                            <button @click="selectedStatus = 'all'; filterReportsPage()" class="ml-1">×</button>
                        </span>
                        <button @click="searchQuery = ''; selectedType = 'all'; selectedUrgency = 'all'; selectedStatus = 'all'; filterReportsPage()" class="text-xs text-gray-600 hover:text-gray-900">
                            Clear all
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="p-6">
                @if($reports->count() > 0)
                <div id="reportsContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($reports as $report)
                    <div class="report-card-item bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden"
                         data-title="{{ $report->title }}"
                         data-description="{{ $report->description }}"
                         data-type="{{ $report->type }}"
                         data-urgency="{{ $report->urgency }}"
                         data-status="{{ $report->status }}"
                         data-address="{{ $report->address ?? '' }}">
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
                                                <p class="text-xs text-gray-500">PNG, JPG, WebP up to 5MB each</p>
                                                <p class="text-xs text-purple-600 mt-2">✨ AI will analyze and describe your images automatically</p>
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
                                        <div id="imageAiStatus" class="hidden mt-2 text-xs p-2 bg-purple-50 border border-purple-200 rounded-lg">
                                            <div class="flex items-center space-x-2">
                                                <i data-lucide="sparkles" class="w-4 h-4 text-purple-600 animate-pulse"></i>
                                                <span class="text-purple-700 font-medium">AI is analyzing your images...</span>
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
const GEMINI_API_KEY = '{{ env('GEMINI_API_KEY') }}';
const GEMINI_MODEL = 'gemini-2.0-flash-exp';
const GEMINI_API_URL = `https://generativelanguage.googleapis.com/v1beta/models/${GEMINI_MODEL}:generateContent`;

// AI Description Generation Function
async function generateDescription() {
    const titleInput = document.getElementById('addTitle');
    const descriptionTextarea = document.getElementById('addDescription');
    const typeSelect = document.getElementById('addType');
    const urgencySelect = document.getElementById('addUrgency');
    const addressInput = document.getElementById('addAddress');
    const statusDiv = document.getElementById('aiGenerationStatus');
    const generateBtn = event.target.closest('button');
    
    const title = titleInput.value.trim();
    
    if (!title) {
        alert('Please enter a title first to generate description');
        titleInput.focus();
        return;
    }
    
    // Get additional context
    const reportType = typeSelect.value || 'environmental report';
    const urgency = urgencySelect.value || 'medium';
    const location = addressInput.value.trim();
    
    // Show loading state
    const originalHtml = generateBtn.innerHTML;
    generateBtn.innerHTML = '<i data-lucide="loader" class="w-3 h-3 animate-spin"></i><span>Generating...</span>';
    generateBtn.disabled = true;
    lucide.createIcons();
    
    statusDiv.textContent = '✨ AI is crafting your description...';
    statusDiv.className = 'mt-1 text-xs text-blue-600';
    statusDiv.classList.remove('hidden');
    
    try {
        // Build contextual prompt
        const typeDescriptions = {
            'tree_planting': 'tree planting initiative',
            'maintenance': 'maintenance request',
            'pollution': 'pollution report',
            'green_space_suggestion': 'green space suggestion'
        };
        
        const urgencyDescriptions = {
            'high': 'This is urgent and requires immediate attention.',
            'medium': 'This requires timely action.',
            'low': 'This can be addressed at a convenient time.'
        };
        
        const typeText = typeDescriptions[reportType] || 'environmental report';
        const urgencyText = urgencyDescriptions[urgency] || '';
        const locationText = location ? `Location: ${location}` : '';
        
        const prompt = `Generate a professional, engaging, and promotional description for an environmental report with these details:

Title: "${title}"
Type: ${typeText}
Urgency: ${urgency}
${locationText}

Requirements:
- Under 100 words
- Sound natural, professional, and compelling
- Focus on environmental impact and community benefit
- Use accessible language that inspires action
- ${urgencyText}
- Highlight why this matters for the community and environment
- Make it persuasive for stakeholders to take notice

Generate ONLY the description text without any labels, titles, or extra formatting.`;

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
            const errorData = await response.json().catch(() => ({}));
            const errorMessage = errorData.error?.message || `API Error: ${response.status} ${response.statusText}`;
            throw new Error(errorMessage);
        }

        const data = await response.json();
        const generatedText = data.candidates?.[0]?.content?.parts?.[0]?.text;

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
            throw new Error('No description generated from AI');
        }
    } catch (error) {
        console.error('Error generating description:', error);
        const errorMsg = error.message || 'Unknown error occurred';
        statusDiv.textContent = `❌ ${errorMsg.includes('API') || errorMsg.includes('not found') ? 'AI service error' : 'Failed to generate'}. Try again or write manually.`;
        statusDiv.className = 'mt-1 text-xs text-red-600';
        setTimeout(() => {
            statusDiv.classList.add('hidden');
        }, 5000);
    } finally {
        generateBtn.innerHTML = originalHtml;
        generateBtn.disabled = false;
        // Re-initialize icons safely
        if (typeof lucide !== 'undefined' && lucide.createIcons) {
            lucide.createIcons();
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
    
    // Reset previous error/status messages when new images are uploaded
    const statusEl = document.getElementById('aiGenerationStatus');
    const imageAiStatus = document.getElementById('imageAiStatus');
    if (statusEl) {
        statusEl.classList.add('hidden');
        statusEl.textContent = '';
        statusEl.className = '';
    }
    if (imageAiStatus) {
        imageAiStatus.classList.add('hidden');
    }
    
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
        
        // Automatically trigger AI analysis after a short delay
        setTimeout(() => {
            const descriptionField = document.getElementById('addDescription');
            // Only auto-analyze if description is empty
            if (!descriptionField.value.trim()) {
                generateDescription();
            }
        }, 500);
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
        // Clear the file input
        document.getElementById('addImages').value = '';
        
        // Hide and reset status messages
        const statusEl = document.getElementById('aiGenerationStatus');
        const imageAiStatus = document.getElementById('imageAiStatus');
        if (statusEl) {
            statusEl.classList.add('hidden');
            statusEl.textContent = '';
        }
        if (imageAiStatus) {
            imageAiStatus.classList.add('hidden');
        }
    }
}

// Clear all images
function clearAllImages() {
    selectedImages = [];
    const container = document.getElementById('previewContainer');
    if (container) {
        container.innerHTML = '';
    }
    
    document.getElementById('imagePreview').classList.add('hidden');
    document.getElementById('addImages').value = '';
    
    // Hide and reset status messages
    const statusEl = document.getElementById('aiGenerationStatus');
    const imageAiStatus = document.getElementById('imageAiStatus');
    if (statusEl) {
        statusEl.classList.add('hidden');
        statusEl.textContent = '';
        statusEl.className = '';
    }
    if (imageAiStatus) {
        imageAiStatus.classList.add('hidden');
        imageAiStatus.innerHTML = '';
    }
    
    lucide.createIcons();
}

// Clear images and allow retry
function clearImagesAndRetry() {
    clearAllImages();
    showNotification('Images cleared. Please upload environment-related photos.', 'info');
    // Trigger file input click to upload new images
    setTimeout(() => {
        document.getElementById('addImages').click();
    }, 300);
}

// AI Image Analysis
async function generateDescription() {
    const imageInput = document.getElementById('addImages');
    const files = imageInput.files;
    
    if (files.length === 0) {
        showNotification('Please upload images first before generating description', 'warning');
        return;
    }
    
    // Show loading state
    const statusEl = document.getElementById('aiGenerationStatus');
    const imageAiStatus = document.getElementById('imageAiStatus');
    const generateBtn = document.querySelector('[onclick="generateDescription()"]');
    
    // Show image AI status indicator
    if (imageAiStatus) {
        imageAiStatus.classList.remove('hidden');
    }
    
    statusEl.textContent = '🤖 Analyzing images with AI... Please wait';
    statusEl.classList.remove('hidden', 'text-green-600', 'text-red-600');
    statusEl.classList.add('text-blue-600', 'animate-pulse');
    
    // Disable button if it exists
    let originalBtnContent = '';
    if (generateBtn) {
        originalBtnContent = generateBtn.innerHTML;
        generateBtn.disabled = true;
        generateBtn.innerHTML = '<i data-lucide="loader" class="w-3 h-3 animate-spin"></i><span class="ml-1">Analyzing...</span>';
        lucide.createIcons();
    }
    
    // Prepare FormData
    const formData = new FormData();
    if (files.length === 1) {
        // Single image endpoint expects 'image' (singular)
        formData.append('image', files[0]);
    } else {
        // Multiple images endpoint expects 'images[]' (array)
        for (let i = 0; i < files.length; i++) {
            formData.append('images[]', files[i]);
        }
    }
    
    // Add context if available
    const title = document.getElementById('addTitle').value;
    if (title) {
        formData.append('context', `Report title: ${title}`);
    }
    
    try {
        const endpoint = files.length === 1 ? '/api/analyze-image-public' : '/api/analyze-images-public';
        
        const response = await fetch(endpoint, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        const data = await response.json();
        
        // Debug: Log the full response
        console.log('API Response Status:', response.status);
        console.log('API Response Data:', data);
        
        // Check if response is not successful
        if (!response.ok || !data.success) {
            // Log validation errors if present
            if (data.errors) {
                console.error('Validation Errors:', data.errors);
            }
            
            // Check if it's a rate limit error
            if (response.status === 429 || data.error === 'rate_limit' || data.error === 'quota_exceeded') {
                const waitMessage = data.error === 'quota_exceeded' 
                    ? 'Daily quota exceeded. Try again tomorrow.' 
                    : 'Rate limit reached. Please wait 60 seconds...';
                throw new Error('RATE_LIMIT:' + waitMessage);
            }
            
            // Check if it's a non-environmental image error
            if (data.error === 'not_environmental' || (data.message && data.message.includes('not related to environmental'))) {
                throw new Error('NOT_ENVIRONMENTAL:' + (data.message || "This photo is not related to environmental issues."));
            }
            
            // Show validation errors if present
            if (data.errors) {
                const errorMessages = Object.values(data.errors).flat().join(', ');
                throw new Error('Validation Error: ' + errorMessages);
            }
            
            throw new Error(data.message || data.error || 'Analysis failed');
        }
        
        if (data.success && data.data) {
            // Auto-fill form fields
            const descriptionField = document.getElementById('addDescription');
            descriptionField.value = data.data.description;
            
            // Highlight the field briefly
            descriptionField.classList.add('ring-2', 'ring-green-500', 'bg-green-50');
            setTimeout(() => {
                descriptionField.classList.remove('ring-2', 'ring-green-500', 'bg-green-50');
            }, 2000);
            
            // Set suggested type
            if (data.data.suggested_type) {
                const typeSelect = document.getElementById('addType');
                typeSelect.value = data.data.suggested_type;
                typeSelect.classList.add('ring-2', 'ring-green-500', 'bg-green-50');
                setTimeout(() => {
                    typeSelect.classList.remove('ring-2', 'ring-green-500', 'bg-green-50');
                }, 2000);
            }
            
            // Set suggested urgency
            if (data.data.suggested_urgency) {
                const urgencySelect = document.getElementById('addUrgency');
                urgencySelect.value = data.data.suggested_urgency;
                urgencySelect.classList.add('ring-2', 'ring-green-500', 'bg-green-50');
                setTimeout(() => {
                    urgencySelect.classList.remove('ring-2', 'ring-green-500', 'bg-green-50');
                }, 2000);
            }
            
            // Hide image AI status
            if (imageAiStatus) {
                imageAiStatus.innerHTML = `
                    <div class="flex items-center space-x-2">
                        <i data-lucide="check-circle" class="w-4 h-4 text-green-600"></i>
                        <span class="text-green-700 font-medium">✨ AI analysis complete!</span>
                    </div>
                `;
                imageAiStatus.classList.remove('bg-purple-50', 'border-purple-200');
                imageAiStatus.classList.add('bg-green-50', 'border-green-200');
                lucide.createIcons();
                
                setTimeout(() => {
                    imageAiStatus.classList.add('hidden');
                }, 3000);
            }
            
            // Show recommendations if available
            statusEl.classList.remove('animate-pulse');
            if (data.data.recommendations && data.data.recommendations.length > 0) {
                statusEl.innerHTML = `
                    <div class="text-green-700">
                        <strong>✅ AI Analysis Complete!</strong> Description and suggestions added.
                        <details class="mt-1">
                            <summary class="cursor-pointer hover:underline text-xs">💡 View AI Recommendations</summary>
                            <ul class="list-disc list-inside text-xs mt-1 ml-2 space-y-1">
                                ${data.data.recommendations.map(rec => `<li>${rec}</li>`).join('')}
                            </ul>
                        </details>
                    </div>
                `;
            } else {
                statusEl.textContent = '✅ AI analysis complete! Description generated successfully.';
            }
            
            statusEl.classList.add('text-green-600');
            showNotification('AI generated description successfully!', 'success');
            
        } else {
            // Check if it's a non-environmental image error
            if (data.error === 'not_environmental') {
                throw new Error(data.message || "This photo is not related to environmental issues. We can only accept environment-related photos.");
            }
            throw new Error(data.error || data.message || 'Analysis failed');
        }
    } catch (error) {
        statusEl.classList.remove('animate-pulse');
        
        // Check if it's a rate limit error
        const isRateLimit = error.message.startsWith('RATE_LIMIT:');
        
        // Check if it's a non-environmental image error
        const isNonEnvironmental = error.message.startsWith('NOT_ENVIRONMENTAL:') || 
                                   error.message.includes('not related to environmental') || 
                                   error.message.includes('not environmental');
        
        if (isRateLimit) {
            // Extract the message
            const errorMsg = error.message.replace('RATE_LIMIT:', '').trim();
            const isQuotaExceeded = errorMsg.includes('quota') || errorMsg.includes('tomorrow');
            
            statusEl.innerHTML = `
                <div class="text-yellow-700 space-y-2">
                    <div class="flex items-center space-x-1">
                        <i data-lucide="clock" class="w-4 h-4"></i>
                        <strong>⏱️ ${isQuotaExceeded ? 'Daily Quota Exceeded' : 'Rate Limit Reached'}</strong>
                    </div>
                    <p class="text-xs">${errorMsg}</p>
                    ${!isQuotaExceeded ? `
                        <p class="text-xs mt-1">⚠️ Free API allows only 2-3 requests per minute.</p>
                        <p class="text-xs font-medium mt-2">Please wait: <span id="rateLimitCountdown" class="text-yellow-900 font-bold">60</span> seconds</p>
                    ` : `
                        <p class="text-xs mt-1">The free tier has a daily limit. Try again tomorrow or describe manually.</p>
                    `}
                </div>
            `;
            statusEl.classList.add('text-yellow-600');
            
            // Update image AI status
            if (imageAiStatus) {
                imageAiStatus.innerHTML = `
                    <div class="flex items-center space-x-2">
                        <i data-lucide="clock" class="w-4 h-4 text-yellow-600"></i>
                        <span class="text-yellow-700 font-medium">⏱️ ${isQuotaExceeded ? 'Daily quota exceeded' : 'Please wait 60 seconds...'}</span>
                    </div>
                `;
                imageAiStatus.classList.remove('bg-purple-50', 'border-purple-200', 'hidden');
                imageAiStatus.classList.add('bg-yellow-50', 'border-yellow-200');
                lucide.createIcons();
            }
            
            showNotification(isQuotaExceeded ? '⏱️ Daily quota exceeded. Try again tomorrow.' : '⏱️ Rate limit reached. Please wait 60 seconds before trying again.', 'warning');
            
            // Start countdown if not quota exceeded
            if (!isQuotaExceeded) {
                let countdown = 60;
                const countdownEl = document.getElementById('rateLimitCountdown');
                const countdownInterval = setInterval(() => {
                    countdown--;
                    if (countdownEl) {
                        countdownEl.textContent = countdown;
                    }
                    if (countdown <= 0) {
                        clearInterval(countdownInterval);
                        if (imageAiStatus) {
                            imageAiStatus.innerHTML = `
                                <div class="flex items-center space-x-2">
                                    <i data-lucide="check-circle" class="w-4 h-4 text-green-600"></i>
                                    <span class="text-green-700 font-medium">✅ Ready! You can try again now.</span>
                                </div>
                            `;
                            imageAiStatus.classList.remove('bg-yellow-50', 'border-yellow-200');
                            imageAiStatus.classList.add('bg-green-50', 'border-green-200');
                            lucide.createIcons();
                            setTimeout(() => {
                                imageAiStatus.classList.add('hidden');
                            }, 3000);
                        }
                        showNotification('✅ Ready! You can try analyzing your images again.', 'success');
                    }
                }, 1000);
            }
            
        } else if (isNonEnvironmental) {
            // Extract the message
            const errorMsg = error.message.replace('NOT_ENVIRONMENTAL:', '').trim();
            
            statusEl.innerHTML = `
                <div class="text-orange-700 space-y-1">
                    <div class="flex items-center space-x-1">
                        <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                        <strong>❌ Not an environmental photo</strong>
                    </div>
                    <p class="text-xs">${errorMsg || 'This photo doesn\'t show environmental issues.'}</p>
                    <p class="text-xs mt-2">Please upload images of:</p>
                    <ul class="text-xs list-disc list-inside ml-2 space-y-0.5">
                        <li>Trees, plants, or forests</li>
                        <li>Pollution or waste</li>
                        <li>Parks or green spaces</li>
                        <li>Environmental damage</li>
                    </ul>
                    <button onclick="clearImagesAndRetry()" class="mt-2 text-xs bg-orange-100 hover:bg-orange-200 text-orange-700 px-3 py-1 rounded transition-colors">
                        Clear images and upload new ones
                    </button>
                </div>
            `;
            statusEl.classList.add('text-orange-600');
            
            // Update image AI status with specific message
            if (imageAiStatus) {
                imageAiStatus.innerHTML = `
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <i data-lucide="x-circle" class="w-4 h-4 text-orange-600"></i>
                            <span class="text-orange-700 font-medium">⚠️ Not environmental - Image will be removed</span>
                        </div>
                    </div>
                `;
                imageAiStatus.classList.remove('bg-purple-50', 'border-purple-200', 'hidden');
                imageAiStatus.classList.add('bg-orange-50', 'border-orange-200');
                lucide.createIcons();
            }
            
            showNotification('❌ Not an environmental photo. Images will be cleared. Please upload environment-related images only.', 'warning');
            
            // Automatically remove the non-environmental images after showing message
            setTimeout(() => {
                clearAllImages();
                if (imageAiStatus) {
                    imageAiStatus.classList.add('hidden');
                }
                showNotification('Images cleared. Please upload environment-related photos.', 'info');
            }, 3000);
            
        } else {
            statusEl.textContent = '❌ Failed to generate description. Please write manually or try again.';
            statusEl.classList.add('text-red-600');
            
            // Update image AI status for general errors
            if (imageAiStatus) {
                imageAiStatus.innerHTML = `
                    <div class="flex items-center space-x-2">
                        <i data-lucide="alert-circle" class="w-4 h-4 text-red-600"></i>
                        <span class="text-red-700 font-medium">Analysis failed. You can try again or write manually.</span>
                    </div>
                `;
                imageAiStatus.classList.remove('bg-purple-50', 'border-purple-200', 'hidden');
                imageAiStatus.classList.add('bg-red-50', 'border-red-200');
                lucide.createIcons();
                
                setTimeout(() => {
                    imageAiStatus.classList.add('hidden');
                }, 5000);
            }
            
            showNotification(`AI analysis failed: ${error.message}`, 'error');
        }
        
        lucide.createIcons();
        console.error('AI Analysis Error:', error);
    } finally {
        if (generateBtn) {
            generateBtn.disabled = false;
            generateBtn.innerHTML = originalBtnContent;
            lucide.createIcons();
        }
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
    
    // Check if title is only numbers
    if (/^[\d\s\.\-\+]+$/.test(reportData.title.trim())) {
        alert('The title cannot contain only numbers. Please add some text.');
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
    
    // Check if title is only numbers
    if (/^[\d\s\.\-\+]+$/.test(formData.get('title').trim())) {
        alert('The title cannot contain only numbers. Please add some text.');
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

// Store all reports data for filtering
let allReportsPage = [];

// Initialize reports filtering
document.addEventListener('DOMContentLoaded', function() {
    // Store initial reports
    allReportsPage = Array.from(document.querySelectorAll('.report-card-item')).map(card => ({
        element: card,
        title: card.dataset.title?.toLowerCase() || '',
        description: card.dataset.description?.toLowerCase() || '',
        type: card.dataset.type || '',
        urgency: card.dataset.urgency || '',
        status: card.dataset.status || '',
        address: card.dataset.address?.toLowerCase() || ''
    }));
});

// Smart filter function with multi-word search for Reports page
function filterReportsPage() {
    const searchInput = document.querySelector('input[x-model="searchQuery"]');
    const searchQuery = searchInput ? searchInput.value.toLowerCase().trim() : '';
    
    // Get Alpine.js component data
    const alpineComponent = document.querySelector('[x-data]');
    let selectedType = 'all';
    let selectedUrgency = 'all';
    let selectedStatus = 'all';
    
    if (alpineComponent && alpineComponent.__x) {
        selectedType = alpineComponent.__x.$data.selectedType || 'all';
        selectedUrgency = alpineComponent.__x.$data.selectedUrgency || 'all';
        selectedStatus = alpineComponent.__x.$data.selectedStatus || 'all';
    }
    
    // Split search query into words for multi-word search
    const searchWords = searchQuery.split(' ').filter(word => word.length > 0);
    
    let visibleCount = 0;
    
    allReportsPage.forEach(report => {
        let matchesSearch = true;
        let matchesType = true;
        let matchesUrgency = true;
        let matchesStatus = true;
        
        // Multi-word search - all words must match
        if (searchWords.length > 0) {
            matchesSearch = searchWords.every(word => 
                report.title.includes(word) || 
                report.description.includes(word) || 
                report.address.includes(word)
            );
        }
        
        // Type filter
        if (selectedType !== 'all') {
            matchesType = report.type === selectedType;
        }
        
        // Urgency filter
        if (selectedUrgency !== 'all') {
            matchesUrgency = report.urgency === selectedUrgency;
        }
        
        // Status filter
        if (selectedStatus !== 'all') {
            matchesStatus = report.status === selectedStatus;
        }
        
        // Show/hide report based on all filters
        if (matchesSearch && matchesType && matchesUrgency && matchesStatus) {
            report.element.style.display = '';
            visibleCount++;
        } else {
            report.element.style.display = 'none';
        }
    });
    
    // Show/hide empty state
    const container = document.getElementById('reportsContainer');
    let emptyState = document.getElementById('filterEmptyState');
    
    if (visibleCount === 0 && allReportsPage.length > 0) {
        if (!emptyState) {
            emptyState = document.createElement('div');
            emptyState.id = 'filterEmptyState';
            emptyState.className = 'col-span-full text-center py-12';
            emptyState.innerHTML = `
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="search-x" class="w-8 h-8 text-gray-400"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No reports found</h3>
                <p class="text-gray-600 mb-4">Try adjusting your filters or search terms</p>
                <button onclick="clearAllFiltersPage()" class="text-green-600 hover:text-green-700 font-medium">
                    Clear all filters
                </button>
            `;
            container.appendChild(emptyState);
            lucide.createIcons();
        }
    } else if (emptyState) {
        emptyState.remove();
    }
    
    // Reinitialize icons
    setTimeout(() => lucide.createIcons(), 100);
}

// Clear all filters for Reports page
function clearAllFiltersPage() {
    const input = document.querySelector('input[x-model="searchQuery"]');
    if (input) input.value = '';
    
    const alpineComponent = document.querySelector('[x-data]');
    if (alpineComponent && alpineComponent.__x) {
        alpineComponent.__x.$data.searchQuery = '';
        alpineComponent.__x.$data.selectedType = 'all';
        alpineComponent.__x.$data.selectedUrgency = 'all';
        alpineComponent.__x.$data.selectedStatus = 'all';
    }
    
    filterReportsPage();
}

// Make functions globally available
window.filterReportsPage = filterReportsPage;
window.clearAllFiltersPage = clearAllFiltersPage;

// Notification helper function
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-[9999] max-w-md px-6 py-4 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full`;
    
    // Set color based on type
    const colors = {
        success: 'bg-green-600 text-white',
        error: 'bg-red-600 text-white',
        warning: 'bg-yellow-600 text-white',
        info: 'bg-blue-600 text-white'
    };
    
    notification.classList.add(...colors[type].split(' '));
    
    // Set icon based on type
    const icons = {
        success: 'check-circle',
        error: 'alert-circle',
        warning: 'alert-triangle',
        info: 'info'
    };
    
    notification.innerHTML = `
        <div class="flex items-center space-x-3">
            <i data-lucide="${icons[type]}" class="w-5 h-5 flex-shrink-0"></i>
            <p class="font-medium">${message}</p>
        </div>
    `;
    
    document.body.appendChild(notification);
    lucide.createIcons();
    
    // Slide in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Slide out and remove
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

</script>
@endpush
@endsection