<!DOCTYPE html>
<html lang="fr" class="h-full bg-gray-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel - Sylva')</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Additional styles for admin theme -->
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="h-full bg-gray-900 text-white">
    <div class="flex h-full">
        <!-- Admin Sidebar -->
        <div class="hidden lg:flex lg:w-64 lg:flex-col lg:fixed lg:inset-y-0">
            <div class="flex flex-col flex-grow bg-gray-800 pt-5 pb-4 overflow-y-auto">
                <!-- Logo -->
                <div class="flex items-center flex-shrink-0 px-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-red-500 to-orange-600 rounded-xl flex items-center justify-center">
                            <i data-lucide="shield-check" class="w-6 h-6 text-white"></i>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-white">Admin Panel</h1>
                            <p class="text-xs text-gray-400">Sylva Management</p>
                        </div>
                    </div>
                </div>
                
                <!-- Navigation -->
                <nav class="mt-8 flex-1 px-2 space-y-1">
                    <!-- Dashboard -->
                    <a href="{{ route('admin.dashboard') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.dashboard') ? 'bg-red-700 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} transition-colors">
                        <i data-lucide="bar-chart-3" class="mr-3 h-5 w-5"></i>
                        Dashboard
                    </a>
                    
                    <!-- Users Management -->
                    <a href="{{ route('admin.users') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.users*') ? 'bg-red-700 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} transition-colors">
                        <i data-lucide="users" class="mr-3 h-5 w-5"></i>
                        Utilisateurs
                    </a>
                    
                    <!-- Reports Management -->
                    <a href="{{ route('admin.reports') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.reports*') ? 'bg-red-700 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} transition-colors">
                        <i data-lucide="flag" class="mr-3 h-5 w-5"></i>
                        Rapports
                    </a>
                    
                    <!-- Events Management -->
                    <a href="{{ route('admin.events') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.events*') ? 'bg-red-700 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} transition-colors">
                        <i data-lucide="calendar" class="mr-3 h-5 w-5"></i>
                        Événements
                    </a>
                    
                    <!-- Blog Management -->
                    <a href="{{ route('admin.blog') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.blog*') ? 'bg-red-700 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} transition-colors">
                        <i data-lucide="message-circle" class="mr-3 h-5 w-5"></i>
                        Blog
                    </a>
                    
                    <!-- Donations Management -->
                    <a href="{{ route('admin.donations') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.donations*') ? 'bg-red-700 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} transition-colors">
                        <i data-lucide="heart" class="mr-3 h-5 w-5"></i>
                        Donations
                    </a>
                    
                    <!-- Trees Management -->
                    <a href="{{ route('admin.trees.index') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.trees*') ? 'bg-red-700 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} transition-colors">
                        <i data-lucide="tree-pine" class="mr-3 h-5 w-5"></i>
                        Arbres
                    </a>
                    
                    <!-- Divider -->
                    <div class="border-t border-gray-700 my-4"></div>
                    
                    <!-- Settings -->
                    <a href="#" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-500 cursor-not-allowed"
                       title="Bientôt disponible">
                        <i data-lucide="settings" class="mr-3 h-5 w-5"></i>
                        Paramètres
                        <span class="ml-auto text-xs bg-gray-700 px-2 py-1 rounded">Bientôt</span>
                    </a>
                    
                    <!-- Back to User Site -->
                    <a href="{{ route('dashboard') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-300 hover:bg-gray-700 hover:text-white transition-colors">
                        <i data-lucide="arrow-left" class="mr-3 h-5 w-5"></i>
                        Retour Site
                    </a>
                </nav>
            </div>
        </div>

        <!-- Main content -->
        <div class="lg:pl-64 flex flex-col flex-1">
            <!-- Top header -->
            <div class="sticky top-0 z-50 flex-shrink-0 flex h-16 bg-gray-800 shadow-lg border-b border-gray-700">
                <!-- Mobile menu button -->
                <button type="button" class="px-4 border-r border-gray-700 text-gray-400 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-red-500 lg:hidden"
                        @click="open = !open">
                    <span class="sr-only">Open sidebar</span>
                    <i data-lucide="menu" class="h-6 w-6"></i>
                </button>
                
                <!-- Header content -->
                <div class="flex-1 px-4 flex justify-between items-center">
                    <div class="flex-1">
                        <h1 class="text-xl font-semibold text-white">@yield('page-title', 'Administration')</h1>
                        @hasSection('page-subtitle')
                        <p class="text-sm text-gray-400 mt-1">@yield('page-subtitle')</p>
                        @endif
                    </div>
                    
                    <!-- Admin profile dropdown -->
                    <div class="ml-4 relative flex-shrink-0" x-data="{ open: false }">
                        <button @click="open = !open" 
                                class="bg-gray-700 rounded-full flex text-sm text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-red-500">
                            <span class="sr-only">Open user menu</span>
                            <div class="w-8 h-8 bg-gradient-to-br from-red-500 to-orange-600 rounded-full flex items-center justify-center">
                                <span class="text-xs font-bold">{{ substr(Auth::user()->name, 0, 1) }}</span>
                            </div>
                        </button>
                        
                        <div x-show="open" @click.away="open = false" 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-gray-700 ring-1 ring-black ring-opacity-5 z-50">
                            <div class="py-1">
                                <div class="px-4 py-2 text-sm text-gray-300 border-b border-gray-600">
                                    <p class="font-medium">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-gray-400">Administrateur</p>
                                </div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" 
                                            class="w-full text-left px-4 py-2 text-sm text-gray-300 hover:bg-gray-600 transition-colors">
                                        <i data-lucide="log-out" class="w-4 h-4 inline mr-2"></i>
                                        Déconnexion
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page content -->
            <main class="flex-1 overflow-y-auto bg-gray-900">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Mobile sidebar overlay -->
    <div x-show="open" class="fixed inset-0 flex z-40 lg:hidden" x-data="{ open: false }">
        <div x-show="open" @click="open = false" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-600 bg-opacity-75"></div>
        
        <div x-show="open"
             x-transition:enter="transition ease-in-out duration-300 transform"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in-out duration-300 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full"
             class="relative flex-1 flex flex-col max-w-xs w-full bg-gray-800">
            <!-- Same sidebar content as desktop -->
        </div>
    </div>
</body>
</html>