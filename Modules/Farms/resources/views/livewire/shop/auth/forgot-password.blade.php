<div class="min-h-[60vh] flex items-center justify-center px-4">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-sm p-8">
            <div class="text-center mb-8">
                <div class="text-4xl mb-3">🔑</div>
                <h1 class="text-2xl font-bold text-gray-900">Forgot Password</h1>
                <p class="text-sm text-gray-500 mt-1">We'll send a reset link to your email.</p>
            </div>

            @if($status)
                <div class="mb-6 rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800 text-center">
                    {{ $status }}
                </div>
            @endif

            <form wire:submit.prevent="sendLink" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input
                        wire:model="email"
                        type="email"
                        autocomplete="email"
                        class="w-full rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                        placeholder="you@example.com"
                    />
                    @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="w-full bg-green-600 hover:bg-green-700 disabled:opacity-50 text-white font-semibold py-3 rounded-xl transition-colors"
                >
                    <span wire:loading.remove>Send Reset Link</span>
                    <span wire:loading>Sending...</span>
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-gray-500">
                Remembered it?
                <a href="{{ route('farm-shop.login') }}" class="text-green-700 hover:text-green-900 font-medium">Sign in</a>
            </p>
        </div>
    </div>
</div>
