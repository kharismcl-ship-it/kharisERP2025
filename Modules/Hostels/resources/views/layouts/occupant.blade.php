<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Kharis ERP') }} — Hostel Portal</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        /* Sidebar layout — does not depend on Tailwind responsive compilation */
        #occupant-sidebar {
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 30;
            width: 16rem;
            display: flex;
            flex-direction: column;
            background: #fff;
            border-right: 1px solid #e5e7eb;
            transform: translateX(-100%);
            transition: transform 0.2s ease;
        }
        #occupant-sidebar.is-open {
            transform: translateX(0);
        }
        #occupant-main {
            display: flex;
            flex-direction: column;
            flex: 1;
            overflow: hidden;
            min-width: 0;
        }
        /* On desktop (≥1024px): sidebar is relative in the flex row */
        @media (min-width: 1024px) {
            #occupant-sidebar {
                position: relative;
                transform: translateX(0) !important;
                flex-shrink: 0;
            }
            #mobile-menu-btn { display: none; }
            #mobile-overlay { display: none !important; }
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">

<div class="flex overflow-hidden" style="height:100dvh" x-data="{ open: false }">

    {{-- Mobile overlay --}}
    <div id="mobile-overlay"
         x-show="open"
         x-transition:enter="transition-opacity duration-150"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="open = false"
         style="display:none;position:fixed;inset:0;z-index:20;background:rgba(0,0,0,.4)"></div>

    {{-- Sidebar --}}
    <aside id="occupant-sidebar" :class="open ? 'is-open' : ''">
        @livewire('hostels.hostel-occupant.navigation')
    </aside>

    {{-- Main area --}}
    <div id="occupant-main">

        {{-- Top bar --}}
        <header style="display:flex;height:3.5rem;flex-shrink:0;align-items:center;justify-content:space-between;border-bottom:1px solid #e5e7eb;background:#fff;padding:0 1rem;">
            <button id="mobile-menu-btn"
                    @click="open = !open"
                    style="border-radius:.375rem;padding:.5rem;color:#6b7280;background:transparent;border:none;cursor:pointer;"
                    onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='transparent'">
                <svg style="height:1.25rem;width:1.25rem" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            <div style="flex:1"></div>

            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                     style="border-radius:.5rem;background:#f0fdf4;border:1px solid #bbf7d0;padding:.375rem 1rem;font-size:.875rem;color:#166534;">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                     style="border-radius:.5rem;background:#fef2f2;border:1px solid #fecaca;padding:.375rem 1rem;font-size:.875rem;color:#991b1b;">
                    {{ session('error') }}
                </div>
            @endif
        </header>

        {{-- Content --}}
        <main style="flex:1;overflow-y:auto;padding:1.5rem;">
            {{ $slot }}
        </main>

    </div>
</div>

@livewireScripts
</body>
</html>
