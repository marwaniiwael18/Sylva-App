@extends('layouts.dashboard')

@section('page-title', 'Community Feed')
@section('page-subtitle', 'Engage with environmental reports - vote, comment, and react')

@push('styles')
<style>
.feed-card {
    @apply bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-all duration-200;
}
.filter-badge {
    @apply px-3 py-1.5 rounded-lg text-sm font-medium transition-all cursor-pointer;
}
.filter-badge.active {
    @apply bg-green-600 text-white;
}
.filter-badge:not(.active) {
    @apply bg-gray-100 text-gray-700 hover:bg-gray-200;
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
                    <p class="text-sm font-medium text-gray-600">Total Comments</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $statistics['total_comments'] }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                    <i data-lucide="message-circle" class="w-5 h-5 text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Votes</p>
                    <p class="text-2xl font-bold text-green-600">{{ $statistics['total_votes'] }}</p>
                </div>
                <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center">
                    <i data-lucide="arrow-up" class="w-5 h-5 text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Reactions</p>
                    <p class="text-2xl font-bold text-purple-600">{{ $statistics['total_reactions'] }}</p>
                </div>
                <div class="w-10 h-10 bg-purple-50 rounded-lg flex items-center justify-center">
                    <i data-lucide="heart" class="w-5 h-5 text-purple-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Active Discussions</p>
                    <p class="text-2xl font-bold text-orange-600">{{ $statistics['active_discussions'] }}</p>
                </div>
                <div class="w-10 h-10 bg-orange-50 rounded-lg flex items-center justify-center">
                    <i data-lucide="users" class="w-5 h-5 text-orange-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Feed Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Community Discussions</h3>
                    <p class="text-sm text-gray-600 mt-1">Join the conversation! Vote, react, and comment on environmental reports.</p>
                </div>
                <a href="{{ route('reports') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors duration-200">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>Create Report</span>
                </a>
            </div>

            <!-- Search and Filters -->
            <div x-data="{ 
                searchQuery: '',
                selectedType: 'all',
                selectedUrgency: 'all',
                types: ['all', 'tree_planting', 'maintenance', 'pollution', 'green_space_suggestion'],
                urgencies: ['all', 'low', 'medium', 'high']
            }" class="space-y-4">
                <!-- Search Bar -->
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="search" class="w-5 h-5 text-gray-400"></i>
                    </div>
                    <input 
                        type="text" 
                        x-model="searchQuery"
                        @input="filterReports()"
                        placeholder="Search reports by title, description, or location..." 
                        class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                    <div x-show="searchQuery.length > 0" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <button @click="searchQuery = ''; filterReports()" class="text-gray-400 hover:text-gray-600">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                <!-- Filters -->
                <div class="flex items-center space-x-4">
                    <!-- Type Filter -->
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Report Type</label>
                        <div class="flex flex-wrap gap-2">
                            <button 
                                @click="selectedType = 'all'; filterReports()"
                                :class="selectedType === 'all' ? 'active' : ''"
                                class="filter-badge"
                            >
                                <i data-lucide="layers" class="w-3 h-3 inline mr-1"></i>
                                All Types
                            </button>
                            <button 
                                @click="selectedType = 'tree_planting'; filterReports()"
                                :class="selectedType === 'tree_planting' ? 'active' : ''"
                                class="filter-badge"
                            >
                                <i data-lucide="tree-pine" class="w-3 h-3 inline mr-1"></i>
                                Tree Planting
                            </button>
                            <button 
                                @click="selectedType = 'maintenance'; filterReports()"
                                :class="selectedType === 'maintenance' ? 'active' : ''"
                                class="filter-badge"
                            >
                                <i data-lucide="wrench" class="w-3 h-3 inline mr-1"></i>
                                Maintenance
                            </button>
                            <button 
                                @click="selectedType = 'pollution'; filterReports()"
                                :class="selectedType === 'pollution' ? 'active' : ''"
                                class="filter-badge"
                            >
                                <i data-lucide="alert-triangle" class="w-3 h-3 inline mr-1"></i>
                                Pollution
                            </button>
                            <button 
                                @click="selectedType = 'green_space_suggestion'; filterReports()"
                                :class="selectedType === 'green_space_suggestion' ? 'active' : ''"
                                class="filter-badge"
                            >
                                <i data-lucide="leaf" class="w-3 h-3 inline mr-1"></i>
                                Green Space
                            </button>
                        </div>
                    </div>

                    <!-- Urgency Filter -->
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                        <div class="flex flex-wrap gap-2">
                            <button 
                                @click="selectedUrgency = 'all'; filterReports()"
                                :class="selectedUrgency === 'all' ? 'active' : ''"
                                class="filter-badge"
                            >
                                All Priorities
                            </button>
                            <button 
                                @click="selectedUrgency = 'low'; filterReports()"
                                :class="selectedUrgency === 'low' ? 'active' : ''"
                                class="filter-badge"
                            >
                                <span class="w-2 h-2 bg-blue-500 rounded-full inline-block mr-1"></span>
                                Low
                            </button>
                            <button 
                                @click="selectedUrgency = 'medium'; filterReports()"
                                :class="selectedUrgency === 'medium' ? 'active' : ''"
                                class="filter-badge"
                            >
                                <span class="w-2 h-2 bg-yellow-500 rounded-full inline-block mr-1"></span>
                                Medium
                            </button>
                            <button 
                                @click="selectedUrgency = 'high'; filterReports()"
                                :class="selectedUrgency === 'high' ? 'active' : ''"
                                class="filter-badge"
                            >
                                <span class="w-2 h-2 bg-red-500 rounded-full inline-block mr-1"></span>
                                High
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Active Filters Display -->
                <div x-show="searchQuery || selectedType !== 'all' || selectedUrgency !== 'all'" class="flex items-center space-x-2">
                    <span class="text-sm text-gray-600">Active filters:</span>
                    <span x-show="searchQuery" class="inline-flex items-center px-2 py-1 rounded-md text-xs bg-green-100 text-green-800">
                        Search: <span x-text="searchQuery.substring(0, 20)" class="ml-1"></span>
                        <button @click="searchQuery = ''; filterReports()" class="ml-1">×</button>
                    </span>
                    <span x-show="selectedType !== 'all'" class="inline-flex items-center px-2 py-1 rounded-md text-xs bg-green-100 text-green-800">
                        Type: <span x-text="selectedType.replace('_', ' ')" class="ml-1 capitalize"></span>
                        <button @click="selectedType = 'all'; filterReports()" class="ml-1">×</button>
                    </span>
                    <span x-show="selectedUrgency !== 'all'" class="inline-flex items-center px-2 py-1 rounded-md text-xs bg-green-100 text-green-800">
                        Priority: <span x-text="selectedUrgency" class="ml-1 capitalize"></span>
                        <button @click="selectedUrgency = 'all'; filterReports()" class="ml-1">×</button>
                    </span>
                    <button @click="searchQuery = ''; selectedType = 'all'; selectedUrgency = 'all'; filterReports()" class="text-xs text-gray-600 hover:text-gray-900">
                        Clear all
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Reports Feed -->
        <div class="p-6 space-y-6" id="reportsContainer">
        @forelse($reports as $report)
        <div class="feed-card overflow-hidden bg-gray-50 report-card" 
             data-title="{{ $report->title }}"
             data-description="{{ $report->description }}"
             data-type="{{ $report->type }}"
             data-urgency="{{ $report->urgency }}"
             data-address="{{ $report->address ?? '' }}">
            <!-- Report Header -->
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1">
                        <div class="flex items-center space-x-3 mb-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-emerald-400 to-green-500 rounded-full flex items-center justify-center shadow-md">
                                <i data-lucide="user" class="w-5 h-5 text-white"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">{{ $report->user->name ?? 'Unknown User' }}</p>
                                <p class="text-xs text-gray-500">{{ $report->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $report->title }}</h3>
                        <p class="text-gray-600 text-sm mb-3 line-clamp-3">{{ $report->description }}</p>
                        
                        <!-- Report Meta -->
                        <div class="flex items-center flex-wrap gap-2 text-sm">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                                <i data-lucide="tag" class="w-3 h-3 mr-1"></i>
                                {{ ucfirst(str_replace('_', ' ', $report->type)) }}
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border
                                {{ $report->urgency === 'high' ? 'bg-red-50 text-red-700 border-red-200' : 
                                   ($report->urgency === 'medium' ? 'bg-yellow-50 text-yellow-700 border-yellow-200' : 'bg-blue-50 text-blue-700 border-blue-200') }}">
                                {{ ucfirst($report->urgency) }} Priority
                            </span>
                            @if($report->address)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs text-gray-600 bg-gray-50 border border-gray-200">
                                <i data-lucide="map-pin" class="w-3 h-3 mr-1"></i>
                                {{ Str::limit($report->address, 30) }}
                            </span>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Report Image (if exists) -->
                    @if($report->images && count($report->images) > 0)
                    <div class="ml-4 flex-shrink-0">
                        <img src="{{ $report->image_urls[0] }}" 
                             alt="{{ $report->title }}" 
                             class="w-24 h-24 object-cover rounded-lg shadow-sm border border-gray-200">
                    </div>
                    @endif
                </div>
            </div>

            <!-- Social Feed Component -->
            <div class="px-6 pb-6">
                @include('components.report-feed', ['reportId' => $report->id])
            </div>
        </div>
        @empty
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="message-circle" class="w-8 h-8 text-gray-400"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">No Reports Yet</h3>
            <p class="text-gray-600 mb-6">Be the first to create an environmental report and start the conversation!</p>
            <a href="{{ route('reports') }}" class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors shadow-sm">
                <i data-lucide="plus" class="w-5 h-5 mr-2"></i>
                Create First Report
            </a>
        </div>
        @endforelse
        </div>
    </div>

    <!-- Pagination -->
    @if($reports->hasPages())
    <div class="mt-6">
        {{ $reports->links() }}
    </div>
    @endif
</div>

@push('scripts')
<script>
// Store all reports data for filtering
let allReports = [];

// Reinitialize Lucide icons after Alpine.js loads
document.addEventListener('DOMContentLoaded', function() {
    // Store initial reports
    allReports = Array.from(document.querySelectorAll('.report-card')).map(card => ({
        element: card,
        title: card.dataset.title?.toLowerCase() || '',
        description: card.dataset.description?.toLowerCase() || '',
        type: card.dataset.type || '',
        urgency: card.dataset.urgency || '',
        address: card.dataset.address?.toLowerCase() || ''
    }));
    
    lucide.createIcons();
    
    // Reinitialize after a short delay to catch dynamically loaded content
    setTimeout(() => lucide.createIcons(), 500);
});

// Smart filter function with multi-word search
function filterReports() {
    const searchQuery = document.querySelector('input[type="text"]').value.toLowerCase().trim();
    const selectedType = document.querySelector('[x-data]').__x?.$data?.selectedType || 'all';
    const selectedUrgency = document.querySelector('[x-data]').__x?.$data?.selectedUrgency || 'all';
    
    // Get Alpine.js component data
    const alpineData = Alpine.$data(document.querySelector('[x-data]'));
    const actualType = alpineData?.selectedType || 'all';
    const actualUrgency = alpineData?.selectedUrgency || 'all';
    
    // Split search query into words for multi-word search
    const searchWords = searchQuery.split(' ').filter(word => word.length > 0);
    
    let visibleCount = 0;
    
    allReports.forEach(report => {
        let matchesSearch = true;
        let matchesType = true;
        let matchesUrgency = true;
        
        // Multi-word search - all words must match
        if (searchWords.length > 0) {
            matchesSearch = searchWords.every(word => 
                report.title.includes(word) || 
                report.description.includes(word) || 
                report.address.includes(word)
            );
        }
        
        // Type filter
        if (actualType !== 'all') {
            matchesType = report.type === actualType;
        }
        
        // Urgency filter
        if (actualUrgency !== 'all') {
            matchesUrgency = report.urgency === actualUrgency;
        }
        
        // Show/hide report based on all filters
        if (matchesSearch && matchesType && matchesUrgency) {
            report.element.style.display = '';
            visibleCount++;
        } else {
            report.element.style.display = 'none';
        }
    });
    
    // Show/hide empty state
    const emptyState = document.getElementById('emptyState');
    const container = document.getElementById('reportsContainer');
    
    if (visibleCount === 0 && allReports.length > 0) {
        if (!emptyState) {
            const emptyDiv = document.createElement('div');
            emptyDiv.id = 'emptyState';
            emptyDiv.className = 'text-center py-12';
            emptyDiv.innerHTML = `
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="search-x" class="w-8 h-8 text-gray-400"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No reports found</h3>
                <p class="text-gray-600 mb-4">Try adjusting your filters or search terms</p>
                <button onclick="clearAllFilters()" class="text-green-600 hover:text-green-700 font-medium">
                    Clear all filters
                </button>
            `;
            container.appendChild(emptyDiv);
            lucide.createIcons();
        }
    } else if (emptyState) {
        emptyState.remove();
    }
    
    // Reinitialize icons
    setTimeout(() => lucide.createIcons(), 100);
}

// Clear all filters
function clearAllFilters() {
    const input = document.querySelector('input[type="text"]');
    if (input) input.value = '';
    
    const alpineComponent = document.querySelector('[x-data]');
    if (alpineComponent && alpineComponent.__x) {
        alpineComponent.__x.$data.searchQuery = '';
        alpineComponent.__x.$data.selectedType = 'all';
        alpineComponent.__x.$data.selectedUrgency = 'all';
    }
    
    filterReports();
}

// Make filterReports globally available
window.filterReports = filterReports;
window.clearAllFilters = clearAllFilters;
</script>
@endpush
@endsection
