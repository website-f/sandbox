<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    x-data="{ 
        darkMode: localStorage.getItem('theme') === 'dark', 
        sidebarOpen: localStorage.getItem('sidebarOpen') !== 'false', 
        sidebarMobileOpen: false 
    }"
    x-init="
        $watch('darkMode', val => localStorage.setItem('theme', val ? 'dark' : 'light'));
        $watch('sidebarOpen', val => localStorage.setItem('sidebarOpen', val));
        if(darkMode) document.documentElement.classList.add('dark');
        document.documentElement.classList.remove('sidebar-closed');
    "
    :class="{ 'dark': darkMode }">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Sandbox Dashboard' }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.tailwindcss.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.tailwindcss.min.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        if (localStorage.getItem('sidebarOpen') === 'false') {
            document.documentElement.classList.add('sidebar-closed');
        }
    </script>
    <style>
        html.sidebar-closed aside {
            width: 5rem !important;
        }

        @media (min-width: 1024px) {
            html.sidebar-closed .main-transition {
                margin-left: 5rem !important;
            }

            html.sidebar-closed header {
                left: 5rem !important;
                width: calc(100% - 5rem) !important;
            }
        }

        [x-cloak] {
            display: none !important;
        }

        * {
            font-family: 'Inter', sans-serif;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        .dark ::-webkit-scrollbar-thumb {
            background: #475569;
        }

        /* Sidebar Transition */
        .sidebar-transition {
            transition: width 0.3s ease, transform 0.3s ease;
        }

        /* Main content transition */
        .main-transition {
            transition: margin-left 0.3s ease, padding-left 0.3s ease;
        }

        /* Card hover effects */
        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        /* Gradient backgrounds */
        .gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .gradient-success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }

        .gradient-warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .gradient-info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .gradient-dark {
            background: linear-gradient(135deg, #434343 0%, #000000 100%);
        }

        /* DataTables Dark Mode */
        .dark .dataTables_wrapper .dataTables_length,
        .dark .dataTables_wrapper .dataTables_filter,
        .dark .dataTables_wrapper .dataTables_info,
        .dark .dataTables_wrapper .dataTables_paginate {
            color: #e2e8f0;
        }

        .dark .dataTables_wrapper .dataTables_length select,
        .dark .dataTables_wrapper .dataTables_filter input {
            background-color: #374151;
            border-color: #4b5563;
            color: #e2e8f0;
        }

        .dark table.dataTable thead th,
        .dark table.dataTable thead td {
            border-bottom-color: #4b5563;
        }

        .dark table.dataTable tbody tr {
            background-color: #1f2937;
        }

        .dark table.dataTable tbody tr:hover {
            background-color: #374151 !important;
        }

        .dark table.dataTable tbody td {
            border-top-color: #374151;
        }

        .dark .dataTables_wrapper .dataTables_paginate .paginate_button {
            color: #e2e8f0 !important;
        }

        .dark .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #374151 !important;
            border-color: #4b5563 !important;
        }

        .dark .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #4f46e5 !important;
            border-color: #4f46e5 !important;
            color: white !important;
        }

        /* Sidebar menu item */
        .menu-item {
            position: relative;
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            margin: 0.25rem 0.75rem;
            border-radius: 0.75rem;
            color: #64748b;
            transition: all 0.2s ease;
        }

        .menu-item:hover {
            background-color: #f1f5f9;
            color: #334155;
        }

        .dark .menu-item:hover {
            background-color: #374151;
            color: #e2e8f0;
        }

        .menu-item.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .menu-item i {
            width: 24px;
            text-align: center;
        }

        /* Notification badge pulse */
        @keyframes pulse-ring {
            0% {
                transform: scale(0.8);
                opacity: 1;
            }

            100% {
                transform: scale(2);
                opacity: 0;
            }
        }

        .notification-badge::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 50%;
            background: inherit;
            animation: pulse-ring 1.5s infinite;
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 antialiased">
    <div class="flex min-h-screen">
        <!-- Sidebar Overlay (Mobile) -->
        <div x-show="sidebarMobileOpen"
            x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="sidebarMobileOpen = false"
            class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-40 lg:hidden"
            x-cloak>
        </div>

        <!-- Sidebar -->
        <aside :class="[
                   sidebarOpen ? 'w-72' : 'w-20',
                   sidebarMobileOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
               ]"
            class="fixed inset-y-0 left-0 z-50 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 sidebar-transition flex flex-col">

            <!-- Logo -->
            <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200 dark:border-gray-700">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
                    <img src="{{ asset('sandboxlogo.png') }}" class="h-10 w-auto" alt="Sandbox Logo">
                    <span x-show="sidebarOpen" x-transition class="text-xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                        Sandbox
                    </span>
                </a>
                <button @click="sidebarMobileOpen = false" class="lg:hidden p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    <i class="fas fa-times text-gray-500"></i>
                </button>
            </div>

            <!-- User Profile Mini -->
            <div class="p-4 border-b border-gray-200 dark:border-gray-700" x-show="sidebarOpen" x-transition>
                <div class="flex items-center space-x-3">
                    <div class="relative">
                        @if(Auth::user()->photo)
                        <img src="{{ asset('storage/' . Auth::user()->photo) }}" class="w-12 h-12 rounded-xl object-cover ring-2 ring-indigo-500/20">
                        @else
                        <div class="w-12 h-12 rounded-xl gradient-primary flex items-center justify-center">
                            <span class="text-white font-semibold">{{ substr(Auth::user()->name, 0, 1) }}</span>
                        </div>
                        @endif
                        <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white dark:border-gray-800 rounded-full"></span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ Auth::user()->email }}</p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto py-4">
                <div class="px-4 mb-2" x-show="sidebarOpen">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Main Menu</p>
                </div>

                <a href="{{ route('dashboard') }}"
                    class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i>
                    <span x-show="sidebarOpen" x-transition class="ml-3">Dashboard</span>
                </a>

                <a href="{{ route('profile.index') }}"
                    class="menu-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                    <i class="fas fa-user"></i>
                    <span x-show="sidebarOpen" x-transition class="ml-3">Profile</span>
                </a>

                <a href="{{ route('wallet.users.index') }}"
                    class="menu-item {{ request()->routeIs('wallet.*') ? 'active' : '' }}">
                    <i class="fas fa-wallet"></i>
                    <span x-show="sidebarOpen" x-transition class="ml-3">E-Wallet</span>
                </a>

                <a href="{{ route('collection.index') }}"
                    class="menu-item {{ request()->routeIs('collection.*') ? 'active' : '' }}">
                    <i class="fas fa-piggy-bank"></i>
                    <span x-show="sidebarOpen" x-transition class="ml-3">Tabung</span>
                </a>

                @if(auth()->user()->hasRole('Admin'))
                <div class="px-4 mt-6 mb-2" x-show="sidebarOpen">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Administration</p>
                </div>

                <a href="{{ route('admin.users.index') }}"
                    class="menu-item {{ request()->routeIs('admin.users.*') && !request()->routeIs('admin.users.blacklists') ? 'active' : '' }}">
                    <i class="fas fa-users-cog"></i>
                    <span x-show="sidebarOpen" x-transition class="ml-3">User Management</span>
                </a>

                <a href="{{ route('admin.users.blacklists') }}"
                    class="menu-item {{ request()->routeIs('admin.users.blacklists') ? 'active' : '' }}">
                    <i class="fas fa-ban"></i>
                    <span x-show="sidebarOpen" x-transition class="ml-3">Blacklist</span>
                </a>
                @endif
            </nav>

            <!-- Sidebar Footer -->
            <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="menu-item w-full text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20">
                        <i class="fas fa-sign-out-alt"></i>
                        <span x-show="sidebarOpen" x-transition class="ml-3">Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <div :class="sidebarOpen ? 'lg:ml-72' : 'lg:ml-20'" class="flex-1 main-transition">
            <!-- Top Header -->
            <header class="sticky top-0 z-30 bg-white/80 dark:bg-gray-800/80 backdrop-blur-md border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between h-16 px-4 lg:px-8">
                    <!-- Left side -->
                    <div class="flex items-center space-x-4">
                        <!-- Mobile menu button -->
                        <button @click="sidebarMobileOpen = true" class="lg:hidden p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                            <i class="fas fa-bars text-gray-500"></i>
                        </button>

                        <!-- Sidebar toggle (Desktop) -->
                        <button @click="sidebarOpen = !sidebarOpen" class="hidden lg:block p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                            <i class="fas fa-bars text-gray-500"></i>
                        </button>

                        <!-- Page Title -->
                        <div>
                            <h1 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $pageTitle ?? 'Dashboard' }}</h1>
                            @isset($breadcrumb)
                            <p class="text-sm text-gray-500">{{ $breadcrumb }}</p>
                            @endisset
                        </div>
                    </div>

                    <!-- Right side -->
                    <div class="flex items-center space-x-3">
                        <!-- Search -->
                        <div class="hidden md:block relative">
                            <input type="text" placeholder="Search..."
                                class="w-64 pl-10 pr-4 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        </div>

                        <!-- Theme Toggle -->
                        <button @click="darkMode = !darkMode"
                            class="p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <i x-show="!darkMode" class="fas fa-moon text-gray-500"></i>
                            <i x-show="darkMode" x-cloak class="fas fa-sun text-yellow-400"></i>
                        </button>

                        <!-- Notifications -->
                        <button class="relative p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <i class="fas fa-bell text-gray-500"></i>
                            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full notification-badge"></span>
                        </button>

                        <!-- User Dropdown -->
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="flex items-center space-x-3 p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                @if(Auth::user()->photo)
                                <img src="{{ asset('storage/' . Auth::user()->photo) }}" class="w-8 h-8 rounded-lg object-cover">
                                @else
                                <div class="w-8 h-8 rounded-lg gradient-primary flex items-center justify-center">
                                    <span class="text-white text-sm font-semibold">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                </div>
                                @endif
                                <span class="hidden md:block text-sm font-medium text-gray-700 dark:text-gray-200">{{ Auth::user()->name }}</span>
                                <i class="fas fa-chevron-down text-xs text-gray-400"></i>
                            </button>

                            <div x-show="open" @click.away="open = false" x-transition
                                x-cloak
                                class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 py-2">
                                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                                </div>
                                <a href="{{ route('profile.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <i class="fas fa-user w-5"></i>
                                    <span class="ml-2">Profile</span>
                                </a>
                                <a href="{{ route('wallet.users.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <i class="fas fa-wallet w-5"></i>
                                    <span class="ml-2">E-Wallet</span>
                                </a>
                                <div class="border-t border-gray-200 dark:border-gray-700 mt-2 pt-2">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">
                                            <i class="fas fa-sign-out-alt w-5"></i>
                                            <span class="ml-2">Logout</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-4 lg:p-8">
                <!-- Alert Messages -->
                @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                    class="mb-6 p-4 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                        <p class="text-green-700 dark:text-green-400">{{ session('success') }}</p>
                    </div>
                    <button @click="show = false" class="text-green-500 hover:text-green-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                @endif

                @if(session('error'))
                <div x-data="{ show: true }" x-show="show"
                    class="mb-6 p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                        <p class="text-red-700 dark:text-red-400">{{ session('error') }}</p>
                    </div>
                    <button @click="show = false" class="text-red-500 hover:text-red-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                @endif

                {{ $slot }}
            </main>

            <!-- Footer -->
            <footer class="border-t border-gray-200 dark:border-gray-700 py-4 px-4 lg:px-8">
                <div class="flex flex-col md:flex-row justify-between items-center text-sm text-gray-500">
                    <p>&copy; {{ date('Y') }} Sandbox. All rights reserved.</p>
                    <p class="mt-2 md:mt-0">Version 2.0</p>
                </div>
            </footer>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.tailwindcss.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

    <script>
        // Initialize theme on page load
        document.addEventListener('DOMContentLoaded', function() {
            if (localStorage.getItem('theme') === 'dark' ||
                (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        });

        // DataTables default configuration
        if (typeof $.fn.DataTable !== 'undefined') {
            $.extend(true, $.fn.dataTable.defaults, {
                responsive: true,
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
                dom: '<"flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-4"<"flex items-center gap-2"lB><"flex-1"f>>rtip',
                buttons: [{
                    extend: 'excel',
                    text: '<i class="fas fa-file-excel mr-2"></i>Export Excel',
                    className: 'bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors',
                    exportOptions: {
                        columns: ':visible:not(.no-export)'
                    }
                }],
                language: {
                    search: "",
                    searchPlaceholder: "Search...",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    paginate: {
                        first: '<i class="fas fa-angle-double-left"></i>',
                        last: '<i class="fas fa-angle-double-right"></i>',
                        next: '<i class="fas fa-angle-right"></i>',
                        previous: '<i class="fas fa-angle-left"></i>'
                    }
                },
                drawCallback: function() {
                    // Style the search input
                    $('.dataTables_filter input').addClass('pl-10 pr-4 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent');
                    // Style the length select
                    $('.dataTables_length select').addClass('rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500');
                }
            });
        }
    </script>

    @stack('scripts')
</body>

</html>