<div class="flex flex-col h-full" x-data="{ user: null }" x-init="
    fetch('/api/user', {
        headers: {
            'Authorization': 'Bearer ' + localStorage.getItem('token'),
            'Accept': 'application/json'
        }
    }).then(r => r.json()).then(data => user = data).catch(() => {})
">
    <!-- Sidebar Header -->
    <div class="flex items-center justify-between p-4 border-b border-gray-200">
        <div class="flex items-center gap-3" x-show="!sidebarCollapsed">
            <div class="w-8 h-8 bg-green-600 rounded-lg flex items-center justify-center">
                <i data-lucide="leaf" class="w-5 h-5 text-white"></i>
            </div>
            <span class="font-bold text-xl text-gray-900">Sylva</span>
        </div>
        <button 
            x-on:click="sidebarCollapsed = !sidebarCollapsed"
            class="p-1.5 rounded-lg hover:bg-gray-100 transition-colors"
        >
            <i data-lucide="menu" class="w-5 h-5 text-gray-600"></i>
        </button>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 p-4">
        <ul class="space-y-2">
            <!-- Dashboard -->
            <li>
                <a href="{{ route('dashboard') }}" 
                   class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                   :class="sidebarCollapsed && 'justify-center'"
                >
                    <i data-lucide="home" class="w-5 h-5"></i>
                    <span x-show="!sidebarCollapsed">Dashboard</span>
                </a>
            </li>
            
            <!-- Map -->
            <li>
                <a href="{{ route('map') }}" 
                   class="nav-item {{ request()->routeIs('map') ? 'active' : '' }}"
                   :class="sidebarCollapsed && 'justify-center'"
                >
                    <i data-lucide="map" class="w-5 h-5"></i>
                    <span x-show="!sidebarCollapsed">Map</span>
                </a>
            </li>
            
            <!-- Reports -->
            <li>
                <a href="{{ route('reports') }}" 
                   class="nav-item {{ request()->routeIs('reports') ? 'active' : '' }}"
                   :class="sidebarCollapsed && 'justify-center'"
                >
                    <i data-lucide="flag" class="w-5 h-5"></i>
                    <span x-show="!sidebarCollapsed">Reports</span>
                </a>
            </li>
            
            <!-- Projects -->
            <li>
                <a href="{{ route('projects') }}" 
                   class="nav-item {{ request()->routeIs('projects*') ? 'active' : '' }}"
                   :class="sidebarCollapsed && 'justify-center'"
                >
                    <i data-lucide="folder" class="w-5 h-5"></i>
                    <span x-show="!sidebarCollapsed">Projects</span>
                </a>
            </li>
            
            <!-- Events -->
            <li>
                <a href="{{ route('events') }}" 
                   class="nav-item {{ request()->routeIs('events*') ? 'active' : '' }}"
                   :class="sidebarCollapsed && 'justify-center'"
                >
                    <i data-lucide="calendar" class="w-5 h-5"></i>
                    <span x-show="!sidebarCollapsed">Events</span>
                </a>
            </li>
            
            <!-- Feedback -->
            <li>
                <a href="{{ route('feedback') }}" 
                   class="nav-item {{ request()->routeIs('feedback') ? 'active' : '' }}"
                   :class="sidebarCollapsed && 'justify-center'"
                >
                    <i data-lucide="message-circle" class="w-5 h-5"></i>
                    <span x-show="!sidebarCollapsed">Feedback</span>
                </a>
            </li>
            
            <!-- Impact -->
            <li>
                <a href="{{ route('impact') }}" 
                   class="nav-item {{ request()->routeIs('impact') ? 'active' : '' }}"
                   :class="sidebarCollapsed && 'justify-center'"
                >
                    <i data-lucide="trending-up" class="w-5 h-5"></i>
                    <span x-show="!sidebarCollapsed">Impact</span>
                </a>
            </li>
        </ul>
    </nav>

    <!-- User Profile -->
    <div class="p-4 border-t border-gray-200">
        <div class="flex items-center gap-3" x-show="!sidebarCollapsed">
            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                <i data-lucide="user" class="w-4 h-4 text-green-600"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 truncate">{{ Auth::user()->name }}</p>
                <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
            </div>
        </div>
        <div class="flex justify-center" x-show="sidebarCollapsed">
            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                <i data-lucide="user" class="w-4 h-4 text-green-600"></i>
            </div>
        </div>
    </div>
</div>

<style>
.nav-item {
    @apply flex items-center gap-3 px-3 py-2 rounded-lg text-gray-700 hover:bg-green-50 hover:text-green-700 transition-colors;
}

.nav-item.active {
    @apply bg-green-100 text-green-700 font-medium;
}
</style>