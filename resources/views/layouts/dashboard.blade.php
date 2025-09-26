@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50" x-data="{ sidebarCollapsed: false }">
    <!-- Sidebar -->
    <div class="bg-white border-r border-gray-200 flex-shrink-0 transition-all duration-300" 
         :class="sidebarCollapsed ? 'w-16' : 'w-64'">
        @include('components.sidebar')
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Header -->
        @include('components.header')
        
        <!-- Page Content -->
        <main class="flex-1 overflow-auto">
            <div class="h-full">
                @yield('page-content')
            </div>
        </main>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Initialize Lucide icons
    lucide.createIcons();
</script>
@endpush