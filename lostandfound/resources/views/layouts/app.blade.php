<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>@yield('title', 'Strathmore Lost & Found')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="su-bg-surface su-text-body">
    <div class="su-shell">
        <aside class="su-sidebar">
            <a href="{{ route('dashboard') }}" class="su-logo su-logo--sidebar">
                <span class="su-logo-mark"><span class="su-logo-su">SU</span></span>
                <span class="su-logo-text">
                    <span class="su-logo-name">Lost &amp; Found</span>
                    <span class="su-logo-sub">Strathmore University</span>
                </span>
            </a>

            <nav class="su-side-nav" aria-label="Main Navigation">
                <a href="{{ route('dashboard') }}"
                   class="su-side-link {{ request()->routeIs('dashboard') ? 'su-side-link--active' : '' }}">
                    Dashboard
                </a>
                <a href="{{ route('reports.lost.create') }}"
                   class="su-side-link {{ request()->routeIs('reports.lost.create') ? 'su-side-link--active' : '' }}">
                    Report Lost
                </a>
                <a href="{{ route('reports.found.create') }}"
                   class="su-side-link {{ request()->routeIs('reports.found.create') ? 'su-side-link--active' : '' }}">
                    Report Found
                </a>
                <a href="{{ route('reports.my') }}"
                   class="su-side-link {{ request()->routeIs('reports.my') ? 'su-side-link--active' : '' }}">
                    My Reports
                </a>
                <a href="{{ route('reports.search') }}"
                   class="su-side-link {{ request()->routeIs('reports.search') ? 'su-side-link--active' : '' }}">
                    Search &amp; Matches
                </a>
                <a href="{{ route('notifications.index') }}"
                   class="su-side-link {{ request()->routeIs('notifications.*') ? 'su-side-link--active' : '' }}">
                    Notifications
                    @if(($notificationCount ?? 0) > 0)
                        <span class="su-nav-pill">{{ $notificationCount }}</span>
                    @endif
                </a>
                <a href="{{ route('messages.index') }}"
                   class="su-side-link {{ request()->routeIs('messages.*') ? 'su-side-link--active' : '' }}">
                    Messages
                    @if(($messageCount ?? 0) > 0)
                        <span class="su-nav-pill">{{ $messageCount }}</span>
                    @endif
                </a>
            </nav>

            <div class="su-sidebar-user">
                <p class="su-sidebar-user-label">Signed in as</p>
                <p class="su-sidebar-user-name">{{ Auth::user()->name }}</p>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="su-btn-logout su-btn-logout--full">Logout</button>
                </form>
            </div>
        </aside>

        <div class="su-shell-main">
            <div class="su-flash-wrap su-flash-wrap--shell">
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

            <main class="su-main su-main--panel">
                @yield('content')
            </main>

            <footer class="su-footer su-footer--shell">
                <div class="su-footer-inner">
                    <div class="su-footer-brand">
                        <span class="su-footer-logo-mark">SU</span>
                        <span class="su-footer-name">Strathmore Lost &amp; Found</span>
                    </div>
                    <p class="su-footer-copy">&copy; {{ date('Y') }} Strathmore University Lost &amp; Found System</p>
                    <p class="su-footer-tagline">Service and Excellence</p>
                </div>
            </footer>
        </div>
    </div>

</body>
</html>