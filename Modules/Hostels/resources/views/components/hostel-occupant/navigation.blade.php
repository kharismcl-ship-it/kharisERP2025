<div class="flex h-full flex-col bg-white">

    {{-- ── Hostel branding ─────────────────────────────────────────── --}}
    <div class="flex items-center gap-3 px-4 py-5 border-b border-gray-100 shrink-0">
        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-600 text-white font-bold text-sm">
            {{ strtoupper(substr($hostel?->name ?? 'H', 0, 2)) }}
        </div>
        <div class="min-w-0 flex-1">
            <p class="truncate text-sm font-semibold text-gray-900 leading-tight">
                {{ $hostel?->name ?? config('app.name') }}
            </p>
            <p class="text-xs text-gray-400">Resident Portal</p>
        </div>
    </div>

    {{-- ── Nav links ────────────────────────────────────────────────── --}}
    <nav class="flex-1 overflow-y-auto px-3 py-3 space-y-0.5">

        @php
            $base   = 'group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors duration-150 no-underline';
            $active = $base . ' bg-blue-50 text-blue-700';
            $idle   = $base . ' text-gray-600 hover:bg-gray-100 hover:text-gray-900';
            $iBase  = 'h-5 w-5 shrink-0';
            $iAct   = $iBase . ' text-blue-600';
            $iIdle  = $iBase . ' text-gray-400 group-hover:text-gray-600';
        @endphp

        {{-- Dashboard --}}
        @php $on = request()->routeIs('hostel_occupant.dashboard'); @endphp
        <a href="{{ route('hostel_occupant.dashboard') }}" class="{{ $on ? $active : $idle }}">
            <svg class="{{ $on ? $iAct : $iIdle }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Dashboard
        </a>

        {{-- Bookings --}}
        @php $on = request()->routeIs('hostel_occupant.bookings.*'); @endphp
        <a href="{{ route('hostel_occupant.bookings.index') }}" class="{{ $on ? $active : $idle }}">
            <svg class="{{ $on ? $iAct : $iIdle }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Bookings
        </a>

        {{-- Maintenance --}}
        @php $on = request()->routeIs('hostel_occupant.maintenance.*'); @endphp
        <a href="{{ route('hostel_occupant.maintenance.index') }}" class="{{ $on ? $active : $idle }}">
            <svg class="{{ $on ? $iAct : $iIdle }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Maintenance
        </a>

        {{-- Incidents --}}
        @php $on = request()->routeIs('hostel_occupant.incidents.*'); @endphp
        <a href="{{ route('hostel_occupant.incidents.index') }}" class="{{ $on ? $active : $idle }}">
            <svg class="{{ $on ? $iAct : $iIdle }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            Incidents
        </a>

        {{-- Visitors --}}
        @php $on = request()->routeIs('hostel_occupant.visitors.*'); @endphp
        <a href="{{ route('hostel_occupant.visitors.index') }}" class="{{ $on ? $active : $idle }}">
            <svg class="{{ $on ? $iAct : $iIdle }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Visitors
        </a>

        {{-- WhatsApp Groups --}}
        @php $on = request()->routeIs('hostel_occupant.whatsapp-groups.*'); @endphp
        <a href="{{ route('hostel_occupant.whatsapp-groups.index') }}" class="{{ $on ? $active : $idle }}">
            <svg class="{{ $on ? $iAct : $iIdle }}" fill="currentColor" viewBox="0 0 24 24">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12 0C5.373 0 0 5.373 0 12c0 2.123.555 4.116 1.529 5.843L.057 23.428c-.073.322.039.658.292.864.253.206.606.254.92.155l5.81-1.504A11.95 11.95 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818c-1.979 0-3.836-.558-5.417-1.525L3.09 21.303l1.03-3.417A9.773 9.773 0 012.182 12C2.182 6.575 6.575 2.182 12 2.182S21.818 6.575 21.818 12 17.425 21.818 12 21.818z"/>
            </svg>
            WhatsApp Groups
        </a>

        {{-- Restaurant --}}
        @php $on = request()->routeIs('hostel_occupant.restaurant.*'); @endphp
        <a href="{{ route('hostel_occupant.restaurant.menu') }}" class="{{ $on ? $active : $idle }}">
            <svg class="{{ $on ? $iAct : $iIdle }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            Restaurant
        </a>

        {{-- Shop --}}
        @php $on = request()->routeIs('hostel_occupant.shop.*'); @endphp
        <a href="{{ route('hostel_occupant.shop.index') }}" class="{{ $on ? $active : $idle }}">
            <svg class="{{ $on ? $iAct : $iIdle }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
            Shop
        </a>

        {{-- Movies --}}
        @php $on = request()->routeIs('hostel_occupant.movies.*'); @endphp
        <a href="{{ route('hostel_occupant.movies.index') }}" class="{{ $on ? $active : $idle }}">
            <svg class="{{ $on ? $iAct : $iIdle }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/>
            </svg>
            Movies
        </a>

        {{-- Books --}}
        @php $on = request()->routeIs('hostel_occupant.books.*'); @endphp
        <a href="{{ route('hostel_occupant.books.index') }}" class="{{ $on ? $active : $idle }}">
            <svg class="{{ $on ? $iAct : $iIdle }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            Books
        </a>

    </nav>

    {{-- ── User section ─────────────────────────────────────────────── --}}
    @auth('hostel_occupant')
    <div class="shrink-0 border-t border-gray-100 p-3" x-data="{ open: false }">

        <button @click="open = !open"
                class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left hover:bg-gray-100 transition-colors">
            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gray-200 text-xs font-semibold text-gray-700">
                {{ strtoupper(substr($occupant?->first_name ?? $user->email, 0, 2)) }}
            </span>
            <span class="min-w-0 flex-1">
                <span class="block truncate text-sm font-medium text-gray-900 leading-tight">
                    {{ trim(($occupant?->first_name ?? '') . ' ' . ($occupant?->last_name ?? '')) ?: $user->email }}
                </span>
                <span class="block truncate text-xs text-gray-400">{{ $user->email }}</span>
            </span>
            {{-- Chevron — use x-bind:class for rotation, Tailwind h-4 w-4 for size --}}
            <svg class="h-4 w-4 shrink-0 text-gray-400 transition-transform duration-200"
                 :class="open ? 'rotate-180' : ''"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        <div x-show="open"
             x-transition:enter="transition ease-out duration-100"
             x-transition:enter-start="opacity-0 -translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-75"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-1"
             class="mt-1 space-y-0.5">
            <a href="{{ route('hostel_occupant.profile.edit') }}"
               class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors no-underline">
                <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Profile
            </a>
            <form method="POST" action="{{ route('hostel_occupant.logout') }}">
                @csrf
                <button type="submit"
                        class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                    <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Log Out
                </button>
            </form>
        </div>

    </div>
    @endauth

</div>
