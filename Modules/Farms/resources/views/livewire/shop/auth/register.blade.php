<div class="max-w-md mx-auto px-4 py-12">
    <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Create Your Account</h1>
        <p class="text-sm text-gray-500 mt-1">Track orders and save your delivery details</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm p-8">
        <div class="space-y-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                <input wire:model="name" type="text" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500" placeholder="Your full name" />
                @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                <input wire:model="email" type="email" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500" placeholder="you@example.com" />
                @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                <input wire:model="phone" type="tel" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500" placeholder="0XX XXX XXXX" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password *</label>
                <input wire:model="password" type="password" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500" placeholder="At least 6 characters" />
                @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password *</label>
                <input wire:model="password_confirmation" type="password" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500" />
            </div>

            <button wire:click="register" wire:loading.attr="disabled"
                class="w-full bg-green-600 hover:bg-green-700 disabled:opacity-50 text-white font-semibold py-3 rounded-xl transition-colors">
                <span wire:loading.remove wire:target="register">Create Account</span>
                <span wire:loading wire:target="register">Creating...</span>
            </button>
        </div>

        <p class="mt-5 text-center text-sm text-gray-500">
            Already have an account?
            <a href="{{ route('farm-shop.login') }}" class="font-medium text-green-600 hover:text-green-700">Sign in</a>
        </p>
    </div>
</div>
