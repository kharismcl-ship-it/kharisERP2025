<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Kharis Hostels') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- @fluxAppearance --}}
    
    @livewireStyles
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        <nav class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <a href="{{ route('hostels.public.index') }}" class="text-xl font-bold text-indigo-600">
                                Kharis Hostels
                            </a>
                        </div>
                        <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                            <a href="{{ route('hostels.public.index') }}" class="border-indigo-500 text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Hostels
                            </a>
                        </div>
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:items-center">
                        @auth('hostel_occupant')
                            <a href="{{ route('hostel_occupant.dashboard') }}" class="ml-4 px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                                My Account
                            </a>
                        @else
                            <a href="{{ route('hostel_occupant.login') }}" class="ml-4 px-4 py-2 text-sm font-medium text-indigo-600 hover:text-indigo-900">
                                Login
                            </a>
                            <a href="{{ route('hostel_occupant.register') }}" class="ml-4 px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                                Register
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
        
        <!-- Footer -->
        <footer class="bg-white mt-12">
            <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div class="col-span-1 md:col-span-2">
                        <h3 class="text-lg font-semibold text-gray-900">Kharis Hostels</h3>
                        <p class="mt-4 text-base text-gray-500">
                            Providing quality accommodation for students and travelers since 2010.
                        </p>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Navigation</h3>
                        <ul class="mt-4 space-y-4">
                            <li><a href="{{ route('hostels.public.index') }}" class="text-base text-gray-500 hover:text-gray-900">Hostels</a></li>
                            <li><a href="#" class="text-base text-gray-500 hover:text-gray-900">About Us</a></li>
                            <li><a href="#" class="text-base text-gray-500 hover:text-gray-900">Contact</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Support</h3>
                        <ul class="mt-4 space-y-4">
                            <li><a href="#" class="text-base text-gray-500 hover:text-gray-900">Help Center</a></li>
                            <li><a href="#" class="text-base text-gray-500 hover:text-gray-900">Privacy Policy</a></li>
                            <li><a href="#" class="text-base text-gray-500 hover:text-gray-900">Terms of Service</a></li>
                        </ul>
                    </div>
                </div>
                <div class="mt-12 border-t border-gray-200 pt-8">
                    <p class="text-base text-gray-400 text-center">
                        &copy; {{ date('Y') }} Kharis Hostels. All rights reserved.
                    </p>
                </div>
            </div>
        </footer>
    </div>

    @fluxScripts
    @livewireScripts
</body>
</html>