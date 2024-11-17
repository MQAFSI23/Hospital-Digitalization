<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.2/dist/alpine.min.js" defer></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex min-h-screen bg-gray-100">
    <!-- Sidebar -->
    @include('partials.sidebar')

    <!-- Main Content with Scrollable Area -->
    <div class="flex-1 p-6 overflow-y-auto max-h-screen">
        @yield('content')
    </div>
</body>
</html>