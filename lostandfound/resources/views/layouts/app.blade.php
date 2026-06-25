<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>@yield('title', 'Strathmore Lost & Found')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="su-bg-surface su-text-body">

    <!-- Navigation -->
    <nav class="su-nav">
        <div class="su-container">
            <div class="su-nav-inner">

                <!-- Logo -->
                <a href="{{ route('dashboard') }}" class="su-logo">
                    <span class="su-logo-mark"><span class="su-logo-su">SU</span></span>
                    <span class="su-logo-text">
                        <span class="su-logo-name">Lost &amp; Found</span>
                        <span class="su-logo-sub">Strathmore University</span>
                    </span>
                </a>

                <!-- Nav Links -->
                <div class="su-nav-links">
                    <a href="{{ route('dashboard') }}"
                       class="su-nav-link {{ request()->routeIs('dashboard') ? 'su-nav-link--active' : '' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('reports.lost.create') }}"
                       class="su-nav-link {{ request()->routeIs('reports.lost.create') ? 'su-nav-link--active' : '' }}">
                        Report Lost
                    </a>
                    <a href="{{ route('reports.found.create') }}"
                       class="su-nav-link {{ request()->routeIs('reports.found.create') ? 'su-nav-link--active' : '' }}">
                        Report Found
                    </a>
                    <a href="{{ route('reports.my') }}"
                       class="su-nav-link {{ request()->routeIs('reports.my') ? 'su-nav-link--active' : '' }}">
                        My Reports
                    </a>
                    <a href="{{ route('reports.search') }}"
                       class="su-nav-link {{ request()->routeIs('reports.search') ? 'su-nav-link--active' : '' }}">
                        Search &amp; Matches
                    </a>
                </div>

                <!-- User Menu -->
                <div class="su-nav-user">
                    <span class="su-nav-username">{{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="su-btn-logout">
                            Logout
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <div class="su-container su-flash-wrap">
        @if(session('success'))
            <div class="su-alert su-alert--success">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="su-alert su-alert--error">
                {{ session('error') }}
            </div>
        @endif
    </div>

    <!-- Page Content -->
    <main class="su-main su-container">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="su-footer">
        <div class="su-container su-footer-inner">
            <div class="su-footer-brand">
                <span class="su-footer-logo-mark">SU</span>
                <span class="su-footer-name">Strathmore Lost &amp; Found</span>
            </div>
            <p class="su-footer-copy">&copy; {{ date('Y') }} Strathmore University Lost &amp; Found System</p>
            <p class="su-footer-tagline">Service and Excellence</p>
        </div>
    </footer>

</body>
</html>