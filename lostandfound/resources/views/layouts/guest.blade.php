<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>@yield('title', 'Strathmore Lost & Found')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

    <div class="w-full max-w-md px-4 py-8">

        <!-- Logo / Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 bg-blue-700 rounded-full mb-3">
                <span class="text-white font-bold text-xl">SU</span>
            </div>
            <h1 class="text-xl font-bold text-gray-800">Strathmore Lost &amp; Found</h1>
            <p class="text-sm text-gray-500 mt-1">Strathmore University</p>
        </div>

        <!-- Card -->
        <div class="bg-white rounded-xl shadow-md p-8">
            @yield('content')
        </div>

        <!-- Footer -->
        <p class="text-center text-xs text-gray-400 mt-6">
            &copy; {{ date('Y') }} Strathmore University. All rights reserved.
        </p>

    </div>

</body>
</html>