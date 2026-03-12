<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'Kharis ERP') }} — Hostel Portal</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-gray-50 antialiased font-sans">

    <div class="flex min-h-screen flex-col items-center justify-center gap-6 p-6 md:p-10">
        <div class="flex w-full max-w-sm flex-col gap-6">

            <!-- Logo / App name -->
            <a href="{{ route('home') }}" class="flex flex-col items-center gap-2">
                <span class="text-xl font-semibold tracking-tight text-gray-900">
                    {{ config('app.name', 'Kharis ERP') }}
                </span>
                <span class="text-sm text-gray-500">Hostel Resident Portal</span>
            </a>

            <!-- Card -->
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="px-8 py-8">
                    {{ $slot }}
                </div>
            </div>

        </div>
    </div>

    @livewireScripts
</body>
</html>
