@extends('layouts.dashboard')

@section('page-title', 'Community Feed')
@section('page-subtitle', 'Engage with environmental reports - vote, comment, and react')

@push('styles')
<style>
.stat-card {
    @apply bg-white rounded-lg p-6 border border-gray-200 shadow-sm hover:shadow-md transition-shadow;
}
.feed-card {
    @apply bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-all duration-200;
}
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Statistics Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Comments</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $statistics['total_comments'] }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i data-lucide="message-circle" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Votes</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $statistics['total_votes'] }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <i data-lucide="arrow-up" class="w-6 h-6 text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Reactions</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $statistics['total_reactions'] }}</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                    <i data-lucide="heart" class="w-6 h-6 text-purple-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Active Discussions</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $statistics['active_discussions'] }}</p>
                </div>
                <div class="bg-orange-100 p-3 rounded-full">
                    <i data-lucide="users" class="w-6 h-6 text-orange-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Feed Header -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-2xl font-bold text-gray-900">Community Discussions</h2>
            <a href="{{ route('reports') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors duration-200">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Create Report</span>
            </a>
        </div>
        <p class="text-gray-600">Join the conversation! Vote, react, and comment on environmental reports from your community.</p>
    </div>

    <!-- Reports Feed -->
    <div class="space-y-6">
        @forelse($reports as $report)
        <div class="feed-card overflow-hidden">
            <!-- Report Header -->
            <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-green-50 to-blue-50">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1">
                        <div class="flex items-center space-x-3 mb-2">
                            <div class="w-10 h-10 bg-gradient-to-br from-green-400 to-blue-500 rounded-full flex items-center justify-center">
                                <i data-lucide="user" class="w-5 h-5 text-white"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">{{ $report->user->name ?? 'Unknown User' }}</p>
                                <p class="text-sm text-gray-600">{{ $report->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $report->title }}</h3>
                        <p class="text-gray-700 mb-3">{{ Str::limit($report->description, 200) }}</p>
                        
                        <!-- Report Meta -->
                        <div class="flex items-center space-x-4 text-sm">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i data-lucide="tag" class="w-3 h-3 mr-1"></i>
                                {{ ucfirst(str_replace('_', ' ', $report->type)) }}
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $report->urgency === 'high' ? 'bg-red-100 text-red-800' : 
                                   ($report->urgency === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') }}">
                                {{ ucfirst($report->urgency) }} Priority
                            </span>
                            @if($report->address)
                            <span class="text-gray-600 flex items-center">
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
                             class="w-32 h-32 object-cover rounded-lg shadow-md">
                    </div>
                    @endif
                </div>
            </div>

            <!-- Social Feed Component -->
            <div class="p-6">
                @include('components.report-feed', ['reportId' => $report->id])
            </div>
        </div>
        @empty
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <i data-lucide="message-circle" class="w-16 h-16 mx-auto mb-4 text-gray-300"></i>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No Reports Yet</h3>
            <p class="text-gray-600 mb-6">Be the first to create an environmental report and start the conversation!</p>
            <a href="{{ route('reports') }}" class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors">
                <i data-lucide="plus" class="w-5 h-5 mr-2"></i>
                Create First Report
            </a>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($reports->hasPages())
    <div class="mt-8">
        {{ $reports->links() }}
    </div>
    @endif
</div>

@push('scripts')
<script>
// Reinitialize Lucide icons after Alpine.js loads
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
    
    // Reinitialize after a short delay to catch dynamically loaded content
    setTimeout(() => lucide.createIcons(), 500);
});
</script>
@endpush
@endsection
