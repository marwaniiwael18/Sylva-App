<div class="bg-gradient-to-r from-emerald-50 to-green-50 border-b border-emerald-200 px-6 py-4 shadow-sm">
    <div class="flex items-center justify-between">
        <!-- Page Title/Breadcrumb -->
        <div class="flex items-center gap-4">
            <!-- Page Icon (dynamic based on current route) -->
            <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                @if(request()->routeIs('dashboard'))
                    <i data-lucide="layout-dashboard" class="w-5 h-5 text-white"></i>
                @elseif(request()->routeIs('map'))
                    <i data-lucide="map-pin" class="w-5 h-5 text-white"></i>
                @elseif(request()->routeIs('reports'))
                    <i data-lucide="clipboard-list" class="w-5 h-5 text-white"></i>
                @else
                    <i data-lucide="leaf" class="w-5 h-5 text-white"></i>
                @endif
            </div>
            <div>
                <h1 class="text-2xl font-bold text-emerald-900">
                    @yield('page-title', 'Dashboard')
                </h1>
                @hasSection('page-subtitle')
                    <p class="text-sm text-emerald-600 mt-1 flex items-center gap-1">
                        <i data-lucide="seedling" class="w-3 h-3"></i>
                        @yield('page-subtitle')
                    </p>
                @endif
            </div>
        </div>

        <!-- Header Actions -->
        <div class="flex items-center gap-3">
            <!-- Environmental Impact Stats -->
            <div class="hidden md:flex items-center gap-4 px-4 py-2 bg-white/70 backdrop-blur-sm rounded-xl border border-emerald-200">
                <div class="flex items-center gap-2 text-sm">
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                    <span class="text-emerald-700 font-medium">Active Reports</span>
                    <span class="text-emerald-900 font-bold">12</span>
                </div>
                <div class="w-px h-4 bg-emerald-300"></div>
                <div class="flex items-center gap-2 text-sm">
                    <i data-lucide="trees" class="w-3 h-3 text-green-600"></i>
                    <span class="text-emerald-700 font-medium">Trees Planted</span>
                    <span class="text-emerald-900 font-bold">847</span>
                </div>
            </div>

            <!-- Notifications -->
            <div class="relative">
                <button class="p-2.5 text-emerald-600 hover:text-emerald-700 hover:bg-emerald-100 rounded-xl transition-all duration-200 group">
                    <i data-lucide="bell" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                    <!-- Notification Badge -->
                    <span class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full flex items-center justify-center">
                        <span class="w-1.5 h-1.5 bg-white rounded-full"></span>
                    </span>
                </button>
            </div>

            <!-- User Menu -->
            <div x-data="{ open: false }" class="relative">
                <button 
                    x-on:click="open = !open"
                    class="flex items-center gap-3 p-2 text-emerald-700 hover:bg-emerald-100 rounded-xl transition-all duration-200 group"
                >
                    <div class="w-9 h-9 bg-gradient-to-br from-emerald-400 to-green-500 rounded-xl flex items-center justify-center shadow-md group-hover:shadow-lg transition-shadow">
                        <i data-lucide="user" class="w-4 h-4 text-white"></i>
                    </div>
                    <div class="hidden sm:block text-left">
                        <div class="font-semibold text-emerald-900">{{ Auth::user()->name }}</div>
                        <div class="text-xs text-emerald-600">Urban Gardener</div>
                    </div>
                    <i data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-200" :class="open && 'rotate-180'"></i>
                </button>

                <!-- Dropdown Menu -->
                <div 
                    x-show="open" 
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    x-on:click.away="open = false"
                    class="absolute right-0 mt-3 w-56 bg-white rounded-2xl shadow-xl border border-emerald-200 py-2 z-50 backdrop-blur-sm"
                >
                    <div class="px-4 py-3 border-b border-emerald-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-emerald-400 to-green-500 rounded-xl flex items-center justify-center">
                                <i data-lucide="user" class="w-4 h-4 text-white"></i>
                            </div>
                            <div>
                                <div class="font-semibold text-emerald-900">{{ Auth::user()->name }}</div>
                                <div class="text-sm text-emerald-600">{{ Auth::user()->email }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <a href="#" class="flex items-center gap-3 px-4 py-3 text-sm text-emerald-700 hover:bg-emerald-50 transition-colors">
                        <i data-lucide="settings" class="w-4 h-4"></i>
                        Profile Settings
                    </a>
                    <a href="#" class="flex items-center gap-3 px-4 py-3 text-sm text-emerald-700 hover:bg-emerald-50 transition-colors">
                        <i data-lucide="palette" class="w-4 h-4"></i>
                        Preferences
                    </a>
                    <a href="#" class="flex items-center gap-3 px-4 py-3 text-sm text-emerald-700 hover:bg-emerald-50 transition-colors">
                        <i data-lucide="help-circle" class="w-4 h-4"></i>
                        Help & Support
                    </a>
                    
                    <hr class="my-2 border-emerald-100">
                    
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition-colors">
                            <i data-lucide="log-out" class="w-4 h-4"></i>
                            Sign Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>