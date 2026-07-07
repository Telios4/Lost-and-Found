<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>@yield('title', 'Admin | Strathmore Lost & Found')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="su-bg-surface su-text-body">
    <div class="su-shell">
        <aside class="su-sidebar su-sidebar--admin">
            <a href="{{ route('admin.dashboard') }}" class="su-logo su-logo--sidebar">
                <span class="su-logo-mark"><span class="su-logo-su">SU</span></span>
                <span class="su-logo-text">
                    <span class="su-logo-name">Admin Portal</span>
                    <span class="su-logo-sub">Strathmore University</span>
                </span>
            </a>

            <nav class="su-side-nav" aria-label="Admin Navigation">
                <a href="{{ route('admin.dashboard') }}"
                   class="su-side-link {{ request()->routeIs('admin.dashboard') ? 'su-side-link--active' : '' }}">
                    Dashboard
                </a>
                <a href="{{ route('admin.reports.index') }}"
                   class="su-side-link {{ request()->routeIs('admin.reports.index') || request()->routeIs('admin.reports.show') ? 'su-side-link--active' : '' }}">
                    Manage Reports
                </a>
                <a href="{{ route('admin.reports.lost.create') }}"
                   class="su-side-link {{ request()->routeIs('admin.reports.lost.*') ? 'su-side-link--active' : '' }}">
                    Post Lost
                </a>
                <a href="{{ route('admin.reports.found.create') }}"
                   class="su-side-link {{ request()->routeIs('admin.reports.found.*') ? 'su-side-link--active' : '' }}">
                    Post Found
                </a>
            </nav>

            <div class="su-sidebar-user">
                <p class="su-sidebar-user-label">Signed in as</p>
                <p class="su-sidebar-user-name">{{ session('admin_name', 'Administrator') }}</p>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="su-btn-logout su-btn-logout--full">Logout</button>
                </form>
            </div>
        </aside>

        <div class="su-shell-main">
            <div class="su-flash-wrap su-flash-wrap--shell">
                @if(session('success'))
                    <div class="su-alert su-alert--success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="su-alert su-alert--error">{{ session('error') }}</div>
                @endif
            </div>

            <main class="su-main su-main--panel">
                @yield('content')
            </main>

            <footer class="su-footer su-footer--shell">
                <div class="su-footer-inner">
                    <div class="su-footer-brand">
                        <span class="su-footer-logo-mark">SU</span>
                        <span class="su-footer-name">Strathmore Lost &amp; Found Admin</span>
                    </div>
                    <p class="su-footer-copy">&copy; {{ date('Y') }} Strathmore University</p>
                    <p class="su-footer-tagline">Service and Excellence</p>
                </div>
            </footer>
        </div>
    </div>

</body>
</html>
