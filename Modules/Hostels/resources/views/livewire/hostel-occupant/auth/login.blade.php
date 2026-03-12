<div class="flex flex-col gap-6">

    <div class="flex flex-col gap-1 text-center">
        <h1 class="text-xl font-semibold text-gray-900">Sign in to your account</h1>
        <p class="text-sm text-gray-500">Enter your email and password to continue</p>
    </div>

    @if (session('status'))
        <div class="rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
            {{ session('status') }}
        </div>
    @endif

    <form wire:submit.prevent="login" class="flex flex-col gap-5">

        <div class="flex flex-col gap-1.5">
            <label for="email" class="text-sm font-medium text-gray-700">Email address</label>
            <input
                type="email"
                id="email"
                wire:model="email"
                autocomplete="email"
                autofocus
                placeholder="you@example.com"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 placeholder-gray-400
                       focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500
                       @error('email') border-red-400 @enderror"
            >
            @error('email')
                <p class="text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex flex-col gap-1.5">
            <div class="flex items-center justify-between">
                <label for="password" class="text-sm font-medium text-gray-700">Password</label>
                @if (Route::has('hostel_occupant.password.request'))
                    <a href="{{ route('hostel_occupant.password.request') }}"
                       class="text-xs text-indigo-600 hover:text-indigo-500">
                        Forgot password?
                    </a>
                @endif
            </div>
            <input
                type="password"
                id="password"
                wire:model="password"
                autocomplete="current-password"
                placeholder="••••••••"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 placeholder-gray-400
                       focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500
                       @error('password') border-red-400 @enderror"
            >
            @error('password')
                <p class="text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-2">
            <input
                type="checkbox"
                id="remember"
                wire:model="remember"
                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
            >
            <label for="remember" class="text-sm text-gray-600">Remember me</label>
        </div>

        <button
            type="submit"
            wire:loading.attr="disabled"
            class="w-full rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white
                   hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2
                   disabled:opacity-60 disabled:cursor-not-allowed transition-colors"
        >
            <span wire:loading.remove>Sign in</span>
            <span wire:loading>Signing in...</span>
        </button>

    </form>

    <p class="text-center text-sm text-gray-500">
        Don't have an account?
        <a href="{{ route('hostel_occupant.register') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
            Create one
        </a>
    </p>

</div>
