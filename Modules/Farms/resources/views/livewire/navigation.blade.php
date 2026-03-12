<nav class="bg-white shadow" x-data="{ open: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            <!-- Left: Brand + Nav Links -->
            <div class="flex items-center">
                <a href="{{ route('farms.index') }}" class="font-semibold text-gray-800 mr-8 text-lg">
                    Farms Portal
                </a>

                <!-- Desktop Nav -->
                <div class="hidden sm:flex items-center gap-1">
                    <a href="{{ route('farms.index') }}"
                       class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('farms.index') ? 'bg-gray-100 text-gray-900' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                        My Farms
                    </a>

                    @if($farm)
                        <a href="{{ route('farms.dashboard', $farm->slug) }}"
                           class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('farms.dashboard') ? 'bg-gray-100 text-gray-900' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                            Dashboard
                        </a>
                        <a href="{{ route('farms.tasks.index', $farm->slug) }}"
                           class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('farms.tasks.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                            Tasks
                        </a>
                        <a href="{{ route('farms.daily-reports.index', $farm->slug) }}"
                           class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('farms.daily-reports.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                            Reports
                        </a>
                        <a href="{{ route('farms.requests.index', $farm->slug) }}"
                           class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('farms.requests.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                            Requests
                        </a>
                        <a href="{{ route('farms.attendance.index', $farm->slug) }}"
                           class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('farms.attendance.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                            Attendance
                        </a>
                    @endif
                </div>
            </div>

            <!-- Right: User + mobile toggle -->
            <div class="flex items-center gap-3">
                <span class="hidden sm:block text-sm text-gray-500">
                    {{ auth()->user()->name ?? '' }}
                </span>

                <!-- Mobile hamburger -->
                <button
                    @click="open = !open"
                    class="sm:hidden p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100"
                >
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div x-show="open" x-transition class="sm:hidden border-t border-gray-200 bg-white">
        <div class="px-4 py-3 space-y-1">
            <a href="{{ route('farms.index') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                My Farms
            </a>
            @if($farm)
                <a href="{{ route('farms.dashboard', $farm->slug) }}" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Dashboard
                </a>
                <a href="{{ route('farms.tasks.index', $farm->slug) }}" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Tasks
                </a>
                <a href="{{ route('farms.daily-reports.index', $farm->slug) }}" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Reports
                </a>
                <a href="{{ route('farms.requests.index', $farm->slug) }}" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Requests
                </a>
                <a href="{{ route('farms.attendance.index', $farm->slug) }}" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Attendance
                </a>
            @endif
        </div>
    </div>
</nav>
