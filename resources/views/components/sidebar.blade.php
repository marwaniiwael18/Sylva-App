<div class="sidebar-container flex flex-col h-full bg-gradient-to-b from-emerald-900 via-emerald-800 to-green-900 text-white relative overflow-hidden" x-data="{ user: null, sidebarCollapsed: false }" x-init="
    fetch('/api/user', {
        headers: {
            'Authorization': 'Bearer ' + localStorage.getItem('token'),
            'Accept': 'application/json'
        }
    }).then(r => r.json()).then(data => user = data).catch(() => {})
" :class="sidebarCollapsed ? 'w-16' : 'w-64'">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-10 left-4 w-20 h-20 border border-emerald-300 rounded-full"></div>
        <div class="absolute top-32 right-6 w-12 h-12 border border-green-300 rounded-full"></div>
        <div class="absolute bottom-32 left-8 w-16 h-16 border border-emerald-400 rounded-full"></div>
        <div class="absolute bottom-48 right-4 w-8 h-8 border border-green-400 rounded-full"></div>
    </div>

    <!-- Sidebar Header -->
    <div class="relative z-10 flex items-center justify-between p-4 border-b border-emerald-700/50 min-h-[80px]">
        <div class="flex items-center gap-3" x-show="!sidebarCollapsed" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            <div class="w-10 h-10 bg-gradient-to-br from-emerald-400 to-green-500 rounded-2xl flex items-center justify-center shadow-xl flex-shrink-0">
                <i data-lucide="leaf" class="w-5 h-5 text-white"></i>
            </div>
            <div class="min-w-0">
                <span class="font-bold text-xl text-white block">Sylva</span>
                <p class="text-xs text-emerald-200 font-medium">Urban Greening Platform</p>
            </div>
        </div>
        <div x-show="sidebarCollapsed" class="flex justify-center w-full" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            <div class="w-8 h-8 bg-gradient-to-br from-emerald-400 to-green-500 rounded-xl flex items-center justify-center shadow-xl">
                <i data-lucide="leaf" class="w-4 h-4 text-white"></i>
            </div>
        </div>
        <button 
            x-on:click="sidebarCollapsed = !sidebarCollapsed"
            class="sidebar-toggle"
            :class="sidebarCollapsed && 'absolute top-4 right-4'"
        >
            <i data-lucide="menu" class="w-5 h-5 text-emerald-200 group-hover:text-white transition-colors"></i>
        </button>
    </div>

    <!-- Navigation -->
    <nav class="relative z-10 flex-1 p-4 overflow-y-auto">
        <ul class="space-y-2">
            <!-- Dashboard -->
            <li>
                <a href="{{ route('dashboard') }}" 
                   class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                   :class="sidebarCollapsed && 'justify-center'"
                >
                    <i data-lucide="layout-dashboard" class="w-5 h-5 flex-shrink-0"></i>
                    <span x-show="!sidebarCollapsed" class="nav-text">Dashboard</span>
                </a>
            </li>
            
            <!-- Tree Management -->
            <li>
                <a href="{{ route('trees.index') }}" 
                   class="nav-item {{ request()->routeIs('trees.*') ? 'active' : '' }}"
                   :class="sidebarCollapsed && 'justify-center'"
                >
                    <i data-lucide="tree-pine" class="w-5 h-5 flex-shrink-0"></i>
                    <span x-show="!sidebarCollapsed" class="nav-text">Tree Management</span>
                </a>
            </li>
            
            <!-- Reports -->
            <li>
                <a href="{{ route('reports') }}" 
                   class="nav-item {{ request()->routeIs('reports') ? 'active' : '' }}"
                   :class="sidebarCollapsed && 'justify-center'"
                >
                    <i data-lucide="clipboard-list" class="w-5 h-5 flex-shrink-0"></i>
                    <span x-show="!sidebarCollapsed" class="nav-text">Reports</span>
                    <span x-show="!sidebarCollapsed" class="nav-count">
                        <span class="bg-emerald-400 text-emerald-900 text-xs font-bold px-2 py-0.5 rounded-full">12</span>
                    </span>
                </a>
            </li>

            <!-- Community Feed -->
            <li>
                <a href="{{ route('community.feed') }}" 
                   class="nav-item {{ request()->routeIs('community.feed') ? 'active' : '' }}"
                   :class="sidebarCollapsed && 'justify-center'"
                >
                    <i data-lucide="messages-square" class="w-5 h-5 flex-shrink-0"></i>
                    <span x-show="!sidebarCollapsed" class="nav-text">Community Feed</span>
                    <span x-show="!sidebarCollapsed" class="nav-count">
                        <span class="bg-purple-400 text-purple-900 text-xs font-bold px-2 py-0.5 rounded-full">{{ \App\Models\ReportActivity::where('activity_type', 'comment')->count() }}</span>
                    </span>
                </a>
            </li>

            <!-- Events -->
            <li>
                <a href="{{ route('events.index') }}" 
                   class="nav-item {{ request()->routeIs('events.*') ? 'active' : '' }}"
                   :class="sidebarCollapsed && 'justify-center'"
                >
                    <i data-lucide="calendar" class="w-5 h-5 flex-shrink-0"></i>
                    <span x-show="!sidebarCollapsed" class="nav-text">Events</span>
                    <span x-show="!sidebarCollapsed" class="nav-count">
                        <span class="bg-blue-400 text-blue-900 text-xs font-bold px-2 py-0.5 rounded-full">{{ \App\Models\Event::where('date', '>', now())->count() }}</span>
                    </span>
                </a>
            </li>

            <!-- Donations -->
            <li>
                <a href="{{ route('donations.index') }}" 
                   class="nav-item"
                   :class="sidebarCollapsed && 'justify-center'"
                >
                    <i data-lucide="heart" class="w-5 h-5 flex-shrink-0"></i>
                    <span x-show="!sidebarCollapsed" class="nav-text">Donations</span>
                </a>
            </li>

            <!-- Forum -->
            <li>
                <a href="{{ route('forum.index') }}" 
                   class="nav-item {{ request()->routeIs('forum.*') ? 'active' : '' }}"
                   :class="sidebarCollapsed && 'justify-center'"
                >
                    <i data-lucide="message-circle" class="w-5 h-5 flex-shrink-0"></i>
                    <span x-show="!sidebarCollapsed" class="nav-text">Forum</span>
                    <span x-show="!sidebarCollapsed" class="nav-count">
                        <span class="bg-orange-400 text-orange-900 text-xs font-bold px-2 py-0.5 rounded-full">24</span>
                    </span>
                </a>
            </li>
        </ul>
    </nav>

    <!-- User Profile -->
    <div class="relative z-10 p-4 border-t border-emerald-700/50">
        <div class="bg-emerald-800/40 backdrop-blur-sm rounded-2xl p-3 border border-emerald-700/30">
            <div class="flex items-center gap-3" x-show="!sidebarCollapsed" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                <div class="w-10 h-10 bg-gradient-to-br from-emerald-400 to-green-500 rounded-2xl flex items-center justify-center shadow-lg flex-shrink-0">
                    <i data-lucide="user" class="w-4 h-4 text-white"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-white truncate">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-emerald-300 truncate">{{ Auth::user()->email }}</p>
                    <div class="flex items-center gap-1 mt-1">
                        <div class="w-1.5 h-1.5 bg-green-400 rounded-full flex-shrink-0"></div>
                        <span class="text-xs text-green-300 font-medium">Active</span>
                    </div>
                </div>
            </div>
            <div class="flex justify-center" x-show="sidebarCollapsed" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                <div class="w-8 h-8 bg-gradient-to-br from-emerald-400 to-green-500 rounded-2xl flex items-center justify-center shadow-lg">
                    <i data-lucide="user" class="w-4 h-4 text-white"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.nav-item {
    @apply flex items-center gap-3 px-3 py-3 rounded-xl text-emerald-200 hover:bg-emerald-700/50 hover:text-white transition-all duration-200 group relative;
    min-height: 48px;
    align-items: center !important;
}

.nav-item.active {
    @apply bg-gradient-to-r from-emerald-600 to-green-600 text-white font-semibold shadow-lg;
}

.nav-item.active::before {
    content: '';
    position: absolute;
    left: -3px;
    top: 50%;
    transform: translateY(-50%);
    width: 3px;
    height: 20px;
    background: linear-gradient(to bottom, #10b981, #059669);
    border-radius: 2px;
}

.nav-item i {
    flex-shrink: 0;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.nav-text {
    flex: 1;
    font-weight: 500;
    font-size: 14px;
    line-height: 1.4;
    margin: 0;
    padding: 0;
    display: flex;
    align-items: center;
}

.nav-count {
    opacity: 0.9;
    flex-shrink: 0;
    display: flex;
    align-items: center;
}

/* Sidebar collapse button styling */
.sidebar-toggle {
    @apply p-2 rounded-xl hover:bg-emerald-700/50 transition-all duration-200 group flex-shrink-0;
}

/* Make sidebar non-sticky and properly collapsible */
.sidebar-container {
    position: relative;
    transition: width 0.3s ease-in-out;
}

/* Scrollbar Styling */
nav::-webkit-scrollbar {
    width: 4px;
}

nav::-webkit-scrollbar-track {
    background: rgba(6, 78, 59, 0.3);
    border-radius: 2px;
}

nav::-webkit-scrollbar-thumb {
    background: rgba(16, 185, 129, 0.5);
    border-radius: 2px;
}

nav::-webkit-scrollbar-thumb:hover {
    background: rgba(16, 185, 129, 0.7);
}
</style>