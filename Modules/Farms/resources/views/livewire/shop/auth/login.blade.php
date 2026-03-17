<div class="max-w-md mx-auto px-4 py-12">
    <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Sign In</h1>
        <p class="text-sm text-gray-500 mt-1">Access your orders and saved details</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm p-8">
        <div class="space-y-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <input wire:model="email" type="email" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500" placeholder="you@example.com" />
                @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input wire:model="password" type="password" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500" />
                @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                    <input wire:model="remember" type="checkbox" class="rounded border-gray-300 text-green-600" />
                    Remember me
                </label>
                <a href="{{ route('farm-shop.password.request') }}" class="text-sm text-green-700 hover:text-green-900">Forgot password?</a>
            </div>

            <button wire:click="login" wire:loading.attr="disabled"
                class="w-full bg-green-600 hover:bg-green-700 disabled:opacity-50 text-white font-semibold py-3 rounded-xl transition-colors">
                <span wire:loading.remove wire:target="login">Sign In</span>
                <span wire:loading wire:target="login">Signing in...</span>
            </button>
        </div>

        <p class="mt-5 text-center text-sm text-gray-500">
            Don't have an account?
            <a href="{{ route('farm-shop.register') }}" class="font-medium text-green-600 hover:text-green-700">Create one</a>
        </p>
    </div>
</div>
