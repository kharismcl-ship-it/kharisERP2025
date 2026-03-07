<div
    class="min-h-screen flex flex-col"
    x-data="{
        autoResetTimer: null,
        startAutoReset(seconds) {
            clearTimeout(this.autoResetTimer);
            this.autoResetTimer = setTimeout(() => { $wire.resetKiosk() }, seconds * 1000);
        },
        cancelAutoReset() {
            clearTimeout(this.autoResetTimer);
        }
    }"
>
    {{-- ══════════════════════════════════════════════════════
         HEADER — shown on all screens
    ══════════════════════════════════════════════════════ --}}
    <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-sm">
        <div class="max-w-5xl mx-auto px-6 py-4 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-primary-600 dark:text-primary-400">
                    Visitor Check-In
                </p>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white leading-tight">
                    {{ $companyName }}
                </h1>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500 dark:text-gray-400"
                   x-data="{ now: new Date() }"
                   x-init="setInterval(() => now = new Date(), 1000)"
                >
                    <span class="block text-lg font-semibold text-gray-700 dark:text-gray-200"
                          x-text="now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })">
                    </span>
                    <span class="block text-xs"
                          x-text="now.toLocaleDateString([], { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })">
                    </span>
                </p>
            </div>
        </div>
    </header>

    {{-- ══════════════════════════════════════════════════════
         SCREEN: WELCOME — two big check-in cards
    ══════════════════════════════════════════════════════ --}}
    @if ($screen === 'welcome')
        <main class="flex-1 flex flex-col items-center justify-center px-6 py-12">
            <div class="w-full max-w-3xl space-y-8">
                <div class="text-center">
                    <p class="text-4xl font-extrabold text-gray-900 dark:text-white">
                        Welcome!
                    </p>
                    <p class="mt-2 text-lg text-gray-500 dark:text-gray-400">
                        Please select your visit type to begin.
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    {{-- New Visitor card --}}
                    <button
                        wire:click="startCheckIn('new')"
                        wire:loading.attr="disabled"
                        type="button"
                        class="group relative flex flex-col items-center justify-center gap-4 rounded-2xl
                               bg-white dark:bg-gray-800 border-2 border-primary-200 dark:border-primary-700
                               p-10 text-center shadow-md
                               hover:border-primary-500 hover:shadow-xl hover:scale-[1.02]
                               active:scale-100
                               transition-all duration-200 cursor-pointer"
                    >
                        <div class="flex items-center justify-center w-20 h-20 rounded-full
                                    bg-primary-100 dark:bg-primary-900 text-primary-600 dark:text-primary-300
                                    group-hover:bg-primary-200 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">First Visit</p>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                I have not visited before
                            </p>
                        </div>
                        <div wire:loading wire:target="startCheckIn('new')" class="absolute inset-0 flex items-center justify-center rounded-2xl bg-white/80 dark:bg-gray-800/80">
                            <x-filament::loading-indicator class="h-8 w-8 text-primary-600" />
                        </div>
                    </button>

                    {{-- Returning Visitor card --}}
                    <button
                        wire:click="startCheckIn('returning')"
                        wire:loading.attr="disabled"
                        type="button"
                        class="group relative flex flex-col items-center justify-center gap-4 rounded-2xl
                               bg-white dark:bg-gray-800 border-2 border-success-200 dark:border-success-700
                               p-10 text-center shadow-md
                               hover:border-success-500 hover:shadow-xl hover:scale-[1.02]
                               active:scale-100
                               transition-all duration-200 cursor-pointer"
                    >
                        <div class="flex items-center justify-center w-20 h-20 rounded-full
                                    bg-success-100 dark:bg-success-900 text-success-600 dark:text-success-300
                                    group-hover:bg-success-200 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">Returning Visitor</p>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                I have visited before
                            </p>
                        </div>
                        <div wire:loading wire:target="startCheckIn('returning')" class="absolute inset-0 flex items-center justify-center rounded-2xl bg-white/80 dark:bg-gray-800/80">
                            <x-filament::loading-indicator class="h-8 w-8 text-success-600" />
                        </div>
                    </button>
                </div>

                <p class="text-center text-xs text-gray-400 dark:text-gray-600">
                    Need help? Please ask the receptionist.
                </p>
            </div>
        </main>
    @endif

    {{-- ══════════════════════════════════════════════════════
         SCREEN: FORM — Filament wizard (reused from admin)
    ══════════════════════════════════════════════════════ --}}
    @if ($screen === 'form')
        <main class="flex-1 flex flex-col items-center justify-start px-6 py-8">
            <div class="w-full max-w-4xl space-y-4">

                {{-- Back link --}}
                <div>
                    <button
                        wire:click="backToWelcome"
                        type="button"
                        class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400
                               hover:text-primary-600 dark:hover:text-primary-400 transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                        </svg>
                        Back to welcome
                    </button>
                </div>

                {{-- Form card --}}
                <div class="kiosk-form-wrap bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 pt-6 pb-2 border-b border-gray-100 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Visitor Registration
                        </h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Please fill in your details below. Fields marked * are required.
                        </p>
                    </div>

                    <div class="p-6">
                        {{ $this->form }}
                    </div>
                </div>
            </div>
        </main>
    @endif

    {{-- ══════════════════════════════════════════════════════
         SCREEN: SUCCESS — check-in confirmation + QR codes
    ══════════════════════════════════════════════════════ --}}
    @if ($screen === 'success')
        <main
            class="flex-1 flex flex-col items-center justify-center px-6 py-10"
            x-init="startAutoReset(60)"
            x-on:click="startAutoReset(60)"
        >
            <div class="w-full max-w-2xl space-y-6">

                {{-- Green tick + message --}}
                <div class="text-center space-y-3">
                    <div class="flex items-center justify-center">
                        <div class="flex items-center justify-center w-20 h-20 rounded-full
                                    bg-success-100 dark:bg-success-900 text-success-600 dark:text-success-300
                                    ring-8 ring-success-50 dark:ring-success-950">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>

                    <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white">You're checked in!</h2>
                    @if ($checkedInName)
                        <p class="text-lg text-gray-600 dark:text-gray-300">
                            Welcome, <span class="font-semibold text-gray-900 dark:text-white">{{ $checkedInName }}</span>
                        </p>
                    @endif
                    @if ($badgeCode)
                        <p class="inline-flex items-center gap-2 rounded-full
                                   bg-primary-100 dark:bg-primary-900 px-4 py-1.5
                                   text-sm font-bold text-primary-700 dark:text-primary-200 tracking-wide">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z" />
                            </svg>
                            Badge {{ $badgeCode }}
                        </p>
                    @else
                        <p class="text-sm text-warning-600 dark:text-warning-400">
                            No badge available. Please collect one from the reception desk.
                        </p>
                    @endif
                </div>

                {{-- QR code cards --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                    {{-- Badge / Check-out QR --}}
                    @if ($badgeQrDataUri)
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md p-5 flex flex-col items-center gap-3 text-center">
                            <p class="text-xs font-semibold uppercase tracking-widest text-warning-600 dark:text-warning-400">
                                Check-Out QR
                            </p>
                            <img src="{{ $badgeQrDataUri }}" alt="Check-Out QR Code" class="w-40 h-40 rounded-lg" />
                            <div>
                                <p class="text-sm font-medium text-gray-800 dark:text-gray-200">Staff will scan this when you leave</p>
                                <p class="text-xs text-gray-400 mt-0.5">This code is valid for this visit only</p>
                            </div>
                        </div>
                    @endif

                    {{-- Profile / Returning-visitor QR --}}
                    @if ($profileQrDataUri)
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md p-5 flex flex-col items-center gap-3 text-center">
                            <p class="text-xs font-semibold uppercase tracking-widest text-primary-600 dark:text-primary-400">
                                Returning Visitor QR
                            </p>
                            <img src="{{ $profileQrDataUri }}" alt="Profile QR Code" class="w-40 h-40 rounded-lg" />
                            <div>
                                <p class="text-sm font-medium text-gray-800 dark:text-gray-200">Save this for faster check-in next time</p>
                                <p class="text-xs text-gray-400 mt-0.5">Scan at the kiosk to skip re-typing your details</p>
                            </div>
                        </div>
                    @endif

                </div>

                {{-- Action + countdown --}}
                <div class="flex flex-col items-center gap-3">
                    <x-filament::button
                        wire:click="resetKiosk"
                        size="xl"
                        color="primary"
                        icon="heroicon-o-user-plus"
                    >
                        New Check-In
                    </x-filament::button>

                    <p class="text-xs text-gray-400 dark:text-gray-600"
                       x-data="{
                           count: 60,
                           init() {
                               const t = setInterval(() => {
                                   this.count--;
                                   if (this.count <= 0) clearInterval(t);
                               }, 1000);
                               $watch('count', v => { if (v <= 0) clearInterval(t) });
                           }
                       }"
                       x-on:click="count = 60; startAutoReset(60)"
                    >
                        Returning to welcome in <span x-text="count" class="font-medium text-gray-600 dark:text-gray-400"></span>s &mdash; tap anywhere to reset.
                    </p>
                </div>

            </div>
        </main>
    @endif
</div>
