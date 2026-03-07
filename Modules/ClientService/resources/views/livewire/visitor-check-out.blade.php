<div class="min-h-screen flex flex-col">

    {{-- ══ HEADER ══════════════════════════════════════════════════════════ --}}
    <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-sm">
        <div class="max-w-5xl mx-auto px-6 py-4 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-primary-600 dark:text-primary-400">
                    Visitor Check-Out
                </p>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white leading-tight">
                    {{ $companyName }}
                </h1>
            </div>
            <div class="text-right"
                 x-data="{ now: new Date() }"
                 x-init="setInterval(() => now = new Date(), 1000)">
                <span class="block text-lg font-semibold text-gray-700 dark:text-gray-200"
                      x-text="now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })"></span>
                <span class="block text-xs text-gray-500 dark:text-gray-400"
                      x-text="now.toLocaleDateString([], { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })"></span>
            </div>
        </div>
    </header>

    <main class="flex-1 flex flex-col items-center justify-center px-6 py-12">

        {{-- ══ CONFIRM — Show visitor info, ask them to proceed ══════════════ --}}
        @if ($screen === 'confirm')
            <div class="w-full max-w-lg text-center space-y-6">
                <div class="flex items-center justify-center">
                    <div class="flex items-center justify-center w-24 h-24 rounded-full
                                bg-warning-100 dark:bg-warning-900 text-warning-600 dark:text-warning-300
                                ring-8 ring-warning-50 dark:ring-warning-950">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                        </svg>
                    </div>
                </div>

                <div>
                    <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white">Ready to Check Out?</h2>
                    <p class="mt-2 text-xl text-gray-600 dark:text-gray-300">
                        <span class="font-semibold text-gray-900 dark:text-white">{{ $visitorName }}</span>
                    </p>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Checked in at {{ $checkedInAt }}</p>
                    @if ($badgeCode)
                        <p class="mt-3 inline-flex items-center gap-2 rounded-full
                                   bg-gray-100 dark:bg-gray-700 px-4 py-1.5
                                   text-sm font-medium text-gray-700 dark:text-gray-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z" />
                            </svg>
                            Badge {{ $badgeCode }}
                        </p>
                    @endif
                </div>

                @if ($badgeCode)
                    <p class="text-sm font-medium text-warning-700 dark:text-warning-400 bg-warning-50 dark:bg-warning-900/30 rounded-xl px-4 py-3">
                        Please have your badge ready to return before proceeding.
                    </p>
                @endif

                <x-filament::button
                    wire:click="proceedToBadgeReturn"
                    wire:loading.attr="disabled"
                    size="xl"
                    color="warning"
                    icon="heroicon-o-arrow-right-on-rectangle"
                >
                    Proceed to Check-Out
                </x-filament::button>
            </div>
        @endif

        {{-- ══ BADGE RETURN — Confirm badge code before completing checkout ══ --}}
        @if ($screen === 'badge_return')
            <div class="w-full max-w-md text-center space-y-6">
                <div class="flex items-center justify-center">
                    <div class="flex items-center justify-center w-24 h-24 rounded-full
                                bg-primary-100 dark:bg-primary-900 text-primary-600 dark:text-primary-300
                                ring-8 ring-primary-50 dark:ring-primary-950">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z" />
                        </svg>
                    </div>
                </div>

                <div>
                    <h2 class="text-2xl font-extrabold text-gray-900 dark:text-white">Return Your Badge</h2>
                    <p class="mt-2 text-gray-500 dark:text-gray-400">
                        Hand your badge to the receptionist, then enter the badge code below to confirm.
                    </p>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md p-6 space-y-4 text-left">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Badge Code
                    </label>
                    <input
                        type="text"
                        wire:model.live="enteredBadge"
                        placeholder="e.g. KH-0012"
                        autocomplete="off"
                        class="block w-full rounded-lg border border-gray-300 dark:border-gray-600
                               bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                               px-4 py-3 text-lg font-mono uppercase tracking-widest
                               focus:ring-2 focus:ring-primary-500 focus:border-primary-500
                               placeholder-gray-400"
                    />
                    @if ($badgeError)
                        <p class="text-sm text-danger-600 dark:text-danger-400 font-medium">
                            {{ $badgeError }}
                        </p>
                    @endif
                </div>

                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <x-filament::button
                        wire:click="confirmBadgeReturn"
                        wire:loading.attr="disabled"
                        size="xl"
                        color="primary"
                        icon="heroicon-o-check-circle"
                    >
                        Confirm &amp; Check Out
                    </x-filament::button>

                    <x-filament::button
                        wire:click="$set('screen', 'confirm')"
                        size="xl"
                        color="gray"
                        icon="heroicon-o-arrow-left"
                    >
                        Back
                    </x-filament::button>
                </div>
            </div>
        @endif

        {{-- ══ SUCCESS ══════════════════════════════════════════════════════ --}}
        @if ($screen === 'success')
            <div class="w-full max-w-lg text-center space-y-6">
                <div class="flex items-center justify-center">
                    <div class="flex items-center justify-center w-28 h-28 rounded-full
                                bg-success-100 dark:bg-success-900 text-success-600 dark:text-success-300
                                ring-8 ring-success-50 dark:ring-success-950">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-14 h-14" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div>
                    <h2 class="text-4xl font-extrabold text-gray-900 dark:text-white">Goodbye!</h2>
                    <p class="mt-2 text-xl text-gray-600 dark:text-gray-300">
                        Thank you for visiting, <span class="font-semibold text-gray-900 dark:text-white">{{ $visitorName }}</span>.
                    </p>
                </div>
                <p class="text-sm text-gray-400 dark:text-gray-500">
                    You have been checked out. Badge returned. Have a safe journey!
                </p>
            </div>
        @endif

        {{-- ══ ALREADY CHECKED OUT ═══════════════════════════════════════════ --}}
        @if ($screen === 'already_out')
            <div class="w-full max-w-lg text-center space-y-6">
                <div class="flex items-center justify-center">
                    <div class="flex items-center justify-center w-24 h-24 rounded-full
                                bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300
                                ring-8 ring-blue-50 dark:ring-blue-950">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                        </svg>
                    </div>
                </div>
                <div>
                    <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white">Already Checked Out</h2>
                    @if ($visitorName)
                        <p class="mt-2 text-lg text-gray-500 dark:text-gray-400">
                            {{ $visitorName }} was already checked out.
                        </p>
                    @endif
                </div>
                <p class="text-sm text-gray-400">Please see the receptionist if you need assistance.</p>
            </div>
        @endif

        {{-- ══ NOT FOUND ════════════════════════════════════════════════════ --}}
        @if ($screen === 'not_found')
            <div class="w-full max-w-lg text-center space-y-6">
                <div class="flex items-center justify-center">
                    <div class="flex items-center justify-center w-24 h-24 rounded-full
                                bg-danger-100 dark:bg-danger-900 text-danger-600 dark:text-danger-300
                                ring-8 ring-danger-50 dark:ring-danger-950">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>
                </div>
                <div>
                    <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white">QR Code Not Recognised</h2>
                    <p class="mt-2 text-gray-500 dark:text-gray-400">This QR code is invalid or has expired.</p>
                </div>
                <p class="text-sm text-gray-400">Please ask the receptionist to check you out manually.</p>
            </div>
        @endif

    </main>
</div>