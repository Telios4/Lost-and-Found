<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>@yield('title', 'Strathmore Lost & Found')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen flex flex-col">

    <!-- Navigation -->
    <nav class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">

                <!-- Logo -->
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                    <span class="text-blue-700 font-bold text-xl">SU</span>
                    <span class="font-semibold text-gray-800 text-sm">Lost &amp; Found</span>
                </a>

                <!-- Nav Links -->
                <div class="hidden sm:flex items-center gap-6 text-sm">
                    <a href="{{ route('dashboard') }}"
                       class="text-gray-600 hover:text-blue-700 {{ request()->routeIs('dashboard') ? 'text-blue-700 font-semibold' : '' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('reports.lost.create') }}"
                       class="text-gray-600 hover:text-blue-700 {{ request()->routeIs('reports.lost.create') ? 'text-blue-700 font-semibold' : '' }}">
                        Report Lost
                    </a>
                    <a href="{{ route('reports.found.create') }}"
                       class="text-gray-600 hover:text-blue-700 {{ request()->routeIs('reports.found.create') ? 'text-blue-700 font-semibold' : '' }}">
                        Report Found
                    </a>
                    <a href="{{ route('reports.my') }}"
                       class="text-gray-600 hover:text-blue-700 {{ request()->routeIs('reports.my') ? 'text-blue-700 font-semibold' : '' }}">
                        My Reports
                    </a>
                </div>

                <!-- User Menu -->
                <div class="flex items-center gap-4 text-sm">
                    <span class="text-gray-500">{{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="bg-blue-700 text-white px-4 py-1.5 rounded hover:bg-blue-800 transition">
                            Logout
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full mt-4">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif
    </div>

    <!-- Page Content -->
    <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full py-8">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 text-center text-xs text-gray-400 py-4">
        &copy; {{ date('Y') }} Strathmore University Lost &amp; Found System
    </footer>

</body>
</html>
