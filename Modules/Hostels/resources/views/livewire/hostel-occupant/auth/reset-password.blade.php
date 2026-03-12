<div class="flex flex-col gap-6">

    <div class="flex flex-col gap-1 text-center">
        <h1 class="text-xl font-semibold text-gray-900">Set new password</h1>
        <p class="text-sm text-gray-500">Choose a strong password for your account</p>
    </div>

    <form wire:submit.prevent="resetPassword" class="flex flex-col gap-5">

        <div class="flex flex-col gap-1.5">
            <label for="email" class="text-sm font-medium text-gray-700">Email address</label>
            <input
                type="email"
                id="email"
                wire:model="email"
                autocomplete="email"
                readonly
                class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-600
                       focus:outline-none cursor-not-allowed"
            >
            @error('email')
                <p class="text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex flex-col gap-1.5">
            <label for="password" class="text-sm font-medium text-gray-700">New password</label>
            <input
                type="password"
                id="password"
                wire:model="password"
                autocomplete="new-password"
                autofocus
                placeholder="At least 8 characters"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 placeholder-gray-400
                       focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500
                       @error('password') border-red-400 @enderror"
            >
            @error('password')
                <p class="text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex flex-col gap-1.5">
            <label for="password_confirmation" class="text-sm font-medium text-gray-700">Confirm new password</label>
            <input
                type="password"
                id="password_confirmation"
                wire:model="password_confirmation"
                autocomplete="new-password"
                placeholder="Repeat your new password"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 placeholder-gray-400
                       focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
            >
        </div>

        <button
            type="submit"
            wire:loading.attr="disabled"
            class="w-full rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white
                   hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2
                   disabled:opacity-60 disabled:cursor-not-allowed transition-colors"
        >
            <span wire:loading.remove>Reset password</span>
            <span wire:loading>Resetting...</span>
        </button>

    </form>

</div>
