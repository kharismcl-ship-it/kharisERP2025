<div class="min-h-[60vh] flex items-center justify-center px-4">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-sm p-8">
            <div class="text-center mb-8">
                <div class="text-4xl mb-3">🔒</div>
                <h1 class="text-2xl font-bold text-gray-900">Reset Password</h1>
                <p class="text-sm text-gray-500 mt-1">Choose a new password for your account.</p>
            </div>

            <form wire:submit.prevent="resetPassword" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input
                        wire:model="email"
                        type="email"
                        autocomplete="email"
                        class="w-full rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                    />
                    @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                    <input
                        wire:model="password"
                        type="password"
                        autocomplete="new-password"
                        class="w-full rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                        placeholder="At least 8 characters"
                    />
                    @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                    <input
                        wire:model="passwordConfirmation"
                        type="password"
                        autocomplete="new-password"
                        class="w-full rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                    />
                    @error('passwordConfirmation') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="w-full bg-green-600 hover:bg-green-700 disabled:opacity-50 text-white font-semibold py-3 rounded-xl transition-colors"
                >
                    <span wire:loading.remove>Reset Password</span>
                    <span wire:loading>Resetting...</span>
                </button>
            </form>
        </div>
    </div>
</div>
