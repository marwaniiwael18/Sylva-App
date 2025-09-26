<div class="bg-white border-b border-gray-200 px-6 py-4">
    <div class="flex items-center justify-between">
        <!-- Page Title/Breadcrumb -->
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">
                @yield('page-title', 'Dashboard')
            </h1>
            @hasSection('page-subtitle')
                <p class="text-sm text-gray-600 mt-1">@yield('page-subtitle')</p>
            @endif
        </div>

        <!-- Header Actions -->
        <div class="flex items-center gap-4">
            <!-- Notifications -->
            <button class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                <i data-lucide="bell" class="w-5 h-5"></i>
            </button>

            <!-- User Menu -->
            <div x-data="{ open: false }" class="relative">
                <button 
                    x-on:click="open = !open"
                    class="flex items-center gap-3 p-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
                >
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <i data-lucide="user" class="w-4 h-4 text-green-600"></i>
                    </div>
                    <span class="font-medium">{{ Auth::user()->name }}</span>
                    <i data-lucide="chevron-down" class="w-4 h-4" :class="open && 'rotate-180'" style="transition: transform 0.2s;"></i>
                </button>

                <!-- Dropdown Menu -->
                <div 
                    x-show="open" 
                    x-transition
                    x-on:click.away="open = false"
                    class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50"
                >
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        Profile Settings
                    </a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        Preferences
                    </a>
                    <hr class="my-2">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            Sign Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>