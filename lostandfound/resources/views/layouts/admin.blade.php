<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>@yield('title', 'Admin | Strathmore Lost & Found')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="su-bg-surface su-text-body">

    <nav class="su-nav">
        <div class="su-container">
            <div class="su-nav-inner">
                <a href="{{ route('admin.dashboard') }}" class="su-logo">
                    <span class="su-logo-mark"><span class="su-logo-su">SU</span></span>
                    <span class="su-logo-text">
                        <span class="su-logo-name">Admin Portal</span>
                        <span class="su-logo-sub">Strathmore University</span>
                    </span>
                </a>

                <div class="su-nav-links">
                    <a href="{{ route('admin.dashboard') }}"
                       class="su-nav-link {{ request()->routeIs('admin.dashboard') ? 'su-nav-link--active' : '' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('admin.reports.index') }}"
                       class="su-nav-link {{ request()->routeIs('admin.reports.index') || request()->routeIs('admin.reports.show') ? 'su-nav-link--active' : '' }}">
                        Manage Reports
                    </a>
                    <a href="{{ route('admin.reports.lost.create') }}"
                       class="su-nav-link {{ request()->routeIs('admin.reports.lost.*') ? 'su-nav-link--active' : '' }}">
                        Post Lost
                    </a>
                    <a href="{{ route('admin.reports.found.create') }}"
                       class="su-nav-link {{ request()->routeIs('admin.reports.found.*') ? 'su-nav-link--active' : '' }}">
                        Post Found
                    </a>
                </div>

                <div class="su-nav-user">
                    <span class="su-nav-username">{{ session('admin_name', 'Administrator') }}</span>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="su-btn-logout">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="su-container su-flash-wrap">
        @if(session('success'))
            <div class="su-alert su-alert--success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="su-alert su-alert--error">{{ session('error') }}</div>
        @endif
    </div>

    <main class="su-main su-container">
        @yield('content')
    </main>

    <footer class="su-footer">
        <div class="su-container su-footer-inner">
            <div class="su-footer-brand">
                <span class="su-footer-logo-mark">SU</span>
                <span class="su-footer-name">Strathmore Lost &amp; Found Admin</span>
            </div>
            <p class="su-footer-copy">&copy; {{ date('Y') }} Strathmore University</p>
            <p class="su-footer-tagline">Service and Excellence</p>
        </div>
    </footer>

</body>
</html>
