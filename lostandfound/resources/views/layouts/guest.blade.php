<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>@yield('title', 'Strathmore Lost & Found')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="su-auth-page">

    <div class="su-auth-wrap">

        <!-- Logo / Header -->
        <div class="su-auth-header">
            <div class="su-auth-logo-mark">
                <span>SU</span>
            </div>
            <h1 class="su-auth-title">Strathmore Lost &amp; Found</h1>
            <p class="su-auth-subtitle">Strathmore University</p>
        </div>

        <!-- Card -->
        <div class="su-auth-card">
            @if(session('error'))
                <div class="su-alert su-alert--error">
                    {{ session('error') }}
                </div>
            @endif
            @yield('content')
        </div>

        <!-- Footer -->
        <p class="su-auth-footer">
            &copy; {{ date('Y') }} Strathmore University. All rights reserved.
        </p>

    </div>

</body>
</html>