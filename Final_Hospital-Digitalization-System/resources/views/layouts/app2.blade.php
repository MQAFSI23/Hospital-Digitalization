<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>

    <!-- Alpine.js for interactivity -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.2/dist/alpine.min.js" defer></script>

    <link rel="icon" type="image/png" href="{{ asset('/images/hospitalLogo.png') }}">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100">
    <div x-data="{ open: false }" class="lg:flex h-screen">
        <aside :class="open ? 'block' : 'hidden lg:block'" 
            class="fixed lg:relative z-20 bg-gray-800 text-white h-screen overflow-y-auto">
            @include('partials.sidebar')
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col">

            <header class="bg-indigo-950 p-4 flex items-center justify-between lg:hidden sticky top-0 z-30">
                <!-- Button for Opening/Closing Sidebar -->
                <button @click="open = !open" class="text-gray-800 focus:outline-none">
                    <img src="{{ asset('images/sidebar-btn.png') }}" alt="Menu" class="h-6 w-6">
                </button>
                <h2 class="text-3xl font-bold text-white">Hozpitalz</h2>
            </header>

            <!-- Main Content Area with Padding and Responsive Layout -->
            <main class="flex-1 p-6 overflow-y-auto h-full bg-gray-50">
                <div class="max-w-7xl mx-auto">
                    @yield('content')
                </div>
            </main>
        </div>

        <!-- Overlay for Mobile Sidebar -->
        <div 
            x-show="open" 
            @click="open = false" 
            class="fixed inset-0 bg-black bg-opacity-50 z-10 lg:hidden">
        </div>
    </div>

</body>
</html>